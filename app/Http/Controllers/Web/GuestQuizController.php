<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Quiz;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

class GuestQuizController extends Controller
{
    /**
     * Display the guest quiz page
     * 
     * @param string|null $id Optional quiz ID
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse
     */
    /**
     * Display the guest quiz page
     * 
     * @param string $locale The locale (e.g., 'en', 'rw')
     * @param int $quiz The quiz ID
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse
     */
    public function show($locale, $quiz)
    {
        Log::info('Guest quiz request', [
            'locale' => $locale,
            'quiz_id' => $quiz
        ]);
        
        // Fetch the quiz with relationships
        $quiz = Quiz::with(['questions.options'])
            ->where('is_guest_quiz', true)
            ->where('is_active', true)
            ->findOrFail($quiz);
            
        // Log the raw quiz object immediately after fetching
        Log::debug('Raw Quiz Object:', [
            'quiz' => $quiz->toArray(),
            'questions_count' => $quiz->questions->count(),
            'questions_loaded' => $quiz->relationLoaded('questions'),
            'first_question_options_loaded' => $quiz->questions->isNotEmpty() ? $quiz->questions->first()->relationLoaded('options') : false,
            'first_question_options_count' => $quiz->questions->isNotEmpty() ? $quiz->questions->first()->options->count() : 0
        ]);

        // If no questions are found, redirect back with an error
        if ($quiz->questions->isEmpty()) {
            return redirect()->back()->with('error', 'This quiz has no questions available.');
        }
        
        // Verify relationships after fix
        $firstQuestion = $quiz->questions->first();
        $optionsCount = $firstQuestion ? $firstQuestion->options()->count() : 0;
        
        Log::debug('After fix - Question relationships:', [
            'question_id' => $firstQuestion ? $firstQuestion->id : null,
            'options_count' => $optionsCount,
            'correct_option_id' => $firstQuestion ? $firstQuestion->correct_option_id : null,
            'has_correct_option' => $firstQuestion && $firstQuestion->options()->where('id', $firstQuestion->correct_option_id)->exists()
        ]);
        
        // Log the complete quiz structure for debugging
        Log::debug('Quiz Data:', [
            'quiz' => $quiz->toArray(),
            'questions_count' => $quiz->questions->count(),
            'sample_question' => $quiz->questions->first() ? $quiz->questions->first()->toArray() : null,
            'sample_question_options' => $quiz->questions->first() ? $quiz->questions->first()->options->toArray() : null
        ]);

        // Store the quiz start time in the session
        if (!session()->has('quiz_start_time')) {
            session(['quiz_start_time' => now()]);
        }

        return view('guest-quiz.show', [
            'quiz' => $quiz,
            'meta_title' => $quiz->getTranslation('title', app()->getLocale()),
            'meta_description' => $quiz->getTranslation('description', app()->getLocale())
        ]);
    }

    /**
     * Process the quiz submission
     */
    /**
     * Process the quiz submission
     * 
     * @param string $locale The locale (e.g., 'en', 'rw')
     * @param int $quiz The quiz ID
     * @return \Illuminate\Http\RedirectResponse
     */
    public function submit($locale, $quiz, Request $request)
    {
        $quiz = Quiz::findOrFail($quiz);
        
        // Get the time when the quiz was started from the session
        $quizStartTime = session('quiz_start_time');
        $timeLimitMinutes = $quiz->time_limit_minutes ?? 20;
        
        // If time's up was not explicitly indicated by the client-side timer
        if (!$request->has('time_up')) {
            // Check if the quiz has exceeded the time limit
            if ($quizStartTime && (now()->diffInSeconds($quizStartTime) > ($timeLimitMinutes * 60))) {
                return response()->json([
                    'success' => false,
                    'message' => __('Time is up! Your quiz has been automatically submitted.')
                ], 422);
            }
        }
        
        $validated = $request->validate([
            'answers' => 'required|array',
            'answers.*' => 'required|exists:answers,id',
        ]);

        // Calculate score and return results
        $results = $this->calculateResults($quiz, $validated['answers']);
        
        return response()->json([
            'success' => true,
            'results' => $results,
            'passed' => $results['score'] >= $results['passing_score'],
            'message' => __('Quiz submitted successfully!')
        ]);
    }

    /**
     * Reset the quiz session
     * 
     * @param string $locale The locale (e.g., 'en', 'rw')
     * @param int $quiz The quiz ID
     * @return \Illuminate\Http\JsonResponse
     */
    public function reset($locale, $quiz, Request $request)
    {
        // Clear the quiz start time from the session
        session()->forget('quiz_start_time');
        
        return response()->json([
            'success' => true,
            'message' => 'Quiz has been reset successfully.'
        ]);
    }

    /**
     * Calculate quiz results
     */
    protected function calculateResults(Quiz $quiz, array $userAnswers): array
    {
        $questions = $quiz->questions;
        $totalQuestions = $questions->count();
        $correctAnswers = 0;
        $results = [];

        foreach ($questions as $question) {
            $correctAnswer = $question->answers->where('is_correct', true)->first();
            $userAnswer = in_array($correctAnswer->id, $userAnswers);
            
            if ($userAnswer) {
                $correctAnswers++;
            }

            $results[] = [
                'question' => $question->text,
                'correct' => $userAnswer,
                'correct_answer' => $correctAnswer->text,
                'explanation' => $question->explanation
            ];
        }

        $score = ($correctAnswers / $totalQuestions) * 100;
        $passingScore = $quiz->passing_score ?? 70; // Default to 70% if not set

        return [
            'score' => round($score, 2),
            'correct_answers' => $correctAnswers,
            'total_questions' => $totalQuestions,
            'passing_score' => $passingScore,
            'passed' => $score >= $passingScore,
            'details' => $results
        ];
    }
}
