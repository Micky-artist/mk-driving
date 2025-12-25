<!DOCTYPE html>
<html lang="rw">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('auth.reset_password_email.title', [], $locale) }} - {{ config('app.name') }}</title>
    <style>
        /* Brand Colors */
        :root {
            --brand-blue: #0369a1;
            --brand-dark-blue: #0e7490;
            --brand-orange: #ea580c;
            --brand-light-blue: #f8fafc;
            --text-dark: #1a202c;
            --text-light: #4a5568;
            --text-muted: #718096;
            --bg-light: #f4f4f4;
            --white: #ffffff;
        }
        
        /* Dark Mode Variables */
        @media (prefers-color-scheme: dark) {
            :root {
                --brand-light-blue: #1e293b;
                --text-dark: #f8fafc;
                --text-light: #e2e8f0;
                --text-muted: #94a3b8;
                --bg-light: #0f172a;
                --white: #1e293b;
            }
        }
        
        body {
            font-family: 'Nunito', Arial, sans-serif;
            margin: 0;
            padding: 20px;
            background-color: var(--bg-light);
            color: var(--text-dark);
            text-align: center;
        }
        
        .container {
            max-width: 600px;
            margin: 20px auto;
            background-color: var(--white);
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            padding: 0;
        }
        
        .header {
            text-align: center;
            padding: 40px 30px;
            background: linear-gradient(135deg, var(--brand-blue) 0%, var(--brand-dark-blue) 100%);
        }
        
        .logo-container {
            display: inline-block;
            margin-bottom: 25px;
        }
        
        .logo-wrapper {
            padding: 12px;
            background: var(--white);
            border-radius: 16px;
            transform: rotate(6deg);
            display: inline-block;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        
        .logo-inner {
            background: var(--white);
            padding: 8px;
            border-radius: 12px;
            transform: rotate(-6deg);
            display: inline-block;
        }
        
        .logo {
            height: 48px;
            width: auto;
        }
        
        .title {
            color: var(--white);
            font-size: 28px;
            font-weight: 700;
            margin: 0 0 15px 0;
            padding: 0 10px;
            line-height: 1.2;
        }
        
        .subtitle {
            color: rgba(255,255,255,0.95);
            font-size: 18px;
            margin: 0;
            padding: 0 10px;
            line-height: 1.4;
        }
        
        .content {
            padding: 40px 30px;
            text-align: center;
        }
        
        .message-box {
            background-color: var(--brand-light-blue);
            border-radius: 12px;
            padding: 35px 30px;
            margin-bottom: 30px;
            text-align: center;
        }
        
        .message-text {
            color: var(--brand-blue);
            font-size: 17px;
            line-height: 1.7;
            margin: 0 0 25px 0;
            font-weight: 500;
        }
        
        .button-container {
            text-align: center;
            margin: 30px 0;
            padding: 0 10px;
        }
        
        .reset-button {
            display: inline-block;
            background: linear-gradient(135deg, var(--brand-blue) 0%, var(--brand-dark-blue) 100%);
            color: var(--white);
            padding: 18px 40px;
            text-decoration: none;
            border-radius: 8px;
            font-size: 17px;
            font-weight: 600;
            text-align: center;
            box-shadow: 0 4px 6px rgba(3, 105, 161, 0.3);
            transition: all 0.3s ease;
        }
        
        .reset-button:hover {
            background: linear-gradient(135deg, var(--brand-dark-blue) 0%, var(--brand-blue) 100%);
            transform: translateY(-2px);
            box-shadow: 0 6px 12px rgba(3, 105, 161, 0.4);
        }
        
        .expiry-notice {
            color: var(--text-light);
            font-size: 15px;
            line-height: 1.6;
            margin: 25px 0 0 0;
            text-align: center;
            font-weight: 400;
            padding: 0 10px;
        }
        
        .footer-content {
            text-align: center;
            margin-bottom: 30px;
            padding: 0 20px;
        }
        
        .footer-text {
            color: var(--brand-blue);
            font-size: 15px;
            line-height: 1.6;
            margin: 0 0 20px 0;
            font-weight: 500;
        }
        
        .footer {
            background-color: var(--brand-light-blue);
            padding: 25px 30px;
            text-align: center;
            border-top: 1px solid rgba(3, 105, 161, 0.1);
        }
        
        .copyright {
            color: var(--text-muted);
            font-size: 13px;
            margin: 0;
            font-weight: 400;
            padding: 0 10px;
        }
        
        /* Mobile Responsive */
        @media (max-width: 600px) {
            body {
                padding: 10px;
            }
            
            .header {
                padding: 30px 20px;
            }
            
            .content {
                padding: 30px 20px;
            }
            
            .message-box {
                padding: 25px 20px;
            }
            
            .title {
                font-size: 24px;
            }
            
            .subtitle {
                font-size: 16px;
            }
            
            .reset-button {
                padding: 15px 30px;
                font-size: 16px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="logo-container">
                <div class="logo-wrapper">
                    <div class="logo-inner">
                        <img src="{{ asset('images/logo.png') }}" alt="{{ config('app.name') }}" class="logo">
                    </div>
                </div>
            </div>
            <h1 class="title">{{ __('auth.reset_password_email.title', [], $locale) }}</h1>
            <p class="subtitle">{{ __('auth.reset_password_email.subtitle', [], $locale) }}</p>
        </div>

        <div class="content">
            <div class="message-box">
                <p class="message-text">
                    {{ __('auth.reset_password_email.you_are_receiving', [], $locale) }}
                </p>
                
                <div class="button-container">
                    <a href="{{ route('password.reset', ['token' => $token, 'email' => $email, 'locale' => $locale]) }}" class="reset-button">
                        {{ __('auth.reset_password_email.reset_button', [], $locale) }}
                    </a>
                </div>
                
                <p class="expiry-notice">
                    {{ __('auth.reset_password_email.expiry_notice', ['count' => config('auth.passwords.'.config('auth.defaults.passwords').'.expire')], $locale) }}
                </p>
            </div>

            <div class="footer-content">
                <p class="footer-text">
                    {{ __('auth.reset_password_email.ignore_if_not_requested', [], $locale) }}
                </p>
                
                <p class="footer-text">
                    {{ __('auth.reset_password_email.contact_support', ['email' => config('mail.support_email')], $locale) }}
                </p>
            </div>
        </div>

        <div class="footer">
            <p class="copyright">
                &copy; {{ date('Y') }} {{ config('app.name') }}. {{ __('All rights reserved.') }}
            </p>
        </div>
    </div>
</body>
</html>
