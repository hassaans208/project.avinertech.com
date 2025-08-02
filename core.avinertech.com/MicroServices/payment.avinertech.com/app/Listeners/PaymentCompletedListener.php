<?php

namespace App\Listeners;

use App\Events\PaymentCompleted;
use App\Models\PaymentLog;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

class PaymentCompletedListener implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * Handle the event.
     */
    public function handle(PaymentCompleted $event): void
    {
        $transaction = $event->transaction;

        // Log the successful payment
        PaymentLog::info(
            $transaction->id,
            'Payment completed successfully',
            [
                'method_used' => $transaction->paymentMethod->name ?? 'unknown',
                'amount' => $transaction->amount,
                'currency' => $transaction->currency,
                'processed_at' => $transaction->processed_at
            ]
        );

        // Additional actions for completed payments:
        // - Send confirmation emails
        // - Update tenant subscription status
        // - Trigger fulfillment processes
        // - Update analytics/reporting
        
        Log::info('Payment completed successfully', [
            'transaction_id' => $transaction->transaction_id,
            'tenant_id' => $transaction->tenant_id,
            'amount' => $transaction->amount,
            'method' => $transaction->paymentMethod->name ?? 'unknown'
        ]);

        // Example: You might want to call an external service
        // $this->notifyExternalService($transaction);
    }

    /**
     * Example method to notify external services
     */
    private function notifyExternalService($transaction): void
    {
        // This could call signal.avinertech.com or other services
        // to notify them of the successful payment
    }
} 