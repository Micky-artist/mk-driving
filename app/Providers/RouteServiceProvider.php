<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\View;
use Illuminate\Foundation\Application;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * The path to your application's "home" route.
     *
     * Typically, users are redirected here after authentication.
     *
     * @var string
     */
    public const HOME = '/{locale}/dashboard';

    /**
     * The available application locales.
     *
     * @var array
     */
    protected $locales = ['rw', 'en'];
    
    /**
     * The default application locale.
     *
     * @var string
     */
    protected $defaultLocale = 'rw';

    /**
     * Create a new route service provider instance.
     *
     * @return void
     */
    public function __construct(Application $app)
    {
        parent::__construct($app);
        
        $this->locales = array_keys(config('app.available_locales', ['rw' => 'Kinyarwanda', 'en' => 'English']));
        $this->defaultLocale = config('app.locale', 'rw');
    }

    /**
     * Define your route model bindings, pattern filters, etc.
     */
    public function boot(): void
    {
        parent::boot();
        
        $this->configureRateLimiting();

        // Set the default locale
        app()->setLocale($this->defaultLocale);
        
        // Explicitly bind the Quiz model for route model binding
        Route::bind('quiz', function ($value) {
            // Convert string ID to integer if possible
            $id = is_numeric($value) ? (int)$value : $value;
            
            // Try to find the quiz with the given ID
            $quiz = \App\Models\Quiz::find($id);
            
            // If quiz not found, throw 404
            if (!$quiz) {
                abort(404, 'Quiz not found.');
            }
            
            return $quiz;
        });

        $this->routes(function () {
            // API routes (no localization)
            Route::middleware('api')
                ->prefix('api')
                ->group(base_path('routes/api.php'));

            // Web routes with localization
            Route::middleware('web')
                ->group(function () {
                    // Get the locale from the URL
                    $locale = request()->segment(1);
                    
                    // If no locale in URL or invalid locale, redirect to default locale
                    if (!in_array($locale, $this->locales)) {
                        $path = request()->path() === '/' ? '' : '/' . ltrim(request()->path(), '/');
                        return redirect("/{$this->defaultLocale}{$path}");
                    }
                    
                    // Set the application locale
                    app()->setLocale($locale);
                    
                    // Add locale to the URL generator's defaults
                    Route::bind('locale', function ($value) {
                        return in_array($value, $this->locales) ? $value : $this->defaultLocale;
                    });
                    
                    // Load the web routes with the locale prefix
                    Route::prefix($locale)
                        ->group(base_path('routes/web.php'));
                        
                    // Load auth routes with locale prefix
                    Route::prefix($locale)
                        ->group(base_path('routes/auth.php'));
                        
                    // Dashboard routes with localization
                    Route::prefix($locale . '/dashboard')
                        ->name('dashboard.')
                        ->middleware(['web', 'auth', 'verified'])
                        ->group(base_path('routes/dashboard.php'));
                        
                    // Admin routes with localization
                    Route::prefix($locale . '/admin')
                        ->name('admin.')
                        ->middleware(['web', 'auth', 'admin'])
                        ->group(base_path('routes/admin.php'));
                });
        });
        
        // Add a global view composer to share the current locale with all views
        View::composer('*', function ($view) {
            $view->with('currentLocale', app()->getLocale());
            $view->with('availableLocales', $this->locales);
        });
    }
    
    /**
     * Configure the rate limiters for the application.
     */
    protected function configureRateLimiting(): void
    {
        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(60)->by($request->user()?->id ?: $request->ip());
        });
    }
}
