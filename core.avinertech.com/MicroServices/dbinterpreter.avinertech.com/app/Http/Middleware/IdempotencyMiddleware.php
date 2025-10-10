<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class IdempotencyMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        // Only apply to mutating operations
        if (!in_array($request->method(), ['POST', 'PUT', 'PATCH', 'DELETE'])) {
            return $next($request);
        }

        $idempotencyKey = $request->header('Idempotency-Key');
        
        if (!$idempotencyKey) {
            return $next($request);
        }

        $cacheKey = 'idempotency:' . hash('sha256', $idempotencyKey);
        
        // Check if we've already processed this request
        $cachedResponse = Cache::get($cacheKey);
        
        if ($cachedResponse) {
            return response()->json($cachedResponse['data'], $cachedResponse['status'])
                ->header('X-Idempotent', 'true');
        }

        // Process the request
        $response = $next($request);
        
        // Cache successful responses
        if ($response->getStatusCode() >= 200 && $response->getStatusCode() < 300) {
            Cache::put($cacheKey, [
                'data' => $response->getData(true),
                'status' => $response->getStatusCode()
            ], 3600); // Cache for 1 hour
        }

        return $response;
    }
}
