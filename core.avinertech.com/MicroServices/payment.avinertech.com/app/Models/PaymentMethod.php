<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PaymentMethod extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'config',
        'is_active',
        'order'
    ];

    protected $casts = [
        'config' => 'array',
        'is_active' => 'boolean',
    ];

    /**
     * Get all transactions for this payment method.
     */
    public function transactions(): HasMany
    {
        return $this->hasMany(PaymentTransaction::class, 'method_id');
    }

    /**
     * Get all fallback attempts for this payment method.
     */
    public function fallbacks(): HasMany
    {
        return $this->hasMany(PaymentFallback::class, 'method_id');
    }

    /**
     * Scope to get only active payment methods.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope to get payment methods ordered by fallback sequence.
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('order');
    }

    /**
     * Get the configuration value for a specific key.
     */
    public function getConfigValue(string $key, $default = null)
    {
        return data_get($this->config, $key, $default);
    }

    /**
     * Check if the payment method is in sandbox mode.
     */
    public function isSandbox(): bool
    {
        return $this->getConfigValue('sandbox_mode', false);
    }
} 