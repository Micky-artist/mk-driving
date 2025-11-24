<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\ChatbotController;
use App\Http\Controllers\API\ForumController;
use App\Http\Controllers\API\AdminController;
use App\Http\Controllers\API\NewsController;
use App\Http\Controllers\API\SearchController;
use App\Http\Controllers\API\SubscriptionPlanController;
use App\Http\Controllers\API\SubscriptionController;
use App\Http\Controllers\API\UploadController;
use App\Http\Controllers\API\UserController;
use App\Http\Controllers\API\BookmarkController;
use App\Http\Controllers\API\QuizAttemptController;

// Public routes
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// Public routes
Route::get('/forum/questions', [ForumController::class, 'getQuestions']);
Route::get('/news', [NewsController::class, 'index']);
Route::get('/news/by-slug/{slug}', [NewsController::class, 'showBySlug']);

// Search routes
Route::get('/search', [SearchController::class, 'globalSearch']);
Route::get('/search/quizzes', [SearchController::class, 'searchQuizzes']);
Route::get('/search/news', [SearchController::class, 'searchNews']);

// Public subscription plans routes
Route::get('/subscription-plans', [SubscriptionPlanController::class, 'index']);
Route::get('/subscription-plans/active', [SubscriptionPlanController::class, 'active']);
Route::get('/subscription-plans/{id}', [SubscriptionPlanController::class, 'show']);

// Public subscription routes
Route::post('/subscriptions/simulate-payment', [SubscriptionController::class, 'simulatePayment']);

// MTN Mobile Money payment routes
Route::prefix('payments')->group(function () {
    // Initiate MTN Mobile Money payment
    Route::post('/initiate', [\App\Http\Controllers\Api\PaymentController::class, 'initiatePayment'])->name('payment.initiate');
    
    // Check payment status
    Route::get('/status/{reference}', [\App\Http\Controllers\Api\PaymentController::class, 'checkStatus'])->name('payment.status');
    
    // MTN Mobile Money webhook for payment notifications
    Route::post('/webhook/mtn', [\App\Http\Controllers\Api\PaymentController::class, 'handleWebhook'])->name('payment.webhook.mtn');
});

// Bookmark routes
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/quizzes/{quiz}/bookmark', [BookmarkController::class, 'toggle']);
    Route::get('/quizzes/{quiz}/bookmark/check', [BookmarkController::class, 'check']);
    Route::get('/bookmarks', [BookmarkController::class, 'index']);
    
    // Test route to check if bookmarks are working
    Route::get('/test-bookmark/{quiz}', function($quizId) {
        $user = auth()->user();
        $isBookmarked = $user->bookmarks()->where('quiz_id', $quizId)->exists();
        
        return response()->json([
            'user_id' => $user->id,
            'quiz_id' => $quizId,
            'is_bookmarked' => $isBookmarked,
            'bookmarks' => $user->bookmarks->pluck('quiz_id')
        ]);
    });
});

// Public upload routes
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/upload/image', [UploadController::class, 'uploadImage']);
    Route::post('/upload/document', [UploadController::class, 'uploadDocument']);
    Route::delete('/upload/delete', [UploadController::class, 'deleteFile']);
    Route::post('/upload/bulk-delete', [UploadController::class, 'bulkDeleteFiles']);

    // User routes
    Route::get('/users', [UserController::class, 'index']);
    Route::get('/users/subscribers', [UserController::class, 'subscribers']);
    Route::get('/users/stats', [UserController::class, 'stats']);
    Route::get('/users/profile', [UserController::class, 'profile']);
    Route::patch('/users/profile', [UserController::class, 'updateProfile']);
    Route::patch('/users/{user}', [UserController::class, 'update']);
    Route::delete('/users/{user}', [UserController::class, 'destroy']);
});

// Protected routes
Route::middleware('auth:sanctum')->group(function () {
    // Authentication routes
    Route::get('/user', [AuthController::class, 'user']);
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::post('/change-password', [AuthController::class, 'changePassword']);
    
    // Chatbot routes
    Route::prefix('chatbot')->group(function () {
        Route::get('/questions', [ChatbotController::class, 'getPredefinedQuestions']);
        Route::post('/message', [ChatbotController::class, 'handleMessage']);
        Route::get('/conversations', [ChatbotController::class, 'getConversationHistory']);
    });
    
    // Quiz Attempts
    Route::post('/quizzes/start', [QuizAttemptController::class, 'start']);
    Route::get('/attempts/{attemptId}', [QuizAttemptController::class, 'getAttempt']);
    Route::put('/attempts/{attemptId}', [QuizAttemptController::class, 'updateAttempt']);

    // Forum routes
    Route::prefix('forum')->group(function () {
        Route::post('/questions', [ForumController::class, 'storeQuestion']);
        Route::delete('/questions/{id}', [ForumController::class, 'deleteQuestion']);
        Route::post('/questions/{questionId}/answers', [ForumController::class, 'storeAnswer']);
    });

    // Admin routes
    Route::prefix('admin')->middleware(['auth:sanctum', 'admin'])->group(function () {
        // Dashboard
        Route::get('/stats', [AdminController::class, 'getDashboardStats']);
        Route::get('/revenue/plans', [AdminController::class, 'getRevenueByPlan']);
        
        // User management
        Route::get('/users', [AdminController::class, 'getAllUsers']);
        Route::get('/users/{id}', [AdminController::class, 'getUser']);
        Route::put('/users/{id}', [AdminController::class, 'updateUser']);
        Route::delete('/users/{id}', [AdminController::class, 'deleteUser']);
        
        // Subscription management
        Route::put('/subscriptions/{id}', [AdminController::class, 'updateSubscription']);
        Route::delete('/subscriptions/{id}', [AdminController::class, 'deleteSubscription']);
        Route::get('/subscriptions', [AdminController::class, 'getAllSubscriptions']);
        Route::get('/subscriptions/{id}', [AdminController::class, 'getSubscription']);
        
        // News management
        Route::apiResource('news', NewsController::class, ['names' => [
            'show' => 'api.news.show',
            'store' => 'api.news.store',
            'update' => 'api.news.update',
            'destroy' => 'api.news.destroy'
        ]])->except(['index']);
        
        // Admin search routes
        Route::get('search/users', [SearchController::class, 'searchUsers']);
        Route::get('search/subscription-plans', [SearchController::class, 'searchSubscriptionPlans']);
        
        // Admin subscription plans routes
        Route::apiResource('subscription-plans', SubscriptionPlanController::class)->except(['index', 'show']);
        Route::get('subscription-plans/revenue/stats', [SubscriptionPlanController::class, 'revenueByPlan']);
        
        // Subscription routes
        Route::prefix('subscriptions')->group(function () {
            // User subscription management
            Route::post('/request', [SubscriptionController::class, 'requestSubscription']);
            Route::get('/', [SubscriptionController::class, 'getUserSubscriptions']);
            Route::get('/active', [SubscriptionController::class, 'getActiveUserSubscriptions']);
            Route::get('/pending', [SubscriptionController::class, 'getPendingUserSubscriptions']);
            Route::get('/check-quiz-access/{quizId}', [SubscriptionController::class, 'checkQuizAccess']);
            
            // Admin subscription management
            Route::middleware('role:admin')->group(function () {
                Route::get('/all', [SubscriptionController::class, 'getSubscribers']);
                Route::get('/revenue', [SubscriptionController::class, 'getRevenueStats']);
                Route::post('/admin-subscribe', [SubscriptionController::class, 'adminSubscribe']);
                Route::post('/{id}/cancel', [SubscriptionController::class, 'cancelSubscription']);
                Route::get('/{id}', [SubscriptionController::class, 'getSubscription']);
            });
        });
        
        // User subscription to plans
        Route::post('subscription-plans/{id}/subscribe', [SubscriptionPlanController::class, 'subscribe']);
    });
});
