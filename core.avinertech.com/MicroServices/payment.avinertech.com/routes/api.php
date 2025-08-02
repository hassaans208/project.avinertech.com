<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SignalController;
use App\Http\Controllers\EncryptionController;
use App\Http\Controllers\PackageController;
use App\Http\Controllers\ServiceModuleController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PaymentRouterController;

// Public routes (no authentication required)
Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);

// Protected routes (require authentication)
Route::middleware(['auth.api'])->group(function () {
    // Authentication routes
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', [AuthController::class, 'me']);
    Route::post('/refresh', [AuthController::class, 'refresh']);
});

// Payment routes with signature verification
Route::middleware(['throttle:60,1', 'signature.verify'])->group(function () {
    // Dynamic payment processing route - handles any payment method
    Route::post('/{method}/payment', [PaymentRouterController::class, 'handle'])
        ->where('method', '[a-z_]+');
    
    // Payment verification
    Route::get('/payment/verify/{transactionId}', [PaymentRouterController::class, 'verify'])
        ->where('transactionId', '[A-Za-z0-9_-]+');
    
    // Payment refund
    Route::post('/payment/refund/{transactionId}', [PaymentRouterController::class, 'refund'])
        ->where('transactionId', '[0-9]+');
});

// Health check route
Route::get('/up', function (){
    return response()->json([
        'status' => true,
        'message' => 'backend is up'
    ], 200);
});

// Payment system health check
Route::get('/healthz', function () {
    return response()->json([
        'status' => 'healthy',
        'service' => 'payment.avinertech.com',
        'timestamp' => now()->toISOString(),
        'version' => '1.0.0'
    ], 200);
});