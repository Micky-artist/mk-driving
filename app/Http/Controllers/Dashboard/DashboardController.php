<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Quiz;
use App\Models\QuizAttempt;
use App\Models\Subscription;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    /**
     * Display the dashboard index page.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $user = Auth::user();
        
        // Get user's active subscriptions
        $currentSubscriptions = $user->subscriptions()
            ->with('plan')
            ->where('ends_at', '>=', now())
            ->where('status', 'ACTIVE')
            ->get();
            
        // Get available subscription plans (assuming you have a Plan model)
        $availablePlans = \App\Models\SubscriptionPlan::where('is_active', true)
            ->orderBy('price')
            ->get();
            
        // Get quiz statistics
        $totalQuizzes = Quiz::count();
        $completedQuizzes = $user->quizAttempts()
            ->where('status', 'COMPLETED')
            ->count();
            
        // Get new quizzes (not yet attempted)
        $attemptedQuizIds = $user->quizAttempts()->pluck('quiz_id');
        $newQuizzes = Quiz::whereNotIn('id', $attemptedQuizIds)
            ->orderBy('created_at', 'desc')
            ->take(3)
            ->get();
            
        // Get in-progress quizzes (started but not completed)
        $inProgressQuizzes = $user->quizAttempts()
            ->with('quiz')
            ->where('status', 'IN_PROGRESS')
            ->where('created_at', '>', now()->subDays(7))
            ->orderBy('updated_at', 'desc')
            ->get()
            ->map(function ($attempt) {
                $totalQuestions = $attempt->quiz->questions_count ?? 0;
                $attempt->progress = $totalQuestions > 0 
                    ? round(($attempt->answers_count / $totalQuestions) * 100) 
                    : 0;
                return $attempt;
            });
            
        // Get recently completed quizzes
        $completedQuizzesList = $user->quizAttempts()
            ->with('quiz')
            ->where('status', 'COMPLETED')
            ->orderBy('completed_at', 'desc')
            ->take(3)
            ->get();

        // Calculate average score from completed quizzes
        $averageScore = $user->quizAttempts()
            ->where('status', 'COMPLETED')
            ->whereNotNull('score')
            ->avg('score');

        return view('dashboard.index', [
            'user' => $user,
            'currentSubscriptions' => $currentSubscriptions,
            'availablePlans' => $availablePlans,
            'stats' => [
                'total_quizzes' => $totalQuizzes,
                'completed_count' => $completedQuizzes,
                'in_progress_count' => $inProgressQuizzes->count(),
                'new_quizzes_count' => $newQuizzes->count(),
                'average_score' => round($averageScore ?? 0, 1),
            ],
            'newQuizzes' => $newQuizzes,
            'inProgressQuizzes' => $inProgressQuizzes,
            'completedQuizzes' => $completedQuizzesList,
        ]);
    }
}
