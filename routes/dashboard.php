<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Dashboard\DashboardController;
use App\Http\Controllers\Web\Dashboard\QuizController;
use App\Http\Controllers\Web\Dashboard\QuizAttemptController;
use App\Http\Controllers\Web\Dashboard\PaymentController;

Route::prefix('{locale}')->where(['locale' => '(rw|en)'])
    ->middleware(['auth', 'verified'])
    ->group(function () {
    // Dashboard Home
    Route::get('/dashboard', [DashboardController::class, 'index'])
        ->name('dashboard');
    
    // Clear welcome modal session flag
    Route::post('/dashboard/clear-welcome-modal', [DashboardController::class, 'clearWelcomeModal'])
        ->name('dashboard.clear-welcome-modal');

    // Dashboard Progress (direct route)
    Route::get('/dashboard/progress', [QuizController::class, 'progress'])
        ->name('dashboard.progress');

    // Quiz Routes
    Route::prefix('dashboard/quizzes')->name('dashboard.quizzes.')->group(function () {
        Route::get('/', [QuizController::class, 'index'])->name('index');
        Route::get('/progress', [QuizController::class, 'progress'])->name('progress');
        Route::get('/{quiz}', [QuizController::class, 'show'])->name('show');
        Route::post('/{quiz}/start', [QuizController::class, 'start'])->name('start');
        
        // Quiz taking routes (require an active attempt)
        Route::prefix('{quiz}/attempts/{attempt}')->group(function () {
            Route::get('/', [QuizController::class, 'take'])->name('take');
            Route::post('/answer', [QuizAttemptController::class, 'submitAnswer'])->name('submit.answer');
            Route::post('/submit', [QuizAttemptController::class, 'update'])->name('submit');
            Route::get('/results', [QuizController::class, 'results'])->name('results');
            Route::get('/details', [QuizController::class, 'attemptDetails'])->name('details');
        })->middleware('can:view,attempt');
    });

    
    // Help Center
    Route::get('/help-center', [\App\Http\Controllers\Web\HelpCenterController::class, 'index'])->name('help-center');
    
    // Payment Management
    Route::prefix('payments')->name('payments.')->group(function () {
        Route::get('/', [PaymentController::class, 'index'])->name('index');
        Route::get('/{payment}', [PaymentController::class, 'show'])->name('show');
        Route::post('/{payment}/confirm', [PaymentController::class, 'confirm'])->name('confirm');
    });
    
    // Subscription History
    Route::get('/subscription/history', [DashboardController::class, 'subscriptionHistory'])
        ->name('dashboard.subscription.history');
    });
