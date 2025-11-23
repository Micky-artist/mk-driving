<?php

namespace App\Providers;

use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class NotificationServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        View::composer('*', function ($view) {
            // Share flash messages with all views
            if (session('notification')) {
                $notification = session('notification');
                $view->with('notification', [
                    'type' => $notification['type'] ?? 'info',
                    'message' => $notification['message'] ?? '',
                    'duration' => $notification['duration'] ?? 5000
                ]);
            }
        });
    }
}
