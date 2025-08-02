<?php

namespace App\Events;

use App\Models\PaymentTransaction;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class PaymentInitiated
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public PaymentTransaction $transaction;

    /**
     * Create a new event instance.
     */
    public function __construct(PaymentTransaction $transaction)
    {
        $this->transaction = $transaction;
    }
} 