<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ColumnTypeController;
use App\Http\Controllers\Api\SqlStatementController;
use App\Http\Controllers\Api\ColumnInterpreterController;
use App\Http\Controllers\Api\DecryptionTestController;
use App\Http\Controllers\Api\ModelBuilderController;
use App\Http\Controllers\Api\ControllerInterpreterController;
use App\Http\Controllers\Api\RouteBuilderController;
use App\Http\Controllers\Api\ViewBuilderController;
use App\Http\Controllers\Api\ViewStubController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/test', function () {
    return 'Hello World';
});

Route::apiResource('column-types', ColumnTypeController::class);

Route::prefix('sql-statements')->group(function () {
    Route::get('/', [SqlStatementController::class, 'index']);
    Route::post('/', [SqlStatementController::class, 'store']);
    Route::get('/categories', [SqlStatementController::class, 'categories']);
    Route::get('/{sqlStatement}', [SqlStatementController::class, 'show']);
    Route::put('/{sqlStatement}', [SqlStatementController::class, 'update']);
    Route::delete('/{sqlStatement}', [SqlStatementController::class, 'destroy']);
});

// Column Interpreter Routes
Route::prefix('schema')->group(function () {
    Route::post('/interpret', [ColumnInterpreterController::class, 'interpret']);
    Route::post('/preview', [ColumnInterpreterController::class, 'preview']);
});

// Decryption test routes
Route::prefix('encryption-test')->group(function () {
    Route::post('/decrypt', [DecryptionTestController::class, 'testDecryption']);
    Route::post('/encrypt', [DecryptionTestController::class, 'testEncryption']);
});

// Model Builder Routes
Route::post('/models/interpret', [ModelBuilderController::class, 'generate']);

// Controller Interpreter Routes
Route::post('/controllers/interpret', [ControllerInterpreterController::class, 'generate']);

// Route Builder Routes
Route::post('/routes/interpret', [RouteBuilderController::class, 'generate']);

// View Builder Routes
Route::post('/views/interpret', [ViewBuilderController::class, 'generate']);

// View Stub Routes
Route::apiResource('view-stubs', ViewStubController::class);
Route::post('view-stubs/{id}/restore', [ViewStubController::class, 'restore']);


