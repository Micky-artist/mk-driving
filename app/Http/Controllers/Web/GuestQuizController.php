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
        
        $quiz = Quiz::with(['questions.answers'])
            ->where('is_guest_quiz', true)
            ->where('is_active', true)
            ->findOrFail($quiz);

        // If no questions are found, redirect back with an error
        if ($quiz->questions->isEmpty()) {
            return redirect()->back()->with('error', 'This quiz has no questions available.');
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
