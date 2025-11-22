<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\Controller;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\StoreQuizRequest;
use App\Http\Requests\UpdateQuizRequest;
use App\Models\Quiz;
use App\Services\QuizService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class QuizController extends Controller
{
    protected $quizService;

    public function __construct(QuizService $quizService)
    {
        $this->quizService = $quizService;
        
        // Apply auth middleware to all methods except index, show, and getGuestQuizzes
        $this->middleware('auth:api', ['except' => ['index', 'show', 'getGuestQuizzes']]);
        
        // Set up resource authorization
        $this->authorizeResource(Quiz::class, 'quiz');
    }

    /**
     * Display a listing of quizzes.
     */
    public function index(Request $request): JsonResponse
    {
        $quizzes = Quiz::with(['creator', 'subscriptionPlan'])
            ->when($request->has('is_active'), function($query) use ($request) {
                return $query->where('is_active', $request->boolean('is_active'));
            })
            ->paginate($request->input('per_page', 15));

        return response()->json($quizzes);
    }

    /**
     * Store a newly created quiz in storage.
     */
    public function store(StoreQuizRequest $request): JsonResponse
    {
        $quiz = $this->quizService->createQuiz(
            $request->validated(),
            Auth::id(),
            $request->file('option_images', [])
        );

        return response()->json($quiz, 201);
    }

    /**
     * Display the specified quiz.
     */
    public function show(Quiz $quiz): JsonResponse
    {
        $quiz = $this->quizService->getQuizWithQuestions($quiz->id);
        return response()->json($quiz);
    }

    /**
     * Update the specified quiz in storage.
     */
    public function update(UpdateQuizRequest $request, Quiz $quiz): JsonResponse
    {
        $quiz = $this->quizService->updateQuiz(
            $quiz,
            $request->validated(),
            $request->file('option_images', [])
        );

        return response()->json($quiz);
    }

    /**
     * Remove the specified quiz from storage.
     */
    public function destroy(Quiz $quiz): JsonResponse
    {
        $this->quizService->deleteQuiz($quiz);
        
        return response()->json(null, 204);
    }

    /**
     * Get quizzes available for guests.
     */
    public function getGuestQuizzes(): JsonResponse
    {
        $quizzes = Quiz::where('is_guest_quiz', true)
            ->where('is_active', true)
            ->with(['creator', 'subscriptionPlan'])
            ->get();

        return response()->json($quizzes);
    }
}
