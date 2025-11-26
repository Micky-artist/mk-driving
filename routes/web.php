<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Web\NewsController;
use App\Http\Controllers\Web\NewsDetailController;
use App\Http\Controllers\LanguageController;
use App\Models\Blog;
use App\Http\Controllers\SitemapController;
use App\Models\SubscriptionPlan;
use App\Services\LocaleService;
use App\Http\Controllers\Web\PaymentController;

// Google OAuth Routes
Route::get('/auth/google', [\App\Http\Controllers\Auth\GoogleAuthController::class, 'redirect'])
    ->name('google.login');

Route::get('/auth/google/callback', [\App\Http\Controllers\Auth\GoogleAuthController::class, 'callback'])
    ->name('google.callback');

// Sitemap
Route::get('/sitemap.xml', [SitemapController::class, 'index'])
    ->name('sitemap.index');

// Payment routes (no locale needed)
Route::post('/payments/request', [PaymentController::class, 'store'])
    ->middleware(['auth', 'verified'])
    ->name('payments.request');

// API endpoints (no locale needed)
Route::get('/api/subscription-plans', function (LocaleService $localeService) {
    $locale = $localeService->getLocale();
    
    $plans = SubscriptionPlan::where('is_active', true)
        ->orderBy('price')
        ->get()
        ->map(function ($plan) use ($locale) {
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
        
        // Admin routes
        Route::prefix('admin')
            ->name('admin.')
            ->middleware(['auth', 'verified', 'can:isAdmin'])
            ->group(base_path('routes/admin.php'));
        // Homepage route (handles /{locale} and /{locale}/home)
        Route::get('', function ($locale) {
            \Illuminate\Support\Facades\Log::info('Home route accessed', ['locale' => $locale]);
            
            // Set the application locale
            app()->setLocale($locale);
            \Illuminate\Support\Facades\Log::debug('Locale set to: ' . app()->getLocale());
            
            // Get active subscription plans with all language data
            $plans = \App\Models\SubscriptionPlan::where('is_active', true)
                ->orderBy('price')
                ->get()
                ->map(function ($plan) use ($locale) {
                    // Handle name (string or array)
                    $name = $plan->name;
                    if (is_string($name)) {
                        $name = json_decode($name, true) ?: [];
                    }
                    $displayName = $name[$locale] ?? $name[config('app.fallback_locale', 'en')] ?? 'Unnamed Plan';

                    // Handle description (string or array)
                    $description = $plan->description;
                    if (is_string($description)) {
                        $description = json_decode($description, true) ?: [];
                    }
                    $displayDescription = $description[$locale] ?? $description[config('app.fallback_locale', 'en')] ?? '';

                    // Handle features (string, array, or JSON string)
                    $features = $plan->features;
                    if (is_string($features)) {
                        $features = json_decode($features, true) ?: [];
                    }
                    
                    // Ensure features is an array and process it
                    $features = is_array($features) ? $features : [];
                    if (isset($features[$locale])) {
                        $features = (array)$features[$locale];
                    } elseif (isset($features[config('app.fallback_locale', 'en')])) {
                        $features = (array)$features[config('app.fallback_locale', 'en')];
                    }
                    $features = array_map('strval', $features);
                    
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
            $blogs = \App\Models\Blog::where('is_published', true)
                ->with('author')
                ->orderBy('published_at', 'desc')
                ->take(3)
                ->get()
                ->map(function ($post) use ($locale) {
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

            // Get active quizzes with relationships
            \Illuminate\Support\Facades\Log::debug('Starting quizzes query');
            $quizzes = collect();
            
            try {
                // Debug: Check if we can connect to the database
                \Illuminate\Support\Facades\DB::connection()->getPdo();
                \Illuminate\Support\Facades\Log::debug('Database connection successful');
                
                // Get active guest quizzes with plan data and all active questions
                $quizzes = \App\Models\Quiz::where('is_guest_quiz', true)
        ->where('is_active', true)
        ->withCount('questions')
        ->with(['questions' => function($query) {
            $query->where('is_active', true)
                  ->with(['options' => function($q) {
                      $q->where('is_active', true)
                        ->orderBy('order');
                  }]);
            $query->orderBy('order');
        }])
        ->orderBy('created_at', 'desc')
        ->take(3)
        ->get();

// Fallback to any active quizzes if no guest quizzes found
if ($quizzes->isEmpty()) {
    $quizzes = \App\Models\Quiz::where('is_active', true)
        ->with(['questions' => function ($query) {
            $query->where('is_active', true)
                  ->with(['options' => function ($q) {
                      $q->where('is_active', true);
                  }]);
        }])
        ->withCount(['questions' => function ($query) {
            $query->where('is_active', true);
        }])
        ->orderBy('created_at', 'desc')
        ->take(3)
        ->get();
}
                    
                // Log the actual number of questions loaded for each quiz
                foreach ($quizzes as $quiz) {
                    \Illuminate\Support\Facades\Log::debug('Quiz questions loaded', [
                        'quiz_id' => $quiz->id,
                        'quiz_title' => $quiz->title,
                        'questions_count' => $quiz->questions_count,
                        'questions_loaded' => $quiz->questions->count(),
                        'sample_question' => $quiz->questions->isNotEmpty() ? 'exists' : 'none'
                    ]);
                }
                
                // Log basic quiz info for debugging
                \Illuminate\Support\Facades\Log::debug('Homepage - Quizzes loaded:', [
                    'count' => $quizzes->count(),
                    'quiz_ids' => $quizzes->pluck('id'),
                    'has_questions' => $quizzes->every(fn($q) => $q->questions->isNotEmpty())
                ]);
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::error('Error in quizzes query: ' . $e->getMessage(), [
                    'exception' => get_class($e),
                    'file' => $e->getFile(),
                    'line' => $e->getLine(),
                    'trace' => $e->getTraceAsString()
                ]);
                $quizzes = collect(); // Return empty collection on error
            }

            // Render the home view
            return view('home', [
                'plans' => $plans,
                'blogs' => $blogs,
                'quizzes' => $quizzes,
                'currentLocale' => $locale
            ]);
        })->name('home');
        
        // Profile routes (authenticated only)
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
        
        // Subscription routes (public but with auth middleware on specific actions)
        Route::prefix('subscriptions')->name('subscriptions.')->group(function () {
            Route::get('/', [\App\Http\Controllers\Web\SubscriptionController::class, 'index'])->name('index');
            Route::get('/{plan}', [\App\Http\Controllers\Web\SubscriptionController::class, 'show'])->name('show');
            Route::middleware(['auth'])->group(function () {
                Route::post('/{plan}/subscribe', [\App\Http\Controllers\Web\SubscriptionController::class, 'subscribe'])
                    ->name('subscribe');
            });
        });
    Route::get('', function ($locale) {
        \Illuminate\Support\Facades\Log::info('Home route accessed', ['locale' => $locale]);
        
        // Set the application locale
        app()->setLocale($locale);
        \Illuminate\Support\Facades\Log::debug('Locale set to: ' . app()->getLocale());
        
        // Get active subscription plans with all language data
        $plans = SubscriptionPlan::where('is_active', true)
            ->orderBy('price')
            ->get()
            ->map(function ($plan) use ($locale) {
                // Handle name (string or array)
                $name = $plan->name;
                if (is_string($name)) {
                    $name = json_decode($name, true) ?: [];
                }
                $displayName = $name[$locale] ?? $name[config('app.fallback_locale', 'en')] ?? 'Unnamed Plan';

                // Handle description (string or array)
                $description = $plan->description;
                if (is_string($description)) {
                    $description = json_decode($description, true) ?: [];
                }
                $displayDescription = $description[$locale] ?? $description[config('app.fallback_locale', 'en')] ?? '';

                // Handle features (string, array, or JSON string)
                $features = $plan->features;
                if (is_string($features)) {
                    $features = json_decode($features, true) ?: [];
                }
                
                // Ensure features is an array and process it
                $features = is_array($features) ? $features : [];
                if (isset($features[$locale])) {
                    $features = (array)$features[$locale];
                } elseif (isset($features[config('app.fallback_locale', 'en')])) {
                    $features = (array)$features[config('app.fallback_locale', 'en')];
                }
                $features = array_map('strval', $features);
                
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
            ->map(function ($post) use ($locale) {
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

        $quizzes = collect();
        
        try {
            // First try to get guest quizzes
            $quizzes = \App\Models\Quiz::where('is_active', true)
    ->where('is_guest_quiz', true)
    ->with(['questions' => function ($query) {
        $query->where('is_active', true);
    }])
    ->withCount(['questions' => function ($query) {
        $query->where('is_active', true);
    }])
    ->orderBy('created_at', 'desc')
    ->take(3)
    ->get();
                
            // If no guest quizzes found, get any active quizzes
            if ($quizzes->isEmpty()) {
                $quizzes = \App\Models\Quiz::where('is_active', true)
                    ->withCount(['questions' => function ($query) {
                        $query->where('is_active', true);
                    }])
                    ->orderBy('created_at', 'desc')
                    ->take(3)
                    ->get();
            }
            
            // Format the quizzes data
            $quizzes = $quizzes->map(function ($quiz) use ($locale) {
    try {
        $quiz->title = is_array($quiz->title) 
            ? ($quiz->title[$locale] ?? $quiz->title['en'] ?? 'Untitled Quiz')
            : $quiz->title;
            
        $quiz->description = is_array($quiz->description)
            ? ($quiz->description[$locale] ?? $quiz->description['en'] ?? '')
            : $quiz->description;
            
        return $quiz;
    } catch (\Exception $e) {
        \Illuminate\Support\Facades\Log::error('Error formatting quiz data: ' . $e->getMessage(), [
            'quiz_id' => $quiz->id ?? null,
            'error' => $e->getTraceAsString()
        ]);
        return null;
    }
})->filter(); // Remove any null entries from the collection
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Error in quizzes query: ' . $e->getMessage(), [
                'exception' => get_class($e),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);
            $quizzes = collect(); // Return empty collection on error
        }
            
        \Illuminate\Support\Facades\Log::debug('Final quiz data prepared for homepage', [
    'quizzes_count' => $quizzes->count(),
    'quizzes' => $quizzes->map(function($quiz) {
        return [
            'id' => $quiz->id,
            'title' => $quiz->title,
            'questions_count' => $quiz->questions_count,
            'is_guest_quiz' => $quiz->is_guest_quiz,
            'subscription_plan_slug' => $quiz->subscription_plan_slug,
            'created_at' => $quiz->created_at,
            'questions' => $quiz->questions->map(function($question) {
                return [
                    'id' => $question->id,
                    'text' => $question->text,
                    'options' => $question->options->map(function($option) {
                        return [
                            'id' => $option->id,
                            'option_text' => $option->option_text,
                            'is_correct' => $option->is_correct
                        ];
                    })
                ];
            })
        ];
    })
]);
            
        return view('home', [
            'plans' => $plans,
            'blogs' => $blogs,
            'quizzes' => $quizzes,
            'currentLocale' => $locale
        ]);
    })->name('home');
    
    // Alias for home
    Route::get('home', function ($locale) {
        return redirect()->route('home', ['locale' => $locale]);
    });
    
    // Public Pages
    Route::get('about', function () {
        return view('about');
    })->name('about');
    
    Route::get('contact', function () {
        return view('contact');
    })->name('contact');
    
    Route::get('privacy', function () {
        return view('privacy');
    })->name('privacy');
    
    Route::get('terms', function () {
        return view('terms');
    })->name('terms');
    
    // News routes
    Route::get('news', [\App\Http\Controllers\Web\NewsController::class, 'index'])
        ->name('news');
        
    Route::get('news/{slug}', [\App\Http\Controllers\Web\NewsController::class, 'show'])
        ->name('news.detail');
        
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
    Route::get('quizzes', [\App\Http\Controllers\Web\Dashboard\QuizController::class, 'index'])
        ->name('quizzes')
        ->middleware('auth');
        
    // Subscriptions routes (authenticated)
    Route::middleware('auth')->group(function () {
        Route::get('subscriptions', [\App\Http\Controllers\Web\SubscriptionController::class, 'index'])
            ->name('subscriptions');
            
        Route::post('subscriptions/{plan}', [\App\Http\Controllers\Web\SubscriptionController::class, 'store'])
            ->name('subscriptions.store');
    });
    
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
    Route::get('plans', function (LocaleService $localeService) {
        $locale = $localeService->getLocale();
        
        $plans = SubscriptionPlan::where('is_active', true)
            ->orderBy('price')
            ->get()
            ->map(function ($plan) use ($locale) {
                // Handle name (string or array)
                $name = $plan->name;
                if (is_string($name)) {
                    $name = json_decode($name, true) ?: [];
                }
                $displayName = $name[$locale] ?? $name[config('app.fallback_locale', 'en')] ?? 'Unnamed Plan';

                // Handle description (string or array)
                $description = $plan->description;
                if (is_string($description)) {
                    $description = json_decode($description, true) ?: [];
                }
                $displayDescription = $description[$locale] ?? $description[config('app.fallback_locale', 'en')] ?? '';

                // Handle features (string, array, or JSON string)
                $features = $plan->features;
                if (is_string($features)) {
                    $features = json_decode($features, true) ?: [];
                }
                
                // Ensure features is an array and process it
                $features = is_array($features) ? $features : [];
                if (isset($features[$locale])) {
                    $features = (array)$features[$locale];
                } elseif (isset($features[config('app.fallback_locale', 'en')])) {
                    $features = (array)$features[config('app.fallback_locale', 'en')];
                }
                $features = array_map('strval', $features);
                
                return [
                    'id' => $plan->id,
                    'slug' => $plan->slug,
                    'name' => $name,
                    'display_name' => $displayName,
                    'description' => $description,
                    'display_description' => $displayDescription,
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
            
        // User quiz routes
        Route::get('dashboard/my-quizzes', 'DashboardController@myQuizzes')
            ->name('dashboard.my-quizzes');
            
        // Quiz attempt API routes
        Route::prefix('api')->group(function () {
            // Get or create an active quiz attempt
            Route::get('quizzes/{quiz}/attempt', 'QuizAttemptController@getActiveAttempt')
                ->name('api.quizzes.attempt');
                
            // Update a quiz attempt (save progress or submit)
            Route::put('attempts/{attempt}', 'QuizAttemptController@update')
                ->name('api.attempts.update');
                
            // Get user's quiz attempts
            Route::get('my-attempts', 'QuizAttemptController@getUserAttempts')
                ->name('api.my-attempts');
                
            // Start or continue a quiz attempt
            Route::get('quizzes/{quiz}/start', 'QuizAttemptController@start')
                ->name('quizzes.attempt');
        });
        
        // Forum
        Route::get('forum', [\App\Http\Controllers\Web\ForumController::class, 'index'])->name('forum');
        
        // Dashboard Quizzes Routes
        Route::prefix('dashboard/quizzes')->name('dashboard.quizzes.')->group(function () {
            // All quizzes
            Route::get('/', [\App\Http\Controllers\Web\Dashboard\QuizController::class, 'index'])
                ->name('index');
                
            // In Progress quizzes
            Route::get('/in-progress', [\App\Http\Controllers\Web\Dashboard\QuizController::class, 'inProgress'])
                ->name('in-progress');
                
            // Completed quizzes
            Route::get('/completed', [\App\Http\Controllers\Web\Dashboard\QuizController::class, 'completed'])
                ->name('completed');
                
            // Add bookmark route
            Route::post('/{quiz}/bookmark', [\App\Http\Controllers\Web\Dashboard\QuizController::class, 'bookmark'])
                ->name('bookmark');
                
            // Update quiz attempt
            Route::put('/attempts/{attempt}', [\App\Http\Controllers\Web\Dashboard\QuizAttemptController::class, 'update'])
                ->name('attempt.update');
                
            // Quiz submission
            Route::post('/{quiz}/submit', [\App\Http\Controllers\Web\Dashboard\QuizAttemptController::class, 'update'])
                ->name('submit')
                ->where('quiz', '[0-9]+');
                
            // Quiz details
            Route::get('/{quiz}', [\App\Http\Controllers\Web\Dashboard\QuizController::class, 'show'])
                ->name('show')
                ->where('quiz', '[0-9]+');
                
            // Attempt details
            Route::get('/attempts/{attempt}', [\App\Http\Controllers\Web\Dashboard\QuizController::class, 'attemptDetails'])
                ->name('attempt.details')
                ->where('attempt', '[0-9]+');
        });
        
        // News Routes
        Route::get('news', [\App\Http\Controllers\Web\NewsController::class, 'index'])
            ->name('news.index');
            
        Route::get('news/{news:slug}', [\App\Http\Controllers\Web\NewsDetailController::class, 'show'])
            ->name('news.show');
        
            // Subscription success/cancel routes
        Route::get('subscriptions/success', [\App\Http\Controllers\Web\SubscriptionController::class, 'success'])
            ->name('subscriptions.success');
            
        Route::get('subscriptions/cancel', [\App\Http\Controllers\Web\SubscriptionController::class, 'cancel'])
            ->name('subscriptions.cancel');
            
        Route::delete('subscriptions/{subscription}', [\App\Http\Controllers\Web\SubscriptionController::class, 'destroy'])
            ->name('subscriptions.destroy');
        });

// Admin routes - moved inside locale group to support localized admin routes

// Forum Routes (already inside locale prefix)
Route::prefix('forum')
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
    }); // Close the locale prefix group

// Authentication routes (from auth.php)
require __DIR__ . '/auth.php';
