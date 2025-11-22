<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\Controller;
use App\Http\Requests\StoreQuizAttemptRequest;
use App\Http\Requests\SubmitQuizAnswersRequest;
use App\Models\Quiz;
use App\Models\QuizAttempt;
use App\Services\QuizAttemptService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class QuizAttemptController extends Controller
{
    protected $quizAttemptService;

    public function __construct(QuizAttemptService $quizAttemptService)
    {
        $this->quizAttemptService = $quizAttemptService;
        $this->middleware('auth:api');
    }

    /**
     * Start a new quiz attempt
     */
    public function start(StoreQuizAttemptRequest $request, Quiz $quiz): JsonResponse
    {
        $attempt = $this->quizAttemptService->startQuizAttempt($quiz, $request->user());
        return response()->json($attempt->load('quiz.questions.options'), 201);
    }

    /**
     * Submit answers for a quiz attempt
     */
    public function submitAnswers(SubmitQuizAnswersRequest $request, QuizAttempt $attempt): JsonResponse
    {
        $this->authorize('update', $attempt);
        
        $attempt = $this->quizAttemptService->submitAnswers(
            $attempt,
            $request->validated()['answers']
        );

        return response()->json([
            'message' => 'Quiz submitted successfully',
            'attempt' => $attempt->load('userAnswers')
        ]);
    }

    /**
     * Get user's quiz attempts
     */
    public function index(Request $request): JsonResponse
    {
        $attempts = $this->quizAttemptService->getUserAttempts(
            $request->user(),
            $request->only(['quiz_id', 'status', 'per_page'])
        );

        return response()->json($attempts);
    }

    /**
     * Get quiz attempt details
     */
    public function show(QuizAttempt $attempt): JsonResponse
    {
        $this->authorize('view', $attempt);
        
        return response()->json(
            $attempt->load(['quiz.questions.options', 'userAnswers'])
        );
    }

    /**
     * Get quiz statistics
     */
    public function getStats(Quiz $quiz): JsonResponse
    {
        $this->authorize('viewStats', [QuizAttempt::class, $quiz]);
        
        return response()->json([
            'stats' => $this->quizAttemptService->getQuizStats($quiz)
        ]);
    }

    /**
     * Get user's active quiz attempt
     */
    public function getActiveAttempt(Quiz $quiz): JsonResponse
    {
        $attempt = QuizAttempt::where('user_id', Auth::id())
            ->where('quiz_id', $quiz->id)
            ->where('status', 'in_progress')
            ->with('quiz.questions.options')
            ->first();

        if (!$attempt) {
            return response()->json(['message' => 'No active attempt found'], 404);
        }

        return response()->json($attempt);
    }
}
