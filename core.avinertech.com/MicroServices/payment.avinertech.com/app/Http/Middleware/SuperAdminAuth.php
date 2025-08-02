<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\User;

class SuperAdminAuth
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $token = $this->extractAccessToken($request);

        if (!$token) {
            return $this->redirectToLogin();
        }

        $user = User::where('api_token', $token)
                   ->where('is_active', true)
                   ->first();

        if (!$user || !$user->isSuperAdmin()) {
            return $this->redirectToLogin();
        }

        // Set the authenticated user for the request
        $request->setUserResolver(function () use ($user) {
            return $user;
        });

        return $next($request);
    }

    /**
     * Extract access token from request
     */
    private function extractAccessToken(Request $request): ?string
    {
        // First check Authorization header
        $authHeader = $request->header('Authorization');
        if ($authHeader && str_starts_with($authHeader, 'Bearer ')) {
            return substr($authHeader, 7);
        }

        // Fallback to query parameter
        return $request->query('access_token');
    }

    /**
     * Redirect to login page
     */
    private function redirectToLogin(): Response
    {
        if (request()->expectsJson()) {
            return response()->json([
                'success' => false,
                'error' => 'Super Admin access required'
            ], 403);
        }

        return redirect('/login');
    }
} 