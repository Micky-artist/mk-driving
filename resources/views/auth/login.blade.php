@extends('layouts.app')

@section('content')
<div class="w-full min-h-screen flex flex-col relative overflow-x-hidden">
    @include('components.navbar')
    
    <div class="flex-grow pt-20 sm:pt-24 relative z-10">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-center px-4 py-8 md:py-16">
                <div class="w-full max-w-md mx-auto">
                    <div class="w-full max-w-md mx-auto shadow-lg mt-10 bg-white rounded-lg overflow-hidden border border-gray-100">
                        <div class="text-center pb-6 pt-8 px-8">
                            <div class="flex justify-center mb-6">
                                <img 
                                    src="{{ asset('logo.png') }}" 
                                    alt="Logo" 
                                    class="h-24 w-24 object-cover"
                                    width="100"
                                    height="100"
                                >
                            </div>
                            <h1 class="text-2xl font-semibold text-slate-800 mb-2">
                                {{ __('auth.login.title') }}
                            </h1>
                            <p class="text-gray-600 text-sm">
                                {{ __('auth.login.subtitle') }}
                            </p>
                        </div>

                        <div class="px-8 pb-8">
                            <!-- Session Status -->
                            @if (session('status'))
                                <div class="mb-4 text-sm font-medium text-green-600">
                                    {{ session('status') }}
                                </div>
                            @endif

                            <form id="loginForm" method="POST" action="{{ route('login', app()->getLocale()) }}" class="space-y-4">
                                @csrf

                                <!-- Email -->
                                <div>
                                    <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username" placeholder="{{ __('auth.login.email') }}" class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-[#035b96] focus:border-[#035b96] sm:text-sm transition-colors">
                                    @error('email')
                                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Password -->
                                <div class="relative">
                                    <div class="relative">
                                        <input id="password" type="password" name="password" required autocomplete="current-password" placeholder="{{ __('auth.login.password') }}" class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-[#035b96] focus:border-[#035b96] sm:text-sm transition-colors pr-10">
                                        <button type="button" onclick="togglePasswordVisibility()" class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-600 focus:outline-none" tabindex="-1">
                                            <svg id="eye-icon" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path id="eye-open" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                <path id="eye-open-2" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                <path id="eye-closed" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" style="display: none;" />
                                            </svg>
                                        </button>
                                    </div>
                                    @error('password')
                                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Remember Me -->
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center">
                                        <input id="remember_me" type="checkbox" name="remember" class="h-4 w-4 text-[#035b96] focus:ring-[#035b96] border-gray-300 rounded transition-colors">
                                        <label for="remember_me" class="ml-2 block text-sm text-gray-700">
                                            {{ __('auth.remember_me') }}
                                        </label>
                                    </div>

                                    @if (Route::has('password.request'))
                                        <a href="{{ route('password.request') }}" class="text-sm text-indigo-600 hover:text-indigo-500">
                                            {{ __('auth.login.forgot_password') }}
                                        </a>
                                    @endif
                                </div>

                                <div>
                                    <button type="submit" class="w-full flex justify-center py-3 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-[#035b96] hover:bg-[#023047] focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#035b96] transition-colors">
                                        {{ __('auth.login.login_button') }}
                                    </button>
                                </div>

                                <div class="text-center text-sm text-gray-700">
                                    {{ __('auth.login.no_account') }}
                                    <a href="{{ route('register', ['locale' => app()->getLocale()]) }}" class="font-medium text-[#035b96] hover:text-[#023047] transition-colors">
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
@push('scripts')
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
            submitButton.innerHTML = `
                <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white inline" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                {{ __('auth.login.logging_in') }}
            `;
            
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
                submitButton.innerHTML = originalButtonText;
            }
        } catch (error) {
            console.error('Login error:', error);
            alert('An error occurred. Please try again.');
            // Re-enable the submit button
            submitButton.disabled = false;
            submitButton.innerHTML = originalButtonText;
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
