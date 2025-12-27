<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\SubscriptionPlan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

class PlansController extends Controller
{
    /**
     * Display the plans page.
     */
    public function index(): View
    {
        error_log('=== PLANS CONTROLLER HIT ===');
        Log::debug('PLANS CONTROLLER - Starting execution');
        
        // Get user subscriptions if authenticated
        $userSubscriptions = collect([]);
        if (Auth::check()) {
            Log::debug('PLANS CONTROLLER - User is authenticated, loading subscriptions', [
                'user_id' => Auth::id(),
                'user_email' => Auth::user()->email
            ]);
            
            try {
                // Use the same approach as DashboardController
                $user = Auth::user();
                $user->load(['subscriptions.plan']);
                $userSubscriptions = $user->subscriptions->sortByDesc('created_at');
                
                Log::debug('PLANS CONTROLLER - Subscriptions loaded successfully', [
                    'user_id' => Auth::id(),
                    'subscription_count' => $userSubscriptions->count(),
                    'subscriptions_data' => $userSubscriptions->map(function ($sub) {
                        return [
                            'id' => $sub->id,
                            'plan_id' => $sub->subscription_plan_id,
                            'plan_name' => $sub->plan->name ?? null,
                            'status' => $sub->status,
                            'ends_at' => $sub->ends_at?->toIso8601String(),
                        ];
                    })->toArray()
                ]);
            } catch (\Exception $e) {
                Log::error('PLANS CONTROLLER - Error loading subscriptions', [
                    'error' => $e->getMessage(),
                    'user_id' => Auth::id(),
                    'trace' => $e->getTraceAsString()
                ]);
                $userSubscriptions = collect([]);
            }
        } else {
            Log::debug('PLANS CONTROLLER - User is not authenticated');
        }
        
        // Get active subscription plans
        $plans = SubscriptionPlan::where('is_active', true)
            ->orderBy('price')
            ->get()
            ->map(function ($plan) use ($userSubscriptions) {
                // Check if user has an active subscription for this plan
                $isCurrent = false;
                if (Auth::check() && $userSubscriptions->count() > 0) {
                    $isCurrent = $userSubscriptions->contains(function ($subscription) use ($plan) {
                        return $subscription->subscription_plan_id === $plan->id && 
                               $subscription->status === 'ACTIVE';
                    });
                }
                
                // Handle plan name localization
                $nameData = $plan->name;
                if (is_string($nameData)) {
                    $nameData = json_decode($nameData, true) ?: [];
                }
                $displayName = $nameData[app()->getLocale()] ?? $nameData['en'] ?? $nameData['rw'] ?? 'Plan';
                
                // Handle plan description localization
                $descriptionData = $plan->description;
                if (is_string($descriptionData)) {
                    $descriptionData = json_decode($descriptionData, true) ?: [];
                }
                $displayDescription = $descriptionData[app()->getLocale()] ?? $descriptionData['en'] ?? $descriptionData['rw'] ?? '';
                
                // Handle features localization
                $featuresData = $plan->features;
                if (is_string($featuresData)) {
                    $featuresData = json_decode($featuresData, true) ?: [];
                }
                $displayFeatures = [];
                if (isset($featuresData[app()->getLocale()]) && is_array($featuresData[app()->getLocale()])) {
                    $displayFeatures = $featuresData[app()->getLocale()];
                } elseif (isset($featuresData['en']) && is_array($featuresData['en'])) {
                    $displayFeatures = $featuresData['en'];
                } elseif (isset($featuresData['rw']) && is_array($featuresData['rw'])) {
                    $displayFeatures = $featuresData['rw'];
                } elseif (is_array($featuresData) && !isset($featuresData[app()->getLocale()])) {
                    $displayFeatures = array_values($featuresData);
                }
                
                return [
                    'id' => $plan->id,
                    'slug' => $plan->slug,
                    'name' => $plan->name,
                    'description' => $plan->description,
                    'price' => $plan->price,
                    'duration' => $plan->duration,
                    'duration_in_days' => $plan->duration_in_days ?? 0,
                    'max_quizzes' => $plan->max_quizzes,
                    'is_active' => $plan->is_active,
                    'color' => $plan->color,
                    'features' => $plan->features,
                    'is_current' => $isCurrent,
                    'display_name' => $displayName,
                    'display_description' => $displayDescription,
                    'display_features' => $displayFeatures,
                ];
            });
            
        Log::debug('PLANS CONTROLLER - Plans loaded', [
            'plans_count' => $plans->count(),
            'userSubscriptions_count' => $userSubscriptions->count()
        ]);
        
        return view('plans.index', [
            'plans' => $plans,
            'userSubscriptions' => $userSubscriptions
        ]);
    }
}
