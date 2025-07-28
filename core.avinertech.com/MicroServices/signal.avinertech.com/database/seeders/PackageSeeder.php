<?php

namespace Database\Seeders;

use App\Models\Package;
use Illuminate\Database\Seeder;

class PackageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $packages = [
            [
                'name' => 'free_package',
                'cost' => 0.00,
                'currency' => 'USD',
                'tax_rate' => 0.0000,
                'modules' => ['api_access'],
            ],
            [
                'name' => 'basic_package',
                'cost' => 29.99,
                'currency' => 'USD',
                'tax_rate' => 0.0825, // 8.25%
                'modules' => ['api_access', 'analytics', 'custom_domains'],
            ],
            [
                'name' => 'professional_package',
                'cost' => 99.99,
                'currency' => 'USD',
                'tax_rate' => 0.0825,
                'modules' => [
                    'api_access',
                    'analytics',
                    'custom_domains',
                    'ai_integration',
                    'payment_methods',
                    'priority_support',
                ],
            ],
            [
                'name' => 'enterprise_package',
                'cost' => 299.99,
                'currency' => 'USD',
                'tax_rate' => 0.0825,
                'modules' => [
                    'api_access',
                    'analytics',
                    'custom_domains',
                    'ai_integration',
                    'payment_methods',
                    'priority_support',
                    'white_label',
                    'advanced_security',
                ],
            ],
        ];

        foreach ($packages as $packageData) {
            Package::updateOrCreate(
                ['name' => $packageData['name']],
                $packageData
            );
        }
    }
} 