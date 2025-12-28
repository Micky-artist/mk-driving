<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Quiz;
use App\Models\QuizAttempt;
use App\Models\Subscription;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Pagination\LengthAwarePaginator;

class DashboardController extends Controller
{
    /**
     * Display the dashboard index page.
     *
     * @return \Illuminate\View\View
     */
    /**
     * Display the dashboard index page.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $user = Auth::user();
        
        // Get user's active and pending subscriptions with plan details and quiz count
        $currentSubscriptions = $user->subscriptions()
            ->with(['plan' => function($query) {
                $query->withCount('quizzes');
            }])
            ->whereIn('status', ['ACTIVE', 'PENDING'])
            ->where(function($query) {
                $query->where('ends_at', '>=', now())
                      ->orWhere('status', 'PENDING'); // Include pending regardless of end date
            })
            ->get()
            ->each(function($subscription) use ($user) {
                $subscription->quizzes_count = $subscription->plan->quizzes_count ?? 0;
                
                // Get quizzes for this plan
                $subscription->quizzes = $subscription->plan->quizzes()
                    ->where('is_active', true)
                    ->with(['attempts' => function($query) use ($user) {
                        $query->where('user_id', $user->id)->orderBy('created_at', 'desc');
                    }])
                    ->take(10) // Limit to 10 quizzes for carousel
                    ->get()
                    ->map(function($quiz) use ($user) {
                        // Check if user has attempted this quiz
                        $attempt = $quiz->attempts->first();
                        $quiz->user_attempt = $attempt;
                        $quiz->attempt_status = $attempt ? $attempt->status : 'not_started';
                        $quiz->score = $attempt ? $attempt->score_percentage : null;
                        return $quiz;
                    });
                
                return $subscription;
            });
            
        // Get available subscription plans
        $availablePlans = \App\Models\SubscriptionPlan::where('is_active', true)
            ->orderBy('price')
            ->get();
            
        // Get user's available quizzes
        $availableQuizzes = $this->getAvailableQuizzes($currentSubscriptions)->count();
        
        // Get user's quiz attempts
        $quizAttempts = QuizAttempt::where('user_id', $user->id)
            ->with('quiz')
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();
            
        // Get count of completed and in-progress quizzes
        $completedAttempts = QuizAttempt::where('user_id', $user->id)
            ->where('status', 'completed')
            ->get();
            
        $inProgressCount = QuizAttempt::where('user_id', $user->id)
            ->where('status', 'in_progress')
            ->count();
            
        // Calculate average score from completed attempts
        $averageScore = 0;
        if ($completedAttempts->count() > 0) {
            $averageScore = $completedAttempts->avg('score');
        }
        
        // Get new quizzes - show a mix of free and premium quizzes
        $attemptedQuizIds = $user->quizAttempts()->pluck('quiz_id');
        $hasActiveSubscription = !$currentSubscriptions->isEmpty();
        
        // Get 3 newest quizzes (both free and premium) that user hasn't attempted
        $newQuizzes = Quiz::where('is_active', true)
            ->whereNotIn('id', $attemptedQuizIds)
            ->with('subscriptionPlan') // Eager load subscription plan
            ->orderBy('created_at', 'desc')
            ->take(6) // Get a few more than needed in case we need to filter
            ->get()
            ->map(function($quiz) use ($hasActiveSubscription, $currentSubscriptions) {
                // Check if quiz is locked based on subscription status
                $quiz->is_locked = $quiz->subscription_plan_slug && 
                                 ($hasActiveSubscription ? 
                                     !$currentSubscriptions->contains('subscription_plan_slug', $quiz->subscription_plan_slug) : 
                                     true);
                return $quiz;
            });
            
        // If user has no active subscription, make sure we have some premium quizzes to show
        if (!$hasActiveSubscription) {
            $premiumQuizzes = $newQuizzes->where('subscription_plan_slug', '!==', null);
            if ($premiumQuizzes->count() === 0) {
                // If no premium quizzes in the initial fetch, get some
                $additionalPremium = Quiz::where('is_active', true)
                    ->whereNotNull('subscription_plan_slug')
                    ->whereNotIn('id', $attemptedQuizIds)
                    ->with('subscriptionPlan')
                    ->orderBy('created_at', 'desc')
                    ->take(3)
                    ->get()
                    ->map(function($quiz) {
                        $quiz->is_locked = true;
                        return $quiz;
                    });
                $newQuizzes = $newQuizzes->concat($additionalPremium);
            }
        }
        
        // Take only the 3 newest quizzes
        $newQuizzes = $newQuizzes->sortByDesc('created_at')->take(3);
            
        // Get in-progress quizzes (started but not completed)
        $inProgressQuizzes = $user->quizAttempts()
            ->with('quiz')
            ->where('status', 'in_progress')
            ->orderBy('updated_at', 'desc')
            ->get()
            ->filter(function($attempt) use ($currentSubscriptions) {
                return $attempt->quiz && 
                       $attempt->quiz->is_active && 
                       (!$attempt->quiz->subscription_plan_id || 
                        $currentSubscriptions->contains('subscription_plan_id', $attempt->quiz->subscription_plan_id));
            });
            
        // Get recently completed quizzes
        $completedQuizzes = $user->quizAttempts()
            ->with('quiz')
            ->where('status', 'completed')
            ->orderBy('completed_at', 'desc')
            ->take(5)
            ->get()
            ->filter(function($attempt) use ($currentSubscriptions) {
                return $attempt->quiz && 
                       $attempt->quiz->is_active && 
                       (!$attempt->quiz->subscription_plan_id || 
                        $currentSubscriptions->contains('subscription_plan_id', $attempt->quiz->subscription_plan_id));
            });
            
        // Prepare stats for the dashboard
        $stats = [
            'total_quizzes' => $availableQuizzes,
            'completed_count' => $completedAttempts->count(),
            'in_progress_count' => $inProgressCount,
            'average_score' => $averageScore,
        ];
        
        // Calculate test readiness
        try {
            // Get all quiz attempts with user answers
            $quizAttempts = $user->quizAttempts()->with('userAnswers')->get() ?? collect();
            
            // Filter attempts with answers (both completed and in-progress)
            $attemptsWithAnswers = $quizAttempts->filter(function($attempt) {
                if (!$attempt) return false;
                $userAnswers = $attempt->userAnswers ?? null;
                return $userAnswers && is_countable($userAnswers) && count($userAnswers) > 0;
            });
            
            $totalAttemptsWithAnswers = $attemptsWithAnswers->count();
            
            // Calculate scores from both completed and in-progress quizzes
            $totalScore = 0;
            $scoreCount = 0;
            
            foreach ($attemptsWithAnswers as $attempt) {
                $userAnswers = $attempt->userAnswers ?? [];
                if (!is_countable($userAnswers) || count($userAnswers) === 0) continue;
                
                // Count correct answers
                $correctAnswers = 0;
                foreach ($userAnswers as $answer) {
                    // Check if is_correct exists and equals 1 (or true)
                    if ($answer && 
                        ((isset($answer->is_correct) && $answer->is_correct == 1) ||
                         (isset($answer->is_correct) && $answer->is_correct === true))) {
                        $correctAnswers++;
                    }
                }
                
                // Calculate percentage score for this attempt
                $attemptScore = ($correctAnswers / count($userAnswers)) * 100;
                $totalScore += $attemptScore;
                $scoreCount++;
            }
            
            $readinessAverageScore = $scoreCount > 0 ? round($totalScore / $scoreCount, 1) : 0;
            
            // Calculate readiness percentage using the original formula
            $readinessPercentage = 0;
            
            if ($totalAttemptsWithAnswers >= 25) {
                // User has completed 25+ quizzes - only score matters
                $readinessPercentage = min(100, round(($readinessAverageScore / 60) * 100));
            } elseif ($totalAttemptsWithAnswers > 0) {
                // User has fewer than 25 quizzes - combine score + progress
                $scoreFactor = min(100, ($readinessAverageScore / 60) * 100); // Score factor (max 100%)
                $progressFactor = $totalAttemptsWithAnswers / 25; // Progress factor (0 to 1)
                $readinessPercentage = round($scoreFactor * $progressFactor);
            }
            
            $readinessData = [
                'percentage' => $readinessPercentage,
                'average_score' => $readinessAverageScore,
                'total_tests' => $totalAttemptsWithAnswers,
                'is_ready' => $readinessPercentage >= 100,
                'getting_ready' => $readinessPercentage >= 60 && $totalAttemptsWithAnswers >= 25,
            ];
            
        } catch (\Exception $e) {
            // Fallback data
            $readinessData = [
                'percentage' => 0,
                'average_score' => 0,
                'total_tests' => 0,
                'is_ready' => false,
                'getting_ready' => false,
            ];
        }
        
        return view('dashboard.index', [
            'user' => $user,
            'currentSubscriptions' => $currentSubscriptions,
            'availablePlans' => $availablePlans,
            'availableQuizzes' => $availableQuizzes,
            'quizAttempts' => $quizAttempts,
            'newQuizzes' => $newQuizzes,
            'inProgressQuizzes' => $inProgressQuizzes,
            'completedQuizzes' => $completedQuizzes,
            'stats' => $stats,
            'readinessData' => $readinessData,
            'activeRoute' => 'dashboard',
        ]);
    }
    
    /**
     * Display the user's quizzes page
     *
     * @return \Illuminate\View\View
     */
    public function myQuizzes()
    {
        $user = Auth::user();
        
        // Get user's active subscriptions
        $currentSubscriptions = $user->subscriptions()
            ->where('ends_at', '>=', now())
            ->where('status', 'ACTIVE')
            ->get();
            
        // Get available quizzes with pagination
        $quizzes = $this->getAvailableQuizzes($currentSubscriptions)
            ->with(['attempts' => function($query) use ($user) {
                $query->where('user_id', $user->id)
                    ->orderBy('created_at', 'desc');
            }])
            ->orderBy('created_at', 'desc')
            ->paginate(10);  // 10 items per page
            
        return view('dashboard.quizzes.index', [
            'quizzes' => $quizzes,
            'subscriptions' => $currentSubscriptions
        ]);
    }
    
    /**
     * Display a specific quiz for the authenticated user
     *
     * @param int $quizId
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse
     */
    public function showQuiz($locale, $quizId)
    {
        $user = Auth::user();
        
        // Fetch the quiz with relationships
        $quiz = Quiz::with(['questions.options'])
            ->where('is_active', true)
            ->findOrFail($quizId);
            
        // Check if user has access to this quiz
        if (!$this->userHasAccessToQuiz($user, $quiz)) {
            return redirect()->route('dashboard.my-quizzes')
                ->with('error', 'You do not have access to this quiz.');
        }
        
        // Get user's previous attempts
        $previousAttempts = QuizAttempt::where('user_id', $user->id)
            ->where('quiz_id', $quiz->id)
            ->orderBy('created_at', 'desc')
            ->get();
            
        // Get the latest in-progress attempt or create a new one
        $attempt = $previousAttempts->first(function($attempt) {
            return $attempt->completed_at === null;
        });
        
        if (!$attempt) {
            $attempt = QuizAttempt::create([
                'user_id' => $user->id,
                'quiz_id' => $quiz->id,
                'started_at' => now(),
                'answers' => [],
                'score' => 0,
                'total_questions' => $quiz->questions->count(),
            ]);
        }
        
        return view('dashboard.quizzes.show', [
            'quiz' => $quiz,
            'attempt' => $attempt,
            'previousAttempts' => $previousAttempts
        ]);
    }
    
    /**
     * Check if user has access to a quiz
     *
     * @param \App\Models\User $user
     * @param \App\Models\Quiz $quiz
     * @return bool
     */
    protected function userHasAccessToQuiz($user, $quiz)
    {
        // If it's a guest quiz, anyone can access it
        if ($quiz->is_guest_quiz) {
            return true;
        }
        
        // Check if user has an active subscription that includes this quiz
        $activeSubscription = $user->subscriptions()
            ->where('ends_at', '>=', now())
            ->where('status', 'ACTIVE')
            ->whereHas('plan', function($query) use ($quiz) {
                $query->where('slug', $quiz->subscription_plan_slug);
            })
            ->exists();
            
        return $activeSubscription;
    }
    
    /**
     * Get available quizzes for the user
     *
     * @param \Illuminate\Database\Eloquent\Collection $currentSubscriptions
     * @return \Illuminate\Database\Eloquent\Builder
     */
    protected function getAvailableQuizzes($currentSubscriptions)
    {
        return Quiz::where('is_active', true)
            ->where(function($query) use ($currentSubscriptions) {
                // Include guest quizzes
                $query->where('is_guest_quiz', true);
                
                // Include quizzes from active subscriptions
                if ($currentSubscriptions->isNotEmpty()) {
                    $planSlugs = $currentSubscriptions->pluck('subscription_plan_slug')->filter()->toArray();
                    if (!empty($planSlugs)) {
                        $query->orWhereIn('subscription_plan_slug', $planSlugs);
                    }
                }
            });
    }
    
    /**
     * Clear the welcome modal session flag
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function clearWelcomeModal()
    {
        session()->forget('show_welcome_modal');
        return response()->json(['success' => true]);
    }
    
    /**
     * Display the user's subscription history
     *
     * @return \Illuminate\View\View
     */
    public function subscriptionHistory()
    {
        $user = Auth::user();
        
        // Get user's subscription history with pagination
        $subscriptions = $user->subscriptions()
            ->with('plan')
            ->orderBy('created_at', 'desc')
            ->paginate(10);
            
        return view('dashboard.subscription-history', [
            'subscriptions' => $subscriptions
        ]);
    }
}
