<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SignalController;
use App\Http\Controllers\EncryptionController;
use App\Http\Controllers\PackageController;
use App\Http\Controllers\ServiceModuleController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\RegisterApplicationController;
use App\Http\Controllers\SignatureController;

// Public routes (no authentication required)
Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);

// Application registration routes (public)
Route::post('/register-application', [RegisterApplicationController::class, 'register']);
Route::get('/registration-status/{email}', [RegisterApplicationController::class, 'getRegistrationStatus']);

// Signature verification routes (public)
Route::post('/signature/verify', [SignatureController::class, 'verify']);
Route::get('/signature/status', [SignatureController::class, 'status']);

// Encryption/Decryption utility routes (public)
Route::post('/encrypt', [EncryptionController::class, 'encrypt']);
Route::post('/decrypt', [EncryptionController::class, 'decrypt']);

// Package API routes (public)
Route::get('/packages', [PackageController::class, 'getPackages']);

// Service Module API routes (public)
Route::get('/service-modules', [ServiceModuleController::class, 'index']);
Route::get('/service-modules/{id}', [ServiceModuleController::class, 'show']);

// Protected routes (require authentication)
Route::middleware(['auth.api'])->group(function () {
    // Authentication routes
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', [AuthController::class, 'me']);
    Route::post('/refresh', [AuthController::class, 'refresh']);
    
    // Signal handling route (requires authentication)
    Route::post('{encryptedHostId}/signal', [SignalController::class, 'handle']);
    
    // Service Module management routes (protected)
    Route::post('/service-modules', [ServiceModuleController::class, 'store']);
    Route::put('/service-modules/{id}', [ServiceModuleController::class, 'update']);
    Route::delete('/service-modules/{id}', [ServiceModuleController::class, 'destroy']);
});

// Health check route
Route::get('/up', function (){
    return response()->json([
        'status' => true,
        'message' => 'backend is up'
    ], 200);
});