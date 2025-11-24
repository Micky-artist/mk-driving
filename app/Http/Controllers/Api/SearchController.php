<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Services\SearchService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class SearchController extends Controller
{
    protected $searchService;

    public function __construct(SearchService $searchService)
    {
        $this->searchService = $searchService;
        $this->middleware('auth:api', ['except' => ['globalSearch']]);
    }

    /**
     * Perform a global search across all content types
     * 
     * @param Request $request
     * @return JsonResponse
     */
    public function globalSearch(Request $request): JsonResponse
    {
        $request->validate([
            'q' => 'required|string|min:2|max:255',
        ]);

        $query = trim($request->input('q'));
        $user = Auth::guard('api')->user();
        
        $results = $this->searchService->globalSearch($query, $user);
        
        return response()->json([
            'success' => true,
            'data' => $results,
        ]);
    }

    /**
     * Search only in quizzes
     * 
     * @param Request $request
     * @return JsonResponse
     */
    public function searchQuizzes(Request $request): JsonResponse
    {
        $request->validate([
            'q' => 'required|string|min:2|max:255',
        ]);

        $query = trim($request->input('q'));
        $user = Auth::guard('api')->user();
        $userRole = $user ? $user->role : null;
        
        $results = $this->searchService->searchQuizzes($query, $userRole);
        
        return response()->json([
            'success' => true,
            'data' => $results,
        ]);
    }

    /**
     * Search only in subscription plans (admin only)
     * 
     * @param Request $request
     * @return JsonResponse
     */
    public function searchSubscriptionPlans(Request $request): JsonResponse
    {
        $request->validate([
            'q' => 'required|string|min:2|max:255',
        ]);

        $query = trim($request->input('q'));
        
        $results = $this->searchService->searchSubscriptionPlans($query);
        
        return response()->json([
            'success' => true,
            'data' => $results,
        ]);
    }

    /**
     * Search only in news
     * 
     * @param Request $request
     * @return JsonResponse
     */
    public function searchNews(Request $request): JsonResponse
    {
        $request->validate([
            'q' => 'required|string|min:2|max:255',
        ]);

        $query = trim($request->input('q'));
        
        $results = $this->searchService->searchNews($query);
        
        return response()->json([
            'success' => true,
            'data' => $results,
        ]);
    }

    /**
     * Search only in users (admin only)
     * 
     * @param Request $request
     * @return JsonResponse
     */
    public function searchUsers(Request $request): JsonResponse
    {
        $request->validate([
            'q' => 'required|string|min:2|max:255',
        ]);

        $query = trim($request->input('q'));
        
        $results = $this->searchService->searchUsers($query);
        
        return response()->json([
            'success' => true,
            'data' => $results,
        ]);
    }
}
