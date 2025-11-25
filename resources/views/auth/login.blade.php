@extends('layouts.app')

@push('styles')
<style>
    /* Override some styles for the login page */
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
    
    /* Ensure the login form is properly spaced below the navbar */
    .login-container {
        min-height: calc(100vh - 5rem); /* Account for navbar height */
        padding-top: 0.5rem; /* Space for fixed navbar */
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
    
    .login-card {
        background: var(--card-bg);
        border-radius: 16px;
        box-shadow: var(--card-shadow);
        border: 1px solid var(--card-border);
        transform: translateZ(0);
        transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        overflow: hidden;
        backdrop-filter: blur(10px);
    }
    
    .login-card:hover {
        transform: translateY(-4px) translateZ(0);
        box-shadow: var(--card-hover-shadow);
    }
    
    .input-field:focus ~ .input-icon,
    .input-field:not(:placeholder-shown) ~ .input-icon {
        color: var(--primary-color);
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
        text-transform: uppercase;
        border: 2px solid rgba(255, 255, 255, 0.2);
        box-shadow: 0 10px 20px -5px rgba(3, 105, 161, 0.3);
        transition: all 0.3s ease-out;
        cursor: pointer;
    }
    
    .btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 15px 25px -5px rgba(3, 105, 161, 0.4);
    }
    
    .btn-primary:active {
        transform: translateY(1px);
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
    }
    
    /* Text colors */
    .text-gray-900 {
        color: var(--text-primary);
    }
    
    .text-gray-600 {
        color: var(--text-secondary);
    }
    
    /* Remember me checkbox */
    .form-checkbox {
        background-color: var(--input-bg);
        border-color: var(--border-color);
    }
    
    /* Links */
    a {
        color: var(--primary-color);
        transition: color 0.2s ease;
    }
    
    a:hover {
        color: var(--primary-hover);
    }
</style>
@endpush

@section('content')
@section('content')
<div class="w-full min-h-screen flex flex-col relative overflow-hidden gradient-bg login-container">
    <!-- Background Animation Component -->
    <x-background-animation />
    
    <div class="flex-grow flex items-center justify-center px-1 relative z-10">
        <div class="w-full max-w-md mx-auto">
            <div class="login-card rounded-2xl p-8">
                        <div class="text-center pb-4">
                            <div class="flex justify-center mb-6">
                                <div class="p-3 bg-gradient-to-br from-[#0369a1] to-[#0e7490] rounded-2xl shadow-lg transform rotate-6">
                                    <div class="bg-white p-2 rounded-xl shadow-inner -rotate-6">
                                        <img 
                                            src="{{ asset('logo.png') }}" 
                                            alt="Logo" 
                                            class="h-16 w-16 object-contain"
                                            width="64"
                                            height="64"
                                        >
                                    </div>
                                </div>
                            </div>
                            <h1 class="text-4xl font-extrabold text-gray-900 mb-3 bg-gradient-to-r from-[#0369a1] to-[#0e7490] bg-clip-text text-transparent">
                                {{ __('auth.login.title') }}
                            </h1>
                            <p class="text-gray-600 text-base font-medium">
                                {{ __('auth.login.subtitle') }}
                            </p>
                        </div>

                        <div class="space-y-5">
                            <!-- Session Status -->
                            @if (session('status'))
                                <div class="mb-4 text-sm font-medium text-green-600">
                                    {{ session('status') }}
                                </div>
                            @endif

                            <!-- Google Sign In Button -->
                            <div class="mb-6">
                                <a href="{{ route('google.login') }}" class="btn-google w-full flex items-center justify-center space-x-3 py-2.5 px-4">
                                    <img src="{{ asset('images/google-icon.png') }}" alt="Google" class="w-8 h-8">
                                    <span class="text-lg font-medium">{{ __('Continue with Google') }}</span>
                                </a>
                            </div>

                            <div class="divider">
                                {{ __('Or sign in with email') }}
                            </div>

                            <form id="loginForm" method="POST" action="{{ route('login', app()->getLocale()) }}" class="space-y-4">
                                @csrf

                                <!-- Email -->
                                <div>
                                    <div class="relative">
                                        <input 
                                            id="email"
                                            type="email"
                                            name="email"
                                            value="{{ old('email') }}"
                                            required
                                            autofocus
                                            autocomplete="username"
                                            placeholder="{{ __('auth.login.email') }}"
                                            class="input-field pl-12"
                                        >
                                        <div class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-400">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207"></path>
                                            </svg>
                                        </div>
                                    </div>
                                    @error('email')
                                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Password -->
                                <div class="relative">
                                    <div class="relative">
                                        <div class="relative">
                                            <input 
                                                id="password"
                                                type="password"
                                                name="password"
                                                required
                                                autocomplete="current-password"
                                                placeholder="{{ __('auth.login.password') }}"
                                                class="input-field pl-12 pr-12"
                                            >
                                            <div class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-400">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                                                </svg>
                                            </div>
                                        <button 
                                            type="button" 
                                            onclick="togglePasswordVisibility()" 
                                            class="absolute inset-y-0 right-0 pr-4 flex items-center text-gray-400 hover:text-gray-700 focus:outline-none transition-all duration-200" 
                                            tabindex="-1"
                                            aria-label="Toggle password visibility"
                                        >
                                            <svg id="eye-icon" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path id="eye-open" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                <path id="eye-open-2" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                <path id="eye-closed" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" style="display: none;" />
                                            </svg>
                                        </button>
                                    </div>
                                    </div>
                                    @error('password')
                                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Remember Me -->
                                <div class="flex items-center justify-between pt-1">
                                    <div class="flex items-center">
                                        <div class="flex items-center h-5">
                                            <input 
                                                id="remember_me" 
                                                type="checkbox" 
                                                name="remember" 
                                                class="h-5 w-5 text-[#0369a1] focus:ring-[#0369a1] border-2 border-gray-300 rounded-lg transition-all duration-200 focus:ring-2 focus:ring-offset-2 focus:ring-offset-white focus:ring-[#0369a1] cursor-pointer"
                                            >
                                        </div>
                                        <label for="remember_me" class="ml-3 block text-sm font-medium text-gray-700 cursor-pointer hover:text-gray-900 transition-colors">
                                            {{ __('auth.remember_me') }}
                                        </label>
                                    </div>

                                    <div class="text-right">
                                        <a 
                                            href="{{ route('password.email', app()->getLocale()) }}" 
                                            class="text-sm font-semibold text-[#0369a1] hover:text-[#0c4a6e] hover:underline transition-colors"
                                        >
                                            {{ __('auth.login.forgot_password') }}
                                        </a>
                                    </div>
                                </div>

                                <div>
                                    <button type="submit" class="btn-primary">
                                    <span class="flex items-center justify-center">
                                        <span class="login-button-text">{{ __('auth.login.login_button') }}</span>
                                        <svg class="animate-spin -mr-1 ml-2 h-4 w-4 text-white hidden" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                        </svg>
                                    </span>
                                </button>
                                </div>

                                <div class="pt-2 text-center">
                                    <p class="text-sm text-gray-600">
                                        {{ __('auth.login.no_account') }}
                                        <a 
                                            href="{{ route('register', ['locale' => app()->getLocale()]) }}" 
                                            class="font-semibold text-[#0369a1] hover:text-[#0c4a6e] hover:underline transition-colors"
                                        >
                                            {{ __('auth.login.sign_up') }}
                                        </a>
                                    </p>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="{{ asset('js/google-auth.js') }}"></script>
<script>
    // Handle form submission
    document.getElementById('loginForm').addEventListener('submit', async function(e) {
        e.preventDefault();
        
        const form = e.target;
        const submitButton = form.querySelector('button[type="submit"]');
        const originalButtonText = submitButton.innerHTML;
        
        try {
            // Disable the submit button and show loading state
            submitButton.disabled = true;
            const loginText = submitButton.querySelector('.login-button-text');
            const spinner = submitButton.querySelector('svg');
            
            if (loginText) loginText.textContent = '{{ __("auth.login.logging_in") }}';
            if (spinner) spinner.classList.remove('hidden');
            
            const response = await fetch(form.action, {
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({
                    email: form.email.value,
                    password: form.password.value,
                    remember: form.remember ? form.remember.checked : false
                })
            });
            
            const data = await response.json();
            
            if (response.ok) {
                // Redirect to the provided URL on success
                window.location.href = data.redirect;
            } else {
                // Handle errors
                if (data.errors) {
                    // You can add error handling here if needed
                    console.error('Login error:', data.message);
                    // Show error message to user
                    alert(data.message || 'An error occurred during login');
                }
                // Re-enable the submit button
                submitButton.disabled = false;
                if (loginText) loginText.textContent = '{{ __("auth.login.login_button") }}';
                if (spinner) spinner.classList.add('hidden');
            }
        } catch (error) {
            console.error('Login error:', error);
            alert('An error occurred. Please try again.');
            // Re-enable the submit button
            submitButton.disabled = false;
            if (loginText) loginText.textContent = '{{ __("auth.login.login_button") }}';
            if (spinner) spinner.classList.add('hidden');
        }
    });
    
    function togglePasswordVisibility() {
        const passwordInput = document.getElementById('password');
        const eyeOpen = document.getElementById('eye-open');
        const eyeOpen2 = document.getElementById('eye-open-2');
        const eyeClosed = document.getElementById('eye-closed');
        
        if (passwordInput.type === 'password') {
            passwordInput.type = 'text';
            eyeOpen.style.display = 'none';
            eyeOpen2.style.display = 'none';
            eyeClosed.style.display = 'block';
        } else {
            passwordInput.type = 'password';
            eyeOpen.style.display = 'block';
            eyeOpen2.style.display = 'block';
            eyeClosed.style.display = 'none';
        }
    }
</script>
@endpush
