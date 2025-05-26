<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SqlStatementController;
use App\Http\Controllers\ColumnTypeController;

Route::get('/', function () {
    return view('welcome');
});

Route::resource('sql-statements', SqlStatementController::class);
Route::resource('column-types', ColumnTypeController::class);


Route::resource('view-stubs', ViewStubController::class);
