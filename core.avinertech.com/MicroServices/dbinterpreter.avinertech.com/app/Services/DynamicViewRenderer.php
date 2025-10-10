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

    private function renderUpdateView(array $schema, array $options): array
    {
        $columns = array_filter($schema['columns'], fn($col) => $col['is_editable'] && !$col['is_auto_increment']);
        
        $html = '<form class="dynamic-form update-form" data-view-type="update" data-table="' . $schema['table_name'] . '">';
        $html .= '<div class="form-container">';
        
        foreach ($columns as $column) {
            $html .= $this->renderFormField($column, 'update');
        }
        
        $html .= '<div class="form-actions">';
        $html .= '<button type="submit" class="btn btn-primary">Update Record</button>';
        $html .= '<button type="button" class="btn btn-secondary cancel-btn">Cancel</button>';
        $html .= '</div>';
        $html .= '</div>';
        $html .= '</form>';
        
        return [
            'html' => $html,
            'css' => $this->getFormCss(),
            'js' => $this->getFormJs('update'),
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

    private function renderAnalyticsView(array $schema, array $options): array
    {
        $columns = array_filter($schema['columns'], fn($col) => $col['is_visible']);
        $numericColumns = array_filter($columns, fn($col) => in_array($col['type'], ['int', 'bigint', 'decimal', 'float', 'double']));
        
        $html = '<div class="dynamic-analytics-view" data-table="' . $schema['table_name'] . '">';
        $html .= '<div class="analytics-header">';
        $html .= '<h3>Analytics Dashboard</h3>';
        $html .= '<div class="date-range-picker">';
        $html .= '<input type="date" class="form-control start-date" placeholder="Start Date">';
        $html .= '<input type="date" class="form-control end-date" placeholder="End Date">';
        $html .= '<button class="btn btn-primary apply-filters">Apply Filters</button>';
        $html .= '</div>';
        $html .= '</div>';
        
        $html .= '<div class="analytics-widgets">';
        
        // Summary cards
        if (!empty($numericColumns)) {
            $html .= '<div class="summary-cards">';
            foreach (array_slice($numericColumns, 0, 4) as $column) {
                $html .= '<div class="summary-card">';
                $html .= '<h4>' . $column['display_name'] . '</h4>';
                $html .= '<div class="metric-value" data-metric="' . $column['name'] . '">--</div>';
                $html .= '</div>';
            }
            $html .= '</div>';
        }
        
        // Charts section
        $html .= '<div class="charts-section">';
        $html .= '<div class="chart-container">';
        $html .= '<canvas id="analytics-chart" width="400" height="200"></canvas>';
        $html .= '</div>';
        $html .= '</div>';
        
        $html .= '</div>';
        $html .= '</div>';
        
        return [
            'html' => $html,
            'css' => $this->getAnalyticsCss(),
            'js' => $this->getAnalyticsJs(),
            'metadata' => [
                'columns' => $columns,
                'numeric_columns' => $numericColumns,
                'chart_config' => [
                    'type' => 'line',
                    'responsive' => true
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

    private function getAnalyticsCss(): string
    {
        return '
        .dynamic-analytics-view {
            padding: 20px;
        }
        .analytics-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
        }
        .date-range-picker {
            display: flex;
            gap: 10px;
            align-items: center;
        }
        .summary-cards {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        .summary-card {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            text-align: center;
        }
        .summary-card h4 {
            margin: 0 0 10px 0;
            color: #6c757d;
        }
        .metric-value {
            font-size: 2em;
            font-weight: bold;
            color: #007bff;
        }
        .charts-section {
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
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
            form.addEventListener("submit", function(e) {
                e.preventDefault();
                if (validateForm(form)) {
                    submitForm(form);
                }
            });
            
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
            return true; // Implementation for field validation
        }
        
        function showPasswordStrength(field) {
            // Implementation for password strength indicator
        }
        
        function submitForm(form) {
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
            const searchInput = listView.querySelector(".search-input");
            if (searchInput) {
                searchInput.addEventListener("input", debounce(function() {
                    performSearch(this.value);
                }, 300));
            }
            
            const sortableHeaders = listView.querySelectorAll(".sortable");
            sortableHeaders.forEach(header => {
                header.addEventListener("click", function() {
                    sortTable(this.dataset.column);
                });
            });
            
            loadTableData();
        }
        
        function performSearch(query) {
            console.log("Searching for:", query);
        }
        
        function sortTable(column) {
            console.log("Sorting by:", column);
        }
        
        function loadTableData() {
            console.log("Loading table data");
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

    private function getAnalyticsJs(): string
    {
        return '
        document.addEventListener("DOMContentLoaded", function() {
            const analyticsView = document.querySelector(".dynamic-analytics-view");
            if (analyticsView) {
                initializeAnalytics(analyticsView);
            }
        });
        
        function initializeAnalytics(view) {
            loadAnalyticsData();
            
            const applyFiltersBtn = view.querySelector(".apply-filters");
            if (applyFiltersBtn) {
                applyFiltersBtn.addEventListener("click", function() {
                    applyDateFilters();
                });
            }
        }
        
        function loadAnalyticsData() {
            console.log("Loading analytics data");
            // Implementation for loading analytics data
        }
        
        function applyDateFilters() {
            console.log("Applying date filters");
            // Implementation for applying date filters
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
