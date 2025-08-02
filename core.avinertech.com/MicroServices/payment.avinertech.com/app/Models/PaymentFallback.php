<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PaymentFallback extends Model
{
    use HasFactory;

    protected $fillable = [
        'transaction_id',
        'method_id',
        'attempt_order',
        'status',
        'error_message',
        'tried_at'
    ];

    protected $casts = [
        'tried_at' => 'datetime',
    ];

    /**
     * Get the transaction this fallback belongs to.
     */
    public function transaction(): BelongsTo
    {
        return $this->belongsTo(PaymentTransaction::class, 'transaction_id');
    }

    /**
     * Get the payment method for this fallback attempt.
     */
    public function paymentMethod(): BelongsTo
    {
        return $this->belongsTo(PaymentMethod::class, 'method_id');
    }

    /**
     * Scope to get fallbacks by attempt order.
     */
    public function scopeByOrder($query, int $order)
    {
        return $query->where('attempt_order', $order);
    }

    /**
     * Scope to get fallbacks by status.
     */
    public function scopeByStatus($query, string $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Mark the fallback attempt as tried.
     */
    public function markAsAttempted(string $errorMessage = null): void
    {
        $this->update([
            'status' => 'attempted',
            'error_message' => $errorMessage,
            'tried_at' => now()
        ]);
    }

    /**
     * Mark the fallback attempt as succeeded.
     */
    public function markAsSucceeded(): void
    {
        $this->update([
            'status' => 'succeeded',
            'tried_at' => now()
        ]);
    }

    /**
     * Mark the fallback attempt as failed.
     */
    public function markAsFailed(string $errorMessage): void
    {
        $this->update([
            'status' => 'failed',
            'error_message' => $errorMessage,
            'tried_at' => now()
        ]);
    }
} 