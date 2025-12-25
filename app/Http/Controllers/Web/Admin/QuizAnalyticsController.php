<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use App\Models\Quiz;
use App\Models\QuizAttempt;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;

class QuizAnalyticsController extends Controller
{
    /**
     * Display quiz analytics dashboard.
     */
    public function index(Request $request): View
    {
        $period = $request->get('period', '30'); // Default to last 30 days
        
        // Overall Statistics
        $totalQuizzes = Quiz::count();
        $activeQuizzes = Quiz::where('is_active', true)->count();
        $totalAttempts = QuizAttempt::count();
        $completedAttempts = QuizAttempt::where('status', 'completed')->count();
        $averageScore = QuizAttempt::where('status', 'completed')->avg('score');
        
        // Recent Performance
        $recentAttempts = QuizAttempt::with(['user', 'quiz'])
            ->where('status', 'completed')
            ->latest('completed_at')
            ->take(10)
            ->get();
        
        // Quiz Performance by Quiz
        $quizPerformance = Quiz::withCount(['attempts', 'attempts as completed_attempts' => function ($query) {
                $query->where('status', 'completed');
            }])
            ->withAvg(['attempts as avg_score' => function ($query) {
                $query->where('status', 'completed');
            }], 'score')
            ->get()
            ->map(function ($quiz) {
                $completionRate = $quiz->attempts_count > 0 
                    ? ($quiz->completed_attempts / $quiz->attempts_count) * 100 
                    : 0;
                
                return [
                    'title' => $quiz->title,
                    'attempts' => $quiz->attempts_count,
                    'completed' => $quiz->completed_attempts,
                    'completion_rate' => round($completionRate, 2),
                    'avg_score' => round($quiz->avg_score ?? 0, 2),
                ];
            });
        
        // User Performance
        $topPerformers = User::withCount(['quizAttempts as completed_attempts' => function ($query) {
                $query->where('status', 'completed');
            }])
            ->withAvg(['quizAttempts as avg_score' => function ($query) {
                $query->where('status', 'completed');
            }], 'score')
            ->having('completed_attempts', '>', 0)
            ->orderBy('avg_score', 'desc')
            ->take(10)
            ->get();
        
        // Monthly Statistics (last 6 months)
        $monthlyStats = QuizAttempt::selectRaw('
                DATE_FORMAT(completed_at, "%Y-%m") as month,
                COUNT(*) as total_attempts,
                COUNT(CASE WHEN status = "completed" THEN 1 END) as completed_attempts,
                AVG(CASE WHEN status = "completed" THEN score END) as avg_score
            ')
            ->where('completed_at', '>=', now()->subMonths(6))
            ->groupBy('month')
            ->orderBy('month', 'asc')
            ->get();
        
        return view('admin.quiz-analytics.index', compact(
            'totalQuizzes',
            'activeQuizzes', 
            'totalAttempts',
            'completedAttempts',
            'averageScore',
            'recentAttempts',
            'quizPerformance',
            'topPerformers',
            'monthlyStats',
            'period'
        ));
    }
}
