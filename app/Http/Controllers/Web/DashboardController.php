<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Quiz;
use App\Models\News;
use App\Models\ForumQuestion;
use App\Models\QuizAttempt;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    /**
     * Display the dashboard.
     */
public function index()
    {
        try {
            $user = Auth::user();
            
            // Get latest quizzes
            $quizzes = Quiz::query()
                ->where('is_published', true)
                ->orderBy('created_at', 'desc')
                ->take(3)
                ->get();
                
            // Get latest news
            $news = News::query()
                ->published()
                ->orderBy('published_at', 'desc')
                ->take(3)
                ->get();
                
            // Get recent forum questions with user relationship
            $questions = ForumQuestion::with(['user' => function($query) {
                    $query->select('id', 'first_name', 'last_name');
                }])
                ->orderBy('created_at', 'desc')
                ->take(5)
                ->get();
                
            // Get user's quiz attempts with quiz relationship
            $userQuizzes = QuizAttempt::query()
                ->where('user_id', $user->id)
                ->with(['quiz' => function($query) {
                    $query->select('id', 'title', 'description');
                }])
                ->orderBy('completed_at', 'desc')
                ->take(3)
                ->get();
                
            // Get in-progress quizzes with progress calculation
            $inProgressQuizzes = $user->quizAttempts()
                ->with('quiz')
                ->where('status', 'in_progress')
                ->orderBy('updated_at', 'desc')
                ->get()
                ->map(function ($attempt) {
                    $totalQuestions = $attempt->quiz->questions_count ?? 0;
                    $attempt->progress = $totalQuestions > 0 
                        ? round(($attempt->answers_count / $totalQuestions) * 100) 
                        : 0;
                    return $attempt;
                });
                
            // Get recently completed quizzes
            $completedQuizzes = $user->quizAttempts()
                ->with('quiz')
                ->where('status', 'completed')
                ->orderBy('completed_at', 'desc')
                ->take(5)
                ->get();
                
            return view('dashboard.index', [
                'quizzes' => $quizzes,
                'news' => $news,
                'questions' => $questions,
                'userQuizzes' => $userQuizzes,
                'user' => $user,
                'inProgressQuizzes' => $inProgressQuizzes,
                'completedQuizzes' => $completedQuizzes
            ]);
            
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->with('error', 'Unable to load dashboard data. Please try again.');
        }
    }
}
