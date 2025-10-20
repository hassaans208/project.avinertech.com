<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\ViewDefinition;
use App\Models\ViewColumnConfiguration;
use App\Models\ViewLayoutConfiguration;
use App\Services\SchemaAnalysisService;
use App\Services\DynamicViewRenderer;
use App\Services\HybridViewService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ViewController extends Controller
{
    public function __construct(
        private SchemaAnalysisService $schemaAnalysis,
        private DynamicViewRenderer $dynamicRenderer,
        private HybridViewService $hybridViewService
    ) {}

    /**
     * Get all view definitions for a tenant
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $tenantId = $request->get('tenant_id');
            $schemaName = $request->get('schema_name');
            
            $query = ViewDefinition::forTenant($tenantId)->active();
            
            if ($request->has('table_name')) {
                $query->forTable($schemaName, $request->table_name);
            }
            
            if ($request->has('view_type')) {
                $query->byType($request->view_type);
            }
            
            $views = $query->with(['columnConfigurations', 'layoutConfigurations'])
                          ->orderBy('created_at', 'desc')
                          ->get();

            return response()->json([
                'status' => 'success',
                'message' => 'View definitions retrieved successfully',
                'data' => $views
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to retrieve view definitions', [
                'tenant_id' => $request->get('tenant_id'),
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'status' => 'error',
                'message' => 'Failed to retrieve view definitions',
                'error' => [
                    'code' => 'VIEW_RETRIEVAL_FAILED',
                    'details' => $e->getMessage()
                ]
            ], 500);
        }
    }

    /**
     * Get specific view definition
     */
    public function show(Request $request, string $viewId): JsonResponse
    {
        try {
            $tenantId = $request->get('tenant_id');
            
            $view = ViewDefinition::forTenant($tenantId)
                ->with(['columnConfigurations', 'layoutConfigurations', 'permissions'])
                ->findOrFail($viewId);

            return response()->json([
                'status' => 'success',
                'message' => 'View definition retrieved successfully',
                'data' => $view
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to retrieve view definition', [
                'tenant_id' => $request->get('tenant_id'),
                'view_id' => $viewId,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'status' => 'error',
                'message' => 'Failed to retrieve view definition',
                'error' => [
                    'code' => 'VIEW_NOT_FOUND',
                    'details' => $e->getMessage()
                ]
            ], 404);
        }
    }

    /**
     * Create new view definition
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'table_name' => 'required|string|max:64',
                'view_name' => 'required|string|max:100',
                'view_type' => 'required|in:create,update,list,analytics',
                'title' => 'required|string|max:255',
                'description' => 'nullable|string',
                'rendering_mode' => 'nullable|in:dynamic,cached,hybrid',
                'column_configurations' => 'required|array',
                'layout_configuration' => 'required|array'
            ]);

            $tenantId = $request->get('tenant_id');
            $schemaName = $request->get('schema_name');

            // Check if view already exists
            $existingView = ViewDefinition::forTenant($tenantId)
                ->forTable($schemaName, $request->table_name)
                ->where('view_name', $request->view_name)
                ->byType($request->view_type)
                ->first();

            if ($existingView) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'View definition already exists',
                    'error' => [
                        'code' => 'VIEW_ALREADY_EXISTS',
                        'details' => 'A view with this name and type already exists for this table'
                    ]
                ], 409);
            }

            DB::beginTransaction();

            $viewDefinition = ViewDefinition::create([
                'tenant_id' => $tenantId,
                'schema_name' => $schemaName,
                'table_name' => $request->table_name,
                'view_name' => $request->view_name,
                'view_type' => $request->view_type,
                'title' => $request->title,
                'description' => $request->description,
                'rendering_mode' => $request->rendering_mode ?? 'hybrid',
                'schema_version' => $this->getCurrentSchemaVersion($schemaName, $request->table_name)
            ]);

            // Create column configurations
            foreach ($request->column_configurations as $config) {
                $viewDefinition->columnConfigurations()->create($config);
            }

            // Create layout configuration
            $viewDefinition->layoutConfigurations()->create($request->layout_configuration);

            DB::commit();

            $viewDefinition->load(['columnConfigurations', 'layoutConfigurations']);

            return response()->json([
                'status' => 'success',
                'message' => 'View definition created successfully',
                'data' => $viewDefinition
            ], 201);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed',
                'error' => [
                    'code' => 'VALIDATION_FAILED',
                    'details' => $e->errors()
                ]
            ], 422);
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Failed to create view definition', [
                'tenant_id' => $request->get('tenant_id'),
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'status' => 'error',
                'message' => 'Failed to create view definition',
                'error' => [
                    'code' => 'VIEW_CREATION_FAILED',
                    'details' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]
            ], 500);
        }
    }

    /**
     * Update view definition
     */
    public function update(Request $request, string $viewId): JsonResponse
    {
        try {
            $request->validate([
                'title' => 'sometimes|string|max:255',
                'description' => 'nullable|string',
                'rendering_mode' => 'sometimes|in:dynamic,cached,hybrid',
                'is_active' => 'sometimes|boolean'
            ]);

            $tenantId = $request->get('tenant_id');
            
            $viewDefinition = ViewDefinition::forTenant($tenantId)->findOrFail($viewId);

            $updateData = $request->only(['title', 'description', 'rendering_mode', 'is_active']);
            
            if ($request->has('is_active') && !$request->is_active) {
                $updateData['cache_expires_at'] = now()->subMinute();
            }

            $viewDefinition->update($updateData);

            return response()->json([
                'status' => 'success',
                'message' => 'View definition updated successfully',
                'data' => $viewDefinition->fresh(['columnConfigurations', 'layoutConfigurations'])
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed',
                'error' => [
                    'code' => 'VALIDATION_FAILED',
                    'details' => $e->errors()
                ]
            ], 422);
        } catch (\Exception $e) {
            Log::error('Failed to update view definition', [
                'tenant_id' => $request->get('tenant_id'),
                'view_id' => $viewId,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'status' => 'error',
                'message' => 'Failed to update view definition',
                'error' => [
                    'code' => 'VIEW_UPDATE_FAILED',
                    'details' => $e->getMessage()
                ]
            ], 500);
        }
    }

    /**
     * Delete view definition
     */
    public function destroy(Request $request, string $viewId): JsonResponse
    {
        try {
            $tenantId = $request->get('tenant_id');
            
            $viewDefinition = ViewDefinition::forTenant($tenantId)->findOrFail($viewId);
            $viewDefinition->delete();

            return response()->json([
                'status' => 'success',
                'message' => 'View definition deleted successfully'
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to delete view definition', [
                'tenant_id' => $request->get('tenant_id'),
                'view_id' => $viewId,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'status' => 'error',
                'message' => 'Failed to delete view definition',
                'error' => [
                    'code' => 'VIEW_DELETION_FAILED',
                    'details' => $e->getMessage()
                ]
            ], 500);
        }
    }

    /**
     * Render view (hybrid approach)
     */
    public function render(Request $request): JsonResponse
    {
        try {
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

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed',
                'error' => [
                    'code' => 'VALIDATION_FAILED',
                    'details' => $e->errors()
                ]
            ], 422);
        } catch (\Exception $e) {
            Log::error('Failed to render view', [
                'tenant_id' => $request->get('tenant_id'),
                'table_name' => $request->table_name,
                'view_type' => $request->view_type,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'status' => 'error',
                'message' => 'Failed to render view',
                'error' => [
                    'code' => 'VIEW_RENDER_FAILED',
                    'details' => $e->getMessage()
                ]
            ], 500);
        }
    }

    /**
     * Build and cache view
     */
    public function build(Request $request, string $viewId): JsonResponse
    {
        try {
            $request->validate([
                'force_rebuild' => 'nullable|boolean',
                'cache_duration' => 'nullable|integer|min:300|max:86400'
            ]);

            $tenantId = $request->get('tenant_id');
            $forceRebuild = $request->force_rebuild ?? false;
            $cacheDuration = $request->cache_duration ?? 3600;

            $viewDefinition = ViewDefinition::forTenant($tenantId)->findOrFail($viewId);

            if (!$forceRebuild && $viewDefinition->isCacheValid()) {
                return response()->json([
                    'status' => 'success',
                    'message' => 'View already cached',
                    'data' => [
                        'view_id' => $viewId,
                        'cache_key' => $viewDefinition->getCacheKey(),
                        'cache_expires_at' => $viewDefinition->cache_expires_at
                    ]
                ]);
            }

            $result = $this->hybridViewService->buildView($viewDefinition, $cacheDuration);

            return response()->json([
                'status' => 'success',
                'message' => 'View built and cached successfully',
                'data' => $result
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to build view', [
                'tenant_id' => $request->get('tenant_id'),
                'view_id' => $viewId,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'status' => 'error',
                'message' => 'Failed to build view',
                'error' => [
                    'code' => 'VIEW_BUILD_FAILED',
                    'details' => $e->getMessage()
                ]
            ], 500);
        }
    }

    /**
     * Get cached view content
     */
    public function getCached(Request $request, string $viewId): JsonResponse
    {
        try {
            $tenantId = $request->get('tenant_id');
            
            $viewDefinition = ViewDefinition::forTenant($tenantId)->findOrFail($viewId);

            if (!$viewDefinition->isCacheValid()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'View cache is invalid or expired',
                    'error' => [
                        'code' => 'CACHE_INVALID',
                        'details' => 'Please rebuild the view cache'
                    ]
                ], 404);
            }

            $cachedContent = json_decode($viewDefinition->cached_content, true);

            return response()->json([
                'status' => 'success',
                'message' => 'Cached view retrieved successfully',
                'data' => [
                    'view_id' => $viewId,
                    'view_type' => $viewDefinition->view_type,
                    'title' => $viewDefinition->title,
                    'html_content' => $cachedContent['html_content'],
                    'css_content' => $cachedContent['css_content'],
                    'js_content' => $cachedContent['js_content'],
                    'metadata' => $cachedContent['metadata'],
                    'cache_info' => [
                        'cache_key' => $viewDefinition->getCacheKey(),
                        'cached_at' => $viewDefinition->updated_at,
                        'expires_at' => $viewDefinition->cache_expires_at,
                        'hit_count' => 0 // TODO: Implement hit counting
                    ]
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to get cached view', [
                'tenant_id' => $request->get('tenant_id'),
                'view_id' => $viewId,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'status' => 'error',
                'message' => 'Failed to get cached view',
                'error' => [
                    'code' => 'CACHE_RETRIEVAL_FAILED',
                    'details' => $e->getMessage()
                ]
            ], 500);
        }
    }

    /**
     * Update column configuration
     */
    public function updateColumn(Request $request, string $viewId, string $columnName): JsonResponse
    {
        try {
            $request->validate([
                'display_name' => 'sometimes|string|max:255',
                'is_visible' => 'sometimes|boolean',
                'is_editable' => 'sometimes|boolean',
                'is_required' => 'sometimes|boolean',
                'is_searchable' => 'sometimes|boolean',
                'is_sortable' => 'sometimes|boolean',
                'display_order' => 'sometimes|integer',
                'validation_rules' => 'sometimes|array',
                'placeholder_text' => 'nullable|string|max:255',
                'help_text' => 'nullable|string'
            ]);

            $tenantId = $request->get('tenant_id');
            
            $viewDefinition = ViewDefinition::forTenant($tenantId)->findOrFail($viewId);
            
            $columnConfig = $viewDefinition->columnConfigurations()
                ->where('column_name', $columnName)
                ->firstOrFail();

            $columnConfig->update($request->only([
                'display_name', 'is_visible', 'is_editable', 'is_required',
                'is_searchable', 'is_sortable', 'display_order', 'validation_rules',
                'placeholder_text', 'help_text'
            ]));

            // Invalidate cache
            $viewDefinition->update(['cache_expires_at' => now()->subMinute()]);

            return response()->json([
                'status' => 'success',
                'message' => 'Column configuration updated successfully',
                'data' => $columnConfig
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to update column configuration', [
                'tenant_id' => $request->get('tenant_id'),
                'view_id' => $viewId,
                'column_name' => $columnName,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'status' => 'error',
                'message' => 'Failed to update column configuration',
                'error' => [
                    'code' => 'COLUMN_UPDATE_FAILED',
                    'details' => $e->getMessage()
                ]
            ], 500);
        }
    }

    /**
     * Reorder columns
     */
    public function reorderColumns(Request $request, string $viewId): JsonResponse
    {
        try {
            $request->validate([
                'column_orders' => 'required|array',
                'column_orders.*.column_name' => 'required|string',
                'column_orders.*.display_order' => 'required|integer'
            ]);

            $tenantId = $request->get('tenant_id');
            
            $viewDefinition = ViewDefinition::forTenant($tenantId)->findOrFail($viewId);

            DB::beginTransaction();

            foreach ($request->column_orders as $order) {
                $viewDefinition->columnConfigurations()
                    ->where('column_name', $order['column_name'])
                    ->update(['display_order' => $order['display_order']]);
            }

            // Invalidate cache
            $viewDefinition->update(['cache_expires_at' => now()->subMinute()]);

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Columns reordered successfully'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            
            Log::error('Failed to reorder columns', [
                'tenant_id' => $request->get('tenant_id'),
                'view_id' => $viewId,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'status' => 'error',
                'message' => 'Failed to reorder columns',
                'error' => [
                    'code' => 'COLUMN_REORDER_FAILED',
                    'details' => $e->getMessage()
                ]
            ], 500);
        }
    }

    /**
     * Get schema analysis for a table
     */
    public function getSchemaAnalysis(Request $request, string $tableName): JsonResponse
    {
        try {
            $tenantId = $request->get('tenant_id');
            $schemaName = $request->get('schema_name');

            $result = $this->schemaAnalysis->analyzeTable($tenantId, $schemaName, $tableName);

            return response()->json([
                'status' => 'success',
                'message' => 'Schema analysis retrieved successfully',
                'data' => $result
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to get schema analysis', [
                'tenant_id' => $request->get('tenant_id'),
                'table_name' => $tableName,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'status' => 'error',
                'message' => 'Failed to get schema analysis',
                'error' => [
                    'code' => 'SCHEMA_ANALYSIS_FAILED',
                    'details' => $e->getMessage()
                ]
            ], 500);
        }
    }

    /**
     * Build all views for a table
     */
    public function buildAllViews(Request $request, string $tableName): JsonResponse
    {
        try {
            $request->validate([
                'view_types' => 'nullable|array',
                'view_types.*' => 'in:create,update,list,analytics',
                'force_rebuild' => 'nullable|boolean',
                'cache_duration' => 'nullable|integer|min:300|max:86400'
            ]);

            $tenantId = $request->get('tenant_id');
            $schemaName = $request->get('schema_name');
            $viewTypes = $request->view_types ?? ['create', 'update', 'list', 'analytics'];
            $forceRebuild = $request->force_rebuild ?? false;
            $cacheDuration = $request->cache_duration ?? 3600;

            $results = [];

            foreach ($viewTypes as $viewType) {
                $viewDefinition = ViewDefinition::forTenant($tenantId)
                    ->forTable($schemaName, $tableName)
                    ->byType($viewType)
                    ->first();

                if ($viewDefinition) {
                    $result = $this->hybridViewService->buildView($viewDefinition, $cacheDuration);
                    $results[] = $result;
                } else {
                    // Create dynamic view if no definition exists
                    $result = $this->hybridViewService->renderView(
                        $tenantId,
                        $schemaName,
                        $tableName,
                        $viewType,
                        ['force_refresh' => $forceRebuild]
                    );
                    $results[] = $result;
                }
            }

            return response()->json([
                'status' => 'success',
                'message' => 'All views built successfully',
                'data' => [
                    'table_name' => $tableName,
                    'view_types' => $viewTypes,
                    'results' => $results
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to build all views', [
                'tenant_id' => $request->get('tenant_id'),
                'table_name' => $tableName,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'status' => 'error',
                'message' => 'Failed to build all views',
                'error' => [
                    'code' => 'BULK_BUILD_FAILED',
                    'details' => $e->getMessage()
                ]
            ], 500);
        }
    }

    private function getCurrentSchemaVersion(string $schemaName, string $tableName): int
    {
        $result = DB::connection('ui_api')->selectOne("
            SELECT UNIX_TIMESTAMP(MAX(UPDATE_TIME)) as version
            FROM information_schema.TABLES 
            WHERE TABLE_SCHEMA = ? AND TABLE_NAME = ?
        ", [$schemaName, $tableName]);
        
        return $result->version ?? 0;
    }
}
