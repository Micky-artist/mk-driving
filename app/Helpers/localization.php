<?php

use App\Helpers\VersionHelper;
use App\Services\LocaleService;

if (!function_exists('formatDuration')) {
    /**
     * Format duration in seconds to human-readable format
     *
     * @param int $seconds Duration in seconds
     * @return string Formatted duration (e.g., "2m 30s")
     */
    function formatDuration($seconds)
    {
        $minutes = floor($seconds / 60);
        $seconds = $seconds % 60;
        
        if ($minutes > 0) {
            return sprintf('%dm %02ds', $minutes, $seconds);
        }
        
        return sprintf('%ds', $seconds);
    }
}

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

if (!function_exists('app_version')) {
    /**
     * Get the application version
     *
     * @param bool $fullVersion Whether to include build number
     * @return string
     */
    function app_version(bool $fullVersion = true): string
    {
        return $fullVersion ? VersionHelper::getFullVersion() : VersionHelper::getVersion();
    }
}

if (!function_exists('app_build')) {
    /**
     * Get the application build number
     *
     * @return int
     */
    function app_build(): int
    {
        return VersionHelper::getBuildNumber();
    }
}

if (!function_exists('app_changelog')) {
    /**
     * Get the application changelog
     *
     * @param int $limit Number of entries to return (0 for all)
     * @return array
     */
    function app_changelog(int $limit = 0): array
    {
        $changelog = VersionHelper::getChangelog();
        
        if ($limit > 0) {
            return array_slice($changelog, -$limit);
        }
        
        return $changelog;
    }
}
