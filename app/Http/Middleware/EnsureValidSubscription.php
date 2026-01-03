<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Models\Subscription;
use App\Models\Quiz;
use App\Enums\SubscriptionStatus;
use Carbon\Carbon;

class EnsureValidSubscription
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        if (!Auth::check()) {
            return $next($request);
        }

        $user = Auth::user();
        
        // Always check and update expired subscriptions in real-time
        $this->updateExpiredSubscriptions($user);
        
        // Get quiz from route if accessing specific quiz
        $quiz = $this->getQuizFromRoute($request);
        
        // Validate access based on quiz requirements
        if (!$this->hasValidAccess($user, $quiz)) {
            return $this->handleAccessDenied($request);
        }
        
        return $next($request);
    }
    
    /**
     * Get quiz from current route if available
     */
    private function getQuizFromRoute(Request $request): ?Quiz
    {
        $quizId = $request->route('quiz');
        
        if ($quizId) {
            return Quiz::find($quizId);
        }
        
        return null;
    }
    
    /**
     * Check if user has valid access for the requested content
     */
    private function hasValidAccess($user, ?Quiz $quiz): bool
    {
        // If no specific quiz, allow access (for listing pages like /dashboard/quizzes)
        if (!$quiz) {
            return true;
        }
        
        // Admins can access all quizzes
        if ($user && $user->isAdmin()) {
            return true;
        }
        
        // Guest quizzes are accessible to everyone
        if ($quiz->is_guest_quiz) {
            return true;
        }
        
        // For premium quizzes, check if user has active subscription for the quiz's plan
        if ($quiz->subscription_plan_slug) {
            $hasSubscription = $this->hasActiveSubscriptionForPlan($user, $quiz->subscription_plan_slug);
            if (!$hasSubscription) {
                return false;
            }
        }
        
        // Check if user has reached their quiz limit
        // Only enforce for new quiz attempts, not for viewing existing attempts
        if ($user && $user->hasReachedQuizLimit()) {
            // Check if user has an existing attempt for this quiz
            $hasExistingAttempt = $user->quizAttempts()
                ->where('quiz_id', $quiz->id)
                ->where('status', 'in_progress')
                ->exists();
            
            // Allow access if they have an existing attempt, block if starting new
            return $hasExistingAttempt;
        }
        
        // If quiz has no plan requirement, allow access
        return true;
    }
    
    /**
     * Check if user has any active subscription
     */
    private function hasActiveSubscription($user): bool
    {
        return $user->subscriptions()
            ->where('status', SubscriptionStatus::ACTIVE->value)
            ->where('starts_at', '<=', now())
            ->where('ends_at', '>', now())
            ->exists();
    }
    
    /**
     * Check if user has active subscription for specific plan (with hierarchical access)
     */
    private function hasActiveSubscriptionForPlan($user, string $planSlug): bool
    {
        // Get user's current subscriptions
        $currentSubscriptions = $user->subscriptions()
            ->where('status', SubscriptionStatus::ACTIVE->value)
            ->where('ends_at', '>', now())
            ->with('subscriptionPlan')
            ->get();
        
        $accessiblePlanSlugs = $currentSubscriptions->pluck('subscriptionPlan.slug')->filter()->unique();
        $hierarchicalPlans = $this->getHierarchicalPlanAccess($accessiblePlanSlugs);

        // Check if user has hierarchical access to the required plan
        return $hierarchicalPlans->contains($planSlug);
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
    
    /**
     * Handle access denied scenarios
     */
    private function handleAccessDenied(Request $request)
    {
        $locale = app()->getLocale();
        
        // For AJAX requests, return JSON error
        if ($request->expectsJson()) {
            return response()->json([
                'success' => false,
                'message' => __('subscription.required_for_quiz_message')
            ], 403);
        }
        
        // For regular requests, redirect with flash message
        return redirect()
            ->route('dashboard', ['locale' => $locale])
            ->with('error', __('subscription.required_for_quiz_message'));
    }
    
    /**
     * Update expired subscriptions to expired status
     */
    private function updateExpiredSubscriptions($user)
    {
        $now = Carbon::now();
        
        // Update all expired subscriptions (remove time restriction for robustness)
        $expiredSubscriptions = $user->subscriptions()
            ->where('status', SubscriptionStatus::ACTIVE->value)
            ->where('ends_at', '<=', $now)
            ->get();
            
        foreach ($expiredSubscriptions as $subscription) {
            $subscription->update([
                'status' => SubscriptionStatus::EXPIRED->value,
                'updated_at' => $now
            ]);
            
            // Log the expiration
            Log::info('Subscription automatically expired', [
                'subscription_id' => $subscription->id,
                'user_id' => $user->id,
                'plan_id' => $subscription->subscription_plan_id,
                'plan_slug' => $subscription->plan->slug ?? 'unknown',
                'expired_at' => $subscription->ends_at,
                'checked_at' => $now,
                'time_since_expiry' => $now->diffInSeconds($subscription->ends_at)
            ]);
        }
    }
}
