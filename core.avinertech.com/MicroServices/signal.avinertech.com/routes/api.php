<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SignalController;
use App\Http\Controllers\EncryptionController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// Signal handling route
Route::post('{encryptedHostId}/signal', [SignalController::class, 'handle']);

// Encryption/Decryption utility routes
Route::post('/encrypt', [EncryptionController::class, 'encrypt']);
Route::post('/decrypt', [EncryptionController::class, 'decrypt']);

Route::get('/up', function (){
    return response()->json([
        'status' => true,
        'message' => 'backend is up'
    ], 200);
});