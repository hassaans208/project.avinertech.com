<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UniverseController;

Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::get('/qs-rankings', [UniverseController::class, 'qsRankings'])->name('qs-rankings');
Route::get('/programs-database', [UniverseController::class, 'programsDatabase'])->name('programs-database');
Route::get('/api/csv-files', [UniverseController::class, 'getAvailableFiles'])->name('api.csv-files');

require __DIR__.'/auth.php';
