<?php

namespace App\Http\Controllers;

use App\Models\Quiz;
use App\Models\SubscriptionPlan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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
        
        // Eager load relationships
        $user->load(['subscriptions.plan', 'quizAttempts.quiz']);
        
        // Get user's current subscriptions from loaded relationship
        $currentSubscriptions = $user->subscriptions
            ->where('ends_at', '>=', now())
            ->values();

        // Get available subscription plans
        $availablePlans = SubscriptionPlan::where('is_active', true)
            ->orderBy('price')
            ->get();

        // Get user's quiz attempts from loaded relationship
        $quizAttempts = $user->quizAttempts->sortByDesc('created_at');

        // Calculate stats based on user's subscription access
        $accessiblePlanIds = $currentSubscriptions->pluck('subscription_plan_id')->unique();
        $accessiblePlanIds->push(null); // Include guest quizzes (subscription_plan_id = null)
        
        $totalQuizzes = Quiz::where('is_active', true)
            ->where(function ($query) use ($accessiblePlanIds) {
                $query->whereIn('subscription_plan_id', $accessiblePlanIds)
                      ->orWhereNull('subscription_plan_id'); // Include guest quizzes
            })
            ->count();
        $completedQuizzes = $quizAttempts->where('status', 'COMPLETED');
        $inProgressQuizzes = $quizAttempts->where('status', 'IN_PROGRESS');
        
        $stats = [
            'total_quizzes' => $totalQuizzes,
            'completed_count' => $completedQuizzes->count(),
            'in_progress_count' => $inProgressQuizzes->count(),
            'average_score' => round($completedQuizzes->avg('score') ?? 0, 1),
        ];

        // Get new quizzes (not attempted by user)
        $attemptedQuizIds = $quizAttempts->pluck('quiz_id');
        $newQuizzes = Quiz::where('is_active', true)
            ->whereNotIn('id', $attemptedQuizIds)
            ->with('subscriptionPlan')
            ->take(3)
            ->get();

        // Calculate subscription stats using the loaded relationship
        $subscriptions = $user->subscriptions;
        $subscriptionStats = [
            'total' => $subscriptions->count(),
            'active' => $subscriptions->where('ends_at', '>=', now())->count(),
            'pending' => $subscriptions->where('status', 'pending')->count(),
        ];

        return view('dashboard.index', [
            'currentSubscriptions' => $currentSubscriptions,
            'availablePlans' => $availablePlans,
            'newQuizzes' => $newQuizzes,
            'inProgressQuizzes' => $inProgressQuizzes->take(3),
            'completedQuizzes' => $completedQuizzes->take(3),
            'stats' => $stats,
            'subscriptionStats' => $subscriptionStats,
        ]);
    }
}
