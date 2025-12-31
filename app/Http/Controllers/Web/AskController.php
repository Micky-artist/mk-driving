<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\ForumQuestion;
use App\Services\PointsService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AskController extends Controller
{
    public function __construct(private PointsService $pointsService)
    {
    }

    /**
     * Show the ask question form
     */
    public function index()
    {
        // Check if user is authenticated
        if (!Auth::check()) {
            return redirect()->route('login', ['locale' => app()->getLocale()])
                ->with('status', __('auth.forum_login_required'));
        }
        
        $topics = config('forum.topics', []);
        return view('forum.create', compact('topics'));
    }

    /**
     * Store a new question
     */
    public function store(Request $request)
    {
        // Check if user is authenticated
        if (!Auth::check()) {
            return redirect()->route('login', ['locale' => app()->getLocale()])
                ->with('status', __('auth.forum_login_required'));
        }
        
        $locale = app()->getLocale();
        $fallback = config('app.fallback_locale', 'rw');
        
        $validated = $request->validate([
            'content' => 'required|array',
            'content.'.$locale => 'required|string|min:10|max:255',
        ], [
            'content.required' => __('forum.validation.content_required'),
            'content.'.$locale.'.required' => __('forum.validation.content_required'),
            'content.'.$locale.'.min' => __('forum.validation.content_min', ['min' => 10]),
            'content.'.$locale.'.max' => __('forum.validation.content_max', ['max' => 255]),
        ]);
        
        // Ensure we have both languages, using current language for fallback if needed
        $content = [
            $locale => $validated['content'][$locale],
            $fallback => $validated['content'][$fallback] ?? $validated['content'][$locale]
        ];

        $question = new ForumQuestion([
            'content' => json_encode($content),
            'user_id' => Auth::id(),
            'is_approved' => true,
        ]);

        $question->save();

        // Award points for asking a question
        $this->pointsService->awardPoints(Auth::id(), 'question');

        return redirect()->route('forum.index', ['locale' => $locale])
            ->with('success', 'Your question has been posted successfully!');
    }
}
