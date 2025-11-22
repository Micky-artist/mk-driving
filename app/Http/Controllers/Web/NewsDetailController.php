<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\News;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;

class NewsDetailController extends Controller
{
    public function show($slug)
    {
        $news = News::where('slug', $slug)
            ->where('is_published', true)
            ->with('author')
            ->firstOrFail();

        // Format the news data for the view
        $formattedNews = [
            'id' => $news->id,
            'title' => $news->title,
            'slug' => $news->slug,
            'content' => $news->content,
            'images' => $news->images ? json_decode($news->images, true) : [],
            'created_at' => $news->created_at,
            'author' => [
                'first_name' => $news->author->first_name,
                'last_name' => $news->author->last_name,
            ],
            'read_time' => $this->calculateReadTime($news->content),
        ];

        return view('news.detail', [
            'news' => $formattedNews,
            'meta_title' => $news->meta_title ?? $news->title,
            'meta_description' => $news->meta_description ?? \Illuminate\Support\Str::limit(strip_tags($news->content), 160),
            'meta_keywords' => $news->meta_keywords ?? '',
        ]);
    }

    protected function calculateReadTime($content)
    {
        if (empty($content)) return '1 min read';
        
        $wordCount = str_word_count(strip_tags($content));
        $minutes = ceil($wordCount / 200); // Average reading speed
        return max(1, $minutes) . ' min read';
    }
}
