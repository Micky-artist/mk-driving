<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'postmark' => [
        'key' => env('POSTMARK_API_KEY'),
    ],

    'resend' => [
        'key' => env('RESEND_API_KEY'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

    'mailtrap' => [
    'smtp' => [
        'host' => env('MAIL_HOST', 'sandbox.smtp.mailtrap.io'),
        'port' => env('MAIL_PORT', 2525),
        'username' => env('MAIL_USERNAME'),
        'password' => env('MAIL_PASSWORD'),
        'encryption' => env('MAIL_ENCRYPTION', 'tls'),
    ],
],

    /*
    |--------------------------------------------------------------------------
    | MTN Mobile Money Configuration
    |--------------------------------------------------------------------------
    |
    | This configuration is for MTN Mobile Money API integration.
    | Make sure to set the correct environment variables in your .env file.
    |
    */
    'google' => [
        'client_id' => env('GOOGLE_CLIENT_ID'),
        'client_secret' => env('GOOGLE_CLIENT_SECRET'),
        'redirect' => env('GOOGLE_REDIRECT_URI', '/auth/google/callback'),
    ],

    'mtn_momo' => [
        // Base URL for the MTN MoMo API (sandbox or production)
        'base_url' => env('MTN_MOMO_BASE_URL', 'https://sandbox.momodeveloper.mtn.com'),
        
        // API credentials
        'api_key' => env('MTN_MOMO_API_KEY'),
        'api_user_id' => env('MTN_MOMO_API_USER_ID'),
        'api_secret' => env('MTN_MOMO_API_SECRET'),
        'subscription_key' => env('MTN_MOMO_SUBSCRIPTION_KEY'),
        
        // Target environment (sandbox or production)
        'target_environment' => env('MTN_MOMO_TARGET_ENV', 'sandbox'),
        
        // Webhook URL for payment notifications (must be HTTPS in production)
        'callback_url' => env('MTN_MOMO_CALLBACK_URL', env('APP_URL') . '/api/payments/webhook/mtn'),
        
        // Collection API settings
        'collection' => [
            'primary_key' => env('MTN_MOMO_COLLECTION_PRIMARY_KEY'),
            'secondary_key' => env('MTN_MOMO_COLLECTION_SECONDARY_KEY'),
            'api_user_id' => env('MTN_MOMO_COLLECTION_API_USER_ID'),
            'api_secret' => env('MTN_MOMO_COLLECTION_API_SECRET'),
        ],
        
        // Disbursement API settings (if needed in the future)
        'disbursement' => [
            'primary_key' => env('MTN_MOMO_DISBURSEMENT_PRIMARY_KEY'),
            'secondary_key' => env('MTN_MOMO_DISBURSEMENT_SECONDARY_KEY'),
            'api_user_id' => env('MTN_MOMO_DISBURSEMENT_USER_ID'),
            'api_secret' => env('MTN_MOMO_DISBURSEMENT_API_SECRET'),
        ],
    ],

];
