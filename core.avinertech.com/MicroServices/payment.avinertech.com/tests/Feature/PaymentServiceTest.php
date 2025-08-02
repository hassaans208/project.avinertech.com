<?php

namespace Tests\Feature;

use App\Models\PaymentMethod;
use App\Models\PaymentTransaction;
use App\Models\Tenant;
use App\Services\PaymentService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PaymentServiceTest extends TestCase
{
    use RefreshDatabase;

    protected PaymentService $paymentService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->paymentService = app(PaymentService::class);
    }

    public function test_payment_processing_with_successful_first_method()
    {
        // Create a tenant
        $tenant = Tenant::factory()->create();

        // Create payment methods
        PaymentMethod::create([
            'name' => 'stripe',
            'config' => [
                'api_key' => 'test_key',
                'sandbox_mode' => true
            ],
            'is_active' => true,
            'order' => 1
        ]);

        $paymentData = [
            'tenant_id' => $tenant->id,
            'amount' => 100.00,
            'currency' => 'USD',
            'transaction_id' => 'test_' . uniqid()
        ];

        $result = $this->paymentService->processPayment($paymentData);

        $this->assertTrue($result['success']);
        $this->assertEquals('stripe', $result['method_used']);
        $this->assertInstanceOf(PaymentTransaction::class, $result['transaction']);
    }

    public function test_payment_processing_with_fallback()
    {
        // Create a tenant
        $tenant = Tenant::factory()->create();

        // Create multiple payment methods
        PaymentMethod::create([
            'name' => 'stripe',
            'config' => ['sandbox_mode' => true],
            'is_active' => true,
            'order' => 1
        ]);

        PaymentMethod::create([
            'name' => 'paypal',
            'config' => ['sandbox_mode' => true],
            'is_active' => true,
            'order' => 2
        ]);

        $paymentData = [
            'tenant_id' => $tenant->id,
            'amount' => 100.00,
            'currency' => 'USD',
            'transaction_id' => 'test_' . uniqid()
        ];

        $result = $this->paymentService->processPayment($paymentData);

        $this->assertTrue($result['success']);
        $this->assertInstanceOf(PaymentTransaction::class, $result['transaction']);
        
        // Check that fallback records were created
        $transaction = $result['transaction'];
        $this->assertGreaterThan(0, $transaction->fallbacks()->count());
    }

    public function test_payment_verification()
    {
        // Create a completed transaction
        $tenant = Tenant::factory()->create();
        $method = PaymentMethod::create([
            'name' => 'stripe',
            'config' => ['sandbox_mode' => true],
            'is_active' => true,
            'order' => 1
        ]);

        $transaction = PaymentTransaction::create([
            'tenant_id' => $tenant->id,
            'method_id' => $method->id,
            'transaction_id' => 'test_' . uniqid(),
            'amount' => 100.00,
            'currency' => 'USD',
            'status' => 'completed'
        ]);

        $result = $this->paymentService->verifyPayment($transaction->transaction_id);

        $this->assertTrue($result['success']);
    }
} 