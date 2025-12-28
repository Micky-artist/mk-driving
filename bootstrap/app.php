<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use App\Http\Middleware\VerifyCsrfToken;
use App\Http\Middleware\EncryptCookies;
use Illuminate\Support\Facades\Route;

return Application::configure(basePath: dirname(__DIR__))
    ->withProviders([
        \App\Providers\LocaleServiceProvider::class,
    ])
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
        then: function () {
            Route::middleware('web')
                ->group(base_path('routes/dashboard.php'));
        },
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'verified' => \App\Http\Middleware\EnsureEmailIsVerified::class,
            'localize' => \App\Http\Middleware\SetLocale::class,
            'track.activity' => \App\Http\Middleware\TrackUserActivity::class,
            'check.subscription' => \App\Http\Middleware\EnsureValidSubscription::class,
        ]);

        // Add SetLocale middleware to web group
        $middleware->web(\App\Http\Middleware\SetLocale::class);
        
        // Add TrackVisitor middleware to web group
        $middleware->web(\App\Http\Middleware\TrackVisitor::class);
        
        // Add TrackNotifications middleware to web group for admin users
        $middleware->web(\App\Http\Middleware\TrackNotifications::class);
        
        // Add subscription validation to web group
        $middleware->web(\App\Http\Middleware\EnsureValidSubscription::class);
        
        // Add session middleware to API group for web auth compatibility
        $middleware->api([
            EncryptCookies::class,
            AddQueuedCookiesToResponse::class,
            StartSession::class,
            ShareErrorsFromSession::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
