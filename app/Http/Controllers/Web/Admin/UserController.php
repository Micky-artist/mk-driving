<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\UserActivity;
use App\Models\Visitor;
use App\Services\DeviceTrackingService;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    /**
     * Display a listing of users.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request): View
    {
        $search = $request->get('search');
        
        $query = User::with(['subscriptions' => function($query) {
                $query->latest()->limit(1); // Get only the most recent subscription
            }])
            ->withCount('subscriptions')
            ->whereHas('subscriptions', function($query) {
                $query->where(function($q) {
                    // Active subscriptions: status is 'active' and not expired
                    $q->where('status', 'active')
                      ->where(function($subQuery) {
                          $subQuery->where('ends_at', '>', now())
                                   ->orWhereNull('ends_at');
                      });
                })->orWhere('status', 'pending');
            });
            
        // Apply search filter
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('first_name', 'LIKE', "%{$search}%")
                  ->orWhere('last_name', 'LIKE', "%{$search}%")
                  ->orWhere('email', 'LIKE', "%{$search}%")
                  ->orWhere('phone_number', 'LIKE', "%{$search}%");
            });
        }
        
        $users = $query->orderBy('created_at', 'desc')
            ->paginate(15)
            ->appends(['search' => $search]);
            
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

        // Add visitor analytics data with enhanced device tracking
        $visitorStats = DeviceTrackingService::getVisitorStats();

        return view('admin.users.index', compact('users', 'stats', 'visitorStats'));
    }

    /**
     * Display the specified user.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string  $id
     * @return \Illuminate\View\View
     */
    public function show(Request $request, string $id): View
    {
        $user = User::findOrFail($id);
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
        $roles = [
            'USER' => 'User',
            'INSTRUCTOR' => 'Instructor', 
            'ADMIN' => 'Admin'
        ];
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
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'phone_number' => 'nullable|string|max:20',
            'password' => 'required|string|min:6|confirmed',
            'role' => 'required|in:USER,INSTRUCTOR,ADMIN',
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
     * @param  string  $id
     * @return \Illuminate\View\View
     */
    public function edit(string $id): View
    {
        $user = User::findOrFail($id);
        
        // Prevent admins from editing themselves
        if (Auth::id() == $user->id && Auth::user()->isAdmin()) {
            abort(403, 'Admins cannot edit their own accounts.');
        }
        
        $roles = [
            'USER' => 'User',
            'INSTRUCTOR' => 'Instructor', 
            'ADMIN' => 'Admin'
        ];
        return view('admin.users.edit', compact('user', 'roles'));
    }

    /**
     * Update the specified user.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, string $id)
    {
        $user = User::findOrFail($id);
        
        // Prevent admins from editing themselves
        if (Auth::id() == $user->id && Auth::user()->isAdmin()) {
            abort(403, 'Admins cannot edit their own accounts.');
        }
        
        // Prevent admins from deactivating themselves
        if (Auth::user()->isAdmin() && $request->has('status') && $request->status === 'suspended' && Auth::id() == $user->id) {
            abort(403, 'Admins cannot suspend their own accounts.');
        }
        
        // Prevent admins from removing their own admin access
        if (Auth::user()->isAdmin() && $request->has('role') && $request->role !== 'ADMIN' && Auth::id() == $user->id && $user->isAdmin()) {
            abort(403, 'Admins cannot remove their own admin access.');
        }
        
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
            'phone_number' => 'nullable|string|max:20',
            'role' => 'required|in:USER,INSTRUCTOR,ADMIN',
        ]);

        try {
            $user->update($validated);
            return redirect()->route('admin.users.show', $user->id)
                ->with('success', 'User updated successfully.');
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Failed to update user: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified user.
     *
     * @param  string  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(string $id)
    {
        try {
            $user = User::findOrFail($id);
            
            // Prevent deleting own account
            if (Auth::check() && $user->id === Auth::user()->id) {
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
     * Send password reset link to user.
     *
     * @param  string  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function changePassword(string $id)
    {
        $user = User::findOrFail($id);
        
        try {
            // Generate password reset token
            $token = Password::createToken($user);
            
            // Send password reset email
            $user->sendPasswordResetNotification($token);
            
            return back()->with('success', 'Password reset link has been sent to the user\'s email.');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to send password reset link: ' . $e->getMessage());
        }
    }

    /**
     * Update the specified user's password.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updatePassword(Request $request, string $id)
    {
        $user = User::findOrFail($id);
        $validated = $request->validate([
            'password' => 'required|string|min:6|confirmed',
        ]);

        try {
            $user->update([
                'password' => Hash::make($validated['password']),
            ]);

            return redirect()->route('admin.users.show', $user->id)
                ->with('success', 'Password updated successfully.');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to update password: ' . $e->getMessage());
        }
    }

    /**
     * Suspend the specified user.
     *
     * @param  string  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function suspend(string $id)
    {
        try {
            $user = User::findOrFail($id);
            
            // Prevent suspending self
            if (Auth::check() && $user->id === Auth::user()->id) {
                return back()->with('error', 'You cannot suspend your own account.');
            }

            $user->update([
                'is_active' => false,
            ]);

            return back()->with('success', 'User has been suspended successfully.');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to suspend user: ' . $e->getMessage());
        }
    }

    /**
     * Activate the specified user.
     *
     * @param  string  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function activate(string $id)
    {
        try {
            $user = User::findOrFail($id);
            
            // Prevent activating self
            if (Auth::check() && $user->id === Auth::user()->id) {
                return back()->with('error', 'You cannot activate your own account.');
            }
            
            $user->update([
                'is_active' => true,
            ]);

            return back()->with('success', 'User has been activated successfully.');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to activate user: ' . $e->getMessage());
        }
    }

    /**
     * Make the specified user an admin.
     *
     * @param  string  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function makeAdmin(string $id)
    {
        try {
            $user = User::findOrFail($id);
            
            // Prevent making self admin (should already be admin)
            if (Auth::check() && $user->id === Auth::user()->id) {
                return back()->with('error', 'You cannot change your own admin status.');
            }

            $user->update(['role' => 'admin']);

            return back()->with('success', 'User has been made admin successfully.');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to make user admin: ' . $e->getMessage());
        }
    }

    /**
     * Remove admin privileges from the specified user.
     *
     * @param  string  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function removeAdmin(string $id)
    {
        try {
            $user = User::findOrFail($id);
            
            // Prevent removing own admin status
            if (Auth::check() && $user->id === Auth::user()->id) {
                return back()->with('error', 'You cannot remove your own admin status.');
            }

            $user->update(['role' => 'USER']);

            return back()->with('success', 'Admin privileges removed successfully.');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to remove admin privileges: ' . $e->getMessage());
        }
    }

    /**
     * Display all users with advanced filtering.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\View\View
     */
    public function all(Request $request): View
    {
        $search = $request->get('search');
        $role = $request->get('role');
        $status = $request->get('status');
        $subscription = $request->get('subscription');
        $dateFilter = $request->get('date_filter');
        $dateRange = $request->get('date_range');
        $sort = $request->get('sort', 'created_at');
        $order = $request->get('order', 'desc');
        
        // Legacy parameters for backward compatibility
        $filter = $request->get('filter');
        $period = $request->get('period');
        
        $query = User::with(['subscriptions' => function($query) {
                $query->latest()->limit(1); // Get only the most recent subscription
            }])
            ->withCount('subscriptions');
            
        // Apply search filter
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('first_name', 'LIKE', "%{$search}%")
                  ->orWhere('last_name', 'LIKE', "%{$search}%")
                  ->orWhere('email', 'LIKE', "%{$search}%")
                  ->orWhere('phone_number', 'LIKE', "%{$search}%");
            });
        }
        
        // Apply role filter
        if ($role) {
            $query->where('role', $role);
        }
        
        // Apply status filter
        if ($status !== null && $status !== '') {
            $query->where('is_active', $status === '1');
        }
        
        // Apply subscription filter
        if ($subscription) {
            if ($subscription === 'active') {
                $query->whereHas('subscriptions', function($q) {
                    $q->where('status', 'active')
                      ->where(function($subQuery) {
                          $subQuery->where('ends_at', '>', now())
                                   ->orWhereNull('ends_at');
                      });
                });
            } elseif ($subscription === 'pending') {
                $query->whereHas('subscriptions', function($q) {
                    $q->where('status', 'pending');
                });
            } elseif ($subscription === 'expired') {
                $query->whereHas('subscriptions', function($q) {
                    $q->where('status', '!=', 'active')
                      ->where('ends_at', '<', now());
                });
            } elseif ($subscription === 'none') {
                $query->whereDoesntHave('subscriptions');
            }
        }
        
        // Apply new date filter
        if ($dateFilter === 'created_at') {
            if ($dateRange === '30_days') {
                $query->where('created_at', '>=', now()->subDays(30));
            } elseif ($dateRange === '7_days') {
                $query->where('created_at', '>=', now()->subDays(7));
            } elseif ($dateRange === 'this_month') {
                $query->where('created_at', '>=', now()->startOfMonth());
            }
        }
        
        // Legacy date filter for backward compatibility
        if ($filter === 'created_at') {
            if ($period === 'this_month') {
                $query->where('created_at', '>=', now()->startOfMonth());
            } elseif ($period === 'last_30_days') {
                $query->where('created_at', '>=', now()->subDays(30));
            } elseif ($period === 'last_7_days') {
                $query->where('created_at', '>=', now()->subDays(7));
            }
        }
        
        // Validate sort column
        $allowedSorts = ['name', 'email', 'created_at'];
        if (!in_array($sort, $allowedSorts)) {
            $sort = 'created_at';
        }
        
        // Handle name sorting (concatenate first_name and last_name)
        if ($sort === 'name') {
            $query->orderByRaw("CONCAT(first_name, ' ', last_name) {$order}");
        } else {
            $query->orderBy($sort, $order);
        }
        
        $users = $query->paginate(15)
            ->appends([
                'search' => $search,
                'role' => $role,
                'status' => $status,
                'subscription' => $subscription,
                'date_filter' => $dateFilter,
                'date_range' => $dateRange,
                'filter' => $filter,
                'period' => $period,
                'sort' => $sort,
                'order' => $order
            ]);
            
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
            'new_last_30_days' => User::where('created_at', '>=', now()->subDays(30))->count(),
            'new_last_7_days' => User::where('created_at', '>=', now()->subDays(7))->count(),
        ];

        return view('admin.users.all', compact('users', 'stats'));
    }

    /**
     * Display unregistered visitor analytics.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\View\View
     */
    public function unregisteredVisits(Request $request): View
    {
        $visitorStats = DeviceTrackingService::getVisitorStats();
        
        // Get detailed visitor data - only unregistered visitors
        $visitors = Visitor::where('is_registered_user', false)
            ->orderBy('last_seen_at', 'desc')
            ->paginate(20);
            
        return view('admin.users.unregistered-visits', compact('visitorStats', 'visitors'));
    }

    
    /**
     * Display recent user activity timeline.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\View\View
     */
    public function recentActivity(Request $request): View
    {
        $query = UserActivity::with('user')
            ->orderBy('created_at', 'desc');

        // Filter by activity type
        if ($request->filled('activity_type')) {
            $query->where('type', $request->activity_type);
        }

        // Filter by timeframe
        if ($request->filled('timeframe')) {
            switch ($request->timeframe) {
                case '24h':
                    $query->where('created_at', '>=', now()->subHours(24));
                    break;
                case '7d':
                    $query->where('created_at', '>=', now()->subDays(7));
                    break;
                case '30d':
                    $query->where('created_at', '>=', now()->subDays(30));
                    break;
                // 'all' case doesn't need filtering
            }
        }

        $activities = $query->paginate(20);

        // Get activity statistics
        $activityStats = [
            'total_activities' => UserActivity::count(),
            'active_today' => UserActivity::where('created_at', '>=', now()->startOfDay())->distinct('user_id')->count(),
            'quiz_attempts' => UserActivity::where('type', 'quiz_attempt')->count(),
            'new_registrations' => UserActivity::where('type', 'registration')->count(),
            'subscriptions' => UserActivity::where('type', 'subscription')->count(),
        ];

        return view('admin.users.recent-activity', compact('activities', 'activityStats'));
    }

    
    /**
     * Update the specified user's status.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updateStatus(Request $request, string $id)
    {
        $user = User::findOrFail($id);
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
