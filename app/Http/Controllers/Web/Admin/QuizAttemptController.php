<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use App\Models\QuizAttempt;
use Illuminate\Http\Request;
use Illuminate\View\View;

class QuizAttemptController extends Controller
{
    /**
     * Display a listing of quiz attempts.
     */
    public function index(Request $request): View
    {
        $attempts = QuizAttempt::with(['user', 'quiz'])
            ->when($request->quiz_id, function ($query, $quizId) {
                $query->where('quiz_id', $quizId);
            })
            ->when($request->status, function ($query, $status) {
                $query->where('status', $status);
            })
            ->when($request->score_filter, function ($query, $scoreFilter) {
                switch ($scoreFilter) {
                    case '90-100':
                        $query->where('score_percentage', '>=', 90);
                        break;
                    case '70-89':
                        $query->where('score_percentage', '>=', 70)
                              ->where('score_percentage', '<', 90);
                        break;
                    case '50-69':
                        $query->where('score_percentage', '>=', 50)
                              ->where('score_percentage', '<', 70);
                        break;
                    case '0-49':
                        $query->where('score_percentage', '<', 50);
                        break;
                }
            })
            ->latest('started_at')
            ->paginate(20);

        $quizzes = \App\Models\Quiz::pluck('title', 'id');

        return view('admin.quiz-attempts.index', compact('attempts', 'quizzes'));
    }

    /**
     * Display the specified quiz attempt.
     */
    public function show(QuizAttempt $attempt): View
    {
        $attempt->load(['user', 'quiz', 'answers.question']);
        
        return view('admin.quiz-attempts.show', compact('attempt'));
    }

    /**
     * Remove the specified quiz attempt.
     */
    public function destroy(QuizAttempt $attempt)
    {
        try {
            $attempt->delete();
            return redirect()->route('admin.quiz.attempts.index')
                ->with('success', 'Quiz attempt deleted successfully.');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to delete quiz attempt: ' . $e->getMessage());
        }
    }
}
