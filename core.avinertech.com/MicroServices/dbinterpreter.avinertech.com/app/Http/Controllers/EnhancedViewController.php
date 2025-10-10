<?php

namespace App\Http\Controllers;

use App\Services\EnhancedViewService;
use App\Models\ViewDefinition;
use App\Models\ViewType;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class EnhancedViewController extends Controller
{
    protected $enhancedViewService;

    public function __construct(EnhancedViewService $enhancedViewService)
    {
        $this->enhancedViewService = $enhancedViewService;
    }

    /**
     * Get all available view types with their options
     */
    public function getViewTypes(): JsonResponse
    {
        try {
            $viewTypes = $this->enhancedViewService->getAvailableViewTypes();
            
            return response()->json([
                'success' => true,
                'data' => $viewTypes
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve view types',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get table schema information
     */
    public function getTableSchema(Request $request): JsonResponse
    {
        $request->validate([
            'schema_name' => 'required|string',
            'table_name' => 'required|string'
        ]);

        try {
            $schema = $this->enhancedViewService->getTableSchema(
                $request->schema_name,
                $request->table_name
            );
            
            return response()->json([
                'success' => true,
                'data' => $schema
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve table schema',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Generate default column configurations for a table and view type
     */
    public function generateDefaultConfigurations(Request $request): JsonResponse
    {
        $request->validate([
            'schema_name' => 'required|string',
            'table_name' => 'required|string',
            'view_type' => 'required|string'
        ]);

        try {
            $configurations = $this->enhancedViewService->generateDefaultColumnConfigurations(
                $request->schema_name,
                $request->table_name,
                $request->view_type
            );
            
            return response()->json([
                'success' => true,
                'data' => $configurations
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to generate default configurations',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Create a new view definition
     */
    public function createViewDefinition(Request $request): JsonResponse
    {
        $request->validate([
            'tenant_id' => 'required|string',
            'schema_name' => 'required|string',
            'table_name' => 'required|string',
            'view_name' => 'required|string',
            'view_type' => 'required|string',
            'title' => 'required|string',
            'description' => 'nullable|string',
            'view_configuration' => 'nullable|array',
            'columns' => 'nullable|array'
        ]);

        try {
            // Validate the complete configuration
            $validationErrors = $this->enhancedViewService->validateViewConfiguration($request->all());
            if (!empty($validationErrors)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validationErrors
                ], 422);
            }

            $viewDefinition = $this->enhancedViewService->createViewDefinition($request->all());
            
            return response()->json([
                'success' => true,
                'message' => 'View definition created successfully',
                'data' => $this->enhancedViewService->getViewDefinitionWithConfiguration($viewDefinition->id)
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create view definition',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update an existing view definition
     */
    public function updateViewDefinition(Request $request, int $id): JsonResponse
    {
        $request->validate([
            'view_type' => 'nullable|string',
            'title' => 'nullable|string',
            'description' => 'nullable|string',
            'view_configuration' => 'nullable|array',
            'columns' => 'nullable|array'
        ]);

        try {
            $viewDefinition = ViewDefinition::findOrFail($id);
            
            // Validate the complete configuration if provided
            if ($request->has('view_type') || $request->has('view_configuration') || $request->has('columns')) {
                $validationData = array_merge($viewDefinition->toArray(), $request->all());
                $validationErrors = $this->enhancedViewService->validateViewConfiguration($validationData);
                if (!empty($validationErrors)) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Validation failed',
                        'errors' => $validationErrors
                    ], 422);
                }
            }

            $updatedViewDefinition = $this->enhancedViewService->updateViewDefinition($viewDefinition, $request->all());
            
            return response()->json([
                'success' => true,
                'message' => 'View definition updated successfully',
                'data' => $this->enhancedViewService->getViewDefinitionWithConfiguration($updatedViewDefinition->id)
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update view definition',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get a view definition with full configuration
     */
    public function getViewDefinition(int $id): JsonResponse
    {
        try {
            $viewDefinition = $this->enhancedViewService->getViewDefinitionWithConfiguration($id);
            
            return response()->json([
                'success' => true,
                'data' => $viewDefinition
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve view definition',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Validate a view configuration
     */
    public function validateConfiguration(Request $request): JsonResponse
    {
        $request->validate([
            'view_type' => 'required|string',
            'view_configuration' => 'nullable|array',
            'columns' => 'nullable|array'
        ]);

        try {
            $errors = $this->enhancedViewService->validateViewConfiguration($request->all());
            
            return response()->json([
                'success' => empty($errors),
                'message' => empty($errors) ? 'Configuration is valid' : 'Configuration validation failed',
                'errors' => $errors
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to validate configuration',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get view definitions for a specific tenant and table
     */
    public function getViewDefinitions(Request $request): JsonResponse
    {
        $request->validate([
            'tenant_id' => 'required|string',
            'schema_name' => 'nullable|string',
            'table_name' => 'nullable|string',
            'view_type' => 'nullable|string'
        ]);

        try {
            $query = ViewDefinition::with(['viewTypeDefinition.activeOptions', 'columnConfigurations'])
                ->where('tenant_id', $request->tenant_id);

            if ($request->schema_name) {
                $query->where('schema_name', $request->schema_name);
            }

            if ($request->table_name) {
                $query->where('table_name', $request->table_name);
            }

            if ($request->view_type) {
                $query->where('view_type', $request->view_type);
            }

            $viewDefinitions = $query->get()->map(function ($viewDefinition) {
                return $this->enhancedViewService->getViewDefinitionWithConfiguration($viewDefinition->id);
            });
            
            return response()->json([
                'success' => true,
                'data' => $viewDefinitions
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve view definitions',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
