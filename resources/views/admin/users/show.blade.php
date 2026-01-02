@extends('admin.layouts.app')

@section('title', 'User Details: ' . $user->full_name)

@push('styles')
    <style>
        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .fade-in {
            animation: fadeIn 0.6s ease-out forwards;
        }

        .fade-in-delay-1 {
            animation-delay: 0.1s;
        }

        .fade-in-delay-2 {
            animation-delay: 0.2s;
        }

        .fade-in-delay-3 {
            animation-delay: 0.3s;
        }

        .gradient-border {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            padding: 1px;
            border-radius: 0.75rem;
        }

        .card-hover {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .card-hover:hover {
            transform: translateY(-2px);
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        }

        .dark .card-hover:hover {
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.3), 0 10px 10px -5px rgba(0, 0, 0, 0.2);
        }

        .action-btn {
            transition: all 0.2s ease;
            position: relative;
            overflow: hidden;
        }

        .action-btn::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            width: 0;
            height: 0;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.2);
            transform: translate(-50%, -50%);
            transition: width 0.6s, height 0.6s;
        }

        .action-btn:hover::before {
            width: 300px;
            height: 300px;
        }

        .status-badge {
            animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
        }

        @keyframes pulse {

            0%,
            100% {
                opacity: 1;
            }

            50% {
                opacity: 0.8;
            }
        }
    </style>
@endpush

@section('content')
    @php
        // Get subscription phone number for fallback in Personal Information section
        $pendingSubscriptions = $user->subscriptions->filter(function ($sub) {
            return strtolower($sub->status) === 'pending';
        });
        $subscriptionPhone = $pendingSubscriptions->isNotEmpty() 
            ? $pendingSubscriptions->sortByDesc('created_at')->first()->phone_number 
            : null;
    @endphp
    
    <div class="space-y-6 fade-in">
        <!-- Header Section -->
        <div
            class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden card-hover fade-in">
            <div class="bg-gray-50 dark:bg-gray-900 px-6 py-8">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-4">
                        <div class="relative">
                            <div
                                class="h-16 w-16 rounded-full bg-gray-100 dark:bg-gray-700 border-2 border-gray-200 dark:border-gray-600 flex items-center justify-center text-gray-600 dark:text-gray-300 text-xl font-semibold">
                                {{ strtoupper(substr($user->first_name, 0, 1)) }}{{ strtoupper(substr($user->last_name, 0, 1)) }}
                            </div>
                            <div
                                class="absolute -bottom-1 -right-1 h-5 w-5 rounded-full 
                            {{ $user->is_active ? 'bg-green-500' : 'bg-red-500' }} 
                            border-2 border-white dark:border-gray-800">
                            </div>
                        </div>
                        <div>
                            <h1 class="text-2xl font-semibold text-gray-900 dark:text-white">{{ $user->full_name }}</h1>
                            <p class="text-gray-500 dark:text-gray-400 mt-1">Member since
                                {{ $user->created_at->format('F j, Y') }}</p>
                            <div class="flex items-center space-x-2 mt-2">
                                <span
                                    class="px-2 py-1 text-xs font-medium rounded 
                                {{ $user->isAdmin() ? 'bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300' : 'bg-blue-50 text-blue-700 dark:bg-blue-900/20 dark:text-blue-300' }} 
                                border border-gray-200 dark:border-gray-600">
                                    {{ ucfirst($user->role) }}
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="flex items-center space-x-3">
                        <a href="{{ route('admin.users.index') }}"
                            class="inline-flex items-center px-4 py-2 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 font-medium rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-all duration-200">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                            </svg>
                            Back to Users
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Personal Information Card -->
            <div class="lg:col-span-2 space-y-6">
                <div
                    class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden card-hover fade-in fade-in-delay-1">
                    <div class="bg-gray-50 dark:bg-gray-900 px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                        <h2 class="text-lg font-medium text-gray-900 dark:text-white flex items-center">
                            <svg class="w-5 h-5 mr-2 text-gray-500 dark:text-gray-400" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                            Personal Information
                        </h2>
                    </div>
                    <div class="p-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="space-y-4">
                                <div>
                                    <label
                                        class="block text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1">First
                                        Name</label>
                                    <div class="text-gray-900 dark:text-white font-medium text-lg">{{ $user->first_name }}
                                    </div>
                                </div>
                                <div>
                                    <label
                                        class="block text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1">Last
                                        Name</label>
                                    <div class="text-gray-900 dark:text-white font-medium text-lg">{{ $user->last_name }}
                                    </div>
                                </div>
                            </div>
                            <div class="space-y-4">
                                <div>
                                    <label
                                        class="block text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1">Email
                                        Address</label>
                                    <div class="flex items-center text-gray-900 dark:text-white font-medium">
                                        {{ $user->email }}
                                        @if ($user->hasVerifiedEmail())
                                            <span class="ml-2 text-green-500 dark:text-green-400" title="Verified">
                                                <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd"
                                                        d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.643.304 1.254.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                                        clip-rule="evenodd" />
                                                </svg>
                                            </span>
                                        @endif
                                    </div>
                                </div>
                                <div>
                                    <label
                                        class="block text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1">Phone
                                        Number</label>
                                    <div class="text-gray-900 dark:text-white font-medium">
                                        {{ $user->phone_number ?? $subscriptionPhone ?? 'Not provided' }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Recent Activity Card -->
                <div
                    class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden card-hover fade-in fade-in-delay-2">
                    <div class="bg-gray-50 dark:bg-gray-900 px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                        <h2 class="text-lg font-medium text-gray-900 dark:text-white flex items-center">
                            <svg class="w-5 h-5 mr-2 text-gray-500 dark:text-gray-400" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2">
                                </path>
                            </svg>
                            Recent Activity
                        </h2>
                    </div>
                    <div class="p-6">
                        @if ($user->quizAttempts->isNotEmpty())
                            <div class="space-y-3">
                                @foreach ($user->quizAttempts->take(5) as $attempt)
                                    <div
                                        class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-700 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-600 transition-colors">
                                        <div class="flex items-center space-x-3">
                                            <div class="flex-shrink-0">
                                                <div
                                                    class="h-10 w-10 rounded-full bg-blue-100 dark:bg-blue-900/20 flex items-center justify-center">
                                                    <svg class="h-6 w-6 text-blue-600 dark:text-blue-400" fill="none"
                                                        stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                                                        </path>
                                                    </svg>
                                                </div>
                                            </div>
                                            <div>
                                                <div class="text-sm font-medium text-gray-900 dark:text-white">
                                                    {{ $attempt->quiz->title }}
                                                </div>
                                                <div class="text-xs text-gray-500 dark:text-gray-400">
                                                    Score: {{ $attempt->score }}%
                                                </div>
                                            </div>
                                        </div>
                                        <div class="text-sm text-gray-500 dark:text-gray-400">
                                            {{ $attempt->completed_at?->diffForHumans() ?? 'In progress' }}
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-8">
                                <svg class="mx-auto h-12 w-12 text-gray-400 dark:text-gray-500" fill="none"
                                    stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2">
                                    </path>
                                </svg>
                                <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-white">No recent activity</h3>
                                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">This user hasn't completed any
                                    quizzes yet.</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Right Column -->
            <div class="space-y-6">
                <!-- Subscription Card -->
                <div
                    class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden card-hover fade-in fade-in-delay-1">
                    <div class="bg-gray-50 dark:bg-gray-900 px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                        <h2 class="text-lg font-medium text-gray-900 dark:text-white flex items-center">
                            <svg class="w-5 h-5 mr-2 text-gray-500 dark:text-gray-400" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z">
                                </path>
                            </svg>
                            Subscription
                        </h2>
                    </div>
                    <div class="p-6">
                        @if ($pendingSubscriptions->isNotEmpty())
                            @php
                                $subscription = $pendingSubscriptions->sortByDesc('created_at')->first();
                            @endphp

                            <div class="space-y-4">
                                <!-- Status -->
                                <div class="flex items-center justify-between">
                                    <span class="text-sm font-medium text-gray-900 dark:text-white">
                                        {{ $subscription->plan->name['en'] ?? 'Unknown Plan' }}
                                    </span>
                                    <span class="px-2 py-1 text-xs font-medium rounded-full bg-yellow-100 text-yellow-800 dark:bg-yellow-900/20 dark:text-yellow-400">
                                        Pending
                                    </span>
                                </div>

                                <!-- Details -->
                                <div class="space-y-2 text-sm">
                                    @if ($subscription->plan)
                                        <div class="flex justify-between">
                                            <span class="text-gray-500 dark:text-gray-400">Price:</span>
                                            <span class="font-medium text-gray-900 dark:text-white">{{ number_format($subscription->plan->price, 0) }} RWF{{ $subscription->plan->billing_interval }}</span>
                                        </div>
                                    @endif

                                    <div class="flex justify-between">
                                        <span class="text-gray-500 dark:text-gray-400">Phone Number:</span>
                                        <span class="font-medium text-gray-900 dark:text-white">{{ $subscription->phone_number ?? $user->phone_number ?? 'Not provided' }}</span>
                                    </div>

                                    <div class="flex justify-between">
                                        <span class="text-gray-500 dark:text-gray-400">Requested:</span>
                                        <span class="font-medium text-gray-900 dark:text-white">{{ $subscription->created_at->format('M j, Y') }}</span>
                                    </div>

                                    <div class="flex justify-between">
                                        <span class="text-gray-500 dark:text-gray-400">Amount:</span>
                                        <span class="font-medium text-gray-900 dark:text-white">{{ number_format($subscription->amount, 0) }} RWF</span>
                                    </div>
                                </div>

                                <!-- Actions -->
                                <div class="pt-4 space-y-2">
                                    <form action="{{ route('admin.subscriptions.approve', $subscription) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="w-full action-btn inline-flex items-center justify-center px-4 py-2 bg-green-600 hover:bg-green-700 text-white font-medium rounded-lg transition-all duration-200">
                                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                            </svg>
                                            Approve
                                        </button>
                                    </form>
                                    
                                    <form action="{{ route('admin.subscriptions.reject', $subscription) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="w-full action-btn inline-flex items-center justify-center px-4 py-2 bg-red-600 hover:bg-red-700 text-white font-medium rounded-lg transition-all duration-200">
                                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                            </svg>
                                            Reject
                                        </button>
                                    </form>
                                </div>
                            </div>
                        @else
                            <div class="text-center py-2">
                                <svg class="mx-auto h-12 w-12 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-white">No pending subscriptions</h3>
                                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">This user doesn't have any pending subscription requests.</p>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Account Actions Card -->
                <div
                    class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden card-hover fade-in fade-in-delay-3">
                    <div class="bg-gray-50 dark:bg-gray-900 px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                        <h2 class="text-lg font-medium text-gray-900 dark:text-white flex items-center">
                            <svg class="w-5 h-5 mr-2 text-gray-500 dark:text-gray-400" fill="none"
                                stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z">
                                </path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            </svg>
                            Account Management
                        </h2>
                    </div>
                    <div class="p-6 space-y-3">
                        <!-- Edit User -->
                        <a href="{{ route('admin.users.edit', $user) }}"
                            class="action-btn w-full inline-flex items-center justify-center px-4 py-3 bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 text-blue-700 dark:text-blue-300 font-medium rounded-lg hover:bg-blue-100 dark:hover:bg-blue-900/30 transition-all duration-200">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z">
                                </path>
                            </svg>
                            Edit User
                        </a>

                        <!-- Change Password -->
                        <form action="{{ route('admin.users.change-password', $user) }}" method="POST"
                            onsubmit="return confirm('Are you sure you want to send a password reset link to this user?')">
                            @csrf
                            <button type="submit"
                                class="action-btn w-full inline-flex items-center justify-center px-4 py-3 bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 text-gray-700 dark:text-gray-300 font-medium rounded-lg hover:bg-gray-100 dark:hover:bg-gray-600 transition-all duration-200">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z">
                                    </path>
                                </svg>
                                Send Password Reset
                            </button>
                        </form>

                        <!-- Admin Status -->
                        @if (!$user->isAdmin())
                            <form action="{{ route('admin.users.make-admin', $user) }}" method="POST"
                                onsubmit="return confirm('Are you sure you want to make this user an admin?')">
                                @csrf
                                @method('PATCH')
                                <button type="submit"
                                    class="action-btn w-full inline-flex items-center justify-center px-4 py-3 bg-purple-50 dark:bg-purple-900/20 border border-purple-200 dark:border-purple-800 text-purple-700 dark:text-purple-300 font-medium rounded-lg hover:bg-purple-100 dark:hover:bg-purple-900/30 transition-all duration-200">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z">
                                        </path>
                                    </svg>
                                    Make Admin
                                </button>
                            </form>
                        @else
                            <form action="{{ route('admin.users.remove-admin', $user) }}" method="POST"
                                onsubmit="return confirm('Are you sure you want to remove admin privileges from this user?')">
                                @csrf
                                @method('PATCH')
                                <button type="submit"
                                    class="action-btn w-full inline-flex items-center justify-center px-4 py-3 bg-orange-50 dark:bg-orange-900/20 border border-orange-200 dark:border-orange-800 text-orange-700 dark:text-orange-300 font-medium rounded-lg hover:bg-orange-100 dark:hover:bg-orange-900/30 transition-all duration-200">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z">
                                        </path>
                                    </svg>
                                    Remove Admin
                                </button>
                            </form>
                        @endif

                        <!-- Account Status -->
                        @if ($user->is_active)
                            <form action="{{ route('admin.users.suspend', $user) }}" method="POST"
                                onsubmit="return confirm('Are you sure you want to suspend this user?')">
                                @csrf
                                <button type="submit"
                                    class="action-btn w-full inline-flex items-center justify-center px-4 py-3 bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 text-yellow-700 dark:text-yellow-300 font-medium rounded-lg hover:bg-yellow-100 dark:hover:bg-yellow-900/30 transition-all duration-200">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z">
                                        </path>
                                    </svg>
                                    Suspend Account
                                </button>
                            </form>
                        @else
                            <form action="{{ route('admin.users.activate', $user) }}" method="POST"
                                onsubmit="return confirm('Are you sure you want to activate this user?')">
                                @csrf
                                @method('PATCH')
                                <button type="submit"
                                    class="action-btn w-full inline-flex items-center justify-center px-4 py-3 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 text-green-700 dark:text-green-300 font-medium rounded-lg hover:bg-green-100 dark:hover:bg-green-900/30 transition-all duration-200">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    Activate Account
                                </button>
                            </form>
                        @endif

                        <!-- Delete User -->
                        <form action="{{ route('admin.users.destroy', $user) }}" method="POST"
                            onsubmit="return confirm('⚠️ CRITICAL WARNING: You are about to DELETE THE ENTIRE USER ACCOUNT.\n\nThis will permanently delete:\n• The user account\n• All user data\n• All subscription records\n• All quiz attempts\n• All activity history\n\nThis action CANNOT be undone and will delete everything associated with this user.\n\nAre you absolutely sure you want to delete this USER ACCOUNT?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit"
                                class="action-btn w-full inline-flex items-center justify-center px-4 py-3 bg-red-100 dark:bg-red-900/40 border-2 border-red-300 dark:border-red-700 text-red-800 dark:text-red-200 font-bold rounded-lg hover:bg-red-200 dark:hover:bg-red-900/60 transition-all duration-200">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                                </svg>
                                ⚠️ DELETE USER ACCOUNT
                            </button>
                        </form>
                    </div>
                </div>

                <!-- Subscription Records -->
                <div
                    class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden card-hover fade-in fade-in-delay-3">
                    <div class="bg-gray-50 dark:bg-gray-900 px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                        <h2 class="text-lg font-medium text-gray-900 dark:text-white flex items-center">
                            <svg class="w-5 h-5 mr-2 text-gray-500 dark:text-gray-400" fill="none"
                                stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2">
                                </path>
                            </svg>
                            Subscription History
                        </h2>
                    </div>
                    <div class="p-1">
                        @if ($user->subscriptions->isNotEmpty())
                            <!-- Subscriptions -->
                            <div>
                                <h3 class="text-sm font-semibold text-gray-900 dark:text-white mb-3">All Subscriptions</h3>
                                    <div class="overflow-hidden border border-gray-200 dark:border-gray-600 rounded-lg">
                                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-600">
                                            <thead class="bg-gray-50 dark:bg-gray-700">
                                                <tr>
                                                    <th class="px-1 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Plan</th>
                                                    <th class="px-1 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-600">
                                                @foreach ($user->subscriptions->sortByDesc('created_at') as $subscription)
                                                    @php
                                                        $planName = $subscription->plan->name;
                                                        if (is_string($planName)) {
                                                            $planName = json_decode($planName, true) ?: [];
                                                        }
                                                        $displayName =
                                                            $planName[app()->getLocale()] ??
                                                            ($planName['en'] ?? ($planName['rw'] ?? 'Unknown Plan'));

                                                        $statusColor = match ($subscription->status) {
                                                            'EXPIRED' => 'red',
                                                            'CANCELLED' => 'gray',
                                                            'PENDING' => 'yellow',
                                                            'ACTIVE' => 'green',
                                                            'PAUSED' => 'orange',
                                                            default => 'gray',
                                                        };
                                                    @endphp
                                                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors @if($subscription->status === 'ACTIVE' && (!$subscription->ends_at || $subscription->ends_at->isFuture())) border-l-4 border-l-green-500 @endif">
                                                        <td class="px-1 py-2 whitespace-nowrap">
                                                            <div class="text-sm font-medium text-gray-900 dark:text-white">{{ $displayName }}</div>
                                                        </td>
                                                        <td class="px-1 py-2 whitespace-nowrap text-sm font-medium">
                                                            <div class="flex items-center space-x-2">
                                                                @if ($subscription->status === 'ACTIVE')
                                                                    <button onclick="confirmPauseSubscription({{ $subscription->id }})" class="text-orange-600 hover:text-orange-900 dark:text-orange-400 dark:hover:text-orange-300">Pause</button>
                                                                @endif
                                                                <span class="text-gray-400 text-sm">Contact admin to delete</span>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            @endif
                            <div class="text-center py-8">
                                <svg class="mx-auto h-12 w-12 text-gray-400 dark:text-gray-500" fill="none"
                                    stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2">
                                    </path>
                                </svg>
                                <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-white">No subscription records
                                </h3>
                                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">This user hasn't subscribed to any
                                    plans yet.</p>
                            </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('scripts')
    <script>
        function confirmCancelSubscription(subscriptionId) {
            if (confirm('Are you sure you want to cancel this subscription? This action cannot be undone.')) {
                fetch(`/admin/subscriptions/${subscriptionId}/cancel`, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Content-Type': 'application/json',
                        },
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            location.reload();
                        } else {
                            alert('Error cancelling subscription: ' + (data.message || 'Unknown error'));
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('Error cancelling subscription');
                    });
            }
        }

        function confirmPauseSubscription(subscriptionId) {
            if (confirm('Are you sure you want to pause this subscription? The user will lose access until it is resumed.')) {
                fetch(`/admin/subscriptions/${subscriptionId}/pause`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('Subscription paused successfully!');
                        location.reload();
                    } else {
                        alert('Error pausing subscription: ' + (data.message || 'Unknown error'));
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Error pausing subscription');
                });
            }
        }
    </script>
@endpush
