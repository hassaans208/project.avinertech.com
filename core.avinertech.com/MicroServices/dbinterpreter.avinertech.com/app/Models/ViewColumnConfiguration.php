<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ViewColumnConfiguration extends Model
{
    protected $fillable = [
        'view_definition_id',
        'column_name',
        'display_name',
        'is_visible',
        'is_editable',
        'is_required',
        'is_searchable',
        'is_sortable',
        'display_order',
        'column_width',
        'data_type',
        'is_password_field',
        'validation_rules',
        'display_format',
        'placeholder_text',
        'help_text',
        'column_options'
    ];

    protected $casts = [
        'is_visible' => 'boolean',
        'is_editable' => 'boolean',
        'is_required' => 'boolean',
        'is_searchable' => 'boolean',
        'is_sortable' => 'boolean',
        'is_password_field' => 'boolean',
        'validation_rules' => 'array',
        'column_options' => 'array'
    ];

    public function viewDefinition(): BelongsTo
    {
        return $this->belongsTo(ViewDefinition::class);
    }

    public function scopeVisible($query)
    {
        return $query->where('is_visible', true);
    }

    public function scopeEditable($query)
    {
        return $query->where('is_editable', true);
    }

    public function scopeSearchable($query)
    {
        return $query->where('is_searchable', true);
    }

    public function scopeSortable($query)
    {
        return $query->where('is_sortable', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('display_order');
    }

    /**
     * Get column options with defaults from view type
     */
    public function getColumnOptions(): array
    {
        $options = $this->column_options ?? [];
        
        if ($this->viewDefinition && $this->viewDefinition->viewTypeDefinition) {
            $viewTypeOptions = $this->viewDefinition->viewTypeDefinition->activeOptions;
            
            foreach ($viewTypeOptions as $viewTypeOption) {
                if (!isset($options[$viewTypeOption->option_key])) {
                    $options[$viewTypeOption->option_key] = $viewTypeOption->getDefaultValue();
                }
            }
        }
        
        return $options;
    }

    /**
     * Get a specific column option
     */
    public function getColumnOption(string $key, $default = null)
    {
        $options = $this->getColumnOptions();
        return $options[$key] ?? $default;
    }

    /**
     * Set a column option
     */
    public function setColumnOption(string $key, $value): void
    {
        $options = $this->column_options ?? [];
        $options[$key] = $value;
        $this->column_options = $options;
    }

    /**
     * Validate column options against view type options
     */
    public function validateColumnOptions(): array
    {
        $errors = [];
        
        if (!$this->viewDefinition || !$this->viewDefinition->viewTypeDefinition) {
            return $errors;
        }
        
        $viewTypeOptions = $this->viewDefinition->viewTypeDefinition->activeOptions;
        $columnOptions = $this->column_options ?? [];
        
        foreach ($viewTypeOptions as $viewTypeOption) {
            if (isset($columnOptions[$viewTypeOption->option_key])) {
                $optionErrors = $viewTypeOption->validateValue($columnOptions[$viewTypeOption->option_key]);
                foreach ($optionErrors as $error) {
                    $errors[] = "Column '{$this->column_name}' option '{$viewTypeOption->option_key}': {$error}";
                }
            }
        }
        
        return $errors;
    }
}
