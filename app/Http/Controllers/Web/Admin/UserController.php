<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;

class UserController extends Controller
{
    /**
     * Display a listing of users.
     *
     * @return \Illuminate\View\View
     */
    public function index(): View
    {
        $users = User::with(['subscriptions' => function($query) {
                $query->latest()->limit(1); // Get only the most recent subscription
            }])
            ->withCount('subscriptions')
            ->orderBy('created_at', 'desc')
            ->paginate(15);
            
        $stats = [
            'total' => User::count(),
            'active' => User::whereHas('subscriptions', function($query) {
                $query->where('status', 'active')
                      ->where(function($q) {
                          $q->where('ends_at', '>', now())
                            ->orWhereNull('ends_at');
                      });
            })->count(),
            'new_this_month' => User::where('created_at', '>=', now()->startOfMonth())->count(),
        ];

        return view('admin.users.index', compact('users', 'stats'));
    }

    /**
     * Display the specified user.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\View\View
     */
    public function show(User $user): View
    {
        $user->load(['subscriptions.plan', 'quizAttempts.quiz']);
        
        return view('admin.users.show', compact('user'));
    }

    /**
     * Update the specified user's status.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updateStatus(Request $request, User $user)
    {
        $request->validate([
            'status' => 'required|in:active,suspended',
            'reason' => 'nullable|string|max:255',
        ]);

        $user->update([
            'status' => $request->status,
            'status_reason' => $request->reason,
        ]);

        return back()->with('success', 'User status updated successfully.');
    }
}
