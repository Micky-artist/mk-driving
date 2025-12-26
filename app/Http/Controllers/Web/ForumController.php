<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\ForumQuestion;
use App\Models\ForumAnswer;
use App\Services\PointsService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ForumController extends Controller
{
    public function __construct(private PointsService $pointsService)
    {
    }

    /**
     * Display a listing of the forum questions with leaderboard.
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
                // Search in title JSON column for both 'en' and 'rw' locales
                $q->whereRaw("LOWER(JSON_UNQUOTE(title)) LIKE ?", ["%{$searchTerm}%"])
                  ->orWhereRaw("LOWER(JSON_UNQUOTE(title->'$.en')) LIKE ?", ["%{$searchTerm}%"])
                  ->orWhereRaw("LOWER(JSON_UNQUOTE(title->'$.rw')) LIKE ?", ["%{$searchTerm}%"])
                  // Search in content JSON column for both 'en' and 'rw' locales
                  ->orWhereRaw("LOWER(JSON_UNQUOTE(content)) LIKE ?", ["%{$searchTerm}%"])
                  ->orWhereRaw("LOWER(JSON_UNQUOTE(content->'$.en')) LIKE ?", ["%{$searchTerm}%"])
                  ->orWhereRaw("LOWER(JSON_UNQUOTE(content->'$.rw')) LIKE ?", ["%{$searchTerm}%"]);
            });
        }
        
        $questions = $query->paginate($perPage)->withQueryString();
            
        // Transform the paginated collection
        $questions->getCollection()->transform(function($question) {
            $locale = app()->getLocale();
            $fallbackLocale = config('app.fallback_locale', 'en');
            
            // Ensure title is properly decoded if it's a JSON string
            $titleData = is_string($question->title) ? json_decode($question->title, true) : $question->title;
            $title = is_array($titleData) 
                ? ($titleData[$locale] ?? $titleData[$fallbackLocale] ?? 'No title')
                : ($question->title ?? 'No title');
            
            // Ensure content is properly decoded if it's a JSON string
            $contentData = is_string($question->content) ? json_decode($question->content, true) : $question->content;
            $content = is_array($contentData)
                ? ($contentData[$locale] ?? $contentData[$fallbackLocale] ?? 'No content')
                : ($question->content ?? 'No content');
            
            return [
                'id' => $question->id,
                'title' => $title,
                'content' => $content,
                'createdAt' => $question->created_at,
                'updatedAt' => $question->updated_at,
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
                        'author' => [
                            'firstName' => $answer->user && $answer->user->first_name ? $answer->user->first_name : 'Anonymous',
                            'lastName' => $answer->user && $answer->user->last_name ? $answer->user->last_name : '',
                        ],
                        'createdAt' => $answer->created_at->toIso8601String(),
                    ];
                }),
            ];
        });

        // Get weekly leaderboard data (Duolingo-style)
        $leaderboard = $this->pointsService->getLeaderboard(10, 'weekly');
        
        // Get user's rank if authenticated
        $userRank = null;
        $userPoints = null;
        if (Auth::check()) {
            $userRank = $this->pointsService->getUserRank(Auth::id(), 'weekly');
            $userPoints = $this->pointsService->getUserPoints(Auth::id());
        }

        if ($request->wantsJson()) {
            return response()->json([
                'questions' => $questions,
                'leaderboard' => $leaderboard,
                'userRank' => $userRank,
                'userPoints' => $userPoints,
            ]);
        }

        $topics = collect(config('forum.topics', []));
        return view('forum.index', [
            'questions' => $questions,
            'topics' => $topics,
            'leaderboard' => $leaderboard,
            'userRank' => $userRank,
            'userPoints' => $userPoints,
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
        $locale = app()->getLocale();
        $fallback = config('app.fallback_locale', 'rw');
        
        $validated = $request->validate([
            'title' => 'required|array',
            'title.'.$locale => 'required|string|min:10|max:255',
            'content' => 'required|array',
            'content.'.$locale => 'required|string|min:20',
        ]);
        
        // Ensure we have both languages, using current language for fallback if needed
        $title = [
            $locale => $validated['title'][$locale],
            $fallback => $validated['title'][$fallback] ?? $validated['title'][$locale]
        ];
        
        $content = [
            $locale => $validated['content'][$locale],
            $fallback => $validated['content'][$fallback] ?? $validated['content'][$locale]
        ];

        $question = new ForumQuestion([
            'title' => json_encode($title),
            'content' => json_encode($content),
            'user_id' => Auth::id(),
            'is_approved' => true,
        ]);

        $question->save();

        // Award points for asking a question
        $this->pointsService->awardPoints(Auth::id(), 'question_asked', [
            'question_id' => $question->id,
            'forum_question_id' => $question->id,
        ]);

        if ($request->wantsJson()) {
            return response()->json([
                'id' => $question->id,
                'title' => $question->title,
                'content' => $question->content,
                'author' => [
                    'firstName' => $question->user && $question->user->first_name ? $question->user->first_name : 'Anonymous',
                    'lastName' => $question->user && $question->user->last_name ? $question->user->last_name : '',
                ],
                'createdAt' => $question->created_at->toIso8601String(),
                'answers' => [],
            ], 201);
        }

        return redirect()
            ->route('forum.index')
            ->with('success', 'Your question has been posted!');
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
        $titleData = is_string($question->title) ? json_decode($question->title, true) : $question->title;
        $contentData = is_string($question->content) ? json_decode($question->content, true) : $question->content;
        
        $questionData = [
            'id' => $question->id,
            'title' => is_array($titleData) 
                ? ($titleData[$locale] ?? $titleData[$fallbackLocale] ?? 'No title')
                : $question->title,
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
    public function vote(Request $request, $type, $id)
    {
        $request->validate([
            'vote' => 'required|in:up,down',
        ]);

        $model = $type === 'question' 
            ? ForumQuestion::findOrFail($id)
            : ForumAnswer::findOrFail($id);

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
                
                return response()->json([
                    'success' => true,
                    'action' => 'removed',
                    'votes' => $model->votes,
                ]);
            } 
            // If different vote, update it
            else {
                DB::table('votes')
                    ->where('id', $existingVote->id)
                    ->update(['vote' => $voteType]);
                
                // Update votes count (add 2 because we're changing from -1 to 1 or vice versa)
                get_class($model)::where('id', $model->id)->increment('votes', 2 * $voteType);
                
                return response()->json([
                    'success' => true,
                    'action' => 'updated',
                    'votes' => $model->votes,
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

        return response()->json([
            'success' => true,
            'action' => 'added',
            'votes' => $model->votes,
        ]);
    }

    /**
     * Get popular topics for the sidebar.
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
}
