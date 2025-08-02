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
    ];

    protected $casts = [
        'cost' => 'decimal:2',
        'tax_rate' => 'decimal:4',
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
     * Get the service modules associated with this package.
     */
    public function serviceModules(): BelongsToMany
    {
        return $this->belongsToMany(ServiceModule::class, 'package_service_modules')
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
     * Check if package has a specific service module.
     */
    public function hasServiceModule(string $moduleName): bool
    {
        return $this->serviceModules()->where('name', $moduleName)->exists();
    }

    /**
     * Get total cost price from all service modules.
     */
    public function getTotalCostPriceAttribute(): float
    {
        return round($this->serviceModules->sum('cost_price'), 2);
    }

    /**
     * Get total sale price from all service modules.
     */
    public function getTotalSalePriceAttribute(): float
    {
        return round($this->serviceModules->sum('sale_price'), 2);
    }

    /**
     * Get total tax amount from all service modules.
     */
    public function getTotalTaxAttribute(): float
    {
        return round($this->serviceModules->sum(function ($module) {
            return $module->sale_price * $module->tax_rate;
        }), 2);
    }

    /**
     * Get total sale price including tax from all service modules.
     */
    public function getTotalSalePriceInclTaxAttribute(): float
    {
        return round($this->getTotalSalePriceAttribute() + $this->getTotalTaxAttribute(), 2);
    }

    /**
     * Get total profit from all service modules.
     */
    public function getTotalProfitAttribute(): float
    {
        return round($this->getTotalSalePriceAttribute() - $this->getTotalCostPriceAttribute(), 2);
    }

    /**
     * Get formatted total cost price with currency.
     */
    public function getFormattedTotalCostPriceAttribute(): string
    {
        return number_format($this->getTotalCostPriceAttribute(), 2) . ' ' . $this->currency;
    }

    /**
     * Get formatted total sale price with currency.
     */
    public function getFormattedTotalSalePriceAttribute(): string
    {
        return number_format($this->getTotalSalePriceAttribute(), 2) . ' ' . $this->currency;
    }

    /**
     * Get formatted total sale price including tax with currency.
     */
    public function getFormattedTotalSalePriceInclTaxAttribute(): string
    {
        return number_format($this->getTotalSalePriceInclTaxAttribute(), 2) . ' ' . $this->currency;
    }

    /**
     * Scope to find package by snake_case name.
     */
    public function scopeByName($query, string $name)
    {
        return $query->where('name', strtolower(str_replace(' ', '_', $name)));
    }
} 