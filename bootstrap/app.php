<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withProviders([
        \App\Providers\LocaleServiceProvider::class,
    ])
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'verified' => \App\Http\Middleware\EnsureEmailIsVerified::class,
            'localize' => \App\Http\Middleware\SetLocale::class,
            'track.activity' => \App\Http\Middleware\TrackUserActivity::class,
        ]);

        // Add SetLocale middleware to web group
        $middleware->web(\App\Http\Middleware\SetLocale::class);
        
        // Add TrackVisitor middleware to web group
        $middleware->web(\App\Http\Middleware\TrackVisitor::class);
        
        // Add TrackNotifications middleware to web group for admin users
        $middleware->web(\App\Http\Middleware\TrackNotifications::class);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
