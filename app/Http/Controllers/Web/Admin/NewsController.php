<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use App\Models\News;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;

class NewsController extends Controller
{
    /**
     * Display a listing of news articles.
     *
     * @return \Illuminate\View\View
     */
    public function index(): View
    {
        $news = News::latest()->paginate(10);
        return view('admin.news.index', compact('news'));
    }

    /**
     * Show the form for creating a new news article.
     *
     * @return \Illuminate\View\View
     */
    public function create(): View
    {
        return view('admin.news.create');
    }

    /**
     * Store a newly created news article.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'excerpt' => 'nullable|string|max:500',
            'image' => 'nullable|image|max:2048',
            'is_published' => 'boolean',
            'published_at' => 'nullable|date',
        ]);

        try {
            if ($request->hasFile('image')) {
                $validated['image_path'] = $request->file('image')->store('news', 'public');
            }

            $validated['slug'] = Str::slug($validated['title']);
            $validated['user_id'] = auth()->id();
            $validated['is_published'] = $request->has('is_published');

            News::create($validated);

            return redirect()->route('admin.news.index')
                ->with('success', 'News article created successfully.');
        } catch (\Exception $e) {
            if (isset($validated['image_path'])) {
                Storage::disk('public')->delete($validated['image_path']);
            }
            return back()->withInput()->with('error', 'Failed to create news article.');
        }
    }

    /**
     * Show the form for editing the specified news article.
     *
     * @param  \App\Models\News  $news
     * @return \Illuminate\View\View
     */
    public function edit(News $news): View
    {
        return view('admin.news.edit', compact('news'));
    }

    /**
     * Update the specified news article.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\News  $news
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, News $news)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'excerpt' => 'nullable|string|max:500',
            'image' => 'nullable|image|max:2048',
            'is_published' => 'boolean',
            'published_at' => 'nullable|date',
        ]);

        try {
            if ($request->hasFile('image')) {
                // Delete old image if exists
                if ($news->image_path) {
                    Storage::disk('public')->delete($news->image_path);
                }
                $validated['image_path'] = $request->file('image')->store('news', 'public');
            }

            $validated['slug'] = Str::slug($validated['title']);
            $validated['is_published'] = $request->has('is_published');

            $news->update($validated);

            return redirect()->route('admin.news.index')
                ->with('success', 'News article updated successfully.');
        } catch (\Exception $e) {
            if (isset($validated['image_path'])) {
                Storage::disk('public')->delete($validated['image_path']);
            }
            return back()->withInput()->with('error', 'Failed to update news article.');
        }
    }

    /**
     * Remove the specified news article.
     *
     * @param  \App\Models\News  $news
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(News $news)
    {
        try {
            if ($news->image_path) {
                Storage::disk('public')->delete($news->image_path);
            }
            $news->delete();
            return redirect()->route('admin.news.index')
                ->with('success', 'News article deleted successfully.');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to delete news article.');
        }
    }

    /**
     * Remove an image from a news article.
     *
     * @param  int  $id
     * @param  int  $imageIndex
     * @return \Illuminate\Http\JsonResponse
     */
    public function removeImage($id, $imageIndex)
    {
        $news = News::findOrFail($id);
        
        if ($news->image_path) {
            Storage::disk('public')->delete($news->image_path);
            $news->image_path = null;
            $news->save();
            
            return response()->json([
                'success' => true,
                'message' => 'Image removed successfully.'
            ]);
        }
        
        return response()->json([
            'success' => false,
            'message' => 'No image found to remove.'
        ], 404);
    }
}
