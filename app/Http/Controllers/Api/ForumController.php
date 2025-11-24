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
        
        $question = ForumQuestion::create([
            'title' => $validated['title'],
            'content' => $validated['content'],
            'topics' => $validated['topics'] ?? [],
            'user_id' => Auth::id(),
            'is_approved' => false, // Default to false, admin can approve later
        ]);

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
        $questions = ForumQuestion::with(['user', 'answers.user'])
            ->latest()
            ->get();

        return response()->json([
            'data' => $questions
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
