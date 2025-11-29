<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

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
    /**
     * Show the form for creating a new user.
     *
     * @return \Illuminate\View\View
     */
    public function create(): View
    {
        $roles = Role::all();
        return view('admin.users.create', compact('roles'));
    }

    /**
     * Store a newly created user.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'phone' => 'nullable|string|max:20',
            'password' => 'required|string|min:6|confirmed',
            'role_id' => 'required|exists:roles,id',
        ]);

        try {
            $validated['password'] = Hash::make($validated['password']);
            $user = User::create($validated);

            return redirect()->route('admin.users.show', $user)
                ->with('success', 'User created successfully.');
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Failed to create user: ' . $e->getMessage());
        }
    }

    /**
     * Show the form for editing the specified user.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\View\View
     */
    public function edit(User $user): View
    {
        $roles = Role::all();
        return view('admin.users.edit', compact('user', 'roles'));
    }

    /**
     * Update the specified user.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('users')->ignore($user->id),
            ],
            'phone' => 'nullable|string|max:20',
            'role_id' => 'required|exists:roles,id',
        ]);

        try {
            $user->update($validated);
            return redirect()->route('admin.users.show', $user)
                ->with('success', 'User updated successfully.');
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Failed to update user: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified user.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(User $user)
    {
        try {
            // Prevent deleting own account
            if ($user->id === auth()->id()) {
                return back()->with('error', 'You cannot delete your own account.');
            }

            $user->delete();
            return redirect()->route('admin.users.index')
                ->with('success', 'User deleted successfully.');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to delete user: ' . $e->getMessage());
        }
    }

    /**
     * Show the form for changing user password.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\View\View
     */
    public function changePassword(User $user): View
    {
        return view('admin.users.change-password', compact('user'));
    }

    /**
     * Update the specified user's password.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updatePassword(Request $request, User $user)
    {
        $validated = $request->validate([
            'password' => 'required|string|min:6|confirmed',
        ]);

        try {
            $user->update([
                'password' => Hash::make($validated['password']),
            ]);

            return redirect()->route('admin.users.show', $user)
                ->with('success', 'Password updated successfully.');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to update password: ' . $e->getMessage());
        }
    }

    /**
     * Suspend the specified user.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\RedirectResponse
     */
    public function suspend(User $user)
    {
        try {
            // Prevent suspending self
            if ($user->id === auth()->id()) {
                return back()->with('error', 'You cannot suspend your own account.');
            }

            $user->update([
                'status' => 'suspended',
                'suspended_at' => now(),
            ]);

            return back()->with('success', 'User has been suspended successfully.');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to suspend user: ' . $e->getMessage());
        }
    }

    /**
     * Activate the specified user.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\RedirectResponse
     */
    public function activate(User $user)
    {
        try {
            $user->update([
                'status' => 'active',
                'suspended_at' => null,
            ]);

            return back()->with('success', 'User has been activated successfully.');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to activate user: ' . $e->getMessage());
        }
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
