<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\Controller;
use App\Models\Quiz;
use App\Models\QuizAttempt;
use App\Models\Question;
use App\Models\Option;
use App\Models\User;
use App\Models\Leaderboard;
use App\Models\PointConfiguration;
use App\Services\QuizAttemptService;
use App\Services\PointsService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class QuizAttemptController extends Controller
{
    protected $quizAttemptService;

    public function __construct(QuizAttemptService $quizAttemptService)
    {
        $this->quizAttemptService = $quizAttemptService;
        $this->middleware('auth:web');
    }

    /**
     * Start or resume a quiz attempt
     */
    public function start(Request $request): JsonResponse
    {
        Log::debug('Starting quiz attempt', [
            'user_id' => $request->user()->id,
            'request_data' => $request->all(),
            'ip' => $request->ip(),
            'user_agent' => $request->userAgent()
        ]);

        try {
            $request->validate([
                'quizId' => 'required|exists:quizzes,id'
            ]);

            $user = $request->user();
            $quiz = Quiz::findOrFail($request->quizId);

            // Log quiz retrieval
            Log::debug('Found quiz', [
                'quiz_id' => $quiz->id,
                'quiz_title' => $quiz->title
            ]);

            // Check if user has an active attempt
            $attempt = $user->quizAttempts()
                ->where('quiz_id', $quiz->id)
                ->where('status', 'in_progress')
                ->first();

            if (!$attempt) {
                Log::debug('No active attempt found, creating new one', [
                    'user_id' => $user->id,
                    'quiz_id' => $quiz->id
                ]);

                // Create a new attempt
                $attempt = $user->quizAttempts()->create([
                    'quiz_id' => $quiz->id,
                    'started_at' => now(),
                    'score' => 0,
                    'answers' => [],
                    'status' => 'in_progress'
                ]);

                Log::info('New quiz attempt created', [
                    'attempt_id' => $attempt->id,
                    'user_id' => $user->id,
                    'quiz_id' => $quiz->id
                ]);
            } else {
                Log::debug('Resuming existing attempt', [
                    'attempt_id' => $attempt->id,
                    'status' => $attempt->status
                ]);
            }

            return response()->json([
                'success' => true,
                'attempt' => $this->formatAttemptResponse($attempt->load(['quiz', 'userAnswers.option', 'userAnswers.question']))
            ]);

        } catch (\Exception $e) {
            Log::error('Error starting quiz attempt', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'user_id' => $request->user()->id ?? 'guest',
                'request_data' => $request->all()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to start quiz attempt'
            ], 500);
        }
    }

    /**
     * Get attempt details
     */
    public function getAttempt($attemptId): JsonResponse
    {
        try {
            Log::debug('Fetching quiz attempt', [
                'attempt_id' => $attemptId,
                'user_id' => Auth::id()
            ]);

            $attempt = QuizAttempt::with(['quiz', 'userAnswers.option', 'userAnswers.question'])
                ->where('id', $attemptId)
                ->where('user_id', Auth::id())
                ->firstOrFail();

            return response()->json([
                'success' => true,
                'attempt' => $this->formatAttemptResponse($attempt)
            ]);

        } catch (\Exception $e) {
            Log::error('Error fetching quiz attempt', [
                'error' => $e->getMessage(),
                'attempt_id' => $attemptId,
                'user_id' => Auth::id()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch quiz attempt'
            ], 404);
        }
    }

    /**
     * Save or update an answer
     */
    public function updateAttempt(Request $request, $attemptId): JsonResponse
    {
        Log::debug('Updating quiz attempt', [
            'attempt_id' => $attemptId,
            'user_id' => Auth::id(),
            'request_data' => $request->except(['answers']), // Don't log full answers to protect sensitive data
            'answers_count' => $request->has('answers') ? count($request->answers) : 0,
            'completed' => $request->completed ?? false
        ]);

        $request->validate([
            'answers' => 'sometimes|array',
            'completed' => 'sometimes|boolean',
            'time_taken' => 'sometimes|integer|min:0'
        ]);

        DB::beginTransaction();
        try {
            $attempt = QuizAttempt::with(['quiz.questions', 'userAnswers'])
                ->where('id', $attemptId)
                ->where('user_id', Auth::id())
                ->firstOrFail();

            // Update answers if provided
            if ($request->has('answers')) {
                Log::debug('Processing answers', [
                    'attempt_id' => $attemptId,
                    'answer_count' => count($request->answers)
                ]);

                foreach ($request->answers as $questionId => $optionId) {
                    $question = $attempt->quiz->questions->firstWhere('id', $questionId);
                    if (!$question) {
                        throw new \Exception("Question not found in this quiz");
                    }

                    $option = $question->options->firstWhere('id', $optionId);
                    if (!$option) {
                        throw new \Exception("Invalid option for the question");
                    }

                    $isCorrect = $option->is_correct;

                    // Log answer update
                    Log::debug('Updating answer', [
                        'attempt_id' => $attemptId,
                        'question_id' => $questionId,
                        'option_id' => $optionId,
                        'is_correct' => $isCorrect
                    ]);

                    // Update or create user answer
                    $attempt->userAnswers()->updateOrCreate(
                        ['question_id' => $questionId],
                        [
                            'option_id' => $optionId,
                            'is_correct' => $isCorrect,
                            'time_spent' => $request->time_taken ?? 0
                        ]
                    );

                    // Update the answers array in the attempt
                    $answers = $attempt->answers ?? [];
                    $answers[$questionId] = [
                        'answer_id' => $optionId,
                        'is_correct' => $isCorrect,
                        'question_text' => $question->text,
                        'answer_text' => $option->text
                    ];
                    $attempt->answers = $answers;
                }
            }

            // Handle quiz completion
            if ($request->completed) {
                Log::debug('Completing quiz attempt', ['attempt_id' => $attemptId]);
                
                // Ensure all questions are answered before completing
                $totalQuestions = $attempt->quiz->questions->count();
                $answeredQuestions = count($attempt->answers ?? []);
                
                if ($answeredQuestions < $totalQuestions) {
                    throw new \Exception("Please answer all questions before submitting");
                }

                $attempt->completed_at = now();
                $attempt->status = 'completed';
                $attempt->time_spent_seconds = $request->time_taken;
                
                // Calculate score
                $correctAnswers = $attempt->userAnswers->where('is_correct', true)->count();
                $score = $totalQuestions > 0 ? round(($correctAnswers / $totalQuestions) * 100) : 0;
                
                $attempt->score = $score;
                $attempt->passed = $score >= $attempt->quiz->passing_score;

                Log::info('Quiz attempt completed', [
                    'attempt_id' => $attempt->id,
                    'user_id' => Auth::id(),
                    'score' => $score,
                    'passed' => $attempt->passed,
                    'correct_answers' => $correctAnswers,
                    'total_questions' => $totalQuestions
                ]);
            }

            $attempt->save();
            DB::commit();

            return response()->json([
                'success' => true,
                'attempt' => $this->formatAttemptResponse($attempt->fresh())
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            
            Log::error('Error updating quiz attempt', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'attempt_id' => $attemptId,
                'user_id' => Auth::id(),
                'request_data' => $request->except(['answers'])
            ]);

            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 422);
        }
    }

    /**
     * Submit quiz results and update user stats
     */
    public function submitQuiz(Request $request): JsonResponse
    {
        Log::debug('Submitting quiz results', [
            'user_id' => $request->user()->id,
            'quiz_id' => $request->quiz_id,
            'score' => $request->score,
            'ip' => $request->ip()
        ]);

        $request->validate([
            'quiz_id' => 'required|exists:quizzes,id',
            'score' => 'required|integer|min:0|max:100',
            'correct_answers' => 'required|integer|min:0',
            'total_questions' => 'required|integer|min:1',
            'time_spent' => 'required|integer|min:0',
            'answers' => 'sometimes|array'
        ]);

        DB::beginTransaction();
        try {
            $user = $request->user();
            $quiz = Quiz::findOrFail($request->quiz_id);

            // Create or update quiz attempt
            $attempt = $user->quizAttempts()->create([
                'quiz_id' => $quiz->id,
                'score' => $request->score,
                'passed' => $request->score >= $quiz->passing_score,
                'started_at' => now()->subSeconds($request->time_spent),
                'completed_at' => now(),
                'time_spent_seconds' => $request->time_spent,
                'answers' => $request->answers ?? [],
                'status' => 'completed'
            ]);

            // Update user stats
            $this->updateUserStats($user, $request->score, $request->correct_answers, $request->total_questions, $attempt);

            // Award points based on performance
            $pointsAwarded = $this->awardPoints($user, $request->score, $quiz);

            DB::commit();

            Log::info('Quiz submitted successfully', [
                'attempt_id' => $attempt->id,
                'user_id' => $user->id,
                'quiz_id' => $quiz->id,
                'score' => $request->score,
                'points_awarded' => $pointsAwarded
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Quiz submitted successfully',
                'attempt_id' => $attempt->id,
                'points_awarded' => $pointsAwarded
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            
            Log::error('Error submitting quiz', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'user_id' => $request->user()->id,
                'quiz_id' => $request->quiz_id
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to submit quiz: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update user statistics after quiz completion
     */
    private function updateUserStats(User $user, int $score, int $correctAnswers, int $totalQuestions, QuizAttempt $currentAttempt): void
    {
        // Update average score
        $allAttempts = $user->quizAttempts()->where('status', 'completed')->get();
        $totalScore = $allAttempts->sum('score');
        $averageScore = $allAttempts->count() > 0 ? round($totalScore / $allAttempts->count(), 1) : 0;

        // Update streak (quiz completion streak) - using 24-hour periods
        $lastQuizDate = $user->quizAttempts()
            ->where('status', 'completed')
            ->where('id', '!=', $currentAttempt->id) // Exclude current attempt
            ->latest('completed_at')
            ->value('completed_at');

        $streakDays = $user->quiz_completion_streak ?? 0;
        if ($lastQuizDate) {
            $lastQuizDate = \Carbon\Carbon::parse($lastQuizDate);
            $hoursSinceLastQuiz = $lastQuizDate->diffInHours(now());
            
            if ($hoursSinceLastQuiz < 24) {
                // Last quiz was less than 24 hours ago - continue streak
                $streakDays++;
            } else {
                // More than 24 hours since last quiz - reset streak to 1
                $streakDays = 1;
            }
        } else {
            // First quiz completion
            $streakDays = 1;
        }

        $user->update([
            'average_score' => $averageScore,
            'quiz_completion_streak' => $streakDays,
            'last_streak_date' => now()
        ]);

        // Trigger leaderboard update - this will recalculate and update leaderboard_position for all users
        $this->updateLeaderboardAsync();
    }

    /**
     * Update leaderboard asynchronously
     */
    private function updateLeaderboardAsync(): void
    {
        // In production, this should be queued as a job
        // For now, we'll update it immediately but keep it fast
        try {
            // Update all-time leaderboard (this will set leaderboard_position for all users)
            $allTimeLeaderboard = Leaderboard::getAllTime();
            $allTimeLeaderboard->updateRankings();
            
            // Also update weekly leaderboard for weekly rankings
            $weeklyLeaderboard = Leaderboard::getWeekly();
            $weeklyLeaderboard->updateRankings();
        } catch (\Exception $e) {
            Log::warning('Failed to update leaderboard asynchronously', [
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Award points based on quiz performance
     */
    private function awardPoints(User $user, int $score, Quiz $quiz): int
    {
        $pointsService = app(PointsService::class);
        $totalPoints = 0;
        
        // Base points for completion
        if ($pointsService->awardPoints($user->id, 'quiz_completed', [
            'quiz_id' => $quiz->id,
            'score' => $score,
        ])) {
            $totalPoints += PointConfiguration::getPointsForActivity('quiz_completed');
        }
        
        // Bonus points for passing
        if ($score >= $quiz->passing_score) {
            if ($pointsService->awardPoints($user->id, 'quiz_passed', [
                'quiz_id' => $quiz->id,
                'score' => $score,
            ])) {
                $totalPoints += PointConfiguration::getPointsForActivity('quiz_passed');
            }
        }
        
        // Bonus points for perfect score
        if ($score === 100) {
            if ($pointsService->awardPoints($user->id, 'quiz_perfect', [
                'quiz_id' => $quiz->id,
                'score' => $score,
            ])) {
                $totalPoints += PointConfiguration::getPointsForActivity('quiz_perfect');
            }
        }
        
        return $totalPoints;
    }

    /**
     * Format the attempt response
     */
    protected function formatAttemptResponse(QuizAttempt $attempt): array
    {
        $attempt->load(['userAnswers.option', 'userAnswers.question']);
        $totalQuestions = $attempt->quiz->questions()->count();
        $correctCount = $attempt->userAnswers->where('is_correct', true)->count();
        $percentage = $totalQuestions > 0 ? round(($correctCount / $totalQuestions) * 100) : 0;

        return [
            'id' => $attempt->id,
            'quiz_id' => $attempt->quiz_id,
            'user_id' => $attempt->user_id,
            'status' => $attempt->status,
            'score' => $attempt->score,
            'passed' => $attempt->passed ?? false,
            'answers' => $attempt->answers ?? [],
            'started_at' => $attempt->started_at,
            'completed_at' => $attempt->completed_at,
            'time_spent_seconds' => $attempt->time_spent_seconds,
            'quiz' => [
                'id' => $attempt->quiz->id,
                'title' => $attempt->quiz->title,
                'questions_count' => $totalQuestions,
                'passing_score' => $attempt->quiz->passing_score
            ],
            'user_answers' => $attempt->userAnswers->map(function ($answer) {
                return [
                    'id' => $answer->id,
                    'question_id' => $answer->question_id,
                    'option_id' => $answer->option_id,
                    'is_correct' => (bool)$answer->is_correct,
                    'time_spent' => $answer->time_spent,
                    'question' => $answer->question ? [
                        'id' => $answer->question->id,
                        'text' => $answer->question->text,
                        'explanation' => $answer->question->explanation
                    ] : null,
                    'selected_option' => $answer->option ? [
                        'id' => $answer->option->id,
                        'text' => $answer->option->text,
                        'is_correct' => (bool)$answer->option->is_correct
                    ] : null
                ];
            })
        ];
    }
}