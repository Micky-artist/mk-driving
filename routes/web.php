<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
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

// Redirect root to localized home with 'rw' as default
Route::get('/', function (LocaleService $localeService) {
    return redirect()->route('home', ['locale' => 'rw']);
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
                    $displayName = $name[$locale] ?? $name[config('app.fallback_locale', 'rw')] ?? 'Unnamed Plan';

                    // Handle description (string or array)
                    $description = $plan->description;
                    if (is_string($description)) {
                        $description = json_decode($description, true) ?: [];
                    }
                    $displayDescription = $description[$locale] ?? $description[config('app.fallback_locale', 'rw')] ?? '';

                    // Handle features (string, array, or JSON string)
                    $features = $plan->features;
                    if (is_string($features)) {
                        $features = json_decode($features, true) ?: [];
                    }
                    
                    // Ensure features is an array and process it
                    $features = is_array($features) ? $features : [];
                    if (isset($features[$locale])) {
                        $features = (array)$features[$locale];
                    } elseif (isset($features[config('app.fallback_locale', 'rw')])) {
                        $features = (array)$features[config('app.fallback_locale', 'rw')];
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

            // Get recent forum questions with top answer and answers count
            $recentQuestions = \App\Models\ForumQuestion::withCount('answers')
                ->with(['answers' => function($query) {
                    // Get the top-voted answer for each question
                    $query->where('is_approved', true)
                        ->orderBy('votes', 'desc')
                        ->limit(1);
                }, 'answers.user', 'user'])
                ->where('is_approved', true)
                ->orderBy('created_at', 'desc')
                ->take(5)
                ->get()
                ->map(function($question) use ($locale) {
                    $title = $question->title;
                    $content = $question->content;
                    
                    return [
                        'id' => $question->id,
                        'title' => $title[$locale] ?? $title[config('app.fallback_locale', 'rw')] ?? 'No title',
                        'content' => $content[$locale] ?? $content[config('app.fallback_locale', 'rw')] ?? '',
                        'created_at' => $question->created_at,
                        'user' => $question->user,
                        'answers_count' => $question->answers_count,
                        'top_answer' => $question->answers->first() ? [
                            'content' => $question->answers->first()->content[$locale] ?? $question->answers->first()->content[config('app.fallback_locale', 'rw')] ?? '',
                            'user' => $question->answers->first()->user,
                            'created_at' => $question->answers->first()->created_at,
                            'votes' => $question->answers->first()->votes ?? 0
                        ] : null,
                        'topics' => $question->topics
                    ];
                });

            // Get active quizzes with relationships
            \Illuminate\Support\Facades\Log::debug('Starting quizzes query');
            $quizzes = collect();
            
            try {
                // Debug: Check if we can connect to the database
                \Illuminate\Support\Facades\DB::connection()->getPdo();
                \Illuminate\Support\Facades\Log::debug('Database connection successful');
                
                // Get guest quiz (max 1) and plan-based quizzes (up to 3)
                $guestQuiz = \App\Models\Quiz::where('is_guest_quiz', true)
                    ->where('is_active', true)
                    ->withCount('questions')
                    ->with(['questions' => function($query) {
                        $query->where('is_active', true)
                              ->with(['options'])
                              ->inRandomOrder();
                    }])
                    ->orderBy('created_at', 'desc')
                    ->first();

                // Get plan-based quizzes (non-guest quizzes)
                $planQuizzes = \App\Models\Quiz::where('is_active', true)
                    ->where('is_guest_quiz', false)  // Only non-guest quizzes
                    ->with(['questions' => function ($query) {
                        $query->where('is_active', true)
                              ->with(['options'])
                              ->inRandomOrder();
                    }])
                    ->withCount(['questions' => function ($query) {
                        $query->where('is_active', true);
                    }])
                    ->orderBy('created_at', 'desc')
                    ->take(4)  // Get up to 4 plan-based quizzes
                    ->get();

                // Combine guest quiz (if exists) with plan quizzes
                $quizzes = collect();
                if ($guestQuiz) {
                    $quizzes->push($guestQuiz);
                }
                $quizzes = $quizzes->merge($planQuizzes);

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
                'currentLocale' => $locale,
                'recentQuestions' => $recentQuestions ?? collect()
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
        $fallbackLocale = config('app.fallback_locale', 'en');
        
        $plans = SubscriptionPlan::where('is_active', true)
            ->orderBy('price')
            ->get()
            ->map(function ($plan) use ($locale, $fallbackLocale) {
                // Ensure we have the slug
                $slug = $plan->slug;
                
                // Handle name (string or array)
                $name = $plan->name;
                if (is_string($name)) {
                    $name = json_decode($name, true) ?: [];
                }
                $displayName = $name[$locale] ?? $name[$fallbackLocale] ?? 'Unnamed Plan';

                // Handle description
                $description = $plan->description;
                if (is_string($description)) {
                    $description = json_decode($description, true) ?: [];
                }
                $displayDescription = $description[$locale] ?? $description[$fallbackLocale] ?? '';

                // Handle features - ensure we always return an array for the current locale
                $features = $plan->features;
                if (is_string($features)) {
                    $features = json_decode($features, true) ?: [];
                }
                
                // Get features for current locale or fallback
                $displayFeatures = [];
                if (isset($features[$locale]) && is_array($features[$locale])) {
                    $displayFeatures = $features[$locale];
                } elseif (isset($features[$fallbackLocale]) && is_array($features[$fallbackLocale])) {
                    $displayFeatures = $features[$fallbackLocale];
                } elseif (is_array($features) && !isset($features[$locale]) && !isset($features[$fallbackLocale])) {
                    // If features is a simple array without locale keys
                    $displayFeatures = array_values($features);
                }
                
                return [
                    'id' => $plan->id,
                    'slug' => $slug,
                    'name' => $name, // Keep full name object for the component to handle
                    'display_name' => $displayName,
                    'description' => $description, // Keep full description object
                    'display_description' => $displayDescription,
                    'price' => $plan->price,
                    'duration' => $plan->duration,
                    'duration_in_days' => $plan->duration_in_days ?? 0,
                    'max_quizzes' => $plan->max_quizzes,
                    'is_active' => $plan->is_active,
                    'color' => $plan->color,
                    'features' => $features, // Keep full features object
                    'display_features' => $displayFeatures, // Add pre-formatted features for current locale
                    'is_current' => function() use ($plan) {
                        if (!\Illuminate\Support\Facades\Auth::check()) {
                            return false;
                        }
                        return \Illuminate\Support\Facades\Auth::user()->subscription_plan_id === $plan->id;
                    }
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

        // Forum Routes (protected by auth and verified middleware)
        Route::middleware(['auth', 'verified'])->group(function () {
            Route::prefix('forum')
                ->name('forum.')
                ->group(function () {
                    Route::get('/', [\App\Http\Controllers\Web\ForumController::class, 'index'])->name('index');
                    Route::get('/ask', [\App\Http\Controllers\Web\ForumController::class, 'create'])->name('create');
                    Route::post('/', [\App\Http\Controllers\Web\ForumController::class, 'store'])->name('store');
                    Route::get('/{id}', [\App\Http\Controllers\Web\ForumController::class, 'show'])->name('show');
                    
                    // Answers
                    Route::post('/{questionId}/answers', [\App\Http\Controllers\Web\ForumController::class, 'storeAnswer'])
                        ->name('answers.store');
                        
                    // Voting
                    Route::post('/{type}/{id}/vote', [\App\Http\Controllers\Web\ForumController::class, 'vote'])
                        ->name('vote');
                        
                    // Mark as best answer
                    Route::post('/{questionId}/best-answer/{answerId}', [\App\Http\Controllers\Web\ForumController::class, 'markAsBestAnswer'])
                        ->name('best-answer');
                });
        });
    }); // Close the locale prefix group

// Authentication routes (from auth.php)
require __DIR__ . '/auth.php';
