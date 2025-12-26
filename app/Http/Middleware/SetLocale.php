<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Services\LocaleService;
use Illuminate\Support\Facades\Log;

class SetLocale
{
    protected $localeService;

    public function __construct(LocaleService $localeService)
    {
        $this->localeService = $localeService;
    }

    public function handle(Request $request, Closure $next)
    {
        // Get locale from URL first
        $locale = $request->segment(1);
        $localeSource = 'url';
        
        // If not in URL, try session, then default to 'rw' if not set
        if (!$this->localeService->validateLocale($locale)) {
            $locale = session('locale') ?: 'rw'; // Default to 'rw' if no session
            $localeSource = 'session';
            
            // Only check browser language if no session is set and 'rw' is not available
            if (!$locale) {
                $locale = $request->getPreferredLanguage(
                    array_keys($this->localeService->getAvailableLocales())
                );
                $localeSource = 'browser';
            }
            
            // If we need to redirect to include the locale in the URL
            if ($this->shouldRedirectToIncludeLocale($request, $locale)) {
                return $this->redirectWithLocale($request, $locale);
            }
        }
        
        // Validate and set the application locale
        $validatedLocale = $this->localeService->validateLocale($locale);
        $this->localeService->setLocale($validatedLocale);
        
        // Set system locale for date formatting
        if (function_exists('setlocale')) {
            setlocale(LC_TIME, $validatedLocale . '.UTF-8');
        }
        
        // Debug logging
        if (config('app.debug')) {
            Log::debug('Locale set', [
                'locale' => $locale,
                'source' => $localeSource,
                'url' => $request->fullUrl(),
                'available_locales' => array_keys($this->localeService->getAvailableLocales())
            ]);
        }
        
        return $next($request);
    }
    
    protected function shouldRedirectToIncludeLocale($request, $locale)
    {
        return !$request->is('api/*') && 
               !in_array($request->segment(1), array_keys($this->localeService->getAvailableLocales()));
    }
    
    protected function redirectWithLocale($request, $locale)
    {
        $newUrl = '/' . $locale . '/' . ltrim($request->path(), '/');
        
        if (!empty($request->query())) {
            $newUrl .= '?' . http_build_query($request->query());
        }
        
        return redirect($newUrl);
    }
}
