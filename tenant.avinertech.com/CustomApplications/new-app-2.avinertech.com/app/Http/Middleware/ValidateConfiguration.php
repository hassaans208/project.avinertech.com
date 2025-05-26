<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ValidateConfiguration
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
        if ($request->isMethod('post')) {
            $validator = Validator::make($request->all(), [
                'configurations' => 'required|array',
                'configurations.*.key' => 'required|string|max:255',
                'configurations.*.value' => 'required|string',
                'configurations.*.description' => 'nullable|string|max:1000',
                'configurations.*.type' => 'required|string|max:255',
                'configurations.*.group' => 'nullable|string|max:255'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }
        }

        return $next($request);
    }
} 