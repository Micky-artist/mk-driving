<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Web\NewsDetailController;
use App\Http\Controllers\LanguageController;
use App\Models\Blog;
use App\Http\Controllers\SitemapController;
use App\Models\SubscriptionPlan;
use App\Services\LocaleService;
use App\Http\Controllers\Web\PaymentController;
use App\Http\Middleware\TrackUserActivity;

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
                'is_current' => \Illuminate\Support\Facades\Auth::check() ? 
                              \Illuminate\Support\Facades\Auth::user()->subscriptions()
                                  ->where('subscription_plan_id', $plan->id)
                                  ->whereIn('status', ['ACTIVE', 'PENDING'])
                                  ->exists() : false,
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

// Admin routes (non-localized - moved outside locale group)
Route::prefix('admin')
    ->name('admin.')
    ->middleware(['auth', 'verified', 'can:isAdmin'])
    ->group(base_path('routes/admin.php'));

// Localized routes
Route::prefix('{locale}')
    ->where(['locale' => '[a-zA-Z]{2}'])
    ->middleware(['web', 'localize'])
    ->group(function () {
        
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
                        'max_quizzes' => $plan->max_quizzes,
                        'is_current' => \Illuminate\Support\Facades\Auth::check() ? 
                              (function() use ($plan) {
                                  $user = \Illuminate\Support\Facades\Auth::user();
                                  $hasSubscription = $user->subscriptions()
                                      ->where('subscription_plan_id', $plan->id)
                                      ->whereIn('status', ['ACTIVE', 'PENDING'])
                                      ->exists();
                                  
                                  // Debug logging
                                  \Illuminate\Support\Facades\Log::debug('Subscription check', [
                                      'plan_id' => $plan->id,
                                      'user_id' => $user->id,
                                      'has_subscription' => $hasSubscription,
                                      'user_subscriptions' => $user->subscriptions()->get(['subscription_plan_id', 'status'])->toArray()
                                  ]);
                                  
                                  return $hasSubscription;
                              })() : false,
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

            // Get forum data using ForumService
            \Illuminate\Support\Facades\Log::debug('Attempting to get forum data');
            $forumService = app(\App\Services\ForumService::class);
            \Illuminate\Support\Facades\Log::debug('ForumService instantiated');
            $forumData = $forumService->getHomepageData($locale, 3);
            \Illuminate\Support\Facades\Log::debug('Forum data retrieved', ['forumData' => $forumData]);

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

                // Format quiz data for unified component
                $quizzes = $quizzes->map(function($quiz) use ($locale) {
                    return [
                        'id' => $quiz->id,
                        'title' => $quiz->getTranslation('title', $locale),
                        'description' => $quiz->getTranslation('description', $locale),
                        'time_limit_minutes' => $quiz->time_limit_minutes,
                        'is_guest_quiz' => $quiz->is_guest_quiz,
                        'questions' => $quiz->questions->map(function($question) use ($locale) {
                            return [
                                'id' => $question->id,
                                'text' => $question->getTranslation('text', $locale),
                                'image_path' => $question->image_path ? asset('storage/' . $question->image_path) : null,
                                'options' => $question->options->map(function($option) use ($locale) {
                                    return [
                                        'id' => $option->id,
                                        'text' => $option->getTranslation('option_text', $locale),
                                        'is_correct' => (bool)$option->is_correct,
                                        'explanation' => $option->getTranslation('explanation', $locale)
                                    ];
                                })->toArray()
                            ];
                        })->toArray()
                    ];
                });

                // Log the actual number of questions loaded for each quiz
                foreach ($quizzes as $quiz) {
                    \Illuminate\Support\Facades\Log::debug('Quiz questions loaded', [
                        'quiz_id' => $quiz['id'],
                        'quiz_title' => $quiz['title'],
                        'questions_count' => count($quiz['questions']),
                        'questions_loaded' => count($quiz['questions']),
                        'sample_question' => !empty($quiz['questions']) ? 'exists' : 'none'
                    ]);
                }
                
                // Log basic quiz info for debugging
                \Illuminate\Support\Facades\Log::debug('Homepage - Quizzes loaded:', [
                    'count' => $quizzes->count(),
                    'quiz_ids' => $quizzes->pluck('id'),
                    'has_questions' => $quizzes->every(fn($q) => !empty($q['questions']))
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
                'forumData' => $forumData
            ]);
        })->name('home')->middleware(TrackUserActivity::class);
        
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
    Route::get('plans', [\App\Http\Controllers\Web\PlansController::class, 'index'])->name('plans');
        
    // Protected routes (require authentication)
    Route::middleware(['auth', 'verified'])
        ->namespace('App\Http\Controllers\Dashboard')
        ->group(function () {
            // Quiz attempt API routes
        Route::prefix('api')->group(function () {
            // Get or create an active quiz attempt - requires subscription
            Route::get('quizzes/{quiz}/attempt', 'QuizAttemptController@getActiveAttempt')
                ->name('api.quizzes.attempt')
                ->middleware('check.subscription');
                
            // Update a quiz attempt (save progress or submit) - requires subscription
            Route::put('attempts/{attempt}', 'QuizAttemptController@update')
                ->name('api.attempts.update')
                ->middleware('check.subscription');
                
            // Get user's quiz attempts - allow viewing history
            Route::get('my-attempts', 'QuizAttemptController@getUserAttempts')
                ->name('api.my-attempts');
                
            // Start or continue a quiz attempt - requires subscription
            Route::get('quizzes/{quiz}/start', 'QuizAttemptController@start')
                ->name('quizzes.attempt')
                ->middleware('check.subscription');
        });
        
                
            // Subscription success/cancel routes
        Route::get('subscriptions/success', [\App\Http\Controllers\Web\SubscriptionController::class, 'success'])
            ->name('subscriptions.success');
            
        Route::get('subscriptions/cancel', [\App\Http\Controllers\Web\SubscriptionController::class, 'cancel'])
            ->name('subscriptions.cancel');
            
        Route::delete('subscriptions/{subscription}', [\App\Http\Controllers\Web\SubscriptionController::class, 'destroy'])
            ->name('subscriptions.destroy');
        });

        // Forum Routes (public viewing, auth required for interactions)
        Route::prefix('forum')
            ->name('forum.')
            ->group(function () {
                // Public routes - can view without authentication
                Route::get('/', [\App\Http\Controllers\Web\ForumController::class, 'index'])->name('index');
                Route::get('/{id}', [\App\Http\Controllers\Web\ForumController::class, 'show'])->name('show');
            });
        
        // Forum Routes (protected by auth and verified middleware - for interactions)
        Route::middleware(['auth', 'verified'])->group(function () {
            Route::prefix('forum')
                ->name('forum.')
                ->group(function () {
                    Route::get('/ask', [\App\Http\Controllers\Web\ForumController::class, 'create'])->name('create');
                    Route::post('/', [\App\Http\Controllers\Web\ForumController::class, 'store'])->name('store');
                    
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
        
        // Ask Question Routes (new clean routes to avoid existing issues)
        Route::get('/ask', [\App\Http\Controllers\Web\AskController::class, 'index'])->name('ask.question');
        Route::post('/ask', [\App\Http\Controllers\Web\AskController::class, 'store'])->name('ask.store');
    }); // Close the locale prefix group

// Authentication routes (from auth.php)
require __DIR__ . '/auth.php';

// Dashboard routes
require __DIR__ . '/dashboard.php';
