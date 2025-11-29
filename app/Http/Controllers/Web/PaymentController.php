<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;

class PaymentController extends Controller
{
    /**
     * Store a new payment request.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'plan_id' => 'required|exists:subscription_plans,id',
            'phone_number' => 'required|string|max:20',
            'amount' => 'required|numeric|min:0',
            'currency' => 'required|string|size:3',
        ]);

        // Create a unique reference for this payment
        $reference = 'PAY-' . strtoupper(Str::random(10));
        
        // Get the authenticated user
        $user = Auth::user();

        // Create the payment record
        $payment = Payment::create([
            'user_id' => $user->id,
            'plan_id' => $validated['plan_id'],
            'reference' => $reference,
            'phone_number' => $validated['phone_number'],
            'amount' => $validated['amount'],
            'currency' => $validated['currency'],
            'status' => 'pending',
            'payment_method' => 'mobile_money',
            'metadata' => [
                'requested_at' => now()->toDateTimeString(),
                'payment_method' => 'MTN Mobile Money',
                'momo_phone' => env('MOMO_PHONE_NUMBER', 'XXXXX'),
            ],
        ]);

        // In a real app, you might want to send a notification to the admin here
        
        return response()->json([
            'success' => true,
            'message' => 'Payment request received. Please complete the payment using the USSD code.',
            'payment' => [
                'reference' => $payment->reference,
                'amount' => $payment->amount,
                'currency' => $payment->currency,
                'status' => $payment->status,
            ]
        ]);
    }
}
