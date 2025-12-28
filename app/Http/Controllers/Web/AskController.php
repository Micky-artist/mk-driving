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
        
        // Check if user is verified
        if (!Auth::user()->email_verified_at) {
            return redirect()->route('profile.show', ['locale' => app()->getLocale()])
                ->with('status', 'Please verify your email address before asking questions in the forum.');
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
        
        // Check if user is verified
        if (!Auth::user()->email_verified_at) {
            return redirect()->route('profile.show', ['locale' => app()->getLocale()])
                ->with('status', 'Please verify your email address before asking questions in the forum.');
        }
        
        $locale = app()->getLocale();
        $fallback = config('app.fallback_locale', 'rw');
        
        $validated = $request->validate([
            'title' => 'required|array',
            'title.'.$locale => 'required|string|min:10|max:255',
            'content' => 'required|array',
            'content.'.$locale => 'required|string|min:20',
        ], [
            'title.required' => __('forum.validation.title_required'),
            'title.'.$locale.'.required' => __('forum.validation.title_required'),
            'title.'.$locale.'.min' => __('forum.validation.title_min', ['min' => 10]),
            'title.'.$locale.'.max' => __('forum.validation.title_max', ['max' => 255]),
            'content.required' => __('forum.validation.content_required'),
            'content.'.$locale.'.required' => __('forum.validation.content_required'),
            'content.'.$locale.'.min' => __('forum.validation.content_min', ['min' => 20]),
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
        $this->pointsService->awardPoints(Auth::id(), 'question');

        return redirect()->route('forum.index', ['locale' => $locale])
            ->with('success', 'Your question has been posted successfully!');
    }
}
