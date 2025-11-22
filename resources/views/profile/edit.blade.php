@extends('layouts.app')

@section('title', __('Edit Profile'))

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-3xl mx-auto">
        <div class="bg-white rounded-lg shadow-md overflow-hidden">
            <div class="bg-gray-50 px-6 py-4 border-b border-gray-200">
                <h2 class="text-2xl font-semibold text-gray-800">{{ __('Edit Profile') }}</h2>
            </div>
            
            <div class="p-6">
                <!-- Profile Photo Update -->
                <div class="mb-8">
                    <div class="flex items-center space-x-6">
                        <div class="flex-shrink-0">
                            <img class="h-20 w-20 rounded-full" 
                                 src="{{ $user->profile_photo_url ?? 'https://ui-avatars.com/api/?name=' . urlencode($user->name) }}" 
                                 alt="{{ $user->name }}">
                        </div>
                        <div>
                            <h3 class="text-lg font-medium text-gray-900">{{ __('Update Profile Photo') }}</h3>
                            <div class="mt-2 flex items-center space-x-4">
                                <form method="POST" action="{{ route('profile.photo', ['locale' => app()->getLocale()]) }}" enctype="multipart/form-data">
                                    @csrf
                                    <input type="file" name="photo" id="photo" class="hidden" onchange="this.form.submit()">
                                    <label for="photo" class="cursor-pointer inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-medium text-sm text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                        {{ __('Select New Photo') }}
                                    </label>
                                </form>
                                @if($user->profile_photo_path)
                                    <form method="POST" action="{{ route('profile.photo.destroy', ['locale' => app()->getLocale()]) }}">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-sm text-red-600 hover:text-red-700">
                                            {{ __('Remove Photo') }}
                                        </button>
                                    </form>
                                @endif
                            </div>
                            <p class="mt-2 text-sm text-gray-500">
                                {{ __('JPG, GIF or PNG. Max size of 1MB.') }}
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Profile Information -->
                <div class="border-t border-gray-200 pt-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">{{ __('Profile Information') }}</h3>
                    
                    <form method="POST" action="{{ route('profile.update', ['locale' => app()->getLocale()]) }}">
                        @csrf
                        @method('PATCH')

                        <div class="grid grid-cols-1 gap-6">
                            <div>
                                <label for="name" class="block text-sm font-medium text-gray-700">{{ __('Name') }}</label>
                                <input type="text" name="name" id="name" value="{{ old('name', $user->name) }}" required 
                                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                @error('name')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="email" class="block text-sm font-medium text-gray-700">{{ __('Email') }}</label>
                                <input type="email" name="email" id="email" value="{{ old('email', $user->email) }}" required 
                                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                @error('email')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                                
                                @if ($user->email_verified_at)
                                    <p class="mt-2 text-sm text-green-600">
                                        {{ __('Your email address is verified.') }}
                                    </p>
                                @else
                                    <p class="mt-2 text-sm text-yellow-600">
                                        {{ __('Your email address is unverified.') }}
                                        <button type="button" class="text-indigo-600 hover:text-indigo-500">
                                            {{ __('Click here to resend the verification email.') }}
                                        </button>
                                    </p>
                                @endif
                            </div>

                            <div class="flex justify-end">
                                <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                    {{ __('Save') }}
                                </button>
                            </div>
                        </div>
                    </form>
                </div>

                <!-- Update Password -->
                <div class="mt-10 border-t border-gray-200 pt-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">{{ __('Update Password') }}</h3>
                    
                    <form method="POST" action="{{ route('password.update', ['locale' => app()->getLocale()]) }}">
                        @csrf
                        @method('PUT')

                        <div class="grid grid-cols-1 gap-6">
                            <div>
                                <label for="current_password" class="block text-sm font-medium text-gray-700">{{ __('Current Password') }}</label>
                                <input type="password" name="current_password" id="current_password" required 
                                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                @error('current_password')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="password" class="block text-sm font-medium text-gray-700">{{ __('New Password') }}</label>
                                <input type="password" name="password" id="password" required 
                                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                @error('password')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="password_confirmation" class="block text-sm font-medium text-gray-700">{{ __('Confirm New Password') }}</label>
                                <input type="password" name="password_confirmation" id="password_confirmation" required 
                                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                            </div>

                            <div class="flex justify-end">
                                <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                    {{ __('Update Password') }}
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@if (session('status') === 'profile-updated')
    <div id="profile-updated" class="fixed bottom-4 right-4 bg-green-500 text-white px-4 py-2 rounded-md shadow-lg">
        {{ __('Profile updated successfully.') }}
    </div>
    <script>
        setTimeout(() => {
            document.getElementById('profile-updated').remove();
        }, 3000);
    </script>
@endif

@if (session('status') === 'password-updated')
    <div id="password-updated" class="fixed bottom-4 right-4 bg-green-500 text-white px-4 py-2 rounded-md shadow-lg">
        {{ __('Password updated successfully.') }}
    </div>
    <script>
        setTimeout(() => {
            document.getElementById('password-updated').remove();
        }, 3000);
    </script>
@endif

@if (session('status') === 'photo-updated')
    <div id="photo-updated" class="fixed bottom-4 right-4 bg-green-500 text-white px-4 py-2 rounded-md shadow-lg">
        {{ __('Profile photo updated successfully.') }}
    </div>
    <script>
        setTimeout(() => {
            document.getElementById('photo-updated').remove();
        }, 3000);
    </script>
@endif

@if (session('status') === 'photo-deleted')
    <div id="photo-deleted" class="fixed bottom-4 right-4 bg-green-500 text-white px-4 py-2 rounded-md shadow-lg">
        {{ __('Profile photo removed.') }}
    </div>
    <script>
        setTimeout(() => {
            document.getElementById('photo-deleted').remove();
        }, 3000);
    </script>
@endif

@endsection
