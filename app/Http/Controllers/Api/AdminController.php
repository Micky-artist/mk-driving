<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpdateUserRequest;
use App\Http\Requests\Admin\UpdateSubscriptionRequest;
use App\Http\Requests\Admin\CreateUserRequest;
use App\Http\Requests\Admin\UpdateUserRoleRequest;
use App\Models\User;
use App\Models\Subscription;
use App\Models\Quiz;
use App\Models\QuizAttempt;
use App\Models\SubscriptionPlan;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class AdminController extends Controller
{
    /**
     * Get dashboard statistics
     */
    public function getDashboardStats(): JsonResponse
    {
        $stats = [
            'total_users' => User::count(),
            'total_subscribers' => User::has('subscriptions')->count(),
            'total_quizzes' => Quiz::count(),
            'total_revenue' => Subscription::where('status', 'active')
                ->sum('amount'),
            'active_subscriptions' => Subscription::where('status', 'active')
                ->where('end_date', '>', now())
                ->count(),
        ];

        return response()->json($stats);
    }

    /**
     * Get revenue by subscription plan
     */
    public function getRevenueByPlan(): JsonResponse
    {
        $revenueByPlan = DB::table('subscription_plans')
            ->leftJoin('subscriptions', 'subscription_plans.id', '=', 'subscriptions.subscription_plan_id')
            ->where('subscriptions.status', 'active')
            ->select(
                'subscription_plans.id as plan_id',
                'subscription_plans.name as plan_name',
                DB::raw('COUNT(subscriptions.id) as subscription_count'),
                DB::raw('COALESCE(SUM(subscriptions.amount), 0) as revenue')
            )
            ->groupBy('subscription_plans.id', 'subscription_plans.name')
            ->get();

        return response()->json($revenueByPlan);
    }

    /**
     * Get all users with their subscription status and quiz attempts
     */
    public function getAllUsers(): JsonResponse
    {
        $users = User::withCount(['subscriptions', 'quizAttempts'])
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json($users);
    }

    /**
     * Get all subscribers (users with active subscriptions)
     */
    public function getSubscribers(): JsonResponse
    {
        $subscribers = User::whereHas('subscriptions', function($query) {
                $query->where('status', 'active')
                    ->where('end_date', '>', now());
            })
            ->with(['subscriptions.plan'])
            ->withCount('quizAttempts')
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json($subscribers);
    }

    /**
     * Create a new user
     */
    public function createUser(CreateUserRequest $request): JsonResponse
    {
        $validated = $request->validated();
        
        $user = User::create([
            'first_name' => $validated['first_name'],
            'last_name' => $validated['last_name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => $validated['role'] ?? 'student',
            'phone_number' => $validated['phone_number'] ?? null,
            'is_active' => true
        ]);

        return response()->json($user, 201);
    }

    /**
     * Get a single user by ID
     */
    public function getUser(string $id): JsonResponse
    {
        $user = User::with(['subscriptions.plan', 'quizAttempts.quiz'])
            ->withCount(['subscriptions', 'quizAttempts'])
            ->findOrFail($id);

        return response()->json($user);
    }

    /**
     * Update a user's information
     */
    public function updateUser(UpdateUserRequest $request, string $id): JsonResponse
    {
        $user = User::findOrFail($id);
        $user->update($request->validated());

        return response()->json([
            'message' => 'User updated successfully',
            'user' => $user
        ]);
    }

    /**
     * Update a user's role
     */
    public function updateUserRole(UpdateUserRoleRequest $request, string $id): JsonResponse
    {
        $user = User::findOrFail($id);
        $user->role = $request->role;
        $user->save();

        return response()->json([
            'message' => 'User role updated successfully',
            'user' => $user->only(['id', 'email', 'role'])
        ]);
    }

    /**
     * Delete a user
     */
    public function deleteUser(string $id): JsonResponse
    {
        $user = User::findOrFail($id);
        $user->delete();

        return response()->json(['message' => 'User deleted successfully']);
    }

    /**
     * Get all subscriptions for a specific user
     */
    public function getUserSubscriptions(string $userId): JsonResponse
    {
        $subscriptions = Subscription::with('plan')
            ->where('user_id', $userId)
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json($subscriptions);
    }

    /**
     * Update a subscription status
     */
    public function updateSubscriptionStatus(Request $request, string $subscriptionId): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'status' => 'required|in:active,expired,cancelled,pending',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $subscription = Subscription::findOrFail($subscriptionId);
        $subscription->status = $request->status;
        $subscription->save();

        return response()->json([
            'message' => 'Subscription status updated successfully',
            'subscription' => $subscription
        ]);
    }

    /**
     * Delete a subscription
     */
    public function deleteSubscription(string $id): JsonResponse
    {
        $subscription = Subscription::findOrFail($id);
        $subscription->delete();

        return response()->json(['message' => 'Subscription deleted successfully']);
    }

    /**
     * Get quiz analytics
     */
    public function getQuizAnalytics(): JsonResponse
    {
        $totalQuizzes = Quiz::count();
        $totalAttempts = QuizAttempt::count();
        $averageScore = QuizAttempt::avg('score') ?? 0;
        $completionRate = Quiz::has('attempts')
            ->withCount(['attempts as completions' => function($query) {
                $query->where('completed', true);
            }])
            ->get()
            ->avg('completions') ?? 0;

        $quizzesByDifficulty = Quiz::select('difficulty', DB::raw('count(*) as total'))
            ->groupBy('difficulty')
            ->get();

        return response()->json([
            'total_quizzes' => $totalQuizzes,
            'total_attempts' => $totalAttempts,
            'average_score' => round($averageScore, 2),
            'completion_rate' => round($completionRate * 100, 2) . '%',
            'quizzes_by_difficulty' => $quizzesByDifficulty,
        ]);
    }
}