<?php

namespace App\Listeners;

use App\Events\PaymentAttemptFailed;
use App\Models\PaymentLog;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

class PaymentAttemptFailedListener implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * Handle the event.
     */
    public function handle(PaymentAttemptFailed $event): void
    {
        $transaction = $event->transaction;
        $fallback = $event->fallback;
        $errorMessage = $event->errorMessage;

        // Log the individual payment attempt failure
        PaymentLog::warning(
            $transaction->id,
            "Payment attempt failed with {$fallback->paymentMethod->name}",
            [
                'method_name' => $fallback->paymentMethod->name,
                'attempt_order' => $fallback->attempt_order,
                'error_message' => $errorMessage,
                'fallback_id' => $fallback->id
            ]
        );

        // Additional actions for failed attempts:
        // - Update method reliability metrics
        // - Adjust fallback ordering based on failure rates
        // - Send alerts if critical methods are failing
        
        Log::warning('Payment attempt failed', [
            'transaction_id' => $transaction->transaction_id,
            'method' => $fallback->paymentMethod->name,
            'attempt_order' => $fallback->attempt_order,
            'error' => $errorMessage
        ]);

        // Example: Track method reliability
        // $this->updateMethodReliability($fallback->paymentMethod, false);
    }

    /**
     * Example method to update payment method reliability metrics
     */
    private function updateMethodReliability($paymentMethod, bool $success): void
    {
        // This could update a reliability score or failure count
        // to help optimize fallback ordering
    }
} 