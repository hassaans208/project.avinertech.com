<?php

namespace App\Services;

use App\Exceptions\SignatureVerificationException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class SignatureVerificationService
{
    public function __construct(
        private Http $httpClient
    ) {}

    public function verifyAndExtractTenant(string $signature): array
    {
        // Check cache first
        $cacheKey = 'signature_verification:' . hash('sha256', $signature);
        $cachedResult = Cache::get($cacheKey);
        
        // if ($cachedResult) {
        //     return $cachedResult['tenant_id'];
        // }
        
        try {
            // Call signal service to verify signature
            $response = Http::withHeaders([
                'X-APP-SIGNATURE' => $signature,
                'Content-Type' => 'application/json'
            ])->post('https://signal.avinertech.com/api/signature/verify');

            if ($response->status() === 404) {
                throw new SignatureVerificationException('Invalid signature or tenant blocked');
            }
            
            if ($response->status() !== 200) {
                throw new SignatureVerificationException('Signature verification failed');
            }
            
            $responseData = $response->json();
            $tenantId = $responseData['data']['tenant_id'] ?? '';
            $schemaName = $responseData['data']['schema_name'] ?? '';
            
            // Cache result for 1 hour
            Cache::put($cacheKey, [
                'tenant_id' => $tenantId,
                'schema_name' => $schemaName,
                'verified_at' => now()
            ], 3600);
            
            return [
                'tenant_id' => $tenantId,
                'schema_name' => $schemaName
            ];
            
        } catch (\Exception $e) {
            Log::error('Signature verification failed', [
                'signature' => substr($signature, 0, 20) . '...',
                'error' => $e->getMessage()
            ]);
            
            throw new SignatureVerificationException('Signature verification failed: ' . $e->getMessage());
        }
    }
    
    public function logSignatureVerification(string $operationId, string $signature, array $response): void
    {
        // Log signature verification for audit
        Log::info('Signature verification logged', [
            'operation_id' => $operationId,
            'signature_hash' => hash('sha256', $signature),
            'verification_response' => $response,
            'verified_at' => now()
        ]);
    }
}
