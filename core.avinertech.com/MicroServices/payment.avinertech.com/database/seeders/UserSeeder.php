<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Tenant;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create Super Admin
        $superAdmin = User::create([
            'name' => 'Super Admin',
            'email' => 'admin@avinertech.com',
            'password' => Hash::make('admin27901'),
            'user_type' => 'SUPER_ADMIN',
            'is_active' => true,
        ]);

        // Generate API token for super admin
        $superAdmin->generateApiToken();

        // Create Tenant Admins
        $tenantAdmin1 = User::create([
            'name' => 'John Doe',
            'email' => 'john@prototype.com',
            'password' => Hash::make('admin27901'),
            'user_type' => 'TENANT_ADMIN',
            'is_active' => true,
        ]);

        $tenantAdmin2 = User::create([
            'name' => 'Jane Smith',
            'email' => 'jane@prototype.avinertech.com',
            'password' => Hash::make('admin27901'),
            'user_type' => 'TENANT_ADMIN',
            'is_active' => true,
        ]);

        // Generate API tokens
        $tenantAdmin1->generateApiToken();
        $tenantAdmin2->generateApiToken();

        // Assign tenant admins to tenants
        $tenants = Tenant::all();
        
        if ($tenants->count() > 0) {
            // Assign first tenant admin to first tenant
            if ($tenants->count() >= 1) {
                $tenantAdmin1->assignToTenant($tenants->where('id', 2)->first(), 'admin');
            }
            
            // Assign second tenant admin to second tenant (if exists)
            if ($tenants->count() >= 2) {
                $tenantAdmin2->assignToTenant($tenants->skip(1)->first(), 'admin');
            }
            
            // Assign second tenant admin to first tenant as member
            $tenantAdmin2->assignToTenant($tenants->first(), 'member');
        }

        $this->command->info('Users seeded successfully!');
        $this->command->info('Super Admin: admin@avinertech.com / password');
        $this->command->info('Super Admin Token: ' . $superAdmin->api_token);
        $this->command->info('Tenant Admin 1: john@prototype.com / password');
        $this->command->info('Tenant Admin 1 Token: ' . $tenantAdmin1->api_token);
        $this->command->info('Tenant Admin 2: jane@prototype.avinertech.com / password');
        $this->command->info('Tenant Admin 2 Token: ' . $tenantAdmin2->api_token);
    }
} 