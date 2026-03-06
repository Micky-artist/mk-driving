<!DOCTYPE html>
<html lang="{{ $locale }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('auth.welcome_email.title', [], $locale) }} - {{ config('app.name') }}</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
            margin: 0;
            padding: 20px;
            background-color: #f8fafc;
            color: #1a202c;
            line-height: 1.6;
        }
        
        .container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            overflow: hidden;
        }
        
        .header {
            background-color: #2563eb;
            color: white;
            padding: 50px 30px;
            text-align: center;
        }
        
        .title {
            font-size: 24px;
            font-weight: 600;
            margin: 0;
            padding: 20px;
            background-color: #1d4ed8;
            border-radius: 6px;
            display: inline-block;
        }
        
        .content {
            padding: 50px 40px;
            text-align: center;
        }
        
        .welcome-message {
            background-color: #dbeafe;
            border-left: 4px solid #2563eb;
            padding: 30px;
            margin: 25px 0;
            text-align: left;
            border-radius: 0 4px 4px 0;
        }
        
        .button {
            display: inline-block;
            background-color: #2563eb;
            color: white;
            text-decoration: none;
            padding: 12px 24px;
            border-radius: 6px;
            font-weight: 500;
            font-size: 16px;
            margin: 20px 0;
        }
        
        .button:hover {
            background-color: #1d4ed8;
        }
        
        .footer {
            background-color: #dbeafe;
            padding: 30px 40px;
            text-align: center;
            font-size: 14px;
            color: #64748b;
            border-top: 1px solid #e5e7eb;
        }
        
        @media (max-width: 600px) {
            body {
                padding: 10px;
            }
            
            .header, .content, .footer {
                padding: 20px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1 class="title">{{ __('auth.welcome_email.title', [], $locale) }}</h1>
        </div>

        <div class="content">
            <div class="welcome-message">
                <p>{{ __('auth.welcome_email.body', [], $locale) }}</p>
                <p>{{ __('auth.welcome_email.get_started', [], $locale) }}</p>
            </div>
            
            <a href="{{ route('dashboard', ['locale' => $locale]) }}" class="button">
                {{ __('auth.welcome_email.get_started_button', [], $locale) }}
            </a>
        </div>

        <div class="footer">
            <p>{{ __('auth.welcome_email.questions', [], $locale) }}</p>
            <p>{{ __('auth.welcome_email.contact', ['email' => config('mail.support_email')], $locale) }}</p>
            <p>&copy; {{ date('Y') }} {{ config('app.name') }}. {{ __('All rights reserved.') }}</p>
        </div>
    </div>
</body>
</html>
