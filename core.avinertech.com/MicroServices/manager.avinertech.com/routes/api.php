<?php

use App\Http\Controllers\ManagerController;
use Illuminate\Support\Facades\Route;

Route::post('/manage-module', [ManagerController::class, 'createApplication']);