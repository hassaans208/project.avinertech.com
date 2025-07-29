<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TenantController;
use App\Http\Controllers\PackageController;
use App\Http\Controllers\EncryptionController;
use App\Http\Controllers\LoginController;
use App\Http\Middleware\SuperAdminAuth;
use App\Http\Controllers\DeploymentController;

// Authentication Routes
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login'])->name('login.submit');
Route::get('/logout', [LoginController::class, 'logout'])->name('logout');

Route::middleware([SuperAdminAuth::class])->group(function () {
    // Dashboard - redirect root to tenants
    Route::get('/', function () {
        return redirect('/tenants');
    });
    
    // Encryption/Decryption utility view
    Route::get('/encryptor-decryptor', [EncryptionController::class, 'showUtility']);
    
    // Tenant Management Routes
    Route::get('/tenants', [TenantController::class, 'index'])->name('tenants.index');
    Route::get('/tenants/create', [TenantController::class, 'create'])->name('tenants.create');
    Route::post('/tenants', [TenantController::class, 'store'])->name('tenants.store');
    Route::get('/tenants/{id}', [TenantController::class, 'show'])->name('tenants.show');
    Route::get('/tenants/{id}/edit', [TenantController::class, 'edit'])->name('tenants.edit');
    Route::put('/tenants/{id}', [TenantController::class, 'update'])->name('tenants.update');
    
    // Tenant Status Actions
    Route::post('/tenants/{id}/block', [TenantController::class, 'block'])->name('tenants.block');
    Route::post('/tenants/{id}/unblock', [TenantController::class, 'unblock'])->name('tenants.unblock');
    Route::post('/tenants/{id}/change-status', [TenantController::class, 'changeStatus'])->name('tenants.change-status');

    // Package Management Routes
    Route::get('/packages', [PackageController::class, 'index'])->name('packages.index');
    Route::get('/packages/create', [PackageController::class, 'create'])->name('packages.create');
    Route::post('/packages', [PackageController::class, 'store'])->name('packages.store');
    Route::get('/packages/{id}', [PackageController::class, 'show'])->name('packages.show');
    Route::get('/packages/{id}/edit', [PackageController::class, 'edit'])->name('packages.edit');
    Route::put('/packages/{id}', [PackageController::class, 'update'])->name('packages.update');

    // Legacy routes for backward compatibility
    Route::post('/tenants/{tenant}/toggle-payment', [TenantController::class, 'togglePayment'])->name('tenants.toggle-payment');
    Route::post('/tenants/{tenant}/toggle-block', [TenantController::class, 'toggleBlock'])->name('tenants.toggle-block');
    Route::delete('/tenants/{tenant}', [TenantController::class, 'destroy'])->name('tenants.destroy');

    // Module Management Routes
    // Route::post('/api/modules', [DeploymentController::class, 'createModule'])->name('api.modules.create');
});

require __DIR__.'/auth.php';
