<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\UpdateUserRequest;
use App\Models\User;
use App\Services\UploadService;
use App\Services\PointsService;
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
        $this->middleware('auth:web')->only(['getUserStats']);
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
     * Get current user statistics for quiz completion flow
     */
    public function getUserStats(): JsonResponse
    {
        $user = Auth::user();
        
        // Use the unified PointsService to get consistent data
        $pointsService = app(PointsService::class);
        $userPoints = $pointsService->getUserPoints($user->id);
        
        // Get the most recent quiz attempt to calculate XP gained
        $latestAttempt = $user->quizAttempts()
            ->where('status', 'completed')
            ->latest('completed_at')
            ->first();
            
        // Calculate XP gained with gamification
        $xpGained = 0;
        if ($latestAttempt) {
            // Use the userAnswers relationship to get correct answers
            $correctAnswers = $latestAttempt->userAnswers()->where('is_correct', true)->count();
            $totalAnswers = $latestAttempt->userAnswers()->count();
            $score = $totalAnswers > 0 ? ($correctAnswers / $totalAnswers) * 100 : 0;
            
            // Base XP: 5 points per correct answer
            $xpGained = $correctAnswers * 5;
            
            // Performance bonuses
            if ($score >= 90) {
                $xpGained += 30; // Excellent performance bonus
            } elseif ($score >= 80) {
                $xpGained += 20; // Great performance bonus
            } elseif ($score >= 70) {
                $xpGained += 10; // Good performance bonus
            }
            
            // Perfect score bonus
            if ($score === 100) {
                $xpGained += 25; // Perfect score bonus
            }
            
            // Speed bonus (if completed under half the time limit)
            if ($latestAttempt->time_spent && $latestAttempt->quiz) {
                $timeLimit = $latestAttempt->quiz->time_limit_minutes * 60;
                if ($latestAttempt->time_spent < $timeLimit / 2) {
                    $xpGained += 15; // Speed bonus
                }
            }
            
            // Add XP to user's total if not already awarded
            if (!$latestAttempt->xp_awarded) {
                $pointsService->awardPoints($user->id, 'quiz_completion', [
                    'quiz_id' => $latestAttempt->quiz_id,
                    'xp_gained' => $xpGained,
                    'quiz_title' => $latestAttempt->quiz->title ?? 'Quiz'
                ]);
                $latestAttempt->update(['xp_awarded' => true]);
            }
        }
        
        // Calculate average score using the same method as dashboard (includes all attempts with answers)
        $averageScore = $this->calculateUserAverageScore($user);
        
        // Calculate or get user stats
        $totalQuestionsAnswered = $user->quizAttempts()
            ->where('status', 'completed')
            ->sum('total_questions') ?? 0;
            
        $stats = [
            'averageScore' => $averageScore,
            'totalQuestionsAnswered' => $totalQuestionsAnswered,
            'leaderboardPosition' => $pointsService->getUserRank($user->id), // Use PointsService for consistent ranking
            'streak' => $user->streak_days ?? 0,
            'xp' => $userPoints['total'], // Use unified points system
            'xpGained' => $xpGained, // XP gained from this quiz
            'hasPlan' => $user->hasActiveSubscription(),
            'quizComparison' => $this->getQuizComparison($user),
            'weeklyPoints' => $userPoints['weekly'],
            'monthlyPoints' => $userPoints['monthly'],
        ];

        return response()->json($stats);
    }

    /**
     * Get quiz comparison data for improvement tracking
     */
    private function getQuizComparison(User $user): ?array
    {
        // Get the last 2 completed quiz attempts for comparison
        $recentAttempts = $user->quizAttempts()
            ->where('status', 'completed')
            ->orderBy('completed_at', 'desc')
            ->take(2)
            ->get(['score', 'completed_at']);

        if ($recentAttempts->count() < 2) {
            return null;
        }

        $latestScore = $recentAttempts->first()->score;
        $previousScore = $recentAttempts->last()->score;
        $improvement = $latestScore - $previousScore;

        return [
            'improvement' => $improvement,
            'latestScore' => $latestScore,
            'previousScore' => $previousScore
        ];
    }

    /**
     * Calculate user average score using the same method as dashboard (includes all attempts with answers)
     */
    private function calculateUserAverageScore(User $user): float
    {
        // Get all quiz attempts
        $quizAttempts = $user->quizAttempts()->with('quiz')->get() ?? collect();
        
        $totalScore = 0;
        $scoreCount = 0;
        
        // Process completed quizzes
        $completedQuizzes = $quizAttempts->where('status', 'COMPLETED');
        foreach ($completedQuizzes as $completed) {
            // Use the score field if available, otherwise calculate from answers
            if ($completed->score) {
                $totalScore += $completed->score;
            } else {
                // Calculate score from answers JSON
                $score = $this->calculateScoreFromAnswers($completed->answers);
                $totalScore += $score;
            }
            $scoreCount++;
        }
        
        // Process in-progress quizzes
        $inProgressQuizzes = $quizAttempts->where('status', 'IN_PROGRESS');
        foreach ($inProgressQuizzes as $inProgress) {
            if ($inProgress->answers && is_array($inProgress->answers) && count($inProgress->answers) > 0) {
                // Calculate partial score
                $partialScore = $this->calculateScoreFromAnswers($inProgress->answers);
                $totalScore += $partialScore;
                $scoreCount++;
            }
        }
        
        return $scoreCount > 0 ? round($totalScore / $scoreCount, 1) : 0;
    }
    
    /**
     * Calculate score percentage from answers JSON
     */
    private function calculateScoreFromAnswers($answers): float
    {
        if (!is_array($answers) || empty($answers)) {
            return 0;
        }

        $totalQuestions = count($answers);
        $correctAnswers = 0;

        foreach ($answers as $questionId => $answer) {
            if (is_array($answer) && isset($answer['is_correct']) && $answer['is_correct']) {
                $correctAnswers++;
            }
        }

        return $totalQuestions > 0 ? round(($correctAnswers / $totalQuestions) * 100, 1) : 0;
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
