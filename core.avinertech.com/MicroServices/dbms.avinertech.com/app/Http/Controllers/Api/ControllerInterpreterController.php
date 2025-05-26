<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\ControllerInterpreter\ControllerInterpreterService;
use App\Services\EncryptionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ControllerInterpreterController extends Controller
{
    protected $encryptionService;

    public function __construct(EncryptionService $encryptionService)
    {
        $this->encryptionService = $encryptionService;
    }

    /**
     * Generate controllers based on the provided definitions
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function generate(Request $request)
    {
        // dd($request->all());
        $validator = Validator::make($request->all(), [
            'controllers' => 'required|array',
            'controllers.*.name' => 'required|string',
            'controllers.*.namespace' => 'nullable|string',
            'controllers.*.controller_namespace' => 'nullable|string',
            'controllers.*.validation_rules' => 'nullable|array',
            'controllers.*.methods' => 'nullable|array',
            'controllers.*.traits' => 'nullable|array',
            'controllers.*.uses' => 'nullable|array',
            'encryption_id' => 'required|string'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $generatedControllers = [];
        $errors = [];

        foreach ($request->controllers as $controller) {
            try {
                $interpreter = new ControllerInterpreterService(
                    $controller['name'],
                    $controller['namespace'] ?? 'App\\Models',
                    $controller['controller_namespace'] ?? 'App\\Http\\Controllers'
                );

                // Set validation rules if provided
                if (isset($controller['validation_rules'])) {
                    $interpreter->setValidationRules($controller['validation_rules']);
                }

                // Set custom methods if provided
                if (isset($controller['methods'])) {
                    $interpreter->setMethods($controller['methods']);
                }

                // Set traits if provided
                if (isset($controller['traits'])) {
                    $interpreter->setTraits($controller['traits']);
                }

                // Set use statements if provided
                if (isset($controller['uses'])) {
                    $interpreter->setUses($controller['uses']);
                }

                $generatedControllers[] = [
                    'controller_name' => $controller['name'],
                    'content' => $interpreter->generate()
                ];
            } catch (\Exception $e) {
                $errors[] = [
                    'controller' => $controller['name'],
                    'error' => $e->getMessage()
                ];
            }
        }

        // Encrypt the generated controllers data
        $encryptedData = $this->encryptionService->encrypt([
            'controllers' => $generatedControllers,
            'timestamp' => now()->format('Y-m-d H:i:s'),
            'total_controllers' => count($generatedControllers)
        ], $request->encryption_id);

        return response()->json([
            'success' => true,
            'encrypted_data' => $encryptedData,
            'errors' => $errors
        ]);
    }
}