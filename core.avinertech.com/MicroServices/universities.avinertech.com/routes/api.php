<?php

use App\Http\Controllers\{ManagerController, GitManager};
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TenantController;
use App\Http\Controllers\TenantSiteController;
use App\Http\Controllers\DeploymentController;
use App\Http\Controllers\ChatbotController;
use App\Http\Middleware\CheckAccessToken;

// Chatbot API Routes - Domain-specific endpoints for AI training
Route::prefix('chatbot')->group(function () {
    Route::get('/university-data', [ChatbotController::class, 'getUniversityData'])->name('chatbot.university-data');
    Route::get('/search-universities', [ChatbotController::class, 'searchUniversities'])->name('chatbot.search-universities');
    Route::get('/university-programs', [ChatbotController::class, 'getUniversityPrograms'])->name('chatbot.university-programs');
    Route::get('/knowledge-base', [ChatbotController::class, 'getKnowledgeBase'])->name('chatbot.knowledge-base');
    Route::get('/generate-training-data', [ChatbotController::class, 'generateTrainingData'])->name('chatbot.generate-training-data');
    Route::get('/training-stats', [ChatbotController::class, 'getTrainingStats'])->name('chatbot.training-stats');
});

// Add middleware protection if needed for production
Route::prefix('chatbot/protected')->middleware(['auth:sanctum'])->group(function () {
    Route::get('/admin/university-data', [ChatbotController::class, 'getUniversityData']);
    Route::get('/admin/knowledge-base', [ChatbotController::class, 'getKnowledgeBase']);
});
