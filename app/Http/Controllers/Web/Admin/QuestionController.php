<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use App\Models\Question;
use App\Models\Quiz;
use Illuminate\Http\Request;
use Illuminate\View\View;

class QuestionController extends Controller
{
    /**
     * Display a listing of questions.
     */
    public function index(Request $request): View
    {
        $questions = Question::with(['quiz', 'options'])
            ->when($request->quiz_id, function ($query, $quizId) {
                $query->where('quiz_id', $quizId);
            })
            ->when($request->search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('text->en', 'like', "%{$search}%")
                      ->orWhere('text->rw', 'like', "%{$search}%");
                });
            })
            ->latest()
            ->paginate(15);

        $quizzes = Quiz::pluck('title', 'id');

        return view('admin.questions.index', compact('questions', 'quizzes'));
    }

    /**
     * Show the form for creating a new question.
     */
    public function create(): View
    {
        $quizzes = Quiz::where('is_active', true)->pluck('title', 'id');
        return view('admin.questions.edit', compact('quizzes'));
    }

    /**
     * Store a newly created question.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'quiz_id' => 'required|exists:quizzes,id',
            'question_text' => 'required|string',
            'question_type' => 'required|in:multiple_choice,true_false,short_answer',
            'answers' => 'required|array|min:2',
            'answers.*.text' => 'required|string',
            'answers.*.is_correct' => 'required|boolean',
            'explanation' => 'nullable|string',
            'points' => 'required|integer|min:1',
        ]);

        try {
            $question = Question::create([
                'quiz_id' => $validated['quiz_id'],
                'question_text' => $validated['question_text'],
                'question_type' => $validated['question_type'],
                'explanation' => $validated['explanation'],
                'points' => $validated['points'],
            ]);

            foreach ($validated['answers'] as $answer) {
                $question->answers()->create([
                    'answer_text' => $answer['text'],
                    'is_correct' => $answer['is_correct'],
                ]);
            }

            return redirect()->route('admin.questions.index')
                ->with('success', 'Question created successfully.');
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Failed to create question: ' . $e->getMessage());
        }
    }

    /**
     * Show the form for editing the specified question.
     */
    public function edit(Question $question): View
    {
        $question->load('answers');
        $quizzes = Quiz::where('is_active', true)->pluck('title', 'id');
        return view('admin.questions.edit', compact('question', 'quizzes'));
    }

    /**
     * Update the specified question.
     */
    public function update(Request $request, Question $question)
    {
        $validated = $request->validate([
            'quiz_id' => 'required|exists:quizzes,id',
            'question_text' => 'required|string',
            'question_type' => 'required|in:multiple_choice,true_false,short_answer',
            'answers' => 'required|array|min:2',
            'answers.*.text' => 'required|string',
            'answers.*.is_correct' => 'required|boolean',
            'explanation' => 'nullable|string',
            'points' => 'required|integer|min:1',
        ]);

        try {
            $question->update([
                'quiz_id' => $validated['quiz_id'],
                'question_text' => $validated['question_text'],
                'question_type' => $validated['question_type'],
                'explanation' => $validated['explanation'],
                'points' => $validated['points'],
            ]);

            // Delete existing answers and create new ones
            $question->answers()->delete();
            foreach ($validated['answers'] as $answer) {
                $question->answers()->create([
                    'answer_text' => $answer['text'],
                    'is_correct' => $answer['is_correct'],
                ]);
            }

            return redirect()->route('admin.questions.index')
                ->with('success', 'Question updated successfully.');
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Failed to update question: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified question.
     */
    public function destroy(Question $question)
    {
        try {
            $question->delete();
            return redirect()->route('admin.questions.index')
                ->with('success', 'Question deleted successfully.');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to delete question: ' . $e->getMessage());
        }
    }
}
