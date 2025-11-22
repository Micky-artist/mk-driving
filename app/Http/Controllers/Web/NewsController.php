<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\News;
use Illuminate\Http\Request;

class NewsController extends Controller
{
    public function index(Request $request)
    {
        $categories = ['all', 'advice', 'police', 'trends', 'vehicle', 'news'];
        $activeCategory = $request->get('category', 'all');
        $search = $request->get('search', '');
        
        $query = News::with('author')
            ->published() // Only show published news
            ->when($activeCategory !== 'all', function($q) use ($activeCategory) {
                return $q->where('category', $activeCategory);
            })
            ->when($search, function($q) use ($search) {
                return $q->where(function($query) use ($search) {
                    $query->where('title', 'like', "%{$search}%")
                        ->orWhere('content', 'like', "%{$search}%")
                        ->orWhereHas('author', function($q) use ($search) {
                            $q->where('first_name', 'like', "%{$search}%")
                              ->orWhere('last_name', 'like', "%{$search}%");
                        });
                });
            })
            ->orderBy('created_at', 'desc')
            ->latest();

        if ($request->wantsJson()) {
            return $query->paginate(9);
        }

        return view('news.index', [
            'articles' => $query->paginate(9),
            'categories' => $categories,
            'activeCategory' => $activeCategory,
            'searchQuery' => $search
        ]);
    }

    /**
     * Display the news dashboard.
     *
     * @return \Illuminate\View\View
     */
    public function dashboardIndex(Request $request)
    {
        $categories = ['all', 'advice', 'police', 'trends', 'vehicle', 'news'];
        $activeCategory = $request->get('category', 'all');
        $search = $request->get('search', '');
        
        $query = News::with('author')
            ->published()
            ->when($activeCategory !== 'all', function($q) use ($activeCategory) {
                return $q->where('category', $activeCategory);
            })
            ->when($search, function($q) use ($search) {
                return $q->where(function($query) use ($search) {
                    $query->where('title', 'like', "%{$search}%")
                        ->orWhere('content', 'like', "%{$search}%")
                        ->orWhereHas('author', function($q) use ($search) {
                            $q->where('first_name', 'like', "%{$search}%")
                              ->orWhere('last_name', 'like', "%{$search}%");
                        });
                });
            })
            ->orderBy('created_at', 'desc');

        $news = $query->paginate(9);

        return view('dashboard.news.index', compact('news', 'categories', 'activeCategory', 'search'));
    }

    /**
     * Display the specified news article.
     *
     * @param  string  $slug
     * @return \Illuminate\View\View
     */
    public function dashboardShow($slug)
    {
        $article = News::with('author')
            ->where('slug', $slug)
            ->published()
            ->firstOrFail();

        // Calculate read time
        $wordCount = str_word_count(strip_tags($article->content));
        $readTime = max(1, ceil($wordCount / 200));

        // Get related articles
        $relatedArticles = News::with('author')
            ->where('id', '!=', $article->id)
            ->where('category', $article->category)
            ->published()
            ->take(3)
            ->get();

        return view('dashboard.news.show', compact('article', 'readTime', 'relatedArticles'));
    }

    public function show($locale, $slug)
    {
        // Set the application locale
        app()->setLocale($locale);
        
        $article = News::with('author')
            ->where('slug', $slug)
            ->published()
            ->firstOrFail();

        $relatedArticles = News::with('author')
            ->where('id', '!=', $article->id)
            ->published()
            ->latest()
            ->take(3)
            ->get();

        return view('news.show', [
            'article' => $article,
            'relatedArticles' => $relatedArticles
        ]);
    }
}
