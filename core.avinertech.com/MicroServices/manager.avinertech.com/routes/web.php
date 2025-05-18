<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TenantController;
use App\Http\Middleware\CheckAccessToken;
use App\Http\Controllers\DeploymentController;

// // Authentication Routes
// Route::get('/', function () {
//     return redirect()->route('login');
// });

Route::middleware([CheckAccessToken::class])->group(function () {
    // Tenant Management Routes
    Route::get('/', [TenantController::class, 'index']);
    Route::get('/tenants', [TenantController::class, 'index'])->name('tenants.index');
    Route::get('/tenants/create', [TenantController::class, 'create'])->name('tenants.create');
    Route::post('/tenants', [TenantController::class, 'store'])->name('tenants.store');
    Route::get('/tenants/{tenant}/edit', [TenantController::class, 'edit'])->name('tenants.edit');
    Route::put('/tenants/{tenant}', [TenantController::class, 'update'])->name('tenants.update');
    Route::delete('/tenants/{tenant}', [TenantController::class, 'destroy'])->name('tenants.destroy');
    Route::post('/tenants/{tenant}/toggle-payment', [TenantController::class, 'togglePayment'])->name('tenants.toggle-payment');
    Route::post('/tenants/{tenant}/toggle-block', [TenantController::class, 'toggleBlock'])->name('tenants.toggle-block');

    // Module Management Routes
    // Route::post('/api/modules', [DeploymentController::class, 'createModule'])->name('api.modules.create');
});

require __DIR__.'/auth.php';
