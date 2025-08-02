<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TenantController;
use App\Http\Controllers\PackageController;
use App\Http\Controllers\ServiceModuleController;
use App\Http\Controllers\EncryptionController;
use App\Http\Controllers\LoginController;
use App\Http\Middleware\SuperAdminAuth;
use App\Http\Controllers\DeploymentController;

// Authentication Routes
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login'])->name('login.submit');
Route::get('/logout', [LoginController::class, 'logout'])->name('logout');

require __DIR__.'/auth.php';
