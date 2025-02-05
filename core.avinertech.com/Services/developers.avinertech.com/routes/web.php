<?php

use Illuminate\Support\Facades\Route;

Route::get('/', [\App\Http\Controllers\DocsController::class, 'index']);


Route::get('/what-we-do', function () {
    return view('what-we-do');
});

