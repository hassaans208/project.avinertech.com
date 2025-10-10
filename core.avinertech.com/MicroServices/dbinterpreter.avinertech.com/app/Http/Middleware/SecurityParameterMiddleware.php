<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class SecurityParameterMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        // Only apply to database API routes
        if (!$request->is('api/v1/database/*')) {
            return $next($request);
        }

        // Check for prohibited parameters in request data
        $prohibitedParams = ['tenant_id', 'schema_name'];
        
        foreach ($prohibitedParams as $param) {
            if ($request->has($param)) {
                Log::warning('Security violation: Prohibited parameter detected', [
                    'parameter' => $param,
                    'value' => $request->get($param),
                    'ip' => $request->ip(),
                    'user_agent' => $request->userAgent(),
                    'url' => $request->fullUrl()
                ]);
                
                return response()->json([
                    'status' => 'error',
                    'message' => 'Security violation: Prohibited parameter detected',
                    'error' => [
                        'code' => 'SECURITY_VIOLATION',
                        'details' => "Parameter '{$param}' is not allowed in requests. This information is provided by signature verification."
                    ]
                ], 403);
            }
        }

        return $next($request);
    }
}
