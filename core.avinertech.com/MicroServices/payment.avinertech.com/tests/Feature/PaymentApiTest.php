<?php

namespace Tests\Feature;

use App\Models\PaymentMethod;
use App\Models\Tenant;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class PaymentApiTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create test payment methods
        PaymentMethod::create([
            'name' => 'stripe',
            'config' => [
                'api_key' => 'test_key',
                'sandbox_mode' => true
            ],
            'is_active' => true,
            'order' => 1
        ]);
    }

    public function test_payment_endpoint_requires_signature()
    {
        $response = $this->postJson('/api/stripe/payment', [
            'tenant_id' => 1,
            'amount' => 100.00,
            'currency' => 'USD'
        ]);

        $response->assertStatus(401);
        $response->assertJson([
            'success' => false,
            'error' => 'Signature verification failed'
        ]);
    }

    public function test_payment_endpoint_with_valid_signature()
    {
        // Create a tenant
        $tenant = Tenant::factory()->create();

        // Mock the signature verification
        Http::fake([
            'https://signal.avinertech.com/api/signal/verify' => Http::response([
                'success' => true,
                'decrypted_payload' => [
                    'tenant_id' => $tenant->id,
                    'amount' => 100.00,
                    'currency' => 'USD'
                ]
            ])
        ]);

        $response = $this->postJson('/api/stripe/payment', [
            'tenant_id' => $tenant->id,
            'amount' => 100.00,
            'currency' => 'USD'
        ], [
            'X-APP-SIGNATURE' => 'test_signature_123'
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'message' => 'Payment processed successfully'
        ]);
    }

    public function test_payment_verification_endpoint()
    {
        // Mock signature verification
        Http::fake([
            'https://signal.avinertech.com/api/signal/verify' => Http::response([
                'success' => true,
                'decrypted_payload' => []
            ])
        ]);

        $response = $this->getJson('/api/payment/verify/test_transaction_123', [
            'X-APP-SIGNATURE' => 'test_signature_123'
        ]);

        // This will return an error since the transaction doesn't exist,
        // but it shows the endpoint is working
        $response->assertStatus(422);
    }

    public function test_health_check_endpoint()
    {
        $response = $this->getJson('/api/healthz');

        $response->assertStatus(200);
        $response->assertJson([
            'status' => 'healthy',
            'service' => 'payment.avinertech.com'
        ]);
    }

    public function test_cached_signature_verification()
    {
        $tenant = Tenant::factory()->create();
        $signature = 'test_signature_123';
        $cacheKey = 'signature_verified_' . md5($signature);
        
        // Pre-cache the verification result
        Cache::put($cacheKey, [
            'tenant_id' => $tenant->id,
            'amount' => 100.00,
            'currency' => 'USD'
        ], 300);

        // This should not make an HTTP call since it's cached
        Http::fake();

        $response = $this->postJson('/api/stripe/payment', [
            'tenant_id' => $tenant->id,
            'amount' => 100.00,
            'currency' => 'USD'
        ], [
            'X-APP-SIGNATURE' => $signature
        ]);

        $response->assertStatus(200);
        
        // Verify no HTTP calls were made (signature was cached)
        Http::assertNothingSent();
    }
} 