<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Quiz;
use App\Models\Bookmark;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

class BookmarkController extends Controller
{
    /**
     * Toggle bookmark status for a quiz
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $quizId
     * @return \Illuminate\Http\Response
     */
    public function toggle(Request $request, $quizId)
    {
        $user = Auth::user();
        $quiz = Quiz::findOrFail($quizId);
        
        $bookmark = $user->bookmarks()->where('quiz_id', $quiz->id)->first();
        
        if ($bookmark) {
            $bookmark->delete();
            return response()->json([
                'status' => 'removed',
                'message' => 'Quiz removed from bookmarks.'
            ]);
        }
        
        $user->bookmarks()->create([
            'quiz_id' => $quiz->id
        ]);
        
        return response()->json([
            'status' => 'added',
            'message' => 'Quiz added to bookmarks.'
        ]);
    }
    
    /**
     * Check if a quiz is bookmarked by the user
     *
     * @param  int  $quizId
     * @return \Illuminate\Http\Response
     */
    public function check($quizId)
    {
        $user = Auth::user();
        $isBookmarked = $user->bookmarks()->where('quiz_id', $quizId)->exists();
        
        return response()->json([
            'is_bookmarked' => $isBookmarked
        ]);
    }
    
    /**
     * Get all bookmarked quizzes for the authenticated user
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $bookmarks = Auth::user()->bookmarks()->with('quiz')->get();
        
        return response()->json([
            'data' => $bookmarks->pluck('quiz')
        ]);
    }
}
