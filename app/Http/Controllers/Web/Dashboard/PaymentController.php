<?php

namespace App\Http\Controllers\Web\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Models\SubscriptionPlan;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PaymentController extends Controller
{
    /**
     * Display a listing of payments.
     */
    public function index()
    {
        $this->authorize('viewAny', Payment::class);
        
        $payments = Payment::with(['user', 'plan'])
            ->latest()
            ->paginate(15);
            
        return view('dashboard.payments.index', compact('payments'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified payment.
     */
    public function show(Payment $payment)
    {
        $this->authorize('view', $payment);
        
        $payment->load(['user', 'plan']);
        
        return view('dashboard.payments.show', compact('payment'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Payment $payment)
    {
        //
    }

    /**
     * Confirm a payment and activate the subscription.
     */
    public function confirm(Request $request, Payment $payment)
    {
        $this->authorize('update', $payment);
        
        $validated = $request->validate([
            'notes' => 'nullable|string|max:1000',
        ]);
        
        if ($payment->status !== 'completed') {
            try {
                DB::beginTransaction();
                
                // Update payment status
                $payment->update([
                    'status' => 'completed',
                    'completed_at' => now(),
                    'metadata' => array_merge($payment->metadata ?? [], [
                        'confirmed_by' => auth()->id(),
                        'confirmed_at' => now()->toDateTimeString(),
                        'admin_notes' => $validated['notes'] ?? null,
                    ]),
                ]);
                
                // Activate the subscription
                $endDate = now()->addDays($payment->plan->duration);
                
                $payment->user->subscriptions()->create([
                    'subscription_plan_id' => $payment->plan_id,
                    'starts_at' => now(),
                    'ends_at' => $endDate,
                    'status' => 'active',
                    'payment_id' => $payment->id,
                ]);
                
                // Update user's subscription plan
                $payment->user->update([
                    'subscription_plan_id' => $payment->plan_id,
                    'subscription_ends_at' => $endDate,
                ]);
                
                // TODO: Send notification to user
                
                DB::commit();
                
                return redirect()
                    ->route('dashboard.payments.show', $payment)
                    ->with('success', 'Payment confirmed and subscription activated successfully.');
                    
            } catch (\Exception $e) {
                DB::rollBack();
                Log::error('Failed to confirm payment: ' . $e->getMessage(), [
                    'payment_id' => $payment->id,
                    'error' => $e->getTraceAsString(),
                ]);
                
                return back()->with('error', 'Failed to confirm payment. Please try again.');
            }
        }
        
        return back()->with('error', 'This payment has already been processed.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Payment $payment)
    {
        //
    }
}
