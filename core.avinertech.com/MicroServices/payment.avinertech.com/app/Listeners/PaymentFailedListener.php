<?php

namespace App\Listeners;

use App\Events\PaymentFailed;
use App\Models\PaymentLog;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

class PaymentFailedListener implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * Handle the event.
     */
    public function handle(PaymentFailed $event): void
    {
        $transaction = $event->transaction;

        // Log the payment failure
        PaymentLog::error(
            $transaction->id,
            'Payment failed after all fallback attempts',
            [
                'tenant_id' => $transaction->tenant_id,
                'amount' => $transaction->amount,
                'currency' => $transaction->currency,
                'failed_at' => $transaction->processed_at
            ]
        );

        // Additional actions for failed payments:
        // - Send failure notifications
        // - Trigger retry mechanisms
        // - Update tenant status
        // - Alert administrators
        
        Log::error('Payment failed', [
            'transaction_id' => $transaction->transaction_id,
            'tenant_id' => $transaction->tenant_id,
            'amount' => $transaction->amount,
            'fallback_attempts' => $transaction->fallbacks()->count()
        ]);

        // Example: Send alert to administrators
        // $this->alertAdministrators($transaction);
    }

    /**
     * Example method to alert administrators
     */
    private function alertAdministrators($transaction): void
    {
        // This could send emails, Slack notifications, etc.
        // when payments fail after all fallback attempts
    }
} 