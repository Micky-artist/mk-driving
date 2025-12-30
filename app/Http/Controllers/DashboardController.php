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
        $user->load(['subscriptions.plan', 'quizAttempts.quiz']);
        
        // Get user's current subscriptions from loaded relationship
        $currentSubscriptions = $user->subscriptions
            ->where('status', 'ACTIVE')
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
        
        // Add all lower-tier plans for hierarchical access
        $hierarchicalPlans = $this->getHierarchicalPlanAccess($accessiblePlanSlugs);
        
        Log::info('Quiz access calculation', [
            'user_id' => $user->id,
            'user_plan_slugs' => $accessiblePlanSlugs->toArray(),
            'hierarchical_access_slugs' => $hierarchicalPlans->toArray(),
            'has_active_subscription' => $currentSubscriptions->count() > 0,
            'is_admin' => $user->isAdmin()
        ]);
        
        // Admin users can see all quizzes
        if ($user->isAdmin()) {
            $totalQuizzes = Quiz::where('is_active', true)->count();
        } else {
            $totalQuizzes = Quiz::where('is_active', true)
                ->where(function ($query) use ($hierarchicalPlans) {
                    // Check quizzes with subscription_plan_slug (including hierarchical access)
                    $query->whereIn('subscription_plan_slug', $hierarchicalPlans)
                          // Check guest quizzes (no subscription required)
                          ->orWhereNull('subscription_plan_slug')
                          // Check quizzes accessible through many-to-many relationship
                          ->orWhereHas('subscriptionPlans', function ($subQuery) use ($hierarchicalPlans) {
                              $subQuery->whereIn('slug', $hierarchicalPlans);
                          });
                })
                ->count();
        }

        Log::info('Total quizzes calculated', [
            'user_id' => $user->id,
            'total_quizzes' => $totalQuizzes,
            'accessible_plan_slugs' => $accessiblePlanSlugs->toArray()
        ]);
            
        $completedQuizzes = $quizAttempts->where('status', 'COMPLETED');
        $inProgressQuizzes = $quizAttempts->where('status', 'IN_PROGRESS');
        
        // Calculate test readiness with partial progress
        $completedQuizzesForReadiness = $quizAttempts->where('status', 'COMPLETED');
        $inProgressQuizzesForReadiness = $quizAttempts->where('status', 'IN_PROGRESS');
        
        // Calculate total quiz equivalents (partial progress counts as fraction)
        $totalQuizEquivalents = 0;
        $totalScore = 0;
        $scoreCount = 0;
        
        // Process completed quizzes (each counts as 1 full quiz)
        foreach ($completedQuizzesForReadiness as $completed) {
            $totalQuizEquivalents += 1;
            
            // Use the score field if available, otherwise calculate from answers
            if ($completed->score) {
                $totalScore += $completed->score;
            } else {
                // Calculate score from answers JSON
                $score = $this->calculateScoreFromAnswers($completed->answers);
                $totalScore += $score;
            }
            $scoreCount++;
        }
        
        // Process in-progress quizzes (count as fraction based on progress)
        foreach ($inProgressQuizzesForReadiness as $inProgress) {
            if ($inProgress->answers && is_array($inProgress->answers) && count($inProgress->answers) > 0) {
                $answeredQuestions = count($inProgress->answers);
                $totalQuestions = $inProgress->quiz->questions->count() ?? 20; // Default to 20 if not set
                
                if ($totalQuestions > 0) {
                    // This quiz counts as a fraction of a full quiz
                    $quizFraction = $answeredQuestions / $totalQuestions;
                    $totalQuizEquivalents += $quizFraction;
                    
                    // Calculate partial score
                    $partialScore = $this->calculateScoreFromAnswers($inProgress->answers);
                    $totalScore += $partialScore;
                    $scoreCount++;
                }
            }
        }
        
        $averageScore = $scoreCount > 0 ? round($totalScore / $scoreCount, 1) : 0;
        
        // Calculate readiness percentage based on 25-quiz baseline
        $readinessPercentage = 0;
        if ($totalQuizEquivalents >= 25) {
            // User has completed equivalent of 25+ quizzes
            $readinessPercentage = min(100, round(($averageScore / 60) * 100));
        } elseif ($totalQuizEquivalents > 0) {
            // Partial readiness - scale based on how close to 25 quizzes and score
            $testCountFactor = min(1, $totalQuizEquivalents / 25); // Progress toward 25 quizzes (0 to 1)
            $scoreFactor = min(1, $averageScore / 60); // Score factor relative to 60% (0 to 1)
            $readinessPercentage = round($testCountFactor * $scoreFactor * 100);
        }
        
        $readinessData = [
            'percentage' => $readinessPercentage,
            'average_score' => $averageScore,
            'total_tests' => round($totalQuizEquivalents, 1), // Show quiz equivalents
            'quiz_equivalents' => $totalQuizEquivalents, // Raw number for debugging
            'is_ready' => $readinessPercentage >= 100,
            'getting_ready' => $readinessPercentage >= 60 && $totalQuizEquivalents >= 25,
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

    /**
     * Calculate score percentage from answers JSON
     */
    private function calculateScoreFromAnswers($answers): float
    {
        if (!is_array($answers) || empty($answers)) {
            return 0;
        }

        $totalQuestions = count($answers);
        $correctAnswers = 0;

        foreach ($answers as $questionId => $answer) {
            if (is_array($answer) && isset($answer['is_correct']) && $answer['is_correct']) {
                $correctAnswers++;
            }
        }

        return $totalQuestions > 0 ? round(($correctAnswers / $totalQuestions) * 100, 1) : 0;
    }
    
    /**
     * Get hierarchical plan access - higher tiers get access to lower tiers
     */
    private function getHierarchicalPlanAccess($userPlanSlugs): \Illuminate\Support\Collection
    {
        // Define hierarchy from lowest to highest
        $planHierarchy = [
            'basic-plan',
            'standard-plan', 
            'premium-plan',
            'gold-unlimited-plan'
        ];
        
        $hierarchicalAccess = collect();
        
        foreach ($userPlanSlugs as $planSlug) {
            $planIndex = array_search($planSlug, $planHierarchy);
            
            if ($planIndex !== false) {
                // Add this plan and all lower-tier plans
                for ($i = 0; $i <= $planIndex; $i++) {
                    $hierarchicalAccess->push($planHierarchy[$i]);
                }
            }
        }
        
        return $hierarchicalAccess->unique();
    }
}
