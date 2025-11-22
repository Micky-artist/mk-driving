<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use App\Models\Subscription;
use App\Models\User;
use App\Models\Quiz;
use App\Models\News;
use Carbon\Carbon;

class AdminController extends Controller
{
    public function dashboard()
    {
        // Get subscription statistics
        $subscriptionStats = [
            'total' => Subscription::count(),
            'active' => Subscription::where('status', 'active')->count(),
            'pending' => Subscription::where('status', 'pending')->count(),
            'expired' => Subscription::where('status', 'expired')->count(),
        ];

        // Get user statistics
        $userStats = [
            'total' => User::count(),
            'new_today' => User::whereDate('created_at', today())->count(),
            'new_week' => User::where('created_at', '>=', now()->startOfWeek())->count(),
            'new_month' => User::where('created_at', '>=', now()->startOfMonth())->count(),
        ];

        // Get content statistics
        $contentStats = [
            'quizzes' => Quiz::count(),
            'published_quizzes' => Quiz::where('is_published', true)->count(),
            'news' => News::count(),
            'published_news' => News::where('is_published', true)->count(),
        ];

        // Get recent subscriptions that need approval
        $pendingSubscriptions = Subscription::with('user', 'plan')
            ->where('status', 'pending')
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        // Get recent activity (you might want to implement an activity log for this)
        $recentActivity = [];

        return view('admin.dashboard', compact(
            'subscriptionStats',
            'userStats',
            'contentStats',
            'pendingSubscriptions',
            'recentActivity'
        ));
    }
}
