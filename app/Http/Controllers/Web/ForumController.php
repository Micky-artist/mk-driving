<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\ForumQuestion;
use App\Models\ForumAnswer;
use App\Services\PointsService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ForumController extends Controller
{
    public function __construct(private PointsService $pointsService)
    {
    }

    /**
     * Display a listing of the forum questions.
     */
    public function index(Request $request)
    {
        $perPage = 10; // Number of questions per page
        $search = $request->input('search');
        
        $query = ForumQuestion::with(['user', 'answers.user'])
            ->orderBy('created_at', 'desc');
        
        // Apply search filter if provided
        if ($search) {
            $searchTerm = strtolower($search);
            $query->where(function($q) use ($searchTerm) {
                // Search in content JSON column for both 'en' and 'rw' locales
                $q->whereRaw("LOWER(JSON_UNQUOTE(content)) LIKE ?", ["%{$searchTerm}%"])
                  ->orWhereRaw("LOWER(JSON_UNQUOTE(JSON_EXTRACT(content, '$.en'))) LIKE ?", ["%{$searchTerm}%"])
                  ->orWhereRaw("LOWER(JSON_UNQUOTE(JSON_EXTRACT(content, '$.rw'))) LIKE ?", ["%{$searchTerm}%"]);
            });
        }
        
        $questions = $query->paginate($perPage)->withQueryString();
            
        // Transform the paginated collection
        $questions->getCollection()->transform(function($question) {
            $locale = app()->getLocale();
            $fallbackLocale = config('app.fallback_locale', 'en');
            
            // Ensure content is properly decoded if it's a JSON string
            $contentData = is_string($question->content) ? json_decode($question->content, true) : $question->content;
            $content = is_array($contentData)
                ? ($contentData[$locale] ?? $contentData[$fallbackLocale] ?? 'No content')
                : ($question->content ?? 'No content');
            
            return [
                'id' => $question->id,
                'title' => $content, // Use content as title for display
                'content' => $content,
                'createdAt' => $question->created_at,
                'updatedAt' => $question->updated_at,
                'votes' => $question->votes ?? 0,
                'user_id' => $question->user_id,
                'author' => [
                    'firstName' => $question->user && $question->user->first_name ? $question->user->first_name : 'Anonymous',
                    'lastName' => $question->user && $question->user->last_name ? $question->user->last_name : '',
                ],
                'answers' => $question->answers->map(function($answer) use ($locale, $fallbackLocale) {
                    // Ensure answer content is properly decoded if it's a JSON string
                    $answerContentData = is_string($answer->content) ? json_decode($answer->content, true) : $answer->content;
                    $answerContent = is_array($answerContentData)
                        ? ($answerContentData[$locale] ?? $answerContentData[$fallbackLocale] ?? 'No content')
                        : ($answer->content ?? 'No content');
                        
                    return [
                        'id' => $answer->id,
                        'content' => $answerContent,
                        'votes' => $answer->votes ?? 0,
                        'user_id' => $answer->user_id,
                        'author' => [
                            'firstName' => $answer->user && $answer->user->first_name ? $answer->user->first_name : 'Anonymous',
                            'lastName' => $answer->user && $answer->user->last_name ? $answer->user->last_name : '',
                        ],
                        'createdAt' => $answer->created_at->toIso8601String(),
                    ];
                }),
            ];
        });

        if ($request->wantsJson()) {
            return response()->json([
                'questions' => $questions,
            ]);
        }

        $topics = collect(config('forum.topics', []));
        return view('forum.index', [
            'questions' => $questions,
            'topics' => $topics,
        ]);
    }

    /**
     * Display the leaderboard view
     */
    public function leaderboard(Request $request)
    {
        // Get weekly leaderboard data (Duolingo-style)
        $leaderboard = $this->pointsService->getLeaderboard(25, 'weekly');
        
        // Get user's rank if authenticated
        $userRank = null;
        $userPoints = 0;
        $isAdmin = false;
        
        if (Auth::check()) {
            $userPoints = $this->pointsService->getUserPoints(Auth::id());
            $userRank = $this->pointsService->getUserRank(Auth::id(), 'weekly');
            $isAdmin = Auth::user()->hasRole('admin');
        }

        if ($request->wantsJson()) {
            return response()->json([
                'leaderboard' => $leaderboard,
                'userRank' => $userRank,
                'userPoints' => $userPoints,
                'isAdmin' => $isAdmin,
            ]);
        }

        return view('leaderboard', [
            'leaderboard' => $leaderboard,
            'userRank' => $userRank,
            'userPoints' => $userPoints,
            'isAdmin' => $isAdmin,
        ]);
    }

    /**
     * Show the form for creating a new question.
     */
    public function create()
    {
        $topics = config('forum.topics', []);
        return view('forum.create', compact('topics'));
    }

    /**
     * Store a newly created question in storage.
     */
    public function store(Request $request)
    {
        try {
            $locale = app()->getLocale();
            $fallback = config('app.fallback_locale', 'rw');
            
            Log::info('Forum store request', [
                'locale' => $locale,
                'request_data' => $request->all(),
                'user_id' => Auth::id()
            ]);
            
            $validated = $request->validate([
                'content' => 'required|array',
                'content.'.$locale => 'required|string|min:10|max:255',
            ]);
            
            Log::info('Validation passed', ['validated' => $validated]);
            
            // Ensure we have both languages, using current language for fallback if needed
            $content = [
                $locale => $validated['content'][$locale],
                $fallback => $validated['content'][$fallback] ?? $validated['content'][$locale]
            ];

            Log::info('Content prepared', ['content' => $content]);

            $question = new ForumQuestion([
                'content' => json_encode($content),
                'user_id' => Auth::id(),
                'is_approved' => true,
            ]);

            $question->save();

            Log::info('Question saved', ['question_id' => $question->id]);

            // Award points for asking a question
            try {
                $this->pointsService->awardPoints(Auth::id(), 'question_asked', [
                    'question_id' => $question->id,
                    'forum_question_id' => $question->id,
                ]);
                Log::info('Points awarded successfully');
            } catch (\Exception $pointsException) {
                Log::warning('Points awarding failed', ['error' => $pointsException->getMessage()]);
                // Continue even if points fail
            }

            if ($request->wantsJson()) {
                $responseData = [
                    'success' => true,
                    'id' => $question->id,
                    'title' => $question->content, // Use content as title for response
                    'content' => $question->content,
                    'author' => [
                        'firstName' => $question->user && $question->user->first_name ? $question->user->first_name : 'Anonymous',
                        'lastName' => $question->user && $question->user->last_name ? $question->user->last_name : '',
                    ],
                    'createdAt' => $question->created_at->toIso8601String(),
                    'answers' => [],
                ];
                
                Log::info('Returning JSON response', ['response_data' => $responseData]);
                
                return response()->json($responseData, 201);
            }

            return redirect()
                ->route('forum.index')
                ->with('success', 'Your question has been posted!');
                
        } catch (\Exception $e) {
            Log::error('Forum store error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'request_data' => $request->all()
            ]);
            
            if ($request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage()
                ], 500);
            }
            
            throw $e;
        }
    }

    /**
     * Display the specified question.
     */
    public function show($id)
    {
        $question = ForumQuestion::with(['user', 'answers.user'])
            ->findOrFail($id);
            
        // Increment view count
        $question->increment('views');
        
        $locale = app()->getLocale();
        $fallbackLocale = config('app.fallback_locale', 'rw');
        
        // Prepare question data with localized content
        $contentData = is_string($question->content) ? json_decode($question->content, true) : $question->content;
        
        $questionData = [
            'id' => $question->id,
            'title' => is_array($contentData) 
                ? ($contentData[$locale] ?? $contentData[$fallbackLocale] ?? 'No content')
                : $question->content,
            'content' => is_array($contentData) 
                ? ($contentData[$locale] ?? $contentData[$fallbackLocale] ?? 'No content')
                : $question->content,
            'user' => $question->user,
            'created_at' => $question->created_at,
            'views' => $question->views,
        ];
        
        // Prepare answers with localized content
        $answers = $question->answers()->paginate(10);
        $answers->getCollection()->transform(function($answer) use ($locale, $fallbackLocale) {
            // Ensure answer content is properly decoded if it's a JSON string
            $answerContentData = is_string($answer->content) ? json_decode($answer->content, true) : $answer->content;
            
            return [
                'id' => $answer->id,
                'content' => is_array($answerContentData) 
                    ? ($answerContentData[$locale] ?? $answerContentData[$fallbackLocale] ?? 'No content')
                    : $answer->content,
                'user' => $answer->user,
                'created_at' => $answer->created_at,
                'is_best_answer' => $answer->is_best_answer,
            ];
        });
        
        $relatedQuestions = ForumQuestion::where('id', '!=', $id)
            ->where(function($query) use ($question) {
                foreach ($question->topics as $topic) {
                    $query->orWhereJsonContains('topics', $topic);
                }
            })
            ->withCount('answers')
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        return view('forum.show', compact('question', 'relatedQuestions'));
    }

    /**
     * Store a newly created answer in storage.
     */
    public function storeAnswer(Request $request, $questionId)
    {
        // Check if questionId is a locale, if so get the actual question ID from route
        if (in_array($questionId, ['rw', 'en'])) {
            // Get all route parameters and find the one that's not a locale
            $routeParams = $request->route()->parameters();
            foreach ($routeParams as $key => $value) {
                if (!in_array($value, ['rw', 'en']) && is_numeric($value)) {
                    $questionId = $value;
                    break;
                }
            }
        }
        
        $locale = app()->getLocale();
        $fallback = config('app.fallback_locale', 'en');
        
        $validated = $request->validate([
            'content' => 'required|array',
            "content.{$locale}" => 'required|string|min:10',
            'parent_id' => 'nullable|exists:forum_answers,id',
        ]);

        $answer = new ForumAnswer([
            'content' => json_encode([
                $locale => $validated['content'][$locale],
                $fallback => $validated['content'][$locale] // Use same content for fallback
            ]),
            'user_id' => Auth::id(),
            'question_id' => $questionId,
            'parent_id' => $validated['parent_id'] ?? null,
            'is_approved' => true,
        ]);

        $answer->save();

        // Award points for answering a question
        $this->pointsService->awardPoints(Auth::id(), 'question_answered', [
            'answer_id' => $answer->id,
            'question_id' => $questionId,
        ]);

        if ($request->wantsJson()) {
            $locale = app()->getLocale();
            $fallbackLocale = config('app.fallback_locale', 'en');
            
            $content = is_array($answer->content) 
                ? ($answer->content[$locale] ?? $answer->content[$fallbackLocale] ?? 'No content')
                : $answer->content;

            return response()->json([
                'id' => $answer->id,
                'content' => $content,
                'author' => [
                    'firstName' => $answer->user && $answer->user->first_name ? $answer->user->first_name : 'Anonymous',
                    'lastName' => $answer->user && $answer->user->last_name ? $answer->user->last_name : '',
                ],
                'createdAt' => $answer->created_at->toIso8601String(),
            ], 201);
        }

        return back()->with('success', __('Your answer has been posted successfully!'));
    }

    /**
     * Mark an answer as the best answer.
     */
    public function markAsBestAnswer($questionId, $answerId)
    {
        $question = ForumQuestion::findOrFail($questionId);
        
        // Check if the user is the owner of the question
        if ($question->user_id !== Auth::id()) {
            abort(403);
        }

        $question->update(['best_answer_id' => $answerId]);

        return back()->with('success', __('Marked as best answer!'));
    }

    /**
     * Vote for a question or answer.
     */
    public function vote(Request $request, $param1, $param2, $param3 = null)
    {
        // Handle locale parameter extraction
        $locale = null;
        $type = null;
        $id = null;
        
        // Check if first parameter is locale (rw or en)
        if (in_array($param1, ['rw', 'en'])) {
            $locale = $param1;
            $type = $param2;
            $id = $param3;
        } else {
            $type = $param1;
            $id = $param2;
        }
        
        Log::info('Vote method called', [
            'original_params' => [$param1, $param2, $param3],
            'extracted' => ['locale' => $locale, 'type' => $type, 'id' => $id],
            'vote' => $request->vote,
            'user_id' => Auth::id()
        ]);

        $request->validate([
            'vote' => 'required|in:up,down',
        ]);

        Log::info('Validation passed, determining model type');

        try {
            $model = $type === 'question' 
                ? ForumQuestion::findOrFail($id)
                : ForumAnswer::findOrFail($id);

            Log::info('Model found', ['model_type' => get_class($model), 'model_id' => $model->id]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            Log::error('Model not found in vote method', [
                'type' => $type,
                'id' => $id,
                'error' => $e->getMessage()
            ]);
            return response()->json(['success' => false, 'message' => 'Model not found'], 404);
        }

        $user = Auth::user();
        $voteType = $request->vote === 'up' ? 1 : -1;

        // Check if user already voted
        $existingVote = DB::table('votes')
            ->where('user_id', $user->id)
            ->where('votable_type', get_class($model))
            ->where('votable_id', $model->id)
            ->first();

        if ($existingVote) {
            // If same vote, remove it (toggle)
            if ($existingVote->vote === $voteType) {
                DB::table('votes')
                    ->where('id', $existingVote->id)
                    ->delete();
                
                // Decrement the votes count
                get_class($model)::where('id', $model->id)->decrement('votes');
                
                // Refresh the model to get the updated vote count
                $model->refresh();
                
                return response()->json([
                    'success' => true,
                    'action' => 'removed',
                    'votes' => $model->votes,
                    'user_vote' => null,
                ]);
            } 
            // If different vote, update it
            else {
                DB::table('votes')
                    ->where('id', $existingVote->id)
                    ->update(['vote' => $voteType]);
                
                // Update votes count (add 2 because we're changing from -1 to 1 or vice versa)
                get_class($model)::where('id', $model->id)->increment('votes', 2 * $voteType);
                
                // Refresh the model to get the updated vote count
                $model->refresh();
                
                return response()->json([
                    'success' => true,
                    'action' => 'updated',
                    'votes' => $model->votes,
                    'user_vote' => $voteType === 1 ? 'up' : 'down',
                ]);
            }
        }

        // Create new vote
        DB::table('votes')->insert([
            'user_id' => $user->id,
            'votable_type' => get_class($model),
            'votable_id' => $model->id,
            'vote' => $voteType,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Update the votes count
        get_class($model)::where('id', $model->id)->increment('votes', $voteType);

        // Refresh the model to get the updated vote count
        $model->refresh();

        return response()->json([
            'success' => true,
            'action' => 'added',
            'votes' => $model->votes,
            'user_vote' => $voteType === 1 ? 'up' : 'down',
        ]);
    }

    /**
     * Delete a question.
     */
    public function deleteQuestion($localeOrId, $id = null)
    {
        // Handle locale parameter - if first param is locale, use second as ID
        if (in_array($localeOrId, ['rw', 'en'])) {
            $id = $id;
        } else {
            $id = $localeOrId;
        }
        
        $question = ForumQuestion::findOrFail($id);
        
        // Check if user is owner of the question or admin
        if ($question->user_id !== Auth::id() && !Auth::user()->hasRole('admin')) {
            if (request()->wantsJson()) {
                return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
            }
            abort(403);
        }

        // Delete all answers associated with the question
        $question->answers()->delete();
        
        // Delete the question
        $question->delete();

        if (request()->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Question deleted successfully'
            ]);
        }

        return redirect()
            ->route('forum.index')
            ->with('success', 'Question deleted successfully!');
    }

    /**
     * Delete an answer.
     */
    public function deleteAnswer($localeOrId, $id = null)
    {
        // Handle locale parameter - if first param is locale, use second as ID
        if (in_array($localeOrId, ['rw', 'en'])) {
            $id = $id;
        } else {
            $id = $localeOrId;
        }
        
        $answer = ForumAnswer::findOrFail($id);
        
        // Check if user is owner of the answer or admin
        if ($answer->user_id !== Auth::id() && !Auth::user()->hasRole('admin')) {
            if (request()->wantsJson()) {
                return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
            }
            abort(403);
        }

        // If this was the best answer, clear it from the question
        $question = $answer->question;
        if ($question->best_answer_id === $answer->id) {
            $question->update(['best_answer_id' => null]);
        }

        // Delete the answer
        $answer->delete();

        if (request()->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Answer deleted successfully'
            ]);
        }

        return back()->with('success', 'Answer deleted successfully!');
    }

    /**
     * Update a question.
     */
    public function updateQuestion(Request $request, $localeOrId, $id = null)
    {
        // Handle locale parameter - if first param is locale, use second as ID
        if (in_array($localeOrId, ['rw', 'en'])) {
            $id = $id;
        } else {
            $id = $localeOrId;
        }
        
        $question = ForumQuestion::findOrFail($id);
        
        // Check if user is owner of question or admin
        if ($question->user_id !== Auth::id() && !Auth::user()->hasRole('admin')) {
            if (request()->wantsJson()) {
                return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
            }
            abort(403);
        }

        $locale = app()->getLocale();
        $fallback = config('app.fallback_locale', 'rw');
        
        $validated = $request->validate([
            'content' => 'required|array',
            'content.'.$locale => 'required|string|min:10|max:255',
        ]);

        // Get existing content and update only current locale
        $existingContent = json_decode($question->content, true) ?: [];
        
        $existingContent[$locale] = $validated['content'][$locale];

        $question->update([
            'content' => json_encode($existingContent),
        ]);

        if (request()->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Question updated successfully',
                'title' => $existingContent[$locale],
                'content' => $existingContent[$locale]
            ]);
        }

        return back()->with('success', 'Question updated successfully!');
    }

    /**
     * Update an answer.
     */
    public function updateAnswer(Request $request, $localeOrId, $id = null)
    {
        // Handle locale parameter - if first param is locale, use second as ID
        if (in_array($localeOrId, ['rw', 'en'])) {
            $id = $id;
        } else {
            $id = $localeOrId;
        }
        
        $answer = ForumAnswer::findOrFail($id);
        
        // Check if user is owner of answer or admin
        if ($answer->user_id !== Auth::id() && !Auth::user()->hasRole('admin')) {
            if (request()->wantsJson()) {
                return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
            }
            abort(403);
        }

        $locale = app()->getLocale();
        $fallback = config('app.fallback_locale', 'en');
        
        $validated = $request->validate([
            'content' => 'required|array',
            "content.{$locale}" => 'required|string|min:10',
        ]);

        // Get existing content and update only current locale
        $existingContent = json_decode($answer->content, true) ?: [];
        $existingContent[$locale] = $validated['content'][$locale];

        $answer->update([
            'content' => json_encode($existingContent),
        ]);

        if (request()->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Answer updated successfully',
                'content' => $existingContent[$locale]
            ]);
        }

        return back()->with('success', 'Answer updated successfully!');
    }

    /**
     * Get popular topics for sidebar.
     */
    private function getPopularTopics()
    {
        // This is a simplified version - in a real app, you'd want to cache this
        $topics = ForumQuestion::select('topics')
            ->whereNotNull('topics')
            ->get()
            ->flatMap(function ($question) {
                return $question->topics;
            })
            ->countBy()
            ->sortDesc()
            ->take(10);

        return $topics;
    }

    /**
     * Post a new answer to a question.
     */
    public function postAnswer(Request $request, $questionId)
    {
        $locale = app()->getLocale();
        
        Log::info('postAnswer called', [
            'locale' => $locale,
            'questionId' => $questionId,
            'request_data' => $request->all(),
            'user_id' => Auth::id()
        ]);

        $request->validate([
            'content' => 'required|array',
            'content.*' => 'required|string|min:10|max:2000',
            'parent_id' => 'nullable|exists:forum_answers,id'
        ]);

        Log::info('Validation passed');

        $question = ForumQuestion::findOrFail($questionId);
        Log::info('Question found', ['question_id' => $question->id]);

        $answer = ForumAnswer::create([
            'question_id' => $questionId,
            'user_id' => Auth::id(),
            'content' => $request->content,
            'parent_id' => $request->parent_id,
            'votes' => 0
        ]);

        Log::info('Answer created', ['answer_id' => $answer->id]);

        $responseData = [
            'success' => true,
            'message' => 'Answer posted successfully.',
            'answer' => [
                'id' => $answer->id,
                'content' => $answer->content,
                'question_id' => $answer->question_id,
                'author' => [
                    'firstName' => Auth::user()->firstName,
                    'lastName' => Auth::user()->lastName
                ],
                'created_at' => $answer->created_at
            ]
        ];

        Log::info('Returning response', ['response_data' => $responseData]);

        return response()->json($responseData);
    }
}
