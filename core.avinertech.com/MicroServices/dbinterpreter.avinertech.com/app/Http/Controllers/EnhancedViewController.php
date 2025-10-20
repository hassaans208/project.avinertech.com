<?php

namespace App\Http\Controllers;

use App\Services\EnhancedViewService;
use App\Models\ModuleDefinition;
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
     * Create a new view definition
     */
    public function createViewDefinition(Request $request): JsonResponse
    {
        $tenantId = $request->get('tenant_id');
        $schemaName = $request->get('schema_name');

        $request->validate([
            'table_name' => 'required|string',
            'name' => 'required|string',
            'view_category_id' => 'required|integer|exists:view_categories,id',
            'title' => 'required|string',
            'description' => 'nullable|string',
            'view_configuration' => 'nullable|array',
            'column_configurations' => 'required|array'
        ]);

        try {
            // Validate the complete configuration
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
            'view_category_id' => 'nullable|integer|exists:view_categories,id',
            'title' => 'nullable|string',
            'description' => 'nullable|string',
            'view_configuration' => 'nullable|array',
            'column_configurations' => 'nullable|array'
        ]);

        try {
            $viewDefinition = ModuleDefinition::findOrFail($id);
            
            // Validate the complete configuration if provided
            if ($request->has('view_category_id') || $request->has('view_configuration') || $request->has('column_configurations')) {
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
     * Get view definitions for a specific tenant and table
     */
    public function getViewDefinitions(Request $request): JsonResponse
    {
        $request->validate([
            'tenant_id' => 'required|string',
            'schema_name' => 'nullable|string',
            'table_name' => 'nullable|string',
            'view_category_id' => 'nullable|integer|exists:view_categories,id'
        ]);

        try {
            $query = ModuleDefinition::where('tenant_id', $request->tenant_id);

            if ($request->schema_name) {
                $query->where('schema_name', $request->schema_name);
            }

            if ($request->table_name) {
                $query->where('table_name', $request->table_name);
            }

            // Handle view category filtering
            if ($request->view_category_id) {
                $query->where('view_category_id', $request->view_category_id);
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

