<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Services\MTNMomoService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class PaymentController extends Controller
{
    protected $momoService;

    public function __construct(MTNMomoService $momoService)
    {
        $this->momoService = $momoService;
    }

    /**
     * Store a new payment request using MTN Mobile Money API.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'plan_id' => 'required|exists:subscription_plans,id',
            'phone_number' => 'required|string|regex:/^[0-9]{9,12}$/',
            'amount' => 'required|numeric|min:100',
            'currency' => 'required|string|in:RWF',
        ]);

        try {
            // Format phone number to MTN format
            $phoneNumber = $this->formatPhoneNumber($validated['phone_number']);
            $amount = (string) $validated['amount'];
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

            // Get the authenticated user
            $user = Auth::user();

            // Create the payment record
            $payment = Payment::create([
                'user_id' => $user->id,
                'plan_id' => $validated['plan_id'],
                'reference' => $externalId,
                'phone_number' => $phoneNumber,
                'amount' => $amount,
                'currency' => $validated['currency'],
                'status' => 'PENDING',
                'payment_method' => 'MTN_MOBILE_MONEY',
                'metadata' => [
                    'api_response' => $response,
                    'requested_at' => now()->toDateTimeString(),
                    'payment_method' => 'MTN Mobile Money',
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
     * Format phone number to MTN format
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
        
        throw new \Exception('Invalid phone number format. Use Rwanda MTN numbers (07x xxx xxxx)');
    }
}
