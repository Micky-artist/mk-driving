<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use App\Models\Quiz;
use App\Models\Question;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Str;

class QuizController extends Controller
{
    /**
     * Display a listing of quizzes.
     *
     * @return \Illuminate\View\View
     */
    public function index(): View
    {
        $quizzes = Quiz::withCount('questions')
            ->latest()
            ->paginate(10);

        return view('admin.quizzes.index', compact('quizzes'));
    }

    /**
     * Show the form for creating a new quiz.
     *
     * @return \Illuminate\View\View
     */
    public function create(): View
    {
        return view('admin.quizzes.create');
    }

    /**
     * Store a newly created quiz.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'time_limit' => 'nullable|integer|min:1',
            'passing_score' => 'nullable|integer|min:0|max:100',
            'is_active' => 'boolean',
        ]);

        try {
            $validated['slug'] = Str::slug($validated['title']);
            $quiz = Quiz::create($validated);

            return redirect()->route('admin.quizzes.edit', $quiz)
                ->with('success', 'Quiz created successfully.');
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Failed to create quiz: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified quiz.
     *
     * @param  \App\Models\Quiz  $quiz
     * @return \Illuminate\View\View
     */
    public function show(Quiz $quiz): View
    {
        $quiz->load(['questions' => function ($query) {
            $query->withCount('answers');
        }]);

        return view('admin.quizzes.show', compact('quiz'));
    }

    /**
     * Show the form for editing the specified quiz.
     *
     * @param  \App\Models\Quiz  $quiz
     * @return \Illuminate\View\View
     */
    public function edit(Quiz $quiz): View
    {
        $quiz->load(['questions' => function ($query) {
            $query->withCount('answers');
        }]);

        return view('admin.quizzes.edit', compact('quiz'));
    }

    /**
     * Update the specified quiz.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Quiz  $quiz
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, Quiz $quiz)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'time_limit' => 'nullable|integer|min:1',
            'passing_score' => 'nullable|integer|min:0|max:100',
            'is_active' => 'boolean',
        ]);

        try {
            $validated['slug'] = Str::slug($validated['title']);
            $quiz->update($validated);

            return redirect()->route('admin.quizzes.edit', $quiz)
                ->with('success', 'Quiz updated successfully.');
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Failed to update quiz: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified quiz.
     *
     * @param  \App\Models\Quiz  $quiz
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Quiz $quiz)
    {
        try {
            // Prevent deletion if there are attempts
            if ($quiz->attempts()->exists()) {
                return back()->with('error', 'Cannot delete quiz with existing attempts.');
            }

            $quiz->delete();
            return redirect()->route('admin.quizzes.index')
                ->with('success', 'Quiz deleted successfully.');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to delete quiz: ' . $e->getMessage());
        }
    }
}
