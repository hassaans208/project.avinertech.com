<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\ViewBuilder\ViewBuilderService;
use App\Services\EncryptionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ViewBuilderController extends Controller
{
    protected $encryptionService;

    public function __construct(EncryptionService $encryptionService)
    {
        $this->encryptionService = $encryptionService;
    }

    /**
     * Generate views for multiple models
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function generate(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'views' => 'required|array',
            'views.*.model_name' => 'required|string',
            'views.*.fields' => 'required|array',
            'views.*.fields.*.name' => 'required|string',
            'views.*.fields.*.type' => 'nullable|string|in:text,textarea,select,checkbox,date,datetime,boolean',
            'views.*.fields.*.required' => 'nullable|boolean',
            'views.*.fields.*.hidden' => 'nullable|boolean',
            'views.*.fields.*.options' => 'nullable|array',
            'views.*.fields.*.options.*.value' => 'required|string',
            'views.*.fields.*.options.*.label' => 'required|string',
            'views.*.view_path' => 'nullable|string',
            'views.*.layout' => 'nullable|string',
            'views.*.title' => 'nullable|string',
            'views.*.description' => 'nullable|string',
            'views.*.actions' => 'nullable|array',
            'views.*.actions.*.name' => 'required|string',
            'views.*.actions.*.label' => 'nullable|string',
            'views.*.actions.*.route' => 'nullable|string',
            'views.*.actions.*.method' => 'nullable|string|in:get,post,put,patch,delete',
            'views.*.actions.*.class' => 'nullable|string',
            'views.*.relationships' => 'nullable|array',
            'views.*.relationships.*.name' => 'required|string',
            'views.*.relationships.*.type' => 'nullable|string|in:hasOne,hasMany,belongsTo,belongsToMany',
            'views.*.relationships.*.model' => 'required|string',
            'encryption_id' => 'required|string'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $generatedViews = [];
            $errors = [];

            foreach ($request->input('views') as $viewData) {
                try {
                    $viewBuilder = new ViewBuilderService(
                        $viewData['model_name'],
                        $viewData['fields'],
                        $viewData['view_path'] ?? null,
                        $viewData['layout'] ?? 'layouts.app'
                    );

                    if (isset($viewData['title'])) {
                        $viewBuilder->setTitle($viewData['title']);
                    }

                    if (isset($viewData['description'])) {
                        $viewBuilder->setDescription($viewData['description']);
                    }

                    if (isset($viewData['actions'])) {
                        $viewBuilder->setActions($viewData['actions']);
                    }

                    if (isset($viewData['relationships'])) {
                        $viewBuilder->setRelationships($viewData['relationships']);
                    }

                    $views = $viewBuilder->generate();
                    $generatedViews[$viewData['model_name']] = $views;
                } catch (\Exception $e) {
                    $errors[$viewData['model_name']] = $e->getMessage();
                }
            }

            if (empty($generatedViews)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to generate any views',
                    'errors' => $errors
                ], 500);
            }

            // Encrypt the generated views
            $encryptedContent = $this->encryptionService->encrypt(
                $generatedViews,
                $request->input('encryption_id')
            );

            return response()->json([
                'success' => true,
                'data' => [
                    'encrypted_content' => $encryptedContent,
                    'encryption_id' => $request->input('encryption_id'),
                    'errors' => $errors
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to generate views',
                'error' => $e->getMessage()
            ], 500);
        }
    }
} 