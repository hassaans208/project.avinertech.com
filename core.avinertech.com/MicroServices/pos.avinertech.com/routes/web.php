<?php

use Illuminate\Support\Facades\Route;



Route::post('/api/upload', [\App\Http\Controllers\StorageController::class, 'index'])->name('upload');
Route::get('/api/upload/{filename}', [\App\Http\Controllers\StorageController::class, 'show'])->name('upload.show');

Route::get('/', function () {
    return view('welcome');
});

Route::get('/what-we-do', function () {
    return view('what-we-do');
});

Route::get('/service-references', function () {
    return view('service-refs');
});