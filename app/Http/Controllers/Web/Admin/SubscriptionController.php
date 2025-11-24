<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use App\Models\Subscription;
use App\Models\Payment;
use App\Models\SubscriptionPlan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SubscriptionController extends Controller
{
    /**
     * Display the subscriptions dashboard.
     */
    public function index()
    {
        // Get subscription stats for the dashboard
        $stats = [
            'total' => Subscription::count(),
            'active' => Subscription::where('status', 'active')
                ->where('ends_at', '>=', now())
                ->count(),
            'pending' => Subscription::where('status', 'pending')->count(),
            'expiring_soon' => Subscription::where('status', 'active')
                ->whereBetween('ends_at', [now(), now()->addDays(7)])
                ->count(),
        ];

        // Get recent subscriptions
        $recentSubscriptions = Subscription::with(['user', 'plan'])
            ->latest()
            ->take(5)
            ->get();

        return view('admin.subscriptions.dashboard', [
            'stats' => $stats,
            'recentSubscriptions' => $recentSubscriptions
        ]);
    }
    
    /**
     * Display a listing of pending subscription requests.
     */
    public function pending()
    {
        // Get pending subscriptions
        $subscriptions = Subscription::with(['user', 'plan'])
            ->where('status', 'pending')
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function($subscription) {
                $subscription->is_payment = false;
                return $subscription;
            });
            
        // Get pending payments that don't have a subscription yet
        $pendingPayments = Payment::with(['user', 'plan'])
            ->where('status', 'PENDING')
            ->whereDoesntHave('subscription')
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function($payment) {
                $payment->is_payment = true;
                $payment->payment_id = $payment->id;
                return $payment;
            });
            
        // Combine and paginate the results
        $allItems = $subscriptions->merge($pendingPayments)->sortByDesc('created_at');
        $perPage = 10;
        $currentPage = \Illuminate\Pagination\LengthAwarePaginator::resolveCurrentPage();
        $currentItems = $allItems->slice(($currentPage - 1) * $perPage, $perPage)->all();
        $items = new \Illuminate\Pagination\LengthAwarePaginator($currentItems, $allItems->count(), $perPage, $currentPage);

        return view('admin.subscriptions.pending', [
            'items' => $items,
            'hasPendingPayments' => $pendingPayments->isNotEmpty()
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
     * Display the specified subscription.
     *
     * @param  \App\Models\Subscription  $subscription
     * @return \Illuminate\View\View
     */
    public function show(Subscription $subscription)
    {
        $subscription->load(['user', 'plan', 'payment']);
        
        return view('admin.subscriptions.show', [
            'subscription' => $subscription
        ]);
    }

    /**
     * Approve a subscription request.
     */
    public function approve(Subscription $subscription)
    {
        DB::beginTransaction();
        
        try {
            // Update subscription status
            $subscription->update([
                'status' => 'active',
                'approved_at' => now(),
            ]);
            
            // If there's an associated payment, update it
            if ($subscription->payment) {
                $subscription->payment->update([
                    'status' => 'COMPLETED',
                    'completed_at' => now(),
                    'subscription_id' => $subscription->id
                ]);
            }
            
            DB::commit();
            return back()->with('success', 'Subscription approved successfully.');
            
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to approve subscription: ' . $e->getMessage());
        }
    }

    /**
     * Approve a payment and create a subscription.
     *
     * @param  int  $id  The payment ID
     * @return \Illuminate\Http\Response
     */
    public function approvePayment($locale, $id = null)
    {
        // Handle case where parameters might be swapped
        if (!is_numeric($locale) && in_array($locale, ['en', 'rw'])) {
            // If $locale is actually a locale and $id is not set, find the ID from route parameters
            if ($id === null) {
                $params = request()->route()->parameters();
                $id = $params['id'] ?? null;
            }
        } else {
            // If $locale is actually the ID and $id is the locale
            if (is_numeric($locale) && in_array($id, ['en', 'rw'])) {
                $temp = $id;
                $id = $locale;
                $locale = $temp;
            } elseif (is_numeric($locale)) {
                // If only one parameter was passed and it's numeric, it's the ID
                $id = $locale;
                $locale = app()->getLocale();
            }
        }
        
        // Log the payment ID and current request details
        \Log::debug('Payment approval request', [
            'payment_id' => $id,
            'locale' => $locale,
            'request_data' => request()->all(),
            'url' => request()->fullUrl(),
            'route_parameters' => request()->route()->parameters(),
            'all_payments' => Payment::pluck('id')->toArray()
        ]);
        
        try {
            $payment = Payment::findOrFail($id);
            
            // The locale is already set by the RouteServiceProvider
            
            $result = $this->processPaymentApproval($payment, 'COMPLETED');
            
            if (request()->wantsJson() || request()->ajax()) {
                return $result;
            }
            
            return $result
                ? back()->with('success', 'Payment approved and subscription created successfully.')
                : back()->with('error', 'Failed to approve payment.');
                
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            if (request()->wantsJson() || request()->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Payment not found.'
                ], 404);
            }
            return back()->with('error', 'Payment not found.');
        } catch (\Exception $e) {
            Log::error('Error approving payment: ' . $e->getMessage(), [
                'payment_id' => $id,
                'exception' => $e
            ]);
            
            if (request()->wantsJson() || request()->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'An error occurred while approving the payment.'
                ], 500);
            }
            
            return back()->with('error', 'An error occurred while approving the payment.');
        }
    }
    
    /**
     * Process payment approval.
     *
     * @param  \App\Models\Payment  $payment
     * @param  string  $status
     * @return bool|\Illuminate\Http\JsonResponse
     */
    protected function processPaymentApproval(Payment $payment, $status)
    {
        // Check if this payment already has a subscription
        if ($payment->subscription_id) {
            $message = 'This payment has already been processed.';
            return request()->wantsJson() 
                ? response()->json(['message' => $message], 400)
                : false;
        }
        
        DB::beginTransaction();
        
        try {
            // Get the plan
            $plan = $payment->plan;
            
            // Create a new subscription if approving
            if ($status === 'COMPLETED') {
                $subscription = Subscription::create([
                    'user_id' => $payment->user_id,
                    'plan_id' => $payment->plan_id,
                    'subscription_plan_id' => $payment->plan_id,
                    'status' => 'active',
                    'starts_at' => now(),
                    'ends_at' => now()->addMonths($plan->duration ?? 1),
                    'payment_id' => $payment->id,
                    'approved_at' => now(),
                ]);
                
                $payment->update([
                    'status' => $status,
                    'completed_at' => now(),
                    'subscription_id' => $subscription->id
                ]);
            } else {
                // For rejections
                $payment->update([
                    'status' => $status,
                    'completed_at' => now()
                ]);
            }
            
            DB::commit();
            
            if (request()->wantsJson() || request()->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => $status === 'COMPLETED' 
                        ? 'Payment approved successfully' 
                        : 'Payment rejected successfully',
                    'payment' => $payment->fresh()
                ]);
            }
            
            return true;
            
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error(($status === 'COMPLETED' ? 'Approve' : 'Reject') . ' payment failed: ' . $e->getMessage(), [
                'payment_id' => $payment->id,
                'status' => $status,
                'trace' => $e->getTraceAsString()
            ]);
            
            if (request()->wantsJson() || request()->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to process payment: ' . $e->getMessage()
                ], 500);
            }
            
            return false;
        }
    }
    
    /**
     * Reject a subscription request.
     */
    public function reject(Subscription $subscription)
    {
        DB::beginTransaction();
        
        try {
            // Update subscription status
            $subscription->update([
                'status' => 'rejected',
                'rejected_at' => now()
            ]);
            
            // Update payment status if exists
            if ($subscription->payment) {
                $subscription->payment->update([
                    'status' => 'REJECTED',
                    'completed_at' => now()
                ]);
            }
            
            DB::commit();
            return back()->with('success', 'Subscription rejected successfully.');
            
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to reject subscription: ' . $e->getMessage(), [
                'subscription_id' => $subscription->id,
                'trace' => $e->getTraceAsString()
            ]);
            return back()->with('error', 'Failed to reject subscription: ' . $e->getMessage());
        }
    }
    
    /**
     * Reject a payment.
     *
     * @param  int  $id  The payment ID
     * @return \Illuminate\Http\Response
     */
    public function rejectPayment($locale, $id = null)
    {
        // Handle case where parameters might be swapped
        if (!is_numeric($locale) && in_array($locale, ['en', 'rw'])) {
            // If $locale is actually a locale and $id is not set, find the ID from route parameters
            if ($id === null) {
                $params = request()->route()->parameters();
                $id = $params['id'] ?? null;
            }
        } else {
            // If $locale is actually the ID and $id is the locale
            if (is_numeric($locale) && in_array($id, ['en', 'rw'])) {
                $temp = $id;
                $id = $locale;
                $locale = $temp;
            } elseif (is_numeric($locale)) {
                // If only one parameter was passed and it's numeric, it's the ID
                $id = $locale;
                $locale = app()->getLocale();
            }
        }
        
        // Log the payment ID and current request details
        \Log::debug('Payment rejection request', [
            'payment_id' => $id,
            'locale' => $locale,
            'request_data' => request()->all(),
            'url' => request()->fullUrl(),
            'route_parameters' => request()->route()->parameters(),
            'all_payments' => Payment::pluck('id')->toArray()
        ]);
        
        try {
            $payment = Payment::findOrFail($id);
            
            // The locale is already set by the RouteServiceProvider
            
            $result = $this->processPaymentApproval($payment, 'REJECTED');
            
            if (request()->wantsJson() || request()->ajax()) {
                return $result;
            }
            
            return $result
                ? back()->with('success', 'Payment rejected successfully.')
                : back()->with('error', 'Failed to reject payment.');
                
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            if (request()->wantsJson() || request()->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Payment not found.'
                ], 404);
            }
            return back()->with('error', 'Payment not found.');
        } catch (\Exception $e) {
            Log::error('Error rejecting payment: ' . $e->getMessage(), [
                'payment_id' => $id,
                'exception' => $e
            ]);
            
            if (request()->wantsJson() || request()->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'An error occurred while rejecting the payment.'
                ], 500);
            }
            
            return back()->with('error', 'An error occurred while rejecting the payment.');
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
}
