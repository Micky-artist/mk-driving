<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\SubscriptionPlan\CreateSubscriptionPlanRequest;
use App\Http\Requests\SubscriptionPlan\UpdateSubscriptionPlanRequest;
use App\Models\SubscriptionPlan;
use App\Models\Subscription;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class SubscriptionPlanController extends Controller
{
    /**
     * Create a new subscription plan (Admin only)
     */
    public function store(CreateSubscriptionPlanRequest $request): JsonResponse
    {
        $data = $request->validated();
        
        $subscriptionPlan = SubscriptionPlan::create([
            'name' => [
                'en' => $data['name_en'] ?? '',
                'rw' => $data['name_rw'] ?? $data['name_en'] ?? '',
            ],
            'description' => [
                'en' => $data['description_en'] ?? '',
                'rw' => $data['description_rw'] ?? $data['description_en'] ?? '',
            ],
            'price' => $data['price'],
            'duration' => $data['duration'],
            'features' => $data['features'] ?? [],
            'is_active' => $data['is_active'] ?? true,
            'max_quizzes' => $data['max_quizzes'] ?? null,
            'color' => $data['color'] ?? null,
        ]);

        return response()->json($subscriptionPlan, 201);
    }

    /**
     * Get all subscription plans (Public)
     */
    public function index(): JsonResponse
    {
        $plans = SubscriptionPlan::withCount(['quizzes', 'subscriptions'])
            ->latest()
            ->get();
            
        return response()->json($plans);
    }

    /**
     * Get active subscription plans (Public)
     */
    public function active(): JsonResponse
    {
        $plans = SubscriptionPlan::withCount(['quizzes', 'subscriptions'])
            ->where('is_active', true)
            ->orderBy('price')
            ->get();
            
        return response()->json($plans);
    }

    /**
     * Get revenue by plan (Admin only)
     */
    public function revenueByPlan(): JsonResponse
    {
        $revenue = Subscription::select([
                'subscription_plans.id',
                'subscription_plans.name',
                DB::raw('COUNT(subscriptions.id) as total_subscriptions'),
                DB::raw('SUM(subscription_plans.price) as total_revenue')
            ])
            ->join('subscription_plans', 'subscriptions.plan_id', '=', 'subscription_plans.id')
            ->where('subscriptions.status', 'active')
            ->groupBy(['subscription_plans.id', 'subscription_plans.name'])
            ->get();
            
        return response()->json($revenue);
    }

    /**
     * Get a specific subscription plan (Public)
     */
    public function show(string $id): JsonResponse
    {
        $plan = SubscriptionPlan::with([
                'quizzes' => function ($query) {
                    $query->select(['id', 'title', 'is_active']);
                },
                'subscriptions' => function ($query) {
                    $query->with(['user' => function ($q) {
                        $q->select(['id', 'first_name', 'last_name', 'email']);
                    }])->select(['id', 'user_id', 'status']);
                },
            ])
            ->withCount(['quizzes', 'subscriptions'])
            ->findOrFail($id);
            
        return response()->json($plan);
    }

    /**
     * Update a subscription plan (Admin only)
     */
    public function update(UpdateSubscriptionPlanRequest $request, string $id): JsonResponse
    {
        $plan = SubscriptionPlan::findOrFail($id);
        $data = $request->validated();
        
        $updateData = [
            'name' => [
                'en' => $data['name_en'] ?? $plan->name['en'] ?? '',
                'rw' => $data['name_rw'] ?? $plan->name['rw'] ?? $plan->name['en'] ?? '',
            ],
            'description' => [
                'en' => $data['description_en'] ?? $plan->description['en'] ?? '',
                'rw' => $data['description_rw'] ?? $plan->description['rw'] ?? $plan->description['en'] ?? '',
            ],
            'price' => $data['price'] ?? $plan->price,
            'duration' => $data['duration'] ?? $plan->duration,
            'features' => $data['features'] ?? $plan->features,
            'is_active' => $data['is_active'] ?? $plan->is_active,
            'max_quizzes' => $data['max_quizzes'] ?? $plan->max_quizzes,
        ];
        
        if (isset($data['color'])) {
            $updateData['color'] = $data['color'];
        }
        
        $plan->update($updateData);
        
        return response()->json($plan->loadCount(['quizzes', 'subscriptions']));
    }

    /**
     * Delete a subscription plan (Admin only)
     */
    public function destroy(string $id): JsonResponse
    {
        $plan = SubscriptionPlan::findOrFail($id);
        
        // Prevent deletion if there are active subscriptions
        if ($plan->subscriptions()->where('status', 'active')->exists()) {
            return response()->json(
                ['message' => 'Cannot delete a plan with active subscriptions'], 
                422
            );
        }
        
        $plan->delete();
        
        return response()->json(['message' => 'Subscription plan deleted successfully']);
    }

    /**
     * Subscribe to a plan (Authenticated users)
     */
    public function subscribe(string $id): JsonResponse
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['message' => 'Unauthenticated.'], 401);
        }
        $plan = SubscriptionPlan::findOrFail($id);
        
        // Check if user already has an active subscription
        $activeSubscription = $user->subscriptions()
            ->where('status', 'active')
            ->where('ends_at', '>', now())
            ->first();
            
        if ($activeSubscription) {
            return response()->json(
                ['message' => 'You already have an active subscription'], 
                422
            );
        }
        
        // Create new subscription
        $subscription = $user->subscriptions()->create([
            'plan_id' => $plan->id,
            'starts_at' => now(),
            'ends_at' => now()->addDays($plan->duration),
            'status' => 'active',
        ]);
        
        return response()->json([
            'message' => 'Successfully subscribed to the plan',
            'subscription' => $subscription->load('plan')
        ], 201);
    }
}
