<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\ModelGeneration\Builders\ModelBuilder;
use App\Services\EncryptionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class ModelBuilderController extends Controller
{
    protected $encryptionService;

    public function __construct(EncryptionService $encryptionService)
    {
        $this->encryptionService = $encryptionService;
    }

    /**
     * Generate multiple models based on the provided definitions
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function generate(Request $request)
    {
        try {
            // Validate the request
            $validator = Validator::make($request->all(), [
                'models' => 'required|array|min:1',
                'models.*.name' => 'required|string|max:255',
                'models.*.namespace' => 'nullable|string|max:255',
                'models.*.table' => 'nullable|string|max:255',
                'models.*.traits' => 'nullable|array',
                'models.*.traits.*' => 'string',
                'models.*.uses' => 'nullable|array',
                'models.*.uses.*' => 'string',
                'models.*.fillable' => 'nullable|array',
                'models.*.fillable.*' => 'string',
                'models.*.guarded' => 'nullable|array',
                'models.*.guarded.*' => 'string',
                'models.*.hidden' => 'nullable|array',
                'models.*.hidden.*' => 'string',
                'models.*.appends' => 'nullable|array',
                'models.*.appends.*' => 'string',
                'models.*.casts' => 'nullable|array',
                'models.*.casts.*' => 'string',
                'models.*.relationships' => 'nullable|array',
                'models.*.relationships.*' => 'array',
                'models.*.relationships.*.name' => 'required|string',
                'models.*.relationships.*.type' => 'required|string|in:hasOne,hasMany,belongsTo,belongsToMany,morphTo,morphOne,morphMany,morphToMany',
                'models.*.relationships.*.related_model' => 'required|string',
                'models.*.relationships.*.foreign_key' => 'nullable|string',
                'models.*.relationships.*.local_key' => 'nullable|string',
                'models.*.methods' => 'nullable|array',
                'models.*.methods.*' => 'array',
                'models.*.methods.*.name' => 'required|string',
                'models.*.methods.*.parameters' => 'nullable|array',
                'models.*.methods.*.body' => 'required|string',
                'models.*.methods.*.visibility' => 'nullable|string|in:public,protected,private',
                'models.*.methods.*.return_type' => 'nullable|string',
                'models.*.validation_rules' => 'nullable|array',
                'models.*.validation_rules.*' => 'string',
                'encryption_id' => 'required|string|max:255'
            ]);

            if ($validator->fails()) {
                throw new ValidationException($validator);
            }

            $generatedModels = [];
            $errors = [];

            // Process each model
            foreach ($request->input('models') as $modelDefinition) {
                try {
                    $modelContent = $this->generateModel($modelDefinition);
                    $generatedModels[] = [
                        'model_name' => $modelDefinition['name'],
                        'namespace' => $modelDefinition['namespace'] ?? 'App\\Models',
                        'model_content' => $modelContent,
                        'file_name' => $modelDefinition['name'] . '.php'
                    ];
                } catch (\Exception $e) {
                    $errors[] = [
                        'model' => $modelDefinition['name'],
                        'error' => $e->getMessage()
                    ];
                }
            }

            // Encrypt the generated models
            $encryptedContent = $this->encryptionService->encrypt([
                    'models' => $generatedModels,
                    'timestamp' => now()->toIso8601String()
                ],
                $request->input('encryption_id')
            );

            return response()->json([
                'success' => true,
                'data' => [
                    'encrypted_content' => $encryptedContent,
                    'total_models' => count($generatedModels),
                    'failed_models' => count($errors),
                    'errors' => $errors
                ]
            ]);

        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Model generation failed',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Generate a single model from definition
     *
     * @param array $modelDefinition
     * @return string
     */
    protected function generateModel(array $modelDefinition): string
    {
        $builder = new ModelBuilder();

        // Set basic model properties
        $builder->setName($modelDefinition['name']);
        if (isset($modelDefinition['namespace'])) {
            $builder->setNamespace($modelDefinition['namespace']);
        }
        if (isset($modelDefinition['table'])) {
            $builder->addProperty('table', $modelDefinition['table']);
        }

        // Add traits
        if (isset($modelDefinition['traits'])) {
            foreach ($modelDefinition['traits'] as $trait) {
                $builder->addTrait($trait);
            }
        }

        // Add use statements
        if (isset($modelDefinition['uses'])) {
            foreach ($modelDefinition['uses'] as $use) {
                $builder->addUse($use);
            }
        }

        // Add fillable attributes
        if (isset($modelDefinition['fillable'])) {
            $builder->addFillable($modelDefinition['fillable']);
        }

        // Add guarded attributes
        if (isset($modelDefinition['guarded'])) {
            $builder->addGuarded($modelDefinition['guarded']);
        }

        // Add hidden attributes
        if (isset($modelDefinition['hidden'])) {
            $builder->addHidden($modelDefinition['hidden']);
        }

        // Add appends attributes
        if (isset($modelDefinition['appends'])) {
            $builder->addAppends($modelDefinition['appends']);
        }

        // Add casts
        if (isset($modelDefinition['casts'])) {
            $builder->addCasts($modelDefinition['casts']);
        }

        // Add relationships
        if (isset($modelDefinition['relationships'])) {
            foreach ($modelDefinition['relationships'] as $relationship) {
                $builder->addRelationship(
                    $relationship['name'],
                    $relationship['type'],
                    $relationship['related_model'],
                    $relationship['foreign_key'] ?? null,
                    $relationship['local_key'] ?? null
                );
            }
        }

        // Add custom methods
        if (isset($modelDefinition['methods'])) {
            foreach ($modelDefinition['methods'] as $method) {
                $builder->addMethod(
                    $method['name'],
                    $method['parameters'] ?? [],
                    $method['body'],
                    $method['visibility'] ?? 'public',
                    $method['return_type'] ?? 'void'
                );
            }
        }

        // Add validation rules
        if (isset($modelDefinition['validation_rules'])) {
            $builder->addValidationRules($modelDefinition['validation_rules']);
        }

        return $builder->build();
    }
} 