<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use App\Models\ForumQuestion;
use App\Models\ForumAnswer;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ForumController extends Controller
{
    /**
     * Display a listing of forum questions.
     *
     * @return \Illuminate\View\View
     */
    public function index(): View
    {
        $questions = ForumQuestion::with(['user', 'answers'])
            ->withCount('answers')
            ->latest()
            ->paginate(15);

        return view('admin.forum.index', compact('questions'));
    }

    /**
     * Display the specified forum question.
     *
     * @param  \App\Models\ForumQuestion  $question
     * @return \Illuminate\View\View
     */
    public function show(ForumQuestion $question): View
    {
        $question->load(['user', 'answers.user']);
        return view('admin.forum.show', compact('question'));
    }

    /**
     * Store a newly created answer.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\ForumQuestion  $question
     * @return \Illuminate\Http\RedirectResponse
     */
    public function storeAnswer(Request $request, ForumQuestion $question)
    {
        $validated = $request->validate([
            'content' => 'required|string|min:10',
        ]);

        try {
            $question->answers()->create([
                'user_id' => auth()->id(),
                'content' => $validated['content'],
                'is_approved' => true, // Auto-approve admin answers
            ]);

            return back()->with('success', 'Answer posted successfully.');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to post answer: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified question.
     *
     * @param  \App\Models\ForumQuestion  $question
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(ForumQuestion $question)
    {
        try {
            $question->delete();
            return redirect()->route('admin.forum.index')
                ->with('success', 'Question deleted successfully.');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to delete question: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified answer.
     *
     * @param  \App\Models\ForumAnswer  $answer
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroyAnswer(ForumAnswer $answer)
    {
        try {
            $answer->delete();
            return response()->json([
                'success' => true,
                'message' => 'Answer deleted successfully.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete answer: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Toggle approval status of a question.
     *
     * @param  \App\Models\ForumQuestion  $question
     * @return \Illuminate\Http\RedirectResponse
     */
    public function toggleApproval(ForumQuestion $question)
    {
        try {
            $question->update(['is_approved' => !$question->is_approved]);
            $status = $question->is_approved ? 'approved' : 'unapproved';
            return back()->with('success', "Question has been {$status} successfully.");
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to update approval status: ' . $e->getMessage());
        }
    }
}
