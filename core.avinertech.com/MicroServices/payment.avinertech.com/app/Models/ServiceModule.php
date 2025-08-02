<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class ServiceModule extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'display_name',
        'description',
        'cost_price',
        'sale_price',
        'tax_rate',
        'currency',
        'is_active',
    ];

    protected $casts = [
        'cost_price' => 'decimal:2',
        'sale_price' => 'decimal:2',
        'tax_rate' => 'decimal:4',
        'is_active' => 'boolean',
    ];

    /**
     * Get the packages that use this module.
     */
    public function packages(): BelongsToMany
    {
        return $this->belongsToMany(Package::class, 'package_service_modules')
            ->withTimestamps();
    }

    /**
     * Calculate the tax amount for this module.
     */
    public function getTaxAmountAttribute(): float
    {
        return round($this->sale_price * $this->tax_rate, 2);
    }

    /**
     * Calculate the sale price including tax.
     */
    public function getSalePriceInclTaxAttribute(): float
    {
        return round($this->sale_price + $this->getTaxAmountAttribute(), 2);
    }

    /**
     * Calculate the profit for this module.
     */
    public function getProfitAttribute(): float
    {
        return round($this->sale_price - $this->cost_price, 2);
    }

    /**
     * Get formatted cost price with currency.
     */
    public function getFormattedCostPriceAttribute(): string
    {
        return number_format($this->cost_price, 2) . ' ' . $this->currency;
    }

    /**
     * Get formatted sale price with currency.
     */
    public function getFormattedSalePriceAttribute(): string
    {
        return number_format($this->sale_price, 2) . ' ' . $this->currency;
    }

    /**
     * Get formatted sale price including tax with currency.
     */
    public function getFormattedSalePriceInclTaxAttribute(): string
    {
        return number_format($this->getSalePriceInclTaxAttribute(), 2) . ' ' . $this->currency;
    }

    /**
     * Scope to get only active modules.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope to find module by name.
     */
    public function scopeByName($query, string $name)
    {
        return $query->where('name', strtolower(str_replace(' ', '_', $name)));
    }
} 