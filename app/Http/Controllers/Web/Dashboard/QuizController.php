<?php

namespace App\Http\Controllers\Web\Dashboard;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Quiz;
use Illuminate\Support\Facades\Auth;

class QuizController extends Controller
{
    /**
     * Display the user's quizzes.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $user = Auth::user();
        
        // Get active quizzes with user's attempts and questions count
        $quizzes = Quiz::where('is_active', true)
            ->with(['attempts' => function($query) use ($user) {
                $query->where('user_id', $user->id)
                      ->orderBy('created_at', 'desc');
            }, 'subscriptionPlan'])
            ->withCount('questions')
            ->orderBy('created_at', 'desc')
            ->paginate(9);

        return view('dashboard.quizzes.index', [
            'quizzes' => $quizzes,
            'user' => $user
        ]);
    }
}
