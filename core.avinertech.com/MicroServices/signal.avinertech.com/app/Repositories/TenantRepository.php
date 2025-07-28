<?php

namespace App\Repositories;

use App\Models\Tenant;
use App\Models\Package;
use App\Repositories\Contracts\TenantRepositoryInterface;

class TenantRepository implements TenantRepositoryInterface
{
    /**
     * Find tenant by host.
     */
    public function findByHost(string $host): ?Tenant
    {
        return Tenant::where('host', $host)->first();
    }

    /**
     * Create a new tenant.
     */
    public function create(array $data): Tenant
    {
        return Tenant::create($data);
    }

    /**
     * Update tenant status.
     */
    public function updateStatus(Tenant $tenant, string $status, ?string $blockReason = null): bool
    {
        $updateData = ['status' => $status];
        
        if ($status === 'blocked' && $blockReason) {
            $updateData['block_reason'] = $blockReason;
        } elseif ($status !== 'blocked') {
            $updateData['block_reason'] = null;
        }
        
        return $tenant->update($updateData);
    }

    /**
     * Assign package to tenant.
     */
    public function assignPackage(Tenant $tenant, Package $package): void
    {
        $tenant->assignPackage($package);
    }

    /**
     * Get all tenants with pagination.
     */
    public function getAllPaginated(int $perPage = 15)
    {
        return Tenant::with('packages')
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
    }

    /**
     * Find tenant by ID.
     */
    public function findById(int $id): ?Tenant
    {
        return Tenant::with('packages')->find($id);
    }
} 