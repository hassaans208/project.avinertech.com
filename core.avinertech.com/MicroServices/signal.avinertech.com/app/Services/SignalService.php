<?php

namespace App\Services;

use App\Helpers\EncryptionHelper;
use App\Models\Tenant;
use App\Models\Package;
use App\Models\SignalLog;
use App\Repositories\Contracts\TenantRepositoryInterface;
use App\Repositories\Contracts\PackageRepositoryInterface;
use App\Exceptions\DecryptionException;
use App\Exceptions\TokenExpiredException;
use App\Exceptions\InvalidTenantException;
use App\Exceptions\InvalidHashFormatException;
use Carbon\Carbon;

class SignalService
{
    public function __construct(
        private TenantRepositoryInterface $tenantRepository,
        private PackageRepositoryInterface $packageRepository
    ) {}

    /**
     * Handle the signal processing
     */
    public function handle(string $encryptedHostId, string $hash): array
    {
        $signalLog = new SignalLog([
            'encrypted_host_id' => $encryptedHostId,
            'hash_payload' => $hash,
            'status' => 'processing'
        ]);

        try {
            // Step 1: Decrypt the host ID using custom encryption
            $host = $this->decryptHostId($encryptedHostId);
            $signalLog->decrypted_host = $host;

            // Step 2: Find or create tenant
            $tenant = $this->findOrCreateTenant($host);
            $signalLog->tenant_id = $tenant->id;

            // Step 3: Validate tenant status
            $this->validateTenant($tenant);

            // Check if hash is empty (first-time connection)
            if (empty($hash)) {
                return $this->handleFirstTimeConnection($tenant, $signalLog);
            }

            // Step 4: Parse and validate hash (subsequent connections)
            $hashData = $this->parseHash($hash, $host);
            $signalLog->package_name = $hashData['package_name'];
            $signalLog->signal_timestamp = $hashData['timestamp'];

            // Step 5: Load package details
            $package = $this->loadPackage($hashData['package_name']);

            // Step 6: Generate response for established connection
            $response = $this->generateEstablishedResponse($tenant, $package, $hashData['timestamp']);
            
            $signalLog->status = 'success';
            $signalLog->response_data = $response;
            $signalLog->save();

            return $response;

        } catch (\Exception $e) {
            $signalLog->status = 'failed';
            $signalLog->error_message = $e->getMessage();
            $signalLog->save();

            throw $e;
        }
    }

    /**
     * Handle first-time connection (empty hash)
     */
    private function handleFirstTimeConnection(Tenant $tenant, SignalLog $signalLog): array
    {
        // Get the tenant's current package (should be free package for new tenants)
        $package = $tenant->getCurrentPackage();
        
        if (!$package) {
            // Assign free package if none exists
            $freePackage = $this->packageRepository->getFreePackage();
            if ($freePackage) {
                $this->tenantRepository->assignPackage($tenant, $freePackage);
                $package = $freePackage;
            } else {
                throw new InvalidTenantException('No package assigned to tenant');
            }
        }

        // Generate hash for the client to store
        $timestamp = now();
        $hashData = sprintf(
            '%s:%s:%s:%s:%s',
            $package->name,
            $timestamp->format('Y'),
            $timestamp->format('m-d'),
            $timestamp->format('H'),
            $tenant->host
        );

        $response = [
            'success' => true,
            'action' => 'create_hash',
            'action_data' => $hashData,
            'message' => 'Secret hash generated for new connection'
        ];

        $signalLog->status = 'success';
        $signalLog->response_data = $response;
        $signalLog->save();

        return $response;
    }

    /**
     * Generate response for established connections
     */
    private function generateEstablishedResponse(Tenant $tenant, Package $package, Carbon $signalTimestamp): array
    {
        $data = [
            'tenant_id' => $tenant->id,
            'tenant_host' => $tenant->host,
            'tenant_name' => $tenant->name,
            'package_id' => $package->id,
            'package_name' => $package->name,
            'package_cost' => number_format($package->cost, 2),
            'package_currency' => $package->currency,
            'package_tax_rate' => number_format($package->tax_rate, 4),
            'package_modules' => $package->modules ?? [],
            'signal_timestamp' => $signalTimestamp->toISOString(),
            'processed_at' => now()->toISOString(),
            'expires_at' => now()->addHour()->toISOString(),
        ];

        // Generate HMAC signature
        $signature = hash_hmac('sha256', json_encode($data), config('app.key'));

        return [
            'success' => true,
            'action' => 'generate_response',
            'data' => $data,
            'signature' => $signature
        ];
    }

    /**
     * Decrypt the host ID using custom encryption
     */
    private function decryptHostId(string $encryptedHostId): string
    {
        try {
            $decrypted = EncryptionHelper::decryptAlphaNumeric($encryptedHostId);
            
            if ($decrypted === false) {
                throw new DecryptionException('Failed to decrypt host ID');
            }
            
            return $decrypted;
        } catch (\Exception $e) {
            throw new DecryptionException('Invalid encrypted host ID: ' . $e->getMessage());
        }
    }

    /**
     * Find or create tenant by host
     */
    private function findOrCreateTenant(string $host): Tenant
    {
        $tenant = $this->tenantRepository->findByHost($host);

        if (!$tenant) {
            // Create new tenant and assign free package
            $tenant = $this->tenantRepository->create([
                'name' => ucfirst(str_replace('.', ' ', $host)) . ' Tenant',
                'host' => $host,
                'status' => 'active',
            ]);

            // Assign free package
            $freePackage = $this->packageRepository->getFreePackage();
            if ($freePackage) {
                $this->tenantRepository->assignPackage($tenant, $freePackage);
            }
        }

        return $tenant;
    }

    /**
     * Validate tenant status
     */
    private function validateTenant(Tenant $tenant): void
    {
        if (!$tenant->isActive()) {
            throw new InvalidTenantException('Tenant is not active or is blocked');
        }
    }

    /**
     * Parse and validate hash format
     */
    private function parseHash(string $hash, string $host): array
    {
        $parts = explode(':', $hash);

        if (count($parts) !== 5) {
            throw new InvalidHashFormatException('Invalid hash format');
        }

        [$packageName, $year, $monthDay, $hour, $hashHost] = $parts;

        // Validate package name format (snake_case)
        if (!preg_match('/^[a-z0-9_]+$/', $packageName)) {
            throw new InvalidHashFormatException('Invalid package name format');
        }

        // Validate host matches
        if ($hashHost !== $host) {
            throw new InvalidHashFormatException('Host mismatch in hash');
        }

        // Parse and validate timestamp
        try {
            $timestamp = Carbon::createFromFormat('Y-m-d-H', "{$year}-{$monthDay}-{$hour}");
            
            // Check if token is within 1 hour
            if ($timestamp->diffInHours(now()) > 1) {
                throw new TokenExpiredException('Token has expired (older than 1 hour)');
            }
        } catch (\Exception $e) {
            if ($e instanceof TokenExpiredException) {
                throw $e;
            }
            throw new InvalidHashFormatException('Invalid timestamp format in hash');
        }

        return [
            'package_name' => $packageName,
            'timestamp' => $timestamp,
            'host' => $hashHost
        ];
    }

    /**
     * Load package by name
     */
    private function loadPackage(string $packageName): Package
    {
        $package = $this->packageRepository->findByName($packageName);

        if (!$package) {
            throw new InvalidHashFormatException('Package not found: ' . $packageName);
        }

        return $package;
    }
} 