<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Redirect;
use App\Services\LocaleService;

class LanguageController extends Controller
{
    protected $localeService;

    public function __construct(LocaleService $localeService)
    {
        $this->localeService = $localeService;
    }

    /**
     * Switch the application language
     *
     * @param string $newLocale The new locale to switch to
     * @return \Illuminate\Http\RedirectResponse
     */
    public function switch($newLocale)
    {
        try {
            // Validate the requested locale
            $newLocale = $this->localeService->validateLocale($newLocale);
            
            // Set the new locale
            $this->localeService->setLocale($newLocale);
            
            // If we have a return_to parameter, redirect there with the new locale
            if (request()->has('return_to')) {
                $returnTo = urldecode(request('return_to'));
                return $this->handleReturnToRedirect($returnTo, $newLocale);
            }
            
            // Default redirect to home with the new locale
            return Redirect::route('home', ['locale' => $newLocale]);
            
        } catch (\Exception $e) {
            Log::error('Error switching language: ' . $e->getMessage(), [
                'locale' => $newLocale,
                'exception' => $e
            ]);
            
            // Fallback to home with the current locale
            return Redirect::route('home', [
                'locale' => App::getFallbackLocale()
            ]);
        }
    }
    
    /**
     * Handle redirect with return_to parameter
     * 
     * @param string $returnTo The URL to return to
     * @param string $newLocale The new locale
     * @return \Illuminate\Http\RedirectResponse
     */
    protected function handleReturnToRedirect($returnTo, $newLocale)
    {
        try {
            // Parse the URL and get the path
            $parsedUrl = parse_url($returnTo);
            
            // Get the path and ensure it starts with a single slash
            $path = '/' . ltrim($parsedUrl['path'] ?? '', '/');
            
            // Remove any existing locale from the path
            $path = $this->removeLocaleFromPath($path);
            
            // Add the new locale
            $newPath = '/' . $newLocale . ($path === '/' ? '' : $path);
            
            // Rebuild the full URL with query string if it exists
            $newUrl = ($parsedUrl['scheme'] ?? (request()->secure() ? 'https' : 'http')) . '://' .
                 ($parsedUrl['host'] ?? request()->getHost()) . 
                 (isset($parsedUrl['port']) ? ':' . $parsedUrl['port'] : '') . 
                 $newPath . 
                 (isset($parsedUrl['query']) ? '?' . $parsedUrl['query'] : '') . 
                 (isset($parsedUrl['fragment']) ? '#' . $parsedUrl['fragment'] : '');
            
            Log::info('Language switch with return_to', [
                'from' => App::getLocale(),
                'to' => $newLocale,
                'return_to' => $returnTo,
                'redirect_to' => $newUrl
            ]);
            
            return Redirect::to($newUrl);
            
        } catch (\Exception $e) {
            Log::error('Error processing return_to URL: ' . $e->getMessage(), [
                'return_to' => $returnTo,
                'exception' => $e
            ]);
            
            // If there's an error with the return_to URL, just redirect to home
            return Redirect::route('home', ['locale' => $newLocale]);
        }
    }
    
    /**
     * Remove any existing locale from the path
     * 
     * @param string $path The path to clean
     * @return string The cleaned path
     */
    protected function removeLocaleFromPath($path)
    {
        $locales = array_keys(config('app.available_locales', ['en' => 'English']));
        
        foreach ($locales as $locale) {
            if (str_starts_with($path, "/$locale/")) {
                return substr($path, strlen($locale) + 1);
            } elseif ($path === "/$locale") {
                return '/';
            }
        }
        
        return $path;
    }
}
