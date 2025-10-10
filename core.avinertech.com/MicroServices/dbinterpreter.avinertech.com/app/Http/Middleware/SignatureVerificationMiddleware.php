<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Services\SignatureVerificationService;
use App\Exceptions\SignatureVerificationException;

class SignatureVerificationMiddleware
{
    public function __construct(
        private SignatureVerificationService $signatureService
    ) {}

    public function handle(Request $request, Closure $next)
    {
        // Only apply to database API routes
        if (!$request->is('api/v1/database/*')) {
            return $next($request);
        }

        $signature = $request->header('X-APP-SIGNATURE');
        
        if (!$signature) {
            return response()->json([
                'status' => 'error',
                'message' => __('messages.signature_required'),
                'error' => [
                    'code' => 'SIGNATURE_REQUIRED',
                    'details' => 'X-APP-SIGNATURE header is required'
                ]
            ], 401);
        }

        try {
            // Verify signature and extract tenant and schema
            $verificationResult = $this->signatureService->verifyAndExtractTenant($signature);
            
            // Add tenant and schema to request
            $request->merge([
                'tenant_id' => $verificationResult['tenant_id'],
                'schema_name' => $verificationResult['schema_name']
            ]);
            
        } catch (SignatureVerificationException $e) {
            return response()->json([
                'status' => 'error',
                'message' => __('messages.signature_verification_failed'),
                'error' => [
                    'code' => 'SIGNATURE_VERIFICATION_FAILED',
                    'details' => $e->getMessage()
                ]
            ], 401);
        }

        return $next($request);
    }
}
