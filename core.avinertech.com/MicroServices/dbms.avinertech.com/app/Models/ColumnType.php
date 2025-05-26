<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ColumnType extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'mysql_type',
        'laravel_method',
        'parameters',
        'description',
        'requires_length',
        'requires_precision',
        'requires_scale',
        'requires_values',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'requires_length' => 'boolean',
        'requires_precision' => 'boolean',
        'requires_scale' => 'boolean',
        'requires_values' => 'boolean',
        'parameters' => 'array',
    ];

    /**
     * Get the Laravel migration method for this column type
     *
     * @return string
     */
    public function getLaravelMethod(): string
    {
        return $this->laravel_method;
    }

    /**
     * Check if this column type requires length parameter
     *
     * @return bool
     */
    public function requiresLength(): bool
    {
        return $this->requires_length;
    }

    /**
     * Check if this column type requires precision parameter
     *
     * @return bool
     */
    public function requiresPrecision(): bool
    {
        return $this->requires_precision;
    }

    /**
     * Check if this column type requires scale parameter
     *
     * @return bool
     */
    public function requiresScale(): bool
    {
        return $this->requires_scale;
    }

    /**
     * Check if this column type requires values parameter
     *
     * @return bool
     */
    public function requiresValues(): bool
    {
        return $this->requires_values;
    }
} 