<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use App\Models\Subscription;
use App\Models\Payment;
use App\Models\SubscriptionPlan;
use App\Models\UserActivity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class SubscriptionController extends Controller
{
    /**
     * Show all subscription history records.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\View\View
     */
    public function all(Request $request)
    {
        $search = $request->get('search');
        
        // Get all subscriptions with user and plan relationships
        $subscriptionsQuery = Subscription::with(['user', 'plan'])
            ->orderBy('created_at', 'desc');
            
        // Apply search filter
        if ($search) {
            $subscriptionsQuery->where(function($query) use ($search) {
                $query->whereHas('user', function($userQuery) use ($search) {
                        $userQuery->where('first_name', 'like', "%{$search}%")
                               ->orWhere('last_name', 'like', "%{$search}%")
                               ->orWhere('email', 'like', "%{$search}%");
                    })
                    ->orWhereHas('plan', function($planQuery) use ($search) {
                        $planQuery->where('name', 'like', "%{$search}%");
                    });
            });
        }
        
        $subscriptions = $subscriptionsQuery->paginate(25);
            
        // Get comprehensive stats
        $stats = [
            'total' => Subscription::count(),
            'active' => Subscription::where('status', 'ACTIVE')->count(),
            'expired' => Subscription::where('status', 'EXPIRED')->count(),
            'cancelled' => Subscription::where('status', 'CANCELLED')->count(),
            'pending' => Subscription::where('status', 'PENDING')->count(),
            'buggy' => $this->getBuggySubscriptionsCount(),
        ];
        
        return view('admin.subscriptions.all', compact('subscriptions', 'stats', 'search'));
    }

        
        
    /**
     * Show the form for creating a new subscription.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        $plans = SubscriptionPlan::where('is_active', true)->orderBy('price')->get();
        $users = \App\Models\User::select('id', 'first_name', 'last_name', 'email')
            ->orderBy('first_name')
            ->get();
            
        return view('admin.subscriptions.create', compact('plans', 'users'));
    }
    
    /**
     * Store a newly created subscription in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'subscription_plan_id' => 'required|exists:subscription_plans,id',
            'starts_at' => 'required|date',
            'status' => 'required|in:pending,active,cancelled',
            'payment_status' => 'required|in:pending,completed,failed,refunded',
            'amount' => 'required|numeric|min:0',
            'payment_method' => 'nullable|string|max:50',
            'notes' => 'nullable|string',
        ]);
        
        DB::beginTransaction();
        
        try {
            // Create the subscription
            $subscription = Subscription::create([
                'user_id' => $validated['user_id'],
                'subscription_plan_id' => $validated['subscription_plan_id'],
                'starts_at' => $validated['starts_at'],
                'ends_at' => now()->addDays(SubscriptionPlan::find($validated['subscription_plan_id'])->duration_in_days),
                'status' => $validated['status'],
                'notes' => $validated['notes'] ?? null,
            ]);
            
            // Create a payment record
            Payment::create([
                'user_id' => $validated['user_id'],
                'subscription_id' => $subscription->id,
                'amount' => $validated['amount'],
                'status' => $validated['payment_status'],
                'payment_method' => $validated['payment_method'] ?? 'manual',
                'transaction_id' => 'MANUAL-' . time(),
                'payment_date' => now(),
            ]);
            
            // Log subscription activity
            UserActivity::log(
                $validated['user_id'],
                UserActivity::TYPE_SUBSCRIPTION,
                [
                    'action' => 'created',
                    'subscription_id' => $subscription->id,
                    'plan_name' => SubscriptionPlan::find($validated['subscription_plan_id'])->name['en'] ?? 'Unknown Plan',
                    'amount' => $validated['amount'],
                    'status' => $validated['status'],
                ],
                request()->ip(),
                request()->userAgent()
            );
            
            DB::commit();
            
            return redirect()->route('admin.subscriptions.show', $subscription)
                ->with('success', 'Subscription created successfully.');
                
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to create subscription: ' . $e->getMessage());
            return back()->withInput()->with('error', 'Failed to create subscription: ' . $e->getMessage());
        }
    }
    /**
     * Display published subscriptions (active with valid end dates).
     */
    public function published(Request $request)
    {
        $search = $request->get('search');
        
        $subscriptionsQuery = Subscription::with(['user', 'plan'])
            ->where('status', 'active')
            ->where('ends_at', '>=', now())
            ->orderBy('created_at', 'desc');
            
        // Apply search filter
        if ($search) {
            $subscriptionsQuery->where(function($query) use ($search) {
                $query->whereHas('user', function($userQuery) use ($search) {
                    $userQuery->where('first_name', 'like', "%{$search}%")
                             ->orWhere('last_name', 'like', "%{$search}%")
                             ->orWhere('email', 'like', "%{$search}%")
                             ->orWhere('phone_number', 'like', "%{$search}%");
                })
                ->orWhereHas('plan', function($planQuery) use ($search) {
                    $planQuery->where('name', 'like', "%{$search}%");
                });
            });
        }
        
        $subscriptions = $subscriptionsQuery->paginate(20);
            
        $stats = [
            'total' => Subscription::count(),
            'active' => Subscription::where('status', 'active')->count(),
            'pending' => Subscription::where('status', 'pending')->count(),
            'published' => Subscription::where('status', 'active')->where('ends_at', '>=', now())->count(),
            'unpublished' => Subscription::whereHas('plan', function($query) {
                $query->where('is_active', false);
            })->count(),
            'revenue' => Subscription::where('status', 'active')
                ->whereMonth('created_at', now()->month)
                ->sum('amount') ?? 0,
        ];
        
        return view('admin.subscriptions.published', compact('subscriptions', 'stats', 'search'));
    }

    /**
     * Display subscription management dashboard.
     */
    public function index()
    {
        // Get all subscriptions with user and plan relationships
        $subscriptions = Subscription::with(['user', 'plan'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);
            
        // Get stats for dashboard
        $stats = [
            'total' => Subscription::count(),
            'active' => Subscription::where('status', 'ACTIVE')->count(),
            'pending' => Subscription::where('status', 'PENDING')->count(),
            'published' => Subscription::where('status', 'ACTIVE')->where('ends_at', '>=', now())->count(),
            'unpublished' => Subscription::whereHas('plan', function($query) {
                $query->where('is_active', false);
            })->count(),
            'revenue' => Subscription::where('status', 'ACTIVE')
                ->whereMonth('created_at', now()->month)
                ->sum('amount') ?? 0,
            // Add plan stats
            'total_plans' => SubscriptionPlan::where('is_active', true)->count(),
        ];
            
        return view('admin.subscriptions.index', compact('subscriptions', 'stats'));
    }
    
    /**
     * Display a listing of pending subscription requests.
     */
    public function pending()
    {
        // Get pending subscriptions (including those with pending payment status)
        $subscriptions = Subscription::with(['user', 'plan'])
            ->where(function($query) {
                $query->where('status', 'PENDING')
                      ->orWhere('payment_status', 'PENDING');
            })
            ->orderBy('created_at', 'desc')
            ->paginate(20);
            
        return view('admin.subscriptions.pending', [
            'subscriptions' => $subscriptions
        ]);
    }

    /**
     * Display a listing of active subscriptions.
     */
    public function active()
    {
        // Get active subscriptions
        $subscriptions = Subscription::with(['user', 'plan'])
            ->where('status', 'active')
            ->where('ends_at', '>=', now())
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('admin.subscriptions.active', [
            'subscriptions' => $subscriptions
        ]);
    }

    /**
     * Cancel the specified subscription.
     *
     * @param  \App\Models\Subscription  $subscription
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Subscription $subscription)
    {
        try {
            $subscription->update(['status' => 'cancelled']);
            return redirect()->back()->with('success', 'Subscription cancelled successfully.');
        } catch (\Exception $e) {
            Log::error('Failed to cancel subscription: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to cancel subscription.');
        }
    }

    /**
     * Cancel the specified subscription immediately.
     *
     * @param  \App\Models\Subscription  $subscription
     * @return \Illuminate\Http\RedirectResponse
     */
    public function cancel(Subscription $subscription)
    {
        try {
            $subscription->update([
                'status' => 'cancelled',
                'ends_at' => now()
            ]);
            return redirect()->back()->with('success', 'Subscription cancelled successfully.');
        } catch (\Exception $e) {
            Log::error('Failed to cancel subscription: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to cancel subscription.');
        }
    }

    /**
     * Display subscription statistics.
     *
     * @return \Illuminate\View\View
     */
    public function stats()
    {
        $stats = [
            'total' => Subscription::count(),
            'active' => Subscription::where('status', 'active')
                ->where('ends_at', '>=', now())
                ->count(),
            'pending' => Subscription::where('status', 'pending')->count(),
            'cancelled' => Subscription::where('status', 'cancelled')->count(),
            'revenue' => Payment::where('status', 'COMPLETED')
                ->sum('amount')
        ];

        // Get subscription growth data for the last 6 months
        $subscriptionGrowth = Subscription::select(
                DB::raw('DATE_FORMAT(created_at, "%Y-%m") as month'),
                DB::raw('count(*) as count')
            )
            ->where('created_at', '>=', now()->subMonths(6))
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        return view('admin.subscriptions.stats', [
            'stats' => $stats,
            'subscriptionGrowth' => $subscriptionGrowth
        ]);
    }

    /**
     * Display the specified subscription.
     *
     * @param  \App\Models\Subscription  $subscription
     * @return \Illuminate\View\View
     */
    public function show(Subscription $subscription)
    {
        $subscription->load(['user', 'plan']);
        
        return view('admin.subscriptions.show', [
            'subscription' => $subscription
        ]);
    }
    
    /**
     * Show the form for editing the specified subscription.
     *
     * @param  \App\Models\Subscription  $subscription
     * @return \Illuminate\View\View
     */
    public function edit(Subscription $subscription)
    {
        $plans = SubscriptionPlan::where('is_active', true)
            ->orderBy('price')
            ->get();
            
        return view('admin.subscriptions.edit', [
            'subscription' => $subscription,
            'plans' => $plans,
        ]);
    }
    
    /**
     * Update the specified subscription in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Subscription  $subscription
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, Subscription $subscription)
    {
        $validated = $request->validate([
            'subscription_plan_id' => 'required|exists:subscription_plans,id',
            'starts_at' => 'required|date',
            'ends_at' => 'nullable|date|after_or_equal:starts_at',
            'status' => 'required|in:pending,active,cancelled,expired',
            'notes' => 'nullable|string',
        ]);
        
        DB::beginTransaction();
        
        try {
            // If the plan is being changed, update the end date based on the new plan's duration
            if ($subscription->subscription_plan_id != $validated['subscription_plan_id']) {
                $plan = SubscriptionPlan::find($validated['subscription_plan_id']);
                $validated['ends_at'] = $validated['ends_at'] ?? 
                    $validated['starts_at']->addDays($plan->duration_in_days);
            }
            
            $subscription->update($validated);
            
            DB::commit();
            
            return redirect()->route('admin.subscriptions.show', $subscription)
                ->with('success', 'Subscription updated successfully.');
                
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to update subscription: ' . $e->getMessage());
            return back()->withInput()->with('error', 'Failed to update subscription: ' . $e->getMessage());
        }
    }

    /**
     * Approve a subscription request.
     */
    public function approve(Subscription $subscription)
    {
        DB::beginTransaction();
        
        try {
            // Update subscription status and payment status
            $subscription->update([
                'status' => 'active',
                'payment_status' => 'COMPLETED',
                'approved_at' => now(),
                'starts_at' => now(), // Start counting from approval time
                'ends_at' => now()->addDays($subscription->plan->duration_in_days), // Calculate expiration from approval time
            ]);
            
            // Log subscription approval activity
            UserActivity::log(
                $subscription->user_id,
                UserActivity::TYPE_SUBSCRIPTION,
                [
                    'action' => 'approved',
                    'subscription_id' => $subscription->id,
                    'plan_name' => $subscription->plan->name['en'] ?? 'Unknown Plan',
                    'amount' => $subscription->amount,
                    'status' => 'active',
                ],
                request()->ip(),
                request()->userAgent()
            );
            
            DB::commit();
            
            if (request()->wantsJson() || request()->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Subscription approved successfully'
                ]);
            }
            
            return back()->with('success', 'Subscription approved successfully.');
            
        } catch (\Exception $e) {
            DB::rollBack();
            
            if (request()->wantsJson() || request()->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to approve subscription: ' . $e->getMessage()
                ], 500);
            }
            
            return back()->with('error', 'Failed to approve subscription: ' . $e->getMessage());
        }
    }
    
    /**
     * Reject a subscription request.
     */
    public function reject(Subscription $subscription)
    {
        DB::beginTransaction();
        
        try {
            // Update subscription status and payment status
            $subscription->update([
                'status' => 'CANCELLED',
                'payment_status' => 'REJECTED',
                'rejected_at' => now()
            ]);
            
            // Log subscription rejection activity
            UserActivity::log(
                $subscription->user_id,
                UserActivity::TYPE_SUBSCRIPTION,
                [
                    'action' => 'rejected',
                    'subscription_id' => $subscription->id,
                    'plan_name' => $subscription->plan->name['en'] ?? 'Unknown Plan',
                    'amount' => $subscription->amount,
                    'status' => 'rejected',
                ],
                request()->ip(),
                request()->userAgent()
            );
            
            DB::commit();
            
            if (request()->wantsJson() || request()->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Subscription rejected successfully'
                ]);
            }
            
            return back()->with('success', 'Subscription rejected successfully.');
            
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to reject subscription: ' . $e->getMessage(), [
                'subscription_id' => $subscription->id,
                'trace' => $e->getTraceAsString()
            ]);
            
            if (request()->wantsJson() || request()->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to reject subscription: ' . $e->getMessage()
                ], 500);
            }
            
            return back()->with('error', 'Failed to reject subscription: ' . $e->getMessage());
        }
    }
    
    /**
     * Force delete the specified subscription from storage.
     *
     * @param  \App\Models\Subscription  $subscription
     * @return \Illuminate\Http\RedirectResponse
     */
    public function forceDelete(Subscription $subscription)
    {
        DB::beginTransaction();
        
        try {
            // Log subscription deletion activity
            UserActivity::log(
                $subscription->user_id,
                UserActivity::TYPE_SUBSCRIPTION,
                [
                    'action' => 'deleted',
                    'subscription_id' => $subscription->id,
                    'plan_name' => $subscription->plan->name['en'] ?? 'Unknown Plan',
                    'amount' => $subscription->amount,
                    'status' => $subscription->status,
                ],
                request()->ip(),
                request()->userAgent()
            );
            
            // Delete the subscription
            $subscription->delete();
            
            DB::commit();
            
            if (request()->wantsJson() || request()->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Subscription deleted permanently'
                ]);
            }
            
            return back()->with('success', 'Subscription deleted permanently.');
            
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to delete subscription: ' . $e->getMessage(), [
                'subscription_id' => $subscription->id,
                'trace' => $e->getTraceAsString()
            ]);
            
            if (request()->wantsJson() || request()->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to delete subscription: ' . $e->getMessage()
                ], 500);
            }
            
            return back()->with('error', 'Failed to delete subscription: ' . $e->getMessage());
        }
    }
    
    /**
     * Calculate subscription end date based on plan duration.
     */
    protected function calculateEndDate($plan)
    {
        if (!$plan) {
            return now()->addMonth(); // Default to 1 month if plan not found
        }
        
        $duration = $plan->duration_in_days ?? 30; // Default to 30 days if not specified
        return now()->addDays($duration);
    }
    
    /**
     * Get count of buggy subscriptions that need fixing
     */
    public function getBuggySubscriptionsCount()
    {
        return $this->getBuggySubscriptions()->count();
    }
    
    /**
     * Get subscriptions that were likely affected by the duration_days bug
     */
    public function getBuggySubscriptions()
    {
        $now = now();
        
        return Subscription::where('status', 'ACTIVE')
            ->where('payment_status', 'COMPLETED')
            ->where(function($query) use ($now) {
                // Case 1: End date is very close to start date (within 1 minute) - clear bug symptom
                $query->whereRaw('TIMESTAMPDIFF(SECOND, starts_at, ends_at) <= 60')
                // Case 2: End date is in past but subscription was approved recently (within last 30 days)
                ->orWhere(function($subQuery) use ($now) {
                    $subQuery->where('ends_at', '<=', $now)
                            ->where('starts_at', '>=', $now->subDays(30));
                });
            })
            ->orWhere(function($query) {
                // Case 3: EXPIRED subscriptions with 0 duration that should be active
                $query->where('status', 'EXPIRED')
                      ->where('payment_status', 'COMPLETED')
                      ->whereRaw('TIMESTAMPDIFF(SECOND, starts_at, ends_at) = 0')
                      ->where('created_at', '>=', now()->subDays(7)); // Created in last 7 days
            });
    }
    
    /**
     * Show page with buggy subscriptions that need fixing
     */
    public function buggy()
    {
        $buggySubscriptions = $this->getBuggySubscriptions()
            ->with(['user', 'plan'])
            ->orderBy('created_at', 'desc')
            ->paginate(25);
            
        return view('admin.subscriptions.buggy', compact('buggySubscriptions'));
    }
    
    /**
     * Fix a single buggy subscription
     */
    public function fixSubscription(Subscription $subscription)
    {
        try {
            $plan = $subscription->plan;
            
            if (!$plan || !$plan->duration_in_days) {
                return back()->with('error', 'Cannot fix subscription - missing plan or duration information.');
            }
            
            // Calculate the correct end date based on NOW (not start time)
            $correctEndDate = now()->copy()->addDays($plan->duration_in_days);
            
            // Only fix if the correct end date is actually in the future
            if ($correctEndDate <= now()) {
                return back()->with('error', 'Cannot fix subscription - corrected end date is not in the future.');
            }
            
            $oldEndDate = $subscription->ends_at;
            
            // Update the subscription with the correct end date and status
            $subscription->update([
                'status' => 'ACTIVE', // Change from EXPIRED to ACTIVE
                'ends_at' => $correctEndDate,
                'metadata' => array_merge($subscription->metadata ?? [], [
                    'duration_bug_fixed' => true,
                    'fixed_at' => now()->toDateTimeString(),
                    'original_end_date' => $oldEndDate,
                    'corrected_end_date' => $correctEndDate,
                    'original_status' => $subscription->getOriginal('status'),
                    'fix_reason' => 'duration_days_vs_duration_in_days_bug',
                    'fixed_by_admin' => auth()->id(),
                ])
            ]);
            
            // Log the fix for audit purposes
            Log::info('Subscription duration bug fixed by admin', [
                'subscription_id' => $subscription->id,
                'user_id' => $subscription->user_id,
                'plan_id' => $subscription->subscription_plan_id,
                'plan_name' => $plan->name['en'] ?? 'Unknown',
                'old_end_date' => $oldEndDate,
                'new_end_date' => $correctEndDate,
                'plan_duration_days' => $plan->duration_in_days,
                'fixed_by' => auth()->id(),
                'fixed_at' => now()
            ]);
            
            return back()->with('success', 'Subscription fixed successfully! End date corrected from ' . $oldEndDate . ' to ' . $correctEndDate);
            
        } catch (\Exception $e) {
            Log::error('Failed to fix subscription', [
                'subscription_id' => $subscription->id,
                'error' => $e->getMessage()
            ]);
            
            return back()->with('error', 'Failed to fix subscription: ' . $e->getMessage());
        }
    }
    
    /**
     * Fix all buggy subscriptions in bulk
     */
    public function fixAllBuggy()
    {
        $buggySubscriptions = $this->getBuggySubscriptions()->get();
        $fixedCount = 0;
        $errorCount = 0;
        
        foreach ($buggySubscriptions as $subscription) {
            try {
                $plan = $subscription->plan;
                
                if (!$plan || !$plan->duration_in_days) {
                    $errorCount++;
                    continue;
                }
                
                $correctEndDate = now()->copy()->addDays($plan->duration_in_days);
                
                if ($correctEndDate <= now()) {
                    $errorCount++;
                    continue;
                }
                
                $oldEndDate = $subscription->ends_at;
                
                $subscription->update([
                    'status' => 'ACTIVE', // Change from EXPIRED to ACTIVE
                    'ends_at' => $correctEndDate,
                    'metadata' => array_merge($subscription->metadata ?? [], [
                        'duration_bug_fixed' => true,
                        'fixed_at' => now()->toDateTimeString(),
                        'original_end_date' => $oldEndDate,
                        'corrected_end_date' => $correctEndDate,
                        'original_status' => $subscription->getOriginal('status'),
                        'fix_reason' => 'duration_days_vs_duration_in_days_bug',
                        'fixed_by_admin' => auth()->id(),
                    ])
                ]);
                
                $fixedCount++;
                
            } catch (\Exception $e) {
                $errorCount++;
                Log::error('Failed to fix subscription in bulk', [
                    'subscription_id' => $subscription->id,
                    'error' => $e->getMessage()
                ]);
            }
        }
        
        return back()->with('success', "Bulk fix completed! Fixed: {$fixedCount} subscriptions, Errors: {$errorCount}");
    }
}
