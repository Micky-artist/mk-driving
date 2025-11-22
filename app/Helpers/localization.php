<?php

use App\Services\LocaleService;

if (!function_exists('localized_route')) {
    function localized_route($name, $parameters = [], $locale = null, $absolute = true)
    {
        $locale = $locale ?: app()->getLocale();
        $parameters = array_merge(
            ['locale' => $locale],
            $parameters
        );
        
        try {
            return route($name, $parameters, $absolute);
        } catch (\Exception $e) {
            // Fallback to current URL if route doesn't exist
            return url()->current();
        }
    }
}

if (!function_exists('current_locale')) {
    function current_locale()
    {
        return app(LocaleService::class)->getLocale();
    }
}

if (!function_exists('available_locales')) {
    function available_locales()
    {
        return app(LocaleService::class)->getAvailableLocales();
    }
}
