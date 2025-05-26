<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\ColumnInterpreterService;
use App\Services\EncryptionService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;

class ColumnInterpreterController extends Controller
{
    protected $interpreterService;
    protected $encryptionService;

    public function __construct(
        ColumnInterpreterService $interpreterService,
        EncryptionService $encryptionService
    ) {
        $this->interpreterService = $interpreterService;
        $this->encryptionService = $encryptionService;
    }

    /**
     * Interpret column definitions and generate migration
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function interpret(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'encryption_id' => 'required|string',
            'models' => 'required|array',
            'models.*.name' => 'required|string',
            'models.*.exists' => 'required|boolean',
            'models.*.existing_columns' => 'required_if:models.*.exists,true|array',
            'models.*.existing_columns.*' => 'string',
            'models.*.new_columns' => 'required|array',
            'models.*.new_columns.*' => 'required|array',
            'models.*.new_columns.*.type' => 'required|string',
            'models.*.new_columns.*.length' => 'nullable|integer',
            'models.*.new_columns.*.precision' => 'nullable|integer',
            'models.*.new_columns.*.scale' => 'nullable|integer',
            'models.*.new_columns.*.values' => 'nullable|array',
            'models.*.new_columns.*.nullable' => 'nullable|boolean',
            'models.*.new_columns.*.default' => 'nullable',
            'models.*.new_columns.*.unique' => 'nullable|boolean',
            'models.*.new_columns.*.index' => 'nullable|boolean',
            'models.*.new_columns.*.unsigned' => 'nullable|boolean',
            'models.*.new_columns.*.comment' => 'nullable|string',
            'models.*.drop_table' => 'required|boolean',
            'models.*.dump_data' => 'required|boolean',
            'models.*.download_data' => 'required|boolean',
            'models.*.download_format' => 'required_if:models.*.download_data,true|string|in:csv',
            'models.*.order' => 'nullable|integer|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        try {
            $results = $this->interpreterService->generateAndSaveMigrations($request->models, $request->encryption_id);

            if (!$results['success']) {
                return response()->json(['error' => $results['error']], 400);
            }

            $response = [
                'success' => true,
                'message' => 'Migration data generated and encrypted successfully',
                'encrypted_data' => $results['encrypted_data']
            ];

            return response()->json($response);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Preview the migration code for models
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function preview(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'encryption_id' => 'required|string',
            'models' => 'required|array',
            'models.*.name' => 'required|string',
            'models.*.exists' => 'required|boolean',
            'models.*.existing_columns' => 'required_if:models.*.exists,true|array',
            'models.*.existing_columns.*' => 'string',
            'models.*.new_columns' => 'required|array',
            'models.*.new_columns.*' => 'required|array',
            'models.*.new_columns.*.type' => 'required|string',
            'models.*.new_columns.*.length' => 'nullable|integer',
            'models.*.new_columns.*.precision' => 'nullable|integer',
            'models.*.new_columns.*.scale' => 'nullable|integer',
            'models.*.new_columns.*.values' => 'nullable|array',
            'models.*.new_columns.*.nullable' => 'nullable|boolean',
            'models.*.new_columns.*.default' => 'nullable',
            'models.*.new_columns.*.unique' => 'nullable|boolean',
            'models.*.new_columns.*.index' => 'nullable|boolean',
            'models.*.new_columns.*.unsigned' => 'nullable|boolean',
            'models.*.new_columns.*.comment' => 'nullable|string',
            'models.*.order' => 'nullable|integer|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        try {
            $previews = $this->interpreterService->generateModelMigrationsPreview($request->models, $request->encryption_id);
            
            return response()->json([
                'success' => true,
                'encrypted_previews' => $previews
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }
} 