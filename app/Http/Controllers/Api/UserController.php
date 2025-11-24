<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\UpdateUserRequest;
use App\Models\User;
use App\Services\UploadService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Symfony\Component\HttpFoundation\Response;

class UserController extends Controller
{
    protected UploadService $uploadService;

    public function __construct(UploadService $uploadService)
    {
        $this->uploadService = $uploadService;
        $this->middleware('auth:sanctum');
    }

    /**
     * Get all users (Admin only)
     */
    public function index(): JsonResponse
    {
        $this->authorize('viewAny', User::class);

        $users = User::withCount(['subscriptions', 'quizAttempts'])
            ->orderBy('created_at', 'desc')
            ->get([
                'id',
                'first_name',
                'last_name',
                'email',
                'role',
                'profile_image',
                'phone',
                'has_attempted_guest_quiz',
                'created_at',
                'updated_at'
            ]);

        return response()->json($users);
    }

    /**
     * Get all subscribers (Admin only)
     */
    public function subscribers(): JsonResponse
    {
        $this->authorize('viewAny', User::class);

        $subscribers = User::whereHas('subscriptions')
            ->with(['subscriptions' => function ($query) {
                $query->with(['subscriptionPlan' => function ($q) {
                    $q->select('id', 'name', 'price');
                }])
                ->orderBy('created_at', 'desc');
            }])
            ->withCount('quizAttempts')
            ->orderBy('created_at', 'desc')
            ->get([
                'id',
                'first_name',
                'last_name',
                'email',
                'role',
                'profile_image',
                'phone',
                'created_at',
                'updated_at'
            ]);

        return response()->json($subscribers);
    }

    /**
     * Get user statistics (Admin only)
     */
    public function stats(): JsonResponse
    {
        $this->authorize('viewAny', User::class);

        $stats = [
            'total_users' => User::count(),
            'active_users' => User::where('is_active', true)->count(),
            'subscribers_count' => User::has('subscriptions')->count(),
            'users_by_role' => User::selectRaw('role, COUNT(*) as count')
                ->groupBy('role')
                ->pluck('count', 'role'),
            'users_by_month' => User::selectRaw('DATE_FORMAT(created_at, "%Y-%m") as month, COUNT(*) as count')
                ->groupBy('month')
                ->orderBy('month')
                ->pluck('count', 'month'),
        ];

        return response()->json($stats);
    }

    /**
     * Get current user profile
     */
    public function profile(): JsonResponse
    {
        $user = Auth::user()
            ->load(['subscriptions' => function ($query) {
                $query->with(['subscriptionPlan'])
                    ->orderBy('created_at', 'desc');
            }]);

        return response()->json($user);
    }

    /**
     * Update current user profile
     */
    public function updateProfile(UpdateUserRequest $request): JsonResponse
    {
        $user = Auth::user();
        $data = $request->validated();

        // Handle password update
        if (isset($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        }

        // Handle profile image upload
        if ($request->hasFile('profile_image')) {
            $path = $this->uploadService->upload($request->file('profile_image'), 'profile-images');
            $data['profile_image'] = $this->uploadService->getFileUrl($path);
        }

        $user->update($data);

        return response()->json($user->fresh(['subscriptions.subscriptionPlan']));
    }

    /**
     * Update user (Admin only)
     */
    public function update(UpdateUserRequest $request, User $user): JsonResponse
    {
        $this->authorize('update', $user);
        
        $data = $request->validated();

        // Handle password update
        if (isset($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        }

        // Handle profile image upload
        if ($request->hasFile('profile_image')) {
            $path = $this->uploadService->upload($request->file('profile_image'), 'profile-images');
            $data['profile_image'] = $this->uploadService->getFileUrl($path);
        }

        $user->update($data);

        return response()->json($user->fresh(['subscriptions.subscriptionPlan']));
    }

    /**
     * Delete user (Admin only)
     */
    public function destroy(User $user): JsonResponse
    {
        $this->authorize('delete', $user);

        // Prevent self-deletion
        if ($user->id === Auth::id()) {
            return response()->json([
                'message' => 'You cannot delete your own account.'
            ], Response::HTTP_FORBIDDEN);
        }

        $user->delete();

        return response()->json([
            'message' => 'User deleted successfully.'
        ]);
    }
}
