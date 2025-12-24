<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
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
        $this->targetEnvironment = config('services.mtn_momo.target_environment', 'sandbox');
        $this->callbackUrl = config('services.mtn_momo.callback_url');
        
        // Collection API uses primary key as subscription key
        $this->apiKey = config('services.mtn_momo.collection.primary_key');
        $this->subscriptionKey = config('services.mtn_momo.collection.primary_key');
        
        // Use Rwanda-specific API endpoints for production
        if ($this->targetEnvironment === 'production') {
            $this->baseUrl = 'https://momoapi.mtn.co.rw';
        }
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
        
        // Use EUR for sandbox environment as per MTN documentation
        $currency = ($this->targetEnvironment === 'sandbox') ? 'EUR' : 'RWF';
        
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
            'currency' => $currency,
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
        
        if ($response->status() === 202) {
            return [
                'success' => true,
                'reference_id' => $referenceId,
                'status' => 'PENDING',
            ];
        }
        
        // Log unexpected responses for debugging
        Log::warning('MTN MoMo Unexpected Response', [
            'expected_status' => 202,
            'actual_status' => $response->status(),
            'response_body' => $response->json(),
        ]);
        
        throw new \Exception('Failed to initiate payment: Expected 202 Accepted, got ' . $response->status() . ' - ' . $response->body());
    }
    
    /**
     * Get payment status
     */
    public function getPaymentStatus($referenceId)
    {
        $url = "{$this->baseUrl}/collection/v1_0/requesttopay/{$referenceId}";
        
        // Get access token
        $token = $this->getAccessToken();
        
        // Prepare request headers - include subscription key like the POST request
        $headers = [
            'Authorization' => 'Bearer ' . $token,
            'X-Target-Environment' => $this->targetEnvironment,
            'Ocp-Apim-Subscription-Key' => $this->subscriptionKey, // This was missing!
        ];
        
        // Make the API request
        $response = Http::withHeaders($headers)
            ->withOptions(['verify' => false]) // Only for testing, remove in production
            ->get($url);
        
        // Log the request and response for debugging
        Log::info('MTN MoMo Payment Status', [
            'reference_id' => $referenceId,
            'url' => $url,
            'response_status' => $response->status(),
            'response_body' => $response->json(),
        ]);
        
        if ($response->successful()) {
            $data = $response->json();
            $status = $data['status'] ?? 'PENDING';
            
            // Map MTN status to our internal status
            return $this->mapMtnStatus($status);
        }
        
        // Handle error responses
        if ($response->status() === 404) {
            return 'NOT_FOUND';
        }
        
        if ($response->status() >= 400) {
            $errorData = $response->json();
            $errorCode = $errorData['code'] ?? 'UNKNOWN_ERROR';
            
            // Map MTN error codes to our status
            return $this->mapMtnError($errorCode);
        }
        
        throw new \Exception('Failed to get payment status: ' . $response->body());
    }
    
    /**
     * Map MTN API status to our internal status
     */
    private function mapMtnStatus($status)
    {
        $statusMap = [
            'PENDING' => 'pending',
            'SUCCESSFUL' => 'successful', 
            'FAILED' => 'failed',
            'TIMEOUT' => 'expired',
            'EXPIRED' => 'expired',
            'CANCELLED' => 'cancelled',
            'REJECTED' => 'rejected',
        ];
        
        return $statusMap[$status] ?? 'pending';
    }
    
    /**
     * Map MTN API error codes to our internal status
     */
    private function mapMtnError($errorCode)
    {
        $errorMap = [
            'PAYEE_NOT_FOUND' => 'failed',
            'PAYER_NOT_FOUND' => 'not_found',
            'NOT_ALLOWED' => 'failed',
            'NOT_ALLOWED_TARGET_ENVIRONMENT' => 'failed',
            'INVALID_CALLBACK_URL_HOST' => 'failed',
            'INVALID_CURRENCY' => 'failed',
            'SERVICE_UNAVAILABLE' => 'error',
            'INTERNAL_PROCESSING_ERROR' => 'error',
            'NOT_ENOUGH_FUNDS' => 'failed',
            'PAYER_LIMIT_REACHED' => 'failed',
            'PAYEE_NOT_ALLOWED_TO_RECEIVE' => 'failed',
            'PAYMENT_NOT_APPROVED' => 'rejected',
            'RESOURCE_NOT_FOUND' => 'not_found',
            'APPROVAL_REJECTED' => 'rejected',
            'EXPIRED' => 'expired',
            'TRANSACTION_CANCELED' => 'cancelled',
            'RESOURCE_ALREADY_EXIST' => 'error',
        ];
        
        return $errorMap[$errorCode] ?? 'error';
    }
    
    /**
     * Get access token for API authentication
     */
    protected function getAccessToken()
    {
        // Cache the token for better performance
        $cacheKey = 'mtn_momo_access_token_' . $this->targetEnvironment;
        
        if (Cache::has($cacheKey)) {
            return Cache::get($cacheKey);
        }
        
        // Create access token using Basic Auth
        $url = "{$this->baseUrl}/collection/token/";
        
        // We need API User ID and API Secret for Basic Auth
        $apiUserId = config('services.mtn_momo.collection.api_user_id');
        $apiSecret = config('services.mtn_momo.collection.api_secret');
        
        if (!$apiUserId || !$apiSecret) {
            throw new \Exception('API User ID and API Secret are required for token generation');
        }
        
        $response = Http::withHeaders([
            'Authorization' => 'Basic ' . base64_encode("{$apiUserId}:{$apiSecret}"),
            'Ocp-Apim-Subscription-Key' => $this->subscriptionKey,
        ])
        ->withOptions(['verify' => false]) // Only for testing, remove in production
        ->post($url);
        
        Log::info('MTN MoMo Token Request', [
            'url' => $url,
            'response_status' => $response->status(),
            'response_body' => $response->json(),
        ]);
        
        if ($response->successful()) {
            $data = $response->json();
            $token = $data['access_token'] ?? null;
            $expiresIn = $data['expires_in'] ?? 3600;
            
            if ($token) {
                // Cache the token for slightly less than its expiry time
                Cache::put($cacheKey, $token, $expiresIn - 60);
                return $token;
            }
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
