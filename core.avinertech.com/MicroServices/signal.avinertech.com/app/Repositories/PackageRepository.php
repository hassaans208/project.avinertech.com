<?php

namespace App\Repositories;

use App\Models\Package;
use App\Repositories\Contracts\PackageRepositoryInterface;

class PackageRepository implements PackageRepositoryInterface
{
    /**
     * Find package by name.
     */
    public function findByName(string $name): ?Package
    {
        return Package::byName($name)->first();
    }

    /**
     * Get the free package.
     */
    public function getFreePackage(): ?Package
    {
        return Package::where('name', 'free_package')->first();
    }

    /**
     * Get all packages.
     */
    public function getAll()
    {
        return Package::orderBy('cost')->get();
    }

    /**
     * Create a new package.
     */
    public function create(array $data): Package
    {
        return Package::create($data);
    }

    /**
     * Update package.
     */
    public function update(Package $package, array $data): bool
    {
        return $package->update($data);
    }

    /**
     * Find package by ID.
     */
    public function findById(int $id): ?Package
    {
        return Package::find($id);
    }
} 