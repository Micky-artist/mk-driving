<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class MTNMomoService
{
    protected $baseUrl;
    protected $apiKey;
    protected $apiUserId;
    protected $apiSecret;
    protected $subscriptionKey;
    protected $targetEnvironment; // sandbox or production
    protected $callbackUrl;

    public function __construct()
    {
        $this->baseUrl = config('services.mtn_momo.base_url');
        $this->apiKey = config('services.mtn_momo.api_key');
        $this->apiUserId = config('services.mtn_momo.api_user_id');
        $this->apiSecret = config('services.mtn_momo.api_secret');
        $this->subscriptionKey = config('services.mtn_momo.subscription_key');
        $this->targetEnvironment = config('services.mtn_momo.target_environment', 'sandbox');
        $this->callbackUrl = config('services.mtn_momo.callback_url');
    }

    /**
     * Request payment from customer
     */
    public function requestToPay($externalId, $phoneNumber, $amount, $payerMessage, $payeeNote)
    {
        $url = "{$this->baseUrl}/collection/v1_0/requesttopay";
        
        // Generate a new access token
        $token = $this->getAccessToken();
        
        // Generate X-Reference-Id
        $referenceId = $externalId;
        
        // Prepare request headers
        $headers = [
            'Authorization' => 'Bearer ' . $token,
            'X-Reference-Id' => $referenceId,
            'X-Target-Environment' => $this->targetEnvironment,
            'Ocp-Apim-Subscription-Key' => $this->subscriptionKey,
            'Content-Type' => 'application/json',
        ];
        
        // Prepare request body
        $body = [
            'amount' => $amount,
            'currency' => 'EUR', // MTN MoMo uses EUR as the base currency
            'externalId' => $externalId,
            'payer' => [
                'partyIdType' => 'MSISDN',
                'partyId' => $phoneNumber,
            ],
            'payerMessage' => $payerMessage,
            'payeeNote' => $payeeNote,
        ];
        
        // Make the API request
        $response = Http::withHeaders($headers)
            ->withOptions(['verify' => false]) // Only for testing, remove in production
            ->post($url, $body);
        
        // Log the request and response for debugging
        Log::info('MTN MoMo Request to Pay', [
            'url' => $url,
            'headers' => $headers,
            'body' => $body,
            'response_status' => $response->status(),
            'response_body' => $response->json(),
        ]);
        
        if ($response->successful()) {
            return [
                'success' => true,
                'reference_id' => $referenceId,
                'status' => 'PENDING',
            ];
        }
        
        throw new \Exception('Failed to initiate payment: ' . $response->body());
    }
    
    /**
     * Get payment status
     */
    public function getPaymentStatus($referenceId)
    {
        $url = "{$this->baseUrl}/collection/v1_0/requesttopay/{$referenceId}";
        
        // Get access token
        $token = $this->getAccessToken();
        
        // Prepare request headers
        $headers = [
            'Authorization' => 'Bearer ' . $token,
            'Ocp-Apim-Subscription-Key' => $this->subscriptionKey,
            'X-Target-Environment' => $this->targetEnvironment,
        ];
        
        // Make the API request
        $response = Http::withHeaders($headers)
            ->withOptions(['verify' => false]) // Only for testing, remove in production
            ->get($url);
        
        // Log the request and response for debugging
        Log::info('MTN MoMo Payment Status', [
            'reference_id' => $referenceId,
            'response_status' => $response->status(),
            'response_body' => $response->json(),
        ]);
        
        if ($response->successful()) {
            $data = $response->json();
            return $data['status'] ?? 'PENDING';
        }
        
        throw new \Exception('Failed to get payment status: ' . $response->body());
    }
    
    /**
     * Get access token for API authentication
     */
    protected function getAccessToken()
    {
        // In a production environment, you should cache this token and reuse it until it expires
        $url = "{$this->baseUrl}/collection/token/";
        
        $response = Http::withHeaders([
            'Ocp-Apim-Subscription-Key' => $this->subscriptionKey,
            'Authorization' => 'Basic ' . base64_encode("{$this->apiUserId}:{$this->apiSecret}"),
        ])
        ->withOptions(['verify' => false]) // Only for testing, remove in production
        ->post($url);
        
        if ($response->successful()) {
            $data = $response->json();
            return $data['access_token'] ?? null;
        }
        
        throw new \Exception('Failed to get access token: ' . $response->body());
    }
    
    /**
     * Validate webhook signature (implement based on MTN's security requirements)
     */
    public function validateWebhookSignature($request)
    {
        // MTN MoMo typically sends a signature in the headers
        $signature = $request->header('X-Momo-Signature');
        
        if (!$signature) {
            throw new \Exception('No signature provided in webhook request');
        }
        
        // Get the request body
        $body = $request->getContent();
        
        // In a real implementation, you would validate the signature here
        // This is a placeholder for the actual validation logic
        $isValid = $this->verifySignature($signature, $body);
        
        if (!$isValid) {
            throw new \Exception('Invalid webhook signature');
        }
        
        return true;
    }
    
    /**
     * Verify the webhook signature (placeholder implementation)
     */
    protected function verifySignature($signature, $body)
    {
        // In a real implementation, you would verify the signature using your API secret
        // This is a simplified example - replace with actual signature verification
        $expectedSignature = hash_hmac('sha256', $body, $this->apiSecret);
        
        return hash_equals($expectedSignature, $signature);
    }
}
