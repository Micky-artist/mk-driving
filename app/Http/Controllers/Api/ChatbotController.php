<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\ChatbotService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ChatbotController extends Controller
{
    protected $chatbotService;

    public function __construct(ChatbotService $chatbotService)
    {
        $this->chatbotService = $chatbotService;
    }

    /**
     * Get all predefined questions
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getPredefinedQuestions()
    {
        try {
            $questions = $this->chatbotService->getPredefinedQuestions();
            return response()->json(['questions' => $questions]);
        } catch (\Exception $e) {
            Log::error('Error fetching predefined questions: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to fetch questions'], 500);
        }
    }

    /**
     * Handle user message
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function handleMessage(Request $request)
    {
        $request->validate([
            'message' => 'required|string',
            'conversation_id' => 'nullable|exists:conversations,id',
        ]);

        try {
            $user = $request->user();
            $response = $this->chatbotService->processMessage(
                $user,
                $request->message,
                $request->conversation_id
            );

            return response()->json($response);
        } catch (\Exception $e) {
            Log::error('Error processing message: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to process message'], 500);
        }
    }

    /**
     * Get conversation history
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getConversationHistory(Request $request)
    {
        try {
            $user = $request->user();
            $conversations = $this->chatbotService->getUserConversations($user->id);
            return response()->json(['conversations' => $conversations]);
        } catch (\Exception $e) {
            Log::error('Error fetching conversation history: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to fetch conversation history'], 500);
        }
    }
}
