<?php

namespace App\Http\Middleware;

use App\Exceptions\SignatureInvalidException;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class VerifySignature
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
    {
        $signature = $request->header('X-APP-SIGNATURE');
        
        if (!$signature) {
            throw new SignatureInvalidException('Missing X-APP-SIGNATURE header');
        }

        // Check cache first
        $cacheKey = 'signature_verified_' . md5($signature);
        $cachedPayload = Cache::get($cacheKey);
        
        if ($cachedPayload) {
            // Add the decrypted payload to the request
            $request->merge(['decrypted_payload' => $cachedPayload]);
            return $next($request);
        }

        try {
            // Call signal.avinertech.com for verification
            $response = Http::timeout(10)
                ->post('https://signal.avinertech.com/api/signal/verify', [
                    'signature' => $signature
                ]);

            if (!$response->successful()) {
                Log::warning('Signature verification failed', [
                    'signature' => substr($signature, 0, 20) . '...',
                    'response_status' => $response->status(),
                    'response_body' => $response->body()
                ]);
                
                throw new SignatureInvalidException('Signature verification failed');
            }

            $verificationData = $response->json();
            
            if (!$verificationData['success'] ?? false) {
                throw new SignatureInvalidException($verificationData['message'] ?? 'Invalid signature');
            }

            $decryptedPayload = $verificationData['decrypted_payload'] ?? null;
            
            if (!$decryptedPayload) {
                throw new SignatureInvalidException('No decrypted payload received');
            }

            // Cache the verified payload for 5 minutes
            Cache::put($cacheKey, $decryptedPayload, 300);
            
            // Add the decrypted payload to the request
            $request->merge(['decrypted_payload' => $decryptedPayload]);
            
            Log::info('Signature verified successfully', [
                'signature' => substr($signature, 0, 20) . '...',
                'payload_keys' => array_keys($decryptedPayload)
            ]);

            return $next($request);

        } catch (\Exception $e) {
            Log::error('Signature verification error', [
                'signature' => substr($signature, 0, 20) . '...',
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            if ($e instanceof SignatureInvalidException) {
                throw $e;
            }
            
            throw new SignatureInvalidException('Signature verification service unavailable');
        }
    }
} 