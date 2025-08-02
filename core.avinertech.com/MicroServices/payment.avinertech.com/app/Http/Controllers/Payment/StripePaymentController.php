<?php

namespace App\Http\Controllers\Payment;

use App\Contracts\PaymentInterface;
use App\Http\Controllers\Controller;
use App\Models\PaymentTransaction;
use App\Repositories\PaymentMethodRepositoryInterface;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class StripePaymentController extends Controller implements PaymentInterface
{
    private array $config;

    public function __construct(
        private PaymentMethodRepositoryInterface $paymentMethodRepository
    ) {
        $method = $this->paymentMethodRepository->findByName('stripe');
        $this->config = $method ? $method->config : [];
    }

    /**
     * Process a payment transaction.
     */
    public function processPayment(array $paymentData): array
    {
        try {
            if (!$this->validatePaymentData($paymentData)) {
                return [
                    'success' => false,
                    'message' => 'Invalid payment data for Stripe'
                ];
            }

            // Simulate Stripe API call
            $response = $this->callStripeAPI('/v1/payment_intents', [
                'amount' => $paymentData['amount'] * 100, // Stripe uses cents
                'currency' => strtolower($paymentData['currency']),
                'metadata' => [
                    'tenant_id' => $paymentData['tenant_id'],
                    'transaction_id' => $paymentData['transaction_id']
                ]
            ]);

            if ($response['success']) {
                return [
                    'success' => true,
                    'transaction_id' => $response['data']['id'] ?? $paymentData['transaction_id'],
                    'status' => 'completed',
                    'provider_response' => $response['data']
                ];
            }

            return [
                'success' => false,
                'message' => $response['error'] ?? 'Stripe payment failed'
            ];

        } catch (\Exception $e) {
            Log::error('Stripe payment error', [
                'transaction_id' => $paymentData['transaction_id'] ?? null,
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'message' => 'Stripe payment processing failed: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Verify a payment transaction.
     */
    public function verifyPayment(string $transactionId): array
    {
        try {
            $response = $this->callStripeAPI("/v1/payment_intents/{$transactionId}");

            if ($response['success']) {
                return [
                    'success' => true,
                    'status' => $response['data']['status'] ?? 'unknown',
                    'verified' => true,
                    'provider_data' => $response['data']
                ];
            }

            return [
                'success' => false,
                'message' => 'Transaction verification failed'
            ];

        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Stripe verification error: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Refund a payment transaction.
     */
    public function refundPayment(PaymentTransaction $transaction, float $amount = null): array
    {
        try {
            $refundAmount = $amount ?? $transaction->amount;
            
            $response = $this->callStripeAPI('/v1/refunds', [
                'payment_intent' => $transaction->transaction_id,
                'amount' => $refundAmount * 100, // Stripe uses cents
                'metadata' => [
                    'original_transaction_id' => $transaction->id
                ]
            ]);

            if ($response['success']) {
                return [
                    'success' => true,
                    'refund_id' => $response['data']['id'] ?? null,
                    'amount_refunded' => $refundAmount,
                    'provider_response' => $response['data']
                ];
            }

            return [
                'success' => false,
                'message' => $response['error'] ?? 'Stripe refund failed'
            ];

        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Stripe refund error: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Handle webhook notifications.
     */
    public function handleWebhook(array $webhookData): array
    {
        try {
            // Verify webhook signature (simplified)
            if (!$this->verifyWebhookSignature($webhookData)) {
                return [
                    'success' => false,
                    'message' => 'Invalid webhook signature'
                ];
            }

            $eventType = $webhookData['type'] ?? null;
            
            switch ($eventType) {
                case 'payment_intent.succeeded':
                    // Handle successful payment
                    break;
                case 'payment_intent.payment_failed':
                    // Handle failed payment
                    break;
                default:
                    Log::info('Unhandled Stripe webhook event', ['type' => $eventType]);
            }

            return [
                'success' => true,
                'message' => 'Webhook processed successfully'
            ];

        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Webhook processing error: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Get payment method configuration.
     */
    public function getConfig(): array
    {
        return $this->config;
    }

    /**
     * Validate payment data before processing.
     */
    public function validatePaymentData(array $data): bool
    {
        $required = ['transaction_id', 'amount', 'currency', 'tenant_id'];
        
        foreach ($required as $field) {
            if (!isset($data[$field]) || empty($data[$field])) {
                return false;
            }
        }

        return true;
    }

    /**
     * Make API call to Stripe.
     */
    private function callStripeAPI(string $endpoint, array $data = []): array
    {
        try {
            $apiKey = $this->config['api_key'] ?? null;
            
            if (!$apiKey) {
                return [
                    'success' => false,
                    'error' => 'Stripe API key not configured'
                ];
            }

            // For sandbox/demo purposes, simulate API responses
            if ($this->config['sandbox_mode'] ?? true) {
                return $this->simulateStripeResponse($endpoint, $data);
            }

            // Real Stripe API call would go here
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $apiKey,
                'Content-Type' => 'application/x-www-form-urlencoded'
            ])->post('https://api.stripe.com' . $endpoint, $data);

            if ($response->successful()) {
                return [
                    'success' => true,
                    'data' => $response->json()
                ];
            }

            return [
                'success' => false,
                'error' => $response->json()['error']['message'] ?? 'Stripe API error'
            ];

        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => 'Stripe API call failed: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Simulate Stripe API responses for sandbox mode.
     */
    private function simulateStripeResponse(string $endpoint, array $data): array
    {
        // Simulate different scenarios for testing
        $random = rand(1, 100);
        
        if ($random <= 85) { // 85% success rate
            return [
                'success' => true,
                'data' => [
                    'id' => 'pi_' . uniqid(),
                    'status' => 'succeeded',
                    'amount' => $data['amount'] ?? 1000,
                    'currency' => $data['currency'] ?? 'usd',
                    'created' => time()
                ]
            ];
        } else { // 15% failure rate for testing
            return [
                'success' => false,
                'error' => 'Your card was declined.'
            ];
        }
    }

    /**
     * Verify webhook signature (simplified).
     */
    private function verifyWebhookSignature(array $webhookData): bool
    {
        // In a real implementation, this would verify the Stripe webhook signature
        // For now, just return true for demo purposes
        return true;
    }
} 