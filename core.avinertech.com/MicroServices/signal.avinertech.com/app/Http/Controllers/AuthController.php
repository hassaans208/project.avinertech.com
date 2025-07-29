<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    /**
     * Login user and generate API token
     */
    public function login(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|string|min:6',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'error' => 'Invalid input',
                'errors' => $validator->errors()
            ], 400);
        }

        $user = User::where('email', $request->email)->first();

        if($user->user_type == 'SUPER_ADMIN') {
            return response()->json([
                'success' => false,
                'error' => 'Login Failed'
            ], 403);
        }

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'success' => false,
                'error' => 'Invalid credentials'
            ], 401);
        }

        if (!$user->isActive()) {
            return response()->json([
                'success' => false,
                'error' => 'Account is inactive'
            ], 403);
        }

        $token = $user->generateApiToken();

        return response()->json([
            'success' => true,
            'access_token' => encryptAlphaNumeric($token),
            'message' => 'Login successful'
        ]);
    }

    /**
     * Logout user and revoke API token
     */
    public function logout(Request $request): JsonResponse
    {
        $user = $request->user();
        
        if ($user) {
            $user->revokeApiToken();
        }

        return response()->json([
            'success' => true,
            'message' => 'Successfully logged out'
        ]);
    }

    /**
     * Get authenticated user information
     */
    public function me(Request $request): JsonResponse
    {
        $user = $request->user();

        return response()->json([
            'success' => true,
            'data' => [
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'user_type' => $user->user_type,
                    'is_active' => $user->is_active,
                    'created_at' => $user->created_at->toISOString(),
                ],
                'tenants' => $user->getActiveTenants()->map(function ($tenant) {
                    return [
                        'id' => $tenant->id,
                        'name' => $tenant->name,
                        'host' => $tenant->host,
                        'role' => $tenant->pivot->role,
                        'status' => $tenant->status,
                    ];
                }),
            ]
        ]);
    }

    /**
     * Register new user (only for tenant admins)
     */
    public function register(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6|confirmed',
            'tenant_ids' => 'required|array',
            'tenant_ids.*' => 'exists:tenants,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'error' => 'Invalid input',
                'errors' => $validator->errors()
            ], 400);
        }

        try {
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'user_type' => 'TENANT_ADMIN',
                'is_active' => true,
            ]);

            // Assign user to tenants
            foreach ($request->tenant_ids as $tenantId) {
                $tenant = \App\Models\Tenant::find($tenantId);
                if ($tenant) {
                    $user->assignToTenant($tenant, 'admin');
                }
            }

            return response()->json([
                'success' => true,
                'access_token' => encryptAlphaNumeric($user->api_token),
                'message' => 'User registered successfully'
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Registration failed: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Refresh API token
     */
    public function refresh(Request $request): JsonResponse
    {
        $user = $request->user();
        $newToken = $user->generateApiToken();

        return response()->json([
            'success' => true,
            'data' => [
                'token' => $newToken,
                'token_type' => 'Bearer',
            ],
            'message' => 'Token refreshed successfully'
        ]);
    }
} 