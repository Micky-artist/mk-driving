<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use App\Models\ForumQuestion;
use App\Models\ForumAnswer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ForumController extends Controller
{
    /**
     * Display a listing of forum questions.
     */
    public function index()
    {
        $questions = ForumQuestion::with(['user', 'answers'])
            ->latest()
            ->paginate(10);

        return view('admin.forum.index', compact('questions'));
    }

    /**
     * Remove the specified forum question from storage.
     */
    public function destroy(ForumQuestion $question)
    {
        try {
            DB::beginTransaction();
            
            // Delete all answers first
            $question->answers()->delete();
            
            // Then delete the question
            $question->delete();
            
            DB::commit();
            
            return redirect()->route('admin.forum.index')
                ->with('success', 'Question deleted successfully.');
                
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error deleting forum question: ' . $e->getMessage());
            
            return back()->with('error', 'An error occurred while deleting the question.');
        }
    }

    /**
     * Store a newly created answer in storage.
     */
    public function storeAnswer(Request $request, ForumQuestion $question)
    {
        $validated = $request->validate([
            'content' => 'required|string|min:10',
        ]);

        try {
            $answer = new ForumAnswer([
                'content' => [
                    'en' => $validated['content'],
                    'rw' => $validated['content'],
                ],
                'user_id' => Auth::id(),
                'question_id' => $question->id,
            ]);

            $answer->save();

            return back()->with('success', 'Answer posted successfully.');
            
        } catch (\Exception $e) {
            \Log::error('Error posting answer: ' . $e->getMessage());
            return back()->with('error', 'An error occurred while posting your answer.');
        }
    }

    /**
     * Delete a specific answer.
     */
    public function destroyAnswer(ForumAnswer $answer)
    {
        try {
            $answer->delete();
            return back()->with('success', 'Answer deleted successfully.');
        } catch (\Exception $e) {
            \Log::error('Error deleting answer: ' . $e->getMessage());
            return back()->with('error', 'An error occurred while deleting the answer.');
        }
    }

    /**
     * Toggle the approval status of a question.
     */
    public function toggleApproval(ForumQuestion $question)
    {
        try {
            $question->update([
                'is_approved' => !$question->is_approved
            ]);

            $status = $question->is_approved ? 'approved' : 'unapproved';
            return back()->with('success', "Question {$status} successfully.");
            
        } catch (\Exception $e) {
            \Log::error('Error toggling question approval: ' . $e->getMessage());
            return back()->with('error', 'An error occurred while updating the question status.');
        }
    }
}
