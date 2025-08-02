<?php

namespace App\Http\Controllers;

use App\Exceptions\MethodNotSupportedException;
use App\Http\Requests\PaymentRequest;
use App\Services\PaymentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PaymentRouterController extends Controller
{
    public function __construct(
        private PaymentService $paymentService
    ) {}

    /**
     * Handle payment requests for any method.
     */
    public function handle(Request $request, string $method): JsonResponse
    {
        try {
            // Validate the payment request
            $validatedData = app(PaymentRequest::class)->validated();
            
            // Get the decrypted payload from middleware
            $decryptedPayload = $request->get('decrypted_payload', []);
            
            // Merge decrypted payload with request data
            $paymentData = array_merge($validatedData, $decryptedPayload);
            
            Log::info('Payment request received', [
                'method' => $method,
                'tenant_id' => $paymentData['tenant_id'] ?? null,
                'amount' => $paymentData['amount'] ?? null
            ]);

            // Process the payment
            $result = $this->paymentService->processPayment($paymentData);

            return response()->json([
                'success' => true,
                'message' => 'Payment processed successfully',
                'data' => [
                    'transaction_id' => $result['transaction']->transaction_id,
                    'status' => $result['transaction']->status,
                    'method_used' => $result['method_used'],
                    'amount' => $result['transaction']->amount,
                    'currency' => $result['transaction']->currency,
                ]
            ]);

        } catch (MethodNotSupportedException $e) {
            Log::warning('Unsupported payment method', [
                'method' => $method,
                'error' => $e->getMessage()
            ]);
            
            return $e->render();
            
        } catch (\Exception $e) {
            Log::error('Payment processing error', [
                'method' => $method,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'error' => 'Payment processing failed',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Verify a payment transaction.
     */
    public function verify(Request $request, string $transactionId): JsonResponse
    {
        try {
            $result = $this->paymentService->verifyPayment($transactionId);

            return response()->json([
                'success' => true,
                'message' => 'Payment verification completed',
                'data' => $result
            ]);

        } catch (\Exception $e) {
            Log::error('Payment verification error', [
                'transaction_id' => $transactionId,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'error' => 'Payment verification failed',
                'message' => $e->getMessage()
            ], 422);
        }
    }

    /**
     * Process a refund.
     */
    public function refund(Request $request, int $transactionId): JsonResponse
    {
        try {
            $amount = $request->input('amount');
            $result = $this->paymentService->refundPayment($transactionId, $amount);

            return response()->json([
                'success' => true,
                'message' => 'Refund processed successfully',
                'data' => $result
            ]);

        } catch (\Exception $e) {
            Log::error('Refund processing error', [
                'transaction_id' => $transactionId,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'error' => 'Refund processing failed',
                'message' => $e->getMessage()
            ], 422);
        }
    }
} 