<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use App\Models\SubscriptionPlan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class SubscriptionPlanController extends Controller
{
    /**
     * Display a listing of the subscription plans.
     */
    public function index()
    {
        $plans = SubscriptionPlan::latest()->paginate(10);
        
        return view('admin.subscription-plans.index', compact('plans'));
    }

    /**
     * Show the form for creating a new subscription plan.
     */
    public function create()
    {
        return view('admin.subscription-plans.create');
    }

    /**
     * Store a newly created subscription plan in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|array',
            'name.en' => 'required|string|max:255',
            'name.rw' => 'required|string|max:255',
            'description' => 'required|array',
            'description.en' => 'required|string',
            'description.rw' => 'required|string',
            'price' => 'required|numeric|min:0',
            'duration' => 'required|integer|min:1',
            'features' => 'required|array',
            'features.*' => 'string',
            'is_active' => 'boolean',
            'max_quizzes' => 'required|integer|min:0',
            'color' => 'required|string|max:50',
        ]);

        $plan = SubscriptionPlan::create($validated);

        return redirect()->route('admin.subscription-plans.index')
            ->with('success', 'Subscription plan created successfully.');
    }

    /**
     * Display the specified subscription plan.
     */
    public function show(SubscriptionPlan $subscriptionPlan)
    {
        return view('admin.subscription-plans.show', compact('subscriptionPlan'));
    }

    /**
     * Show the form for editing the specified subscription plan.
     */
    public function edit(SubscriptionPlan $subscriptionPlan)
    {
        return view('admin.subscription-plans.edit', compact('subscriptionPlan'));
    }

    /**
     * Update the specified subscription plan in storage.
     */
    public function update(Request $request, SubscriptionPlan $subscriptionPlan)
    {
        $validated = $request->validate([
            'name' => 'required|array',
            'name.en' => 'required|string|max:255',
            'name.rw' => 'required|string|max:255',
            'description' => 'required|array',
            'description.en' => 'required|string',
            'description.rw' => 'required|string',
            'price' => 'required|numeric|min:0',
            'duration' => 'required|integer|min:1',
            'features' => 'required|array',
            'features.*' => 'string',
            'is_active' => 'boolean',
            'max_quizzes' => 'required|integer|min:0',
            'color' => 'required|string|max:50',
        ]);

        $subscriptionPlan->update($validated);

        return redirect()->route('admin.subscription-plans.index')
            ->with('success', 'Subscription plan updated successfully.');
    }

    /**
     * Remove the specified subscription plan from storage.
     */
    public function destroy(SubscriptionPlan $subscriptionPlan)
    {
        $subscriptionPlan->delete();

        return redirect()->route('admin.subscription-plans.index')
            ->with('success', 'Subscription plan deleted successfully.');
    }
}
