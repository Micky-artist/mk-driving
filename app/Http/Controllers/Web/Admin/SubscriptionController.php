<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use App\Models\Subscription;
use App\Enums\SubscriptionStatus;
use App\Enums\PaymentStatus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SubscriptionController extends Controller
{
    /**
     * Display a listing of subscriptions for approval.
     */
    /**
     * Display a listing of subscriptions with filters and search.
     */
    public function index(Request $request)
    {
        $status = $request->query('status', 'pending');
        $search = $request->query('search');
        $perPage = 10;
        
        // Get status counts for the filter tabs
        $statusCounts = [
            'all' => Subscription::count(),
            SubscriptionStatus::PENDING->value => Subscription::where('status', SubscriptionStatus::PENDING->value)->count(),
            SubscriptionStatus::ACTIVE->value => Subscription::where('status', SubscriptionStatus::ACTIVE->value)->count(),
            SubscriptionStatus::EXPIRED->value => Subscription::where('status', SubscriptionStatus::EXPIRED->value)->count(),
            SubscriptionStatus::CANCELLED->value => Subscription::where('status', SubscriptionStatus::CANCELLED->value)->count(),
        ];
        
        $query = Subscription::with(['user', 'subscriptionPlan'])
            ->when($status !== 'all', function($q) use ($status) {
                return $q->where('status', $status);
            })
            ->when($search, function($q) use ($search) {
                return $q->where(function($query) use ($search) {
                    $query->whereHas('user', function($q) use ($search) {
                        $q->where('first_name', 'like', "%{$search}%")
                          ->orWhere('last_name', 'like', "%{$search}%")
                          ->orWhere('email', 'like', "%{$search}%");
                    })
                    ->orWhere('payment_reference', 'like', "%{$search}%")
                    ->orWhere('amount', 'like', "%{$search}%")
                    ->orWhereHas('subscriptionPlan', function($q) use ($search) {
                        $q->where('name->en', 'like', "%{$search}%")
                          ->orWhere('name->rw', 'like', "%{$search}%");
                    });
                });
            })
            ->latest();
        
        $subscriptions = $query->paginate($perPage);
        
        // Get stats for the stats cards
        $stats = $this->getSubscriptionStats();
        
        if ($request->ajax()) {
            return response()->json([
                'html' => view('admin.subscriptions.partials.subscription-list', [
                    'subscriptions' => $subscriptions,
                    'status' => $status,
                ])->render(),
                'pagination' => (string) $subscriptions->links(),
                'stats' => $stats
            ]);
        }
        
        return view('admin.subscriptions.index', [
            'subscriptions' => $subscriptions,
            'status' => $status,
            'search' => $search,
            'statusCounts' => $statusCounts,
            'stats' => $stats,
            'statuses' => SubscriptionStatus::cases(),
        ]);
    }
    
    /**
     * Show the subscription details.
     */
    public function show(Subscription $subscription)
    {
        $subscription->load(['user', 'subscriptionPlan', 'cancelledBy']);
        
        return view('admin.subscriptions.show', compact('subscription'));
    }
    
    /**
     * Approve a subscription.
     */
    public function approve(Request $request, Subscription $subscription)
    {
        if ($subscription->status !== SubscriptionStatus::PENDING->value) {
            return back()->with('error', 'Only pending subscriptions can be approved.');
        }
        
        DB::beginTransaction();
        
        try {
            // Update subscription status
            $subscription->update([
                'status' => SubscriptionStatus::ACTIVE->value,
                'payment_status' => PaymentStatus::COMPLETED->value,
                'admin_notes' => $request->input('notes'),
                'start_date' => now(),
                'end_date' => now()->addDays($subscription->subscriptionPlan->duration),
            ]);
            
            // If there are any other pending subscriptions for this user, cancel them
            Subscription::where('user_id', $subscription->user_id)
                ->where('id', '!=', $subscription->id)
                ->where('status', SubscriptionStatus::PENDING->value)
                ->update([
                    'status' => SubscriptionStatus::CANCELLED->value,
                    'cancelled_by' => auth()->id(),
                    'cancelled_at' => now(),
                    'admin_notes' => 'Cancelled due to approval of another subscription.'
                ]);
            
            DB::commit();
            
            // TODO: Send notification to user
            
            return redirect()->route('admin.subscriptions.index')
                ->with('success', 'Subscription approved successfully.');
                
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to approve subscription: ' . $e->getMessage());
        }
    }
    
    /**
     * Reject a subscription.
     */
    public function reject(Request $request, Subscription $subscription)
    {
        $request->validate([
            'reason' => 'required|string|max:1000',
        ]);
        
        if ($subscription->status !== SubscriptionStatus::PENDING->value) {
            return back()->with('error', 'Only pending subscriptions can be rejected.');
        }
        
        $subscription->update([
            'status' => SubscriptionStatus::CANCELLED->value,
            'payment_status' => PaymentStatus::FAILED->value,
            'admin_notes' => $request->input('reason'),
            'cancelled_by' => auth()->id(),
            'cancelled_at' => now(),
        ]);
        
        // TODO: Send notification to user
        
        return redirect()->route('admin.subscriptions.index')
            ->with('success', 'Subscription has been rejected.');
    }
    
    /**
     * Cancel a subscription.
     */
    public function cancel(Request $request, Subscription $subscription)
    {
        $request->validate([
            'reason' => 'required|string|max:1000',
        ]);
        
        if ($subscription->status !== SubscriptionStatus::ACTIVE->value) {
            return back()->with('error', 'Only active subscriptions can be cancelled.');
        }
        
        $subscription->update([
            'status' => SubscriptionStatus::CANCELLED->value,
            'admin_notes' => $request->input('reason'),
            'cancelled_by' => auth()->id(),
            'cancelled_at' => now(),
        ]);
        
        // TODO: Send notification to user
        
        return redirect()->route('admin.subscriptions.index')
            ->with('success', 'Subscription has been cancelled.');
    }
    
    /**
     * Get subscription statistics.
     */
    public function stats()
    {
        return response()->json($this->getSubscriptionStats());
    }
    
    /**
     * Get subscription statistics.
     * 
     * @return array
     */
    protected function getSubscriptionStats()
    {
        $total = Subscription::count();
        $pending = Subscription::where('status', SubscriptionStatus::PENDING->value)->count();
        $active = Subscription::where('status', SubscriptionStatus::ACTIVE->value)->count();
        $expired = Subscription::where('status', SubscriptionStatus::EXPIRED->value)->count();
        $cancelled = Subscription::where('status', SubscriptionStatus::CANCELLED->value)->count();
        $revenue = Subscription::where('payment_status', PaymentStatus::COMPLETED->value)
            ->sum('amount');
            
        $recentSubscriptions = Subscription::with(['user', 'subscriptionPlan'])
            ->latest()
            ->take(5)
            ->get();
            
        return [
            'total' => $total,
            'pending' => $pending,
            'active' => $active,
            'expired' => $expired,
            'cancelled' => $cancelled,
            'revenue' => $revenue,
            'recent_subscriptions' => $recentSubscriptions,
            'active_percentage' => $total > 0 ? round(($active / $total) * 100) : 0,
            'revenue_formatted' => number_format($revenue, 2)
        ];
    }
}
