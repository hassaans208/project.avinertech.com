<?php

namespace App\Repositories\Contracts;

use App\Models\Package;

interface PackageRepositoryInterface
{
    /**
     * Find package by name.
     */
    public function findByName(string $name): ?Package;

    /**
     * Get the free package.
     */
    public function getFreePackage(): ?Package;

    /**
     * Get all packages.
     */
    public function getAll();

    /**
     * Create a new package.
     */
    public function create(array $data): Package;

    /**
     * Update package.
     */
    public function update(Package $package, array $data): bool;

    /**
     * Find package by ID.
     */
    public function findById(int $id): ?Package;
} 