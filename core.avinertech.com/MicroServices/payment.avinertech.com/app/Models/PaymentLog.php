<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PaymentLog extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'transaction_id',
        'log_message',
        'level',
        'context',
        'created_at'
    ];

    protected $casts = [
        'context' => 'array',
        'created_at' => 'datetime',
    ];

    /**
     * Get the transaction this log belongs to.
     */
    public function transaction(): BelongsTo
    {
        return $this->belongsTo(PaymentTransaction::class, 'transaction_id');
    }

    /**
     * Scope to get logs by level.
     */
    public function scopeByLevel($query, string $level)
    {
        return $query->where('level', $level);
    }

    /**
     * Scope to get recent logs.
     */
    public function scopeRecent($query, int $hours = 24)
    {
        return $query->where('created_at', '>=', now()->subHours($hours));
    }

    /**
     * Create a debug log entry.
     */
    public static function debug(int $transactionId, string $message, array $context = []): self
    {
        return self::create([
            'transaction_id' => $transactionId,
            'log_message' => $message,
            'level' => 'debug',
            'context' => $context,
            'created_at' => now()
        ]);
    }

    /**
     * Create an info log entry.
     */
    public static function info(int $transactionId, string $message, array $context = []): self
    {
        return self::create([
            'transaction_id' => $transactionId,
            'log_message' => $message,
            'level' => 'info',
            'context' => $context,
            'created_at' => now()
        ]);
    }

    /**
     * Create a warning log entry.
     */
    public static function warning(int $transactionId, string $message, array $context = []): self
    {
        return self::create([
            'transaction_id' => $transactionId,
            'log_message' => $message,
            'level' => 'warning',
            'context' => $context,
            'created_at' => now()
        ]);
    }

    /**
     * Create an error log entry.
     */
    public static function error(int $transactionId, string $message, array $context = []): self
    {
        return self::create([
            'transaction_id' => $transactionId,
            'log_message' => $message,
            'level' => 'error',
            'context' => $context,
            'created_at' => now()
        ]);
    }

    /**
     * Create a critical log entry.
     */
    public static function critical(int $transactionId, string $message, array $context = []): self
    {
        return self::create([
            'transaction_id' => $transactionId,
            'log_message' => $message,
            'level' => 'critical',
            'context' => $context,
            'created_at' => now()
        ]);
    }
} 