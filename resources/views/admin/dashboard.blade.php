@extends('layouts.admin')

@section('content')
<div class="flex-1 overflow-x-hidden overflow-y-auto bg-gray-50">
    <div class="container mx-auto px-3 sm:px-4 lg:px-6 py-4 sm:py-6 lg:py-8">
        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
            <!-- Total Subscriptions -->
            <div class="bg-white rounded-lg shadow p-4">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-blue-100 text-blue-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                        </svg>
                    </div>
                    <div class="ml-4">
                        <h3 class="text-gray-500 text-sm font-medium">Total Subscriptions</h3>
                        <p class="text-2xl font-semibold text-gray-900">{{ $subscriptionStats['total'] }}</p>
                    </div>
                </div>
            </div>

            <!-- Active Subscriptions -->
            <div class="bg-white rounded-lg shadow p-4">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-green-100 text-green-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div class="ml-4">
                        <h3 class="text-gray-500 text-sm font-medium">Active</h3>
                        <p class="text-2xl font-semibold text-gray-900">{{ $subscriptionStats['active'] }}</p>
                    </div>
                </div>
            </div>

            <!-- Pending Subscriptions -->
            <div class="bg-white rounded-lg shadow p-4">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-yellow-100 text-yellow-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div class="ml-4">
                        <h3 class="text-gray-500 text-sm font-medium">Pending</h3>
                        <p class="text-2xl font-semibold text-gray-900">{{ $subscriptionStats['pending'] }}</p>
                    </div>
                </div>
            </div>

            <!-- Expired Subscriptions -->
            <div class="bg-white rounded-lg shadow p-4">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-red-100 text-red-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </div>
                    <div class="ml-4">
                        <h3 class="text-gray-500 text-sm font-medium">Expired</h3>
                        <p class="text-2xl font-semibold text-gray-900">{{ $subscriptionStats['expired'] }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- User Stats -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
            <div class="bg-white rounded-lg shadow p-4">
                <h3 class="text-lg font-medium text-gray-900 mb-4">User Statistics</h3>
                <div class="space-y-3">
                    <div class="flex justify-between">
                        <span class="text-gray-600">Total Users</span>
                        <span class="font-medium">{{ $userStats['total'] }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">New Today</span>
                        <span class="font-medium text-green-600">{{ $userStats['new_today'] }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">New This Week</span>
                        <span class="font-medium text-green-600">{{ $userStats['new_week'] }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">New This Month</span>
                        <span class="font-medium text-green-600">{{ $userStats['new_month'] }}</span>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow p-4">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Content Statistics</h3>
                <div class="space-y-3">
                    <div class="flex justify-between">
                        <span class="text-gray-600">Total Quizzes</span>
                        <span class="font-medium">{{ $contentStats['quizzes'] }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Published Quizzes</span>
                        <span class="font-medium text-green-600">{{ $contentStats['published_quizzes'] }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Total News</span>
                        <span class="font-medium">{{ $contentStats['news'] }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Published News</span>
                        <span class="font-medium text-green-600">{{ $contentStats['published_news'] }}</span>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow p-4">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Quick Actions</h3>
                <div class="space-y-2">
                    <a href="{{ route('admin.quizzes.create') }}" class="block w-full text-left px-4 py-2 text-sm text-white bg-blue-600 hover:bg-blue-700 rounded-md transition duration-150">
                        Create New Quiz
                    </a>
                    <a href="{{ route('admin.news.create') }}" class="block w-full text-left px-4 py-2 text-sm text-white bg-green-600 hover:bg-green-700 rounded-md transition duration-150">
                        Create News Post
                    </a>
                    <a href="{{ route('admin.subscriptions') }}" class="block w-full text-left px-4 py-2 text-sm text-white bg-purple-600 hover:bg-purple-700 rounded-md transition duration-150">
                        Manage Subscriptions
                    </a>
                    <a href="{{ route('admin.users') }}" class="block w-full text-left px-4 py-2 text-sm text-white bg-indigo-600 hover:bg-indigo-700 rounded-md transition duration-150">
                        View All Users
                    </a>
                </div>
            </div>
        </div>

        <!-- Pending Subscriptions -->
        <div class="bg-white rounded-lg shadow overflow-hidden mb-6">
            <div class="px-4 py-5 border-b border-gray-200 sm:px-6">
                <h3 class="text-lg font-medium leading-6 text-gray-900">Pending Subscription Approvals</h3>
            </div>
            <div class="bg-white overflow-hidden">
                @if($pendingSubscriptions->isEmpty())
                    <div class="p-6 text-center text-gray-500">
                        No pending subscriptions
                    </div>
                @else
                    <ul class="divide-y divide-gray-200">
                        @foreach($pendingSubscriptions as $subscription)
                            <li class="p-4 hover:bg-gray-50">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center">
                                        <div class="ml-4">
                                            <div class="text-sm font-medium text-gray-900">
                                                {{ $subscription->user->name }}
                                            </div>
                                            <div class="text-sm text-gray-500">
                                                {{ $subscription->plan->name }} - 
                                                {{ number_format($subscription->amount, 2) }} {{ $subscription->currency }}
                                            </div>
                                        </div>
                                    </div>
                                    <div class="flex items-center space-x-2">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                            Pending
                                        </span>
                                        <a href="{{ route('admin.subscriptions.show', $subscription) }}" class="text-blue-600 hover:text-blue-900 text-sm font-medium">
                                            Review
                                        </a>
                                    </div>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                    <div class="px-4 py-3 bg-gray-50 text-right sm:px-6">
                        <a href="{{ route('admin.subscriptions') }}" class="text-sm font-medium text-blue-600 hover:text-blue-500">
                            View all subscriptions
                        </a>
                    </div>
                @endif
            </div>
        </div>

        <!-- Recent Activity -->
        <div class="bg-white rounded-lg shadow overflow-hidden">
            <div class="px-4 py-5 border-b border-gray-200 sm:px-6">
                <h3 class="text-lg font-medium leading-6 text-gray-900">Recent Activity</h3>
            </div>
            <div class="bg-white overflow-hidden">
                @if(empty($recentActivity))
                    <div class="p-6 text-center text-gray-500">
                        No recent activity
                    </div>
                @else
                    <ul class="divide-y divide-gray-200">
                        @foreach($recentActivity as $activity)
                            <li class="p-4 hover:bg-gray-50">
                                <div class="flex items-center space-x-4">
                                    <div class="flex-shrink-0">
                                        <span class="inline-flex items-center justify-center h-10 w-10 rounded-full bg-gray-500">
                                            <span class="text-sm font-medium leading-none text-white">
                                                {{ substr($activity['user_name'], 0, 1) }}
                                            </span>
                                        </span>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm font-medium text-gray-900 truncate">
                                            {{ $activity['user_name'] }}
                                        </p>
                                        <p class="text-sm text-gray-500 truncate">
                                            {{ $activity['action'] }}
                                        </p>
                                    </div>
                                    <div class="text-sm text-gray-500">
                                        {{ $activity['time_ago'] }}
                                    </div>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
