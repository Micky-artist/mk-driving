<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\MTNMomoService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class PaymentController extends Controller
{
    protected $momoService;

    public function __construct(MTNMomoService $momoService)
    {
        $this->momoService = $momoService;
    }

    /**
     * Initiate MTN Mobile Money payment
     */
    public function initiatePayment(Request $request)
    {
        $request->validate([
            'plan_id' => 'required|exists:subscription_plans,id',
            'phone_number' => 'required|string|regex:/^0[0-9]{9}$/',
            'amount' => 'required|numeric|min:100', // Minimum 100 RWF
            'currency' => 'required|string|in:RWF',
        ]);

        try {
            // Format phone number to MTN format (remove leading 0 and add country code)
            $phoneNumber = '250' . ltrim($request->phone_number, '0');
            $amount = (string) $request->amount;
            $externalId = (string) Str::uuid();
            $payerMessage = 'Subscription payment';
            $payeeNote = 'Thank you for subscribing!';

            // Call MTN MoMo API to request payment
            $response = $this->momoService->requestToPay(
                $externalId,
                $phoneNumber,
                $amount,
                $payerMessage,
                $payeeNote
            );

            // Save payment record to database
            $payment = \App\Models\Payment::create([
                'user_id' => $request->user()->id,
                'plan_id' => $request->plan_id,
                'reference' => $externalId,
                'phone_number' => $phoneNumber,
                'amount' => $amount,
                'currency' => $request->currency,
                'status' => 'PENDING',
                'payment_method' => 'MTN_MOBILE_MONEY',
                'metadata' => [
                    'api_response' => $response,
                    'request_data' => $request->all(),
                ],
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Payment request sent. Please check your phone to complete the transaction.',
                'payment_reference' => $externalId,
                'payment' => $payment,
            ]);

        } catch (\Exception $e) {
            Log::error('Payment initiation failed: ' . $e->getMessage(), [
                'exception' => $e,
                'request' => $request->all(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to initiate payment. Please try again.',
                'error' => config('app.debug') ? $e->getMessage() : null,
            ], 500);
        }
    }

    /**
     * Check payment status
     */
    public function checkStatus($reference)
    {
        try {
            // Find payment record
            $payment = \App\Models\Payment::where('reference', $reference)->firstOrFail();
            
            // If payment is already completed, return the status
            if (in_array($payment->status, ['SUCCESSFUL', 'FAILED', 'CANCELLED'])) {
                return response()->json([
                    'status' => strtolower($payment->status),
                    'payment' => $payment,
                ]);
            }

            // Check status from MTN MoMo
            $status = $this->momoService->getPaymentStatus($reference);
            
            // Update payment status
            $payment->update([
                'status' => strtoupper($status),
                'completed_at' => now(),
            ]);

            // If payment is successful, activate subscription
            if ($status === 'SUCCESSFUL') {
                $this->activateSubscription($payment);
            }

            return response()->json([
                'status' => strtolower($status),
                'payment' => $payment,
            ]);

        } catch (\Exception $e) {
            Log::error('Payment status check failed: ' . $e->getMessage(), [
                'reference' => $reference,
                'exception' => $e,
            ]);

            return response()->json([
                'status' => 'error',
                'message' => 'Failed to check payment status',
                'error' => config('app.debug') ? $e->getMessage() : null,
            ], 500);
        }
    }

    /**
     * Handle MTN MoMo webhook notifications
     */
    public function handleWebhook(Request $request)
    {
        $data = $request->all();
        Log::info('MTN MoMo Webhook received:', $data);

        try {
            // Verify the webhook signature (implement this based on MTN's security requirements)
            // $this->verifyWebhookSignature($request);

            $reference = $data['externalId'] ?? null;
            if (!$reference) {
                throw new \Exception('No reference ID in webhook data');
            }

            // Find payment record
            $payment = \App\Models\Payment::where('reference', $reference)->firstOrFail();
            
            // Update payment status
            $status = strtoupper($data['status'] ?? 'PENDING');
            $payment->update([
                'status' => $status,
                'completed_at' => now(),
                'metadata' => array_merge($payment->metadata ?? [], [
                    'webhook_data' => $data,
                    'webhook_received_at' => now()->toDateTimeString(),
                ]),
            ]);

            // If payment is successful, activate subscription
            if ($status === 'SUCCESSFUL') {
                $this->activateSubscription($payment);
            }

            return response()->json(['success' => true]);

        } catch (\Exception $e) {
            Log::error('Webhook processing failed: ' . $e->getMessage(), [
                'webhook_data' => $data,
                'exception' => $e,
            ]);

            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Activate user subscription after successful payment
     */
    protected function activateSubscription($payment)
    {
        try {
            $user = $payment->user;
            $plan = $payment->plan;
            
            // Calculate subscription end date (1 month from now)
            $endDate = now()->addMonth();
            
            // Create or update user subscription
            $user->subscription()->updateOrCreate(
                ['user_id' => $user->id],
                [
                    'plan_id' => $plan->id,
                    'starts_at' => now(),
                    'ends_at' => $endDate,
                    'status' => 'active',
                    'payment_method' => 'MTN_MOBILE_MONEY',
                ]
            );
            
            // Update payment record with subscription details
            $payment->update([
                'status' => 'SUCCESSFUL',
                'completed_at' => now(),
            ]);
            
            // Send confirmation email (you can implement this)
            // $user->notify(new SubscriptionActivated($payment));
            
            Log::info('Subscription activated', [
                'user_id' => $user->id,
                'plan_id' => $plan->id,
                'payment_id' => $payment->id,
            ]);
            
        } catch (\Exception $e) {
            Log::error('Failed to activate subscription: ' . $e->getMessage(), [
                'payment_id' => $payment->id,
                'exception' => $e,
            ]);
            
            // Even if subscription activation fails, we still consider the payment successful
            // but log the error for manual intervention
            $payment->update([
                'metadata' => array_merge($payment->metadata ?? [], [
                    'subscription_activation_error' => $e->getMessage(),
                ]),
            ]);
            
            throw $e; // Re-throw to be handled by the caller
        }
    }
}
