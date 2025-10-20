<?php

namespace App\Services;

use App\Models\ViewType;
use App\Models\ViewTypeOption;
use App\Models\ViewDefinition;
use App\Models\ModuleColumnConfiguration;
use App\Models\ModuleDefinition;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

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
                    'options' => $viewType->activeOptions()->get()->map(function ($option) {
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
    public function createViewDefinition(array $data): ModuleDefinition
    {

        // Prepare view definition data
        $moduleDefinitionData = [
            'tenant_id' => $data['tenant_id'],
            'schema_name' => $data['schema_name'],
            'table_name' => $data['table_name'],
            // 'view_name' => $data['view_name'],
            'title' => $data['title'],
            'description' => $data['description'] ?? null,
            // 'rendering_mode' => $data['rendering_mode'] ?? 'hybrid',
            'module_type_id' => $data['module_type_id'] ?? null, // Backward compatibility
            // 'view_configuration' => $data['view_configuration'] ?? []

            // 'name' => $data['name'],
            'module_category_id' => $data['module_category_id'] ?? null,
            'module_group_id' => $data['module_group_id'] ?? null,
            'is_active' => $data['is_active'] ?? true,
            'cache_key' => $data['cache_key'] ?? Str::slug($data['title']),
        ];

        // Create view definition
        $moduleDefinition = ModuleDefinition::create($moduleDefinitionData);

        // Create column configurations if provided
        if (isset($data['column_configurations'])) {
            $this->createModuleColumnConfigurations($moduleDefinition, $data['column_configurations']);
        }

        return $moduleDefinition;
    }

    /**
     * Update view definition with validation
     */
    public function updateModuleDefinition(ModuleDefinition $moduleDefinition, array $data): ModuleDefinition
    {

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
    public function createModuleColumnConfigurations(ModuleDefinition $moduleDefinition, array $columns): void
    {
        foreach ($columns as $index => $column) {
            $columnData = array_merge([
                'module_definition_id' => $moduleDefinition->id,
                'column_name' => $column['column_name'],
                'display_name' => $column['display_name'],
                'data_type' => $column['data_type'],
                'list_display_order' => $column['list_display_order'],
                'form_display_order' => $column['form_display_order'],
                'placeholder_text' => $column['placeholder_text'],
                'validation_rules' => $column['validation_rules']
            ]);

            // Validate column options
            if (isset($column['validation_rules'])) {
                // $columnConfig = new ModuleColumnConfiguration($columnData);
                // $errors = $columnConfig->validateValidationRules();
                    if (!empty($errors)) {
                        throw new \InvalidArgumentException("Column '{$column['column_name']}' validation failed: " . implode(', ', $errors));
                    }
            }

            ModuleColumnConfiguration::create($columnData);
        }
    }

    /**
     * Get view definition with full configuration
     */
    public function getViewDefinitionWithConfiguration(int $viewDefinitionId): array
    {
        $viewDefinition = ViewDefinition::where('id', $viewDefinitionId)->first();
        // $viewDefinition->load(['viewTypeDefinitions', 'viewTypeDefinitions.activeOptions', 'columnConfigurations']);

        return [
            'id' => $viewDefinition->id,
            'tenant_id' => $viewDefinition->tenant_id,
            'schema_name' => $viewDefinition->schema_name,
            'table_name' => $viewDefinition->table_name,
            'view_name' => $viewDefinition->view_name,
            'view_type' => $viewDefinition->view_type, // Backward compatibility
            'view_types' => $viewDefinition->getViewTypes(),
            'title' => $viewDefinition->title,
            'description' => $viewDefinition->description,
            'is_active' => $viewDefinition->is_active,
            'rendering_mode' => $viewDefinition->rendering_mode,
            'view_configuration' => $viewDefinition->getViewConfiguration(),
            'view_type_definitions' => $viewDefinition->viewTypeDefinitions()->get()->map(function ($viewType) {
                return [
                    'name' => $viewType->name,
                    'display_name' => $viewType->display_name,
                    'description' => $viewType->description,
                    'icon' => $viewType->icon,
                    'color' => $viewType->color,
                    'options' => $viewType->activeOptions()->get()->map(function ($option) {
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
                ];
            }),
            'column_configurations' => $viewDefinition->columnConfigurations()->get()->map(function ($column) {
                return [
                    'id' => $column->id,
                    'column_name' => $column->column_name,
                    'display_name' => $column->display_name,
                    'is_visible' => $column->is_visible,
                    'is_editable' => $column->is_editable,
                    'is_required' => $column->is_required,
                    'is_searchable' => $column->is_searchable,
                    'is_sortable' => $column->is_sortable,
                    'generic_display_order' => $column->generic_display_order,
                    'list_display_order' => $column->list_display_order,
                    'form_display_order' => $column->form_display_order,
                    'column_width' => $column->column_width,
                    'data_type' => $column->data_type,
                    'is_password_field' => $column->is_password_field,
                    'validation_rules' => $column->validation_rules,
                    'display_format' => $column->display_format,
                    'form_display_order' => $column->form_display_order,
                    'list_display_order' => $column->list_display_order,
                    'generic_display_order' => $column->generic_display_order,
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
            foreach ($viewTypeDefinition->activeOptions()->get() as $option) {
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
        if (empty($viewData['view_type']) && empty($viewData['view_types'])) {
            $errors[] = 'View type is required';
        } else {
            $viewTypesFromData = !empty($viewData['view_type']) ? [$viewData['view_type']] : ($viewData['view_types'] ?? []);
            $viewType = ViewType::whereIn('name', $viewTypesFromData)->first();
            if (!$viewType) {
                $errors[] = "View type '{implode(', ', $viewTypesFromData)}' not found";
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

