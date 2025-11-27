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
        --card-bg: rgba(255, 255, 255, 0.95);
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
            --card-bg: rgba(30, 41, 59, 0.95);
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
        max-width: 28rem;
        width: 100%;
    }
    
    .login-card:hover {
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
        height: 56px;
        margin-bottom: 0.25rem;
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
    
    .btn-primary:hover:not(:disabled) {
        transform: translateY(-2px);
        box-shadow: 0 15px 30px -5px rgba(3, 105, 161, 0.4);
        border-color: rgba(255, 255, 255, 0.3);
    }
    
    .btn-primary:active {
        transform: translateY(1px);
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
    }

    .btn-primary:disabled {
        opacity: 0.7;
        cursor: not-allowed;
    }

    .btn-google {
        border: 2px solid var(--border-color);
        border-radius: 0.75rem;
        background: var(--input-bg);
        color: var(--text-primary);
        font-weight: 600;
        transition: all 0.3s ease;
        padding: 0.75rem 1rem;
    }

    .btn-google:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
        border-color: var(--primary-color);
    }

    .divider {
        display: flex;
        align-items: center;
        text-align: center;
        color: var(--text-secondary);
        font-size: 0.875rem;
        margin: 1.5rem 0;
    }

    .divider::before,
    .divider::after {
        content: '';
        flex: 1;
        border-bottom: 1px solid var(--border-color);
    }

    .divider::before {
        margin-right: 1rem;
    }

    .divider::after {
        margin-left: 1rem;
    }
    
    /* Text colors */
    .text-gray-900 {
        color: var(--text-primary);
    }
    
    .text-gray-600 {
        color: var(--text-secondary);
    }

    /* Password visibility toggle button */
    .toggle-password-btn {
        position: absolute;
        right: 1rem;
        top: 50%;
        transform: translateY(-50%);
        background: none;
        border: none;
        cursor: pointer;
        color: var(--text-secondary);
        padding: 0.5rem;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: color 0.2s;
    }

    .toggle-password-btn:hover {
        color: var(--text-primary);
    }

    .toggle-password-btn:focus {
        outline: none;
    }
</style>
@endpush

@section('content')
<div class="w-full min-h-screen flex flex-col relative overflow-hidden gradient-bg">
    
    <div class="flex-grow flex items-center justify-center px-4 py-8 relative z-10">
        <div class="w-full max-w-md mx-auto">
            <div class="login-card rounded-2xl p-8">
                <div class="text-center pb-4">
                    <div class="flex justify-center mb-6">
                        <div class="p-3 bg-gradient-to-br from-[#0369a1] to-[#0e7490] rounded-2xl shadow-lg transform rotate-6">
                            <div class="bg-white p-2 rounded-xl shadow-inner -rotate-6 dark:bg-gray-800">
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
                        {{ __('auth.register.title') }}
                    </h1>
                    <p class="text-gray-600 text-base font-medium dark:text-gray-300">
                        {{ __('auth.register.subtitle') }}
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
                        <a href="{{ route('google.login') }}" class="btn-google w-full flex items-center justify-center space-x-3">
                            <img src="{{ asset('images/google-icon.png') }}" alt="Google" class="w-8 h-8">
                            <span class="text-lg font-medium">{{ __('auth.continue_with_google') }}</span>
                        </a>
                    </div>

                    <div class="divider">
                        {{ __('auth.or_sign_up_with_email') }}
                    </div>

                    <form id="registerForm" method="POST" action="{{ route('register', app()->getLocale()) }}">
                        @csrf
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                            <!-- First Name -->
                            <div class="form-group">
                                <div class="relative">
                                    <input 
                                        id="first_name" 
                                        name="first_name" 
                                        type="text" 
                                        value="{{ old('first_name') }}" 
                                        placeholder="{{ __('auth.register.first_name') }}"
                                        class="input-field"
                                        required 
                                        autofocus
                                    />
                                    <div class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-400">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                        </svg>
                                    </div>
                                </div>
                                @error('first_name')
                                    <p class="text-sm text-red-500 mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Last Name -->
                            <div class="form-group">
                                <div class="relative">
                                    <input 
                                        id="last_name" 
                                        name="last_name" 
                                        type="text" 
                                        value="{{ old('last_name') }}" 
                                        required 
                                        placeholder="{{ __('auth.register.last_name') }}"
                                        class="input-field"
                                    />
                                    <div class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-400">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                        </svg>
                                    </div>
                                </div>
                                @error('last_name')
                                    <p class="text-sm text-red-500 mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <!-- Email -->
                        <div class="form-group mb-4">
                            <div class="relative">
                                <input 
                                    id="email" 
                                    name="email" 
                                    type="email" 
                                    value="{{ old('email') }}" 
                                    required 
                                    autocomplete="username"
                                    placeholder="{{ __('auth.register.email') }}"
                                    class="input-field"
                                />
                                <div class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-400">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                    </svg>
                                </div>
                            </div>
                            @error('email')
                                <p class="text-sm text-red-500 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Password -->
                        <div class="form-group mb-4">
                            <div class="relative">
                                <input 
                                    id="password" 
                                    name="password" 
                                    type="password" 
                                    required 
                                    autocomplete="new-password"
                                    placeholder="{{ __('auth.register.password') }}"
                                    class="input-field pr-12"
                                />
                                <div class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-400">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                    </svg>
                                </div>
                                <button type="button" 
                                        class="toggle-password-btn"
                                        onclick="togglePasswordVisibility('password')"
                                        aria-label="Toggle password visibility">
                                    <svg id="eye-icon-password" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                    </svg>
                                </button>
                            </div>
                            
                            <!-- Password Strength Meter -->
                            <div id="password-validation" class="mt-2 hidden">
                                <div class="flex items-center justify-between mb-1">
                                    <div class="h-2 w-full bg-gray-200 rounded-full overflow-hidden dark:bg-gray-700">
                                        <div id="strength-meter-fill" class="h-full bg-gray-400 rounded-full transition-all duration-300 w-0"></div>
                                    </div>
                                    <span id="strength-text" class="text-xs font-medium ml-2 text-gray-500 dark:text-gray-400">{{ __('auth.password_requirements.strength.weak') }}</span>
                                </div>
                                
                                <div id="password-requirements" class="text-xs text-gray-500 dark:text-gray-400 space-y-1 mt-2">
                                    <p class="font-medium text-sm mb-1">{{ __('auth.password_requirements.title') }}</p>
                                    <div class="flex items-center" id="req-length">
                                        <svg class="w-3.5 h-3.5 mr-1.5 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                        </svg>
                                        <span>{{ __('auth.password_requirements.length') }}</span>
                                    </div>
                                    <div class="flex items-center" id="req-uppercase">
                                        <svg class="w-3.5 h-3.5 mr-1.5 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                        </svg>
                                        <span>{{ __('auth.password_requirements.uppercase') }}</span>
                                    </div>
                                    <div class="flex items-center" id="req-lowercase">
                                        <svg class="w-3.5 h-3.5 mr-1.5 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                        </svg>
                                        <span>{{ __('auth.password_requirements.lowercase') }}</span>
                                    </div>
                                    <div class="flex items-center" id="req-number">
                                        <svg class="w-3.5 h-3.5 mr-1.5 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                        </svg>
                                        <span>{{ __('auth.password_requirements.number') }}</span>
                                    </div>
                                    <div class="flex items-center" id="req-special">
                                        <svg class="w-3.5 h-3.5 mr-1.5 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                        </svg>
                                        <span>{{ __('auth.password_requirements.special') }}</span>
                                    </div>
                                </div>
                            </div>
                            
                            @error('password')
                                <p class="text-sm text-red-500 mt-2">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Password Confirmation -->
                        <div class="form-group mb-4">
                            <div class="relative">
                                <input 
                                    id="password_confirmation" 
                                    name="password_confirmation" 
                                    type="password" 
                                    required 
                                    autocomplete="new-password"
                                    placeholder="{{ __('auth.register.confirm_password') }}"
                                    class="input-field pr-12"
                                />
                                <div class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-400">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                                    </svg>
                                </div>
                                <button type="button" 
                                        class="toggle-password-btn"
                                        onclick="togglePasswordVisibility('password_confirmation')"
                                        aria-label="Toggle password confirmation visibility">
                                    <svg id="eye-icon-password_confirmation" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                    </svg>
                                </button>
                            </div>
                            
                            <!-- Password Match Indicator -->
                            <div id="password-match" class="hidden mt-2">
                                <p class="text-sm text-green-500 flex items-center">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                    </svg>
                                    <span>{{ __('auth.password_requirements.match') }}</span>
                                </p>
                            </div>
                            <div id="password-mismatch" class="hidden mt-2">
                                <p class="text-sm text-red-500 flex items-center">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                    </svg>
                                    <span>{{ __('auth.password_requirements.mismatch') }}</span>
                                </p>
                            </div>
                        </div>

                        <!-- API Error -->
                        @if(session('error'))
                            <div class="p-3 bg-red-50 dark:bg-red-900/20 text-red-700 dark:text-red-300 rounded-lg text-sm text-center mb-4">
                                {{ session('error') }}
                            </div>
                        @endif

                        <div class="mt-6">
                            <button type="submit" id="submitBtn" class="btn-primary">
                                <span id="buttonText">{{ __('auth.register.register_button') }}</span>
                            </button>
                        </div>

                        <div class="mt-6 text-center">
                            <p class="text-sm text-gray-600 dark:text-gray-400">
                                {{ __('auth.register.have_account') }}
                                <a href="{{ route('login', app()->getLocale()) }}" class="font-medium text-slate-800 hover:text-slate-900 dark:text-slate-200 dark:hover:text-white transition-colors">
                                    {{ __('auth.register.sign_in') }}
                                </a>
                            </p>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="{{ asset('js/google-auth.js') }}"></script>
<script>
    let hasUserTyped = false;
    
    // Toggle password visibility
    function togglePasswordVisibility(fieldId) {
        const passwordField = document.getElementById(fieldId);
        const eyeOpen = document.getElementById(`eye-open-${fieldId}`);
        const eyeOpen2 = document.getElementById(`eye-open-2-${fieldId}`);
        const eyeClosed = document.getElementById(`eye-closed-${fieldId}`);
        
        if (passwordField.type === 'password') {
            passwordField.type = 'text';
            if (eyeOpen) eyeOpen.style.display = 'none';
            if (eyeOpen2) eyeOpen2.style.display = 'none';
            if (eyeClosed) eyeClosed.style.display = 'block';
        } else {
            passwordField.type = 'password';
            if (eyeOpen) eyeOpen.style.display = 'block';
            if (eyeOpen2) eyeOpen2.style.display = 'block';
            if (eyeClosed) eyeClosed.style.display = 'none';
        }
    }
    
    // Update requirement indicator
    function updateRequirement(type, isValid) {
        const element = document.getElementById(`req-${type}`);
        if (!element) return;
        
        const icon = element.querySelector('svg');
        if (isValid) {
            icon.classList.remove('text-red-400');
            icon.classList.add('text-green-500');
            icon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>';
        } else {
            icon.classList.remove('text-green-500');
            icon.classList.add('text-red-400');
            icon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>';
        }
    }
    
    // Password validation function
    function validatePassword() {
        const password = document.getElementById('password').value;
        const validationDiv = document.getElementById('password-validation');
        const strengthMeter = document.getElementById('strength-meter-fill');
        const strengthText = document.getElementById('strength-text');
        
        // Hide validation UI by default
        if (!validationDiv) return false;
        
        // Show validation UI after first character
        if (password.length > 0) {
            if (!hasUserTyped) {
                hasUserTyped = true;
                validationDiv.classList.remove('hidden');
            }
        } else {
            // Hide validation UI when password is empty
            validationDiv.classList.add('hidden');
            hasUserTyped = false;
            // Clear any existing error messages
            const errorDiv = document.getElementById('password-error');
            if (errorDiv) errorDiv.remove();
            return false;
        }
        
        // Check password requirements
        const hasMinLength = password.length >= 8;
        const hasUppercase = /[A-Z]/.test(password);
        const hasLowercase = /[a-z]/.test(password);
        const hasNumber = /[0-9]/.test(password);
        const hasSpecial = /[^A-Za-z0-9]/.test(password);
        
        // Update requirement indicators
        updateRequirement('length', hasMinLength);
        updateRequirement('uppercase', hasUppercase);
        updateRequirement('lowercase', hasLowercase);
        updateRequirement('number', hasNumber);
        updateRequirement('special', hasSpecial);
        
        // Calculate password strength
        let strength = 0;
        if (hasMinLength) strength += 1;
        if (hasUppercase) strength += 1;
        if (hasLowercase) strength += 1;
        if (hasNumber) strength += 1;
        if (hasSpecial) strength += 1;
        
        // Update strength meter
        if (strengthMeter) {
            const width = (strength / 5) * 100;
            strengthMeter.style.width = `${width}%`;
            
            // Set color based on strength
            if (strength <= 2) {
                strengthMeter.className = 'h-full bg-red-500 rounded-full transition-all duration-300';
                strengthText.textContent = '{{ __("auth.password_requirements.strength.weak") }}';
            } else if (strength === 3) {
                strengthMeter.className = 'h-full bg-yellow-500 rounded-full transition-all duration-300';
                strengthText.textContent = '{{ __("auth.password_requirements.strength.medium") }}';
            } else if (strength === 4) {
                strengthMeter.className = 'h-full bg-blue-500 rounded-full transition-all duration-300';
                strengthText.textContent = '{{ __("auth.password_requirements.strength.strong") }}';
            } else {
                strengthMeter.className = 'h-full bg-green-500 rounded-full transition-all duration-300';
                strengthText.textContent = '{{ __("auth.password_requirements.strength.very_strong") }}';
            }
        }
        
        // Return true only if all requirements are met
        return hasMinLength && hasUppercase && hasLowercase && hasNumber && hasSpecial;
        
        // Also check password match when password changes
        validatePasswordMatch();
    }
    
    function updateRequirement(id, isValid) {
        const element = document.getElementById(`req-${id}`);
        if (!element) return;
        
        const icon = element.querySelector('svg');
        const text = element.querySelector('span');
        
        if (isValid) {
            icon.classList.remove('text-red-400');
            icon.classList.add('text-green-500');
            icon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>';
        } else {
            icon.classList.remove('text-green-500');
            icon.classList.add('text-red-400');
            icon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>';
        }
    }
    
    function validatePasswordMatch() {
        const password = document.getElementById('password').value;
        const confirmPassword = document.getElementById('password_confirmation').value;
        const matchElement = document.getElementById('password-match');
        const mismatchElement = document.getElementById('password-mismatch');
        
        if (confirmPassword === '') {
            matchElement.classList.add('hidden');
            mismatchElement.classList.add('hidden');
            return;
        }
        
        if (password === confirmPassword) {
            matchElement.classList.remove('hidden');
            mismatchElement.classList.add('hidden');
        } else {
            matchElement.classList.add('hidden');
            mismatchElement.classList.remove('hidden');
        }
    }
    
    // Initialize password validation on page load
    document.addEventListener('DOMContentLoaded', function() {
        // Add event listeners if elements exist
        const passwordInput = document.getElementById('password');
        const confirmPasswordInput = document.getElementById('password_confirmation');
        
        // Hide validation UI by default
        const validationDiv = document.getElementById('password-validation');
        if (validationDiv) {
            validationDiv.classList.add('hidden');
        }
        
        if (passwordInput) {
            // Add focus event to show validation on focus
            passwordInput.addEventListener('focus', function() {
                if (this.value.length > 0) {
                    const validationDiv = document.getElementById('password-validation');
                    if (validationDiv) {
                        validationDiv.classList.remove('hidden');
                    }
                }
            });
            
            // Add input event for real-time validation
            passwordInput.addEventListener('input', validatePassword);
            
            // Add blur event to hide validation if empty
            passwordInput.addEventListener('blur', function() {
                if (this.value.length === 0) {
                    const validationDiv = document.getElementById('password-validation');
                    if (validationDiv) {
                        validationDiv.classList.add('hidden');
                    }
                }
            });
        }
        
        if (confirmPasswordInput) {
            confirmPasswordInput.addEventListener('input', validatePasswordMatch);
        }
    });

    // Handle form submission
    document.getElementById('registerForm').addEventListener('submit', async function(e) {
        e.preventDefault();
        
        const form = e.target;
        const submitButton = form.querySelector('button[type="submit"]');
        const submitText = submitButton.querySelector('.register-button-text');
        const spinner = submitButton.querySelector('svg');
        
        // Run client-side validation
        if (!validatePassword()) {
            // Show error message
            const errorDiv = document.createElement('div');
            errorDiv.className = 'text-red-500 text-sm mt-2';
            errorDiv.textContent = 'Please ensure your password meets all requirements.';
            
            const existingError = document.getElementById('password-error');
            if (existingError) {
                existingError.remove();
            }
            errorDiv.id = 'password-error';
            document.getElementById('password').parentNode.appendChild(errorDiv);
            return false;
        }
        
        // Validate password match
        const password = form.password.value;
        const confirmPassword = form.password_confirmation.value;
        
        if (password !== confirmPassword) {
            const errorDiv = document.createElement('div');
            errorDiv.className = 'text-red-500 text-sm mt-2';
            errorDiv.textContent = 'Passwords do not match.';
            
            const existingError = document.getElementById('password-match-error');
            if (existingError) {
                existingError.remove();
            }
            errorDiv.id = 'password-match-error';
            document.getElementById('password_confirmation').parentNode.appendChild(errorDiv);
            return false;
        }
        
        try {
            // Disable the submit button and show loading state
            submitButton.disabled = true;
            if (submitText) submitText.textContent = '{{ __("auth.register.registering") }}';
            if (spinner) spinner.classList.remove('hidden');
            
            const formData = new FormData(form);
            const response = await fetch(form.action, {
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: formData
            });
            
            const data = await response.json();
            
            if (response.ok) {
                // Redirect to the provided URL on success
                window.location.href = data.redirect || '{{ route("dashboard", app()->getLocale()) }}';
            } else {
                // Handle errors
                if (data.errors) {
                    // Clear previous errors
                    const errorElements = document.querySelectorAll('.text-red-500');
                    errorElements.forEach(el => el.remove());
                    
                    // Show new errors
                    for (const [field, errors] of Object.entries(data.errors)) {
                        const input = document.querySelector(`[name="${field}"]`);
                        if (input) {
                            const errorDiv = document.createElement('div');
                            errorDiv.className = 'text-red-500 text-sm mt-1';
                            errorDiv.textContent = errors[0];
                            input.parentNode.appendChild(errorDiv);
                        }
                    }
                    alert(data.message || 'An error occurred during registration');
                }
                // Re-enable the submit button
                submitButton.disabled = false;
                if (submitText) submitText.textContent = '{{ __("auth.register.register_button") }}';
                if (spinner) spinner.classList.add('hidden');
            }
        } catch (error) {
            console.error('Error:', error);
            alert('An unexpected error occurred. Please try again.');
            // Re-enable the submit button
            submitButton.disabled = false;
            if (submitText) submitText.textContent = '{{ __("auth.register.register_button") }}';
            if (spinner) spinner.classList.add('hidden');
        }
    });
    // Handle form submission
    document.getElementById('registerForm').addEventListener('submit', async function(e) {
        e.preventDefault();
        
        const form = e.target;
        const submitButton = form.querySelector('button[type="submit"]');
        const originalButtonText = submitButton.innerHTML;
        
        try {
            // Disable the submit button and show loading state
            submitButton.disabled = true;
            submitButton.innerHTML = `
                <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white inline" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                {{ __('auth.register.creating_account') }}
            `;
            
            const formData = new FormData(form);
            const response = await fetch(form.action, {
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: formData
            });
            
            const data = await response.json();
            
            if (response.ok) {
                // Redirect to the provided URL on success
                window.location.href = data.redirect;
            } else {
                // Handle errors
                if (data.errors) {
                    // Clear previous errors
                    const errorElements = form.querySelectorAll('.error-message');
                    errorElements.forEach(el => el.remove());
                    
                    // Add new errors
                    for (const [field, messages] of Object.entries(data.errors)) {
                        const input = form.querySelector(`[name="${field}"]`);
                        if (input) {
                            const errorDiv = document.createElement('p');
                            errorDiv.className = 'text-red-500 text-xs mt-1 error-message';
                            errorDiv.textContent = messages[0];
                            input.parentNode.insertBefore(errorDiv, input.nextSibling);
                        }
                    }
                } else if (data.message) {
                    alert(data.message);
                }
                
                // Re-enable the submit button
                submitButton.disabled = false;
                submitButton.innerHTML = originalButtonText;
            }
        } catch (error) {
            console.error('Registration error:', error);
            alert('An error occurred. Please try again.');
            // Re-enable the submit button
            submitButton.disabled = false;
            submitButton.innerHTML = originalButtonText;
        }
    });
    
    // Toggle password visibility
    function togglePasswordVisibility(inputId) {
        const input = document.getElementById(inputId);
        const icon = document.querySelector(`[data-toggle="${inputId}"]`);
        
        if (input.type === 'password') {
            input.type = 'text';
            icon.innerHTML = `
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
                </svg>
            `;
        } else {
            input.type = 'password';
            icon.innerHTML = `
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                </svg>
            `;
        }
    }
</script>
@endpush
