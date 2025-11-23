<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Quiz;
use App\Models\QuizAttempt;
use App\Models\QuizQuestion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class QuizAttemptController extends Controller
{
    /**
     * Save quiz progress
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string  $attemptId
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $locale, $attemptId)
    {
        $user = Auth::user();
        
        // Find the attempt
        $attempt = QuizAttempt::where('id', $attemptId)
            ->where('user_id', $user->id)
            ->firstOrFail();
            
        // If quiz is already completed, don't allow updates
        if ($attempt->completed_at) {
            return response()->json([
                'success' => false,
                'message' => __('This quiz attempt has already been completed.')
            ], 403);
        }
        
        // Get the answers from the request
        $answers = $request->input('answers', []);
        $timeTaken = (int) $request->input('time_taken', 0);
        $isComplete = (bool) $request->input('completed', false);
        
        // Start a database transaction
        return DB::transaction(function () use ($attempt, $answers, $timeTaken, $isComplete) {
            // Update the attempt
            $updates = [
                'answers' => $answers,
                'time_taken' => $timeTaken,
            ];
            
            if ($isComplete) {
                // Calculate score if completing the quiz
                $quiz = $attempt->quiz()->with('questions.options')->firstOrFail();
                $score = 0;
                $totalQuestions = $quiz->questions->count();
                
                // Calculate correct answers
                foreach ($quiz->questions as $question) {
                    $userAnswer = $answers[$question->id] ?? null;
                    $correctOption = $question->options->where('is_correct', true)->first();
                    
                    if ($correctOption && $userAnswer == $correctOption->id) {
                        $score++;
                    }
                }
                
                $percentage = $totalQuestions > 0 ? round(($score / $totalQuestions) * 100, 2) : 0;
                $passingScore = $quiz->passing_score ?? 70; // Default passing score is 70%
                
                $updates = array_merge($updates, [
                    'score' => $score,
                    'total_questions' => $totalQuestions,
                    'percentage' => $percentage,
                    'passed' => $percentage >= $passingScore,
                    'completed_at' => now(),
                    'status' => 'completed',
                ]);
            } else if (!$attempt->started_at) {
                // Mark as started if this is the first save
                $updates['started_at'] = now();
                $updates['status'] = 'in_progress';
            }
            
            $attempt->update($updates);
            
            return response()->json([
                'success' => true,
                'attempt' => $attempt->fresh(),
                'score' => $attempt->score ?? 0,
                'completed' => $isComplete,
            ]);
        });
    }
    
    /**
     * Get a user's active attempt for a quiz
     *
     * @param  string  $quizId
     * @return \Illuminate\Http\JsonResponse
     */
    public function getActiveAttempt($locale, $quizId)
    {
        $user = Auth::user();
        
        // Find an existing in-progress attempt or create a new one
        $attempt = QuizAttempt::firstOrCreate(
            [
                'user_id' => $user->id,
                'quiz_id' => $quizId,
                'status' => 'in_progress',
                'completed_at' => null,
            ],
            [
                'started_at' => now(),
                'answers' => [],
                'score' => 0,
                'total_questions' => Quiz::findOrFail($quizId)->questions()->count(),
            ]
        );
        
        return response()->json([
            'success' => true,
            'attempt' => $attempt,
        ]);
    }
    
    /**
     * Get a user's quiz attempts
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getUserAttempts()
    {
        $user = Auth::user();
        
        $attempts = QuizAttempt::with('quiz')
            ->where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->get()
            ->groupBy('quiz_id')
            ->map(function ($attempts) {
                return $attempts->first();
            });
            
        return response()->json([
            'success' => true,
            'attempts' => $attempts,
        ]);
    }
}
