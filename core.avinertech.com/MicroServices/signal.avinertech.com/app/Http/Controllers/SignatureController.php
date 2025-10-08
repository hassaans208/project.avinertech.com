<?php

namespace App\Http\Controllers;

use App\Models\Tenant;
use App\Helpers\EncryptionHelper;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SignatureController extends Controller
{
    /**
     * Verify signature for a tenant
     * Gets signature from X-APP-SIGNATURE header, decrypts it to extract tenantId and checks if tenant is not blocked
     */
    public function verify(Request $request): JsonResponse
    {
        try {
            // Get signature from X-APP-SIGNATURE header
            $signature = $request->header('X-APP-SIGNATURE');
            
            if (!$signature) {
                return response()->json([
                    'success' => false,
                    'message' => 'X-APP-SIGNATURE header is required',
                    'error' => 'MISSING_SIGNATURE',
                ], 400);
            }
            
            // Decrypt the signature using EncryptionHelper
            $decryptedString = EncryptionHelper::decryptAlphaNumeric($signature);
            
            if ($decryptedString === false) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid signature format or decryption failed',
                    'error' => 'INVALID_SIGNATURE_FORMAT',
                ], 400);
            }

            // Parse the decrypted string to extract tenant data
            $decryptedData = $this->parseDecryptedData($decryptedString);
            
            if (!$decryptedData) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid decrypted data format',
                    'error' => 'INVALID_DECRYPTED_DATA',
                ], 400);
            }

            // Extract tenantId from decrypted data
            $tenantId = $decryptedData['tenant_id'] ?? null;
            
            if (!$tenantId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tenant ID not found in signature',
                    'error' => 'MISSING_TENANT_ID',
                ], 400);
            }

            // Find the tenant
            $tenant = Tenant::find($tenantId);
            
            if (!$tenant) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tenant not found',
                    'error' => 'TENANT_NOT_FOUND',
                    'data' => [
                        'tenant_id' => $tenantId,
                    ],
                ], 404);
            }

            // Check if tenant is blocked
            if ($tenant->isBlocked()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tenant is blocked',
                    'error' => 'TENANT_BLOCKED',
                    'data' => [
                        'tenant_id' => $tenantId,
                        'status' => $tenant->status,
                        'block_reason' => $tenant->block_reason,
                    ],
                ], 403);
            }

            // Check if tenant is inactive
            if (!$tenant->isActive()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tenant is inactive',
                    'error' => 'TENANT_INACTIVE',
                    'data' => [
                        'tenant_id' => $tenantId,
                        'status' => $tenant->status,
                    ],
                ], 403);
            }

            // Signature is valid and tenant is active
            return response()->json([
                'success' => true,
                'message' => 'Signature verified successfully',
                'data' => [
                    'tenant_id' => $tenantId,
                    'tenant_name' => $tenant->name,
                    'tenant_host' => $tenant->host,
                    'tenant_status' => $tenant->status,
                    'signature_valid' => true,
                    'verified_at' => now()->toISOString(),
                    'decrypted_data' => $decryptedData, // Include decrypted data for debugging
                ],
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to verify signature',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Parse the decrypted string to extract tenant data
     * The decrypted string should contain tenant information in a parseable format
     */
    private function parseDecryptedData(string $decryptedString): ?array
    {
        try {
            // Try to parse as JSON first
            $data = json_decode($decryptedString, true);
            if (json_last_error() === JSON_ERROR_NONE && isset($data['tenant_id'])) {
                return $data;
            }
            
            // Try to parse as colon-separated format: "tenant_id:timestamp:user_id:email:package_id"
            if (strpos($decryptedString, ':') !== false) {
                $parts = explode(':', $decryptedString);
                if (count($parts) >= 1 && is_numeric($parts[0])) {
                    return [
                        'tenant_id' => (int) $parts[0],
                        'timestamp' => $parts[1] ?? null,
                        'user_id' => $parts[2] ?? null,
                        'email' => $parts[3] ?? null,
                        'package_id' => $parts[4] ?? null,
                    ];
                }
            }
            
            // Try to parse as pipe-separated format: "tenant_id|timestamp|user_id|email|package_id"
            if (strpos($decryptedString, '|') !== false) {
                $parts = explode('|', $decryptedString);
                if (count($parts) >= 1 && is_numeric($parts[0])) {
                    return [
                        'tenant_id' => (int) $parts[0],
                        'timestamp' => $parts[1] ?? null,
                        'user_id' => $parts[2] ?? null,
                        'email' => $parts[3] ?? null,
                        'package_id' => $parts[4] ?? null,
                    ];
                }
            }
            
            // If none of the above formats work, return null
            return null;
            
        } catch (\Exception $e) {
            // Log the error if needed
            \Log::error('Decrypted data parsing failed: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Get signature verification status for a tenant
     */
    public function status(Request $request): JsonResponse
    {
        try {
            $tenantId = $request->query('tenantId');
            
            if (!$tenantId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tenant ID is required',
                    'error' => 'MISSING_TENANT_ID',
                ], 400);
            }

            $tenant = Tenant::find($tenantId);
            
            if (!$tenant) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tenant not found',
                    'error' => 'TENANT_NOT_FOUND',
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'tenant_id' => $tenant->id,
                    'tenant_name' => $tenant->name,
                    'tenant_host' => $tenant->host,
                    'tenant_status' => $tenant->status,
                    'is_active' => $tenant->isActive(),
                    'is_blocked' => $tenant->isBlocked(),
                    'block_reason' => $tenant->block_reason,
                    'can_verify_signature' => $tenant->isActive() && !$tenant->isBlocked(),
                ],
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to get signature status',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
