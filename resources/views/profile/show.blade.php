@extends('layouts.app')

@section('title', __('My Profile'))

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-3xl mx-auto">
        <div class="bg-white rounded-lg shadow-md overflow-hidden">
            <div class="bg-gray-50 px-6 py-4 border-b border-gray-200">
                <h2 class="text-2xl font-semibold text-gray-800">{{ __('My Profile') }}</h2>
            </div>
            
            <div class="p-6">
                <div class="flex items-center space-x-6 mb-8">
                    <div class="flex-shrink-0">
                        <img class="h-20 w-20 rounded-full" 
                             src="{{ $user->profile_photo_url ?? 'https://ui-avatars.com/api/?name=' . urlencode($user->first_name . ' ' . $user->last_name) }}" 
                             alt="{{ $user->first_name }} {{ $user->last_name }}">
                    </div>
                    <div>
                        <h3 class="text-xl font-medium text-gray-900">{{ $user->first_name }} {{ $user->last_name }}</h3>
                        <p class="text-gray-500">{{ $user->email }}</p>
                        <p class="text-sm text-gray-500 mt-1">
                            {{ __('Member since') }} {{ $user->created_at->format('F Y') }}
                        </p>
                    </div>
                </div>

                <div class="border-t border-gray-200 pt-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">{{ __('Account Information') }}</h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <h4 class="text-sm font-medium text-gray-500">{{ __('First Name') }}</h4>
                            <p class="mt-1 text-sm text-gray-900">{{ $user->first_name }}</p>
                        </div>
                        <div>
                            <h4 class="text-sm font-medium text-gray-500">{{ __('Last Name') }}</h4>
                            <p class="mt-1 text-sm text-gray-900">{{ $user->last_name }}</p>
                        </div>
                        <div>
                            <h4 class="text-sm font-medium text-gray-500">{{ __('Email') }}</h4>
                            <p class="mt-1 text-sm text-gray-900">{{ $user->email }}</p>
                        </div>
                        @if($user->email_verified_at)
                            <div>
                                <h4 class="text-sm font-medium text-gray-500">{{ __('Email Verified') }}</h4>
                                <p class="mt-1 text-sm text-gray-900">{{ $user->email_verified_at->format('M d, Y') }}</p>
                            </div>
                        @endif
                    </div>
                </div>

                <div class="mt-8 border-t border-gray-200 pt-6">
                    <div class="flex justify-end space-x-3">
                        <a href="{{ route('profile.edit', ['locale' => app()->getLocale()]) }}" 
                           class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            {{ __('Edit Profile') }}
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
