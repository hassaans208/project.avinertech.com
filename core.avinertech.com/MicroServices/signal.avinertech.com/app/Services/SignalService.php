<?php

namespace App\Services;

use App\Models\Tenant;
use App\Models\Package;
use App\Models\SignalLog;
use App\Models\User;
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
     * Handle the signal processing with user authentication
     */
    public function handle(string $encryptedHostId, ?string $hash = null, ?User $user = null): array
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

            // Step 4: Validate user authentication and tenant access
            if ($user) {
                $this->validateUserAccess($user, $tenant);
                $signalLog->user_id = $user->id;
                $signalLog->auth_user_email = $user->email;
            }

            if(empty($hash)) {
                $hash = $tenant->createHash();

                return [
                    'success' => true,
                    'action' => 'create_hash',
                    'action_data' => $hash
                ];
            }

            // Step 5: Parse and validate hash
            $hashData = $this->parseHash($hash, $host, $tenant);
            $signalLog->package_name = $hashData['package_name'];
            $signalLog->signal_timestamp = $hashData['timestamp'];

            // Step 6: Load package details
            $package = $this->loadPackage($hashData['package_name']);

            // Step 7: Generate response
            $response = $this->generateResponse($tenant, $package, $hashData['timestamp'], $user);
            
            $signalLog->status = 'success';
            $signalLog->response_data = $response;
            $signalLog->save();

            return [
                'success' => true,
                'action' => 'generate_response',
                'action_data' => $response['signature']
            ];

        } catch (\Exception $e) {
            $signalLog->status = 'failed';
            $signalLog->error_message = $e->getMessage();
            $signalLog->save();

            throw $e;
        }
    }

    /**
     * Validate user access to tenant
     */
    private function validateUserAccess(User $user, Tenant $tenant): void
    {
        if (!$user->isActive()) {
            throw new InvalidTenantException('User account is inactive');
        }

        if (!$user->hasAccessToTenant($tenant->id)) {
            throw new InvalidTenantException('User does not have access to this tenant');
        }
    }

    /**
     * Decrypt the host ID using custom encryption
     */
    private function decryptHostId(string $encryptedHostId): string
    {
        try {
            $decrypted = decryptAlphaNumeric($encryptedHostId);

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
        }

        if(empty($tenant->getCurrentPackage())) {
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
    private function parseHash(string $hash, string $host, Tenant $tenant): array
    {
        $decryptedHash = decryptAlphaNumeric($hash);
        $parts = explode(':', $decryptedHash);

        if (count($parts) !== 9) {
            throw new InvalidHashFormatException('Invalid hash format');
        }

        [$packageName, $year, $month, $day, $hour, $hashHost, $userId, $userEmail, $packageId] = $parts;

        // Validate package name format (snake_case)
        if (!preg_match('/^[a-z0-9_]+$/', $packageName)) {
            throw new InvalidHashFormatException('Invalid package name format');
        }

        $package = $tenant->getCurrentPackage();

        if(!$package) {
            throw new InvalidHashFormatException('Package not found');
        }

        // Validate host matches
        if ($hashHost !== $host) {
            throw new InvalidHashFormatException('Host mismatch in hash');
        }

        // Parse and validate timestamp
        try {
            // Use 24-hour format (H) for hour validation
            $timestamp = Carbon::createFromFormat('Y-m-d H', "{$year}-{$month}-{$day} {$hour}");
            
            // Check if token is within 3 hours
            if ($timestamp->diffInHours(now()) > 3 || $timestamp->diffInHours(now()) < 0) {
                throw new TokenExpiredException('Token has expired (older than 3 hours)');
            }
        } catch (\Exception $e) {
            if ($e instanceof TokenExpiredException) {
                throw $e;
            }
            throw new InvalidHashFormatException('Invalid timestamp format in hash');
        }

        return [
            'package_name' => $packageName,
            'timestamp' => $timestamp->year . '-' . $timestamp->month . '-' . $timestamp->day . ' ' . $timestamp->hour . ':00:00',
            'host' => $hashHost,
            'user_id' => $userId,
            'user_email' => $userEmail,
            'package_id' => $packageId
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

    /**
     * Generate response with signature
     */
    private function generateResponse(Tenant $tenant, Package $package, string $signalTimestamp, User $user): array
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
            'signal_timestamp' => "$signalTimestamp:00:00",
            'user_id' => $user->id,
            'user_email' => $user->email,
            'user_api_token' => $user->api_token
        ];

        // Generate HMAC signature
        $signature = encryptAlphaNumeric(json_encode($data));

        return [
            'data' => $data,
            'signature' => $signature
        ];
    }
} 