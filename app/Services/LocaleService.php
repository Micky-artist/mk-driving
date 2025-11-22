<?php

namespace App\Services;

class LocaleService
{
    public function setLocale($locale = null)
    {
        $locale = $this->validateLocale($locale);
        app()->setLocale($locale);
        session(['locale' => $locale]);
        return $this;
    }

    public function getLocale()
    {
        return app()->getLocale();
    }

    public function getAvailableLocales()
    {
        return config('app.available_locales', ['en' => 'English']);
    }

    public function getFallbackLocale()
    {
        return config('app.fallback_locale', 'en');
    }

    public function validateLocale($locale)
    {
        $locales = array_keys($this->getAvailableLocales());
        return in_array($locale, $locales) ? $locale : $this->getFallbackLocale();
    }
}
