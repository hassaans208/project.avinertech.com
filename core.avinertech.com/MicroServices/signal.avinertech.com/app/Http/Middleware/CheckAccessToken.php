<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
// aGFzc2FhblNoYXJpcTI3OTAx
class CheckAccessToken
{
    public function handle(Request $request, Closure $next)
    {
        $host = $request->getHost();
        $date = date('Y:m:d');
        $token = 'hassaanShariq27901:'. $host.':'.$date;
        $accessToken = base64_encode($token);

        if(str_contains($request->getHost(), 'local')){
            return $next($request);
        }

        if ($request->has('access_token') && $request->access_token === $accessToken) {
            return $next($request);
        }

        return response()->json([
            'status' => false,
            'message' => 'backend is blocked'
        ], 403);
    }
} 