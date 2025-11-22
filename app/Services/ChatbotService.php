<?php

namespace App\Services;

use App\Models\PredefinedQA;
use App\Models\Conversation;
use App\Models\Message;
use App\Models\User;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class ChatbotService
{
    protected $openaiApiKey;
    protected $openaiEndpoint = 'https://api.openai.com/v1/chat/completions';

    public function __construct()
    {
        $this->openaiApiKey = config('services.openai.api_key');
    }

    /**
     * Get all predefined questions
     *
     * @return array
     */
    public function getPredefinedQuestions()
    {
        return PredefinedQA::select('id', 'question', 'category')
            ->where('is_active', true)
            ->get()
            ->groupBy('category');
    }

    /**
     * Process incoming message
     *
     * @param \App\Models\User $user
     * @param string $message
     * @param int|null $conversationId
     * @return array
     */
    public function processMessage($user, $message, $conversationId = null)
    {
        // Check if it's a predefined question
        $predefinedAnswer = $this->handlePredefinedQuestion($message);
        
        if ($predefinedAnswer) {
            return $this->handlePredefinedResponse($user, $message, $predefinedAnswer, $conversationId);
        }

        // Handle custom question with AI
        return $this->handleCustomQuestion($user, $message, $conversationId);
    }

    /**
     * Handle predefined question
     *
     * @param string $message
     * @return mixed
     */
    protected function handlePredefinedQuestion($message)
    {
        return PredefinedQA::where('question', 'like', "%{$message}%")
            ->orWhere('keywords', 'like', "%{$message}%")
            ->first();
    }

    /**
     * Handle response for predefined question
     *
     * @param \App\Models\User $user
     * @param string $message
     * @param PredefinedQA $qa
     * @param int|null $conversationId
     * @return array
     */
    protected function handlePredefinedResponse($user, $message, $qa, $conversationId = null)
    {
        $conversation = $this->getOrCreateConversation($user->id, $conversationId);
        
        // Save user message
        $this->saveMessage($conversation->id, $user->id, $message, 'user');
        
        // Save bot response
        $this->saveMessage($conversation->id, null, $qa->answer, 'assistant');
        
        return [
            'conversation_id' => $conversation->id,
            'response' => $qa->answer,
            'is_predefined' => true,
            'suggested_questions' => $this->getSuggestedQuestions($qa->id)
        ];
    }

    /**
     * Handle custom question with AI
     *
     * @param \App\Models\User $user
     * @param string $message
     * @param int|null $conversationId
     * @return array
     */
    protected function handleCustomQuestion($user, $message, $conversationId = null)
    {
        $conversation = $this->getOrCreateConversation($user->id, $conversationId);
        
        // Save user message
        $this->saveMessage($conversation->id, $user->id, $message, 'user');
        
        // Get AI response
        $aiResponse = $this->getAIResponse($message, $conversation->id);
        
        // Save bot response
        $this->saveMessage($conversation->id, null, $aiResponse, 'assistant');
        
        return [
            'conversation_id' => $conversation->id,
            'response' => $aiResponse,
            'is_predefined' => false
        ];
    }

    /**
     * Get AI response from OpenAI
     *
     * @param string $message
     * @param int $conversationId
     * @return string
     */
    protected function getAIResponse($message, $conversationId)
    {
        try {
            $context = $this->getConversationContext($conversationId);
            
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->openaiApiKey,
                'Content-Type' => 'application/json',
            ])->post($this->openaiEndpoint, [
                'model' => 'gpt-3.5-turbo',
                'messages' => array_merge([
                    [
                        'role' => 'system',
                        'content' => 'You are a helpful driving assistant. Provide clear and concise answers about driving rules, regulations, and best practices.'
                    ]
                ], $context, [
                    [
                        'role' => 'user',
                        'content' => $message
                    ]
                ])
            ]);

            if ($response->successful()) {
                return $response->json('choices.0.message.content', 'Sorry, I encountered an error processing your request.');
            }

            return 'I apologize, but I am having trouble connecting to the AI service at the moment.';
        } catch (\Exception $e) {
            \Log::error('AI API Error: ' . $e->getMessage());
            return 'I apologize, but I am currently unable to process your request. Please try again later.';
        }
    }

    /**
     * Get or create conversation
     *
     * @param int $userId
     * @param int|null $conversationId
     * @return Conversation
     */
    protected function getOrCreateConversation($userId, $conversationId = null)
    {
        if ($conversationId) {
            return Conversation::findOrFail($conversationId);
        }

        return Conversation::create([
            'user_id' => $userId,
            'title' => 'Conversation ' . now()->format('Y-m-d H:i:s'),
        ]);
    }

    /**
     * Save message to database
     *
     * @param int $conversationId
     * @param int|null $userId
     * @param string $content
     * @param string $role
     * @return Message
     */
    protected function saveMessage($conversationId, $userId, $content, $role)
    {
        return Message::create([
            'conversation_id' => $conversationId,
            'user_id' => $userId,
            'content' => $content,
            'role' => $role,
        ]);
    }

    /**
     * Get conversation context for AI
     *
     * @param int $conversationId
     * @return array
     */
    protected function getConversationContext($conversationId)
    {
        return Message::where('conversation_id', $conversationId)
            ->orderBy('created_at', 'desc')
            ->limit(10) // Get last 10 messages for context
            ->get()
            ->map(function ($message) {
                return [
                    'role' => $message->role === 'user' ? 'user' : 'assistant',
                    'content' => $message->content
                ];
            })
            ->toArray();
    }

    /**
     * Get suggested questions based on current question
     *
     * @param int $qaId
     * @return array
     */
    protected function getSuggestedQuestions($qaId)
    {
        return PredefinedQA::where('id', '!=', $qaId)
            ->inRandomOrder()
            ->limit(3)
            ->pluck('question')
            ->toArray();
    }

    /**
     * Get user's conversation history
     *
     * @param int $userId
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getUserConversations($userId)
    {
        return Conversation::with(['messages' => function ($query) {
            $query->orderBy('created_at', 'desc');
        }])
        ->where('user_id', $userId)
        ->orderBy('updated_at', 'desc')
        ->get();
    }
}
