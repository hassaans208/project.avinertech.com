<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SignalLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'tenant_id',
        'encrypted_host_id',
        'decrypted_host',
        'hash_payload',
        'package_name',
        'signal_timestamp',
        'status',
        'error_message',
        'response_data',
    ];

    protected $casts = [
        'signal_timestamp' => 'datetime',
        'response_data' => 'array',
    ];

    /**
     * Get the tenant that owns this signal log.
     */
    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    /**
     * Scope for successful signals.
     */
    public function scopeSuccessful($query)
    {
        return $query->where('status', 'success');
    }

    /**
     * Scope for failed signals.
     */
    public function scopeFailed($query)
    {
        return $query->where('status', '!=', 'success');
    }

    /**
     * Scope for recent signals.
     */
    public function scopeRecent($query, int $hours = 24)
    {
        return $query->where('created_at', '>=', now()->subHours($hours));
    }
} 