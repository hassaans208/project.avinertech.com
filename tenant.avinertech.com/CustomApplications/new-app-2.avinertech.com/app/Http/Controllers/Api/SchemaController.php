<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\SchemaService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SchemaController extends Controller
{
    protected $schemaService;

    public function __construct(SchemaService $schemaService)
    {
        $this->schemaService = $schemaService;
    }

    /**
     * Get all schemas
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getSchemas()
    {
        try {
            $schemas = $this->schemaService->getSchemas();
            return response()->json([
                'status' => 'success',
                'data' => $schemas
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Store schema data
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function storeSchema(Request $request)
    {
        try {
            // Validate request
            $validator = Validator::make($request->all(), [
                'schemas' => 'required|array',
                'schemas.*.name' => 'required|string|max:255',
                'schemas.*.fields' => 'required|array',
                'schemas.*.fields.*.name' => 'required|string|max:255', 
                'schemas.*.fields.*.type' => 'required|string|max:255',
                'schemas.*.fields.*.nullable' => 'boolean',
                'schemas.*.fields.*.unique' => 'boolean',
                'schemas.*.fields.*.indexed' => 'boolean',
                'schemas.*.fields.*.encrypted' => 'boolean',
                'schemas.*.tableType' => 'required|string|in:regular,pivot,enum',
                'schemas.*.description' => 'nullable|string',
                'schemas.*.migration' => 'required|array',
                'schemas.*.model' => 'required|array',
                'schemas.*.factory' => 'required|array',
                'schemas.*.queries' => 'array'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            // Store schemas
            $storedSchemas = $this->schemaService->storeSchema($request->schemas);

            return response()->json([
                'status' => 'success',
                'message' => 'Schemas stored successfully',
                'data' => $storedSchemas
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    }
} 