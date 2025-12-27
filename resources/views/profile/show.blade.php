@extends('layouts.app')

@section('title', __('My Profile'))

@section('content')
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
                            {{ __('Member since') }} {{ $user->created_at->format('F Y') }}
                        </span>
                        @if($user->email_verified_at)
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">
                                <i class="fas fa-check-circle mr-1"></i>
                                {{ __('Verified') }}
                            </span>
                        @else
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200">
                                <i class="fas fa-exclamation-triangle mr-1"></i>
                                {{ __('Unverified') }}
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
                        <h3 class="text-lg font-medium text-blue-900 dark:text-blue-100">{{ __('Verify your email address') }}</h3>
                        <div class="mt-2 text-blue-700 dark:text-blue-300">
                            <p>{{ __('Please check your email and click the verification link to unlock all features and ensure account security.') }}</p>
                        </div>
                        <div class="mt-4">
                            <form method="POST" action="{{ route('verification.send', ['locale' => app()->getLocale()]) }}" class="inline">
                                @csrf
                                <button type="submit" class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition-colors duration-200">
                                    <i class="fas fa-paper-plane mr-2"></i>
                                    {{ __('Resend verification email') }}
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
                    {{ __('Account Information') }}
                </h2>
            </div>
            
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-4">
                        <h4 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">{{ __('First Name') }}</h4>
                        <p class="text-lg font-medium text-gray-900 dark:text-white">{{ $user->first_name }}</p>
                    </div>
                    <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-4">
                        <h4 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">{{ __('Last Name') }}</h4>
                        <p class="text-lg font-medium text-gray-900 dark:text-white">{{ $user->last_name }}</p>
                    </div>
                    <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-4">
                        <h4 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">{{ __('Email') }}</h4>
                        <p class="text-lg font-medium text-gray-900 dark:text-white">{{ $user->email }}</p>
                    </div>
                    @if($user->email_verified_at)
                        <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-4">
                            <h4 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">{{ __('Email Verified') }}</h4>
                            <p class="text-lg font-medium text-gray-900 dark:text-white">{{ $user->email_verified_at->format('M d, Y') }}</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Actions -->
        <div class="mt-6 flex justify-end space-x-3">
            @can('isAdmin')
                <a href="{{ route('admin.portal', ['locale' => app()->getLocale()]) }}" 
                   class="inline-flex items-center px-6 py-3 bg-purple-600 hover:bg-purple-700 text-white font-medium rounded-lg shadow-md hover:shadow-lg transition-all duration-200 transform hover:scale-105">
                    <i class="fas fa-briefcase mr-2"></i>
                    {{ __('Business') }}
                </a>
            @endcan
            <form method="POST" action="{{ route('logout', ['locale' => app()->getLocale()]) }}" class="inline">
                @csrf
                <button type="submit" 
                        class="inline-flex items-center px-6 py-3 bg-red-500 hover:bg-red-600 text-white font-medium rounded-lg shadow-md hover:shadow-lg transition-all duration-200 transform hover:scale-105">
                    <i class="fas fa-sign-out-alt mr-2"></i>
                    {{ __('Logout') }}
                </button>
            </form>
            <a href="{{ route('profile.edit', ['locale' => app()->getLocale()]) }}" 
               class="inline-flex items-center px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg shadow-md hover:shadow-lg transition-all duration-200 transform hover:scale-105">
                <i class="fas fa-edit mr-2"></i>
                {{ __('Edit Profile') }}
            </a>
        </div>
    </div>
</div>
@endsection
