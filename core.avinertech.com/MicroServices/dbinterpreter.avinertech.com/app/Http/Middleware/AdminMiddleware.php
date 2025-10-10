<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class AdminMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $user = $request->user();
        
        if (!$user || !$user->is_admin) {
            return response()->json([
                'status' => 'error',
                'message' => __('messages.admin_access_required'),
                'error' => [
                    'code' => 'ADMIN_ACCESS_REQUIRED',
                    'details' => 'Admin privileges required for this operation'
                ]
            ], 403);
        }

        return $next($request);
    }
}
