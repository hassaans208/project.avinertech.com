<?php

namespace App\Repositories\Contracts;

use App\Models\Tenant;
use App\Models\Package;

interface TenantRepositoryInterface
{
    /**
     * Find tenant by host.
     */
    public function findByHost(string $host): ?Tenant;

    /**
     * Create a new tenant.
     */
    public function create(array $data): Tenant;

    /**
     * Update tenant status.
     */
    public function updateStatus(Tenant $tenant, string $status, ?string $blockReason = null): bool;

    /**
     * Assign package to tenant.
     */
    public function assignPackage(Tenant $tenant, Package $package): void;

    /**
     * Get all tenants with pagination.
     */
    public function getAllPaginated(int $perPage = 15);

    /**
     * Find tenant by ID.
     */
    public function findById(int $id): ?Tenant;
} 