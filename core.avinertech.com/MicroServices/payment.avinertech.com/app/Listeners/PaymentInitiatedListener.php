<?php

namespace App\Listeners;

use App\Events\PaymentInitiated;
use App\Models\PaymentLog;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

class PaymentInitiatedListener implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * Handle the event.
     */
    public function handle(PaymentInitiated $event): void
    {
        $transaction = $event->transaction;

        // Log the payment initiation
        PaymentLog::info(
            $transaction->id,
            'Payment transaction initiated',
            [
                'tenant_id' => $transaction->tenant_id,
                'amount' => $transaction->amount,
                'currency' => $transaction->currency,
                'transaction_id' => $transaction->transaction_id
            ]
        );

        // You could also:
        // - Send notifications
        // - Update analytics
        // - Trigger webhooks to external systems
        
        Log::info('Payment initiated', [
            'transaction_id' => $transaction->transaction_id,
            'tenant_id' => $transaction->tenant_id,
            'amount' => $transaction->amount
        ]);
    }
} 