<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PaymentMethodSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $paymentMethods = [
            [
                'name' => 'stripe',
                'config' => json_encode([
                    'api_key' => env('STRIPE_SECRET_KEY', 'sk_test_sandbox'),
                    'publishable_key' => env('STRIPE_PUBLISHABLE_KEY', 'pk_test_sandbox'),
                    'webhook_secret' => env('STRIPE_WEBHOOK_SECRET', 'whsec_sandbox'),
                    'currency' => 'USD',
                    'sandbox_mode' => true,
                    'api_version' => '2023-10-16'
                ]),
                'is_active' => true,
                'order' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'paypal',
                'config' => json_encode([
                    'client_id' => env('PAYPAL_CLIENT_ID', 'sandbox_client_id'),
                    'client_secret' => env('PAYPAL_CLIENT_SECRET', 'sandbox_client_secret'),
                    'sandbox_mode' => true,
                    'currency' => 'USD',
                    'api_base_url' => 'https://api.sandbox.paypal.com',
                    'webhook_id' => env('PAYPAL_WEBHOOK_ID', 'sandbox_webhook_id')
                ]),
                'is_active' => true,
                'order' => 2,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'local_bank',
                'config' => json_encode([
                    'bank_name' => 'Local Bank Transfer',
                    'account_number' => 'XXXX-XXXX-XXXX-1234',
                    'routing_number' => '123456789',
                    'swift_code' => 'LOCALBANK',
                    'currency' => 'USD',
                    'processing_time' => '1-3 business days',
                    'manual_verification' => true
                ]),
                'is_active' => true,
                'order' => 3,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('payment_methods')->insert($paymentMethods);
    }
} 