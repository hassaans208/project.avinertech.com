<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ViewTypeOption extends Model
{
    protected $fillable = [
        'view_type_id',
        'option_key',
        'display_name',
        'description',
        'option_type',
        'default_value',
        'validation_rules',
        'possible_values',
        'is_required',
        'is_active',
        'sort_order'
    ];

    protected $casts = [
        'default_value' => 'array',
        'validation_rules' => 'array',
        'possible_values' => 'array',
        'is_required' => 'boolean',
        'is_active' => 'boolean',
        'sort_order' => 'integer'
    ];

    /**
     * Get the view type that owns this option
     */
    public function viewType(): BelongsTo
    {
        return $this->belongsTo(ViewType::class);
    }

    /**
     * Scope for active options
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
     * Scope for required options
     */
    public function scopeRequired($query)
    {
        return $query->where('is_required', true);
    }

    /**
     * Scope for optional options
     */
    public function scopeOptional($query)
    {
        return $query->where('is_required', false);
    }

    /**
     * Get the default value for this option
     */
    public function getDefaultValue()
    {
        return $this->default_value;
    }

    /**
     * Validate a value against this option's rules
     */
    public function validateValue($value): array
    {
        $errors = [];
        
        // Type validation
        switch ($this->option_type) {
            case 'boolean':
                if (!is_bool($value)) {
                    $errors[] = "Value must be a boolean";
                }
                break;
            case 'string':
                if (!is_string($value)) {
                    $errors[] = "Value must be a string";
                }
                break;
            case 'number':
                if (!is_numeric($value)) {
                    $errors[] = "Value must be a number";
                }
                break;
            case 'array':
                if (!is_array($value)) {
                    $errors[] = "Value must be an array";
                }
                break;
            case 'object':
                if (!is_array($value) && !is_object($value)) {
                    $errors[] = "Value must be an object";
                }
                break;
        }
        
        // Required validation
        if ($this->is_required && ($value === null || $value === '')) {
            $errors[] = "This field is required";
        }
        
        // Possible values validation
        if ($this->possible_values && !in_array($value, $this->possible_values)) {
            $errors[] = "Value must be one of: " . implode(', ', $this->possible_values);
        }
        
        // Custom validation rules
        if ($this->validation_rules) {
            foreach ($this->validation_rules as $rule => $ruleValue) {
                switch ($rule) {
                    case 'min_length':
                        if (is_string($value) && strlen($value) < $ruleValue) {
                            $errors[] = "Value must be at least {$ruleValue} characters long";
                        }
                        break;
                    case 'max_length':
                        if (is_string($value) && strlen($value) > $ruleValue) {
                            $errors[] = "Value must be no more than {$ruleValue} characters long";
                        }
                        break;
                    case 'min':
                        if (is_numeric($value) && $value < $ruleValue) {
                            $errors[] = "Value must be at least {$ruleValue}";
                        }
                        break;
                    case 'max':
                        if (is_numeric($value) && $value > $ruleValue) {
                            $errors[] = "Value must be no more than {$ruleValue}";
                        }
                        break;
                }
            }
        }
        
        return $errors;
    }

    /**
     * Get formatted possible values for UI
     */
    public function getFormattedPossibleValues(): array
    {
        if (!$this->possible_values) {
            return [];
        }
        
        $formatted = [];
        foreach ($this->possible_values as $value) {
            if (is_array($value) && isset($value['value'], $value['label'])) {
                $formatted[] = $value;
            } else {
                $formatted[] = [
                    'value' => $value,
                    'label' => is_string($value) ? ucfirst(str_replace('_', ' ', $value)) : $value
                ];
            }
        }
        
        return $formatted;
    }
}
