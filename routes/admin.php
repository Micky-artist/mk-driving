<?php

use App\Http\Controllers\Web\Admin\AdminController;
use App\Http\Controllers\Web\Admin\UserController;
use App\Http\Controllers\Web\Admin\SubscriptionPlanController;
use App\Http\Controllers\Web\Admin\SubscriptionController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Web\Admin\NewsController;
use App\Http\Controllers\Web\Admin\GuestQuizController;
use App\Http\Controllers\Web\Admin\ForumController;
use App\Http\Controllers\Web\Admin\ReportController;
use Illuminate\Support\Facades\Route;

// Admin routes group - middleware, prefix and name are applied in web.php
Route::group([], function () {
        // Admin Portal
        Route::get('/', [AdminController::class, 'dashboard'])->name('portal'); // This will be 'admin.portal' due to the route group prefix

        // Subscription Management
        Route::get('/subscriptions/manage', [SubscriptionController::class, 'manage'])->name('subscriptions.manage');
        Route::get('/subscriptions/manage/{plan}', [SubscriptionController::class, 'managePlan'])->name('subscriptions.manage.plan');
        
        // Profile Management
        Route::prefix('profile')->name('profile.')->group(function () {
            Route::get('/', [ProfileController::class, 'edit'])->name('edit');
            Route::put('/', [ProfileController::class, 'update'])->name('update');
            Route::put('/password', [ProfileController::class, 'updatePassword'])->name('password');
        });

        // User Management
        Route::get('users/all', [UserController::class, 'all'])
            ->name('users.all');
        Route::get('users/unregistered-visits', [UserController::class, 'unregisteredVisits'])
            ->name('users.unregistered-visits');
        Route::get('users/recent-activity', [UserController::class, 'recentActivity'])
            ->name('users.recent-activity');
                Route::resource('users', UserController::class)->names('users');
        Route::post('users/{user}/suspend', [UserController::class, 'suspend'])
            ->name('users.suspend');
        // User Activation
        Route::patch('users/{user}/activate', [UserController::class, 'activate'])
            ->name('users.activate');
        Route::post('users/{user}/change-password', [UserController::class, 'changePassword'])
            ->name('users.change-password');
        // Admin Role Management
        Route::patch('users/{user}/make-admin', [UserController::class, 'makeAdmin'])
            ->name('users.make-admin');
        Route::patch('users/{user}/remove-admin', [UserController::class, 'removeAdmin'])
            ->name('users.remove-admin');

        // Subscription Plans Management
        Route::resource('subscription-plans', SubscriptionPlanController::class)->names('subscription-plans');
        
        // Subscription Management
        Route::get('subscriptions', [SubscriptionController::class, 'index'])->name('subscriptions.index');
        Route::get('subscriptions/published', [SubscriptionController::class, 'published'])->name('subscriptions.published');
        Route::get('subscriptions/pending', [SubscriptionController::class, 'pending'])->name('subscriptions.pending');
        Route::get('subscriptions/active', [SubscriptionController::class, 'active'])->name('subscriptions.active');
        Route::resource('subscriptions', SubscriptionController::class)->names('subscriptions')->except(['index', 'pending']);
            
        // Subscription approval routes
        Route::post('subscriptions/{subscription}/approve', [SubscriptionController::class, 'approve'])
            ->name('subscriptions.approve');
        Route::post('subscriptions/{subscription}/reject', [SubscriptionController::class, 'reject'])
            ->name('subscriptions.reject');
            
        // Other subscription routes
        Route::post('subscriptions/{subscription}/cancel', [SubscriptionController::class, 'cancel'])
            ->name('subscriptions.cancel');
        Route::get('subscriptions/stats', [SubscriptionController::class, 'stats'])
            ->name('subscriptions.stats');
            
        // News Management
        Route::prefix('news')->name('news.')->group(function () {
            Route::get('/', [NewsController::class, 'index'])->name('index');
            Route::get('/create', [NewsController::class, 'create'])->name('create');
            Route::post('/', [NewsController::class, 'store'])->name('store');
            Route::get('/{news}/edit', [NewsController::class, 'edit'])->name('edit');
            Route::put('/{news}', [NewsController::class, 'update'])->name('update');
            Route::delete('/{news}', [NewsController::class, 'destroy'])->name('destroy');
            Route::delete('/{id}/remove-image/{imageIndex}', [NewsController::class, 'removeImage'])->name('remove-image');
        });

        // Settings
        Route::prefix('settings')->name('settings.')->group(function () {
            Route::get('/', [AdminController::class, 'settings'])->name('index');
            Route::post('/', [AdminController::class, 'updateSettings'])->name('update');
        });

        // Notifications
        Route::prefix('notifications')->name('notifications.')->group(function () {
            Route::post('/{id}/read', [AdminController::class, 'markNotificationAsRead'])->name('mark-read');
            Route::post('/mark-all-read', [AdminController::class, 'markAllNotificationsAsRead'])->name('mark-all-read');
        });

        // Guest Quiz Management
        Route::prefix('guest-quiz')->name('guest-quiz.')->group(function () {
            Route::get('/', [GuestQuizController::class, 'index'])->name('index');
            Route::post('/{quiz}/set', [GuestQuizController::class, 'setGuestQuiz'])->name('set');
        });

        // Quiz Management
        Route::resource('quizzes', \App\Http\Controllers\Web\Admin\QuizController::class)->names('quizzes');
        Route::post('quizzes/{quiz}/assign-plans', [\App\Http\Controllers\Web\Admin\QuizController::class, 'assignPlans'])->name('quizzes.assign-plans');
        
        // Test routes for list and drafts views
        Route::get('test/quizzes/list', [\App\Http\Controllers\Web\Admin\QuizController::class, 'list'])->name('test.quizzes.list');
        Route::get('test/quizzes/drafts', [\App\Http\Controllers\Web\Admin\QuizController::class, 'drafts'])->name('test.quizzes.drafts');
        
        // Questions Management
        Route::resource('questions', \App\Http\Controllers\Web\Admin\QuestionController::class)->names('questions');
        
        // Step-by-step Quiz Creation
        Route::post('quizzes/initialize', [\App\Http\Controllers\Web\Admin\QuizController::class, 'initializeQuiz'])->name('quizzes.initialize');
        Route::get('quizzes/create/question/{step}', [\App\Http\Controllers\Web\Admin\QuizController::class, 'createQuestion'])->name('quizzes.create.question');
        Route::post('quizzes/create/question/{step}', [\App\Http\Controllers\Web\Admin\QuizController::class, 'storeQuestion'])->name('quizzes.create.question.store');
        Route::get('quizzes/create/review', [\App\Http\Controllers\Web\Admin\QuizController::class, 'reviewQuiz'])->name('quizzes.create.review');
        Route::post('quizzes/create/complete', [\App\Http\Controllers\Web\Admin\QuizController::class, 'completeQuiz'])->name('quizzes.create.complete');
        
        // Quiz Draft Management
        Route::post('quizzes/save-draft', [\App\Http\Controllers\Web\Admin\QuizController::class, 'saveDraft'])->name('quizzes.save-draft');
        Route::get('quizzes/load-draft', [\App\Http\Controllers\Web\Admin\QuizController::class, 'loadDraft'])->name('quizzes.load-draft');
        Route::delete('quizzes/delete-draft', [\App\Http\Controllers\Web\Admin\QuizController::class, 'deleteDraft'])->name('quizzes.delete-draft');
        
                
        // Quiz Attempts Management
        Route::prefix('quiz-attempts')->name('quiz.attempts.')->group(function () {
            Route::get('/', [\App\Http\Controllers\Web\Admin\QuizAttemptController::class, 'index'])->name('index');
            Route::get('/{attempt}', [\App\Http\Controllers\Web\Admin\QuizAttemptController::class, 'show'])->name('show');
            Route::delete('/{attempt}', [\App\Http\Controllers\Web\Admin\QuizAttemptController::class, 'destroy'])->name('destroy');
        });
        
        // Quiz Analytics
        Route::get('/quiz-analytics', [\App\Http\Controllers\Web\Admin\QuizAnalyticsController::class, 'index'])->name('quiz.analytics');

        // Forum Management
        Route::prefix('forum')->name('forum.')->group(function () {
            Route::get('/', [ForumController::class, 'index'])->name('index');
            Route::get('/leaderboard', [ForumController::class, 'leaderboard'])->name('leaderboard');
            
            // Moderation
            Route::prefix('moderation')->name('moderation.')->group(function () {
                Route::get('/', [ForumController::class, 'moderationIndex'])->name('index');
                Route::get('/pending-answers', [ForumController::class, 'pendingAnswers'])->name('pending-answers');
                Route::post('/answers/{answer}/approve', [ForumController::class, 'approveAnswer'])->name('approve-answer');
                Route::post('/answers/{answer}/reject', [ForumController::class, 'rejectAnswer'])->name('reject-answer');
                Route::get('/reported-content', [ForumController::class, 'reportedContent'])->name('reported-content');
                Route::post('/reports/{report}/resolve', [ForumController::class, 'resolveReport'])->name('resolve-report');
                Route::post('/reports/{report}/dismiss', [ForumController::class, 'dismissReport'])->name('dismiss-report');
            });
            
            Route::delete('/{question}', [ForumController::class, 'destroy'])->name('destroy');
            Route::post('/{question}/answers', [ForumController::class, 'storeAnswer'])->name('answers.store');
            Route::delete('/answers/{answer}', [ForumController::class, 'destroyAnswer'])->name('answers.destroy');
            Route::patch('/{question}/toggle-approval', [ForumController::class, 'toggleApproval'])->name('toggle-approval');
            
            // Announcement Management
            Route::prefix('announcements')->name('announcement.')->group(function () {
                Route::get('/create', [ForumController::class, 'createAnnouncement'])->name('create');
                Route::post('/', [ForumController::class, 'storeAnnouncement'])->name('store');
                Route::get('/{announcement}/edit', [ForumController::class, 'editAnnouncement'])->name('edit');
                Route::put('/{announcement}', [ForumController::class, 'updateAnnouncement'])->name('update');
                Route::delete('/{announcement}', [ForumController::class, 'destroyAnnouncement'])->name('destroy');
            });
        });

        // Reports & Analytics
        Route::prefix('reports')->name('reports.')->group(function () {
            Route::get('/', [ReportController::class, 'index'])->name('index');
            Route::get('/user-performance', [ReportController::class, 'userPerformance'])->name('user-performance');
            Route::get('/revenue', [ReportController::class, 'revenue'])->name('revenue');
            Route::get('/usage', [ReportController::class, 'usage'])->name('usage');
            Route::get('/content-effectiveness', [ReportController::class, 'contentEffectiveness'])->name('content-effectiveness');
            Route::get('/export/{type}', [ReportController::class, 'export'])->name('export');
            Route::get('/visitors/export', [ReportController::class, 'exportVisitors'])->name('visitors.export');
        });

        // Visitor Analytics - Redirect to Reports
        Route::prefix('visitors')->name('visitors.')->group(function () {
            Route::get('/', function() {
                return redirect()->route('admin.reports.index');
            })->name('index');
            Route::post('/track', [\App\Http\Controllers\Web\Admin\VisitorController::class, 'track'])->name('track');
            Route::get('/analytics', [\App\Http\Controllers\Web\Admin\VisitorController::class, 'analytics'])->name('analytics');
            Route::get('/export', function() {
                return redirect()->route('admin.reports.visitors.export');
            })->name('export');
        });
    });