<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use App\Models\Quiz;
use App\Models\Question;
use App\Models\Option;
use App\Models\QuizAttempt;
use App\Models\QuizDraft;
use App\Models\SubscriptionPlan;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class QuizController extends Controller
{
    /**
     * Display a listing of quizzes with dashboard overview.
     *
     * @return \Illuminate\View\View
     */
    public function index(Request $request): View
    {
        $query = Quiz::withCount('questions');
        
        // Apply search filter
        if ($search = $request->get('search')) {
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }
        
        // Apply status filter
        if ($request->get('is_active') === 'true') {
            $query->where('is_active', true);
        } elseif ($request->get('is_active') === 'false') {
            $query->where('is_active', false);
        }
        
        $quizzes = $query->latest()->paginate(10);
        
        // Get statistics
        $stats = [
            'total_quizzes' => Quiz::count(),
            'active_quizzes' => Quiz::where('is_active', true)->count(),
            'total_attempts' => QuizAttempt::count(),
            'average_score' => QuizAttempt::avg('score') ?? 0,
        ];
        
        // Get popular quizzes (most attempts)
        $popularQuizzes = Quiz::withCount('attempts')
            ->withAvg('attempts as average_score', 'score')
            ->orderBy('attempts_count', 'desc')
            ->limit(10)
            ->get();
            
        // Get recent quizzes
        $recentQuizzes = Quiz::withCount('questions')
            ->latest()
            ->limit(10)
            ->get();

        return view('admin.quizzes.index', compact('quizzes', 'stats', 'popularQuizzes', 'recentQuizzes', 'search'));
    }

    /**
     * Display a full list view of quizzes with advanced filtering.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\View\View
     */
    public function list(Request $request): View
    {
        $query = Quiz::withCount(['questions', 'attempts'])
            ->withAvg('attempts as average_score', 'score');
        
        // Apply search filter
        if ($search = $request->get('search')) {
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }
        
        // Apply status filter
        if ($request->get('is_active') === 'true') {
            $query->where('is_active', true);
        } elseif ($request->get('is_active') === 'false') {
            $query->where('is_active', false);
        }
        
        // Apply date range filter
        if ($request->get('date_from')) {
            $query->whereDate('created_at', '>=', $request->get('date_from'));
        }
        if ($request->get('date_to')) {
            $query->whereDate('created_at', '<=', $request->get('date_to'));
        }
        
        // Apply questions count filter
        if ($request->get('questions_min')) {
            $query->where('questions_count', '>=', $request->get('questions_min'));
        }
        if ($request->get('questions_max')) {
            $query->where('questions_count', '<=', $request->get('questions_max'));
        }
        
        // Apply sorting
        $sort = $request->get('sort', 'created_at');
        $direction = $request->get('direction', 'desc');
        
        if (in_array($sort, ['title', 'created_at', 'questions_count', 'attempts_count', 'average_score'])) {
            $query->orderBy($sort, $direction);
        }
        
        $quizzes = $query->latest()->paginate(15);
        
        return view('admin.quizzes.list', compact('quizzes'));
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
     * Show step-by-step quiz creation form for a specific question.
     *
     * @param int $step
     * @return \Illuminate\View\View
     */
    public function createQuestion(int $step): View
    {
        if ($step < 1 || $step > 20) {
            abort(404, 'Invalid question step');
        }

        // Load draft data if exists
        $draft = QuizDraft::where('user_id', Auth::id())->latest()->first();
        $draftData = $draft ? json_decode($draft->quiz_data, true) : [];
        
        return view('admin.quizzes.create-step', compact('step', 'draftData'));
    }

    /**
     * Store a single question in the draft.
     *
     * @param int $step
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function storeQuestion(int $step, Request $request)
    {
        if ($step < 1 || $step > 20) {
            abort(404, 'Invalid question step');
        }

        try {
            // Validate the question data
            $validated = $request->validate([
                'text' => 'required|string|max:1000',
                'text_rw' => 'required|string|max:1000',
                'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
                'correct_answer' => 'required|integer|in:1,2,3,4',
                'options' => 'required|array|size:4',
                'options.*.text' => 'required|string|max:500',
                'options.*.text_rw' => 'required|string|max:500',
                'options.*.image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            ], [
                'options.size' => 'Each question must have exactly 4 answer options',
                'correct_answer.required' => 'You must select a correct answer before proceeding',
                'correct_answer.integer' => 'Invalid correct answer selection',
                'correct_answer.in' => 'You must select a correct answer (A, B, C, or D) before proceeding',
                'text_rw.required' => 'Rwanda question text is required',
                'options.*.text_rw.required' => 'Rwanda option text is required',
            ]);

            // Get existing draft
            $draft = QuizDraft::where('user_id', Auth::id())->latest()->first();
            $draftData = $draft ? json_decode($draft->quiz_data, true) : [];

            // Handle question image upload
            $questionImagePath = null;
            if ($request->hasFile('image')) {
                $questionImagePath = $request->file('image')->store('quiz-questions', 'public');
            }

            // Prepare question data
            $questionData = [
                'text' => $validated['text'],
                'text_rw' => $validated['text_rw'],
                'image_url' => $questionImagePath,
                'correct_answer' => $validated['correct_answer'] - 1,
                'options' => []
            ];

            // Process options with image uploads
            foreach ($validated['options'] as $index => $option) {
                $optionImagePath = null;
                $optionImage = $request->file("options.{$index}.image");
                if ($optionImage && $optionImage->isValid()) {
                    $optionImagePath = $optionImage->store('quiz-options', 'public');
                }

                $questionData['options'][] = [
                    'text' => $option['text'],
                    'text_rw' => $option['text_rw'],
                    'image_url' => $optionImagePath
                ];
            }

            // Add/update question in draft data
            $draftData['questions'][$step - 1] = $questionData;

            // Save/update draft
            QuizDraft::updateOrCreate(
                ['user_id' => Auth::id()],
                ['quiz_data' => json_encode($draftData)]
            );

            // Determine next step
            if ($step < 20) {
                return redirect()
                    ->route('admin.quizzes.create.question', ['step' => $step + 1])
                    ->with('success', "Question {$step} saved successfully!");
            } else {
                return redirect()
                    ->route('admin.quizzes.create.review')
                    ->with('success', 'All 20 questions completed! Review your quiz before submitting.');
            }

        } catch (\Illuminate\Validation\ValidationException $e) {
            return back()
                ->withErrors($e->validator)
                ->withInput();
        } catch (\Exception $e) {
            Log::error('Question save failed: ' . $e->getMessage());
            return back()
                ->with('error', 'Failed to save question. Please try again.')
                ->withInput();
        }
    }

    /**
     * Show review page before final submission.
     *
     * @return \Illuminate\View\View
     */
    public function reviewQuiz(): View|RedirectResponse
    {
        $draft = QuizDraft::where('user_id', Auth::id())->latest()->first();
        
        if (!$draft || !$draft->quiz_data) {
            return redirect()->route('admin.quizzes.create.question', ['step' => 1])
                ->with('error', 'No quiz data found. Please start creating your quiz.');
        }

        $draftData = json_decode($draft->quiz_data, true);
        
        if (!isset($draftData['questions']) || count($draftData['questions']) < 20) {
            $missingQuestions = 20 - (isset($draftData['questions']) ? count($draftData['questions']) : 0);
            return redirect()->route('admin.quizzes.create.question', ['step' => 1])
                ->with('error', "You need to complete {$missingQuestions} more questions before reviewing.");
        }

        return view('admin.quizzes.review', compact('draftData'));
    }

    /**
     * Complete the quiz creation and save to database.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function completeQuiz(Request $request)
    {
        try {
            DB::beginTransaction();

            $draft = QuizDraft::where('user_id', Auth::id())->latest()->first();
            
            if (!$draft || !$draft->quiz_data) {
                throw new \Exception('No quiz data found');
            }

            $draftData = json_decode($draft->quiz_data, true);

            // Use quiz_info from draft or validate if not present
            if (isset($draftData['quiz_info'])) {
                $quizInfo = $draftData['quiz_info'];
            } else {
                // Fallback to request validation for backward compatibility
                $quizInfo = $request->validate([
                    'title' => 'required|string|max:255',
                    'title_rw' => 'required|string|max:255',
                    'description' => 'nullable|string|max:1000',
                    'description_rw' => 'nullable|string|max:1000',
                    'time_limit' => 'nullable|integer|min:1|max:180',
                    'passing_score' => 'nullable|integer|min:0|max:100',
                    'is_active' => 'sometimes|boolean',
                    'subscription_plans' => 'nullable|array',
                    'subscription_plans.*' => 'exists:subscription_plans,id',
                ]);
            }

            // Create the quiz
            $quiz = Quiz::create([
                'title' => json_encode(['en' => $quizInfo['title'], 'rw' => $quizInfo['title_rw']]),
                'description' => $quizInfo['description'] ? json_encode(['en' => $quizInfo['description'], 'rw' => $quizInfo['description_rw']]) : null,
                'time_limit_minutes' => $quizInfo['time_limit'] ?? 20,
                'passing_score' => $quizInfo['passing_score'] ?? 60,
                'is_active' => $quizInfo['is_active'] ?? true,
                'creator_id' => Auth::id(),
            ]);

            // Handle subscription plan assignments
            if (!empty($quizInfo['subscription_plans'])) {
                $quiz->subscriptionPlans()->sync($quizInfo['subscription_plans']);
            }

            // Create questions and options from draft data
            foreach ($draftData['questions'] as $questionData) {
                // Create question with multilingual support
                $question = Question::create([
                    'quiz_id' => $quiz->id,
                    'text' => json_encode(['en' => $questionData['text'], 'rw' => $questionData['text_rw']]),
                    'image_url' => $questionData['image_url'] ?? null,
                    'type' => 'multiple_choice',
                    'points' => 1,
                    'is_active' => true,
                ]);

                // Create options for this question
                foreach ($questionData['options'] as $optionIndex => $optionData) {
                    Option::create([
                        'question_id' => $question->id,
                        'option_text' => json_encode(['en' => $optionData['text'], 'rw' => $optionData['text_rw']]),
                        'is_correct' => $optionIndex == $questionData['correct_answer'],
                        'order' => $optionIndex,
                        'image_url' => $optionData['image_url'] ?? null,
                    ]);
                }

                // Update question with correct option reference
                $correctOption = $question->options()->where('is_correct', true)->first();
                if ($correctOption) {
                    $question->update(['correct_option_id' => $correctOption->id]);
                }
            }

            // Delete the draft after successful completion
            $draft->delete();

            DB::commit();

            return redirect()
                ->route('admin.quizzes.show', $quiz)
                ->with('success', 'Quiz created successfully with 20 questions!');

        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
            return back()
                ->withErrors($e->validator)
                ->withInput();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Quiz completion failed: ' . $e->getMessage());
            return back()
                ->with('error', 'Failed to create quiz: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Initialize quiz creation by creating draft and redirecting to first question.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function initializeQuiz(Request $request)
    {
        try {
            // Validate basic quiz information
            $validated = $request->validate([
                'title' => 'required|string|max:255',
                'title_rw' => 'required|string|max:255',
                'description' => 'nullable|string|max:1000',
                'description_rw' => 'nullable|string|max:1000',
                'time_limit' => 'nullable|integer|min:1|max:180',
                'passing_score' => 'nullable|integer|min:0|max:100',
                'is_active' => 'sometimes|boolean',
                'subscription_plans' => 'nullable|array',
                'subscription_plans.*' => 'exists:subscription_plans,id',
            ]);

            // Get existing draft or create new one
            $draft = QuizDraft::where('user_id', Auth::id())->latest()->first();
            $draftData = $draft ? json_decode($draft->quiz_data, true) : [];

            // Add basic quiz info to draft
            $draftData['quiz_info'] = $validated;

            // Save/update draft
            QuizDraft::updateOrCreate(
                ['user_id' => Auth::id()],
                ['quiz_data' => json_encode($draftData)]
            );

            return redirect()
                ->route('admin.quizzes.create.question', ['step' => 1])
                ->with('success', 'Quiz initialized! Now create your first question.');

        } catch (\Illuminate\Validation\ValidationException $e) {
            return back()
                ->withErrors($e->validator)
                ->withInput();
        } catch (\Exception $e) {
            Log::error('Quiz initialization failed: ' . $e->getMessage());
            return back()
                ->with('error', 'Failed to initialize quiz. Please try again.')
                ->withInput();
        }
    }

    /**
     * Store a newly created quiz.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        try {
            DB::beginTransaction();

            // Validate quiz basic information
            $validated = $request->validate([
                'title' => 'required|string|max:255',
                'title_rw' => 'required|string|max:255',
                'description' => 'nullable|string|max:1000',
                'description_rw' => 'nullable|string|max:1000',
                'time_limit' => 'nullable|integer|min:1|max:180',
                'passing_score' => 'nullable|integer|min:0|max:100',
                'is_active' => 'sometimes|boolean',
                'subscription_plans' => 'nullable|array',
                'subscription_plans.*' => 'exists:subscription_plans,id',
                'questions' => 'required|array|min:20|max:20',
                'questions.*.text' => 'required|string|max:1000',
                'questions.*.text_rw' => 'required|string|max:1000',
                'questions.*.image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
                'questions.*.correct_answer' => 'required|integer|min:0|max:3',
                'questions.*.options' => 'required|array|size:4',
                'questions.*.options.*.text' => 'required|string|max:500',
                'questions.*.options.*.text_rw' => 'required|string|max:500',
                'questions.*.options.*.image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            ], [
                'questions.required' => 'Exactly 20 questions are required for road theory test',
                'questions.min' => 'Exactly 20 questions are required for road theory test',
                'questions.max' => 'Maximum 20 questions allowed for road theory test',
                'questions.*.options.size' => 'Each question must have exactly 4 answer options',
                'questions.*.correct_answer.required' => 'Please select correct answer for each question',
                'title_rw.required' => 'Rwanda title is required',
                'questions.*.text_rw.required' => 'Rwanda question text is required',
                'questions.*.options.*.text_rw.required' => 'Rwanda option text is required',
            ]);

            // Create the quiz
            $quiz = Quiz::create([
                'title' => json_encode(['en' => $validated['title'], 'rw' => $validated['title_rw']]),
                'description' => $validated['description'] ? json_encode(['en' => $validated['description'], 'rw' => $validated['description_rw']]) : null,
                'time_limit_minutes' => $validated['time_limit'] ?? 20,
                'is_active' => $validated['is_active'] ?? true,
                'creator_id' => Auth::id(),
            ]);

            // Handle subscription plan assignments
            if (!empty($validated['subscription_plans'])) {
                $quiz->subscriptionPlans()->sync($validated['subscription_plans']);
            }

            // Create questions and options
            foreach ($validated['questions'] as $questionIndex => $questionData) {
                // Handle question image upload
                $questionImagePath = null;
                $questionImage = $request->file("questions.{$questionIndex}.image");
                if ($questionImage && $questionImage->isValid()) {
                    $questionImagePath = $questionImage->store('quiz-questions', 'public');
                }

                // Create question with multilingual support
                $question = Question::create([
                    'quiz_id' => $quiz->id,
                    'text' => json_encode(['en' => $questionData['text'], 'rw' => $questionData['text_rw']]),
                    'image_url' => $questionImagePath,
                    'type' => 'multiple_choice',
                    'points' => 1,
                    'is_active' => true,
                ]);

                // Create options for this question
                foreach ($questionData['options'] as $optionIndex => $optionData) {
                    // Handle option image upload
                    $optionImagePath = null;
                    $optionImage = $request->file("questions.{$questionIndex}.options.{$optionIndex}.image");
                    if ($optionImage && $optionImage->isValid()) {
                        $optionImagePath = $optionImage->store('quiz-options', 'public');
                    }

                    Option::create([
                        'question_id' => $question->id,
                        'option_text' => json_encode(['en' => $optionData['text'], 'rw' => $optionData['text_rw']]),
                        'is_correct' => $optionIndex == $questionData['correct_answer'],
                        'order' => $optionIndex,
                        'image_url' => $optionImagePath,
                    ]);
                }

                // Update question with correct option reference
                $correctOption = $question->options()->where('is_correct', true)->first();
                if ($correctOption) {
                    $question->update(['correct_option_id' => $correctOption->id]);
                }
            }

            DB::commit();

            return redirect()
                ->route('admin.quizzes.show', $quiz)
                ->with('success', 'Quiz created successfully with ' . count($validated['questions']) . ' questions!');

        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
            return back()
                ->withErrors($e->validator)
                ->withInput();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Quiz creation failed: ' . $e->getMessage());
            return back()
                ->with('error', 'Failed to create quiz. Please try again.')
                ->withInput();
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
        $quiz->load([
            'questions' => function ($query) {
                $query->withCount('answers');
            },
            'attempts',
            'subscriptionPlans'
        ]);

        // Calculate quiz statistics
        $quiz->attempts_count = $quiz->attempts->count();
        $quiz->average_score = $quiz->attempts_count > 0 ? $quiz->attempts->avg('score') : 0;

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

    /**
     * Assign quiz to subscription plans.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Quiz  $quiz
     * @return \Illuminate\Http\RedirectResponse
     */
    public function assignPlans(Request $request, Quiz $quiz)
    {
        try {
            $planIds = $request->input('plans', []);
            
            // Sync the quiz with selected subscription plans
            $quiz->subscriptionPlans()->sync($planIds);
            
            return back()->with('success', 'Quiz assignments updated successfully.');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to update quiz assignments: ' . $e->getMessage());
        }
    }

    /**
     * Save quiz draft to database.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function saveDraft(Request $request): JsonResponse
    {
        try {
            $quizData = $request->except(['_token']);
            
            // Debug: Log what we're receiving
            Log::info('Auto-save data received', ['data' => $quizData]);
            
            // Create or update draft
            $draft = QuizDraft::updateOrCreate(
                ['user_id' => Auth::id()],
                ['quiz_data' => json_encode($quizData)]
            );
            
            return response()->json([
                'success' => true,
                'draft_id' => $draft->id,
                'message' => 'Draft saved successfully'
            ]);
        } catch (\Exception $e) {
            Log::error('Draft save failed', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Failed to save draft: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display quiz drafts for management.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\View\View
     */
    public function drafts(Request $request): View
    {
        $search = $request->get('search');
        $drafts = QuizDraft::where('user_id', Auth::id())
            ->when($search, function ($query, $builder) use ($search) {
                return $builder->where(function ($subQuery) use ($search) {
                    $subQuery->where('quiz_data', 'like', '%' . $search . '%');
                });
            })
            ->latest()
            ->paginate(10);

        return view('admin.quizzes.drafts', compact('drafts'));
    }

    /**
     * Load quiz draft from database.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function loadDraft(Request $request): JsonResponse
    {
        try {
            $draft = QuizDraft::where('user_id', Auth::id())
                ->latest()
                ->first();
            
            if ($draft) {
                return response()->json([
                    'success' => true,
                    'data' => json_decode($draft->quiz_data, true),
                    'message' => 'Draft loaded successfully'
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'No draft found'
                ]);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to load draft: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete quiz draft.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function deleteDraft(Request $request): JsonResponse
    {
        try {
            QuizDraft::where('user_id', Auth::id())->delete();
            
            return response()->json([
                'success' => true,
                'message' => 'Draft deleted successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete draft: ' . $e->getMessage()
            ], 500);
        }
    }
}
