<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MailController;

Route::middleware('throttle:1,1')->post('/send', [MailController::class, 'send']);


