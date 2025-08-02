<?php

namespace App\Events;

use App\Models\PaymentFallback;
use App\Models\PaymentTransaction;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class PaymentAttemptFailed
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public PaymentTransaction $transaction;
    public PaymentFallback $fallback;
    public string $errorMessage;

    /**
     * Create a new event instance.
     */
    public function __construct(PaymentTransaction $transaction, PaymentFallback $fallback, string $errorMessage)
    {
        $this->transaction = $transaction;
        $this->fallback = $fallback;
        $this->errorMessage = $errorMessage;
    }
} 