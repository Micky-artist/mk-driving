<?php

namespace App\Http\Controllers\Web\Dashboard;

use App\Http\Controllers\Controller;
use App\Data\QuizSubmission;
use App\Models\QuizAttempt;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class QuizAttemptController extends Controller
{
    /**
     * Update the specified quiz attempt.
     */
    public function update(Request $request, $locale, $quiz, $attempt = null)
    {
        // If $attempt is not resolved (null), try to get it from the request
        if (!$attempt || !($attempt instanceof QuizAttempt)) {
            $attemptId = $request->input('attempt_id');
            if ($attemptId) {
                $attempt = QuizAttempt::find($attemptId);
            }
            
            if (!$attempt) {
                Log::error('Quiz submission failed: Attempt not found', [
                    'attempt_id' => $attemptId,
                    'user_id' => Auth::id(),
                    'url' => $request->fullUrl(),
                    'method' => $request->method(),
                    'request_data' => $request->all()
                ]);
                return response()->json(['error' => 'Attempt not found'], 404);
            }
        }
        
        // Simple log for debugging
        Log::info('Quiz submission request', [
            'attempt_id' => $attempt->id,
            'user_id' => Auth::id(),
            'attempt_user_id' => $attempt->user_id,
            'url' => $request->fullUrl(),
            'method' => $request->method(),
            'auth_check' => Auth::check(),
            'answers' => $request->input('answers'),
            'time_taken' => $request->input('time_taken'),
            'time_spent' => $request->input('time_spent'),
            'end_time' => $request->input('end_time'),
            'paused_time' => $request->input('paused_time'),
            'time_up' => $request->input('time_up'),
        ]);
        
        try {
            // Authorization check
            if ($attempt->user_id !== Auth::id()) {
                return response()->json(['error' => 'Unauthorized'], 403);
            }

            // Convert JSON strings to arrays if needed
            $request->merge([
                'answers' => is_string($request->answers) ? json_decode($request->answers, true) : $request->answers,
                'time_spent' => is_string($request->time_spent) ? json_decode($request->time_spent, true) : $request->time_spent,
                'time_up' => filter_var($request->time_up, FILTER_VALIDATE_BOOLEAN)
            ]);

            // Basic validation
            $validated = $request->validate([
                'answers' => 'required|array',
                'answers.*' => 'required|exists:options,id',
                'time_taken' => 'required|integer|min:0',
                'time_spent' => 'required|array',
                'time_spent.*' => 'required|integer|min:0',
                'end_time' => 'required|date',
                'paused_time' => 'required|integer|min:0',
                'time_up' => 'required|boolean',
            ]);

            // Create submission object
            $submission = QuizSubmission::fromRequest($validated);

            // Process the attempt in a transaction
            return DB::transaction(function () use ($attempt, $submission) {
                // Prepare answers data for JSON storage
                $answersData = [];
                $score = 0;
                $totalQuestions = $attempt->quiz->questions()->count();
                
                // Process each answer
                foreach ($submission->getAnswers() as $questionId => $optionId) {
                    $question = $attempt->quiz->questions()->findOrFail($questionId);
                    $option = $question->options()->findOrFail($optionId);
                    
                    $isCorrect = $option->is_correct;
                    if ($isCorrect) $score++;
                    
                    $answersData[] = [
                        'question_id' => $questionId,
                        'option_id' => $optionId,
                        'is_correct' => $isCorrect,
                        'time_spent' => $submission->getTimeSpent()[$questionId] ?? 0,
                        'answered_at' => now()->toDateTimeString()
                    ];
                }
                
                // Calculate score percentage (passing is 70% or higher)
                $scorePercentage = $totalQuestions > 0 ? round(($score / $totalQuestions) * 100, 2) : 0;
                $passed = $scorePercentage >= 70;
                
                // Update the attempt with all data
                $attempt->update([
                    'score' => $score,
                    'total_questions' => $totalQuestions,
                    'score_percentage' => $scorePercentage,
                    'time_spent_seconds' => $submission->getTimeTaken(),
                    'time_taken' => $submission->getTimeTaken(),
                    'completed_at' => now(),
                    'status' => 'COMPLETED',
                    'answers' => $answersData,
                    'percentage' => $scorePercentage,
                    'passed' => $passed
                ]);
                
                return response()->json([
                    'message' => 'Quiz submitted successfully',
                    'data' => [
                        'score' => $score,
                        'total_questions' => $totalQuestions,
                        'percentage' => $scorePercentage,
                        'passed' => $passed,
                        'answers' => $answersData
                    ]
                ]);
            });

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'error' => 'Validation failed',
                'messages' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('Quiz submission failed: ' . $e->getMessage());
            return response()->json([
                'error' => 'Failed to submit quiz',
                'message' => $e->getMessage()
            ], 500);
        }
    }
}