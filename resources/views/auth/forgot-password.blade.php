@extends('layouts.app')

@push('styles')
<style>
    .auth-card {
        max-width: 28rem;
        width: 100%;
    }
</style>
@endpush

@section('content')
<!-- Confirmation Dialog -->
<div id="confirmationDialog" class="fixed inset-0 bg-black/50 z-50 hidden items-center justify-center">
    <div class="bg-white dark:bg-gray-800 rounded-2xl p-8 max-w-md w-[90%] shadow-xl border border-gray-200 dark:border-gray-700 text-center">
        <div class="w-16 h-16 mx-auto mb-6 flex items-center justify-center rounded-full bg-green-100 dark:bg-green-900/20">
            <svg class="w-10 h-10 text-green-600 dark:text-green-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
            </svg>
        </div>
        <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-2">
            {{ __('auth.forgot_password_page.dialog_title') }}
        </h3>
        <p class="text-gray-600 dark:text-gray-300 mb-6">
            {{ __('auth.forgot_password_page.dialog_message') }} <span id="sentEmail" class="font-medium text-primary-600 dark:text-primary-400"></span>
        </p>
        <button onclick="closeConfirmationDialog()" class="w-auto px-8 py-3 rounded-xl bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white font-bold text-base transition-all duration-300 transform hover:-translate-y-1 shadow-lg hover:shadow-xl focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 focus:ring-offset-white dark:focus:ring-offset-gray-800">
            {{ __('auth.forgot_password_page.dialog_button') }}
        </button>
    </div>
</div>

<div class="w-full min-h-screen flex flex-col relative overflow-hidden bg-gradient-to-br from-white to-gray-50 dark:from-gray-900 dark:to-gray-800">
    <div class="flex-grow flex items-center justify-center px-1 relative z-10 py-8">
        <div class="w-full max-w-md mx-auto">
            <div class="bg-white/95 dark:bg-gray-800/95 backdrop-blur-lg rounded-2xl p-8 shadow-xl dark:shadow-2xl border border-white/20 dark:border-gray-700 hover:shadow-2xl dark:hover:shadow-3xl transition-all duration-300 hover:-translate-y-1 auth-card">
                <div class="text-center pb-4">
                    <div class="flex justify-center mb-6">
                        <a href="{{ route('home', app()->getLocale()) }}" class="auth-logo">
                            <img src="{{ asset('images/logo.png') }}" alt="{{ config('app.name') }}" class="h-16 w-auto">
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

                <form id="forgotPasswordForm" method="POST" action="{{ route('password.email', app()->getLocale()) }}" class="space-y-4">
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
                                class="w-full px-4 py-3 pl-12 rounded-full border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200" 
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
                        <button type="submit" class="w-full py-5 px-6 rounded-xl bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white font-bold text-lg tracking-wide transition-all duration-300 transform hover:-translate-y-1 shadow-lg hover:shadow-xl focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 focus:ring-offset-white dark:focus:ring-offset-gray-800 disabled:opacity-60 disabled:cursor-not-allowed disabled:transform-none flex items-center justify-center">
                            {{ __('auth.forgot_password_page.submit_button') }}
                        </button>
                    </div>
                </form>

                <div class="mt-6 text-center">
                    <a href="{{ route('login', app()->getLocale()) }}" class="inline-flex items-center px-6 py-3 border-2 border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 font-medium rounded-xl hover:border-gray-400 dark:hover:border-gray-500 hover:text-gray-900 dark:hover:text-white transition-all duration-200">
                        {{ __('auth.forgot_password_page.back_to_login') }}
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@push('scripts')
<script>
    function showConfirmationDialog(email) {
        const dialog = document.getElementById('confirmationDialog');
        if (!dialog) return;
        
        // Update dialog content with localized strings
        const titleElement = dialog.querySelector('h3');
        const messageElement = dialog.querySelector('p');
        const buttonElement = dialog.querySelector('button');
        
        if (titleElement) {
            titleElement.textContent = '{{ __("auth.forgot_password_page.dialog_title") }}';
        }
        
        if (messageElement) {
            messageElement.innerHTML = '{{ __("auth.forgot_password_page.dialog_message") }} ' + 
                '<span id="sentEmail" class="font-medium text-primary-600 dark:text-primary-400">' + 
                email + 
                '</span>';
        }
        
        if (buttonElement) {
            buttonElement.textContent = '{{ __("auth.forgot_password_page.dialog_button") }}';
        }
        
        // Show the dialog
        dialog.classList.add('active');
    }
    
    function closeConfirmationDialog() {
        document.getElementById('confirmationDialog').classList.remove('active');
    }
    
    // Check URL parameters for email already sent state
    document.addEventListener('DOMContentLoaded', function() {
        const urlParams = new URLSearchParams(window.location.search);
        const emailSent = urlParams.get('email_sent');
        const email = urlParams.get('email');
        
        if (emailSent && email) {
            // Show email already sent confirmation
            showConfirmationDialog(email);
        }
    });
    
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.getElementById('forgotPasswordForm');
        
        if (form) {
            form.addEventListener('submit', async function(e) {
                e.preventDefault();
                
                const formData = new FormData(form);
                const submitButton = form.querySelector('button[type="submit"]');
                const originalButtonText = submitButton.innerHTML;
                
                try {
                    // Show loading state
                    submitButton.disabled = true;
                    submitButton.innerHTML = `
                        <svg class="animate-spin -ml-1 mr-2 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        {{ __('auth.forgot_password_page.sending') }}
                    `;
                    
                    const response = await fetch(form.action, {
                        method: 'POST',
                        headers: {
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'X-Requested-With': 'XMLHttpRequest'
                        },
                        body: formData
                    });
                    
                    const data = await response.json();
                    
                    if (response.ok && data.status === 'passwords.sent') {
                        // Clear any previous success messages
                        const previousSuccess = form.parentNode.querySelectorAll('.text-green-700');
                        previousSuccess.forEach(el => el.remove());
                        
                        // Clear any previous error messages
                        const previousErrors = form.parentNode.querySelectorAll('.error-message');
                        previousErrors.forEach(el => el.remove());
                        
                        // Show immediate success feedback
                        const successMessage = document.createElement('div');
                        successMessage.className = 'mb-4 p-4 text-sm text-green-700 bg-green-100 rounded-lg dark:bg-green-900 dark:text-green-200';
                        successMessage.setAttribute('role', 'alert');
                        successMessage.innerHTML = `
                            <div class="flex items-center">
                                <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 0 8 8 0 016 16zm3.707-8.293l-3.414 3.414-1.414 1.414-1.414-1.414L10 11.586l3.293 3.293a1 1 0 001.414 1.414L14.586 10z" clip-rule="evenodd"></path>
                                </svg>
                                {{ __('auth.forgot_password_page.success_message') }}
                            </div>
                        `;
                        form.parentNode.insertBefore(successMessage, form);
                        
                        // Show success dialog after a short delay
                        setTimeout(() => {
                            showConfirmationDialog(formData.get('email'));
                        }, 1000);
                    } else {
                        // Handle errors
                        if (data.errors) {
                            // Clear previous errors
                            const errorElements = form.querySelectorAll('.error-message');
                            errorElements.forEach(el => el.remove());
                            
                            // Show new errors
                            for (const [field, messages] of Object.entries(data.errors)) {
                                const input = form.querySelector(`[name="${field}"]`);
                                if (input) {
                                    const errorDiv = document.createElement('p');
                                    errorDiv.className = 'mt-1 text-sm text-red-600 dark:text-red-400 error-message';
                                    errorDiv.textContent = messages[0];
                                    input.parentNode.insertBefore(errorDiv, input.nextSibling);
                                }
                            }
                        }
                    }
                } catch (error) {
                    console.error('Error:', error);
                    // Fall back to normal form submission if AJAX fails
                    form.submit();
                } finally {
                    // Reset button state
                    submitButton.disabled = false;
                    submitButton.innerHTML = originalButtonText;
                }
            });
        }
        
        // Check if we have a success status and show the dialog (for non-AJAX submissions)
        @if(session('status') === 'passwords.sent' && session('email'))
            showConfirmationDialog('{{ session('email') }}');
        @endif
    });
</script>
@endpush

@endsection
