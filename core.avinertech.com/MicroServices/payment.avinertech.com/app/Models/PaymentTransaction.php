<?php

namespace App\Models;

use App\Events\PaymentCompleted;
use App\Events\PaymentFailed;
use App\Events\PaymentInitiated;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class PaymentTransaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'tenant_id',
        'method_id',
        'transaction_id',
        'amount',
        'currency',
        'status',
        'package_cost',
        'metadata',
        'processed_at'
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'package_cost' => 'decimal:2',
        'metadata' => 'array',
        'processed_at' => 'datetime',
    ];

    protected $dispatchesEvents = [
        'creating' => PaymentInitiated::class,
    ];

    /**
     * Boot the model and register event listeners.
     */
    protected static function boot()
    {
        parent::boot();

        static::updated(function ($transaction) {
            if ($transaction->wasChanged('status')) {
                switch ($transaction->status) {
                    case 'completed':
                        event(new PaymentCompleted($transaction));
                        break;
                    case 'failed':
                        event(new PaymentFailed($transaction));
                        break;
                }
            }
        });
    }

    /**
     * Get the tenant that owns the transaction.
     */
    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    /**
     * Get the payment method used for this transaction.
     */
    public function paymentMethod(): BelongsTo
    {
        return $this->belongsTo(PaymentMethod::class, 'method_id');
    }

    /**
     * Get all fallback attempts for this transaction.
     */
    public function fallbacks(): HasMany
    {
        return $this->hasMany(PaymentFallback::class, 'transaction_id');
    }

    /**
     * Get all logs for this transaction.
     */
    public function logs(): HasMany
    {
        return $this->hasMany(PaymentLog::class, 'transaction_id');
    }

    /**
     * Get the dispute for this transaction.
     */
    public function dispute(): HasOne
    {
        return $this->hasOne(PaymentDispute::class, 'transaction_id');
    }

    /**
     * Scope to get transactions by status.
     */
    public function scopeByStatus($query, string $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope to get transactions for a specific tenant.
     */
    public function scopeForTenant($query, int $tenantId)
    {
        return $query->where('tenant_id', $tenantId);
    }

    /**
     * Check if the transaction is successful.
     */
    public function isSuccessful(): bool
    {
        return $this->status === 'completed';
    }

    /**
     * Check if the transaction has failed.
     */
    public function hasFailed(): bool
    {
        return $this->status === 'failed';
    }

    /**
     * Check if the transaction is pending.
     */
    public function isPending(): bool
    {
        return in_array($this->status, ['pending', 'processing']);
    }

    /**
     * Mark the transaction as completed.
     */
    public function markAsCompleted(): void
    {
        $this->update([
            'status' => 'completed',
            'processed_at' => now()
        ]);
    }

    /**
     * Mark the transaction as failed.
     */
    public function markAsFailed(): void
    {
        $this->update([
            'status' => 'failed',
            'processed_at' => now()
        ]);
    }
} 