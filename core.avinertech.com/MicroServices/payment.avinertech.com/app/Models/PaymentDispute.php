<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PaymentDispute extends Model
{
    use HasFactory;

    protected $fillable = [
        'transaction_id',
        'tenant_id',
        'reason',
        'status',
        'disputed_amount',
        'evidence',
        'resolved_at'
    ];

    protected $casts = [
        'disputed_amount' => 'decimal:2',
        'evidence' => 'array',
        'resolved_at' => 'datetime',
    ];

    /**
     * Get the transaction this dispute belongs to.
     */
    public function transaction(): BelongsTo
    {
        return $this->belongsTo(PaymentTransaction::class, 'transaction_id');
    }

    /**
     * Get the tenant that filed this dispute.
     */
    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    /**
     * Scope to get disputes by status.
     */
    public function scopeByStatus($query, string $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope to get open disputes.
     */
    public function scopeOpen($query)
    {
        return $query->whereIn('status', ['opened', 'under_review', 'evidence_required']);
    }

    /**
     * Scope to get resolved disputes.
     */
    public function scopeResolved($query)
    {
        return $query->whereIn('status', ['resolved_won', 'resolved_lost', 'closed']);
    }

    /**
     * Check if the dispute is open.
     */
    public function isOpen(): bool
    {
        return in_array($this->status, ['opened', 'under_review', 'evidence_required']);
    }

    /**
     * Check if the dispute is resolved.
     */
    public function isResolved(): bool
    {
        return in_array($this->status, ['resolved_won', 'resolved_lost', 'closed']);
    }

    /**
     * Mark the dispute as won.
     */
    public function markAsWon(): void
    {
        $this->update([
            'status' => 'resolved_won',
            'resolved_at' => now()
        ]);
    }

    /**
     * Mark the dispute as lost.
     */
    public function markAsLost(): void
    {
        $this->update([
            'status' => 'resolved_lost',
            'resolved_at' => now()
        ]);
    }

    /**
     * Close the dispute.
     */
    public function close(): void
    {
        $this->update([
            'status' => 'closed',
            'resolved_at' => now()
        ]);
    }
} 