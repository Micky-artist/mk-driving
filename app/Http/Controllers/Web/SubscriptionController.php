<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Subscription;
use App\Models\SubscriptionPlan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class SubscriptionController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $currentLocale = app()->getLocale();
        
        // Get all active subscription plans with formatted data for the component
        $plans = SubscriptionPlan::where('is_active', true)
            ->orderBy('price')
            ->get()
            ->map(function($plan) use ($currentLocale, $user) {
                // These are already arrays due to the model's $casts
                $name = $plan->name ?? [];
                $description = $plan->description ?? [];
                
                return [
                    'id' => $plan->id,
                    'slug' => $plan->slug,
                    'name' => $name,
                    'display_name' => $name[$currentLocale] ?? $name['en'] ?? 'Unnamed Plan',
                    'description' => $description,
                    'display_description' => $description[$currentLocale] ?? $description['en'] ?? '',
                    'price' => $plan->price,
                    'duration' => $plan->duration,
                    'features' => $plan->features,
                    'is_current' => $user->subscriptions()
                        ->where('subscription_plan_id', $plan->id)
                        ->whereIn('status', ['ACTIVE', 'PENDING'])
                        ->exists(),
                ];
            });
            
        return view('subscriptions.index', [
            'plans' => $plans,
            'user' => $user
        ]);
    }
    
/**
     * Show a specific subscription plan
     *
     * @param SubscriptionPlan $plan
     * @return \Illuminate\View\View
     */
    public function show(SubscriptionPlan $plan)
    {
        return view('subscriptions.show', [
            'plan' => $plan
        ]);
    }
    
    public function store(Request $request, SubscriptionPlan $plan)
    {
        $user = $request->user();
        
        // Check if user already has an active subscription for this plan
        $existingSubscription = $user->subscriptions()
            ->where('subscription_plan_id', $plan->id)
            ->whereIn('status', ['ACTIVE', 'PENDING'])
            ->exists();
            
        if ($existingSubscription) {
            return redirect()->back()->with('error', 'You already have an active or pending subscription for this plan.');
        }
        
        // Start database transaction
        return DB::transaction(function () use ($user, $plan) {
            // Cancel any existing active subscriptions
            $user->subscriptions()
                ->where('status', 'ACTIVE')
                ->update(['status' => 'CANCELLED']);
            
            // Calculate end date based on plan duration
            $endDate = null;
            if ($plan->duration > 0) {
                $endDate = now()->addDays($plan->duration);
            } // For unlimited (duration = 0), end_date remains null
            
            // Create new subscription
            $subscription = $user->subscriptions()->create([
                'subscription_plan_id' => $plan->id,
                'start_date' => now(),
                'ends_at' => $endDate,
                'status' => 'PENDING', // Will be updated after payment
                'amount' => $plan->price,
            ]);
            
            // TODO: Process payment here
            // For now, we'll mark it as active immediately
            $subscription->update(['status' => 'ACTIVE']);
            
            return redirect()->route('subscriptions.success', $subscription);
        });
    }
    
/**
     * Show subscription success page
     *
     * @param Subscription $subscription
     * @return \Illuminate\View\View
     */
    public function success(Subscription $subscription)
    {
        if ($subscription->user_id !== auth()->id()) {
            abort(403);
        }
        
        return view('subscriptions.success', [
            'subscription' => $subscription->load('subscriptionPlan')
        ]);
    }
    
    /**
     * Cancel a subscription
     */
/**
     * Cancel a subscription
     *
     * @param Subscription $subscription
     * @return \Illuminate\Http\RedirectResponse
     */
    public function cancel(Subscription $subscription)
    {
        if ($subscription->user_id !== auth()->id()) {
            abort(403);
        }
        
        // Only allow cancelling active subscriptions
        if ($subscription->status !== 'ACTIVE') {
            return redirect()->back()->with('error', 'Only active subscriptions can be cancelled.');
        }
        
        $subscription->update([
            'status' => 'CANCELLED',
            'ends_at' => now()
        ]);
        
        return redirect()->route('subscriptions.index')
            ->with('success', 'Your subscription has been cancelled successfully.');
    }

    /**
     * Subscribe to a plan
     *
     * @param SubscriptionPlan $plan
     * @return \Illuminate\Http\RedirectResponse
     */
    public function subscribe(SubscriptionPlan $plan)
    {
        $user = auth()->user();
        
        // Check if user already has an active subscription for this plan
        $existingSubscription = $user->subscriptions()
            ->where('subscription_plan_id', $plan->id)
            ->whereIn('status', ['ACTIVE', 'PENDING'])
            ->exists();
            
        if ($existingSubscription) {
            return redirect()->back()->with('error', 'You already have an active or pending subscription for this plan.');
        }
        
        return $this->store(new Request(), $plan);
    }

    /**
     * Remove the specified subscription
     *
     * @param  \App\Models\Subscription  $subscription
     * @return \Illuminate\Http\Response
     */
    public function destroy(Subscription $subscription)
    {
        if ($subscription->user_id !== auth()->id()) {
            abort(403);
        }
        
        // Only allow deleting cancelled or expired subscriptions
        if (!in_array($subscription->status, ['CANCELLED', 'EXPIRED'])) {
            return redirect()->back()->with('error', 'Only cancelled or expired subscriptions can be deleted.');
        }
        
        $subscription->delete();
        
        return redirect()->route('subscriptions.index')
            ->with('success', 'Subscription removed successfully.');
    }
}
