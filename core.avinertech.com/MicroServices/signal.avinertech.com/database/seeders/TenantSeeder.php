<?php

namespace Database\Seeders;

use App\Models\Tenant;
use App\Models\Package;
use Illuminate\Database\Seeder;

class TenantSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tenants = [
            [
                'name' => 'Example.com Tenant',
                'host' => 'example.avinertech.com',
                'status' => 'active',
                'package' => 'free_package',
            ],
            [
                'name' => 'Test Site Tenant',
                'host' => 'prototype.avinertech.com',
                'status' => 'active',
                'package' => 'basic_package',
            ],
            [
                'name' => 'Demo Application',
                'host' => 'demo.avinertech.com',
                'status' => 'active',
                'package' => 'professional_package',
            ],
            [
                'name' => 'Blocked Tenant',
                'host' => 'blocked.avinertech.com',
                'status' => 'blocked',
                'package' => 'free_package',
            ],
        ];

        foreach ($tenants as $tenantData) {
            $packageName = $tenantData['package'];
            unset($tenantData['package']);

            $tenant = Tenant::updateOrCreate(
                ['host' => $tenantData['host']],
                $tenantData
            );

            // Assign package
            $package = Package::where('name', $packageName)->first();
            if ($package && !$tenant->packages()->where('package_id', $package->id)->exists()) {
                $tenant->assignPackage($package);
            }
        }
    }
} 