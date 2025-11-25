<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use App\Models\Quiz;
use Illuminate\Http\Request;
use Illuminate\View\View;

class GuestQuizController extends Controller
{
    /**
     * Display the guest quiz management page.
     *
     * @return \Illuminate\View\View
     */
    public function index(): View
    {
        $quizzes = Quiz::where('is_guest_quiz', true)
            ->withCount('questions')
            ->latest()
            ->paginate(10);

        return view('admin.guest-quiz.index', compact('quizzes'));
    }

    /**
     * Set a quiz as the active guest quiz.
     *
     * @param  \App\Models\Quiz  $quiz
     * @return \Illuminate\Http\RedirectResponse
     */
    public function setGuestQuiz(Quiz $quiz)
    {
        try {
            // Reset all other quizzes
            Quiz::where('is_guest_quiz', true)->update(['is_guest_quiz' => false]);
            
            // Set the selected quiz as guest quiz
            $quiz->update(['is_guest_quiz' => true]);

            return back()->with('success', 'Guest quiz has been set successfully.');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to set guest quiz: ' . $e->getMessage());
        }
    }
}
