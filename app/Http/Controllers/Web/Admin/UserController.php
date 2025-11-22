<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Subscription;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Inertia\Inertia;

class UserController extends Controller
{
    /**
     * Display a listing of users.
     */
    public function index(Request $request)
    {
        $filter = $request->query('filter', '');
        $tab = $request->query('tab', 'users');
        $perPage = 10;
        $currentPage = $request->query('page', 1);

        // Base query for users
        $usersQuery = User::where('id', '!=', auth()->id())
            ->with('subscriptions')
            ->when($filter, function ($query) use ($filter) {
                $query->where(function($q) use ($filter) {
                    $q->where('first_name', 'like', "%{$filter}%")
                      ->orWhere('last_name', 'like', "%{$filter}%")
                      ->orWhere('email', 'like', "%{$filter}%");
                });
            });

        // Get user statistics
        $stats = [
            'total' => (clone $usersQuery)->count(),
            'instructors' => (clone $usersQuery)->where('role', 'instructor')->count(),
            'admins' => (clone $usersQuery)->where('role', 'admin')->count(),
        ];

        // Get subscriber statistics
        $subscriberStats = [
            'total' => Subscription::distinct('user_id')->count('user_id'),
            'active' => Subscription::where('status', 'active')->distinct('user_id')->count('user_id'),
            'total_revenue' => Subscription::where('status', 'active')->sum('amount'),
        ];

        // Get subscribers if on subscribers tab
        $subscribers = [];
        if ($tab === 'subscribers') {
            $subscribers = User::whereHas('subscriptions')
                ->with(['subscriptions'])
                ->when($filter, function ($query) use ($filter) {
                    $query->where(function($q) use ($filter) {
                        $q->where('first_name', 'like', "%{$filter}%")
                          ->orWhere('last_name', 'like', "%{$filter}%")
                          ->orWhere('email', 'like', "%{$filter}%");
                    });
                })
                ->paginate($perPage, ['*'], 'page', $currentPage);
        }

        // Get regular users with pagination
        $users = $usersQuery->paginate($perPage, ['*'], 'page', $currentPage);

        return view('admin.users.index', [
            'users' => $users,
            'subscribers' => $subscribers,
            'stats' => $stats,
            'subscriberStats' => $subscriberStats,
            'filter' => $filter,
            'activeTab' => $tab,
        ]);
    }

    /**
     * Show the form for creating a new user.
     */
    public function create()
    {
        return view('admin.users.create');
    }

    /**
     * Store a newly created user in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'role' => ['required', Rule::in(['user', 'instructor', 'admin'])],
        ]);

        $user = User::create([
            'first_name' => $validated['first_name'],
            'last_name' => $validated['last_name'],
            'email' => $validated['email'],
            'password' => bcrypt($validated['password']),
            'role' => $validated['role'],
            'email_verified_at' => now(),
        ]);

        return redirect()->route('admin.users.index')
            ->with('success', 'User created successfully.');
    }

    /**
     * Show the form for editing the specified user.
     */
    public function edit(User $user)
    {
        return view('admin.users.edit', compact('user'));
    }

    /**
     * Update the specified user in storage.
     */
    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('users')->ignore($user->id),
            ],
            'role' => ['required', Rule::in(['user', 'instructor', 'admin'])],
        ]);

        $user->update($validated);

        return redirect()->route('admin.users.index')
            ->with('success', 'User updated successfully.');
    }

    /**
     * Remove the specified user from storage.
     */
    public function destroy(User $user)
    {
        // Prevent deleting self
        if ($user->id === auth()->id()) {
            return redirect()->back()
                ->with('error', 'You cannot delete your own account.');
        }

        $user->delete();

        return redirect()->route('admin.users.index')
            ->with('success', 'User deleted successfully.');
    }

    /**
     * Change the user's password.
     */
    public function changePassword(Request $request, User $user)
    {
        $validated = $request->validate([
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user->update([
            'password' => bcrypt($validated['password']),
        ]);

        return redirect()->route('admin.users.edit', $user)
            ->with('success', 'Password updated successfully.');
    }
}
