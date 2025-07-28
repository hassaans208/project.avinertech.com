<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Package extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'cost',
        'currency',
        'tax_rate',
        'modules',
    ];

    protected $casts = [
        'cost' => 'decimal:2',
        'tax_rate' => 'decimal:4',
        'modules' => 'array',
    ];

    /**
     * Get the tenants associated with this package.
     */
    public function tenants(): BelongsToMany
    {
        return $this->belongsToMany(Tenant::class, 'tenant_package')
            ->withPivot('registered_at')
            ->withTimestamps();
    }

    /**
     * Check if this is the free package.
     */
    public function isFree(): bool
    {
        return $this->cost == 0 || $this->name === 'free_package';
    }

    /**
     * Get formatted cost with currency.
     */
    public function getFormattedCostAttribute(): string
    {
        return number_format($this->cost, 2) . ' ' . $this->currency;
    }

    /**
     * Check if package has a specific module.
     */
    public function hasModule(string $module): bool
    {
        return in_array($module, $this->modules ?? []);
    }

    /**
     * Get available modules list.
     */
    public static function getAvailableModules(): array
    {
        return [
            'ai_integration',
            'payment_methods',
            'analytics',
            'custom_domains',
            'api_access',
            'priority_support',
            'white_label',
            'advanced_security',
        ];
    }

    /**
     * Scope to find package by snake_case name.
     */
    public function scopeByName($query, string $name)
    {
        return $query->where('name', strtolower(str_replace(' ', '_', $name)));
    }
} 