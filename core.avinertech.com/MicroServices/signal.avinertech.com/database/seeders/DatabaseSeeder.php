<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            ServiceModuleSeeder::class,
            PackageSeeder::class,
            TenantSeeder::class,
            TenantDatabaseSeeder::class,
            UserSeeder::class,
        ]);
    }
}
