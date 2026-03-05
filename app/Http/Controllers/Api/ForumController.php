<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Forum\StoreQuestionRequest;
use App\Http\Requests\Forum\StoreAnswerRequest;
use App\Models\ForumQuestion;
use App\Models\ForumAnswer;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class ForumController extends Controller
{
    /**
     * Create a new forum question
     *
     * @param StoreQuestionRequest $request
     * @return JsonResponse
     */
    public function storeQuestion(StoreQuestionRequest $request): JsonResponse
    {
        $validated = $request->validated();
        
        // Handle both full forum questions and companion sidebar questions
        $questionData = [
            'user_id' => Auth::id(),
            'is_approved' => false, // Default to false, admin can approve later
        ];

        if (isset($validated['title'])) {
            // Full forum question
            $questionData['title'] = $validated['title'];
            $questionData['content'] = $validated['content'];
            $questionData['topics'] = $validated['topics'] ?? [];
        } else {
            // Companion sidebar question (simplified)
            $questionData['title'] = substr($validated['question'], 0, 100);
            $questionData['content'] = $validated['question'];
            $questionData['quiz_id'] = $validated['quiz_id'] ?? null;
        }

        $question = ForumQuestion::create($questionData);

        return response()->json([
            'message' => 'Question created successfully',
            'data' => $question->load('user')
        ], 201);
    }

    /**
     * Get all forum questions
     *
     * @return JsonResponse
     */
    public function getQuestions(): JsonResponse
    {
        $query = ForumQuestion::with(['user', 'answers.user'])
            ->latest();

        // Filter by quiz_id if provided (for companion sidebar)
        if (request()->has('quiz_id')) {
            $query->where('quiz_id', request('quiz_id'));
        }

        $questions = $query->get();

        // Format for companion sidebar
        $formattedQuestions = $questions->map(function ($question) {
            return [
                'id' => $question->id,
                'question' => $question->content,
                'user_name' => $question->user->first_name . ' ' . $question->user->last_name,
                'created_at' => $question->created_at->diffForHumans(),
                'is_current_user' => $question->user_id === Auth::id(),
                'answers' => $question->answers->map(function ($answer) {
                    return [
                        'id' => $answer->id,
                        'answer' => $answer->content,
                        'user_name' => $answer->user->first_name . ' ' . $answer->user->last_name,
                        'created_at' => $answer->created_at->diffForHumans(),
                        'is_helpful' => $answer->is_helpful ?? false
                    ];
                })->toArray()
            ];
        });

        return response()->json([
            'questions' => $formattedQuestions
        ]);
    }

    /**
     * Delete a forum question (admin only)
     *
     * @param string $id
     * @return JsonResponse
     */
    public function deleteQuestion(string $id): JsonResponse
    {
        $question = ForumQuestion::findOrFail($id);
        
        // Check if user is admin or the question owner
        if (Auth::user()->role !== 'admin' && $question->user_id !== Auth::id()) {
            return response()->json([
                'message' => 'Unauthorized action.'
            ], 403);
        }

        $question->delete();

        return response()->json([
            'message' => 'Question deleted successfully'
        ]);
    }

    /**
     * Create an answer to a forum question
     *
     * @param StoreAnswerRequest $request
     * @param string $questionId
     * @return JsonResponse
     */
    public function storeAnswer(StoreAnswerRequest $request, string $questionId): JsonResponse
    {
        $question = ForumQuestion::findOrFail($questionId);
        
        $answer = $question->answers()->create([
            'content' => $request->input('content'),
            'user_id' => Auth::id(),
        ]);

        return response()->json([
            'message' => 'Answer added successfully',
            'data' => $answer->load('user')
        ], 201);
    }
}
