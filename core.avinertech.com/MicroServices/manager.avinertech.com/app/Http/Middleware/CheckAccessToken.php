<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
// aGFzc2FhblNoYXJpcTI3OTAx
class CheckAccessToken
{
    public function handle(Request $request, Closure $next)
    {
        $accessToken = base64_encode('hassaanShariq27901');
        
        if ($request->has('token') && $request->token === $accessToken) {
            return $next($request);
        }

        return response()->json([
            'status' => false,
            'message' => 'backend is blocked'
        ], 403);
    }
} 