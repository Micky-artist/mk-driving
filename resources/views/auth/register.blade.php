@extends('layouts.app')

@section('content')
<div class="w-full min-h-screen flex flex-col relative overflow-x-hidden">
    <div class="flex-grow pt-20 sm:pt-24 relative z-10">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-center px-4 py-8 md:py-16">
                <div class="w-full max-w-md mx-auto">
                    <div class="w-full max-w-md mx-auto shadow-lg border-0 mt-10 bg-white rounded-lg overflow-hidden">
                        <div class="text-center pb-6 pt-8 px-8">
                            <div class="flex justify-center mb-6">
                                <img src="{{ asset('logo.png') }}" alt="Logo" class="h-24 w-24 object-cover">
                            </div>
                            <h1 class="text-2xl font-semibold text-slate-800 mb-2">
                                {{ __('auth.register.title') }}
                            </h1>
                            <p class="text-gray-600 text-sm">
                                {{ __('auth.register.subtitle') }}
                            </p>
                        </div>

                        <div class="px-8 pb-8">
                            <!-- Validation Errors -->
                            @if ($errors->any())
                                <div class="mb-4 p-4 bg-red-50 rounded-md">
                                    <div class="font-medium text-red-600">
                                        {{ __('auth.whoops') }}
                                    </div>
                                    <ul class="mt-2 list-disc list-inside text-sm text-red-600">
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif

                            <form id="registerForm" method="POST" action="{{ route('register', app()->getLocale()) }}" class="space-y-4">
                                @csrf

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <!-- First Name -->
                                    <div>
                                        <input 
                                            id="first_name" 
                                            name="first_name" 
                                            type="text" 
                                            value="{{ old('first_name') }}" 
                                            placeholder="{{ __('auth.register.firstName') }}"
                                            class="block w-full h-12 px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                                            required 
                                            autofocus
                                        />
                                    @error('first_name')
                                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Last Name -->
                                <div>
                                    <input 
                                        id="last_name" 
                                        name="last_name" 
                                        type="text" 
                                        value="{{ old('last_name') }}" 
                                        required 
                                        placeholder="{{ __('auth.register.lastName') }}"
                                        class="block w-full h-12 px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                                    />
                                    @error('last_name')
                                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>

                            <!-- Email -->
                            <div>
                                <input 
                                    id="email" 
                                    name="email" 
                                    type="email" 
                                    value="{{ old('email') }}" 
                                    required 
                                    placeholder="{{ __('auth.register.email') }}"
                                    class="block w-full h-12 px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                                />
                                @error('email')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Password -->
                            <div class="relative">
                                <input 
                                    id="password" 
                                    name="password" 
                                    type="password" 
                                    required 
                                    autocomplete="new-password"
                                    placeholder="{{ __('auth.register.password') }}"
                                    class="block w-full h-12 px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm pr-10"
                                />
                                <button type="button" onclick="togglePasswordVisibility('password')" class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-600 focus:outline-none" data-toggle="password">
                                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                    </svg>
                                </button>
                                @error('password')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Password Confirmation -->
                            <div class="relative">
                                <input 
                                    id="password_confirmation" 
                                    name="password_confirmation" 
                                    type="password" 
                                    required 
                                    autocomplete="new-password"
                                    placeholder="{{ __('auth.register.confirm_password') }}"
                                    class="block w-full h-12 px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm pr-10 mt-4"
                                />
                                <button type="button" onclick="togglePasswordVisibility('password_confirmation')" class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-600 focus:outline-none" data-toggle="password_confirmation">
                                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                    </svg>
                                </button>
                            </div>

                            <!-- API Error -->
                            @if(session('error'))
                                <p class="text-red-500 text-sm text-center">{{ session('error') }}</p>
                            @endif

                            <div>
                                <button type="submit" class="w-full h-12 bg-slate-800 hover:bg-slate-700 text-white font-medium rounded-lg mt-6 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-slate-800">
                                    {{ __('auth.register.register_button') }}
                                </button>
                            </div>
                        </form>
                        
                        <div class="text-center mt-6 pb-6">
                            <p class="text-sm text-gray-600">
                                {{ __('auth.register.have_account') }}
                                <a href="{{ route('login', ['locale' => app()->getLocale()]) }}" class="text-slate-800 font-medium hover:underline">
                                    {{ __('auth.register.sign_in') }}
                                </a>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
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
