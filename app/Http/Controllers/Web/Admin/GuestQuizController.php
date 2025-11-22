<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use App\Models\Quiz;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class GuestQuizController extends Controller
{
    /**
     * Display the guest quiz management page.
     */
    public function index()
    {
        $quizzes = Quiz::with(['subscriptionPlan', 'questions'])
            ->withCount('questions')
            ->latest()
            ->get();
            
        $currentGuestQuiz = Quiz::where('is_guest_quiz', true)
            ->where('is_active', true)
            ->first();

        return view('admin.guest-quiz.index', [
            'quizzes' => $quizzes,
            'currentGuestQuiz' => $currentGuestQuiz
        ]);
    }

    /**
     * Set a quiz as the guest quiz.
     */
    public function setGuestQuiz(Quiz $quiz)
    {
        // Check if the quiz is already the guest quiz
        if ($quiz->is_guest_quiz) {
            return back()->with('info', 'This quiz is already set as the guest quiz.');
        }

        // Check if the quiz is active
        if (!$quiz->is_active) {
            return back()->with('error', 'You cannot set an inactive quiz as the guest quiz.');
        }

        try {
            DB::beginTransaction();

            // Remove guest status from current guest quiz
            Quiz::where('is_guest_quiz', true)->update(['is_guest_quiz' => false]);

            // Set the selected quiz as guest quiz
            $quiz->update(['is_guest_quiz' => true]);

            DB::commit();

            return back()->with('success', 'Guest quiz has been updated successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error setting guest quiz: ' . $e->getMessage());
            return back()->with('error', 'An error occurred while updating the guest quiz.');
        }
    }
}
