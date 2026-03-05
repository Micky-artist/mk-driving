@extends('layouts.app')

@push('styles')
<style>
    /* Ensure the login form is properly spaced below the navbar */
    .login-container {
        min-height: calc(100vh - 5rem); /* Account for navbar height */
        padding-top: 0.5rem; /* Space for fixed navbar */
    }
</style>
@endpush

@section('content')
@section('content')
<div class="w-full min-h-screen flex flex-col relative overflow-hidden bg-gradient-to-br from-white to-gray-50 dark:from-gray-900 dark:to-gray-800 login-container py-8">
    
    <div class="flex-grow flex items-center justify-center px-1 relative z-10">
        <div class="w-full max-w-md mx-auto">
            <div class="bg-white/95 dark:bg-gray-800/95 backdrop-blur-lg rounded-2xl p-8 shadow-xl dark:shadow-2xl border border-white/20 dark:border-gray-700 hover:shadow-2xl dark:hover:shadow-3xl transition-all duration-300 hover:-translate-y-1">
                        <div class="text-center pb-4">
                            <div class="flex justify-center mb-6">
                                <a href="{{ route('home', ['locale' => app()->getLocale()]) }}" class="flex items-center space-x-1">
                                    <img src="{{ asset('logo.png') }}" alt="MK Driving School Logo" class="h-16 w-16 rounded-lg shadow-md"
                                        onerror="this.onerror=null; this.src='{{ asset('images/logo.png') }}'">
                                </a>
                            </div>
                            <h1 class="text-4xl font-extrabold text-gray-900 dark:text-white mb-3">
                                {{ __('auth.login.title') }}
                            </h1>
                            <p class="text-gray-600 dark:text-gray-300 text-base font-medium">
                                {{ __('auth.login.subtitle') }}
                            </p>
                        </div>

                        <div class="space-y-5">
                            <!-- Session Status -->
                            @if (session('status'))
                                @php
                                    $statusMessage = session('status');
                                    $passwordResetMessage = __('auth.password_reset_success.message');
                                    $isPasswordReset = $statusMessage === $passwordResetMessage;
                                    $isForumLogin = $statusMessage === __('auth.forum_login_required');
                                @endphp
                                
                                <div class="mb-6 p-4 {{ $isPasswordReset ? 'bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800' : 'bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800' }} rounded-xl">
                                    <div class="flex items-start">
                                        <div class="flex-shrink-0">
                                            @if ($isPasswordReset)
                                                <svg class="h-6 w-6 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                </svg>
                                            @else
                                                <svg class="h-6 w-6 text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                </svg>
                                            @endif
                                        </div>
                                        <div class="ml-3">
                                            <h3 class="text-lg font-medium {{ $isPasswordReset ? 'text-green-800 dark:text-green-200' : 'text-blue-800 dark:text-blue-200' }}">
                                                {{ $isPasswordReset ? __('auth.password_reset_success.title') : __('auth.login_required') }}
                                            </h3>
                                            <div class="mt-1 text-sm {{ $isPasswordReset ? 'text-green-700 dark:text-green-300' : 'text-blue-700 dark:text-blue-300' }}">
                                                {{ $statusMessage }}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endif

                            <!-- Google Sign In Button -->
                            <div class="mb-6">
                                <a href="{{ route('google.login') }}" class="btn-google w-full flex items-center justify-center space-x-3 py-2.5 px-4">
                                    <img src="{{ asset('images/google-icon.png') }}" alt="Google" class="w-8 h-8">
                                    <span class="text-lg font-medium">{{ __('auth.continue_with_google') }}</span>
                                </a>
                            </div>

                            <div class="divider">
                                {{ __('auth.or_sign_in_with_email') }}
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
                                            class="w-full pl-12 pr-4 py-4 rounded-xl border-2 border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white font-medium text-base transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent hover:border-gray-400 dark:hover:border-gray-500"
                                        >
                                        <div class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 dark:text-gray-500">
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
                                                class="w-full pl-12 pr-12 py-4 rounded-xl border-2 border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white font-medium text-base transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent hover:border-gray-400 dark:hover:border-gray-500"
                                            >
                                            <div class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 dark:text-gray-500">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                                                </svg>
                                            </div>
                                        <button 
                                            type="button" 
                                            onclick="togglePasswordVisibility()" 
                                            class="absolute inset-y-0 right-0 pr-4 flex items-center text-gray-400 dark:text-gray-500 hover:text-gray-600 dark:hover:text-gray-400 focus:outline-none transition-all duration-200" 
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
                                                class="h-5 w-5 text-blue-600 focus:ring-blue-500 border-2 border-gray-300 dark:border-gray-600 rounded-lg transition-all duration-200 focus:ring-2 focus:ring-offset-2 focus:ring-offset-white dark:focus:ring-offset-gray-800 cursor-pointer"
                                            >
                                        </div>
                                        <label for="remember_me" class="ml-3 block text-sm font-medium text-gray-700 dark:text-gray-300 cursor-pointer hover:text-gray-900 dark:hover:text-white transition-colors">
                                            {{ __('auth.remember_me') }}
                                        </label>
                                    </div>

                                    <div class="text-right">
                                        <a 
                                            href="{{ route('password.request', ['locale' => app()->getLocale()]) }}" 
                                            class="text-sm font-semibold text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300 hover:underline transition-colors"
                                        >
                                            {{ __('auth.login.forgot_password') }}
                                        </a>
                                    </div>
                                </div>

                                <div>
                                    <button type="submit" class="w-full py-5 px-8 rounded-xl bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white font-bold text-lg uppercase tracking-wide transition-all duration-300 transform hover:-translate-y-1 shadow-lg hover:shadow-xl focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 focus:ring-offset-white dark:focus:ring-offset-gray-800">
                                    <span class="flex items-center justify-center">
                                        <span class="login-button-text">{{ __('auth.login.login_button') }}</span>
                                        <svg class="animate-spin -mr-1 ml-2 h-4 w-4 text-white hidden" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                        </svg>
                                    </span>
                                </button>
                                </div>

                                <div class="pt-6 text-center">
                                    <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">
                                        {{ __('auth.login.no_account') }}
                                    </p>
                                    <a 
                                        href="{{ route('register', ['locale' => app()->getLocale()]) }}" 
                                        class="inline-flex items-center justify-center w-full px-6 py-4 text-lg font-bold text-blue-600 dark:text-blue-400 bg-white dark:bg-gray-800 border-2 border-blue-500 dark:border-blue-400 hover:bg-blue-50 dark:hover:bg-gray-700 rounded-xl shadow-lg hover:shadow-xl transform hover:-translate-y-1 transition-all duration-200"
                                    >
                                        <i class="fas fa-user-plus mr-3 text-xl"></i>
                                        {{ __('auth.login.sign_up') }}
                                    </a>
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
