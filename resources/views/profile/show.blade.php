@extends('layouts.app')

@section('title', __('profile.title'))

@section('content')
<!-- Status Messages -->
@if (session('status'))
    <div class="container mx-auto px-4 py-4">
        <div class="max-w-4xl mx-auto">
            <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-4 mb-6">
                <div class="flex items-center">
                    <svg class="w-5 h-5 text-blue-600 dark:text-blue-400 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <p class="text-blue-700 dark:text-blue-300">{{ session('status') }}</p>
                </div>
            </div>
        </div>
    </div>
@endif

<!-- Error Messages -->
@if(request('error') == 'invalid_verification_link')
    <div class="container mx-auto px-4 py-4">
        <div class="max-w-4xl mx-auto">
            <div class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg p-4 mb-6">
                <div class="flex items-center">
                    <svg class="w-5 h-5 text-red-600 dark:text-red-400 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <div>
                        <p class="text-red-700 dark:text-red-300 font-medium">{{ __('profile.invalid_verification_link') }}</p>
                        <p class="text-red-600 dark:text-red-400 text-sm mt-1">{{ __('profile.invalid_verification_link_message') }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endif

<div class="container mx-auto px-4 py-8">
    <div class="max-w-4xl mx-auto">
        <!-- Profile Header Card -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg overflow-hidden border border-gray-200 dark:border-gray-700">
            <!-- Header Background -->
            <div class="bg-gradient-to-r from-blue-500 to-blue-600 h-32"></div>
            
            <!-- Profile Info -->
            <div class="relative px-6 pb-6">
                <!-- Avatar -->
                <div class="absolute -top-16">
                    <div class="relative">
                        <img class="h-32 w-32 rounded-full border-4 border-white dark:border-gray-800 shadow-xl" 
                             src="{{ $user->profile_photo_url ?? 'https://ui-avatars.com/api/?name=' . urlencode($user->first_name . ' ' . $user->last_name) . '&size=128&background=2563eb&color=fff' }}" 
                             alt="{{ $user->first_name }} {{ $user->last_name }}">
                        <div class="absolute bottom-2 right-2 h-6 w-6 bg-green-400 border-2 border-white dark:border-gray-800 rounded-full"></div>
                    </div>
                </div>
                
                <!-- User Details -->
                <div class="pt-20">
                    <h1 class="text-3xl font-bold text-gray-900 dark:text-white">
                        {{ $user->first_name }} {{ $user->last_name }}
                    </h1>
                    <p class="text-gray-600 dark:text-gray-400 mt-1">{{ $user->email }}</p>
                    <div class="flex items-center mt-2 space-x-4">
                        <span class="text-sm text-gray-500 dark:text-gray-400">
                            <i class="fas fa-calendar-alt mr-1"></i>
                            {{ __('profile.member_since') }} {{ $user->created_at->format('F Y') }}
                        </span>
                        @if($user->email_verified_at)
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">
                                <i class="fas fa-check-circle mr-1"></i>
                                {{ __('profile.verified') }}
                            </span>
                        @else
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200">
                                <i class="fas fa-exclamation-triangle mr-1"></i>
                                {{ __('profile.unverified') }}
                            </span>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Email Verification Alert -->
        @if(!$user->email_verified_at)
            <div class="mt-6 bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-xl p-6">
                <div class="flex items-start">
                    <div class="flex-shrink-0">
                        <i class="fas fa-info-circle text-blue-500 text-xl"></i>
                    </div>
                    <div class="ml-4 flex-1">
                        <h3 class="text-lg font-medium text-blue-900 dark:text-blue-100">{{ __('profile.verify_your_email_address') }}</h3>
                        <div class="mt-2 text-blue-700 dark:text-blue-300">
                            <p>{{ __('profile.verify_email_message') }}</p>
                            @if(session('status') && str_contains(session('status'), 'forum'))
                                <p class="mt-2 font-medium">{{ __('profile.forum_verification_required') }}</p>
                            @endif
                        </div>
                        <div class="mt-4">
                            <form method="POST" action="{{ route('verification.send', ['locale' => app()->getLocale()]) }}" class="inline">
                                @csrf
                                <button type="submit" class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition-colors duration-200">
                                    <i class="fas fa-paper-plane mr-2"></i>
                                    {{ __('profile.resend_verification_email') }}
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <!-- Account Information Card -->
        <div class="mt-6 bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-200 dark:border-gray-700">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                <h2 class="text-xl font-semibold text-gray-900 dark:text-white flex items-center">
                    <i class="fas fa-user-circle mr-2 text-blue-500"></i>
                    {{ __('profile.account_information') }}
                </h2>
            </div>
            
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-4">
                        <h4 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">{{ __('profile.first_name') }}</h4>
                        <p class="text-lg font-medium text-gray-900 dark:text-white">{{ $user->first_name }}</p>
                    </div>
                    <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-4">
                        <h4 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">{{ __('profile.last_name') }}</h4>
                        <p class="text-lg font-medium text-gray-900 dark:text-white">{{ $user->last_name }}</p>
                    </div>
                    <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-4">
                        <h4 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">{{ __('profile.email') }}</h4>
                        <p class="text-lg font-medium text-gray-900 dark:text-white">{{ $user->email }}</p>
                    </div>
                    @if($user->email_verified_at)
                        <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-4">
                            <h4 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">{{ __('profile.email_verified') }}</h4>
                            <p class="text-lg font-medium text-gray-900 dark:text-white">{{ $user->email_verified_at->format('M d, Y') }}</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Actions -->
        <div class="mt-6 flex flex-col sm:flex-row sm:justify-end sm:space-x-3 space-y-3 sm:space-y-0">
            @can('isAdmin')
                <a href="{{ route('admin.portal', ['locale' => app()->getLocale()]) }}" 
                   class="inline-flex items-center justify-center sm:justify-start sm:w-auto w-full px-6 py-3 bg-purple-600 hover:bg-purple-700 text-white font-medium rounded-lg shadow-md hover:shadow-lg transition-all duration-200 transform hover:scale-105">
                    <i class="fas fa-briefcase mr-2"></i>
                    {{ __('profile.business') }}
                </a>
            @endcan
            <a href="{{ route('profile.password.request', ['locale' => app()->getLocale()]) }}" 
               class="inline-flex items-center justify-center sm:justify-start sm:w-auto w-full px-6 py-3 bg-gray-600 hover:bg-gray-700 text-white font-medium rounded-lg shadow-md hover:shadow-lg transition-all duration-200 transform hover:scale-105">
                <i class="fas fa-key mr-2"></i>
                {{ __('profile.change_password') }}
            </a>
            <form method="POST" action="{{ route('logout', ['locale' => app()->getLocale()]) }}" class="inline">
                @csrf
                <button type="submit" 
                        class="inline-flex items-center justify-center sm:justify-start sm:w-auto w-full px-6 py-3 bg-red-500 hover:bg-red-600 text-white font-medium rounded-lg shadow-md hover:shadow-lg transition-all duration-200 transform hover:scale-105">
                    <i class="fas fa-sign-out-alt mr-2"></i>
                    {{ __('profile.logout') }}
                </button>
            </form>
            <a href="{{ route('profile.edit', ['locale' => app()->getLocale()]) }}" 
               class="inline-flex items-center justify-center sm:justify-start sm:w-auto w-full px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg shadow-md hover:shadow-lg transition-all duration-200 transform hover:scale-105">
                <i class="fas fa-edit mr-2"></i>
                {{ __('profile.edit_profile') }}
            </a>
        </div>
    </div>
</div>
@endsection
