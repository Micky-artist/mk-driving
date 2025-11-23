<?php

namespace App\Providers;

use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\URL;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Set default URL parameters for route generation
        URL::defaults([
            'locale' => $this->getCurrentLocale(),
        ]);

        ResetPassword::createUrlUsing(function (object $notifiable, string $token) {
            return config('app.frontend_url')."/password-reset/$token?email={$notifiable->getEmailForPasswordReset()}";
        });

        // Register components
        \Illuminate\Support\Facades\Blade::component('guest-layout', \App\View\Components\GuestLayout::class);
        \Illuminate\Support\Facades\Blade::component('auth-session-status', \App\View\Components\AuthSessionStatus::class);
        \Illuminate\Support\Facades\Blade::component('dropdown', \App\View\Components\Dropdown::class);
        \Illuminate\Support\Facades\Blade::component('dropdown-link', \App\View\Components\DropdownLink::class);
        \Illuminate\Support\Facades\Blade::component('nav-link', \App\View\Components\NavLink::class);
        \Illuminate\Support\Facades\Blade::component('responsive-nav-link', \App\View\Components\ResponsiveNavLink::class);
    }

    /**
     * Get the current locale from the URL or use the default.
     *
     * @return string
     */
    protected function getCurrentLocale(): string
    {
        $locale = request()->segment(1);
        $availableLocales = array_keys(config('app.available_locales', ['en' => 'English']));
        
        return in_array($locale, $availableLocales) ? $locale : config('app.locale', 'en');
    }
}
