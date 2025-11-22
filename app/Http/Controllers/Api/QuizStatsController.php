<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\Controller;
use App\Models\Quiz;
use App\Services\QuizAttemptService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class QuizStatsController extends Controller
{
    protected $quizAttemptService;

    public function __construct(QuizAttemptService $quizAttemptService)
    {
        $this->quizAttemptService = $quizAttemptService;
        $this->middleware('auth:api');
    }

    /**
     * Get overall quiz statistics
     */
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();
        
        $stats = [
            'total_quizzes' => Quiz::count(),
            'total_attempts' => $user->quizAttempts()->count(),
            'average_score' => round($user->quizAttempts()->avg('score') ?? 0, 2),
            'quizzes_completed' => $user->quizAttempts()->where('status', 'completed')->count(),
        ];

        return response()->json(['stats' => $stats]);
    }

    /**
     * Get statistics for a specific quiz
     */
    public function show(Quiz $quiz): JsonResponse
    {
        $this->authorize('viewStats', $quiz);
        
        $stats = $this->quizAttemptService->getQuizStats($quiz);
        
        // Add more detailed statistics
        $stats['question_stats'] = $this->getQuestionStats($quiz);
        $stats['user_rank'] = $this->getUserRank(auth()->id(), $quiz);
        
        return response()->json(['stats' => $stats]);
    }

    /**
     * Get statistics for each question in the quiz
     */
    protected function getQuestionStats(Quiz $quiz): array
    {
        $questions = $quiz->questions()->withCount([
            'userAnswers as correct_answers_count' => function($query) {
                $query->where('is_correct', true);
            },
            'userAnswers as total_answers_count'
        ])->get();

        return $questions->map(function($question) {
            return [
                'question_id' => $question->id,
                'question_text' => $question->text,
                'total_attempts' => $question->total_answers_count,
                'correct_answers' => $question->correct_answers_count,
                'success_rate' => $question->total_answers_count > 0 
                    ? round(($question->correct_answers_count / $question->total_answers_count) * 100, 2)
                    : 0,
            ];
        })->toArray();
    }

    /**
     * Get user's rank for a specific quiz
     */
    protected function getUserRank(int $userId, Quiz $quiz): ?array
    {
        $userAttempt = $quiz->attempts()
            ->where('user_id', $userId)
            ->where('status', 'completed')
            ->orderBy('score', 'desc')
            ->first();

        if (!$userAttempt) {
            return null;
        }

        $totalUsers = $quiz->attempts()
            ->where('status', 'completed')
            ->select('user_id')
            ->distinct()
            ->count();

        $betterScores = $quiz->attempts()
            ->where('status', 'completed')
            ->where('score', '>', $userAttempt->score)
            ->select('user_id')
            ->distinct()
            ->count();

        $rank = $betterScores + 1;
        $percentile = $totalUsers > 0 ? round((1 - ($rank / $totalUsers)) * 100, 2) : 100;

        return [
            'score' => $userAttempt->score,
            'rank' => $rank,
            'total_users' => $totalUsers,
            'percentile' => $percentile,
            'attempt_date' => $userAttempt->completed_at->toDateTimeString(),
        ];
    }
}
