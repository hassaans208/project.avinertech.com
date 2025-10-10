<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class ViewType extends Model
{
    protected $fillable = [
        'name',
        'display_name',
        'description',
        'icon',
        'color',
        'default_config',
        'is_active',
        'sort_order'
    ];

    protected $casts = [
        'default_config' => 'array',
        'is_active' => 'boolean',
        'sort_order' => 'integer'
    ];

    /**
     * Get the options associated with this view type
     */
    public function options(): HasMany
    {
        return $this->hasMany(ViewTypeOption::class);
    }

    /**
     * Get active options for this view type
     */
    public function activeOptions(): HasMany
    {
        return $this->hasMany(ViewTypeOption::class)->where('is_active', true)->orderBy('sort_order');
    }

    /**
     * Get view definitions that use this view type
     */
    public function viewDefinitions(): HasMany
    {
        return $this->hasMany(ViewDefinition::class, 'view_type', 'name');
    }

    /**
     * Scope for active view types
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope for ordering by sort order
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order');
    }

    /**
     * Get option by key
     */
    public function getOption(string $key): ?ViewTypeOption
    {
        return $this->options()->where('option_key', $key)->first();
    }

    /**
     * Get default configuration merged with option defaults
     */
    public function getDefaultConfiguration(): array
    {
        $config = $this->default_config ?? [];
        
        foreach ($this->activeOptions as $option) {
            if ($option->default_value !== null) {
                $config[$option->option_key] = $option->default_value;
            }
        }
        
        return $config;
    }

    /**
     * Validate configuration against available options
     */
    public function validateConfiguration(array $config): array
    {
        $errors = [];
        
        foreach ($this->activeOptions as $option) {
            if ($option->is_required && !isset($config[$option->option_key])) {
                $errors[] = "Option '{$option->option_key}' is required";
                continue;
            }
            
            if (isset($config[$option->option_key])) {
                $value = $config[$option->option_key];
                
                // Basic type validation
                switch ($option->option_type) {
                    case 'boolean':
                        if (!is_bool($value)) {
                            $errors[] = "Option '{$option->option_key}' must be a boolean";
                        }
                        break;
                    case 'string':
                        if (!is_string($value)) {
                            $errors[] = "Option '{$option->option_key}' must be a string";
                        }
                        break;
                    case 'number':
                        if (!is_numeric($value)) {
                            $errors[] = "Option '{$option->option_key}' must be a number";
                        }
                        break;
                    case 'array':
                        if (!is_array($value)) {
                            $errors[] = "Option '{$option->option_key}' must be an array";
                        }
                        break;
                }
            }
        }
        
        return $errors;
    }
}