<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\News\CreateNewsRequest;
use App\Http\Requests\News\UpdateNewsRequest;
use App\Models\News;
use App\Services\UploadService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

class NewsController extends Controller
{
    protected $uploadService;

    public function __construct(UploadService $uploadService)
    {
        $this->uploadService = $uploadService;
        $this->middleware('auth:api', ['except' => ['index', 'showBySlug']]);
        $this->middleware('role:admin', ['except' => ['index', 'showBySlug']]);
    }

    /**
     * Get paginated list of news articles
     */
    public function index(Request $request): JsonResponse
    {
        $perPage = $request->input('per_page', 10);
        $page = $request->input('page', 1);

        $news = News::with('author:id,first_name,last_name')
            ->latest()
            ->paginate($perPage, ['*'], 'page', $page);

        return response()->json([
            'data' => $news->items(),
            'meta' => [
                'total' => $news->total(),
                'per_page' => $news->perPage(),
                'current_page' => $news->currentPage(),
                'last_page' => $news->lastPage(),
            ]
        ]);
    }

    /**
     * Create a new news article
     */
    public function store(CreateNewsRequest $request): JsonResponse
    {
        $data = $request->validated();
        $user = Auth::user();
        if (!$user) {
            return response()->json(['message' => 'Unauthenticated.'], 401);
        }
        
        // Generate slug
        $slug = Str::slug($data['title']);
        if (News::where('slug', $slug)->exists()) {
            $slug = $slug . '-' . time();
        }

        // Handle image uploads
        $imagePaths = [];
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $path = $this->uploadService->upload($image, 'news');
                $imagePaths[] = $path;
            }
        }

        $news = News::create([
            'title' => $data['title'],
            'slug' => $slug,
            'content' => $data['content'],
            'author_id' => $user->id,
            'images' => $imagePaths,
        ]);

        return response()->json($news->load('author'), 201);
    }

    /**
     * Get news article by slug
     */
    public function showBySlug(string $slug): JsonResponse
    {
        $news = News::with('author:id,first_name,last_name')
            ->where('slug', $slug)
            ->firstOrFail();
            
        return response()->json($news);
    }

    /**
     * Get news article by ID
     */
    public function show(string $id): JsonResponse
    {
        $news = News::findOrFail($id);
        return response()->json($news);
    }

    /**
     * Update a news article
     */
    public function update(UpdateNewsRequest $request, string $id): JsonResponse
    {
        $news = News::findOrFail($id);
        $data = $request->validated();

        // Update slug if title was changed
        if (isset($data['title']) && $data['title'] !== $news->title) {
            $slug = Str::slug($data['title']);
            if (News::where('slug', $slug)->where('id', '!=', $id)->exists()) {
                $slug = $slug . '-' . time();
            }
            $data['slug'] = $slug;
        }

        // Handle image uploads
        if ($request->hasFile('images')) {
            $imagePaths = $news->images ?? [];
            
            // Upload new images
            foreach ($request->file('images') as $image) {
                $path = $this->uploadService->upload($image, 'news');
                $imagePaths[] = $path;
            }
            
            $data['images'] = $imagePaths;
        }

        $news->update($data);

        return response()->json($news->load('author'));
    }

    /**
     * Delete a news article
     */
    public function destroy(string $id): JsonResponse
    {
        $news = News::findOrFail($id);
        
        // Delete associated images
        if (!empty($news->images)) {
            foreach ($news->images as $image) {
                Storage::disk('public')->delete($image);
            }
        }
        
        $news->delete();
        
        return response()->json(['message' => 'News article deleted successfully']);
    }
}
