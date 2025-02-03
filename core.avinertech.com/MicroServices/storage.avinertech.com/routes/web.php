<?php

use Illuminate\Support\Facades\Route;



Route::post('/api/upload', [\App\Http\Controllers\StorageController::class, 'index'])->name('upload');
Route::get('/api/upload/{filename}', [\App\Http\Controllers\StorageController::class, 'show'])->name('upload.show');

Route::get('/', function () {
    return view('welcome');
});