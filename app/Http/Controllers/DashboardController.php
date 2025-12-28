<?php

namespace App\Http\Controllers;

use App\Models\Quiz;
use App\Models\SubscriptionPlan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Show the application dashboard.
     */
    public function index(): View
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        
        Log::info('Dashboard access', [
            'user_id' => $user->id,
            'user_email' => $user->email,
            'timestamp' => now()->toIso8601String()
        ]);
        
        // Eager load relationships
        $user->load(['subscriptions.plan', 'quizAttempts.quiz', 'quizAttempts.userAnswers']);
        
        // Get user's current subscriptions from loaded relationship
        $currentSubscriptions = $user->subscriptions
            ->where('ends_at', '>=', now())
            ->values();

        Log::info('User subscriptions loaded', [
            'user_id' => $user->id,
            'total_subscriptions' => $user->subscriptions->count(),
            'current_subscriptions' => $currentSubscriptions->count(),
            'subscription_details' => $currentSubscriptions->map(function ($sub) {
                return [
                    'plan_id' => $sub->subscription_plan_id,
                    'plan_slug' => $sub->plan->slug ?? null,
                    'plan_name' => $sub->plan->name ?? null,
                    'status' => $sub->status,
                    'ends_at' => $sub->ends_at?->toIso8601String(),
                ];
            })->toArray()
        ]);

        // Get available subscription plans
        $availablePlans = SubscriptionPlan::where('is_active', true)
            ->orderBy('price')
            ->get();

        // Get user's quiz attempts from loaded relationship
        $quizAttempts = $user->quizAttempts->sortByDesc('created_at');

        // Calculate stats based on user's subscription access
        $accessiblePlanSlugs = $currentSubscriptions->pluck('plan.slug')->filter()->unique();
        
        Log::info('Quiz access calculation', [
            'user_id' => $user->id,
            'accessible_plan_slugs' => $accessiblePlanSlugs->toArray(),
            'has_active_subscription' => $currentSubscriptions->count() > 0
        ]);
        
        $totalQuizzes = Quiz::where('is_active', true)
            ->where(function ($query) use ($accessiblePlanSlugs) {
                // Check quizzes with subscription_plan_slug
                $query->whereIn('subscription_plan_slug', $accessiblePlanSlugs)
                      // Check guest quizzes (no subscription required)
                      ->orWhereNull('subscription_plan_slug')
                      // Check quizzes accessible through many-to-many relationship
                      ->orWhereHas('subscriptionPlans', function ($subQuery) use ($accessiblePlanSlugs) {
                          $subQuery->whereIn('slug', $accessiblePlanSlugs);
                      });
            })
            ->count();

        Log::info('Total quizzes calculated', [
            'user_id' => $user->id,
            'total_quizzes' => $totalQuizzes,
            'accessible_plan_slugs' => $accessiblePlanSlugs->toArray()
        ]);
            
        $completedQuizzes = $quizAttempts->where('status', 'COMPLETED');
        $inProgressQuizzes = $quizAttempts->where('status', 'IN_PROGRESS');
        
        // Calculate test readiness
        $completedQuizzesForReadiness = $quizAttempts->where('status', 'COMPLETED');
        $inProgressQuizzesForReadiness = $quizAttempts->where('status', 'IN_PROGRESS');
        
        // Include both completed and in-progress attempts that have answers
        $allAttemptsWithAnswers = $quizAttempts->filter(function($attempt) {
            return $attempt->userAnswers && $attempt->userAnswers->count() > 0;
        });
        
        $totalAttemptsWithAnswers = $allAttemptsWithAnswers->count();
        $totalCompletedTests = $completedQuizzesForReadiness->count();
        
        // Calculate average score from completed quizzes, and partial scores from in-progress
        $totalScore = 0;
        $scoreCount = 0;
        
        foreach ($completedQuizzesForReadiness as $completed) {
            $totalScore += $completed->score ?? 0;
            $scoreCount++;
        }
        
        foreach ($inProgressQuizzesForReadiness as $inProgress) {
            if ($inProgress->userAnswers && $inProgress->userAnswers->count() > 0) {
                $correctAnswers = $inProgress->userAnswers->where('is_correct', true)->count();
                $totalQuestions = $inProgress->userAnswers->count();
                $partialScore = $totalQuestions > 0 ? ($correctAnswers / $totalQuestions) * 100 : 0;
                $totalScore += $partialScore;
                $scoreCount++;
            }
        }
        
        $averageScore = $scoreCount > 0 ? round($totalScore / $scoreCount, 1) : 0;
        
        // Calculate readiness percentage (60% threshold with at least 25 tests)
        $readinessPercentage = 0;
        if ($totalAttemptsWithAnswers >= 25) {
            $readinessPercentage = min(100, round(($averageScore / 60) * 100));
        } elseif ($totalAttemptsWithAnswers > 0) {
            // Partial readiness for users with fewer than 25 tests
            $testCountFactor = $totalAttemptsWithAnswers / 25; // How close they are to 25 tests
            $scoreFactor = min(100, ($averageScore / 60) * 100); // Score factor relative to 60%
            $readinessPercentage = round($testCountFactor * $scoreFactor);
        }
        
        $readinessData = [
            'percentage' => $readinessPercentage,
            'average_score' => $averageScore,
            'total_tests' => $totalAttemptsWithAnswers,
            'is_ready' => $readinessPercentage >= 100,
            'getting_ready' => $readinessPercentage >= 60 && $totalAttemptsWithAnswers >= 25,
        ];

        $stats = [
            'total_quizzes' => $totalQuizzes,
            'completed_count' => $completedQuizzes->count(),
            'in_progress_count' => $inProgressQuizzes->count(),
            'average_score' => round($completedQuizzes->avg('score') ?? 0, 1),
        ];

        Log::info('Quiz stats calculated', [
            'user_id' => $user->id,
            'stats' => $stats,
            'total_attempts' => $quizAttempts->count(),
            'completed_attempts' => $completedQuizzes->count(),
            'in_progress_attempts' => $inProgressQuizzes->count()
        ]);

        // Get new quizzes (not attempted by user) that user has access to
        $attemptedQuizIds = $quizAttempts->pluck('quiz_id');
        $newQuizzes = Quiz::where('is_active', true)
            ->whereNotIn('id', $attemptedQuizIds)
            ->where(function ($query) use ($accessiblePlanSlugs) {
                // Check quizzes with subscription_plan_slug
                $query->whereIn('subscription_plan_slug', $accessiblePlanSlugs)
                      // Check guest quizzes (no subscription required)
                      ->orWhereNull('subscription_plan_slug')
                      // Check quizzes accessible through many-to-many relationship
                      ->orWhereHas('subscriptionPlans', function ($subQuery) use ($accessiblePlanSlugs) {
                          $subQuery->whereIn('slug', $accessiblePlanSlugs);
                      });
            })
            ->with('subscriptionPlan')
            ->take(3)
            ->get();

        Log::info('New quizzes calculated', [
            'user_id' => $user->id,
            'attempted_quiz_ids' => $attemptedQuizIds->toArray(),
            'new_quizzes_count' => $newQuizzes->count(),
            'new_quizzes' => $newQuizzes->map(function ($quiz) {
                return [
                    'id' => $quiz->id,
                    'title' => $quiz->title,
                    'subscription_plan_slug' => $quiz->subscription_plan_slug,
                ];
            })->toArray()
        ]);

        // Calculate subscription stats using the loaded relationship
        $subscriptions = $user->subscriptions;
        $subscriptionStats = [
            'total' => $subscriptions->count(),
            'active' => $subscriptions->where('ends_at', '>=', now())->count(),
            'pending' => $subscriptions->where('status', 'pending')->count(),
        ];

        Log::info('Final dashboard data', [
            'user_id' => $user->id,
            'subscription_stats' => $subscriptionStats,
            'quiz_stats' => $stats,
            'new_quizzes_count' => $newQuizzes->count(),
            'in_progress_quizzes_count' => $inProgressQuizzes->count(),
            'completed_quizzes_count' => $completedQuizzes->count()
        ]);

        return view('dashboard.index', [
            'currentSubscriptions' => $currentSubscriptions,
            'availablePlans' => $availablePlans,
            'newQuizzes' => $newQuizzes,
            'inProgressQuizzes' => $inProgressQuizzes,
            'completedQuizzes' => $completedQuizzes->take(3),
            'stats' => $stats,
            'subscriptionStats' => $subscriptionStats,
            'readinessData' => $readinessData,
        ]);
    }
}
