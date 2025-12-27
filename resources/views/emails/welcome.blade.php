<!DOCTYPE html>
<html lang="{{ $locale }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('auth.welcome_email.title', [], $locale) }} - {{ config('app.name') }}</title>
    <style>
        /* Brand Colors */
        :root {
            --brand-blue: #2563eb;
            --brand-dark-blue: #1d4ed8;
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
        
        .logo {
            height: 48px;
            width: auto;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
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
        
        .welcome-message {
            background-color: var(--brand-light-blue);
            border-radius: 12px;
            padding: 35px 30px;
            margin-bottom: 30px;
            text-align: center;
        }
        
        .welcome-text {
            color: var(--brand-blue);
            font-size: 17px;
            line-height: 1.7;
            margin: 0 0 25px 0;
            font-weight: 500;
        }
        
        .user-name {
            font-weight: 700;
            color: var(--brand-dark-blue);
        }
        
        .actions-container {
            display: flex;
            flex-direction: column;
            gap: 20px;
            margin: 30px 0;
            padding: 0 10px;
        }
        
        .action-button {
            display: inline-block;
            color: var(--white);
            padding: 18px 40px;
            text-decoration: none;
            border-radius: 8px;
            font-size: 17px;
            font-weight: 600;
            text-align: center;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.2);
            transition: all 0.3s ease;
        }
        
        .verify-button {
            background: linear-gradient(135deg, var(--brand-blue) 0%, var(--brand-dark-blue) 100%);
            box-shadow: 0 4px 6px rgba(37, 99, 235, 0.3);
        }
        
        .verify-button:hover {
            background: linear-gradient(135deg, var(--brand-dark-blue) 0%, var(--brand-blue) 100%);
            transform: translateY(-2px);
            box-shadow: 0 6px 12px rgba(37, 99, 235, 0.4);
        }
        
        .quiz-button {
            background: linear-gradient(135deg, var(--brand-orange) 0%, #dc2626 100%);
            box-shadow: 0 4px 6px rgba(234, 88, 12, 0.3);
        }
        
        .quiz-button:hover {
            background: linear-gradient(135deg, #dc2626 0%, var(--brand-orange) 100%);
            transform: translateY(-2px);
            box-shadow: 0 6px 12px rgba(234, 88, 12, 0.4);
        }
        
        .plan-button {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            box-shadow: 0 4px 6px rgba(16, 185, 129, 0.3);
        }
        
        .plan-button:hover {
            background: linear-gradient(135deg, #059669 0%, #10b981 100%);
            transform: translateY(-2px);
            box-shadow: 0 6px 12px rgba(16, 185, 129, 0.4);
        }
        
        .action-description {
            color: var(--text-light);
            font-size: 14px;
            line-height: 1.5;
            margin: 8px 0 0 0;
            text-align: center;
            font-weight: 400;
        }
        
        .instructions {
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
            border-top: 1px solid rgba(37, 99, 235, 0.1);
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
            
            .welcome-message {
                padding: 25px 20px;
            }
            
            .title {
                font-size: 24px;
            }
            
            .subtitle {
                font-size: 16px;
            }
            
            .verify-button {
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
                <img src="{{ asset('logo.png') }}" alt="{{ config('app.name') }}" class="logo">
            </div>
            <h1 class="title">{{ __('auth.welcome_email.title', [], $locale) }}</h1>
            <p class="subtitle">{{ __('auth.welcome_email.subtitle', [], $locale) }}</p>
        </div>

        <div class="content">
            <div class="welcome-message">
                <p class="welcome-text">
                    {{ __('auth.welcome_email.greeting', ['name' => $user->first_name], $locale) }}!
                </p>
                
                <p class="welcome-text">
                    {{ __('auth.welcome_email.marketing_intro', [], $locale) }}
                </p>
                
                <div class="actions-container">
                    <!-- Verify Email Button -->
                    <div>
                        <a href="{{ route('verification.verify', ['id' => $user->id, 'hash' => sha1($user->getEmailForVerification()), 'locale' => $locale]) }}" class="action-button verify-button">
                            {{ __('auth.welcome_email.verify_button', [], $locale) }}
                        </a>
                        <p class="action-description">
                            {{ __('auth.welcome_email.verify_description', [], $locale) }}
                        </p>
                    </div>
                    
                    <!-- Free Quiz Button -->
                    <div>
                        <a href="{{ route('dashboard.quizzes.show', ['id' => 1, 'locale' => $locale]) }}" class="action-button quiz-button">
                            {{ __('auth.welcome_email.free_quiz_button', [], $locale) }}
                        </a>
                        <p class="action-description">
                            {{ __('auth.welcome_email.free_quiz_description', [], $locale) }}
                        </p>
                    </div>
                    
                    <!-- Pricing Plans Button -->
                    <div>
                        <a href="{{ route('plans', ['locale' => $locale]) }}" class="action-button plan-button">
                            {{ __('auth.welcome_email.pricing_button', [], $locale) }}
                        </a>
                        <p class="action-description">
                            {{ __('auth.welcome_email.pricing_description', [], $locale) }}
                        </p>
                    </div>
                </div>
                
                <p class="instructions">
                    {{ __('auth.welcome_email.alternative', [], $locale) }}<br>
                    {{ route('verification.verify', ['id' => $user->id, 'hash' => sha1($user->getEmailForVerification()), 'locale' => $locale]) }}
                </p>
            </div>

            <div class="footer-content">
                <p class="footer-text">
                    {{ __('auth.welcome_email.benefits_title', [], $locale) }}
                </p>
                
                <p class="footer-text">
                    {{ __('auth.welcome_email.benefits_description', [], $locale) }}
                </p>
                
                <p class="footer-text">
                    {{ __('auth.welcome_email.contact_support', ['email' => config('mail.support_email')], $locale) }}
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
