<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\Controller;
use App\Models\Quiz;
use App\Services\OptionTextService;
use App\Services\ImageUrlCleaner;
use App\Models\Question;
use App\Models\Option;
use App\Models\User;
use App\Models\Leaderboard;
use App\Models\PointConfiguration;
use App\Models\QuizAttempt;
use App\Services\QuizAttemptService;
use App\Services\PointsService;
use App\Services\RobotCompanionService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class QuizAttemptController extends Controller
{
    protected $quizAttemptService;
    protected $robotCompanionService;

    public function __construct(QuizAttemptService $quizAttemptService, RobotCompanionService $robotCompanionService)
    {
        $this->quizAttemptService = $quizAttemptService;
        $this->robotCompanionService = $robotCompanionService;
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

            // Check if user has reached their quiz limit before starting new attempt
            if ($user->hasReachedQuizLimit()) {
                // Check if user has an existing in-progress attempt for this quiz
                $existingAttempt = $user->quizAttempts()
                    ->where('quiz_id', $quiz->id)
                    ->where('status', 'in_progress')
                    ->first();

                if (!$existingAttempt) {
                    return response()->json([
                        'success' => false,
                        'message' => 'You have reached your quiz limit for your current subscription plan. Please upgrade to continue.',
                        'quiz_limit_reached' => true,
                        'remaining_attempts' => $user->getRemainingQuizAttempts()
                    ], 403);
                }
            }

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
                    'answer_count' => count($request->answers),
                    'answers_data' => $request->answers,
                    'quiz_id' => $attempt->quiz_id
                ]);

                foreach ($request->answers as $answer) {
                    $questionId = $answer['question_id'] ?? null;
                    $optionId = $answer['option_id'] ?? null;
                    
                    if (!$questionId || !$optionId) {
                        continue;
                    }
                    
                    $question = $attempt->quiz->questions->firstWhere('id', $questionId);
                    if (!$question) {
                        Log::error('Question not found', [
                            'question_id' => $questionId,
                            'quiz_id' => $attempt->quiz_id,
                            'available_questions' => $attempt->quiz->questions->pluck('id')->toArray(),
                            'total_questions' => $attempt->quiz->questions->count()
                        ]);
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

                    // Trigger enhanced robot activity based on quiz answer
                    $this->robotCompanionService->handleQuizAnswerActivity(
                        $attempt->quiz_id,
                        Auth::id(),
                        $questionId,
                        $isCorrect
                    );

                    // Update or create user answer
                    $attempt->userAnswers()->updateOrCreate(
                        ['question_id' => $questionId],
                        [
                            'option_id' => $optionId,
                            'is_correct' => $isCorrect,
                            'time_spent' => $answer['time_spent'] ?? 0
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
                'attempt' => $this->formatAttemptResponse($attempt->fresh()),
                'activities' => $this->robotCompanionService->getLatestActivities()
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
     * Reset a quiz attempt (clear answers and restart)
     */
    public function resetAttempt(Request $request, $attemptId): JsonResponse
    {
        try {
            Log::debug('Resetting quiz attempt', [
                'attempt_id' => $attemptId,
                'user_id' => Auth::id()
            ]);

            $attempt = QuizAttempt::where('id', $attemptId)
                ->where('user_id', Auth::id())
                ->where('status', 'in_progress')
                ->firstOrFail();

            // Clear answers and reset progress
            $attempt->update([
                'answers' => [],
                'score' => 0,
                'started_at' => now(), // Reset start time
            ]);

            // Delete user answers for this attempt
            $attempt->userAnswers()->delete();

            Log::info('Quiz attempt reset successfully', [
                'attempt_id' => $attempt->id,
                'user_id' => Auth::id()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Quiz attempt reset successfully'
            ]);

        } catch (\Exception $e) {
            Log::error('Error resetting quiz attempt', [
                'error' => $e->getMessage(),
                'attempt_id' => $attemptId,
                'user_id' => Auth::id()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to reset quiz attempt'
            ], 500);
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

            // Find and update existing in-progress attempt
            $attempt = $user->quizAttempts()
                ->where('quiz_id', $quiz->id)
                ->where('status', 'in_progress')
                ->whereNull('completed_at')
                ->first();

            if (!$attempt) {
                // If no in-progress attempt exists, create a new one (fallback)
                $attempt = $user->quizAttempts()->create([
                    'quiz_id' => $quiz->id,
                    'started_at' => now()->subSeconds($request->time_spent),
                    'total_questions' => $request->total_questions,
                    'answers' => $request->answers ?? [],
                    'status' => 'in_progress'
                ]);
            }

            // Update the attempt with completion data
            $attempt->update([
                'score' => $request->score,
                'passed' => $request->score >= $quiz->passing_score,
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
     * Extract and clean image URL from option text or image_url field
     */
    private static function extractAndCleanImageUrl($option): ?string
    {
        Log::info('Processing option for image extraction', [
            'option_id' => $option->id,
            'text' => $option->text,
            'image_url' => $option->image_url
        ]);
        
        // First check if there's an image in the text field
        if ($option->text) {
            $imageUrl = ImageUrlCleaner::extractImageUrlFromHtml($option->text);
            if ($imageUrl) {
                Log::info('Extracted image from option text', [
                    'option_id' => $option->id,
                    'original_text' => $option->text,
                    'extracted_url' => $imageUrl,
                    'cleaned_url' => ImageUrlCleaner::clean($imageUrl)
                ]);
                return ImageUrlCleaner::clean($imageUrl);
            }
        }
        
        // Fall back to image_url field
        if ($option->image_url) {
            Log::info('Using image_url field', [
                'option_id' => $option->id,
                'image_url' => $option->image_url,
                'cleaned_url' => ImageUrlCleaner::clean($option->image_url)
            ]);
            return ImageUrlCleaner::clean($option->image_url);
        }
        
        Log::info('No image found in option', [
            'option_id' => $option->id
        ]);
        
        return null;
    }

    /**
     * Get live quiz activities and companion feed
     */
    public function getLiveActivities(Request $request): JsonResponse
    {
        try {
            $user = $request->user();
            
            Log::info('getLiveActivities API called', [
                'user_id' => $user->id,
                'quiz_id' => $request->quiz_id ?? 'not provided'
            ]);
            
            // Get historical activities (last 50)
            $historicalActivities = $this->robotCompanionService->getHistoricalActivity(
                $request->quiz_id ?? 1, // Get quiz_id from request or default
                $user->id
            );
            
            // Get latest companion messages (new incremental activities)
            $latestActivities = $this->robotCompanionService->getLatestActivities();
            
            // Combine historical + new activities
            $allActivities = array_merge($historicalActivities, $latestActivities);
            
            // Sort by timestamp (newest first) and limit to 50
            usort($allActivities, function($a, $b) {
                $timestampA = $a['timestamp'] ?? (is_string($a['timestamp']) ? strtotime($a['timestamp']) : $a['timestamp']);
                $timestampB = $b['timestamp'] ?? (is_string($b['timestamp']) ? strtotime($b['timestamp']) : $b['timestamp']);
                return $timestampB - $timestampA;
            });
            
            $activities = array_slice($allActivities, 0, 50);
            
            // Get any recent notification (optional)
            $notification = $this->robotCompanionService->getLatestLiveNotification();

            Log::info('Returning live activities', [
                'activities_count' => count($activities),
                'historical_count' => count($historicalActivities),
                'new_count' => count($latestActivities),
                'has_notification' => !is_null($notification)
            ]);

            return response()->json([
                'success' => true,
                'activities' => $activities,
                'notification' => $notification,
                'active_users_count' => $this->getActiveUsersCount()
            ]);

        } catch (\Exception $e) {
            Log::error('Error fetching live activities', [
                'error' => $e->getMessage(),
                'user_id' => $request->user()->id
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch live activities'
            ], 500);
        }
    }

    /**
     * Get count of active users
     */
    private function getActiveUsersCount(): int
    {
        return QuizAttempt::where('status', 'in_progress')
            ->where('updated_at', '>', now()->subMinutes(30))
            ->distinct('user_id')
            ->count();
    }

    /**
     * Get robot companion summary for completed quiz
     */
    public function getRobotSummary(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'quiz_id' => 'required|exists:quizzes,id'
            ]);

            $user = $request->user();
            $robotSummary = $this->robotCompanionService->getRobotSessionSummary($user->id, $request->quiz_id);

            return response()->json([
                'success' => true,
                'robot_summary' => $robotSummary
            ]);

        } catch (\Exception $e) {
            Log::error('Error getting robot summary', [
                'error' => $e->getMessage(),
                'user_id' => $request->user()->id,
                'quiz_id' => $request->quiz_id
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to get robot summary'
            ], 500);
        }
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
                    'id' => $answer->id ?? null,
                    'question_id' => $answer->question_id,
                    'option_id' => $answer->option_id,
                    'is_correct' => (bool)$answer->is_correct,
                    'time_spent' => $answer->time_spent ?? null,
                    'question' => $answer->question ? [
                        'id' => $answer->question->id,
                        'text' => $answer->question->text,
                        'explanation' => $answer->question->explanation
                    ] : null,
                    'selected_option' => $answer->option ? [
                        'id' => $answer->option->id,
                        'text' => OptionTextService::cleanOptionText($answer->option->text),
                        'image_url' => self::extractAndCleanImageUrl($answer->option),
                        'is_correct' => (bool)$answer->option->is_correct
                    ] : null
                ];
            })
        ];
    }
}