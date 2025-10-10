# Hybrid View Rendering System Design

This document outlines a hybrid approach that combines cached views with dynamic schema-based rendering for maximum flexibility and performance.

## Table of Contents

1. [Hybrid Architecture Overview](#hybrid-architecture-overview)
2. [Dynamic Schema Rendering](#dynamic-schema-rendering)
3. [Cached View System](#cached-view-system)
4. [Rendering Strategy](#rendering-strategy)
5. [API Design](#api-design)
6. [Implementation Examples](#implementation-examples)

---

## Hybrid Architecture Overview

### Core Concept

The hybrid system provides three rendering modes:

1. **Dynamic Mode**: Real-time rendering based on current schema
2. **Cached Mode**: Pre-built views stored in cache
3. **Hybrid Mode**: Cached base with dynamic schema overlays

### Benefits

- **Performance**: Cached views for frequently accessed data
- **Flexibility**: Dynamic rendering for schema changes
- **Fallback**: Automatic fallback to dynamic when cache is invalid
- **Real-time**: Schema changes reflected immediately
- **Customization**: User-specific view configurations

---

## Dynamic Schema Rendering

### 1. Schema Analysis Service

```php
<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;

class SchemaAnalysisService
{
    public function analyzeTable(string $tenantId, string $schemaName, string $tableName): array
    {
        // Get table structure
        $columns = DB::connection('ui_api')->select("
            SELECT 
                COLUMN_NAME,
                DATA_TYPE,
                COLUMN_TYPE,
                IS_NULLABLE,
                COLUMN_DEFAULT,
                COLUMN_KEY,
                EXTRA,
                COLUMN_COMMENT
            FROM information_schema.COLUMNS 
            WHERE TABLE_SCHEMA = ? AND TABLE_NAME = ?
            ORDER BY ORDINAL_POSITION
        ", [$schemaName, $tableName]);

        // Get indexes
        $indexes = DB::connection('ui_api')->select("
            SELECT 
                INDEX_NAME,
                COLUMN_NAME,
                NON_UNIQUE,
                INDEX_TYPE
            FROM information_schema.STATISTICS 
            WHERE TABLE_SCHEMA = ? AND TABLE_NAME = ?
            ORDER BY INDEX_NAME, SEQ_IN_INDEX
        ", [$schemaName, $tableName]);

        // Get foreign keys
        $foreignKeys = DB::connection('ui_api')->select("
            SELECT 
                CONSTRAINT_NAME,
                COLUMN_NAME,
                REFERENCED_TABLE_NAME,
                REFERENCED_COLUMN_NAME,
                UPDATE_RULE,
                DELETE_RULE
            FROM information_schema.KEY_COLUMN_USAGE 
            WHERE TABLE_SCHEMA = ? AND TABLE_NAME = ? 
            AND REFERENCED_TABLE_NAME IS NOT NULL
        ", [$schemaName, $tableName]);

        return [
            'table_name' => $tableName,
            'schema_name' => $schemaName,
            'columns' => $this->processColumns($columns),
            'indexes' => $this->processIndexes($indexes),
            'foreign_keys' => $this->processForeignKeys($foreignKeys),
            'analyzed_at' => now()
        ];
    }

    private function processColumns(array $columns): array
    {
        return array_map(function ($column) {
            return [
                'name' => $column->COLUMN_NAME,
                'type' => $column->DATA_TYPE,
                'full_type' => $column->COLUMN_TYPE,
                'nullable' => $column->IS_NULLABLE === 'YES',
                'default' => $column->COLUMN_DEFAULT,
                'is_primary_key' => $column->COLUMN_KEY === 'PRI',
                'is_auto_increment' => str_contains($column->EXTRA, 'auto_increment'),
                'comment' => $column->COLUMN_COMMENT,
                'is_password_field' => $this->isPasswordField($column),
                'form_control' => $this->getFormControl($column),
                'validation_rules' => $this->getValidationRules($column),
                'display_name' => $this->generateDisplayName($column->COLUMN_NAME),
                'is_editable' => !$this->isSystemField($column),
                'is_visible' => true,
                'is_searchable' => $this->isSearchable($column),
                'is_sortable' => $this->isSortable($column)
            ];
        }, $columns);
    }

    private function isPasswordField($column): bool
    {
        $passwordFields = ['password', 'passwd', 'pwd', 'secret', 'token'];
        return in_array(strtolower($column->COLUMN_NAME), $passwordFields) ||
               str_contains(strtolower($column->COLUMN_TYPE), 'password');
    }

    private function getFormControl($column): string
    {
        $type = strtolower($column->DATA_TYPE);
        
        if ($this->isPasswordField($column)) {
            return 'password';
        }
        
        switch ($type) {
            case 'varchar':
            case 'char':
            case 'text':
                return 'text';
            case 'int':
            case 'bigint':
            case 'decimal':
            case 'float':
            case 'double':
                return 'number';
            case 'date':
                return 'date';
            case 'datetime':
            case 'timestamp':
                return 'datetime';
            case 'time':
                return 'time';
            case 'tinyint':
                return 'checkbox';
            case 'json':
                return 'textarea';
            default:
                return 'text';
        }
    }

    private function getValidationRules($column): array
    {
        $rules = [];
        
        if ($column->IS_NULLABLE === 'NO') {
            $rules['required'] = true;
        }
        
        if ($this->isPasswordField($column)) {
            $rules['min'] = 8;
            $rules['pattern'] = '^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)';
        }
        
        if (str_contains($column->COLUMN_NAME, 'email')) {
            $rules['email'] = true;
        }
        
        if (str_contains($column->COLUMN_TYPE, 'varchar')) {
            preg_match('/varchar\((\d+)\)/', $column->COLUMN_TYPE, $matches);
            if (isset($matches[1])) {
                $rules['max'] = (int)$matches[1];
            }
        }
        
        return $rules;
    }

    private function generateDisplayName(string $columnName): string
    {
        // Convert snake_case to Title Case
        return ucwords(str_replace('_', ' ', $columnName));
    }

    private function isSystemField($column): bool
    {
        $systemFields = ['id', 'created_at', 'updated_at', 'deleted_at'];
        return in_array($column->COLUMN_NAME, $systemFields);
    }

    private function isSearchable($column): bool
    {
        $searchableTypes = ['varchar', 'char', 'text', 'int', 'bigint'];
        return in_array(strtolower($column->DATA_TYPE), $searchableTypes) &&
               !$this->isSystemField($column);
    }

    private function isSortable($column): bool
    {
        return !$this->isSystemField($column);
    }
}
```

### 2. Dynamic View Renderer

```php
<?php

namespace App\Services;

use App\Services\SchemaAnalysisService;

class DynamicViewRenderer
{
    public function __construct(
        private SchemaAnalysisService $schemaAnalysis
    ) {}

    public function renderView(string $tenantId, string $schemaName, string $tableName, string $viewType, array $options = []): array
    {
        $startTime = microtime(true);
        
        // Analyze current schema
        $schema = $this->schemaAnalysis->analyzeTable($tenantId, $schemaName, $tableName);
        
        // Generate view based on type
        $view = match($viewType) {
            'create' => $this->renderCreateView($schema, $options),
            'update' => $this->renderUpdateView($schema, $options),
            'list' => $this->renderListView($schema, $options),
            'analytics' => $this->renderAnalyticsView($schema, $options),
            default => throw new \InvalidArgumentException("Invalid view type: {$viewType}")
        };
        
        $renderTime = round((microtime(true) - $startTime) * 1000, 2);
        
        return [
            'view_type' => $viewType,
            'table_name' => $tableName,
            'schema_name' => $schemaName,
            'rendered_at' => now(),
            'render_time' => $renderTime . 'ms',
            'schema_version' => $schema['analyzed_at']->timestamp,
            'html_content' => $view['html'],
            'css_content' => $view['css'],
            'js_content' => $view['js'],
            'metadata' => $view['metadata']
        ];
    }

    private function renderCreateView(array $schema, array $options): array
    {
        $columns = array_filter($schema['columns'], fn($col) => $col['is_editable'] && !$col['is_auto_increment']);
        
        $html = '<form class="dynamic-form create-form" data-view-type="create" data-table="' . $schema['table_name'] . '">';
        $html .= '<div class="form-container">';
        
        foreach ($columns as $column) {
            $html .= $this->renderFormField($column, 'create');
        }
        
        $html .= '<div class="form-actions">';
        $html .= '<button type="submit" class="btn btn-primary">Create Record</button>';
        $html .= '<button type="reset" class="btn btn-secondary">Reset</button>';
        $html .= '</div>';
        $html .= '</div>';
        $html .= '</form>';
        
        return [
            'html' => $html,
            'css' => $this->getFormCss(),
            'js' => $this->getFormJs('create'),
            'metadata' => [
                'columns' => $columns,
                'validation_rules' => $this->extractValidationRules($columns)
            ]
        ];
    }

    private function renderListView(array $schema, array $options): array
    {
        $columns = array_filter($schema['columns'], fn($col) => $col['is_visible']);
        $searchableColumns = array_filter($columns, fn($col) => $col['is_searchable']);
        $sortableColumns = array_filter($columns, fn($col) => $col['is_sortable']);
        
        $html = '<div class="dynamic-list-view" data-table="' . $schema['table_name'] . '">';
        
        // Search and filters
        if (!empty($searchableColumns)) {
            $html .= '<div class="list-controls">';
            $html .= '<div class="search-container">';
            $html .= '<input type="text" class="form-control search-input" placeholder="Search...">';
            $html .= '<button class="btn btn-outline-secondary search-btn">Search</button>';
            $html .= '</div>';
            $html .= '</div>';
        }
        
        // Table
        $html .= '<div class="table-responsive">';
        $html .= '<table class="table table-striped dynamic-table">';
        $html .= '<thead><tr>';
        
        foreach ($columns as $column) {
            $sortableClass = $column['is_sortable'] ? 'sortable' : '';
            $html .= '<th class="' . $sortableClass . '" data-column="' . $column['name'] . '">';
            $html .= $column['display_name'];
            if ($column['is_sortable']) {
                $html .= ' <span class="sort-indicator"></span>';
            }
            $html .= '</th>';
        }
        
        $html .= '<th>Actions</th>';
        $html .= '</tr></thead>';
        $html .= '<tbody class="table-body">';
        $html .= '<!-- Data will be loaded dynamically -->';
        $html .= '</tbody>';
        $html .= '</table>';
        $html .= '</div>';
        
        // Pagination
        $html .= '<div class="pagination-container">';
        $html .= '<nav aria-label="Table pagination">';
        $html .= '<ul class="pagination">';
        $html .= '<!-- Pagination will be generated dynamically -->';
        $html .= '</ul>';
        $html .= '</nav>';
        $html .= '</div>';
        
        $html .= '</div>';
        
        return [
            'html' => $html,
            'css' => $this->getListCss(),
            'js' => $this->getListJs(),
            'metadata' => [
                'columns' => $columns,
                'searchable_columns' => $searchableColumns,
                'sortable_columns' => $sortableColumns,
                'pagination' => [
                    'per_page' => $options['per_page'] ?? 25,
                    'show_page_info' => true
                ]
            ]
        ];
    }

    private function renderFormField(array $column, string $context): string
    {
        $html = '<div class="form-group" data-column="' . $column['name'] . '">';
        $html .= '<label for="' . $column['name'] . '" class="form-label">';
        $html .= $column['display_name'];
        if ($column['validation_rules']['required'] ?? false) {
            $html .= ' <span class="required">*</span>';
        }
        $html .= '</label>';
        
        $inputType = $column['form_control'];
        $inputClass = 'form-control';
        
        if ($inputType === 'password') {
            $inputClass .= ' password-field';
        }
        
        $html .= '<input type="' . $inputType . '" ';
        $html .= 'id="' . $column['name'] . '" ';
        $html .= 'name="' . $column['name'] . '" ';
        $html .= 'class="' . $inputClass . '" ';
        
        if ($column['validation_rules']['required'] ?? false) {
            $html .= 'required ';
        }
        
        if ($column['validation_rules']['max'] ?? false) {
            $html .= 'maxlength="' . $column['validation_rules']['max'] . '" ';
        }
        
        $html .= 'data-validation="' . htmlspecialchars(json_encode($column['validation_rules'])) . '">';
        
        if ($column['comment']) {
            $html .= '<small class="form-text text-muted">' . htmlspecialchars($column['comment']) . '</small>';
        }
        
        $html .= '</div>';
        
        return $html;
    }

    private function getFormCss(): string
    {
        return '
        .dynamic-form {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .form-group {
            margin-bottom: 20px;
        }
        .form-label {
            font-weight: 600;
            margin-bottom: 5px;
        }
        .required {
            color: #dc3545;
        }
        .password-field {
            font-family: monospace;
        }
        .form-actions {
            margin-top: 30px;
            text-align: right;
        }
        ';
    }

    private function getListCss(): string
    {
        return '
        .dynamic-list-view {
            padding: 20px;
        }
        .list-controls {
            margin-bottom: 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .search-container {
            display: flex;
            gap: 10px;
        }
        .search-input {
            width: 300px;
        }
        .sortable {
            cursor: pointer;
            user-select: none;
        }
        .sortable:hover {
            background-color: #f8f9fa;
        }
        .sort-indicator {
            font-size: 12px;
            color: #6c757d;
        }
        .pagination-container {
            margin-top: 20px;
            display: flex;
            justify-content: center;
        }
        ';
    }

    private function getFormJs(string $context): string
    {
        return '
        document.addEventListener("DOMContentLoaded", function() {
            const form = document.querySelector(".dynamic-form");
            if (form) {
                initializeDynamicForm(form);
            }
        });
        
        function initializeDynamicForm(form) {
            // Form validation
            form.addEventListener("submit", function(e) {
                e.preventDefault();
                if (validateForm(form)) {
                    submitForm(form);
                }
            });
            
            // Password strength indicator
            const passwordFields = form.querySelectorAll(".password-field");
            passwordFields.forEach(field => {
                field.addEventListener("input", function() {
                    showPasswordStrength(this);
                });
            });
        }
        
        function validateForm(form) {
            let isValid = true;
            const fields = form.querySelectorAll("[data-validation]");
            
            fields.forEach(field => {
                const rules = JSON.parse(field.dataset.validation);
                if (!validateField(field, rules)) {
                    isValid = false;
                }
            });
            
            return isValid;
        }
        
        function validateField(field, rules) {
            // Implementation for field validation
            return true;
        }
        
        function showPasswordStrength(field) {
            // Implementation for password strength indicator
        }
        
        function submitForm(form) {
            // Implementation for form submission
            console.log("Form submitted:", new FormData(form));
        }
        ';
    }

    private function getListJs(): string
    {
        return '
        document.addEventListener("DOMContentLoaded", function() {
            const listView = document.querySelector(".dynamic-list-view");
            if (listView) {
                initializeDynamicList(listView);
            }
        });
        
        function initializeDynamicList(listView) {
            // Search functionality
            const searchInput = listView.querySelector(".search-input");
            if (searchInput) {
                searchInput.addEventListener("input", debounce(function() {
                    performSearch(this.value);
                }, 300));
            }
            
            // Sorting functionality
            const sortableHeaders = listView.querySelectorAll(".sortable");
            sortableHeaders.forEach(header => {
                header.addEventListener("click", function() {
                    sortTable(this.dataset.column);
                });
            });
            
            // Load initial data
            loadTableData();
        }
        
        function performSearch(query) {
            console.log("Searching for:", query);
            // Implementation for search
        }
        
        function sortTable(column) {
            console.log("Sorting by:", column);
            // Implementation for sorting
        }
        
        function loadTableData() {
            console.log("Loading table data");
            // Implementation for data loading
        }
        
        function debounce(func, wait) {
            let timeout;
            return function executedFunction(...args) {
                const later = () => {
                    clearTimeout(timeout);
                    func(...args);
                };
                clearTimeout(timeout);
                timeout = setTimeout(later, wait);
            };
        }
        ';
    }

    private function extractValidationRules(array $columns): array
    {
        $rules = [];
        foreach ($columns as $column) {
            if (!empty($column['validation_rules'])) {
                $rules[$column['name']] = $column['validation_rules'];
            }
        }
        return $rules;
    }
}
```

---

## Cached View System

### 1. Enhanced View Definition Model

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ViewDefinition extends Model
{
    protected $fillable = [
        'tenant_id',
        'schema_name',
        'table_name',
        'view_name',
        'view_type',
        'title',
        'description',
        'is_active',
        'cache_key',
        'cached_content',
        'cache_expires_at',
        'schema_version',
        'rendering_mode'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'cache_expires_at' => 'datetime',
        'schema_version' => 'integer'
    ];

    public function columnConfigurations(): HasMany
    {
        return $this->hasMany(ViewColumnConfiguration::class);
    }

    public function scopeForTenant($query, string $tenantId)
    {
        return $query->where('tenant_id', $tenantId);
    }

    public function scopeForTable($query, string $schemaName, string $tableName)
    {
        return $query->where('schema_name', $schemaName)
                    ->where('table_name', $tableName);
    }

    public function scopeByType($query, string $viewType)
    {
        return $query->where('view_type', $viewType);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function isCacheValid(): bool
    {
        return $this->cached_content && 
               $this->cache_expires_at && 
               $this->cache_expires_at->isFuture() &&
               $this->isSchemaCurrent();
    }

    public function isSchemaCurrent(): bool
    {
        // Check if schema version matches current schema
        $currentSchemaVersion = $this->getCurrentSchemaVersion();
        return $this->schema_version === $currentSchemaVersion;
    }

    private function getCurrentSchemaVersion(): int
    {
        // Get current schema version from database
        $result = DB::connection('ui_api')->selectOne("
            SELECT UNIX_TIMESTAMP(MAX(UPDATE_TIME)) as version
            FROM information_schema.TABLES 
            WHERE TABLE_SCHEMA = ? AND TABLE_NAME = ?
        ", [$this->schema_name, $this->table_name]);
        
        return $result->version ?? 0;
    }

    public function getRenderingMode(): string
    {
        return $this->rendering_mode ?? 'hybrid';
    }
}
```

### 2. Hybrid View Service

```php
<?php

namespace App\Services;

use App\Models\ViewDefinition;
use App\Services\DynamicViewRenderer;
use App\Services\SchemaAnalysisService;

class HybridViewService
{
    public function __construct(
        private DynamicViewRenderer $dynamicRenderer,
        private SchemaAnalysisService $schemaAnalysis
    ) {}

    public function renderView(string $tenantId, string $schemaName, string $tableName, string $viewType, array $options = []): array
    {
        // Check for existing view definition
        $viewDefinition = ViewDefinition::forTenant($tenantId)
            ->forTable($schemaName, $tableName)
            ->byType($viewType)
            ->active()
            ->first();

        if (!$viewDefinition) {
            // No view definition exists, use pure dynamic rendering
            return $this->renderDynamicView($tenantId, $schemaName, $tableName, $viewType, $options);
        }

        $renderingMode = $viewDefinition->getRenderingMode();

        return match($renderingMode) {
            'dynamic' => $this->renderDynamicView($tenantId, $schemaName, $tableName, $viewType, $options),
            'cached' => $this->renderCachedView($viewDefinition, $options),
            'hybrid' => $this->renderHybridView($viewDefinition, $tenantId, $schemaName, $tableName, $viewType, $options),
            default => $this->renderHybridView($viewDefinition, $tenantId, $schemaName, $tableName, $viewType, $options)
        };
    }

    private function renderDynamicView(string $tenantId, string $schemaName, string $tableName, string $viewType, array $options): array
    {
        return $this->dynamicRenderer->renderView($tenantId, $schemaName, $tableName, $viewType, $options);
    }

    private function renderCachedView(ViewDefinition $viewDefinition, array $options): array
    {
        if (!$viewDefinition->isCacheValid()) {
            // Cache is invalid, fallback to dynamic
            return $this->renderDynamicView(
                $viewDefinition->tenant_id,
                $viewDefinition->schema_name,
                $viewDefinition->table_name,
                $viewDefinition->view_type,
                $options
            );
        }

        $cachedContent = json_decode($viewDefinition->cached_content, true);

        return [
            'view_type' => $viewDefinition->view_type,
            'table_name' => $viewDefinition->table_name,
            'schema_name' => $viewDefinition->schema_name,
            'rendered_at' => $viewDefinition->updated_at,
            'render_time' => '0ms (cached)',
            'schema_version' => $viewDefinition->schema_version,
            'html_content' => $cachedContent['html_content'],
            'css_content' => $cachedContent['css_content'],
            'js_content' => $cachedContent['js_content'],
            'metadata' => $cachedContent['metadata'],
            'cache_info' => [
                'cache_key' => $viewDefinition->cache_key,
                'cached_at' => $viewDefinition->updated_at,
                'expires_at' => $viewDefinition->cache_expires_at,
                'schema_version' => $viewDefinition->schema_version
            ]
        ];
    }

    private function renderHybridView(ViewDefinition $viewDefinition, string $tenantId, string $schemaName, string $tableName, string $viewType, array $options): array
    {
        if (!$viewDefinition->isCacheValid()) {
            // Cache is invalid, rebuild and return dynamic
            $dynamicResult = $this->renderDynamicView($tenantId, $schemaName, $tableName, $viewType, $options);
            $this->updateCache($viewDefinition, $dynamicResult);
            return $dynamicResult;
        }

        // Use cached content as base
        $cachedContent = json_decode($viewDefinition->cached_content, true);
        
        // Apply dynamic schema overlays
        $schema = $this->schemaAnalysis->analyzeTable($tenantId, $schemaName, $tableName);
        $overlay = $this->generateSchemaOverlay($schema, $viewDefinition);

        return [
            'view_type' => $viewDefinition->view_type,
            'table_name' => $viewDefinition->table_name,
            'schema_name' => $viewDefinition->schema_name,
            'rendered_at' => now(),
            'render_time' => '5ms (hybrid)',
            'schema_version' => $schema['analyzed_at']->timestamp,
            'html_content' => $this->mergeHtmlContent($cachedContent['html_content'], $overlay['html']),
            'css_content' => $this->mergeCssContent($cachedContent['css_content'], $overlay['css']),
            'js_content' => $this->mergeJsContent($cachedContent['js_content'], $overlay['js']),
            'metadata' => $this->mergeMetadata($cachedContent['metadata'], $overlay['metadata']),
            'cache_info' => [
                'cache_key' => $viewDefinition->cache_key,
                'cached_at' => $viewDefinition->updated_at,
                'expires_at' => $viewDefinition->cache_expires_at,
                'schema_version' => $viewDefinition->schema_version,
                'overlay_applied' => true
            ]
        ];
    }

    private function generateSchemaOverlay(array $schema, ViewDefinition $viewDefinition): array
    {
        // Generate overlay based on schema changes
        $overlay = [
            'html' => '',
            'css' => '',
            'js' => '',
            'metadata' => []
        ];

        // Check for new columns
        $existingColumns = $viewDefinition->columnConfigurations()->pluck('column_name')->toArray();
        $currentColumns = array_column($schema['columns'], 'name');
        $newColumns = array_diff($currentColumns, $existingColumns);

        if (!empty($newColumns)) {
            $overlay['html'] = $this->generateNewColumnHtml($newColumns, $schema);
            $overlay['js'] = $this->generateNewColumnJs($newColumns);
        }

        // Check for removed columns
        $removedColumns = array_diff($existingColumns, $currentColumns);
        if (!empty($removedColumns)) {
            $overlay['js'] .= $this->generateRemovedColumnJs($removedColumns);
        }

        return $overlay;
    }

    private function generateNewColumnHtml(array $newColumns, array $schema): string
    {
        $html = '';
        foreach ($newColumns as $columnName) {
            $column = collect($schema['columns'])->firstWhere('name', $columnName);
            if ($column) {
                $html .= '<div class="new-column-overlay" data-column="' . $columnName . '">';
                $html .= '<span class="badge badge-info">New Column: ' . $column['display_name'] . '</span>';
                $html .= '</div>';
            }
        }
        return $html;
    }

    private function generateNewColumnJs(array $newColumns): string
    {
        return '
        // Handle new columns
        document.addEventListener("DOMContentLoaded", function() {
            const newColumns = ' . json_encode($newColumns) . ';
            newColumns.forEach(column => {
                console.log("New column detected:", column);
                // Add column to form or table
            });
        });
        ';
    }

    private function generateRemovedColumnJs(array $removedColumns): string
    {
        return '
        // Handle removed columns
        document.addEventListener("DOMContentLoaded", function() {
            const removedColumns = ' . json_encode($removedColumns) . ';
            removedColumns.forEach(column => {
                console.log("Column removed:", column);
                // Remove column from form or table
            });
        });
        ';
    }

    private function mergeHtmlContent(string $cachedHtml, string $overlayHtml): string
    {
        if (empty($overlayHtml)) {
            return $cachedHtml;
        }
        
        return $cachedHtml . $overlayHtml;
    }

    private function mergeCssContent(string $cachedCss, string $overlayCss): string
    {
        return $cachedCss . "\n" . $overlayCss;
    }

    private function mergeJsContent(string $cachedJs, string $overlayJs): string
    {
        return $cachedJs . "\n" . $overlayJs;
    }

    private function mergeMetadata(array $cachedMetadata, array $overlayMetadata): array
    {
        return array_merge_recursive($cachedMetadata, $overlayMetadata);
    }

    private function updateCache(ViewDefinition $viewDefinition, array $dynamicResult): void
    {
        $viewDefinition->update([
            'cached_content' => json_encode([
                'html_content' => $dynamicResult['html_content'],
                'css_content' => $dynamicResult['css_content'],
                'js_content' => $dynamicResult['js_content'],
                'metadata' => $dynamicResult['metadata']
            ]),
            'cache_expires_at' => now()->addHours(24),
            'schema_version' => $dynamicResult['schema_version']
        ]);
    }
}
```

---

## Rendering Strategy

### 1. Rendering Mode Selection

```php
<?php

namespace App\Services;

class RenderingStrategyService
{
    public function selectRenderingMode(string $tenantId, string $schemaName, string $tableName, string $viewType): string
    {
        // Check if table has frequent schema changes
        $schemaChangeFrequency = $this->getSchemaChangeFrequency($tenantId, $schemaName, $tableName);
        
        // Check if view is frequently accessed
        $accessFrequency = $this->getAccessFrequency($tenantId, $schemaName, $tableName, $viewType);
        
        // Check if view has custom configurations
        $hasCustomConfig = $this->hasCustomConfiguration($tenantId, $schemaName, $tableName, $viewType);
        
        if ($hasCustomConfig && $accessFrequency > 100) {
            return 'cached';
        }
        
        if ($schemaChangeFrequency > 10) {
            return 'dynamic';
        }
        
        if ($accessFrequency > 50) {
            return 'hybrid';
        }
        
        return 'dynamic';
    }

    private function getSchemaChangeFrequency(string $tenantId, string $schemaName, string $tableName): int
    {
        // Count schema changes in last 30 days
        return DB::table('schema_change_logs')
            ->where('tenant_id', $tenantId)
            ->where('schema_name', $schemaName)
            ->where('table_name', $tableName)
            ->where('created_at', '>=', now()->subDays(30))
            ->count();
    }

    private function getAccessFrequency(string $tenantId, string $schemaName, string $tableName, string $viewType): int
    {
        // Count view accesses in last 7 days
        return DB::table('view_access_logs')
            ->where('tenant_id', $tenantId)
            ->where('schema_name', $schemaName)
            ->where('table_name', $tableName)
            ->where('view_type', $viewType)
            ->where('created_at', '>=', now()->subDays(7))
            ->count();
    }

    private function hasCustomConfiguration(string $tenantId, string $schemaName, string $tableName, string $viewType): bool
    {
        return ViewDefinition::forTenant($tenantId)
            ->forTable($schemaName, $tableName)
            ->byType($viewType)
            ->exists();
    }
}
```

---

## API Design

### 1. Hybrid View API Endpoints

#### 1.1 Render View (Hybrid)

**GET** `/api/v1/database/views/render`

**Query Parameters**:
- `table_name` (required): Table name
- `view_type` (required): View type (create, update, list, analytics)
- `rendering_mode` (optional): Force rendering mode (dynamic, cached, hybrid)
- `force_refresh` (optional): Force refresh cache

**Response**:
```json
{
  "status": "success",
  "message": "View rendered successfully",
  "data": {
    "view_type": "list",
    "table_name": "users",
    "schema_name": "tenant_db",
    "rendered_at": "2024-01-01T00:00:00Z",
    "render_time": "5ms (hybrid)",
    "schema_version": 1704067200,
    "html_content": "<div class=\"dynamic-list-view\">...</div>",
    "css_content": ".dynamic-list-view { ... }",
    "js_content": "function initializeDynamicList() { ... }",
    "metadata": {
      "columns": [...],
      "searchable_columns": [...],
      "sortable_columns": [...],
      "pagination": {...}
    },
    "cache_info": {
      "cache_key": "view_tenant123_schema_users_list",
      "cached_at": "2024-01-01T00:00:00Z",
      "expires_at": "2024-01-02T00:00:00Z",
      "schema_version": 1704067200,
      "overlay_applied": true
    }
  }
}
```

#### 1.2 Get Schema Analysis

**GET** `/api/v1/database/tables/{tableName}/schema`

**Response**:
```json
{
  "status": "success",
  "message": "Schema analysis retrieved successfully",
  "data": {
    "table_name": "users",
    "schema_name": "tenant_db",
    "columns": [
      {
        "name": "id",
        "type": "bigint",
        "full_type": "bigint(20)",
        "nullable": false,
        "default": null,
        "is_primary_key": true,
        "is_auto_increment": true,
        "comment": null,
        "is_password_field": false,
        "form_control": "number",
        "validation_rules": {
          "required": false
        },
        "display_name": "Id",
        "is_editable": false,
        "is_visible": true,
        "is_searchable": true,
        "is_sortable": true
      }
    ],
    "indexes": [...],
    "foreign_keys": [...],
    "analyzed_at": "2024-01-01T00:00:00Z"
  }
}
```

#### 1.3 Update Rendering Mode

**PATCH** `/api/v1/database/views/{viewId}/rendering-mode`

```json
{
  "rendering_mode": "hybrid",
  "cache_duration": 3600
}
```

### 2. View Configuration APIs

#### 2.1 Create View Definition

**POST** `/api/v1/database/views`

```json
{
  "table_name": "users",
  "view_name": "user_management",
  "view_type": "list",
  "title": "User Management",
  "description": "Manage system users",
  "rendering_mode": "hybrid",
  "column_configurations": [
    {
      "column_name": "id",
      "display_name": "ID",
      "is_visible": true,
      "is_editable": false,
      "is_required": false,
      "is_searchable": true,
      "is_sortable": true,
      "display_order": 1
    }
  ],
  "layout_configuration": {
    "layout_type": "table",
    "layout_config": {
      "columns_per_row": 3,
      "show_actions": true,
      "pagination": {
        "per_page": 25
      }
    }
  }
}
```

---

## Implementation Examples

### 1. Hybrid View Controller

```php
<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Services\HybridViewService;
use App\Services\RenderingStrategyService;
use Illuminate\Http\Request;

class HybridViewController extends Controller
{
    public function __construct(
        private HybridViewService $hybridViewService,
        private RenderingStrategyService $renderingStrategy
    ) {}

    public function render(Request $request)
    {
        $request->validate([
            'table_name' => 'required|string',
            'view_type' => 'required|in:create,update,list,analytics',
            'rendering_mode' => 'nullable|in:dynamic,cached,hybrid',
            'force_refresh' => 'nullable|boolean'
        ]);

        $tenantId = $request->get('tenant_id');
        $schemaName = $request->get('schema_name');
        $tableName = $request->table_name;
        $viewType = $request->view_type;
        $renderingMode = $request->rendering_mode;
        $forceRefresh = $request->force_refresh ?? false;

        $options = [
            'rendering_mode' => $renderingMode,
            'force_refresh' => $forceRefresh
        ];

        $result = $this->hybridViewService->renderView(
            $tenantId,
            $schemaName,
            $tableName,
            $viewType,
            $options
        );

        return response()->json([
            'status' => 'success',
            'message' => 'View rendered successfully',
            'data' => $result
        ]);
    }

    public function getSchemaAnalysis(Request $request, string $tableName)
    {
        $tenantId = $request->get('tenant_id');
        $schemaName = $request->get('schema_name');

        $schemaAnalysis = app(SchemaAnalysisService::class);
        $result = $schemaAnalysis->analyzeTable($tenantId, $schemaName, $tableName);

        return response()->json([
            'status' => 'success',
            'message' => 'Schema analysis retrieved successfully',
            'data' => $result
        ]);
    }

    public function updateRenderingMode(Request $request, string $viewId)
    {
        $request->validate([
            'rendering_mode' => 'required|in:dynamic,cached,hybrid',
            'cache_duration' => 'nullable|integer|min:300|max:86400'
        ]);

        $viewDefinition = ViewDefinition::findOrFail($viewId);
        
        $viewDefinition->update([
            'rendering_mode' => $request->rendering_mode,
            'cache_expires_at' => now()->addSeconds($request->cache_duration ?? 3600)
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Rendering mode updated successfully',
            'data' => [
                'view_id' => $viewId,
                'rendering_mode' => $request->rendering_mode,
                'cache_expires_at' => $viewDefinition->cache_expires_at
            ]
        ]);
    }
}
```

### 2. View Cache Management

```php
<?php

namespace App\Services;

use App\Models\ViewDefinition;
use Illuminate\Support\Facades\DB;

class ViewCacheManagementService
{
    public function invalidateCache(string $tenantId, string $schemaName, string $tableName): void
    {
        ViewDefinition::forTenant($tenantId)
            ->forTable($schemaName, $tableName)
            ->update([
                'cache_expires_at' => now()->subMinute(),
                'schema_version' => 0
            ]);
    }

    public function cleanupExpiredCache(): int
    {
        return ViewDefinition::where('cache_expires_at', '<', now())
            ->update([
                'cached_content' => null,
                'cache_key' => null
            ]);
    }

    public function getCacheStatistics(string $tenantId): array
    {
        $stats = DB::table('view_definitions')
            ->where('tenant_id', $tenantId)
            ->selectRaw('
                COUNT(*) as total_views,
                COUNT(cached_content) as cached_views,
                SUM(CASE WHEN cache_expires_at > NOW() THEN 1 ELSE 0 END) as valid_cache,
                AVG(CASE WHEN cached_content IS NOT NULL THEN LENGTH(cached_content) ELSE 0 END) as avg_cache_size
            ')
            ->first();

        return [
            'total_views' => $stats->total_views,
            'cached_views' => $stats->cached_views,
            'valid_cache' => $stats->valid_cache,
            'cache_hit_rate' => $stats->total_views > 0 ? round(($stats->valid_cache / $stats->total_views) * 100, 2) : 0,
            'avg_cache_size' => round($stats->avg_cache_size, 2)
        ];
    }
}
```

---

## Summary

The hybrid view rendering system provides:

1. **Dynamic Rendering**: Real-time views based on current schema
2. **Cached Rendering**: Pre-built views for performance
3. **Hybrid Rendering**: Cached base with dynamic schema overlays
4. **Automatic Fallback**: Seamless fallback to dynamic when cache is invalid
5. **Schema Versioning**: Track schema changes and invalidate cache
6. **Performance Optimization**: Choose rendering mode based on usage patterns
7. **Flexibility**: Support for both dynamic and custom configurations

This approach combines the best of both worlds: performance from caching and flexibility from dynamic rendering.
