<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Tenant;
use App\Models\TenantDatabase;
use App\Models\User;
use App\Models\Package;
use App\Helpers\EncryptionHelper;

class TenantDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeder.
     */
    public function run(): void
    {
        // Use existing tenant with ID 2
        $tenant = Tenant::find(2);
        
        if (!$tenant) {
            $this->command->error('Tenant with ID 2 not found. Please create a tenant first.');
            return;
        }

        // Create database entry with encrypted details
        $databaseDetails = [
            'database_name' => 'tenant_db',
            'database_host' => 'localhost',
            'database_port' => 3306,
            'database_username' => 'root',
            'database_password' => '27901',
            'database_charset' => 'utf8mb4',
            'database_collation' => 'utf8mb4_unicode_ci',
        ];

        // Encrypt the database details using EncryptionHelper
        $encryptedDetails = EncryptionHelper::encryptAlphaNumeric(json_encode($databaseDetails));

        // Create the tenant database entry
        TenantDatabase::create([
            'tenant_id' => $tenant->id,
            'schema_name' => 'tenant_db',
            'database_details' => $encryptedDetails,
            'is_active' => true,
        ]);

        $this->command->info('TenantDatabase seeder completed successfully!');
        $this->command->info('Tenant ID: ' . $tenant->id);
        $this->command->info('Tenant Name: ' . $tenant->name);
        $this->command->info('Schema Name: tenant_schema');
        $this->command->info('Database Name: tenant_db');
        $this->command->info('Database User: root');
        $this->command->info('Database Password: 27901');
    }
}
