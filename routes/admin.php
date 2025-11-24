<?php

use App\Http\Controllers\Web\Admin\AdminController;
use App\Http\Controllers\Web\Admin\UserController;
use App\Http\Controllers\Web\Admin\SubscriptionPlanController;
use App\Http\Controllers\Web\Admin\SubscriptionController;
use App\Http\Controllers\Web\Admin\ProfileController;
use App\Http\Controllers\Web\Admin\NewsController;
use App\Http\Controllers\Web\Admin\GuestQuizController;
use App\Http\Controllers\Web\Admin\ForumController;
use Illuminate\Support\Facades\Route;

// Admin routes group - middleware and prefix are applied in web.php
Route::group([], function () {
        // Admin Dashboard
        Route::get('/', [AdminController::class, 'dashboard'])->name('dashboard');

        // Profile Management
        Route::prefix('profile')->name('profile.')->group(function () {
            Route::get('/', [ProfileController::class, 'edit'])->name('edit');
            Route::put('/', [ProfileController::class, 'update'])->name('update');
            Route::put('/password', [ProfileController::class, 'updatePassword'])->name('password');
        });

        // User Management
        Route::resource('users', UserController::class)->names('users');
        Route::post('users/{user}/change-password', [UserController::class, 'changePassword'])
            ->name('users.change-password');

        // Subscription Plans Management
        Route::resource('subscription-plans', SubscriptionPlanController::class)->names('subscription-plans');
        
        // Subscription Management
        Route::get('subscriptions', [SubscriptionController::class, 'index'])->name('subscriptions.index');
        Route::get('subscriptions/pending', [SubscriptionController::class, 'pending'])->name('subscriptions.pending');
        Route::get('subscriptions/active', [SubscriptionController::class, 'active'])->name('subscriptions.active');
        Route::resource('subscriptions', SubscriptionController::class)->names('subscriptions')->except(['index', 'pending']);
            
        // Subscription approval routes
        Route::post('subscriptions/{subscription}/approve', [SubscriptionController::class, 'approve'])
            ->name('subscriptions.approve');
        Route::post('subscriptions/{subscription}/reject', [SubscriptionController::class, 'reject'])
            ->name('subscriptions.reject');
            
        // Payment approval routes - only allow PATCH for security
        // Note: The locale is already in the URL prefix from the route group
        Route::patch('payments/{id}/approve', [SubscriptionController::class, 'approvePayment'])
            ->name('payments.approve')
            ->middleware('verified')
            ->where('id', '[0-9]+');

        Route::patch('payments/{id}/reject', [SubscriptionController::class, 'rejectPayment'])
            ->name('payments.reject')
            ->middleware('verified')
            ->where('id', '[0-9]+');
            
        // Other subscription routes
        Route::post('subscriptions/{subscription}/cancel', [SubscriptionController::class, 'cancel'])
            ->name('subscriptions.cancel');
        Route::get('subscriptions/stats', [SubscriptionController::class, 'stats'])
            ->name('subscriptions.stats');
            
        // News Management
        Route::prefix('msi/news')->name('msi.news.')->group(function () {
            Route::get('/', [NewsController::class, 'index'])->name('index');
            Route::get('/create', [NewsController::class, 'create'])->name('create');
            Route::post('/', [NewsController::class, 'store'])->name('store');
            Route::get('/{news}/edit', [NewsController::class, 'edit'])->name('edit');
            Route::put('/{news}', [NewsController::class, 'update'])->name('update');
            Route::delete('/{news}', [NewsController::class, 'destroy'])->name('destroy');
            Route::delete('/{id}/remove-image/{imageIndex}', [NewsController::class, 'removeImage'])->name('remove-image');
        });

        // Settings
        Route::prefix('settings')->name('settings.')->group(function () {
            Route::get('/', [AdminController::class, 'settings'])->name('index');
            Route::post('/', [AdminController::class, 'updateSettings'])->name('update');
        });

        // Guest Quiz Management
        Route::prefix('guest-quiz')->name('guest-quiz.')->group(function () {
            Route::get('/', [GuestQuizController::class, 'index'])->name('index');
            Route::post('/{quiz}/set', [GuestQuizController::class, 'setGuestQuiz'])->name('set');
        });

        // Quiz Management
        Route::resource('quizzes', \App\Http\Controllers\Web\Admin\QuizController::class)->names('quizzes');

        // Forum Management
        Route::prefix('forum')->name('forum.')->group(function () {
            Route::get('/', [ForumController::class, 'index'])->name('index');
            Route::delete('/{question}', [ForumController::class, 'destroy'])->name('destroy');
            Route::post('/{question}/answers', [ForumController::class, 'storeAnswer'])->name('answers.store');
            Route::delete('/answers/{answer}', [ForumController::class, 'destroyAnswer'])->name('answers.destroy');
            Route::patch('/{question}/toggle-approval', [ForumController::class, 'toggleApproval'])->name('toggle-approval');
        });
    });