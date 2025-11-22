<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\SubscriptionPlan;
use App\Models\Subscription;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class SubscriptionController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        
        // Get all active subscription plans
        $plans = SubscriptionPlan::where('is_active', true)
            ->orderBy('price')
            ->get();
            
        // Get user's active subscriptions
        $activeSubscriptions = $user->subscriptions()
            ->where('status', 'ACTIVE')
            ->where('end_date', '>', now())
            ->with('subscriptionPlan')
            ->get();
            
        // Get user's pending subscriptions
        $pendingSubscriptions = $user->subscriptions()
            ->where('status', 'PENDING')
            ->with('subscriptionPlan')
            ->get();
            
        return view('dashboard.subscriptions.index', [
            'plans' => $plans,
            'activeSubscriptions' => $activeSubscriptions,
            'pendingSubscriptions' => $pendingSubscriptions,
            'user' => $user
        ]);
    }
    
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
            
            // Create new subscription
            $subscription = $user->subscriptions()->create([
                'subscription_plan_id' => $plan->id,
                'start_date' => now(),
                'end_date' => now()->addMonths($plan->duration),
                'status' => 'PENDING', // Will be updated after payment
                'amount' => $plan->price,
            ]);
            
            // TODO: Process payment here
            // For now, we'll mark it as active immediately
            $subscription->update(['status' => 'ACTIVE']);
            
            return redirect()->route('subscriptions.success', $subscription);
        });
    }
    
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
            'end_date' => now()
        ]);
        
        return redirect()->route('subscriptions.index')
            ->with('success', 'Your subscription has been cancelled successfully.');
    }
}
