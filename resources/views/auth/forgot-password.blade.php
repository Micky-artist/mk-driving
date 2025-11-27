@extends('layouts.app')

@push('styles')
<style>
    :root {
        --primary-color: #0369a1;
        --primary-hover: #0e7490;
        --text-primary: #1f2937;
        --text-secondary: #4b5563;
        --bg-primary: #ffffff;
        --bg-secondary: #f9fafb;
        --border-color: #e5e7eb;
        --input-bg: #ffffff;
        --input-border: #e5e7eb;
        --card-bg: rgba(255, 255, 255, 0.98);
        --card-border: rgba(255, 255, 255, 0.2);
        --card-shadow: 0 10px 30px rgba(0, 0, 0, 0.15), 0 0 0 1px rgba(0, 0, 0, 0.05);
        --card-hover-shadow: 0 20px 40px rgba(0, 0, 0, 0.2), 0 0 0 1px rgba(0, 0, 0, 0.1);
    }
    
    @media (prefers-color-scheme: dark) {
        :root {
            --primary-color: #0ea5e9;
            --primary-hover: #38bdf8;
            --text-primary: #f3f4f6;
            --text-secondary: #9ca3af;
            --bg-primary: #0f172a;
            --bg-secondary: #1e293b;
            --border-color: #334155;
            --input-bg: #1e293b;
            --input-border: #334155;
            --card-bg: rgba(30, 41, 59, 0.9);
            --card-border: rgba(255, 255, 255, 0.1);
            --card-shadow: 0 10px 30px rgba(0, 0, 0, 0.3), 0 0 0 1px rgba(255, 255, 255, 0.05);
            --card-hover-shadow: 0 20px 40px rgba(0, 0, 0, 0.4), 0 0 0 1px rgba(255, 255, 255, 0.1);
        }
    }

    .gradient-bg {
        background: linear-gradient(135deg, var(--bg-primary) 0%, var(--bg-secondary) 100%);
        min-height: 100vh;
    }
    
    .auth-card {
        background: var(--card-bg);
        border-radius: 16px;
        box-shadow: var(--card-shadow);
        border: 1px solid var(--card-border);
        transform: translateZ(0);
        transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        overflow: hidden;
        backdrop-filter: blur(10px);
    }
    
    .auth-card:hover {
        transform: translateY(-4px) translateZ(0);
        box-shadow: var(--card-hover-shadow);
    }
    
    .input-field {
        width: 100%;
        padding: 1.25rem 1rem 1.25rem 4rem;
        border-radius: 1rem;
        border: 2px solid var(--input-border);
        background-color: var(--input-bg);
        color: var(--text-primary);
        font-weight: 500;
        font-size: 1rem;
        transition: all 0.2s ease-out;
        height: 60px;
    }
    
    .input-field:focus {
        transform: translateY(-1px);
        box-shadow: 0 0 0 3px rgba(14, 165, 233, 0.2);
        border-color: var(--primary-color);
        outline: none;
    }
    
    .btn-primary {
        width: 100%;
        padding: 1.25rem 2rem;
        border-radius: 0.75rem;
        background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-hover) 100%);
        color: white;
        font-weight: 700;
        font-size: 1.125rem;
        letter-spacing: 0.5px;
        transition: all 0.2s ease-out;
        border: none;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        text-align: center;
    }
    
    .btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 15px rgba(14, 165, 233, 0.3);
    }
    
    .btn-primary:active {
        transform: translateY(0);
    }
    
    .input-group {
        position: relative;
        margin-bottom: 1.5rem;
    }
    
    .input-icon {
        position: absolute;
        left: 1rem;
        top: 50%;
        transform: translateY(-50%);
        color: var(--text-secondary);
        transition: all 0.2s ease-out;
    }
    
    .input-field:focus ~ .input-icon,
    .input-field:not(:placeholder-shown) ~ .input-icon {
        color: var(--primary-color);
    }
    
    .auth-logo {
        transition: all 0.3s ease-out;
    }
    
    .auth-logo:hover {
        transform: scale(1.05) rotate(-2deg);
    }
    
    .back-to-login {
        display: inline-flex;
        align-items: center;
        color: var(--primary-color);
        font-weight: 600;
        transition: all 0.2s ease-out;
    }
    
    .back-to-login:hover {
        color: var(--primary-hover);
        transform: translateX(-4px);
    }
    
    .back-to-login svg {
        margin-right: 8px;
        transition: transform 0.2s ease-out;
    }
    
    .back-to-login:hover svg {
        transform: translateX(-4px);
    }
</style>
@endpush

@section('content')
<div class="w-full min-h-screen flex flex-col relative overflow-hidden gradient-bg">
    <!-- Background Animation Component -->
    <x-background-animation />
    
    <div class="flex-grow flex items-center justify-center px-1 relative z-10 py-8">
        <div class="w-full max-w-md mx-auto">
            <div class="auth-card rounded-2xl p-8">
                <div class="text-center pb-4">
                    <div class="flex justify-center mb-6">
                        <a href="{{ route('home', app()->getLocale()) }}" class="auth-logo">
                            <div class="p-3 bg-gradient-to-br from-[#0369a1] to-[#0e7490] rounded-2xl shadow-lg transform rotate-6">
                                <div class="bg-white p-2 rounded-xl shadow-inner -rotate-6">
                                    <img src="{{ asset('images/logo.png') }}" alt="{{ config('app.name') }}" class="h-12 w-auto">
                                </div>
                            </div>
                        </a>
                    </div>
                    
                    <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-2">
                        {{ __('auth.forgot_password_page.title') }}
                    </h2>
                    <p class="text-sm text-gray-600 dark:text-gray-300 mb-6">
                        {{ __('auth.forgot_password_page.subtitle') }}
                    </p>
                </div>

                <!-- Session Status -->
                @if (session('status'))
                    <div class="mb-4 p-4 text-sm text-green-700 bg-green-100 rounded-lg dark:bg-green-900 dark:text-green-200" role="alert">
                        {{ session('status') === 'passwords.sent' ? __('auth.forgot_password_page.success_message') : session('status') }}
                    </div>
                @endif

                <form method="POST" action="{{ route('password.email', app()->getLocale()) }}" class="space-y-4">
                    @csrf

                    <!-- Email -->
                    <div class="input-group">
                        <div class="relative w-full">
                            <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                                <svg class="input-icon w-5 h-5" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z"></path>
                                    <path d="M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z"></path>
                                </svg>
                            </div>
                            <input 
                                type="email" 
                                id="email" 
                                name="email" 
                                value="{{ old('email') }}" 
                                class="input-field pl-12" 
                                placeholder="{{ __('auth.forgot_password_page.email_placeholder') }}" 
                                required 
                                autofocus
                            >
                        </div>
                        @error('email')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="pt-2">
                        <button type="submit" class="btn-primary">
                            {{ __('auth.forgot_password_page.submit_button') }}
                            <svg class="w-5 h-5 ml-2 -mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path>
                            </svg>
                        </button>
                    </div>
                </form>

                <div class="mt-6 text-center">
                    <a href="{{ route('login', app()->getLocale()) }}" class="back-to-login">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                        </svg>
                        {{ __('auth.forgot_password_page.back_to_login') }}
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
