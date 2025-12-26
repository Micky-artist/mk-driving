<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\JsonResponse;
use App\Models\User;
use App\Models\Subscription;
use App\Models\QuizAttempt;
use App\Models\Question;
use App\Models\Quiz;
use App\Models\Visitor;
use App\Models\News;
use App\Models\ForumQuestion;
use App\Models\ForumAnswer;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ReportsExport;

class ReportController extends Controller
{
    /**
     * Display the main reports dashboard.
     *
     * @return \Illuminate\View\View
     */
    public function index(): View
    {
        $period = request()->get('period', 30); // Default to 30 days
        
        // Key metrics for overview
        $metrics = [
            'total_revenue' => Subscription::where('status', 'ACTIVE')
                ->where('created_at', '>=', date('Y-m-d H:i:s', subDays($period)))
                ->sum('amount'),
            'revenue_growth' => $this->calculateGrowth('revenue', $period),
            'active_users' => User::whereHas('quizAttempts', function ($query) use ($period) {
                $query->where('created_at', '>=', date('Y-m-d H:i:s', subDays($period)));
            })->count(),
            'user_growth' => $this->calculateGrowth('users', $period),
            'completion_rate' => QuizAttempt::where('status', 'passed')->count() / 
                max(QuizAttempt::count(), 1) * 100,
            'total_tests_completed' => QuizAttempt::where('status', 'passed')->count(),
            'engagement_score' => $this->calculateEngagementScore($period),
            'engagement_growth' => $this->calculateGrowth('engagement', $period),
            'avg_session_duration' => $this->calculateAvgSessionDuration($period),
            'bounce_rate' => $this->calculateBounceRate($period),
            'page_views' => $this->calculatePageViews($period),
            'conversion_rate' => $this->calculateConversionRate($period),
        ];

        // Revenue trends
        $revenueTrends = Subscription::selectRaw('DATE(created_at) as date, SUM(amount) as revenue')
            ->where('created_at', '>=', date('Y-m-d H:i:s', subDays($period)))
            ->where('status', '!=', 'CANCELLED')
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // User activity trends
        $userActivityTrends = User::whereHas('quizAttempts', function ($query) use ($period) {
                $query->where('created_at', '>=', date('Y-m-d H:i:s', subDays($period)));
            })
            ->selectRaw('DATE(quiz_attempts.created_at) as date, COUNT(DISTINCT users.id) as active_users')
            ->join('quiz_attempts', 'users.id', '=', 'quiz_attempts.user_id')
            ->where('quiz_attempts.created_at', '>=', date('Y-m-d H:i:s', subDays($period)))
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // Top performing quizzes
        $topQuizzes = Quiz::withCount(['attempts' => function ($query) use ($period) {
                $query->where('created_at', '>=', date('Y-m-d H:i:s', subDays($period)));
            }])
            ->with(['attempts' => function ($query) use ($period) {
                $query->where('created_at', '>=', date('Y-m-d H:i:s', subDays($period)));
            }])
            ->having('attempts_count', '>', 0)
            ->get()
            ->map(function ($quiz) {
                $totalAttempts = $quiz->attempts->count();
                $passedAttempts = $quiz->attempts->where('status', 'passed')->count();
                
                return [
                    'title' => $quiz->title,
                    'attempts' => $totalAttempts,
                    'pass_rate' => $totalAttempts > 0 ? round(($passedAttempts / $totalAttempts) * 100, 1) : 0,
                ];
            })
            ->sortByDesc('pass_rate')
            ->take(5);

        // Recent activity
        $recentActivity = $this->getRecentActivity();

        // Revenue metrics
        $revenueMetrics = [
            'mrr' => Subscription::where('status', 'ACTIVE')
                ->where('ends_at', '>', date('Y-m-d H:i:s', currentTimestamp()))
                ->sum('amount'),
            'annual_revenue' => Subscription::where('status', 'ACTIVE')
                ->where('ends_at', '>', date('Y-m-d H:i:s', currentTimestamp()))
                ->sum('amount') * 12,
            'arpu' => User::count() > 0 ? 
                Subscription::where('status', 'ACTIVE')->sum('amount') / User::count() : 0,
            'clv' => $this->calculateCustomerLifetimeValue(),
        ];

        // Revenue by plan
        $revenueByPlan = Subscription::with('plan')
            ->selectRaw('subscription_plan_id, COUNT(*) as count, SUM(amount) as total_revenue')
            ->where('status', 'ACTIVE')
            ->groupBy('subscription_plan_id')
            ->get()
            ->map(function ($item) {
                return [
                    'plan_name' => $item->plan->name ?? 'Unknown',
                    'revenue' => $item->total_revenue,
                ];
            });

        // User metrics
        $userMetrics = [
            'total_users' => User::count(),
            'new_users_30d' => User::where('created_at', '>=', date('Y-m-d H:i:s', subDays(30)))->count(),
            'active_users_30d' => User::whereHas('quizAttempts', function ($query) {
                $query->where('created_at', '>=', date('Y-m-d H:i:s', subDays(30)));
            })->count(),
            'retention_rate' => $this->calculateRetentionRate(),
        ];

        // Test metrics
        $testMetrics = [
            'total_attempts' => QuizAttempt::count(),
            'pass_rate' => QuizAttempt::where('status', 'passed')->count() / 
                max(QuizAttempt::count(), 1) * 100,
            'avg_score' => QuizAttempt::avg('score') ?? 0,
            'avg_completion_time' => $this->calculateAvgCompletionTime(),
        ];

        // Subscription metrics
        $subscriptionMetrics = [
            'active_subscriptions' => Subscription::where('status', 'ACTIVE')->count(),
            'churn_rate' => $this->calculateChurnRate(),
            'new_subscriptions_30d' => Subscription::where('created_at', '>=', date('Y-m-d H:i:s', subDays(30)))->count(),
            'mrr_growth' => $this->calculateMRRGrowth(),
        ];

        // Blog metrics (if news model exists)
        $blogMetrics = [
            'total_views' => News::sum('views'),
            'published_posts' => News::where('is_published', true)->count(),
            'engagement_rate' => $this->calculateBlogEngagement(),
            'avg_read_time' => $this->calculateAvgReadTime(),
            'total_likes' => News::sum('likes_count'),
            'total_comments' => News::sum('comments_count'),
            'total_shares' => News::sum('shares_count'),
        ];

        $popularPosts = News::where('is_published', true)
            ->orderBy('views', 'desc')
            ->take(5)
            ->get()
            ->map(function ($post) {
                return [
                    'title' => $post->title,
                    'views' => $post->views,
                    'engagement' => $post->getEngagementRate(),
                    'likes' => $post->likes_count,
                    'comments' => $post->comments_count,
                    'shares' => $post->shares_count,
                ];
            });

        // Forum metrics
        $forumMetrics = [
            'total_questions' => ForumQuestion::count(),
            'total_answers' => ForumAnswer::count(),
            'active_contributors' => ForumAnswer::distinct('user_id')->count(),
            'answer_rate' => ForumQuestion::count() > 0 ? 
                (ForumAnswer::count() / ForumQuestion::count()) * 100 : 0,
            'news_discussions' => ForumQuestion::where('is_news_discussion', true)->count(),
            'regular_questions' => ForumQuestion::where('is_news_discussion', false)->count(),
        ];

        $topContributors = User::withCount(['forumAnswers'])
            ->orderBy('forum_answers_count', 'desc')
            ->take(5)
            ->get()
            ->map(function ($user) {
                return (object) [
                    'name' => $user->first_name . ' ' . $user->last_name,
                    'answers' => $user->forum_answers_count,
                    'points' => $user->points ?? 0,
                    'rank' => $user->getRankName(),
                    'avatar' => $user->profile_image ?? null,
                ];
            });

        // Leaderboard metrics
        $leaderboardMetrics = [
            'total_users_ranked' => User::where('points', '>', 0)->count(),
            'highest_points' => User::max('points') ?? 0,
            'avg_points_per_user' => User::avg('points') ?? 0,
            'active_streaks' => User::where('streak_days', '>', 0)->count(),
            'longest_streak' => User::max('streak_days') ?? 0,
        ];

        $topRankedUsers = User::orderBy('points', 'desc')
            ->take(10)
            ->get()
            ->map(function ($user) {
                return [
                    'name' => $user->first_name . ' ' . $user->last_name,
                    'points' => $user->points ?? 0,
                    'rank' => $user->getRankName(),
                    'streak' => $user->streak_days ?? 0,
                    'badges' => $user->achievement_badges ?? [],
                    'avatar' => $user->profile_image ?? null,
                ];
            });

        // Visitor analytics data
        $visitorStats = [
            'total_visitors' => Visitor::count(),
            'unique_visitors' => Visitor::distinct('visitor_id')->count(),
            'registered_visitors' => Visitor::where('is_registered_user', true)->count(),
            'anonymous_visitors' => Visitor::where('is_registered_user', false)->count(),
            'mobile_visitors' => Visitor::where('device_type', 'mobile')->count(),
            'desktop_visitors' => Visitor::where('device_type', 'desktop')->count(),
            'tablet_visitors' => Visitor::where('device_type', 'tablet')->count(),
            'mobile_percentage' => Visitor::count() > 0 ? 
                (Visitor::where('device_type', 'mobile')->count() / Visitor::count()) * 100 : 0,
        ];

        $recentVisitors = Visitor::with('user')
            ->orderBy('last_visit_at', 'desc')
            ->take(50)
            ->get();

        return view('admin.reports.index', compact(
            'metrics',
            'revenueTrends',
            'userActivityTrends',
            'topQuizzes',
            'recentActivity',
            'revenueMetrics',
            'revenueByPlan',
            'userMetrics',
            'testMetrics',
            'subscriptionMetrics',
            'blogMetrics',
            'popularPosts',
            'forumMetrics',
            'topContributors',
            'leaderboardMetrics',
            'topRankedUsers',
            'visitorStats',
            'recentVisitors'
        ));
    }

    /**
     * Export visitor data.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function exportVisitors(): JsonResponse
    {
        $visitors = Visitor::with('user')
            ->orderBy('last_visit_at', 'desc')
            ->get();

        $csvData = [];
        foreach ($visitors as $visitor) {
            $csvData[] = [
                'Visitor ID' => $visitor->visitor_id,
                'IP Address' => $visitor->ip_address,
                'Device Type' => $visitor->device_type,
                'Device Name' => $visitor->device_name,
                'Browser' => $visitor->browser,
                'Platform' => $visitor->platform,
                'Country' => $visitor->country,
                'City' => $visitor->city,
                'Is Registered User' => $visitor->is_registered_user ? 'Yes' : 'No',
                'User ID' => $visitor->user_id,
                'First Visit' => $visitor->first_visit_at,
                'Last Visit' => $visitor->last_visit_at,
                'Total Visits' => $visitor->total_visits,
            ];
        }

        return response()->json([
            'data' => $csvData,
            'filename' => 'visitors_export_' . date('Y-m-d_H-i-s') . '.csv'
        ]);
    }

    /**
     * Export reports to Excel/CSV.
     *
     * @param string $type
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function export(string $type)
    {
        $filename = 'driving-school-report-' . date('Y-m-d') . '.xlsx';
        
        return Excel::download(new ReportsExport($type), $filename);
    }

    // Helper methods for calculations
    private function calculateGrowth($type, $period): float
    {
        $current = currentTimestamp();
        $previous = subDays($period);
        
        switch ($type) {
            case 'revenue':
                $currentRevenue = Subscription::where('status', 'ACTIVE')
                    ->whereBetween('created_at', [$previous, $current])
                    ->sum('amount');
                $previousRevenue = Subscription::where('status', 'ACTIVE')
                    ->whereBetween('created_at', [$previous->copy()->subDays($period), $previous])
                    ->sum('amount');
                return $previousRevenue > 0 ? 
                    (($currentRevenue - $previousRevenue) / $previousRevenue) * 100 : 0;
                
            case 'users':
                $currentUsers = User::whereBetween('created_at', [$previous, $current])->count();
                $previousUsers = User::whereBetween('created_at', 
                    [$previous->copy()->subDays($period), $previous])->count();
                return $previousUsers > 0 ? 
                    (($currentUsers - $previousUsers) / $previousUsers) * 100 : 0;
                
            case 'engagement':
                // Simplified engagement calculation
                return rand(5, 25); // Placeholder - implement real calculation
                
            default:
                return 0;
        }
    }

    private function calculateEngagementScore($period): float
    {
        // Simplified engagement score calculation
        $activeUsers = User::whereHas('quizAttempts', function ($query) use ($period) {
            $query->where('created_at', '>=', date('Y-m-d H:i:s', subDays($period)));
        })->count();
        
        $totalUsers = User::count();
        
        return $totalUsers > 0 ? ($activeUsers / $totalUsers) * 100 : 0;
    }

    private function calculateAvgSessionDuration($period): int
    {
        // Placeholder - implement real session tracking
        return rand(3, 15);
    }

    private function calculateBounceRate($period): float
    {
        // Placeholder - implement real bounce rate calculation
        return rand(20, 60);
    }

    private function calculatePageViews($period): int
    {
        // Placeholder - implement real page view tracking
        return rand(1000, 10000);
    }

    private function calculateConversionRate($period): float
    {
        // Placeholder - implement real conversion tracking
        return rand(2, 8);
    }

    private function calculateCustomerLifetimeValue(): float
    {
        $avgSubscriptionValue = Subscription::where('status', 'ACTIVE')->avg('amount') ?? 0;
        $avgCustomerLifetime = 12; // months
        
        return $avgSubscriptionValue * $avgCustomerLifetime;
    }

    private function calculateRetentionRate(): float
    {
        $thirtyDaysAgo = subDays(30);
        $sixtyDaysAgo = subDays(60);
        
        $users30DaysAgo = User::where('created_at', '<=', $thirtyDaysAgo)->count();
        $activeUsers = User::where('created_at', '<=', $thirtyDaysAgo)
            ->whereHas('quizAttempts', function ($query) use ($thirtyDaysAgo) {
                $query->where('created_at', '>=', $thirtyDaysAgo);
            })->count();
            
        return $users30DaysAgo > 0 ? ($activeUsers / $users30DaysAgo) * 100 : 0;
    }

    private function calculateAvgCompletionTime(): int
    {
        return QuizAttempt::whereNotNull('completed_at')
            ->selectRaw('AVG(TIMESTAMPDIFF(MINUTE, created_at, completed_at)) as avg_time')
            ->value('avg_time') ?? 0;
    }

    private function calculateChurnRate(): float
    {
        $thirtyDaysAgo = subDays(30);
        $cancelledSubscriptions = Subscription::where('status', 'CANCELLED')
            ->where('updated_at', '>=', date('Y-m-d H:i:s', $thirtyDaysAgo))
            ->count();
        $totalSubscriptions = Subscription::where('created_at', '<=', date('Y-m-d H:i:s', $thirtyDaysAgo))->count();
            
        return $totalSubscriptions > 0 ? ($cancelledSubscriptions / $totalSubscriptions) * 100 : 0;
    }

    private function calculateMRRGrowth(): float
    {
        $currentMRR = Subscription::where('status', 'ACTIVE')
            ->where('ends_at', '>', date('Y-m-d H:i:s', currentTimestamp()))
            ->sum('amount');
            
        $previousMRR = Subscription::where('status', 'ACTIVE')
            ->where('ends_at', '>', date('Y-m-d H:i:s', subMonths(1)))
            ->sum('amount');
            
        return $previousMRR > 0 ? (($currentMRR - $previousMRR) / $previousMRR) * 100 : 0;
    }

    private function calculateBlogViews(): int
    {
        return News::sum('views') ?? 0;
    }

    private function calculateBlogEngagement(): float
    {
        $totalViews = News::sum('views') ?? 0;
        $totalEngagement = (News::sum('likes_count') + News::sum('comments_count') + News::sum('shares_count')) ?? 0;
        
        return $totalViews > 0 ? ($totalEngagement / $totalViews) * 100 : 0;
    }

    private function calculateAvgReadTime(): int
    {
        // Estimate based on content length - placeholder for now
        return 5; // minutes
    }

    private function getRecentActivity(): array
    {
        $activities = [];
        
        // Recent quiz attempts
        $recentQuizzes = QuizAttempt::with('user')
            ->orderBy('created_at', 'desc')
            ->take(3)
            ->get();
            
        foreach ($recentQuizzes as $quiz) {
            $activities[] = (object) [
                'description' => $quiz->user->first_name . ' completed a quiz',
                'time' => $quiz->created_at->diffForHumans(),
            ];
        }
        
        // Recent registrations
        $recentUsers = User::orderBy('created_at', 'desc')
            ->take(2)
            ->get();
            
        foreach ($recentUsers as $user) {
            $activities[] = (object) [
                'description' => $user->first_name . ' registered',
                'time' => $user->created_at->diffForHumans(),
            ];
        }
        
        return $activities;
    }
}
