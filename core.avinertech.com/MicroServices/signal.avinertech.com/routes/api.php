<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SignalController;
use App\Http\Controllers\EncryptionController;
use App\Http\Controllers\PackageController;
use App\Http\Controllers\AuthController;

// Public routes (no authentication required)
Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);

// Encryption/Decryption utility routes (public)
Route::post('/encrypt', [EncryptionController::class, 'encrypt']);
Route::post('/decrypt', [EncryptionController::class, 'decrypt']);

// Package API routes (public)
Route::get('/packages', [PackageController::class, 'getPackages']);

// Protected routes (require authentication)
Route::middleware(['auth.api'])->group(function () {
    // Authentication routes
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', [AuthController::class, 'me']);
    Route::post('/refresh', [AuthController::class, 'refresh']);
    
    // Signal handling route (requires authentication)
    Route::post('{encryptedHostId}/signal', [SignalController::class, 'handle']);
});

// Health check route
Route::get('/up', function (){
    return response()->json([
        'status' => true,
        'message' => 'backend is up'
    ], 200);
});