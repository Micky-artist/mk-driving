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
        --card-bg: rgba(255, 255, 255, 0.95);
        --card-border: rgba(255, 255, 255, 0.2);
        --card-shadow: 0 10px 30px rgba(0, 0, 0, 0.15), 0 0 0 1px rgba(0, 0, 0, 0.05);
        --card-hover-shadow: 0 20px 40px rgba(0, 0, 0, 0.2), 0 0 0 1px rgba(0, 0, 0, 0.1);
    }

    @media (prefers-color-scheme: dark) {
        :root {
            --primary-color: #2563eb;
            --primary-hover: #1d4ed8;
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
        box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.2);
        border-color: var(--primary-color);
        outline: none;
    }
    
    .btn-primary {
        width: 100%;
        padding: 1.25rem 2rem;
        border-radius: 0.75rem;
        background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
        color: white;
        font-weight: 700;
        font-size: 1.125rem;
        letter-spacing: 0.5px;
        text-transform: uppercase;
        border: 2px solid rgba(255, 255, 255, 0.2);
        box-shadow: 0 10px 20px -5px rgba(37, 99, 235, 0.3);
        transition: all 0.3s ease-out;
        cursor: pointer;
    }
    
    .btn-primary:hover:not(:disabled) {
        transform: translateY(-2px);
        box-shadow: 0 15px 30px -5px rgba(37, 99, 235, 0.4);
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
    <input type="hidden" id="returnTo" value="{{ request()->input('return_to') }}">
    
    <div class="flex-grow flex items-center justify-center px-4 py-8 relative z-10">
        <div class="w-full max-w-md mx-auto">
            <div class="login-card rounded-2xl p-8">
                <div class="text-center pb-4">
                    <div class="flex justify-center mb-6">
                        <a href="{{ route('home', ['locale' => app()->getLocale()]) }}" class="flex items-center space-x-1">
                            <img src="{{ asset('logo.png') }}" alt="MK Driving School Logo" class="h-16 w-16 rounded-lg shadow-md"
                                onerror="this.onerror=null; this.src='{{ asset('images/logo.png') }}'">
                        </a>
                    </div>
                    <h1 class="text-4xl font-extrabold text-gray-900 dark:text-white mb-3">
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
                        
                        <div class="mb-4">
                            <!-- Full Name -->
                            <div class="form-group">
                                <div class="relative">
                                    <input 
                                        id="name" 
                                        name="name" 
                                        type="text" 
                                        value="{{ old('name') }}" 
                                        placeholder="{{ __('auth.register.name') }}"
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
                                @error('name')
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
                                    <div id="password-strength-meter" class="h-full bg-gray-400 rounded-full transition-all duration-300 w-0"></div>
                                </div>
                                <span id="password-strength-text" class="text-xs font-medium ml-2 text-gray-500 dark:text-gray-400">{{ __('auth.password_requirements.strength.weak') }}</span>
                                </div>
                                
                                <div id="password-requirements" class="text-xs text-gray-500 dark:text-gray-400 space-y-1 mt-2">
                                    <p class="font-medium text-sm mb-1">{{ __('auth.password_requirements.title') }}</p>
                                    <div class="flex items-center" id="req-length">
                                        <svg class="w-3.5 h-3.5 mr-1.5 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                        </svg>
                                        <span>{{ __('auth.password_requirements.length') }}</span>
                                    </div>
                                    <div class="flex items-center" id="req-letter">
                                        <svg class="w-3.5 h-3.5 mr-1.5 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                        </svg>
                                        <span>{{ __('auth.letter_only') }}</span>
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
                                    <span>{{ __('auth.mismatch') }}</span>
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
                            <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">
                                {{ __('auth.register.have_account') }}
                            </p>
                            <a 
                                href="{{ route('login', app()->getLocale()) }}" 
                                class="inline-flex items-center justify-center w-full px-6 py-4 text-lg font-bold text-blue-600 dark:text-blue-400 bg-white dark:bg-gray-800 border-2 border-blue-500 dark:border-blue-400 hover:bg-blue-50 dark:hover:bg-gray-700 rounded-xl shadow-lg hover:shadow-xl transform hover:-translate-y-1 transition-all duration-200"
                            >
                                <i class="fas fa-sign-in-alt mr-3 text-xl"></i>
                                {{ __('auth.register.sign_in') }}
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Account exists message (initially hidden) -->
<div id="accountExistsMessage" class="mt-4 p-4 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-md hidden">
    <div class="flex">
        <div class="flex-shrink-0">
            <svg class="h-5 w-5 text-red-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.28 7.22a.75.75 0 00-1.06 1.06L8.94 10l-1.72 1.72a.75.75 0 101.06 1.06L10 11.06l1.72 1.72a.75.75 0 101.06-1.06L11.06 10l1.72-1.72a.75.75 0 00-1.06-1.06L10 8.94 8.28 7.22z" clip-rule="evenodd" />
            </svg>
        </div>
        <div class="ml-3">
            <p class="text-sm text-red-700 dark:text-red-300">
                {{ __('auth.register.account_exists_message', ['email' => '']) }}
                <a href="{{ route('login', app()->getLocale()) }}" class="font-medium text-blue-600 dark:text-blue-400 hover:text-blue-500 dark:hover:text-blue-300">
                    {{ __('auth.register.sign_in_instead') }}
                </a>
            </p>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script src="{{ asset('js/google-auth.js') }}"></script>
<script>
    let hasUserTyped = false;
    // Register Form JavaScript
document.addEventListener('DOMContentLoaded', function() {
    // Get the form element
    const form = document.getElementById('registerForm');
    if (!form) return;

    // Remove any existing event listeners to prevent duplicates
    const newForm = form.cloneNode(true);
    form.parentNode.replaceChild(newForm, form);
    const formToUse = document.getElementById('registerForm');

    // Initialize password validation UI
    const passwordInput = document.getElementById('password');
    const confirmPasswordInput = document.getElementById('password_confirmation');
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
    
    // Show validation UI when typing
    if (password.length > 0) {
        if (!hasUserTyped) {
            hasUserTyped = true;
            validationDiv.classList.remove('hidden');
        }
    } else {
        validationDiv.classList.add('hidden');
        hasUserTyped = false;
        const errorDiv = document.getElementById('password-error');
        if (errorDiv) errorDiv.remove();
        return false;
    }
    
    // Only check for minimum length and at least one letter
    const hasMinLength = password.length >= 6;
    const hasLetter = /[A-Za-z]/.test(password);
    
    // Update requirement indicators
    updateRequirement('length', hasMinLength);
    updateRequirement('letter', hasLetter);
    
    // Calculate password strength (simplified)
    let strength = 0;
    if (hasMinLength) strength += 1;
    if (hasLetter) strength += 1;
    
    // Update strength meter
    if (strengthMeter && strengthText) {
        const width = (strength / 2) * 100;
        strengthMeter.style.width = `${width}%`;
        
        // Set color and localized text based on strength
        if (strength <= 1) {
            strengthMeter.className = 'h-full bg-red-500 rounded-full transition-all duration-300';
            strengthText.textContent = '{{ __("auth.password_requirements.strength.weak") }}';
        } else {
            strengthMeter.className = 'h-full bg-green-500 rounded-full transition-all duration-300';
            strengthText.textContent = '{{ __("auth.password_requirements.strength.strong") }}';
        }
    }
    
    // Return true only if all requirements are met
    return hasMinLength && hasLetter;
}
    
    // Update requirement indicator
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
    
    // Validate password match
    function validatePasswordMatch() {
        const password = document.getElementById('password').value;
        const confirmPassword = document.getElementById('password_confirmation').value;
        const matchElement = document.getElementById('password-match');
        const mismatchElement = document.getElementById('password-mismatch');
        
        if (confirmPassword === '') {
            matchElement?.classList.add('hidden');
            mismatchElement?.classList.add('hidden');
            return;
        }
        
        if (password === confirmPassword) {
            matchElement?.classList.remove('hidden');
            mismatchElement?.classList.add('hidden');
        } else {
            matchElement?.classList.add('hidden');
            mismatchElement?.classList.remove('hidden');
        }
    }
    
    // Toggle password visibility
    function togglePasswordVisibility(targetId) {
        const input = document.getElementById(targetId);
        if (!input) return;
        
        const type = input.getAttribute('type') === 'password' ? 'text' : 'password';
        input.setAttribute('type', type);
        
        // Toggle icon
        const icon = document.getElementById(`eye-icon-${targetId}`);
        if (icon) {
            if (type === 'password') {
                icon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />';
            } else {
                icon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />';
            }
        }
    }
    
    // Show form error
    function showFormError(form, message) {
        // Remove any existing error messages
        const existingError = form.querySelector('.form-error-message');
        if (existingError) {
            existingError.remove();
        }
        
        // Create and show new error message
        const errorDiv = document.createElement('div');
        errorDiv.className = 'form-error-message p-3 bg-red-50 dark:bg-red-900/20 text-red-700 dark:text-red-300 rounded-lg text-sm text-center mb-4';
        errorDiv.textContent = message;
        form.insertBefore(errorDiv, form.firstChild);
        
        // Scroll to error
        errorDiv.scrollIntoView({ behavior: 'smooth', block: 'center' });
    }
    
    // Show user exists modal
    function showUserExistsModal(email) {
        const modal = document.getElementById('accountExistsModal');
        const messageElement = document.getElementById('accountExistsMessage');
        
        if (modal && messageElement) {
            // Set the message with the email
            const message = "{{ __('auth.register.account_exists_message', ['email' => '']) }}".replace(':email', email);
            messageElement.textContent = message;
            
            // Show the modal
            modal.classList.remove('hidden');
            
            // Focus on the first interactive element for accessibility
            const focusable = modal.querySelector('button, [href], [tabindex]:not([tabindex="-1"])');
            if (focusable) focusable.focus();
        }
    }
    
    // Initialize event listeners
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
    
    // Initialize toggle password buttons
    document.querySelectorAll('.toggle-password-btn').forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            const targetId = this.getAttribute('onclick')
                .replace('togglePasswordVisibility(\'', '')
                .replace('\')', '');
            togglePasswordVisibility(targetId);
        });
    });
    
    // Form submission handler
    formToUse.addEventListener('submit', async function(e) {
        e.preventDefault();
        
        const submitButton = this.querySelector('button[type="submit"]');
        const buttonText = submitButton.querySelector('#buttonText');
        const originalButtonText = buttonText ? buttonText.textContent : submitButton.textContent;
        
        try {
            // Validate password
            if (!validatePassword()) {
                showFormError(this, '{{ __("auth.letter_only") }}');
                return;
            }
            
            // Validate password match
            const password = this.querySelector('input[name="password"]').value;
            const confirmPassword = this.querySelector('input[name="password_confirmation"]').value;
            
            if (password !== confirmPassword) {
                showFormError(this, '{{ __("auth.mismatch") }}');
                return;
            }
            
            // Disable the submit button and show loading state
            submitButton.disabled = true;
            if (buttonText) {
                buttonText.textContent = '{{ __("auth.register.creating_account") }}';
            } else {
                submitButton.innerHTML = `
                    <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white inline" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    {{ __("auth.register.creating_account") }}
                `;
            }
            
            // Get form data
            const formData = new FormData(this);
            const email = this.querySelector('input[name="email"]').value;
            
            // Get return_to from URL if not in form data
            const returnTo = new URLSearchParams(window.location.search).get('return_to');
            if (returnTo) {
                formData.append('return_to', returnTo);
            }
            
            try {
                const response = await fetch(this.action, {
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
                    // Redirect on success
                    window.location.href = data.redirect || '{{ route("dashboard", app()->getLocale()) }}';
                } else {
                    // Handle validation errors
                    if (data.errors) {
                        // Handle email already exists error
                        if (data.errors.email && data.errors.email.includes('Konti isanzweho. Mujye aho binjirira.')) {
                            showUserExistsModal(email);
                        } else {
                            // Show first error message
                            const firstError = Object.values(data.errors)[0][0];
                            showFormError(this, firstError);
                        }
                    } else {
                        showFormError(this, data.message || '{{ __("auth.register.error_occurred") }}');
                    }
                }
            } catch (error) {
                console.error('Error:', error);
                showFormError(this, '{{ __("auth.register.error_occurred") }}');
            } finally {
                submitButton.disabled = false;
                if (buttonText) {
                    buttonText.textContent = originalButtonText;
                } else {
                    submitButton.innerHTML = originalButtonText;
                }
            }
        } catch (error) {
            console.error('Registration error:', error);
            showFormError(this, '{{ __("auth.register.error_occurred") }}');
            submitButton.disabled = false;
            if (buttonText) {
                buttonText.textContent = originalButtonText;
            } else {
                submitButton.innerHTML = originalButtonText;
            }
        }
    });
});

// Show account exists message (kept outside DOMContentLoaded as it might be called from other scripts)
function showUserExistsMessage(email) {
    const messageDiv = document.getElementById('accountExistsMessage');
    if (messageDiv) {
        const messageText = messageDiv.querySelector('p');
        if (messageText) {
            // Update the message with the email
            const baseMessage = "{{ __('auth.register.account_exists_message', ['email' => '%%EMAIL%%']) }}";
            messageText.textContent = baseMessage.replace('%%EMAIL%%', email);
            
            // Show the message
            messageDiv.classList.remove('hidden');
            
            // Scroll to the message
            messageDiv.scrollIntoView({ behavior: 'smooth', block: 'center' });
        }
    }
}
</script>
@endpush
