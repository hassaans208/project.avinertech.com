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

    public function buildView(ViewDefinition $viewDefinition, int $cacheDuration = 3600): array
    {
        $startTime = microtime(true);

        // Generate HTML content
        $htmlContent = $this->generateHtmlContent($viewDefinition);
        
        // Generate CSS content
        $cssContent = $this->generateCssContent($viewDefinition);
        
        // Generate JS content
        $jsContent = $this->generateJsContent($viewDefinition);
        
        // Prepare metadata
        $metadata = $this->prepareMetadata($viewDefinition);

        $cacheContent = [
            'html_content' => $htmlContent,
            'css_content' => $cssContent,
            'js_content' => $jsContent,
            'metadata' => $metadata,
            'version' => time()
        ];

        // Update view definition with cached content
        $viewDefinition->update([
            'cached_content' => json_encode($cacheContent),
            'cache_expires_at' => now()->addSeconds($cacheDuration),
            'cache_key' => $viewDefinition->getCacheKey()
        ]);

        $buildTime = round((microtime(true) - $startTime) * 1000, 2);

        return [
            'view_id' => $viewDefinition->id,
            'cache_key' => $viewDefinition->getCacheKey(),
            'cache_expires_at' => $viewDefinition->cache_expires_at,
            'build_time' => $buildTime . 'ms',
            'cache_size' => $this->formatBytes(strlen($viewDefinition->cached_content))
        ];
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
                'cache_key' => $viewDefinition->getCacheKey(),
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
                'cache_key' => $viewDefinition->getCacheKey(),
                'cached_at' => $viewDefinition->updated_at,
                'expires_at' => $viewDefinition->cache_expires_at,
                'schema_version' => $viewDefinition->schema_version,
                'overlay_applied' => true
            ]
        ];
    }

    private function generateHtmlContent(ViewDefinition $viewDefinition): string
    {
        $columns = $viewDefinition->columnConfigurations()->ordered()->get();
        $layout = $viewDefinition->layoutConfigurations()->first();

        switch ($viewDefinition->view_type) {
            case 'create':
                return $this->generateCreateFormHtml($columns, $layout);
            case 'update':
                return $this->generateUpdateFormHtml($columns, $layout);
            case 'list':
                return $this->generateListTableHtml($columns, $layout);
            case 'analytics':
                return $this->generateAnalyticsDashboardHtml($columns, $layout);
            default:
                throw new \InvalidArgumentException('Invalid view type');
        }
    }

    private function generateCreateFormHtml($columns, $layout): string
    {
        $html = '<form class="view-form create-form" data-view-type="create">';
        $html .= '<div class="form-container">';
        
        foreach ($columns as $column) {
            if (!$column->is_visible) continue;
            
            $html .= '<div class="form-group">';
            $html .= '<label for="' . $column->column_name . '">' . $column->display_name . '</label>';
            
            if ($column->is_password_field) {
                $html .= '<input type="password" 
                                id="' . $column->column_name . '" 
                                name="' . $column->column_name . '" 
                                class="form-control password-field" 
                                placeholder="' . $column->placeholder_text . '">';
            } else {
                $html .= '<input type="text" 
                                id="' . $column->column_name . '" 
                                name="' . $column->column_name . '" 
                                class="form-control" 
                                placeholder="' . $column->placeholder_text . '">';
            }
            
            if ($column->help_text) {
                $html .= '<small class="form-text text-muted">' . $column->help_text . '</small>';
            }
            
            $html .= '</div>';
        }
        
        $html .= '<button type="submit" class="btn btn-primary">Create</button>';
        $html .= '</div>';
        $html .= '</form>';
        
        return $html;
    }

    private function generateListTableHtml($columns, $layout): string
    {
        $html = '<div class="view-container list-view">';
        $html .= '<div class="table-responsive">';
        $html .= '<table class="table table-striped">';
        $html .= '<thead><tr>';
        
        foreach ($columns as $column) {
            if (!$column->is_visible) continue;
            
            $sortableClass = $column->is_sortable ? 'sortable' : '';
            $html .= '<th class="' . $sortableClass . '" data-column="' . $column->column_name . '">';
            $html .= $column->display_name;
            $html .= '</th>';
        }
        
        $html .= '<th>Actions</th>';
        $html .= '</tr></thead>';
        $html .= '<tbody id="table-body">';
        $html .= '<!-- Data will be loaded dynamically -->';
        $html .= '</tbody>';
        $html .= '</table>';
        $html .= '</div>';
        $html .= '</div>';
        
        return $html;
    }

    private function generateCssContent(ViewDefinition $viewDefinition): string
    {
        return '
        .view-container {
            padding: 20px;
        }
        .password-field {
            font-family: monospace;
        }
        .sortable {
            cursor: pointer;
        }
        .sortable:hover {
            background-color: #f8f9fa;
        }
        ';
    }

    private function generateJsContent(ViewDefinition $viewDefinition): string
    {
        return '
        function initializeView() {
            console.log("View initialized for: ' . $viewDefinition->view_name . '");
            
            if (document.querySelector(".view-form")) {
                initializeFormValidation();
            }
            
            if (document.querySelector(".sortable")) {
                initializeTableSorting();
            }
            
            if (document.querySelector(".password-field")) {
                initializePasswordField();
            }
        }
        
        function initializePasswordField() {
            const passwordFields = document.querySelectorAll(".password-field");
            passwordFields.forEach(field => {
                field.addEventListener("input", function() {
                    validatePasswordStrength(this.value);
                });
            });
        }
        
        function validatePasswordStrength(password) {
            // Implementation for password strength validation
        }
        ';
    }

    private function prepareMetadata(ViewDefinition $viewDefinition): array
    {
        return [
            'view_id' => $viewDefinition->id,
            'view_type' => $viewDefinition->view_type,
            'table_name' => $viewDefinition->table_name,
            'columns' => $viewDefinition->columnConfigurations()->ordered()->get()->toArray(),
            'layout' => $viewDefinition->layoutConfigurations()->first()?->toArray(),
            'permissions' => $viewDefinition->permissions()->get()->toArray()
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
        document.addEventListener("DOMContentLoaded", function() {
            const newColumns = ' . json_encode($newColumns) . ';
            newColumns.forEach(column => {
                console.log("New column detected:", column);
            });
        });
        ';
    }

    private function generateRemovedColumnJs(array $removedColumns): string
    {
        return '
        document.addEventListener("DOMContentLoaded", function() {
            const removedColumns = ' . json_encode($removedColumns) . ';
            removedColumns.forEach(column => {
                console.log("Column removed:", column);
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

    private function formatBytes(int $bytes): string
    {
        $units = ['B', 'KB', 'MB', 'GB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        $bytes /= pow(1024, $pow);
        return round($bytes, 2) . ' ' . $units[$pow];
    }
}
