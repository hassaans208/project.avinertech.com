<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\UserController;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\Api\{SchemaController, ConfigurationController};
use App\Http\Middleware\ValidateConfiguration;

// Authentication Routes (Public)
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// Protected Routes
Route::middleware('auth:sanctum')->group(function () {
    // Auth routes
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', [AuthController::class, 'user']);
    
    // User management routes
    Route::get('/users', [UserController::class, 'index']);
    Route::post('/users', [UserController::class, 'store']);
    Route::get('/users/{user}', [UserController::class, 'show']);
    Route::put('/users/{user}', [UserController::class, 'update']);
    Route::delete('/users/{user}', [UserController::class, 'destroy']);
});

// User API routes - unprotected for development
// Keep these routes during development if needed, but comment them out or remove for production
Route::get('/dev/users', [UserController::class, 'index']);
Route::post('/dev/users', [UserController::class, 'store']);
Route::get('/dev/users/{user}', [UserController::class, 'show']);
Route::put('/dev/users/{user}', [UserController::class, 'update']);
Route::delete('/dev/users/{user}', [UserController::class, 'destroy']); 

// Schema Management Routes
Route::prefix('schema')->group(function () {
    Route::get('/', [SchemaController::class, 'getSchemas']);
    Route::post('/', [SchemaController::class, 'storeSchema']);
}); 

// Configuration Management Routes
Route::prefix('configuration')->group(function () {
    // Cache management
    Route::post('/cache/clear', [ConfigurationController::class, 'clearCache']);
    
    // Database connection test
    Route::post('/test-connection', [ConfigurationController::class, 'testDatabaseConnection']);
    
    // Configuration management
    Route::get('/{type}', [ConfigurationController::class, 'getConfigurations']);
    Route::post('/{type}', [ConfigurationController::class, 'storeConfiguration']);
}); 

