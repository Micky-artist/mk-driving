<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Subscription\SimulatePaymentRequest;
use App\Http\Requests\Subscription\AdminSubscribeRequest;
use App\Models\Subscription;
use App\Models\SubscriptionPlan;
use App\Models\Quiz;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SubscriptionController extends Controller
{
    /**
     * Handle subscription request
     */
    public function requestSubscription(Request $request): JsonResponse
    {
        // Log the incoming request data
        \Log::info('Subscription request received', [
            'user_id' => auth()->id(),
            'request_data' => $request->all(),
            'headers' => $request->headers->all()
        ]);

        $request->validate([
            'plan_id' => 'required|exists:subscription_plans,id',
            'phone_number' => 'required|string|min:10|max:15',
            'payment_method' => 'required|in:mtn_mobile_money,card,bank_transfer',
        ]);

        $user = auth()->user();
        $plan = SubscriptionPlan::findOrFail($request->plan_id);

        // Check if user already has an active subscription for this plan
        $existingSubscription = $user->subscriptions()
            ->where('subscription_plan_id', $plan->id)
            ->whereIn('status', ['ACTIVE', 'PENDING'])
            ->first();

        if ($existingSubscription) {
            return response()->json([
                'message' => 'You already have an active or pending subscription for this plan.',
                'subscription' => $existingSubscription
            ], 422);
        }

        try {
            // Start database transaction
            return DB::transaction(function () use ($user, $plan, $request) {
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
                    'status' => 'PENDING',
                    'amount' => $plan->price,
                    'payment_method' => $request->payment_method,
                    'phone_number' => $request->phone_number,
                    'metadata' => [
                        'ip_address' => $request->ip(),
                        'user_agent' => $request->userAgent(),
                    ]
                ]);

                // Log the created subscription
                \Log::info('Subscription created', [
                    'subscription_id' => $subscription->id,
                    'user_id' => $user->id,
                    'plan_id' => $plan->id,
                    'amount' => $plan->price,
                    'status' => 'PENDING'
                ]);

                // TODO: Process payment here
                // For now, we'll mark it as pending and handle payment asynchronously
                
                return response()->json([
                    'message' => 'Subscription request received and is being processed.',
                    'subscription' => $subscription->load('subscriptionPlan')
                ]);
            });
        } catch (\Exception $e) {
            \Log::error('Subscription request failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'message' => 'Failed to process subscription request. Please try again.',
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }

    /**
    /**
     * Simulate a payment for a subscription
     */
    public function simulatePayment(SimulatePaymentRequest $request): JsonResponse
    {
        $user = Auth::user();
        $data = $request->validated();
        
        $plan = SubscriptionPlan::findOrFail($data['plan_id']);
        
        // Check if user already has an active subscription for this plan
        $existingSubscription = $user->subscriptions()
            ->where('plan_id', $plan->id)
            ->where('status', 'active')
            ->where('ends_at', '>', now())
            ->first();
            
        if ($existingSubscription) {
            return response()->json([
                'message' => 'You already have an active subscription for this plan',
                'subscription' => $existingSubscription
            ], 422);
        }
        
        // Create a new subscription
        $subscription = $user->subscriptions()->create([
            'plan_id' => $plan->id,
            'status' => 'active',
            'starts_at' => now(),
            'ends_at' => now()->addDays($plan->duration),
            'payment_method' => $data['payment_method'],
            'payment_reference' => $data['payment_reference'],
            'amount' => $data['amount'],
            'metadata' => [
                'simulated' => true,
                'simulated_at' => now()->toDateTimeString(),
            ]
        ]);
        
        return response()->json([
            'message' => 'Payment simulated successfully',
            'subscription' => $subscription->load('plan')
        ]);
    }
    
    /**
     * Get authenticated user's subscriptions
     */
    public function getUserSubscriptions(): JsonResponse
    {
        $user = Auth::user();
        $subscriptions = $user->subscriptions()
            ->with('plan')
            ->latest()
            ->get();
            
        return response()->json($subscriptions);
    }
    
    /**
     * Get authenticated user's active subscriptions
     */
    public function getActiveUserSubscriptions(): JsonResponse
    {
        $user = Auth::user();
        $subscriptions = $user->subscriptions()
            ->where('status', 'active')
            ->where('ends_at', '>', now())
            ->with('plan')
            ->latest()
            ->get();
            
        return response()->json($subscriptions);
    }
    
    /**
     * Get authenticated user's pending subscriptions
     */
    public function getPendingUserSubscriptions(): JsonResponse
    {
        $user = Auth::user();
        $subscriptions = $user->subscriptions()
            ->where('status', 'pending')
            ->with('plan')
            ->latest()
            ->get();
            
        return response()->json($subscriptions);
    }
    
    /**
     * Check if user has access to a specific quiz
     */
    public function checkQuizAccess(string $quizId): JsonResponse
    {
        $user = Auth::user();
        $quiz = Quiz::findOrFail($quizId);
        
        // If user is admin, they have access to all quizzes
        if ($user->isAdmin()) {
            return response()->json(['has_access' => true]);
        }
        
        // Check if quiz is free
        if (!$quiz->requires_subscription) {
            return response()->json(['has_access' => true]);
        }
        
        // Check if user has an active subscription that includes this quiz
        $hasAccess = $user->subscriptions()
            ->where('status', 'active')
            ->where('ends_at', '>', now())
            ->whereHas('plan', function($query) use ($quiz) {
                $query->whereHas('quizzes', function($q) use ($quiz) {
                    $q->where('quizzes.id', $quiz->id);
                });
            })
            ->exists();
            
        return response()->json(['has_access' => $hasAccess]);
    }
    
    /**
     * Admin: Get all subscribers
     */
    public function getSubscribers(): JsonResponse
    {
        $subscriptions = Subscription::with(['user', 'plan'])
            ->latest()
            ->paginate(20);
            
        return response()->json($subscriptions);
    }
    
    /**
     * Admin: Get revenue statistics
     */
    public function getRevenueStats(): JsonResponse
    {
        $stats = [
            'total_revenue' => Subscription::sum('amount'),
            'active_subscriptions' => Subscription::where('status', 'active')
                ->where('ends_at', '>', now())
                ->count(),
            'monthly_revenue' => Subscription::where('status', 'active')
                ->where('starts_at', '>=', now()->startOfMonth())
                ->sum('amount'),
            'revenue_by_plan' => Subscription::select([
                    'subscription_plans.name',
                    DB::raw('COUNT(subscriptions.id) as subscription_count'),
                    DB::raw('SUM(subscriptions.amount) as total_revenue')
                ])
                ->join('subscription_plans', 'subscriptions.plan_id', '=', 'subscription_plans.id')
                ->where('subscriptions.status', 'active')
                ->groupBy('subscription_plans.name')
                ->get(),
        ];
        
        return response()->json($stats);
    }
    
    /**
     * Admin: Subscribe a user to a plan
     */
    public function adminSubscribe(AdminSubscribeRequest $request): JsonResponse
    {
        $data = $request->validated();
        
        $user = User::findOrFail($data['user_id']);
        $plan = SubscriptionPlan::findOrFail($data['plan_id']);
        
        // Check if user already has an active subscription for this plan
        $existingSubscription = $user->subscriptions()
            ->where('plan_id', $plan->id)
            ->where('status', 'active')
            ->where('ends_at', '>', now())
            ->first();
            
        if ($existingSubscription) {
            return response()->json([
                'message' => 'User already has an active subscription for this plan',
                'subscription' => $existingSubscription
            ], 422);
        }
        
        // Calculate start and end dates
        $startsAt = $data['starts_at'] ?? now();
        $endsAt = $data['ends_at'] ?? Carbon::parse($startsAt)->addDays($plan->duration);
        
        // Create the subscription
        $subscription = $user->subscriptions()->create([
            'plan_id' => $plan->id,
            'status' => $data['status'] ?? 'active',
            'starts_at' => $startsAt,
            'ends_at' => $endsAt,
            'payment_method' => $data['payment_method'],
            'payment_reference' => $data['payment_reference'] ?? null,
            'amount' => $data['amount'] ?? $plan->price,
            'notes' => $data['notes'] ?? 'Admin created subscription',
            'metadata' => [
                'created_by_admin' => true,
                'admin_id' => Auth::id(),
            ]
        ]);
        
        return response()->json([
            'message' => 'Subscription created successfully',
            'subscription' => $subscription->load(['user', 'plan'])
        ], 201);
    }
    
    /**
     * Admin: Cancel a subscription
     */
    public function cancelSubscription(string $id): JsonResponse
    {
        $subscription = Subscription::findOrFail($id);
        
        $subscription->update([
            'status' => 'canceled',
            'cancelled_at' => now(),
            'cancelled_by' => Auth::id(),
        ]);
        
        return response()->json([
            'message' => 'Subscription cancelled successfully',
            'subscription' => $subscription->load(['user', 'plan'])
        ]);
    }
    
    /**
     * Admin: Get subscription by ID
     */
    public function getSubscription(string $id): JsonResponse
    {
        $subscription = Subscription::with(['user', 'plan'])
            ->findOrFail($id);
            
        return response()->json($subscription);
    }
}
