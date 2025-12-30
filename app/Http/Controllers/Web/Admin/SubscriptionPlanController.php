<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use App\Models\SubscriptionPlan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

class SubscriptionPlanController extends Controller
{
    /**
     * Display a listing of subscription plans.
     *
     * @return \Illuminate\View\View
     */
    public function index(): View
    {
        $plans = SubscriptionPlan::withCount(['subscriptions' => function($query) {
                $query->where('status', 'active');
            }])
            ->orderBy('price')
            ->paginate(20);
            
        $stats = [
            'total_plans' => SubscriptionPlan::count(),
            'active_plans' => SubscriptionPlan::where('is_active', true)->count(),
            'inactive_plans' => SubscriptionPlan::where('is_active', false)->count(),
            'total_subscriptions' => \App\Models\Subscription::count(),
            'revenue' => \App\Models\Subscription::where('status', 'active')
                ->whereMonth('created_at', now()->month)
                ->sum('amount') ?? 0,
        ];
        
        return view('admin.subscription-plans.index', compact('plans', 'stats'));
    }

    /**
     * Display the specified subscription plan.
     *
     * @param  \App\Models\SubscriptionPlan  $subscriptionPlan
     * @return \Illuminate\View\View
     */
    public function show(SubscriptionPlan $subscriptionPlan): View
    {
        $subscriptionPlan->loadCount('subscriptions');
        
        $stats = [
            'total' => $subscriptionPlan->subscriptions()->count(),
            'active' => $subscriptionPlan->subscriptions()
                ->where('status', 'active')
                ->where(function($query) {
                    $query->where('ends_at', '>', now())
                          ->orWhereNull('ends_at');
                })
                ->count(),
            'pending' => $subscriptionPlan->subscriptions()
                ->where('status', 'pending')
                ->count(),
            'cancelled' => $subscriptionPlan->subscriptions()
                ->where('status', 'cancelled')
                ->count(),
            'revenue' => $subscriptionPlan->subscriptions()
                ->where('status', 'active')
                ->sum('amount') ?? 0,
        ];
        
        return view('admin.subscription-plans.manage', [
            'plan' => $subscriptionPlan,
            'subscriptions' => $subscriptionPlan->subscriptions()
                ->where('status', 'active')
                ->where(function($query) {
                    $query->where('ends_at', '>', now())
                          ->orWhereNull('ends_at');
                })
                ->latest()
                ->paginate(10),
            'stats' => $stats
        ]);
    }

    /**
     * Show the form for creating a new subscription plan.
     *
     * @return \Illuminate\View\View
     */
    public function create(): View
    {
        return view('admin.subscription-plans.create');
    }

    /**
     * Store a newly created subscription plan.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'duration_in_days' => 'required|integer|min:1',
            'features' => 'nullable|array',
            'is_active' => 'boolean',
        ]);

        try {
            SubscriptionPlan::create($validated);
            return redirect()->route('admin.subscription-plans.index')
                ->with('success', 'Subscription plan created successfully.');
        } catch (\Exception $e) {
            Log::error('Failed to create subscription plan: ' . $e->getMessage());
            return back()->withInput()->with('error', 'Failed to create subscription plan.');
        }
    }

    /**
     * Show the form for editing the specified subscription plan.
     *
     * @param  \App\Models\SubscriptionPlan  $subscriptionPlan
     * @return \Illuminate\View\View
     */
    public function edit(SubscriptionPlan $subscriptionPlan): View
    {
        return view('admin.subscription-plans.edit', compact('subscriptionPlan'));
    }

    /**
     * Update the specified subscription plan.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\SubscriptionPlan  $subscriptionPlan
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, SubscriptionPlan $subscriptionPlan)
    {
        $validated = $request->validate([
            'name' => 'required|array|max:255',
            'name.en' => 'required|string|max:255',
            'name.rw' => 'required|string|max:255',
            'description' => 'required|array',
            'description.en' => 'required|string',
            'description.rw' => 'required|string',
            'price' => 'required|numeric|min:0',
            'duration' => 'required|integer|min:1',
            'max_quizzes' => 'required|integer|min:1',
            'color' => 'nullable|string|max:7',
            'is_active' => 'boolean',
        ]);

        try {
            $subscriptionPlan->update($validated);
            return redirect()->route('admin.subscription-plans.index')
                ->with('success', 'Subscription plan updated successfully.');
        } catch (\Exception $e) {
            Log::error('Failed to update subscription plan: ' . $e->getMessage());
            return back()->withInput()->with('error', 'Failed to update subscription plan.');
        }
    }

    /**
     * Remove the specified subscription plan.
     *
     * @param  \App\Models\SubscriptionPlan  $subscriptionPlan
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(SubscriptionPlan $subscriptionPlan)
    {
        try {
            // Prevent deletion if there are active subscriptions
            if ($subscriptionPlan->subscriptions()->exists()) {
                return back()->with('error', 'Cannot delete plan with active subscriptions.');
            }

            $subscriptionPlan->delete();
            return redirect()->route('admin.subscription-plans.index')
                ->with('success', 'Subscription plan deleted successfully.');
        } catch (\Exception $e) {
            Log::error('Failed to delete subscription plan: ' . $e->getMessage());
            return back()->with('error', 'Failed to delete subscription plan.');
        }
    }

    /**
     * Toggle the active status of a subscription plan.
     *
     * @param  \App\Models\SubscriptionPlan  $subscriptionPlan
     * @return \Illuminate\Http\RedirectResponse
     */
    public function toggleStatus(SubscriptionPlan $subscriptionPlan)
    {
        try {
            $subscriptionPlan->update(['is_active' => !$subscriptionPlan->is_active]);
            $status = $subscriptionPlan->is_active ? 'activated' : 'deactivated';
            return back()->with('success', "Subscription plan {$status} successfully.");
        } catch (\Exception $e) {
            Log::error('Failed to toggle subscription plan status: ' . $e->getMessage());
            return back()->with('error', 'Failed to update subscription plan status.');
        }
    }
}
