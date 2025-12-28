@extends('layouts.app')

@push('styles')
<style>
    :root {
        --primary-color: #2563eb;
        --primary-hover: #1d4ed8;
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
            --primary-color: #3b82f6;
            --primary-hover: #2563eb;
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
    }

    .auth-card:hover {
        box-shadow: var(--card-hover-shadow);
    }

    .auth-logo {
        display: inline-block;
        transition: transform 0.3s ease;
    }

    .auth-logo:hover {
        transform: translateY(-2px);
    }

    .input-field {
        width: 100%;
        padding: 0.75rem 1rem 0.75rem 3rem;
        border-radius: 0.5rem;
        border: 1px solid var(--input-border);
        background-color: var(--input-bg);
        color: var(--text-primary);
        font-size: 0.9375rem;
        line-height: 1.25rem;
        transition: all 0.2s ease-in-out;
    }

    .input-field:focus {
        outline: none;
        border-color: var(--primary-color);
        box-shadow: 0 0 0 3px rgba(3, 105, 161, 0.2);
    }

    .input-icon {
        color: var(--text-secondary);
    }

    .btn-primary {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 100%;
        padding: 0.75rem 1.5rem;
        border-radius: 0.5rem;
        background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-hover) 100%);
        color: white;
        font-weight: 500;
        font-size: 0.9375rem;
        line-height: 1.25rem;
        text-align: center;
        transition: all 0.2s ease-in-out;
        border: none;
        cursor: pointer;
    }

    .btn-primary:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(3, 105, 161, 0.2);
    }

    .btn-primary:active {
        transform: translateY(0);
    }

    .back-to-login {
        display: inline-flex;
        align-items: center;
        color: var(--primary-color);
        font-size: 0.875rem;
        font-weight: 500;
        text-decoration: none;
        transition: color 0.2s ease-in-out;
    }

    .back-to-login:hover {
        color: var(--primary-hover);
    }

    .back-to-login svg {
        margin-right: 0.5rem;
    }
</style>
@endpush

@section('content')
<div class="w-full min-h-screen flex flex-col relative overflow-hidden gradient-bg">
    <div class="flex-grow flex items-center justify-center px-1 relative z-10 py-8">
        <div class="w-full max-w-md mx-4">
            <div class="auth-card p-8 sm:p-10">
                <div class="text-center pb-4">
                    <div class="flex justify-center mb-6">
                        <a href="{{ route('home', app()->getLocale()) }}" class="auth-logo">
                            <img src="{{ asset('images/logo.png') }}" alt="{{ config('app.name') }}" class="h-16 w-auto">
                        </a>
                    </div>
                    
                    <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-2">
                        {{ __('auth.reset_password_page.title') }}
                    </h2>
                    <p class="text-sm text-gray-600 dark:text-gray-300 mb-6">
                        {{ __('auth.reset_password_page.subtitle') }}
                    </p>
                </div>

                @if ($errors->any())
                    <div class="mb-6 p-4 text-sm text-red-700 bg-red-100 rounded-lg dark:bg-red-900 dark:text-red-200" role="alert">
                        <ul class="space-y-1">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('password.store', app()->getLocale()) }}" class="space-y-4">
                    @csrf

                    <!-- Password Reset Token -->
                    <input type="hidden" name="token" value="{{ $request->route('token') }}">

                    <!-- Email Address -->
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
                                value="{{ old('email', $request->email) }}" 
                                class="input-field" 
                                placeholder="{{ __('auth.reset_password_page.email_placeholder') }}" 
                                required 
                                readonly
                            >
                        </div>
                    </div>

                    <!-- Password -->
                    <div class="input-group">
                        <div class="relative w-full">
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                                    <svg class="input-icon w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                                    </svg>
                                </div>
                                <input 
                                    type="password"
                                    id="password" 
                                    name="password" 
                                    class="input-field pl-12 pr-12" 
                                    placeholder="{{ __('auth.reset_password_page.new_password_placeholder') }}" 
                                    required 
                                    autofocus
                                >
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
                        
                        <!-- Password Strength Meter -->
                        <div id="password-validation" class="mt-2 hidden">
                            <div class="flex items-center justify-between mb-1">
                                <div class="h-2 w-full bg-gray-200 rounded-full overflow-hidden dark:bg-gray-700">
                                    <div id="password-strength-meter" class="h-full bg-gray-400 rounded-full transition-all duration-300 w-0"></div>
                                </div>
                                <span id="password-strength-text" class="text-xs font-medium ml-2 text-gray-500 dark:text-gray-400">{{ __('auth.password_requirements.strength.weak') }}</span>
                            </div>
                            
                            <div id="password-requirements" class="text-xs text-gray-500 dark:text-gray-400 space-y-1 mt-2">
                                <div id="req-length" class="flex items-center">
                                    <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                    </svg>
                                    {{ __('auth.password_requirements.length') }}
                                </div>
                                <div id="req-letter" class="flex items-center">
                                    <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                    </svg>
                                    {{ __('auth.password_requirements.letter') }}
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Confirm Password -->
                    <div class="input-group">
                        <div class="relative w-full">
                            <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                                <svg class="input-icon w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                                </svg>
                            </div>
                            <input 
                                type="password" 
                                id="password_confirmation" 
                                name="password_confirmation" 
                                class="input-field" 
                                placeholder="{{ __('auth.reset_password_page.confirm_password_placeholder') }}" 
                                required
                            >
                        </div>
                    </div>

                    <div class="pt-2">
                        <button type="submit" class="btn-primary">
                            {{ __('auth.reset_password_page.submit_button') }}
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
                        {{ __('auth.reset_password_page.back_to_login') }}
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@push('scripts')
<script>
    // Initialize password validation UI
    const passwordInput = document.getElementById('password');
    const validationDiv = document.getElementById('password-validation');
    
    // Hide validation UI by default
    if (validationDiv) {
        validationDiv.classList.add('hidden');
    }
    
    // Track if user has started typing
    let hasUserTyped = false;
    
    // Update the password validation function
    function validatePassword() {
        const password = passwordInput.value;
        const strengthMeter = document.getElementById('password-strength-meter');
        const strengthText = document.getElementById('password-strength-text');
        
        if (!validationDiv) return false;
        
        // Password requirements
        const hasMinLength = password.length >= 6;
        const hasLetter = /[a-zA-Z]/.test(password);
        const hasNumber = /\d/.test(password);
        
        // Update requirement indicators
        function updateRequirement(id, isValid) {
            const element = document.getElementById('req-' + id);
            if (element) {
                const svg = element.querySelector('svg');
                if (isValid) {
                    svg.classList.remove('text-gray-400');
                    svg.classList.add('text-green-500');
                } else {
                    svg.classList.remove('text-green-500');
                    svg.classList.add('text-gray-400');
                }
            }
        }
        
        updateRequirement('length', hasMinLength);
        updateRequirement('letter', hasLetter && hasNumber);
        
        // Calculate password strength (simplified)
        let strength = 0;
        if (hasMinLength) strength += 1;
        if (hasLetter) strength += 1;
        if (hasNumber) strength += 1;
        
        // Update strength meter
        if (strengthMeter && strengthText) {
            const percentage = (strength / 3) * 100;
            strengthMeter.style.width = percentage + '%';
            
            // Set color and localized text based on strength
            if (strength <= 1) {
                strengthMeter.className = 'h-full bg-red-500 rounded-full transition-all duration-300';
                strengthText.textContent = '{{ __("auth.password_requirements.strength.weak") }}';
            } else if (strength === 2) {
                strengthMeter.className = 'h-full bg-yellow-500 rounded-full transition-all duration-300';
                strengthText.textContent = '{{ __("auth.password_requirements.strength.good") }}';
            } else {
                strengthMeter.className = 'h-full bg-green-500 rounded-full transition-all duration-300';
                strengthText.textContent = '{{ __("auth.password_requirements.strength.strong") }}';
            }
        }
        
        return strength >= 2; // Require at least "good" strength
    }
    
    // Add input event listener
    if (passwordInput) {
        passwordInput.addEventListener('input', function() {
            hasUserTyped = true;
            if (this.value.length > 0) {
                if (validationDiv) {
                    validationDiv.classList.remove('hidden');
                }
                validatePassword();
            } else {
                if (validationDiv) {
                    validationDiv.classList.add('hidden');
                }
            }
        });
        
        // Add focus event to show validation on focus
        passwordInput.addEventListener('focus', function() {
            if (this.value.length > 0) {
                if (validationDiv) {
                    validationDiv.classList.remove('hidden');
                }
            }
        });
        
        // Add blur event to hide validation if empty
        passwordInput.addEventListener('blur', function() {
            if (this.value.length === 0) {
                if (validationDiv) {
                    validationDiv.classList.add('hidden');
                }
            }
        });
    }
    
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
@endsection
