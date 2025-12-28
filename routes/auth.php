<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\EmailVerificationNotificationController;
use App\Http\Controllers\Auth\NewPasswordController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Auth\VerifyEmailController;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;

// Group auth routes with locale prefix
Route::prefix('{locale}')->where(['locale' => '(rw|en)'])->group(function () {
    // Display login form
    Route::get('/login', [AuthenticatedSessionController::class, 'create'])
        ->middleware('guest')
        ->name('login.form');

    // Display registration form
    Route::get('/register', [RegisteredUserController::class, 'create'])
        ->middleware('guest')
        ->name('register.form');

    Route::post('/register', [RegisteredUserController::class, 'store'])
        ->middleware('guest')
        ->name('register');

    Route::post('/login', [AuthenticatedSessionController::class, 'store'])
        ->middleware('guest')
        ->name('login');

    // Display forgot password form (for guests)
    Route::get('/forgot-password', [PasswordResetLinkController::class, 'create'])
        ->middleware('guest')
        ->name('password.request');

    // Handle forgot password form submission (for guests)
    Route::post('/forgot-password', [PasswordResetLinkController::class, 'store'])
        ->middleware('guest')
        ->name('password.email');

    // Display forgot password form (for authenticated users)
    Route::get('/profile/password-reset', [PasswordResetLinkController::class, 'create'])
        ->middleware('auth')
        ->name('profile.password.request');

    // Handle forgot password form submission (for authenticated users)
    Route::post('/profile/password-reset', [PasswordResetLinkController::class, 'store'])
        ->middleware('auth')
        ->name('profile.password.email');

    // Display reset password form
    Route::get('/reset-password/{token}', [NewPasswordController::class, 'create'])
        ->middleware('guest')
        ->name('password.reset');

    // Handle reset password form submission
    Route::post('/reset-password', [NewPasswordController::class, 'store'])
        ->middleware('guest')
        ->name('password.store');

    // Email Verification Routes
    Route::get('/verify-email', function () {
        return view('auth.verify');
    })->middleware('auth')->name('verification.notice');

    Route::get('/verify-email/{id}/{hash}', function (\Illuminate\Foundation\Auth\EmailVerificationRequest $request) {
        $request->fulfill();
        return redirect(app()->getLocale() . '/dashboard?verified=1');
    })->middleware(['auth', 'signed', 'throttle:6,1'])->name('verification.verify');

    Route::post('/email/verification-notification', function (Request $request) {
        $request->user()->sendEmailVerificationNotification();
        return back()->with('status', 'verification-link-sent');
    })->middleware(['auth', 'throttle:6,1'])->name('verification.send');

    Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])
        ->middleware('auth')
        ->name('logout');
});
