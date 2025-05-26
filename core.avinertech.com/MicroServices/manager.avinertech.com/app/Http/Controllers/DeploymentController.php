<?php

namespace App\Http\Controllers;

use App\Services\SshService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use App\Models\Tenant;

class DeploymentController extends Controller
{
    protected $sshService;

    public function __construct(SshService $sshService)
    {
        $this->sshService = $sshService;
    }

    /**
     * Handle deployment actions
     *
     * @param Request $request
     * @param string $action
     * @return \Illuminate\Http\JsonResponse
     */
    public function handleAction(Request $request, string $action)
    {
        try {
            $validationRules = [
                'create-tenant' => [
                    "id" => 'nullable|integer|exists:main.Tenant,id',
                    'host' => 'required|string|max:255',
                    'username' => 'required|string|max:255',
                    'company_name' => 'required|string|max:255',
                    'address' => 'required|string|max:255',
                    'phone' => 'required|string|max:255',
                    'email' => 'required|string|max:255',
                    'password' => 'required|string|max:255',
                    'port' => 'integer|nullable',
                    'database_host' => 'string|max:255|nullable',
                    'database_port' => 'integer|nullable',
                    'database_name' => 'required|string|max:255',
                    'database_user' => 'required|string|max:255',
                    'database_password' => 'required|string|max:255',
                ],
                'create-module' => [
                    'module' => 'required|in:core,tenant',
                    'submodule' => 'required|string', 
                    'tenant_id' => 'required',
                ],
                'deploy-module' => [
                    'module' => 'required|in:core,tenant',
                    'submodule' => 'required|string', 
                    'tenant_id' => 'required',
                ],
                'ssl-cert' => [
                    'tenant_id' => 'required',
                ],
                'get-log' => [
                    'app_domain' => 'required|string|regex:/^[a-z0-9-]+\.[a-z0-9-.]+$/',
                ],
                'create-tenant-db' => [
                    'tenant_id' => 'required',
                ],
            ];

            $validationMessages = [
                'create-module' => [
                    'module.in' => 'Module must be either "core" or "tenant"',
                    'app_name.regex' => 'App name can only contain lowercase letters, numbers, and hyphens',
                ],
                'create-tenant-db' => [
                    'database_name.regex' => 'Database name can only contain letters, numbers, and underscores',
                    'database_user.regex' => 'Database user can only contain letters, numbers, and underscores',
                    'database_password.min' => 'Database password must be at least 8 characters long',
                ],
            ];

            if (!isset($validationRules[$action])) {
                return response()->json([
                    'status' => false,
                    'message' => 'Invalid action specified'
                ], 400);
            }

            $validator = Validator::make($request->all(), 
                $validationRules[$action],
                $validationMessages[$action] ?? []
            );

            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            $result = match($action) {
                'create-module' => $this->sshService->createModule(
                    $request->module,
                    $request->submodule,
                    $request->tenant_id
                ),
                'deploy-module' => $this->sshService->deployModule(
                    $request->module,
                    $request->submodule,
                    $request->tenant_id
                ),
                'ssl-cert' => $this->sshService->sslCert(
                    $request->tenant_id
                ),
                'get-log' => $this->sshService->getLog(
                    $request->app_domain
                ),
                'create-tenant-db' => $this->sshService->createDatabase(
                    $request->tenant_id
                ),
                'create-tenant' => $this->sshService->createTenant($request->all()) 
            };

            $responseData = match($action) {
                'create-module' => [
                    'data' => $result
                 ],
                'deploy-module' => [
                    'data' => $result
                ],
                'ssl-cert' => [
                    'data' => $result
                ],
                'get-log' => [
                    'data' => $result
                ],
                'create-tenant' => [
                    'data' => $result,
                ],
                default => $result
            };

            return response()->json([
                'status' => true,
                'message' => ucfirst(preg_replace('/([A-Z])/', ' $1', $action)) . ' completed successfully',
                'server_response' => $responseData
            ]);

        } catch (\Exception $e) {
            Log::error(ucfirst($action) . ' failed', [
                'error' => $e->getMessage(),
                'request' => $request->all()
            ]);

            return response()->json([
                'status' => false,
                'message' => 'Failed to ' . strtolower(preg_replace('/([A-Z])/', ' $1', $action)),
                'error' => $e->getMessage()
            ], 500);
        }
    }
}