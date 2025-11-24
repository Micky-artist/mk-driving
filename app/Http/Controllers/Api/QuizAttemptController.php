<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\Controller;
use App\Models\Quiz;
use App\Models\QuizAttempt;
use App\Models\Question;
use App\Models\Option;
use App\Services\QuizAttemptService;
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
        $this->middleware('auth:api');
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