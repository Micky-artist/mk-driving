<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use App\Models\News;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class NewsController extends Controller
{
    /**
     * Display a listing of the news articles.
     */
    public function index()
    {
        $news = News::with('author')
            ->latest()
            ->paginate(10);

        return view('admin.news.index', compact('news'));
    }

    /**
     * Show the form for creating a new news article.
     */
    public function create()
    {
        return view('admin.news.create');
    }

    /**
     * Store a newly created news article in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'content' => ['required', 'string'],
            'images.*' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif', 'max:2048'],
            'is_published' => ['boolean'],
            'category' => ['nullable', 'string', 'max:100'],
        ]);

        $news = new News();
        $news->title = $validated['title'];
        $news->slug = Str::slug($validated['title']) . '-' . time();
        $news->content = $validated['content'];
        $news->author_id = Auth::id();
        $news->is_published = $request->has('is_published');
        $news->category = $validated['category'] ?? null;

        // Handle image uploads
        if ($request->hasFile('images')) {
            $images = [];
            foreach ($request->file('images') as $image) {
                $path = $image->store('news/images', 'public');
                $images[] = $path;
            }
            $news->images = $images;
        }

        $news->save();

        return redirect()->route('admin.news.index')
            ->with('success', 'News article created successfully.');
    }

    /**
     * Show the form for editing the specified news article.
     */
    public function edit(News $news)
    {
        return view('admin.news.edit', compact('news'));
    }

    /**
     * Update the specified news article in storage.
     */
    public function update(Request $request, News $news)
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'content' => ['required', 'string'],
            'images.*' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif', 'max:2048'],
            'is_published' => ['boolean'],
            'category' => ['nullable', 'string', 'max:100'],
        ]);

        $news->title = $validated['title'];
        $news->content = $validated['content'];
        $news->is_published = $request->has('is_published');
        $news->category = $validated['category'] ?? null;

        // Handle image uploads
        if ($request->hasFile('images')) {
            // Delete old images
            if ($news->images) {
                foreach ($news->images as $image) {
                    Storage::disk('public')->delete($image);
                }
            }

            $images = [];
            foreach ($request->file('images') as $image) {
                $path = $image->store('news/images', 'public');
                $images[] = $path;
            }
            $news->images = $images;
        }

        $news->save();

        return redirect()->route('admin.news.index')
            ->with('success', 'News article updated successfully.');
    }

    /**
     * Remove the specified news article from storage.
     */
    public function destroy(News $news)
    {
        // Delete associated images
        if ($news->images) {
            foreach ($news->images as $image) {
                Storage::disk('public')->delete($image);
            }
        }

        $news->delete();

        return redirect()->route('admin.news.index')
            ->with('success', 'News article deleted successfully.');
    }
}
