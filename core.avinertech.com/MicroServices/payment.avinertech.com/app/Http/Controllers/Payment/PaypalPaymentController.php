<?php

namespace App\Http\Controllers\Payment;

use App\Contracts\PaymentInterface;
use App\Http\Controllers\Controller;
use App\Models\PaymentTransaction;
use App\Repositories\PaymentMethodRepositoryInterface;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PaypalPaymentController extends Controller implements PaymentInterface
{
    private array $config;

    public function __construct(
        private PaymentMethodRepositoryInterface $paymentMethodRepository
    ) {
        $method = $this->paymentMethodRepository->findByName('paypal');
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
                    'message' => 'Invalid payment data for PayPal'
                ];
            }

            // Get access token first
            $accessToken = $this->getAccessToken();
            if (!$accessToken) {
                return [
                    'success' => false,
                    'message' => 'Failed to authenticate with PayPal'
                ];
            }

            // Create payment
            $response = $this->callPayPalAPI('/v2/checkout/orders', [
                'intent' => 'CAPTURE',
                'purchase_units' => [
                    [
                        'amount' => [
                            'currency_code' => strtoupper($paymentData['currency']),
                            'value' => number_format($paymentData['amount'], 2, '.', '')
                        ],
                        'custom_id' => $paymentData['transaction_id']
                    ]
                ]
            ], $accessToken);

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
                'message' => $response['error'] ?? 'PayPal payment failed'
            ];

        } catch (\Exception $e) {
            Log::error('PayPal payment error', [
                'transaction_id' => $paymentData['transaction_id'] ?? null,
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'message' => 'PayPal payment processing failed: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Verify a payment transaction.
     */
    public function verifyPayment(string $transactionId): array
    {
        try {
            $accessToken = $this->getAccessToken();
            if (!$accessToken) {
                return [
                    'success' => false,
                    'message' => 'Failed to authenticate with PayPal'
                ];
            }

            $response = $this->callPayPalAPI("/v2/checkout/orders/{$transactionId}", [], $accessToken, 'GET');

            if ($response['success']) {
                return [
                    'success' => true,
                    'status' => strtolower($response['data']['status'] ?? 'unknown'),
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
                'message' => 'PayPal verification error: ' . $e->getMessage()
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
            $accessToken = $this->getAccessToken();
            
            if (!$accessToken) {
                return [
                    'success' => false,
                    'message' => 'Failed to authenticate with PayPal'
                ];
            }

            $response = $this->callPayPalAPI("/v2/payments/captures/{$transaction->transaction_id}/refund", [
                'amount' => [
                    'currency_code' => strtoupper($transaction->currency),
                    'value' => number_format($refundAmount, 2, '.', '')
                ]
            ], $accessToken);

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
                'message' => $response['error'] ?? 'PayPal refund failed'
            ];

        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'PayPal refund error: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Handle webhook notifications.
     */
    public function handleWebhook(array $webhookData): array
    {
        try {
            if (!$this->verifyWebhookSignature($webhookData)) {
                return [
                    'success' => false,
                    'message' => 'Invalid webhook signature'
                ];
            }

            $eventType = $webhookData['event_type'] ?? null;
            
            switch ($eventType) {
                case 'CHECKOUT.ORDER.APPROVED':
                    // Handle approved order
                    break;
                case 'PAYMENT.CAPTURE.COMPLETED':
                    // Handle completed payment
                    break;
                case 'PAYMENT.CAPTURE.DENIED':
                    // Handle denied payment
                    break;
                default:
                    Log::info('Unhandled PayPal webhook event', ['type' => $eventType]);
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
     * Get PayPal access token.
     */
    private function getAccessToken(): ?string
    {
        try {
            $clientId = $this->config['client_id'] ?? null;
            $clientSecret = $this->config['client_secret'] ?? null;

            if (!$clientId || !$clientSecret) {
                return null;
            }

            // For sandbox mode, return a mock token
            if ($this->config['sandbox_mode'] ?? true) {
                return 'mock_access_token_' . time();
            }

            $response = Http::withBasicAuth($clientId, $clientSecret)
                ->asForm()
                ->post($this->config['api_base_url'] . '/v1/oauth2/token', [
                    'grant_type' => 'client_credentials'
                ]);

            if ($response->successful()) {
                $data = $response->json();
                return $data['access_token'] ?? null;
            }

            return null;

        } catch (\Exception $e) {
            Log::error('PayPal access token error', ['error' => $e->getMessage()]);
            return null;
        }
    }

    /**
     * Make API call to PayPal.
     */
    private function callPayPalAPI(string $endpoint, array $data = [], string $accessToken = null, string $method = 'POST'): array
    {
        try {
            if (!$accessToken) {
                return [
                    'success' => false,
                    'error' => 'PayPal access token required'
                ];
            }

            // For sandbox mode, simulate responses
            if ($this->config['sandbox_mode'] ?? true) {
                return $this->simulatePayPalResponse($endpoint, $data);
            }

            $baseUrl = $this->config['api_base_url'] ?? 'https://api.sandbox.paypal.com';
            
            $request = Http::withHeaders([
                'Authorization' => 'Bearer ' . $accessToken,
                'Content-Type' => 'application/json',
                'Accept' => 'application/json'
            ]);

            $response = $method === 'GET' 
                ? $request->get($baseUrl . $endpoint)
                : $request->post($baseUrl . $endpoint, $data);

            if ($response->successful()) {
                return [
                    'success' => true,
                    'data' => $response->json()
                ];
            }

            return [
                'success' => false,
                'error' => $response->json()['message'] ?? 'PayPal API error'
            ];

        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => 'PayPal API call failed: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Simulate PayPal API responses for sandbox mode.
     */
    private function simulatePayPalResponse(string $endpoint, array $data): array
    {
        // Simulate different scenarios for testing
        $random = rand(1, 100);
        
        if ($random <= 80) { // 80% success rate
            return [
                'success' => true,
                'data' => [
                    'id' => 'PAYPAL_' . strtoupper(uniqid()),
                    'status' => 'COMPLETED',
                    'create_time' => date('c'),
                    'update_time' => date('c')
                ]
            ];
        } else { // 20% failure rate for testing
            return [
                'success' => false,
                'error' => 'Payment declined by PayPal.'
            ];
        }
    }

    /**
     * Verify webhook signature (simplified).
     */
    private function verifyWebhookSignature(array $webhookData): bool
    {
        // In a real implementation, this would verify the PayPal webhook signature
        // For now, just return true for demo purposes
        return true;
    }
} 