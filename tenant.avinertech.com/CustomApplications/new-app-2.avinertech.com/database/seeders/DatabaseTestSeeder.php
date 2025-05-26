<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Configuration;

class DatabaseTestSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $configurations = [
            [
                'name' => 'DATABASE_HOST',
                'value' => 'localhost',
                'type' => 'DATABASE',
                'host' => 'localhost', 
                'group' => 'database',
                'is_encrypted' => false,
            ],
            [
                'name' => 'DATABASE_PORT', 
                'value' => '3306',
                'type' => 'DATABASE',
                'host' => 'localhost',
                'group' => 'database', 
                'is_encrypted' => false,
            ],
            [
                'name' => 'DATABASE_USERNAME',
                'value' => encrypt('root'),
                'type' => 'DATABASE',
                'host' => 'localhost',
                'group' => 'database',
                'is_encrypted' => true,
            ],
            [
                'name' => 'DATABASE_PASSWORD',
                'value' => encrypt('27901'),
                'type' => 'DATABASE',
                'host' => 'localhost',
                'group' => 'database',
                'is_encrypted' => true,
            ],
            [
                'name' => 'DATABASE_NAME',
                'value' => 'laravel',
                'type' => 'DATABASE',
                'host' => 'localhost',
                'group' => 'database',
                'is_encrypted' => false,
            ]
        ];

        foreach ($configurations as $config) {
            Configuration::updateOrCreate(
                ['name' => $config['name']],
                $config
            );
        }
    }
}
