<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Web\NewsController;
use App\Http\Controllers\Web\NewsDetailController;
use App\Http\Controllers\LanguageController;
use App\Models\Blog;
use App\Http\Controllers\SitemapController;
use App\Models\SubscriptionPlan;
use App\Services\LocaleService;

// Sitemap
Route::get('/sitemap.xml', [SitemapController::class, 'index'])
    ->name('sitemap.index');

// API endpoints (no locale needed)
Route::get('/api/subscription-plans', function(LocaleService $localeService) {
    $locale = $localeService->getLocale();
    
    $plans = SubscriptionPlan::where('is_active', true)
        ->orderBy('price')
        ->get()
        ->map(function($plan) use ($locale) {
            $name = json_decode($plan->name, true);
            $description = json_decode($plan->description, true);
            
            return [
                'id' => $plan->id,
                'slug' => $plan->slug,
                'name' => $name,
                'display_name' => $name[$locale] ?? $name['en'] ?? 'Unnamed Plan',
                'description' => $description,
                'display_description' => $description[$locale] ?? $description['en'] ?? '',
                'price' => $plan->price,
                'duration' => $plan->duration,
                'features' => $plan->features,
                'color' => $plan->color,
                'is_current' => \Illuminate\Support\Facades\Auth::check() && 
                              \Illuminate\Support\Facades\Auth::user()->subscription_plan_id === $plan->id,
            ];
        });
        
    return response()->json($plans);
})->name('api.subscription-plans');

// Language switcher (must be outside locale group)
Route::get('/language/{locale}', [LanguageController::class, 'switch'])
    ->name('language.switch')
    ->where('locale', '[a-zA-Z]{2}');

// Redirect root to localized home
Route::get('/', function (LocaleService $localeService) {
    return redirect()->route('home', ['locale' => $localeService->getLocale()]);
});

// Localized routes
Route::prefix('{locale}')
    ->where(['locale' => '[a-zA-Z]{2}'])
    ->middleware(['web', 'localize'])
    ->group(function () {
        
        // Profile routes
        Route::middleware(['auth'])->group(function () {
            // Profile
            Route::get('/profile', [\App\Http\Controllers\ProfileController::class, 'show'])
                ->name('profile.show');
                
            Route::get('/profile/edit', [\App\Http\Controllers\ProfileController::class, 'edit'])
                ->name('profile.edit');
                
            Route::patch('/profile', [\App\Http\Controllers\ProfileController::class, 'update'])
                ->name('profile.update');
                
            Route::put('/password', [\App\Http\Controllers\ProfileController::class, 'updatePassword'])
                ->name('password.update');
                
            // Profile photo
            Route::post('/profile/photo', [\App\Http\Controllers\ProfileController::class, 'updatePhoto'])
                ->name('profile.photo');
                
            Route::delete('/profile/photo', [\App\Http\Controllers\ProfileController::class, 'deletePhoto'])
                ->name('profile.photo.destroy');
        });
    // Homepage route (handles /{locale} and /{locale}/home)
    Route::get('', function ($locale) {
        // Set the application locale
        app()->setLocale($locale);
        
        // Get active subscription plans with all language data
        $plans = SubscriptionPlan::where('is_active', true)
            ->orderBy('price')
            ->get()
            ->map(function($plan) use ($locale) {
                $name = json_decode($plan->name, true);
                $description = json_decode($plan->description, true);
                
                // Process features
                $features = [];
                $rawFeatures = json_decode($plan->features, true);
                
                if (is_array($rawFeatures)) {
                    if (isset($rawFeatures[$locale])) {
                        $features = (array)$rawFeatures[$locale];
                    } elseif (isset($rawFeatures[config('app.fallback_locale', 'en')])) {
                        $features = (array)$rawFeatures[config('app.fallback_locale', 'en')];
                    } else {
                        $features = is_array($rawFeatures) ? $rawFeatures : [];
                    }
                } elseif (is_string($rawFeatures)) {
                    $features = [$rawFeatures];
                }
                
                $features = array_map('strval', (array)$features);
                
                return [
                    'id' => $plan->id,
                    'slug' => $plan->slug,
                    'name' => $name,
                    'display_name' => $name[$locale] ?? $name['en'] ?? 'Unnamed Plan',
                    'description' => $description,
                    'display_description' => $description[$locale] ?? $description['en'] ?? '',
                    'price' => $plan->price,
                    'duration' => $plan->duration,
                    'features' => $features,
                    'display_features' => $features,
                    'color' => $plan->color,
                    'max_quizzes' => $plan->max_quizzes
                ];
            });

        // Get latest blog posts
        $blogs = Blog::where('is_published', true)
            ->with('author')
            ->orderBy('published_at', 'desc')
            ->take(3)
            ->get()
            ->map(function($post) use ($locale) {
                $title = $post->title;
                $content = $post->content;
                
                return [
                    'id' => $post->id,
                    'title' => $title[$locale] ?? $title['en'] ?? 'No title',
                    'excerpt' => \Illuminate\Support\Str::limit(strip_tags($content[$locale] ?? $content['en'] ?? ''), 100),
                    'image' => $post->featured_image,
                    'date' => $post->published_at->format('M d, Y'),
                    'read_time' => ceil(str_word_count(strip_tags($content[$locale] ?? $content['en'] ?? '')) / 200) . ' min read',
                    'slug' => $post->slug
                ];
            });

        // Get guest quiz if available
        $guestQuiz = \App\Models\Quiz::where('is_guest_quiz', true)
            ->where('is_active', true)
            ->first();

        return view('home', [
            'plans' => $plans,
            'blogs' => $blogs,
            'guestQuiz' => $guestQuiz,
            'currentLocale' => $locale
        ]);
    })->name('home');
    
    // Alias for home
    Route::get('home', function($locale) {
        return redirect()->route('home', ['locale' => $locale]);
    });
    
    // Public Pages
    Route::get('about', function() {
        return view('about');
    })->name('about');
    
    Route::get('contact', function() {
        return view('contact');
    })->name('contact');
    
    Route::get('privacy', function() {
        return view('privacy');
    })->name('privacy');
    
    Route::get('terms', function() {
        return view('terms');
    })->name('terms');
    
    // News routes
    Route::get('news', [\App\Http\Controllers\Web\NewsController::class, 'index'])
        ->name('news');
        
    Route::get('news/{slug}', [\App\Http\Controllers\Web\NewsController::class, 'show'])
        ->name('news.show');
        
    // Guest Quiz Routes - Accessible without authentication
    Route::prefix('guest-quiz')->group(function () {
        // Show the theory test (with quiz ID)
        Route::get('/{quiz}', [\App\Http\Controllers\Web\GuestQuizController::class, 'show'])
            ->where('quiz', '[0-9]+')  // Ensure quiz is numeric
            ->name('guest-quiz.show');
            
        // Submit the theory test (handles both manual and auto-submit when time runs out)
        Route::post('/{quiz}/submit', [\App\Http\Controllers\Web\GuestQuizController::class, 'submit'])
            ->where('quiz', '[0-9]+')  // Ensure quiz is numeric
            ->name('guest-quiz.submit');
            
        // Reset the quiz session
        Route::post('/{quiz}/reset', [\App\Http\Controllers\Web\GuestQuizController::class, 'reset'])
            ->where('quiz', '[0-9]+')  // Ensure quiz is numeric
            ->name('guest-quiz.reset');
    });
    
    // Quizzes routes (authenticated)
    Route::get('quizzes', [\App\Http\Controllers\Web\QuizController::class, 'index'])
        ->name('quizzes')
        ->middleware('auth');
        
    // Subscriptions routes (authenticated)
    Route::get('subscriptions', [\App\Http\Controllers\Web\SubscriptionController::class, 'index'])
        ->name('subscriptions')
        ->middleware('auth');
    
    // Authentication Routes
    Route::middleware('guest')->group(function () {
        Route::get('login', [\App\Http\Controllers\Auth\AuthenticatedSessionController::class, 'create'])
            ->name('login');
            
        Route::post('login', [\App\Http\Controllers\Auth\AuthenticatedSessionController::class, 'store']);
        
        Route::get('register', [\App\Http\Controllers\Auth\RegisteredUserController::class, 'create'])
            ->name('register');
            
        Route::post('register', [\App\Http\Controllers\Auth\RegisteredUserController::class, 'store']);
    });

    Route::post('logout', [\App\Http\Controllers\Auth\AuthenticatedSessionController::class, 'destroy'])
        ->middleware('auth')
        ->name('logout');
    
    // Plans Page
    Route::get('plans', function(LocaleService $localeService) {
        $locale = $localeService->getLocale();
        
        $plans = SubscriptionPlan::where('is_active', true)
            ->orderBy('price')
            ->get()
            ->map(function($plan) use ($locale) {
                $name = json_decode($plan->name, true);
                $description = json_decode($plan->description, true);
                $features = json_decode($plan->features, true) ?? [];
                
                return [
                    'id' => $plan->id,
                    'slug' => $plan->slug,
                    'name' => $name,
                    'display_name' => $name[$locale] ?? $name['en'] ?? 'Unnamed Plan',
                    'description' => $description,
                    'display_description' => $description[$locale] ?? $description['en'] ?? '',
                    'price' => $plan->price,
                    'duration' => $plan->duration,
                    'features' => $features,
                    'color' => $plan->color,
                ];
            });
            
        return view('plans.index', [
            'plans' => $plans,
            'currentLocale' => $locale
        ]);
    })->name('plans');
        
    // Protected routes (require authentication)
    Route::middleware(['auth', 'verified'])
        ->namespace('App\Http\Controllers\Dashboard')
        ->group(function () {
        // Dashboard route
        Route::get('dashboard', 'DashboardController@index')
            ->name('dashboard');
        
        // Forum
        Route::get('forum', [\App\Http\Controllers\Web\ForumController::class, 'index'])->name('forum');
        
        // Dashboard My Quizzes Routes
        Route::prefix('dashboard/my-quizzes')->name('dashboard.quizzes.')->group(function() {
            Route::get('/', [\App\Http\Controllers\Web\Dashboard\QuizController::class, 'index'])
                ->name('index');
                
            Route::get('/{quiz}', [\App\Http\Controllers\Web\Dashboard\QuizController::class, 'show'])
                ->name('show');
        });
        
        // News Routes
        Route::get('news', [\App\Http\Controllers\Web\NewsController::class, 'index'])
            ->name('news.index');
            
        Route::get('news/{news:slug}', [\App\Http\Controllers\Web\NewsDetailController::class, 'show'])
            ->name('news.show');
        
        // Subscription Routes
        Route::prefix('subscriptions')->name('subscriptions.')->group(function() {
            Route::get('/', [\App\Http\Controllers\Web\SubscriptionController::class, 'index'])
                ->name('index');
                
            Route::get('/success', [\App\Http\Controllers\Web\SubscriptionController::class, 'success'])
                ->name('success');
                
            Route::get('/cancel', [\App\Http\Controllers\Web\SubscriptionController::class, 'cancel'])
                ->name('cancel');
                
            Route::delete('/{subscription}', [\App\Http\Controllers\Web\SubscriptionController::class, 'destroy'])
                ->name('destroy');
        });
    });
});

// Forum Routes
Route::prefix('{locale}/forum')
    ->where(['locale' => '[a-zA-Z]{2}'])
    ->name('forum.')
    ->group(function () {
        Route::get('/', [\App\Http\Controllers\Web\ForumController::class, 'index'])->name('index');
        Route::get('/ask', [\App\Http\Controllers\Web\ForumController::class, 'create'])->name('create');
        Route::post('/', [\App\Http\Controllers\Web\ForumController::class, 'store'])->name('store');
        Route::get('/{id}', [\App\Http\Controllers\Web\ForumController::class, 'show'])->name('show');
        
        // Answers
        Route::post('/{questionId}/answers', [\App\Http\Controllers\Web\ForumController::class, 'storeAnswer'])
            ->name('answers.store')
            ->middleware('auth');
            
        // Voting
        Route::post('/{type}/{id}/vote', [\App\Http\Controllers\Web\ForumController::class, 'vote'])
            ->name('vote')
            ->middleware('auth');
            
        // Mark as best answer
        Route::post('/{questionId}/best-answer/{answerId}', [\App\Http\Controllers\Web\ForumController::class, 'markAsBestAnswer'])
            ->name('best-answer')
            ->middleware('auth');
    });

// Authentication routes (from auth.php)
require __DIR__.'/auth.php';