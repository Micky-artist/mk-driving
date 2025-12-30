<?php

namespace App\Http\Controllers;

use App\Models\News;
use App\Models\ForumQuestion;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class NewsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $news = News::published()
            ->orderBy('published_at', 'desc')
            ->paginate(12);
            
        return view('news.index', compact('news'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $this->authorize('create', News::class);
        return view('news.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $this->authorize('create', News::class);
        
        $validated = $request->validate([
            'title' => 'required|array',
            'title.en' => 'required|string|max:255',
            'title.rw' => 'required|string|max:255',
            'excerpt' => 'required|array',
            'excerpt.en' => 'required|string|max:500',
            'excerpt.rw' => 'required|string|max:500',
            'content' => 'required|array',
            'content.en' => 'required|string',
            'content.rw' => 'required|string',
            'featured_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'category' => 'required|string|in:announcement,article,update,safety,promotion',
            'type' => 'required|string|in:announcement,article,promotion',
            'status' => 'required|string|in:draft,published',
            'featured' => 'boolean',
            'published_at' => 'nullable|date',
            'create_forum_discussion' => 'boolean',
        ]);

        $validated['slug'] = Str::slug($validated['title']['en']);
        $validated['user_id'] = auth()->id();
        
        if ($validated['status'] === 'published' && !$validated['published_at']) {
            $validated['published_at'] = now();
        }

        if ($request->hasFile('featured_image')) {
            $validated['featured_image'] = $request->file('featured_image')->store('news', 'public');
        }

        $news = News::create($validated);

        // Create forum discussion if requested and this is an announcement
        if ($request->boolean('create_forum_discussion') && $news->type === 'announcement') {
            $news->createForumDiscussion();
        }

        return redirect()
            ->route('news.show', ['locale' => app()->getLocale(), 'news' => $news->slug])
            ->with('success', __('news.created_successfully'));
    }

    /**
     * Display the specified resource.
     */
    public function show($locale, $news)
    {
        // Ignore locale parameter, use the news slug
        $newsItem = News::where('slug', $news)
            ->published()
            ->firstOrFail();
            
        $newsItem->incrementViews();
        
        return view('news.show', [
            'news' => $newsItem,
            'relatedNews' => collect() // Empty collection for now since category field doesn't exist
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(News $news)
    {
        $this->authorize('update', $news);
        return view('news.edit', compact('news'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, News $news)
    {
        $this->authorize('update', $news);
        
        $validated = $request->validate([
            'title' => 'required|array',
            'title.en' => 'required|string|max:255',
            'title.rw' => 'required|string|max:255',
            'excerpt' => 'required|array',
            'excerpt.en' => 'required|string|max:500',
            'excerpt.rw' => 'required|string|max:500',
            'content' => 'required|array',
            'content.en' => 'required|string',
            'content.rw' => 'required|string',
            'featured_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'category' => 'required|string|in:announcement,article,update,safety,promotion',
            'type' => 'required|string|in:announcement,article,promotion',
            'status' => 'required|string|in:draft,published',
            'featured' => 'boolean',
            'published_at' => 'nullable|date',
            'change_summary' => 'nullable|string|max:255',
        ]);

        // Create version before updating if content changed
        $oldData = [
            'title' => $news->getRawOriginal('title'),
            'content' => $news->getRawOriginal('content'),
            'excerpt' => $news->getRawOriginal('excerpt'),
        ];

        $contentChanged = (
            $oldData['title'] != $validated['title'] ||
            $oldData['content'] != $validated['content'] ||
            $oldData['excerpt'] != $validated['excerpt']
        );

        if ($contentChanged) {
            $news->createVersion($oldData, $validated['change_summary'] ?? null);
        }

        $validated['slug'] = Str::slug($validated['title']['en']);
        
        if ($validated['status'] === 'published' && !$news->published_at) {
            $validated['published_at'] = now();
        }

        if ($request->hasFile('featured_image')) {
            $validated['featured_image'] = $request->file('featured_image')->store('news', 'public');
        }

        $news->update($validated);

        return redirect()
            ->route('news.show', ['locale' => app()->getLocale(), 'news' => $news->slug])
            ->with('success', __('news.updated_successfully'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(News $news)
    {
        $this->authorize('delete', $news);
        
        $news->delete();

        return redirect()
            ->route('news.index', ['locale' => app()->getLocale()])
            ->with('success', __('news.deleted_successfully'));
    }

    /**
     * Share news to forum for discussion
     */
    public function shareToForum(News $news)
    {
        $this->authorize('view', $news);
        
        if ($news->forum_question_id) {
            return redirect()
                ->route('forum.show', ['locale' => app()->getLocale(), 'question' => $news->forumQuestion->id])
                ->with('info', __('news.already_shared_to_forum'));
        }

        $forumQuestion = $news->shareToForum();

        return redirect()
            ->route('forum.show', ['locale' => app()->getLocale(), 'question' => $forumQuestion->id])
            ->with('success', __('news.shared_to_forum_successfully'));
    }

    /**
     * Like a news article
     */
    public function like(News $news)
    {
        $this->authorize('view', $news);
        
        $news->incrementLikes();

        return back()->with('success', __('news.liked_successfully'));
    }

    /**
     * Get news statistics
     */
    public function stats()
    {
        $stats = [
            'total_news' => News::count(),
            'published_news' => News::published()->count(),
            'total_views' => News::sum('views'),
            'total_likes' => News::sum('likes'),
            'total_comments' => News::sum('comments'),
            'featured_news' => News::featured()->count(),
            'announcements' => News::announcements()->count(),
            'articles' => News::articles()->count(),
        ];

        return response()->json($stats);
    }
}
