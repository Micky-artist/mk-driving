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
            'phone_number' => 'required|string|regex:/^[0-9]{9,12}$/', // Accept 9-12 digit numbers
            'amount' => 'required|numeric|min:100', // Minimum 100 RWF
            'currency' => 'required|string|in:RWF',
        ]);

        try {
            // Format phone number using the same logic as the web controller
            $phoneNumber = $this->formatPhoneNumber($request->phone_number);
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
     * Format phone number to MTN format (supports both Rwanda numbers and sandbox test numbers)
     */
    private function formatPhoneNumber($phone)
    {
        // Remove all non-digit characters
        $cleaned = preg_replace('/\D/', '', $phone);
        
        // Allow MTN sandbox test numbers (46733123450-46733123461)
        if (strlen($cleaned) === 11 && preg_match('/^467331234[5-6][0-9]$/', $cleaned)) {
            return $cleaned; // Return test number as-is
        }
        
        // Check if it's a 9-digit number starting with 72, 73, 78, or 79
        if (strlen($cleaned) === 9 && preg_match('/^7[2389]\d{7}$/', $cleaned)) {
            return '250' . $cleaned; // Convert to 12-digit format
        }
        
        // Check if it's a 12-digit number starting with 25072, 25073, 25078, or 25079
        if (strlen($cleaned) === 12 && preg_match('/^2507[2389]\d{7}$/', $cleaned)) {
            return $cleaned; // Already in correct format
        }
        
        // Check if it's a 10-digit number starting with 07 (Rwanda format)
        if (strlen($cleaned) === 10 && preg_match('/^07[2389]\d{7}$/', $cleaned)) {
            return '250' . substr($cleaned, 1); // Remove 0, add 250 prefix
        }
        
        throw new \Exception('Invalid phone number format. Use Rwanda MTN numbers (07x xxx xxxx) or sandbox test numbers.');
    }

    /**
     * Check payment status with real-time polling support
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
                    'completed' => true,
                ]);
            }

            // Check status from MTN MoMo
            $status = $this->momoService->getPaymentStatus($reference);
            
            // Update payment status
            $payment->update([
                'status' => strtoupper($status),
                'completed_at' => in_array(strtoupper($status), ['SUCCESSFUL', 'FAILED', 'CANCELLED']) ? now() : null,
            ]);

            // If payment is successful, activate subscription
            if ($status === 'SUCCESSFUL') {
                $this->activateSubscription($payment);
            }

            return response()->json([
                'status' => strtolower($status),
                'payment' => $payment,
                'completed' => in_array(strtoupper($status), ['SUCCESSFUL', 'FAILED', 'CANCELLED']),
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
            // Validate webhook signature for security
            $this->momoService->validateWebhookSignature($request);

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
                'completed_at' => in_array($status, ['SUCCESSFUL', 'FAILED', 'CANCELLED']) ? now() : null,
                'metadata' => array_merge($payment->metadata ?? [], [
                    'webhook_data' => $data,
                    'webhook_received_at' => now()->toDateTimeString(),
                ]),
            ]);

            // If payment is successful, activate subscription immediately
            if ($status === 'SUCCESSFUL') {
                $this->activateSubscription($payment);
                
                // Send real-time notification to frontend if needed
                // You could implement WebSocket or Server-Sent Events here
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
