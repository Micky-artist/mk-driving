<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;
use App\Models\User;
use App\Models\Subscription;
use App\Models\QuizAttempt;
use App\Models\SubscriptionPlan;
use App\Models\Setting;
use Carbon\Carbon;
use Illuminate\Support\Facades\Artisan;

class AdminController extends Controller
{
    /**
     * Display the admin dashboard.
     *
     * @return \Illuminate\View\View
     */
    public function dashboard(): View
    {
        // Revenue & Business Metrics
        $monthlyRevenue = Subscription::where('status', 'ACTIVE')
            ->whereMonth('subscriptions.created_at', Carbon::now()->month)
            ->whereYear('subscriptions.created_at', Carbon::now()->year)
            ->join('subscription_plans', 'subscriptions.subscription_plan_id', '=', 'subscription_plans.id')
            ->sum('subscription_plans.price');
            
        $lastMonthRevenue = Subscription::where('status', 'ACTIVE')
            ->whereMonth('subscriptions.created_at', Carbon::now()->subMonth()->month)
            ->whereYear('subscriptions.created_at', Carbon::now()->subMonth()->year)
            ->join('subscription_plans', 'subscriptions.subscription_plan_id', '=', 'subscription_plans.id')
            ->sum('subscription_plans.price');
            
        $revenueGrowth = $lastMonthRevenue > 0 ? round((($monthlyRevenue - $lastMonthRevenue) / $lastMonthRevenue) * 100, 1) : 0;
        
        // User Statistics
        $totalUsers = User::count();
        $activeUsersThisMonth = User::whereHas('quizAttempts', function ($query) {
            $query->where('created_at', '>=', Carbon::now()->startOfMonth());
        })->count();
        $newUsersThisMonth = User::where('created_at', '>=', Carbon::now()->startOfMonth())->count();
        $userGrowthRate = $totalUsers > 0 ? round(($newUsersThisMonth / $totalUsers) * 100, 1) : 0;
        
        // Subscription Statistics
        $totalSubscriptions = Subscription::count();
        $activeSubscriptions = Subscription::where('status', 'ACTIVE')->count();
        $pendingSubscriptions = Subscription::where('status', 'PENDING')->count();
        $newSubscriptionsThisMonth = Subscription::where('created_at', '>=', Carbon::now()->startOfMonth())->count();
        
        // Subscription breakdown by plan
        $subscriptionBreakdown = Subscription::join('subscription_plans', 'subscriptions.subscription_plan_id', '=', 'subscription_plans.id')
            ->selectRaw('subscription_plans.name, COUNT(*) as count')
            ->groupBy('subscription_plans.id', 'subscription_plans.name')
            ->get()
            ->map(function ($item) {
                $planName = is_string($item->name) ? json_decode($item->name, true) : $item->name;
                return [
                    'name' => $planName[app()->getLocale()] ?? ($planName['en'] ?? 'Unknown Plan'),
                    'count' => $item->count
                ];
            });

        // Engagement & Learning Metrics
        $totalQuizAttempts = QuizAttempt::count();
        $completedQuizAttempts = QuizAttempt::where('status', 'completed')->count();
        $quizAttemptsThisMonth = QuizAttempt::where('created_at', '>=', Carbon::now()->startOfMonth())->count();
        $averageQuizScore = QuizAttempt::where('status', 'completed')->avg('score_percentage');
        $passedQuizzes = QuizAttempt::where('status', 'completed')->where('score_percentage', '>=', 70)->count();
        $passRate = $completedQuizAttempts > 0 ? round(($passedQuizzes / $completedQuizAttempts) * 100, 1) : 0;
        $completionRate = $totalQuizAttempts > 0 ? round(($completedQuizAttempts / $totalQuizAttempts) * 100, 1) : 0;
        
        // Customer Metrics
        $churnedUsers = User::whereHas('subscriptions', function ($query) {
            $query->where('status', '!=', 'ACTIVE')
                  ->where('updated_at', '>=', Carbon::now()->subMonth());
        })->count();
        $churnRate = $activeSubscriptions > 0 ? round(($churnedUsers / $activeSubscriptions) * 100, 1) : 0;
        
        // Revenue trends for last 6 months
        $revenueTrends = [];
        for ($i = 5; $i >= 0; $i--) {
            $month = Carbon::now()->subMonths($i);
            $revenue = Subscription::where('status', 'ACTIVE')
                ->whereMonth('subscriptions.created_at', $month->month)
                ->whereYear('subscriptions.created_at', $month->year)
                ->join('subscription_plans', 'subscriptions.subscription_plan_id', '=', 'subscription_plans.id')
                ->sum('subscription_plans.price');
            $revenueTrends[] = [
                'month' => $month->format('M'),
                'revenue' => $revenue
            ];
        }

        return view('admin.dashboard', compact(
            'monthlyRevenue',
            'lastMonthRevenue', 
            'revenueGrowth',
            'totalUsers',
            'activeUsersThisMonth',
            'newUsersThisMonth',
            'userGrowthRate',
            'totalSubscriptions',
            'activeSubscriptions',
            'pendingSubscriptions',
            'newSubscriptionsThisMonth',
            'subscriptionBreakdown',
            'totalQuizAttempts',
            'completedQuizAttempts',
            'quizAttemptsThisMonth',
            'averageQuizScore',
            'passRate',
            'completionRate',
            'churnRate',
            'revenueTrends'
        ));
    }

    /**
     * Display the admin settings page.
     *
     * @return \Illuminate\View\View
     */
    public function settings(): View
    {
        return view('admin.settings');
    }

    /**
     * Update the admin settings.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updateSettings(Request $request)
    {
        // Validate the request
        $validated = $request->validate([
            // Points System
            'points_app_login' => 'required|integer|min:0|max:100',
            'points_quiz_started' => 'required|integer|min:0|max:100',
            'points_quiz_completed' => 'required|integer|min:0|max:100',
            'points_quiz_passed' => 'required|integer|min:0|max:100',
            'points_quiz_perfect' => 'required|integer|min:0|max:100',
            'points_question_asked' => 'required|integer|min:0|max:100',
            'points_question_answered' => 'required|integer|min:0|max:100',
            'points_account_created' => 'required|integer|min:0|max:100',
            
            // Forum Settings
            'forum_auto_approve_questions' => 'nullable|boolean',
            'forum_auto_approve_answers' => 'nullable|boolean',
            'forum_enable_reporting' => 'nullable|boolean',
            'forum_min_points_to_answer' => 'required|integer|min:0|max:1000',
            
            // App Settings
            'app_name' => 'required|string|max:255',
            'contact_email' => 'required|email|max:255',
            'enable_registration' => 'nullable|boolean',
            'maintenance_mode' => 'nullable|boolean',
        ]);

        // Update settings in database
        Setting::set('points_app_login', $validated['points_app_login'], 'integer', 'Points awarded for app login (once per hour)');
        Setting::set('points_quiz_started', $validated['points_quiz_started'], 'integer', 'Points awarded when user starts a quiz');
        Setting::set('points_quiz_completed', $validated['points_quiz_completed'], 'integer', 'Points awarded when user completes a quiz');
        Setting::set('points_quiz_passed', $validated['points_quiz_passed'], 'integer', 'Bonus points for passing quiz (60% or higher)');
        Setting::set('points_quiz_perfect', $validated['points_quiz_perfect'], 'integer', 'Bonus points for perfect quiz score (100%)');
        Setting::set('points_question_asked', $validated['points_question_asked'], 'integer', 'Points awarded when user asks a forum question');
        Setting::set('points_question_answered', $validated['points_question_answered'], 'integer', 'Points awarded when user answers a forum question');
        Setting::set('points_account_created', $validated['points_account_created'], 'integer', 'One-time points awarded when user creates account');
        
        Setting::set('forum_auto_approve_questions', $request->has('forum_auto_approve_questions'), 'boolean', 'Automatically approve new forum questions');
        Setting::set('forum_auto_approve_answers', $request->has('forum_auto_approve_answers'), 'boolean', 'Automatically approve new forum answers');
        Setting::set('forum_enable_reporting', $request->has('forum_enable_reporting'), 'boolean', 'Allow users to report questions and answers');
        Setting::set('forum_min_points_to_answer', $validated['forum_min_points_to_answer'], 'integer', 'Minimum points required to answer forum questions');
        
        Setting::set('app_name', $validated['app_name'], 'string', 'Name of the application displayed to users');
        Setting::set('contact_email', $validated['contact_email'], 'string', 'Contact email for support and inquiries');
        Setting::set('enable_registration', $request->has('enable_registration'), 'boolean', 'Allow new users to register');
        Setting::set('maintenance_mode', $request->has('maintenance_mode'), 'boolean', 'Put application in maintenance mode');
        
        return redirect()->route('admin.settings.index')
            ->with('success', 'Settings updated successfully.');
    }
}
