<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\RouteBuilder\RouteBuilderService;
use App\Services\EncryptionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class RouteBuilderController extends Controller
{
    protected $encryptionService;

    public function __construct(EncryptionService $encryptionService)
    {
        $this->encryptionService = $encryptionService;
    }

    /**
     * Generate routes based on the provided definitions
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function generate(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'routes' => 'required|array',
            'routes.*.type' => 'required|in:resource,custom,group',
            'routes.*.name' => 'required_if:routes.*.type,resource|string',
            'routes.*.controller' => 'required_if:routes.*.type,resource|string',
            'routes.*.methods' => 'required_if:routes.*.type,custom|array',
            'routes.*.uri' => 'required_if:routes.*.type,custom|string',
            'routes.*.action' => 'required_if:routes.*.type,custom|string',
            'routes.*.options' => 'nullable|array',
            'routes.*.attributes' => 'required_if:routes.*.type,group|array',
            'routes.*.routes' => 'required_if:routes.*.type,group|array',
            'middleware' => 'nullable|array',
            'prefix' => 'nullable|string',
            'name' => 'nullable|string',
            'namespace' => 'nullable|string',
            'encryption_id' => 'required|string'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $builder = new RouteBuilderService();

            // Set global route attributes
            if ($request->has('middleware')) {
                $builder->setMiddleware($request->middleware);
            }
            if ($request->has('prefix')) {
                $builder->setPrefix($request->prefix);
            }
            if ($request->has('name')) {
                $builder->setName($request->name);
            }
            if ($request->has('namespace')) {
                $builder->setNamespace($request->namespace);
            }

            // Process each route definition
            foreach ($request->routes as $route) {
                switch ($route['type']) {
                    case 'resource':
                        $builder->addResource(
                            $route['name'],
                            $route['controller'],
                            $route['options'] ?? []
                        );
                        break;

                    case 'custom':
                        $builder->addRoute(
                            $route['methods'],
                            $route['uri'],
                            $route['action'],
                            $route['options'] ?? []
                        );
                        break;

                    case 'group':
                        $builder->addGroup(
                            $route['attributes'],
                            function ($groupBuilder) use ($route) {
                                foreach ($route['routes'] as $groupRoute) {
                                    $this->processGroupRoute($groupBuilder, $groupRoute);
                                }
                            }
                        );
                        break;
                }
            }

            $result = $builder->generate();

            // Encrypt the generated routes data
            $encryptedData = $this->encryptionService->encrypt([
                'routes' => $result,
                'timestamp' => now()->format('Y-m-d H:i:s'),
                'total_routes' => count($result['routes'])
            ], $request->encryption_id);

            return response()->json([
                'success' => true,
                'encrypted_data' => $encryptedData
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Process a route within a group
     *
     * @param RouteBuilderService $builder
     * @param array $route
     * @return void
     */
    protected function processGroupRoute(RouteBuilderService $builder, array $route): void
    {
        switch ($route['type']) {
            case 'resource':
                $builder->addResource(
                    $route['name'],
                    $route['controller'],
                    $route['options'] ?? []
                );
                break;

            case 'custom':
                $builder->addRoute(
                    $route['methods'],
                    $route['uri'],
                    $route['action'],
                    $route['options'] ?? []
                );
                break;
        }
    }
} 