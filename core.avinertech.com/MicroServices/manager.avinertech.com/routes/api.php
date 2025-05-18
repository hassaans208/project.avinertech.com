<?php

use App\Http\Controllers\ManagerController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TenantController;
use App\Http\Controllers\DeploymentController;
use App\Http\Middleware\CheckAccessToken;

Route::post('/manage-module', [ManagerController::class, 'createApplication']);
Route::get('/up', function (){
    return response()->json([
        'status' => true,
        'message' => 'backend is up'
    ], 200);
});
// Deployment Routes
Route::prefix('deployment')->group(function () {
    // Route::post('/create-tenant', [App\Http\Controllers\DeploymentController::class, 'createTenant']);
    // Route::post('/create-module', [App\Http\Controllers\DeploymentController::class, 'createModule']);
    // Route::post('/deploy-module', [App\Http\Controllers\DeploymentController::class, 'deployModule']);
    // Route::post('/ssl-cert', [App\Http\Controllers\DeploymentController::class, 'sslCert']);
    // Route::post('/create-database', [App\Http\Controllers\DeploymentController::class, 'createDatabase']);
    Route::post('/{action}', [App\Http\Controllers\DeploymentController::class, 'handleAction']);
});

Route::post('/api/modules', [DeploymentController::class, 'createModule'])->name('api.modules.create');

// Main routes protected by access token
Route::middleware([CheckAccessToken::class])->group(function () {
    // Dashboard
    Route::get('/', [TenantController::class, 'index']);
    
    // Tenant CRUD Routes
    Route::get('/tenants', [TenantController::class, 'index']);
    Route::get('/tenants/create', [TenantController::class, 'create']);
    Route::post('/tenants', [TenantController::class, 'store']);
    Route::get('/tenants/{tenant}/edit', [TenantController::class, 'edit']);
    Route::put('/tenants/{tenant}', [TenantController::class, 'update']);
    Route::delete('/tenants/{tenant}', [TenantController::class, 'destroy']);
    
    // Tenant Status Actions
    Route::post('/tenants/{tenant}/toggle-payment', [TenantController::class, 'togglePayment']);
    Route::post('/tenants/{tenant}/toggle-block', [TenantController::class, 'toggleBlock']);
    
    // Deployment Actions
    Route::post('/tenants/{tenant}/create-module', [DeploymentController::class, 'createModule']);
    Route::post('/tenants/{tenant}/deploy-module', [DeploymentController::class, 'deployModule']);
    Route::post('/tenants/{tenant}/ssl-cert', [DeploymentController::class, 'sslCert']);
    Route::post('/tenants/{tenant}/create-database', [DeploymentController::class, 'createDatabase']);
});