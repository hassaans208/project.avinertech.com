<?php

use App\Http\Controllers\ManagerController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TenantController;
use App\Http\Controllers\TenantSiteController;
use App\Http\Controllers\DeploymentController;
use App\Http\Middleware\CheckAccessToken;

Route::post('/manage-module', [ManagerController::class, 'createApplication']);
Route::get('/up', function (){
    return response()->json([
        'status' => true,
        'message' => 'backend is up'
    ], 200);
});

// Tenant Site API - No access token required
Route::get('/tenant/{tenant_id}', [TenantSiteController::class, 'getTenantInfo']);
Route::get('/tenant/{tenant_id}/download/{format?}', [TenantSiteController::class, 'downloadTenantInfo']);

// Deployment Routes
Route::prefix('deployment')->group(function () {
    Route::post('/{action}', [App\Http\Controllers\DeploymentController::class, 'handleAction']);
});

Route::post('/api/modules', [DeploymentController::class, 'createModule'])->name('api.modules.create');

// Main routes protected by access token
Route::middleware([CheckAccessToken::class])->group(function () {
    // Dashboard
    Route::get('/', [TenantController::class, 'index']);
    
    // Tenant CRUD Routes
    Route::get('/tenants', [TenantController::class, 'index']);
    Route::get('/tenants/host/{host}', [TenantController::class, 'index']);
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