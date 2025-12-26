<?php

namespace App\Http\Middleware;

use App\Models\Notification;
use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

class TrackNotifications
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Only track notifications for admin users
        if ($request->user() && $request->user()->role === 'admin') {
            $this->trackNotifications();
        }

        return $next($request);
    }

    /**
     * Track and create notifications for recent activity.
     */
    private function trackNotifications(): void
    {
        $twentyFourHoursAgo = now()->subHours(24);

        // Track new users
        $this->trackNewUsers($twentyFourHoursAgo);

        // Track new subscriptions
        $this->trackNewSubscriptions($twentyFourHoursAgo);

        // Track new forum questions
        $this->trackNewForumQuestions($twentyFourHoursAgo);
    }

    /**
     * Track new user registrations.
     */
    private function trackNewUsers($since): void
    {
        $newUsers = User::where('created_at', '>=', $since)
            ->whereNotExists(function ($query) {
                $query->select(DB::raw(1))
                    ->from('notifications')
                    ->where('notifications.type', 'new_user')
                    ->whereRaw('notifications.message LIKE CONCAT("%", users.name, "%")');
            })
            ->get();

        foreach ($newUsers as $user) {
            Notification::firstOrCreate([
                'type' => 'new_user',
                'title' => 'New User Registration',
                'message' => "{$user->name} ({$user->email}) has registered",
                'url' => '/admin/users/all?date_filter=created_at&date_range=30_days',
                'notified_at' => $user->created_at,
            ]);
        }
    }

    /**
     * Track new subscriptions.
     */
    private function trackNewSubscriptions($since): void
    {
        // Check if subscriptions table exists
        if (!DB::getSchemaBuilder()->hasTable('subscriptions')) {
            return;
        }

        $newSubscriptions = DB::table('subscriptions')
            ->where('created_at', '>=', $since)
            ->whereNotExists(function ($query) {
                $query->select(DB::raw(1))
                    ->from('notifications')
                    ->where('notifications.type', 'new_subscription')
                    ->whereRaw('notifications.url LIKE "%/admin/subscriptions/pending%"');
            })
            ->get();

        foreach ($newSubscriptions as $subscription) {
            Notification::firstOrCreate([
                'type' => 'new_subscription',
                'title' => 'New Subscription',
                'message' => 'A new subscription has been created',
                'url' => '/admin/subscriptions/pending',
                'notified_at' => $subscription->created_at,
            ]);
        }
    }

    /**
     * Track new forum questions.
     */
    private function trackNewForumQuestions($since): void
    {
        // Check if forum_posts table exists
        if (!DB::getSchemaBuilder()->hasTable('forum_posts')) {
            return;
        }

        $newQuestions = DB::table('forum_posts')
            ->where('created_at', '>=', $since)
            ->where('type', 'question') // Assuming there's a type column
            ->whereNotExists(function ($query) {
                $query->select(DB::raw(1))
                    ->from('notifications')
                    ->where('notifications.type', 'new_forum_question')
                    ->whereRaw('notifications.url LIKE "%/admin/forum%"');
            })
            ->get();

        foreach ($newQuestions as $question) {
            Notification::firstOrCreate([
                'type' => 'new_forum_question',
                'title' => 'New Forum Question',
                'message' => 'A new question has been posted in the forum',
                'url' => '/admin/forum',
                'notified_at' => $question->created_at,
            ]);
        }
    }
}
