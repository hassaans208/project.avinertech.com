<?php

namespace App\Services;

use App\Models\ViewType;
use App\Models\ViewTypeOption;
use App\Models\ViewDefinition;
use App\Models\ViewColumnConfiguration;
use Illuminate\Support\Facades\DB;

class EnhancedViewService
{
    /**
     * Get all available view types with their options
     */
    public function getAvailableViewTypes(): array
    {
        return ViewType::active()
            ->ordered()
            ->with('activeOptions')
            ->get()
            ->map(function ($viewType) {
                return [
                    'id' => $viewType->id,
                    'name' => $viewType->name,
                    'display_name' => $viewType->display_name,
                    'description' => $viewType->description,
                    'icon' => $viewType->icon,
                    'color' => $viewType->color,
                    'default_config' => $viewType->default_config,
                    'options' => $viewType->activeOptions->map(function ($option) {
                        return [
                            'key' => $option->option_key,
                            'display_name' => $option->display_name,
                            'description' => $option->description,
                            'type' => $option->option_type,
                            'default_value' => $option->default_value,
                            'possible_values' => $option->getFormattedPossibleValues(),
                            'is_required' => $option->is_required,
                            'validation_rules' => $option->validation_rules
                        ];
                    })
                ];
            })
            ->toArray();
    }

    /**
     * Create a new view definition with validation
     */
    public function createViewDefinition(array $data): ViewDefinition
    {
        // Validate view type exists
        $viewType = ViewType::where('name', $data['view_type'])->first();
        if (!$viewType) {
            throw new \InvalidArgumentException("View type '{$data['view_type']}' not found");
        }

        // Validate view configuration
        if (isset($data['view_configuration'])) {
            $errors = $viewType->validateConfiguration($data['view_configuration']);
            if (!empty($errors)) {
                throw new \InvalidArgumentException('View configuration validation failed: ' . implode(', ', $errors));
            }
        }

        // Create view definition
        $viewDefinition = ViewDefinition::create($data);

        // Create default column configurations if columns are provided
        if (isset($data['columns'])) {
            $this->createColumnConfigurations($viewDefinition, $data['columns']);
        }

        return $viewDefinition;
    }

    /**
     * Update view definition with validation
     */
    public function updateViewDefinition(ViewDefinition $viewDefinition, array $data): ViewDefinition
    {
        // Validate view type if changed
        if (isset($data['view_type'])) {
            $viewType = ViewType::where('name', $data['view_type'])->first();
            if (!$viewType) {
                throw new \InvalidArgumentException("View type '{$data['view_type']}' not found");
            }
        }

        // Validate view configuration
        if (isset($data['view_configuration'])) {
            $viewType = $viewDefinition->viewTypeDefinition ?? ViewType::where('name', $data['view_type'] ?? $viewDefinition->view_type)->first();
            if ($viewType) {
                $errors = $viewType->validateConfiguration($data['view_configuration']);
                if (!empty($errors)) {
                    throw new \InvalidArgumentException('View configuration validation failed: ' . implode(', ', $errors));
                }
            }
        }

        $viewDefinition->update($data);
        return $viewDefinition;
    }

    /**
     * Create column configurations for a view definition
     */
    public function createColumnConfigurations(ViewDefinition $viewDefinition, array $columns): void
    {
        foreach ($columns as $index => $column) {
            $columnData = array_merge([
                'view_definition_id' => $viewDefinition->id,
                'display_order' => $index + 1,
                'is_visible' => true,
                'is_editable' => true,
                'is_required' => false,
                'is_searchable' => true,
                'is_sortable' => true,
                'is_password_field' => false,
                'validation_rules' => [],
                'column_options' => []
            ], $column);

            // Validate column options
            if (isset($column['column_options'])) {
                $columnConfig = new ViewColumnConfiguration($columnData);
                $errors = $columnConfig->validateColumnOptions();
                if (!empty($errors)) {
                    throw new \InvalidArgumentException("Column '{$column['column_name']}' validation failed: " . implode(', ', $errors));
                }
            }

            ViewColumnConfiguration::create($columnData);
        }
    }

    /**
     * Get view definition with full configuration
     */
    public function getViewDefinitionWithConfiguration(int $viewDefinitionId): array
    {
        $viewDefinition = ViewDefinition::with(['viewTypeDefinition.activeOptions', 'columnConfigurations'])
            ->findOrFail($viewDefinitionId);

        return [
            'id' => $viewDefinition->id,
            'tenant_id' => $viewDefinition->tenant_id,
            'schema_name' => $viewDefinition->schema_name,
            'table_name' => $viewDefinition->table_name,
            'view_name' => $viewDefinition->view_name,
            'view_type' => $viewDefinition->view_type,
            'title' => $viewDefinition->title,
            'description' => $viewDefinition->description,
            'is_active' => $viewDefinition->is_active,
            'rendering_mode' => $viewDefinition->rendering_mode,
            'view_configuration' => $viewDefinition->getViewConfiguration(),
            'view_type_definition' => $viewDefinition->viewTypeDefinition ? [
                'name' => $viewDefinition->viewTypeDefinition->name,
                'display_name' => $viewDefinition->viewTypeDefinition->display_name,
                'description' => $viewDefinition->viewTypeDefinition->description,
                'icon' => $viewDefinition->viewTypeDefinition->icon,
                'color' => $viewDefinition->viewTypeDefinition->color,
                'options' => $viewDefinition->viewTypeDefinition->activeOptions->map(function ($option) {
                    return [
                        'key' => $option->option_key,
                        'display_name' => $option->display_name,
                        'description' => $option->description,
                        'type' => $option->option_type,
                        'default_value' => $option->default_value,
                        'possible_values' => $option->getFormattedPossibleValues(),
                        'is_required' => $option->is_required
                    ];
                })
            ] : null,
            'columns' => $viewDefinition->columnConfigurations->map(function ($column) {
                return [
                    'id' => $column->id,
                    'column_name' => $column->column_name,
                    'display_name' => $column->display_name,
                    'is_visible' => $column->is_visible,
                    'is_editable' => $column->is_editable,
                    'is_required' => $column->is_required,
                    'is_searchable' => $column->is_searchable,
                    'is_sortable' => $column->is_sortable,
                    'display_order' => $column->display_order,
                    'column_width' => $column->column_width,
                    'data_type' => $column->data_type,
                    'is_password_field' => $column->is_password_field,
                    'validation_rules' => $column->validation_rules,
                    'display_format' => $column->display_format,
                    'placeholder_text' => $column->placeholder_text,
                    'help_text' => $column->help_text,
                    'column_options' => $column->getColumnOptions()
                ];
            })
        ];
    }

    /**
     * Get table schema information for a specific table
     */
    public function getTableSchema(string $schemaName, string $tableName): array
    {
        $columns = DB::connection('ui_api')->select("
            SELECT 
                COLUMN_NAME,
                DATA_TYPE,
                IS_NULLABLE,
                COLUMN_DEFAULT,
                CHARACTER_MAXIMUM_LENGTH,
                NUMERIC_PRECISION,
                NUMERIC_SCALE,
                COLUMN_COMMENT,
                COLUMN_KEY,
                EXTRA
            FROM information_schema.COLUMNS 
            WHERE TABLE_SCHEMA = ? AND TABLE_NAME = ?
            ORDER BY ORDINAL_POSITION
        ", [$schemaName, $tableName]);

        return array_map(function ($column) {
            return [
                'name' => $column->COLUMN_NAME,
                'type' => $column->DATA_TYPE,
                'nullable' => $column->IS_NULLABLE === 'YES',
                'default' => $column->COLUMN_DEFAULT,
                'max_length' => $column->CHARACTER_MAXIMUM_LENGTH,
                'precision' => $column->NUMERIC_PRECISION,
                'scale' => $column->NUMERIC_SCALE,
                'comment' => $column->COLUMN_COMMENT,
                'key' => $column->COLUMN_KEY,
                'extra' => $column->EXTRA
            ];
        }, $columns);
    }

    /**
     * Generate default column configurations from table schema
     */
    public function generateDefaultColumnConfigurations(string $schemaName, string $tableName, string $viewType): array
    {
        $schema = $this->getTableSchema($schemaName, $tableName);
        $viewTypeDefinition = ViewType::where('name', $viewType)->first();
        
        if (!$viewTypeDefinition) {
            throw new \InvalidArgumentException("View type '{$viewType}' not found");
        }

        $configurations = [];
        
        foreach ($schema as $index => $column) {
            $config = [
                'column_name' => $column['name'],
                'display_name' => ucfirst(str_replace('_', ' ', $column['name'])),
                'data_type' => $column['type'],
                'display_order' => $index + 1,
                'is_visible' => true,
                'is_editable' => !in_array($column['key'], ['PRI']), // Not editable if primary key
                'is_required' => $column['nullable'] === false && $column['default'] === null,
                'is_searchable' => true,
                'is_sortable' => true,
                'is_password_field' => false,
                'validation_rules' => [],
                'column_options' => []
            ];

            // Apply default options from view type
            foreach ($viewTypeDefinition->activeOptions as $option) {
                $config['column_options'][$option->option_key] = $option->getDefaultValue();
            }

            // Special handling for password fields
            if (str_contains(strtolower($column['name']), 'password')) {
                $config['is_password_field'] = true;
                $config['column_options']['password'] = true;
            }

            // Special handling for encrypted fields
            if (str_contains(strtolower($column['name']), 'encrypted') || str_contains(strtolower($column['name']), 'hash')) {
                $config['column_options']['show_encrypted'] = false;
            }

            $configurations[] = $config;
        }

        return $configurations;
    }

    /**
     * Validate a complete view configuration
     */
    public function validateViewConfiguration(array $viewData): array
    {
        $errors = [];

        // Validate view type
        if (!isset($viewData['view_type'])) {
            $errors[] = 'View type is required';
        } else {
            $viewType = ViewType::where('name', $viewData['view_type'])->first();
            if (!$viewType) {
                $errors[] = "View type '{$viewData['view_type']}' not found";
            }
        }

        // Validate view configuration
        if (isset($viewData['view_configuration']) && isset($viewType)) {
            $configErrors = $viewType->validateConfiguration($viewData['view_configuration']);
            $errors = array_merge($errors, $configErrors);
        }

        // Validate columns
        if (isset($viewData['columns'])) {
            foreach ($viewData['columns'] as $index => $column) {
                if (!isset($column['column_name'])) {
                    $errors[] = "Column at index {$index} is missing column_name";
                    continue;
                }

                if (isset($column['column_options']) && isset($viewType)) {
                    $columnConfig = new ViewColumnConfiguration([
                        'column_name' => $column['column_name'],
                        'column_options' => $column['column_options']
                    ]);
                    $columnConfig->viewDefinition = (object)['viewTypeDefinition' => $viewType];
                    
                    $columnErrors = $columnConfig->validateColumnOptions();
                    foreach ($columnErrors as $error) {
                        $errors[] = "Column '{$column['column_name']}': {$error}";
                    }
                }
            }
        }

        return $errors;
    }
}
