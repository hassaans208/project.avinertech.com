<?php

namespace App\Services;

use App\Contracts\PaymentInterface;
use App\Events\PaymentAttemptFailed;
use App\Exceptions\MethodNotSupportedException;
use App\Exceptions\PaymentFailedException;
use App\Models\PaymentFallback;
use App\Models\PaymentLog;
use App\Models\PaymentMethod;
use App\Models\PaymentTransaction;
use App\Repositories\PaymentMethodRepositoryInterface;
use App\Repositories\PaymentTransactionRepositoryInterface;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class PaymentService
{
    public function __construct(
        private PaymentMethodRepositoryInterface $paymentMethodRepository,
        private PaymentTransactionRepositoryInterface $transactionRepository
    ) {}

    /**
     * Process payment with fallback support.
     */
    public function processPayment(array $paymentData): array
    {
        $transaction = $this->createTransaction($paymentData);
        
        PaymentLog::info($transaction->id, 'Payment processing initiated', $paymentData);
        
        $activeMethods = $this->paymentMethodRepository->getActiveMethodsOrdered();
        
        if ($activeMethods->isEmpty()) {
            throw new MethodNotSupportedException('No active payment methods available');
        }

        // Create fallback records
        $this->createFallbackRecords($transaction, $activeMethods);

        // Attempt payment with each method in order
        foreach ($activeMethods as $order => $method) {
            try {
                $result = $this->attemptPayment($transaction, $method, $order + 1);
                
                if ($result['success']) {
                    $transaction->markAsCompleted();
                    PaymentLog::info($transaction->id, "Payment completed successfully with {$method->name}");
                    
                    return [
                        'success' => true,
                        'transaction' => $transaction,
                        'method_used' => $method->name,
                        'result' => $result
                    ];
                }
                
            } catch (\Exception $e) {
                $this->handlePaymentAttemptFailure($transaction, $method, $order + 1, $e->getMessage());
                
                // Continue to next method
                continue;
            }
        }

        // All methods failed
        $transaction->markAsFailed();
        PaymentLog::error($transaction->id, 'All payment methods failed');
        
        throw new PaymentFailedException('Payment failed with all available methods');
    }

    /**
     * Attempt payment with a specific method.
     */
    private function attemptPayment(PaymentTransaction $transaction, PaymentMethod $method, int $attemptOrder): array
    {
        $fallback = PaymentFallback::where('transaction_id', $transaction->id)
            ->where('method_id', $method->id)
            ->where('attempt_order', $attemptOrder)
            ->first();

        if (!$fallback) {
            throw new \Exception("Fallback record not found for attempt {$attemptOrder}");
        }

        PaymentLog::info($transaction->id, "Attempting payment with {$method->name} (attempt {$attemptOrder})");
        
        $fallback->markAsAttempted();
        
        // Get the payment controller for this method
        $controller = $this->getPaymentController($method);
        
        // Prepare payment data
        $paymentData = [
            'transaction_id' => $transaction->transaction_id,
            'amount' => $transaction->amount,
            'currency' => $transaction->currency,
            'tenant_id' => $transaction->tenant_id,
            'metadata' => $transaction->metadata
        ];

        try {
            $result = $controller->processPayment($paymentData);
            
            if ($result['success'] ?? false) {
                $fallback->markAsSucceeded();
                PaymentLog::info($transaction->id, "Payment succeeded with {$method->name}");
                return $result;
            } else {
                $errorMessage = $result['message'] ?? 'Payment failed';
                $fallback->markAsFailed($errorMessage);
                throw new PaymentFailedException($errorMessage);
            }
            
        } catch (\Exception $e) {
            $fallback->markAsFailed($e->getMessage());
            throw $e;
        }
    }

    /**
     * Create a new payment transaction.
     */
    private function createTransaction(array $paymentData): PaymentTransaction
    {
        return $this->transactionRepository->create([
            'tenant_id' => $paymentData['tenant_id'],
            'method_id' => $paymentData['method_id'] ?? null, // Will be set when payment succeeds
            'transaction_id' => $paymentData['transaction_id'] ?? Str::uuid(),
            'amount' => $paymentData['amount'],
            'currency' => $paymentData['currency'] ?? 'USD',
            'status' => 'pending',
            'package_cost' => $paymentData['package_cost'] ?? null,
            'metadata' => $paymentData['metadata'] ?? []
        ]);
    }

    /**
     * Create fallback records for all active methods.
     */
    private function createFallbackRecords(PaymentTransaction $transaction, $methods): void
    {
        foreach ($methods as $order => $method) {
            PaymentFallback::create([
                'transaction_id' => $transaction->id,
                'method_id' => $method->id,
                'attempt_order' => $order + 1,
                'status' => 'pending'
            ]);
        }
    }

    /**
     * Handle payment attempt failure.
     */
    private function handlePaymentAttemptFailure(PaymentTransaction $transaction, PaymentMethod $method, int $attemptOrder, string $errorMessage): void
    {
        $fallback = PaymentFallback::where('transaction_id', $transaction->id)
            ->where('method_id', $method->id)
            ->where('attempt_order', $attemptOrder)
            ->first();

        if ($fallback) {
            $fallback->markAsFailed($errorMessage);
            event(new PaymentAttemptFailed($transaction, $fallback, $errorMessage));
        }

        PaymentLog::error($transaction->id, "Payment failed with {$method->name}: {$errorMessage}");
    }

    /**
     * Get payment controller instance for a method.
     */
    private function getPaymentController(PaymentMethod $method): PaymentInterface
    {
        $controllerClass = 'App\\Http\\Controllers\\Payment\\' . ucfirst($method->name) . 'PaymentController';
        
        if (!class_exists($controllerClass)) {
            throw new MethodNotSupportedException("Controller not found for method: {$method->name}");
        }

        $controller = app($controllerClass);
        
        if (!$controller instanceof PaymentInterface) {
            throw new MethodNotSupportedException("Controller for {$method->name} does not implement PaymentInterface");
        }

        return $controller;
    }

    /**
     * Verify a payment transaction.
     */
    public function verifyPayment(string $transactionId): array
    {
        $transaction = $this->transactionRepository->findByTransactionId($transactionId);
        
        if (!$transaction) {
            throw new PaymentFailedException("Transaction not found: {$transactionId}");
        }

        $controller = $this->getPaymentController($transaction->paymentMethod);
        
        return $controller->verifyPayment($transactionId);
    }

    /**
     * Process refund for a transaction.
     */
    public function refundPayment(int $transactionId, float $amount = null): array
    {
        $transaction = $this->transactionRepository->findById($transactionId);
        
        if (!$transaction) {
            throw new PaymentFailedException("Transaction not found: {$transactionId}");
        }

        if (!$transaction->isSuccessful()) {
            throw new PaymentFailedException("Cannot refund unsuccessful transaction");
        }

        $controller = $this->getPaymentController($transaction->paymentMethod);
        
        $result = $controller->refundPayment($transaction, $amount);
        
        if ($result['success'] ?? false) {
            $transaction->update(['status' => 'refunded']);
            PaymentLog::info($transaction->id, "Refund processed successfully");
        }

        return $result;
    }
} 