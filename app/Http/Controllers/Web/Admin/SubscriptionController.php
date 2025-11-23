<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use App\Models\Subscription;
use Illuminate\Http\Request;

class SubscriptionController extends Controller
{
    /**
     * Display a listing of subscription requests.
     */
    public function index()
    {
        $subscriptions = Subscription::with(['user', 'plan'])
            ->where('status', 'pending')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('admin.subscriptions.index', compact('subscriptions'));
    }

    /**
     * Approve a subscription request.
     */
    public function approve(Subscription $subscription)
    {
        $subscription->update([
            'status' => 'active',
            'approved_at' => now(),
        ]);

        return back()->with('success', 'Subscription approved successfully.');
    }

    /**
     * Reject a subscription request.
     */
    public function reject(Subscription $subscription)
    {
        $subscription->update([
            'status' => 'rejected',
            'rejected_at' => now(),
        ]);

        return back()->with('success', 'Subscription rejected successfully.');
    }
}
