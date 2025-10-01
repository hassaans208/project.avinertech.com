<?php

namespace App\Http\Controllers;

use App\Http\Requests\RegisterApplicationRequest;
use App\Models\Package;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class RegisterApplicationController extends Controller
{
    /**
     * Register a new application with user, tenant, and package
     */
    public function register(RegisterApplicationRequest $request): JsonResponse
    {
        try {
            DB::beginTransaction();

            // Find or create the package
            $package = $this->findOrCreatePackage($request);

            // Create the user
            $user = $this->createUser($request);

            // Create the tenant
            $tenant = $this->createTenant($request);

            // Assign package to tenant
            $tenant->assignPackage($package);

            // Assign user to tenant as admin
            $user->assignToTenant($tenant, 'admin');

            // Generate API token for the user
            $apiToken = $user->generateApiToken();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Application registered successfully',
                'data' => [
                    'user' => [
                        'id' => $user->id,
                        'name' => $user->name,
                        'email' => $user->email,
                        'user_type' => $user->user_type,
                        'is_active' => $user->is_active,
                    ],
                    'tenant' => [
                        'id' => $tenant->id,
                        'name' => $tenant->name,
                        'host' => $tenant->host,
                        'status' => $tenant->status,
                    ],
                    'package' => [
                        'id' => $package->id,
                        'name' => $package->name,
                        'cost' => $package->cost,
                        'currency' => $package->currency,
                        'formatted_cost' => $package->formatted_cost,
                    ],
                    'api_token' => $apiToken,
                    'database_config' => [
                        'database_name' => $request->database_name,
                        'database_user' => $request->database_user,
                        'database_password' => $request->database_password ? '***' : null,
                    ],
                ],
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to register application',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Find existing package or create a new one
     */
    private function findOrCreatePackage(RegisterApplicationRequest $request): Package
    {
        $packageName = strtolower(str_replace(' ', '_', $request->package_name));
        
        // Try to find existing package by name
        $package = Package::where('name', $packageName)->first();
        
        if (!$package) {
            // Create new package if it doesn't exist
            $package = Package::create([
                'name' => $packageName,
                'cost' => $request->package_price_per_month,
                'currency' => 'USD',
                'tax_rate' => 0.0,
            ]);
        }
        
        return $package;
    }

    /**
     * Create a new user
     */
    private function createUser(RegisterApplicationRequest $request): User
    {
        return User::create([
            'name' => $request->username,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'user_type' => 'TENANT_ADMIN',
            'is_active' => true,
        ]);
    }

    /**
     * Create a new tenant
     */
    private function createTenant(RegisterApplicationRequest $request): Tenant
    {
        // Ensure host has the .avinertech.com suffix if not already present
        $host = $request->host;
        if (!str_ends_with($host, '.avinertech.com')) {
            $host = $host . '.avinertech.com';
        }

        return Tenant::create([
            'name' => $request->company_name,
            'host' => $host,
            'status' => 'active',
        ]);
    }

    /**
     * Get registration status for a specific email
     */
    public function getRegistrationStatus(string $email): JsonResponse
    {
        try {
            $user = User::where('email', $email)->first();
            
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'User not found',
                ], 404);
            }

            $tenant = $user->getActiveTenants()->first();
            $package = $tenant ? $tenant->getCurrentPackage() : null;

            return response()->json([
                'success' => true,
                'data' => [
                    'user' => [
                        'id' => $user->id,
                        'name' => $user->name,
                        'email' => $user->email,
                        'user_type' => $user->user_type,
                        'is_active' => $user->is_active,
                    ],
                    'tenant' => $tenant ? [
                        'id' => $tenant->id,
                        'name' => $tenant->name,
                        'host' => $tenant->host,
                        'status' => $tenant->status,
                    ] : null,
                    'package' => $package ? [
                        'id' => $package->id,
                        'name' => $package->name,
                        'cost' => $package->cost,
                        'currency' => $package->currency,
                        'formatted_cost' => $package->formatted_cost,
                    ] : null,
                ],
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to get registration status',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
