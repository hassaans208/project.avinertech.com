<?php

namespace Database\Seeders;

use App\Models\ServiceModule;
use Illuminate\Database\Seeder;

class ServiceModuleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $modules = [
            [
                'name' => 'api_access',
                'display_name' => 'API Access',
                'description' => 'Full access to our REST API endpoints for integration',
                'cost_price' => 5.00,
                'sale_price' => 15.00,
                'tax_rate' => 0.0825,
                'currency' => 'USD',
                'is_active' => true,
            ],
            [
                'name' => 'ai_integration',
                'display_name' => 'AI Integration',
                'description' => 'Advanced AI features and machine learning capabilities',
                'cost_price' => 25.00,
                'sale_price' => 50.00,
                'tax_rate' => 0.0825,
                'currency' => 'USD',
                'is_active' => true,
            ],
            [
                'name' => 'analytics',
                'display_name' => 'Analytics Dashboard',
                'description' => 'Comprehensive analytics and reporting tools',
                'cost_price' => 8.00,
                'sale_price' => 20.00,
                'tax_rate' => 0.0825,
                'currency' => 'USD',
                'is_active' => true,
            ],
            [
                'name' => 'custom_domains',
                'display_name' => 'Custom Domains',
                'description' => 'Use your own domain name for branding',
                'cost_price' => 3.00,
                'sale_price' => 10.00,
                'tax_rate' => 0.0825,
                'currency' => 'USD',
                'is_active' => true,
            ],
            [
                'name' => 'payment_methods',
                'display_name' => 'Payment Methods',
                'description' => 'Multiple payment gateway integrations',
                'cost_price' => 12.00,
                'sale_price' => 25.00,
                'tax_rate' => 0.0825,
                'currency' => 'USD',
                'is_active' => true,
            ],
            [
                'name' => 'priority_support',
                'display_name' => 'Priority Support',
                'description' => '24/7 priority customer support with dedicated agent',
                'cost_price' => 15.00,
                'sale_price' => 30.00,
                'tax_rate' => 0.0825,
                'currency' => 'USD',
                'is_active' => true,
            ],
            [
                'name' => 'white_label',
                'display_name' => 'White Label Solution',
                'description' => 'Remove our branding and use your own',
                'cost_price' => 20.00,
                'sale_price' => 40.00,
                'tax_rate' => 0.0825,
                'currency' => 'USD',
                'is_active' => true,
            ],
            [
                'name' => 'advanced_security',
                'display_name' => 'Advanced Security',
                'description' => 'Enhanced security features including 2FA and audit logs',
                'cost_price' => 18.00,
                'sale_price' => 35.00,
                'tax_rate' => 0.0825,
                'currency' => 'USD',
                'is_active' => true,
            ],
        ];

        foreach ($modules as $moduleData) {
            ServiceModule::updateOrCreate(
                ['name' => $moduleData['name']],
                $moduleData
            );
        }
    }
} 