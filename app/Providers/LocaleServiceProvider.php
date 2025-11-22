<?php

namespace App\Providers;

use App\Services\LocaleService;
use Illuminate\Support\ServiceProvider;

class LocaleServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->singleton(LocaleService::class, function ($app) {
            return new LocaleService();
        });

        // Register an alias for the service
        $this->app->alias(LocaleService::class, 'locale');
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Set the application locale at the beginning of the request
        $this->app->booted(function () {
            $this->app->setLocale(
                $this->app->make(LocaleService::class)->getLocale()
            );
        });
    }
}
