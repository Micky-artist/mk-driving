<?php

namespace App\Http\Controllers\Web\Dashboard;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Quiz;
use App\Models\Bookmark;
use App\Models\QuizAttempt;
use App\Models\SubscriptionPlan;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\JsonResponse;

class QuizController extends Controller
{
    /**
     * Display the user's quizzes.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $status = request()->get('see', null);
        // Convert URL parameter from hyphen to underscore
        if ($status === 'in-progress') {
            $status = 'in_progress';
        }
        return $this->getQuizzesByStatus($status);
    }
    
    public function inProgress()
    {
        return $this->getQuizzesByStatus('in_progress');
    }
    
    /**
     * Display user's quiz progress and performance analytics
     */
    public function progress()
    {
        $user = Auth::user();
        
        // Get user's quiz statistics
        $totalAttempts = $user->quizAttempts()->count();
        $completedAttempts = $user->quizAttempts()->whereNotNull('completed_at')->count();
        $averageScore = $user->quizAttempts()
            ->whereNotNull('completed_at')
            ->whereNotNull('score')
            ->avg('score') ?? 0;
        
        // Get quiz attempts over time (last 30 days)
        $attemptsOverTime = $user->quizAttempts()
            ->where('created_at', '>=', now()->subDays(30))
            ->orderBy('created_at', 'asc')
            ->get()
            ->groupBy(function($attempt) {
                return $attempt->created_at->format('Y-m-d');
            });
        
        // Get performance by quiz category/topic
        $performanceByCategory = $user->quizAttempts()
            ->with('quiz')
            ->whereNotNull('completed_at')
            ->whereNotNull('score')
            ->get()
            ->groupBy(function($attempt) {
                return $attempt->quiz->title ?? 'Unknown';
            })
            ->map(function($attempts) {
                return [
                    'attempts' => $attempts->count(),
                    'average_score' => $attempts->avg('score'),
                    'best_score' => $attempts->max('score'),
                    'latest_score' => $attempts->last()->score
                ];
            });
        
        // Get leaderboard position (top 10 users by average score)
        $leaderboard = User::withCount(['quizAttempts' => function($query) {
                $query->whereNotNull('completed_at');
            }])
            ->whereHas('quizAttempts', function($query) {
                $query->whereNotNull('completed_at');
            })
            ->get()
            ->map(function($user) {
                $avgScore = $user->quizAttempts()
                    ->whereNotNull('completed_at')
                    ->whereNotNull('score')
                    ->avg('score') ?? 0;
                
                return [
                    'user' => $user,
                    'average_score' => $avgScore,
                    'completed_quizzes' => $user->quizAttempts()->whereNotNull('completed_at')->count()
                ];
            })
            ->sortByDesc('average_score')
            ->take(10)
            ->values();
        
        // Find current user's position
        $userPosition = $leaderboard->search(function($entry) use ($user) {
            return $entry['user']->id === $user->id;
        }) + 1;
        
        // Get recent activity
        $recentAttempts = $user->quizAttempts()
            ->with('quiz')
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get();
        
        // Calculate streak information
        $currentStreak = $user->getCurrentStreak();
        $bestStreak = $user->streak_days ?? 0;
        
        // Get improvement metrics
        $lastWeekScore = $user->quizAttempts()
            ->whereNotNull('completed_at')
            ->where('completed_at', '>=', now()->subWeek())
            ->whereNotNull('score')
            ->avg('score') ?? 0;
            
        $previousWeekScore = $user->quizAttempts()
            ->whereNotNull('completed_at')
            ->whereBetween('completed_at', [now()->subWeeks(2), now()->subWeek()])
            ->whereNotNull('score')
            ->avg('score') ?? 0;
        
        $scoreImprovement = $previousWeekScore > 0 ? (($lastWeekScore - $previousWeekScore) / $previousWeekScore) * 100 : 0;
        
        return view('dashboard.quizzes.progress', [
            'user' => $user,
            'stats' => [
                'total_attempts' => $totalAttempts,
                'completed_attempts' => $completedAttempts,
                'average_score' => round($averageScore, 1),
                'completion_rate' => $totalAttempts > 0 ? round(($completedAttempts / $totalAttempts) * 100, 1) : 0,
                'current_streak' => $currentStreak,
                'best_streak' => $bestStreak,
            ],
            'attemptsOverTime' => $attemptsOverTime,
            'performanceByCategory' => $performanceByCategory,
            'leaderboard' => $leaderboard,
            'userPosition' => $userPosition,
            'recentAttempts' => $recentAttempts,
            'improvement' => [
                'current_week' => round($lastWeekScore, 1),
                'previous_week' => round($previousWeekScore, 1),
                'improvement_percentage' => round($scoreImprovement, 1)
            ]
        ]);
    }
    
    /**
     * Display completed quizzes with attempt history
     */
    public function completed()
    {
        $user = Auth::user();
        
        // Get completed quizzes with their attempts
        $quizzes = Quiz::whereHas('attempts', function($q) use ($user) {
                $q->where('user_id', $user->id)
                  ->whereNotNull('completed_at');
            })
            ->with(['attempts' => function($query) use ($user) {
                $query->where('user_id', $user->id)
                      ->whereNotNull('completed_at')
                      ->orderBy('completed_at', 'desc');
            }])
            ->withCount('questions')
            ->orderBy('created_at', 'desc')
            ->paginate(6);
            
        // Calculate stats for each quiz
        $quizzes->each(function($quiz) use ($user) {
            $quiz->attempts_count = $quiz->attempts->count();
            $quiz->best_score = $quiz->attempts->max('score');
            $quiz->last_attempt = $quiz->attempts->first();
            $quiz->average_score = round($quiz->attempts->avg('score'), 1);
            
            // Ensure each attempt has the correct score percentage
            $quiz->attempts->each(function($attempt) use ($quiz) {
                $attempt->score_percentage = $quiz->questions_count > 0 
                    ? round(($attempt->score / $quiz->questions_count) * 100)
                    : 0;
            });
        });

        return view('dashboard.quizzes.completed', [
            'quizzes' => $quizzes,
            'user' => $user
        ]);
    }
    
    /**
     * Get detailed attempt information
     *
     * @param string $locale
     * @param int $attemptId
     * @return \Illuminate\Http\JsonResponse
     */
    public function attemptDetails($locale, $attemptId)
    {
        try {
            $attempt = QuizAttempt::with([
                'quiz',
                'userAnswers' => function($query) {
                    $query->with(['question' => function($q) {
                        $q->with(['options' => function($q) {
                            $q->select('id', 'question_id', 'text', 'is_correct');
                        }]);
                    }, 'option']);
                }
            ])
            ->where('user_id', Auth::id())
            ->where('id', $attemptId)
            ->whereNotNull('completed_at')
            ->firstOrFail();
            
            $totalQuestions = $attempt->quiz->questions_count ?? $attempt->userAnswers->count();
            $correctCount = $attempt->userAnswers->where('is_correct', true)->count();
            $percentage = $totalQuestions > 0 ? round(($correctCount / $totalQuestions) * 100) : 0;
            
            // Format the response
            $response = [
                'success' => true,
                'attempt' => [
                    'id' => $attempt->id,
                    'score' => $attempt->score,
                    'status' => $attempt->status,
                    'started_at' => $attempt->started_at,
                    'completed_at' => $attempt->completed_at,
                    'time_spent_seconds' => $attempt->time_spent_seconds,
                    'quiz' => [
                        'id' => $attempt->quiz->id,
                        'title' => $attempt->quiz->title,
                        'questions_count' => $attempt->quiz->questions_count,
                    ],
                    'user_answers' => $attempt->userAnswers->map(function($answer) {
                        return [
                            'id' => $answer->id,
                            'is_correct' => (bool)$answer->is_correct,
                            'question' => [
                                'id' => $answer->question->id ?? null,
                                'text' => $answer->question->text ?? 'Question not found',
                                'options' => $answer->question->options ?? []
                            ],
                            'selected_option' => $answer->option ? [
                                'id' => $answer->option->id,
                                'text' => $answer->option->text,
                                'is_correct' => (bool)$answer->option->is_correct
                            ] : null
                        ];
                    })
                ],
                'correct_count' => $correctCount,
                'total_questions' => $totalQuestions,
                'percentage' => $percentage,
                'formatted_date' => $attempt->completed_at ? $attempt->completed_at->format('M d, Y \a\t h:i A') : 'N/A',
                'time_spent' => $this->formatDuration($attempt->time_spent_seconds ?? 0)
            ];
            
            return response()->json($response);
            
        } catch (\Exception $e) {
            Log::error('Error fetching attempt details: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to load attempt details. Please try again.'
            ], 500);
        }
    }
    
    /**
     * Format duration in seconds to human readable format
     */
    private function formatDuration($seconds)
    {
        $minutes = floor($seconds / 60);
        $seconds = $seconds % 60;
        return sprintf('%02d:%02d', $minutes, $seconds);
    }
    
    /**
     * Check if user can access a quiz based on their subscription
     */
    protected function canAccessQuiz($user, $quiz)
    {
        // Admins can access all quizzes
        if ($user->isAdmin()) {
            return true;
        }

        // Guest users can only access guest quizzes
        if (!$user) {
            return $quiz->is_guest_quiz;
        }

        // If quiz is for guests, anyone can access it
        if ($quiz->is_guest_quiz) {
            return true;
        }

        // If quiz doesn't require a specific plan, it's accessible to all authenticated users
        if (!$quiz->subscription_plan_slug) {
            return true;
        }

        // Check if user has an active subscription to the required plan
        $activeSubscription = $user->activeSubscriptions()
            ->whereHas('plan', function($q) use ($quiz) {
                $q->where('slug', $quiz->subscription_plan_slug);
            })
            ->exists();

        return $activeSubscription;
    }
    
    /**
     * Check if user can retake the quiz
     */
    protected function canRetakeQuiz($user, $quiz)
    {
        // Admins can always retake
        if ($user->isAdmin()) {
            return true;
        }

        // Check if user has an active subscription
        $hasActiveSubscription = $user->activeSubscriptions()->exists();
        if ($hasActiveSubscription) {
            return true;
        }

        // For free users, check last attempt time
        $lastAttempt = $user->quizAttempts()
            ->where('quiz_id', $quiz->id)
            ->whereNotNull('completed_at')
            ->latest('completed_at')
            ->first();

        // If no previous attempts, allow taking the quiz
        if (!$lastAttempt) {
            return true;
        }

        // Check if 24 hours have passed since last attempt
        return $lastAttempt->completed_at->addHours(24)->isPast();
    }

    /**
     * Get quizzes filtered by status and user's subscription
     */
    protected function getQuizzesByStatus($status = null)
    {
        $user = Auth::user();
        
        $query = Quiz::where('is_active', true)
            ->with(['attempts' => function($query) use ($user, $status) {
                $query->where('user_id', $user->id)
                      ->orderBy('created_at', 'desc');
                if ($status === 'in_progress') {
                    $query->where('status', 'IN_PROGRESS');
                } elseif ($status === 'completed') {
                    $query->where('status', 'COMPLETED');
                }
            }, 'subscriptionPlan'])
            ->withCount('questions');
            
        // Apply status filter if provided
        if ($status === 'in_progress') {
            $query->whereHas('attempts', function($q) use ($user) {
                $q->where('user_id', $user->id)
                  ->where('status', 'IN_PROGRESS');
            });
        } elseif ($status === 'completed') {
            $query->whereHas('attempts', function($q) use ($user) {
                $q->where('user_id', $user->id)
                  ->where('status', 'COMPLETED');
            });
        }

        // Get all quizzes first
        $allQuizzes = $query->orderBy('created_at', 'desc')->get();
        
        // Ensure attempts is always a collection, even if empty
        $allQuizzes->each(function ($quiz) {
            if (!isset($quiz->attempts)) {
                $quiz->setRelation('attempts', collect());
            }
        });
        
        // Filter quizzes based on user's subscription
        $filteredQuizzes = $allQuizzes->filter(function($quiz) use ($user) {
            return $this->canAccessQuiz($user, $quiz);
        });

        // Paginate the filtered results
        $perPage = 9;
        $page = request()->get('page', 1);
        $quizzes = new \Illuminate\Pagination\LengthAwarePaginator(
            $filteredQuizzes->forPage($page, $perPage),
            $filteredQuizzes->count(),
            $perPage,
            $page,
            ['path' => request()->url(), 'query' => request()->query()]
        );

        // Get user's quiz statistics (same logic as main dashboard)
        $userAttempts = $user->quizAttempts;
        $completedQuizzes = $userAttempts->where('status', 'COMPLETED');
        $inProgressQuizzes = $userAttempts->where('status', 'IN_PROGRESS');
        
        $stats = [
            'completed_count' => $completedQuizzes->count(),
            'in_progress_count' => $inProgressQuizzes->count(),
            'average_score' => round($completedQuizzes->avg('score') ?? 0, 1),
        ];

        return view('dashboard.quizzes.index', [
            'quizzes' => $quizzes,
            'user' => $user,
            'currentStatus' => $status,
            'stats' => $stats
        ]);
    }
    
    /**
     * Display the specified quiz with user's attempts.
     *
     * @param  string  $locale The locale (e.g., 'en', 'rw')
     * @param  int  $id The quiz ID
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse
     */
    public function show($locale, $id)
    {
        // Ensure the ID is numeric
        if (!is_numeric($id)) {
            abort(404);
        }

        // Fetch the quiz with relationships
        $quiz = Quiz::where('is_active', true)
            ->with(['questions' => function($query) {
                $query->with(['options']);
            }])
            ->findOrFail($id);
            
        // Check if user can access this quiz
        $user = Auth::user();
        if (!$this->canAccessQuiz($user, $quiz)) {
            if (!$user) {
                return redirect()->route('login');
            }
            return redirect()->route('dashboard.quizzes.index')
                ->with('error', 'You do not have access to this quiz. Please upgrade your subscription.');
        }
        
        // Direct database query for the first question's options
        if ($quiz->questions->isNotEmpty()) {
            $firstQuestion = $quiz->questions->first();
            $directOptions = DB::table('options')
                ->where('question_id', $firstQuestion->id)
                ->get();
                
            Log::debug('Direct DB Query - First Question Options:', [
                'question_id' => $firstQuestion->id,
                'options_from_db' => $directOptions->map(function($opt) {
                    return (array)$opt;
                })
            ]);
            
            // Check for translations
            $translatedOptions = $firstQuestion->options->map(function($option) use ($locale) {
                return [
                    'id' => $option->id,
                    'text' => $option->text,
                    'is_correct' => $option->is_correct,
                    'has_translations' => method_exists($option, 'translations') ? $option->translations->isNotEmpty() : false,
                    'translations' => method_exists($option, 'translations') ? $option->translations : []
                ];
            });
            
            Log::debug('First Question Options with Translations:', [
                'question_id' => $firstQuestion->id,
                'options' => $translatedOptions
            ]);
        }
            
        // Authorize the action - handle all quizzes within dashboard for authenticated users
        $this->authorize('view', $quiz);
        
        // For authenticated users, we'll handle all quizzes within the dashboard interface
        // including guest quizzes, to maintain consistent user experience
        
        $user = Auth::user();
        
        // Get user's attempts for this quiz
        $attempts = $quiz->attempts()
            ->where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->get();

        // Get or create an active attempt
        $activeAttempt = $attempts->first(function($attempt) {
            return $attempt->completed_at === null;
        });

        if (!$activeAttempt) {
            // Check if user can retake the quiz before creating a new attempt
            $canRetake = $this->canRetakeQuiz($user, $quiz);
            $hasActiveSubscription = $user->activeSubscriptions()->exists();
            
            if (!$canRetake && !$hasActiveSubscription) {
                $lastAttempt = $user->quizAttempts()
                    ->where('quiz_id', $quiz->id)
                    ->whereNotNull('completed_at')
                    ->latest('completed_at')
                    ->first();
                    
                if ($lastAttempt) {
                    $nextRetakeTime = $lastAttempt->completed_at->addHours(24);
                    return redirect()->route('dashboard.quizzes.index')
                        ->with('error', __('quiz.quizLimitReached') . ' ' . __('quiz.quizLimitMessage', [
                            'time' => $nextRetakeTime->diffForHumans()
                        ]));
                }
            }
            
            $activeAttempt = $user->quizAttempts()->create([
                'quiz_id' => $quiz->id,
                'started_at' => now(),
                'answers' => [],
                'score' => 0,
                'total_questions' => $quiz->questions->count(),
            ]);
        }

        // Check if the quiz is bookmarked by the user
        $isBookmarked = $user->bookmarks()->where('quiz_id', $quiz->id)->exists();
        
        // Get user's total attempts and best score for this quiz
        $userAttempts = $attempts->count();
        $bestScore = $attempts->max('score') ?? 0;
        
        // Get user's current streak
        $userStreak = $user->getCurrentStreak();
        
        // Check if user can retake the quiz
        $canRetake = $this->canRetakeQuiz($user, $quiz);
        $nextRetakeTime = null;
        $hasActiveSubscription = $user->activeSubscriptions()->exists();
        
        if (!$canRetake && !$hasActiveSubscription) {
            $lastAttempt = $user->quizAttempts()
                ->where('quiz_id', $quiz->id)
                ->whereNotNull('completed_at')
                ->latest('completed_at')
                ->first();
                
            if ($lastAttempt) {
                $nextRetakeTime = $lastAttempt->completed_at->addHours(24);
            }
        }

        return view('dashboard.quizzes.show', [
            'quiz' => $quiz,
            'attempt' => $activeAttempt,
            'previousAttempts' => $attempts->where('id', '!=', $activeAttempt->id),
            'user' => $user,
            'isBookmarked' => $isBookmarked,
            'userAttempts' => $userAttempts,
            'bestScore' => $bestScore,
            'userStreak' => $userStreak,
            'canRetake' => $canRetake,
            'nextRetakeTime' => $nextRetakeTime,
            'hasActiveSubscription' => $hasActiveSubscription
        ]);
    }
    
    /**
     * Handle quiz submission
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string  $locale

        // Log the submission
        Log::info('Quiz submission received', [
            'quiz_id' => $id,
            'user_id' => Auth::id(),
            'answers_count' => count($answers),
            'time_taken' => $request->input('time_taken'),
            'end_time' => $request->input('end_time')
        ]);

    // Ensure the ID is numeric
    if (!is_numeric($id)) {
        abort(404);
    }
    
    // Load quiz with questions and their options
    $quiz = Quiz::with(['questions' => function($query) {
        $query->with(['options']);
    }])->where('is_active', true)->findOrFail($id);
        
    // Authorize the action
    $this->authorize('view', $quiz);
    
    $user = Auth::user();
    
    // Get or create the active attempt
    $attempt = $user->quizAttempts()
        ->where('quiz_id', $quiz->id)
        ->whereNull('completed_at')
        ->firstOrFail();

    $totalQuestions = $quiz->questions->count();
    $correctAnswers = 0;
    $userAnswers = [];
    
    // Start a database transaction
    \DB::beginTransaction();
    
    try {
        // Delete any existing answers for this attempt
        $attempt->userAnswers()->delete();
        
        foreach ($quiz->questions as $question) {
            $questionId = $question->id;
            $answerId = $answers[$questionId] ?? null;
            $questionTimeSpent = (int)($timeSpent[$questionId] ?? 0);
            
            // Find the selected option
            $selectedOption = $answerId ? $question->options->firstWhere('id', $answerId) : null;
            $isCorrect = $selectedOption ? (bool)$selectedOption->is_correct : false;
            
            // Log answer validation
            Log::debug('Answer validation', [
                'question_id' => $questionId,
                'answer_id' => $answerId,
                'is_correct' => $isCorrect,
                'time_spent' => $questionTimeSpent
            ]);

            // Store the user's answer
            $userAnswer = $attempt->userAnswers()->create([
                'question_id' => $questionId,
                'answer_id' => $answerId,
                'answer_text' => $selectedOption ? $selectedOption->text : null,
                'is_correct' => $isCorrect,
                'time_spent' => $questionTimeSpent,
                'score' => $isCorrect ? 1 : 0,
                'details' => json_encode([
                    'question_text' => $question->text,
                    'selected_option' => $selectedOption ? $selectedOption->text : null,
                    'correct' => $isCorrect,
                    'timestamp' => now()->toDateTimeString()
                ])
            ]);

            if ($isCorrect) {
                $correctAnswers++;
            }
            
            $userAnswers[$questionId] = [
                'answer_id' => $answerId,
                'is_correct' => $isCorrect,
                'time_spent' => $questionTimeSpent
            ];
        }
        
        // Calculate score and determine pass/fail
        $score = $totalQuestions > 0 ? round(($correctAnswers / $totalQuestions) * 100) : 0;
        $passed = $score >= ($quiz->passing_score ?? 70); // Default to 70% if not set
        
        // Update the attempt
        $attempt->update([
            'answers' => $userAnswers,
            'score' => $score,
            'passed' => $passed,
            'completed_at' => now(),
            'time_taken' => (int)$request->input('time_taken'),
            'end_time' => $request->input('end_time'),
            'paused_time' => (int)($request->input('paused_time', 0)),
            'time_up' => (bool)$request->input('time_up', false)
        ]);
        
        // Commit the transaction
        DB::commit();
        
        // Log successful submission
        Log::info('Quiz submission completed', [
            'quiz_id' => $quiz->id,
            'attempt_id' => $attempt->id,
            'score' => $score,
            'passed' => $passed,
            'total_questions' => $totalQuestions,
            'correct_answers' => $correctAnswers,
            'time_taken' => $request->input('time_taken'),
            'answers_count' => count($userAnswers),
            'response_data' => $responseData
        ]);

        // Return JSON response for AJAX requests
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json($responseData);
        }
        
        // For non-AJAX requests, redirect with a success message
        return redirect()->route('dashboard.quizzes.show', [
            'locale' => $locale,
            'quiz' => $quiz->id
        ])->with('success', $responseData['message']);
    }
    
    /**
     * Toggle bookmark for a quiz
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function bookmark($locale, $id)
    {
        $user = Auth::user();
        $quiz = Quiz::findOrFail($id);
        
        // Check if the quiz is already bookmarked
        $bookmark = $user->bookmarks()->where('quiz_id', $quiz->id)->first();
        
        if ($bookmark) {
            // If bookmarked, remove the bookmark
            $bookmark->delete();
            $isBookmarked = false;
        } else {
            // If not bookmarked, add a new bookmark
            $user->bookmarks()->create([
                'quiz_id' => $quiz->id
            ]);
            $isBookmarked = true;
        }
        
        return response()->json([
            'success' => true,
            'isBookmarked' => $isBookmarked
        ]);
    }
}
