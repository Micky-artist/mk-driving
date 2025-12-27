@extends('admin.layouts.app')

@section('title', 'Reports & Analytics Dashboard')

@push('styles')
@endpush

@section('content')
<div class="container mx-auto px-4 py-6 dark:bg-gray-900 min-h-screen">
    <!-- Header -->
    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4 mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Analytics Dashboard</h1>
            <p class="text-gray-600 dark:text-gray-400 mt-1">Comprehensive insights across all platform features</p>
        </div>
        <div class="flex items-center gap-3">
            <!-- Date Range Selector -->
            <select id="dateRange" class="px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-white">
                <option value="7">Last 7 days</option>
                <option value="30" selected>Last 30 days</option>
                <option value="90">Last 90 days</option>
                <option value="365">Last year</option>
            </select>
            <!-- Export Button -->
            <button onclick="exportAllAnalytics()" class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-green-600 to-green-700 hover:from-green-700 hover:to-green-800 text-white font-semibold rounded-lg shadow-sm hover:shadow-md transform hover:scale-105 transition-all duration-200">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0v6m0-6h6m-6 0H6"></path>
                </svg>
                Export All
            </button>
        </div>
    </div>

    <!-- Key Metrics Overview -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <!-- Total Revenue -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6 hover:shadow-md transition-all duration-300">
            <div class="flex items-center justify-between mb-4">
                <div class="p-3 bg-green-100 dark:bg-green-900/20 rounded-lg">
                    <svg class="w-6 h-6 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <span class="text-xs font-medium text-green-600 dark:text-green-400 bg-green-100 dark:bg-green-900/20 px-2 py-1 rounded-full">
                    Revenue
                </span>
            </div>
            <h3 class="text-2xl font-bold text-gray-900 dark:text-white mb-1">RWF {{ number_format($metrics['total_revenue'], 0) }}</h3>
            <p class="text-sm text-gray-600 dark:text-gray-400">Total Revenue</p>
            <div class="mt-2 flex items-center text-xs">
                <span class="text-green-600 dark:text-green-400">+{{ $metrics['revenue_growth'] }}%</span>
                <span class="text-gray-500 dark:text-gray-400 ml-1">vs last period</span>
            </div>
        </div>

        <!-- Active Users -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6 hover:shadow-md transition-all duration-300">
            <div class="flex items-center justify-between mb-4">
                <div class="p-3 bg-blue-100 dark:bg-blue-900/20 rounded-lg">
                    <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                    </svg>
                </div>
                <span class="text-xs font-medium text-blue-600 dark:text-blue-400 bg-blue-100 dark:bg-blue-900/20 px-2 py-1 rounded-full">
                    Users
                </span>
            </div>
            <h3 class="text-2xl font-bold text-gray-900 dark:text-white mb-1">{{ number_format($metrics['active_users']) }}</h3>
            <p class="text-sm text-gray-600 dark:text-gray-400">Active Users</p>
            <div class="mt-2 flex items-center text-xs">
                <span class="text-blue-600 dark:text-blue-400">+{{ $metrics['user_growth'] }}%</span>
                <span class="text-gray-500 dark:text-gray-400 ml-1">vs last period</span>
            </div>
        </div>

        <!-- Test Completion Rate -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6 hover:shadow-md transition-all duration-300">
            <div class="flex items-center justify-between mb-4">
                <div class="p-3 bg-purple-100 dark:bg-purple-900/20 rounded-lg">
                    <svg class="w-6 h-6 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <span class="text-xs font-medium text-purple-600 dark:text-purple-400 bg-purple-100 dark:bg-purple-900/20 px-2 py-1 rounded-full">
                    Tests
                </span>
            </div>
            <h3 class="text-2xl font-bold text-gray-900 dark:text-white mb-1">{{ number_format($metrics['completion_rate'], 1) }}%</h3>
            <p class="text-sm text-gray-600 dark:text-gray-400">Completion Rate</p>
            <div class="mt-2 flex items-center text-xs">
                <span class="text-purple-600 dark:text-purple-400">{{ $metrics['total_tests_completed'] }} completed</span>
            </div>
        </div>

        <!-- Engagement Score -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6 hover:shadow-md transition-all duration-300">
            <div class="flex items-center justify-between mb-4">
                <div class="p-3 bg-orange-100 dark:bg-orange-900/20 rounded-lg">
                    <svg class="w-6 h-6 text-orange-600 dark:text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                    </svg>
                </div>
                <span class="text-xs font-medium text-orange-600 dark:text-orange-400 bg-orange-100 dark:bg-orange-900/20 px-2 py-1 rounded-full">
                    Engagement
                </span>
            </div>
            <h3 class="text-2xl font-bold text-gray-900 dark:text-white mb-1">{{ number_format($metrics['engagement_score'], 1) }}</h3>
            <p class="text-sm text-gray-600 dark:text-gray-400">Engagement Score</p>
            <div class="mt-2 flex items-center text-xs">
                <span class="text-orange-600 dark:text-orange-400">+{{ $metrics['engagement_growth'] }}%</span>
                <span class="text-gray-500 dark:text-gray-400 ml-1">vs last period</span>
            </div>
        </div>
    </div>

    <!-- Analytics Tabs -->
    <div class="mb-6">
        <div class="flex flex-wrap gap-2 border-b border-gray-200 dark:border-gray-700">
            <button onclick="showTab('overview')" class="px-6 py-3 text-sm font-medium rounded-lg transition-all duration-200 border-2 bg-blue-600 text-white border-blue-600" data-tab="overview">
                <i class="fas fa-chart-line mr-2"></i>Overview
            </button>
            <button onclick="showTab('revenue')" class="px-6 py-3 text-sm font-medium rounded-lg transition-all duration-200 border-2 bg-white dark:bg-gray-800 text-gray-600 dark:text-gray-400 border-gray-300 dark:border-gray-600 hover:border-gray-400 dark:hover:border-gray-500" data-tab="revenue">
                <i class="fas fa-dollar-sign mr-2"></i>Revenue
            </button>
            <button onclick="showTab('users')" class="px-6 py-3 text-sm font-medium rounded-lg transition-all duration-200 border-2 bg-white dark:bg-gray-800 text-gray-600 dark:text-gray-400 border-gray-300 dark:border-gray-600 hover:border-gray-400 dark:hover:border-gray-500" data-tab="users">
                <i class="fas fa-users mr-2"></i>Users
            </button>
            <button onclick="showTab('tests')" class="px-6 py-3 text-sm font-medium rounded-lg transition-all duration-200 border-2 bg-white dark:bg-gray-800 text-gray-600 dark:text-gray-400 border-gray-300 dark:border-gray-600 hover:border-gray-400 dark:hover:border-gray-500" data-tab="tests">
                <i class="fas fa-graduation-cap mr-2"></i>Tests
            </button>
            <button onclick="showTab('subscriptions')" class="px-6 py-3 text-sm font-medium rounded-lg transition-all duration-200 border-2 bg-white dark:bg-gray-800 text-gray-600 dark:text-gray-400 border-gray-300 dark:border-gray-600 hover:border-gray-400 dark:hover:border-gray-500" data-tab="subscriptions">
                <i class="fas fa-crown mr-2"></i>Subscriptions
            </button>
            <button onclick="showTab('blog')" class="px-6 py-3 text-sm font-medium rounded-lg transition-all duration-200 border-2 bg-white dark:bg-gray-800 text-gray-600 dark:text-gray-400 border-gray-300 dark:border-gray-600 hover:border-gray-400 dark:hover:border-gray-500" data-tab="blog">
                <i class="fas fa-newspaper mr-2"></i>Blog
            </button>
            <button onclick="showTab('forum')" class="px-6 py-3 text-sm font-medium rounded-lg transition-all duration-200 border-2 bg-white dark:bg-gray-800 text-gray-600 dark:text-gray-400 border-gray-300 dark:border-gray-600 hover:border-gray-400 dark:hover:border-gray-500" data-tab="forum">
                <i class="fas fa-comments mr-2"></i>Forum
            </button>
            <button onclick="showTab('visitors')" class="px-6 py-3 text-sm font-medium rounded-lg transition-all duration-200 border-2 bg-white dark:bg-gray-800 text-gray-600 dark:text-gray-400 border-gray-300 dark:border-gray-600 hover:border-gray-400 dark:hover:border-gray-500" data-tab="visitors">
                <i class="fas fa-eye mr-2"></i>Visitors
            </button>
        </div>
    </div>

    <!-- Tab Content -->
    <div id="tab-content">
        <!-- Overview Tab -->
        <div id="overview-tab" class="tab-content">
            <!-- Revenue Trends Chart -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm hover:shadow-md transition-all duration-300 transform hover:-translate-y-0.5 border border-gray-200 dark:border-gray-700 p-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Revenue Trends</h3>
                    <div class="relative h-64 w-full">
                        <canvas id="revenueChart"></canvas>
                    </div>
                </div>
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm hover:shadow-md transition-all duration-300 transform hover:-translate-y-0.5 border border-gray-200 dark:border-gray-700 p-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">User Activity</h3>
                    <div class="relative h-64 w-full">
                        <canvas id="userActivityChart"></canvas>
                    </div>
                </div>
            </div>

            <!-- Performance Metrics -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm hover:shadow-md transition-all duration-300 transform hover:-translate-y-0.5 border border-gray-200 dark:border-gray-700 p-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Top Performing Quizzes</h3>
                    <div class="space-y-3">
                        @foreach($topQuizzes as $quiz)
                        <div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
                            <div>
                                <p class="font-medium text-gray-900 dark:text-white">{{ $quiz['title'] }}</p>
                                <p class="text-sm text-gray-600 dark:text-gray-400">{{ $quiz['attempts'] }} attempts</p>
                            </div>
                            <div class="text-right">
                                <p class="font-semibold text-green-600 dark:text-green-400">{{ $quiz['pass_rate'] }}%</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400">pass rate</p>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>

                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm hover:shadow-md transition-all duration-300 transform hover:-translate-y-0.5 border border-gray-200 dark:border-gray-700 p-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Recent Activity</h3>
                    <div class="space-y-3">
                        @foreach($recentActivity as $activity)
                        <div class="flex items-center space-x-3 p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
                            <div class="w-2 h-2 bg-green-500 rounded-full"></div>
                            <div class="flex-1">
                                <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $activity->description }}</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400">{{ $activity->time }}</p>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>

                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm hover:shadow-md transition-all duration-300 transform hover:-translate-y-0.5 border border-gray-200 dark:border-gray-700 p-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Quick Stats</h3>
                    <div class="space-y-4">
                        <div class="flex justify-between items-center">
                            <span class="text-gray-600 dark:text-gray-400">Avg. Session Duration</span>
                            <span class="font-semibold text-gray-900 dark:text-white">{{ $metrics['avg_session_duration'] }}m</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-gray-600 dark:text-gray-400">Bounce Rate</span>
                            <span class="font-semibold text-gray-900 dark:text-white">{{ $metrics['bounce_rate'] }}%</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-gray-600 dark:text-gray-400">Page Views</span>
                            <span class="font-semibold text-gray-900 dark:text-white">{{ number_format($metrics['page_views']) }}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-gray-600 dark:text-gray-400">Conversion Rate</span>
                            <span class="font-semibold text-gray-900 dark:text-white">{{ $metrics['conversion_rate'] }}%</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Revenue Tab -->
        <div id="revenue-tab" class="tab-content" style="display: none;">
            <div class="grid grid-cols-1 lg:grid-cols-4 gap-6 mb-6">
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6 hover:shadow-md transition-all duration-300">
                    <h4 class="text-sm font-medium text-gray-600 dark:text-gray-400 mb-2">Monthly Recurring Revenue</h4>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white">RWF {{ number_format($revenueMetrics['mrr'], 0) }}</p>
                </div>
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6 hover:shadow-md transition-all duration-300">
                    <h4 class="text-sm font-medium text-gray-600 dark:text-gray-400 mb-2">Annual Revenue</h4>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white">RWF {{ number_format($revenueMetrics['annual_revenue'], 0) }}</p>
                </div>
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6 hover:shadow-md transition-all duration-300">
                    <h4 class="text-sm font-medium text-gray-600 dark:text-gray-400 mb-2">Average Revenue Per User</h4>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white">RWF {{ number_format($revenueMetrics['arpu'], 0) }}</p>
                </div>
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6 hover:shadow-md transition-all duration-300">
                    <h4 class="text-sm font-medium text-gray-600 dark:text-gray-400 mb-2">Customer Lifetime Value</h4>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white">RWF {{ number_format($revenueMetrics['clv'], 0) }}</p>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm hover:shadow-md transition-all duration-300 transform hover:-translate-y-0.5 border border-gray-200 dark:border-gray-700 p-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Revenue by Plan</h3>
                    <div class="relative h-64 w-full">
                        <canvas id="revenueByPlanChart"></canvas>
                    </div>
                </div>
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm hover:shadow-md transition-all duration-300 transform hover:-translate-y-0.5 border border-gray-200 dark:border-gray-700 p-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Revenue Trends</h3>
                    <div class="relative h-64 w-full">
                        <canvas id="revenueTrendsChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Users Tab -->
        <div id="users-tab" class="tab-content" style="display: none;">
            <div class="grid grid-cols-1 lg:grid-cols-4 gap-6 mb-6">
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6 hover:shadow-md transition-all duration-300">
                    <h4 class="text-sm font-medium text-gray-600 dark:text-gray-400 mb-2">Total Users</h4>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ number_format($userMetrics['total_users']) }}</p>
                </div>
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6 hover:shadow-md transition-all duration-300">
                    <h4 class="text-sm font-medium text-gray-600 dark:text-gray-400 mb-2">New Users (30d)</h4>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ number_format($userMetrics['new_users_30d']) }}</p>
                </div>
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6 hover:shadow-md transition-all duration-300">
                    <h4 class="text-sm font-medium text-gray-600 dark:text-gray-400 mb-2">Active Users (30d)</h4>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ number_format($userMetrics['active_users_30d']) }}</p>
                </div>
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6 hover:shadow-md transition-all duration-300">
                    <h4 class="text-sm font-medium text-gray-600 dark:text-gray-400 mb-2">Retention Rate</h4>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ number_format($userMetrics['retention_rate'], 1) }}%</p>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm hover:shadow-md transition-all duration-300 transform hover:-translate-y-0.5 border border-gray-200 dark:border-gray-700 p-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">User Registration Trends</h3>
                    <div class="relative h-64 w-full">
                        <canvas id="userRegistrationChart"></canvas>
                    </div>
                </div>
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm hover:shadow-md transition-all duration-300 transform hover:-translate-y-0.5 border border-gray-200 dark:border-gray-700 p-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">User Activity Heatmap</h3>
                    <div class="relative h-64 w-full">
                        <canvas id="userActivityHeatmap"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tests Tab -->
        <div id="tests-tab" class="tab-content" style="display: none;">
            <div class="grid grid-cols-1 lg:grid-cols-4 gap-6 mb-6">
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6 hover:shadow-md transition-all duration-300">
                    <h4 class="text-sm font-medium text-gray-600 dark:text-gray-400 mb-2">Total Tests Taken</h4>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ number_format($testMetrics['total_attempts']) }}</p>
                </div>
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6 hover:shadow-md transition-all duration-300">
                    <h4 class="text-sm font-medium text-gray-600 dark:text-gray-400 mb-2">Pass Rate</h4>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ number_format($testMetrics['pass_rate'], 1) }}%</p>
                </div>
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6 hover:shadow-md transition-all duration-300">
                    <h4 class="text-sm font-medium text-gray-600 dark:text-gray-400 mb-2">Avg. Score</h4>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ number_format($testMetrics['avg_score'], 1) }}%</p>
                </div>
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6 hover:shadow-md transition-all duration-300">
                    <h4 class="text-sm font-medium text-gray-600 dark:text-gray-400 mb-2">Avg. Completion Time</h4>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $testMetrics['avg_completion_time'] }}m</p>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm hover:shadow-md transition-all duration-300 transform hover:-translate-y-0.5 border border-gray-200 dark:border-gray-700 p-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Test Performance by Category</h3>
                    <div class="relative h-64 w-full">
                        <canvas id="testPerformanceChart"></canvas>
                    </div>
                </div>
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm hover:shadow-md transition-all duration-300 transform hover:-translate-y-0.5 border border-gray-200 dark:border-gray-700 p-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Difficulty Distribution</h3>
                    <div class="relative h-64 w-full">
                        <canvas id="difficultyChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Subscriptions Tab -->
        <div id="subscriptions-tab" class="tab-content" style="display: none;">
            <div class="grid grid-cols-1 lg:grid-cols-4 gap-6 mb-6">
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6 hover:shadow-md transition-all duration-300">
                    <h4 class="text-sm font-medium text-gray-600 dark:text-gray-400 mb-2">Active Subscriptions</h4>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ number_format($subscriptionMetrics['active_subscriptions']) }}</p>
                </div>
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6 hover:shadow-md transition-all duration-300">
                    <h4 class="text-sm font-medium text-gray-600 dark:text-gray-400 mb-2">Churn Rate</h4>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ number_format($subscriptionMetrics['churn_rate'], 1) }}%</p>
                </div>
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6 hover:shadow-md transition-all duration-300">
                    <h4 class="text-sm font-medium text-gray-600 dark:text-gray-400 mb-2">New Subscriptions (30d)</h4>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ number_format($subscriptionMetrics['new_subscriptions_30d']) }}</p>
                </div>
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6 hover:shadow-md transition-all duration-300">
                    <h4 class="text-sm font-medium text-gray-600 dark:text-gray-400 mb-2">MRR Growth</h4>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ number_format($subscriptionMetrics['mrr_growth'], 1) }}%</p>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm hover:shadow-md transition-all duration-300 transform hover:-translate-y-0.5 border border-gray-200 dark:border-gray-700 p-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Subscription Trends</h3>
                    <div class="relative h-64 w-full">
                        <canvas id="subscriptionTrendsChart"></canvas>
                    </div>
                </div>
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm hover:shadow-md transition-all duration-300 transform hover:-translate-y-0.5 border border-gray-200 dark:border-gray-700 p-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Plan Distribution</h3>
                    <div class="relative h-64 w-full">
                        <canvas id="planDistributionChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Blog Tab -->
        <div id="blog-tab" class="tab-content" style="display: none;">
            <div class="grid grid-cols-1 lg:grid-cols-4 gap-6 mb-6">
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6 hover:shadow-md transition-all duration-300">
                    <h4 class="text-sm font-medium text-gray-600 dark:text-gray-400 mb-2">Total Views</h4>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ number_format($blogMetrics['total_views']) }}</p>
                </div>
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6 hover:shadow-md transition-all duration-300">
                    <h4 class="text-sm font-medium text-gray-600 dark:text-gray-400 mb-2">Published Posts</h4>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ number_format($blogMetrics['published_posts']) }}</p>
                </div>
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6 hover:shadow-md transition-all duration-300">
                    <h4 class="text-sm font-medium text-gray-600 dark:text-gray-400 mb-2">Engagement Rate</h4>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ number_format($blogMetrics['engagement_rate'], 1) }}%</p>
                </div>
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6 hover:shadow-md transition-all duration-300">
                    <h4 class="text-sm font-medium text-gray-600 dark:text-gray-400 mb-2">Avg. Read Time</h4>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $blogMetrics['avg_read_time'] }}m</p>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm hover:shadow-md transition-all duration-300 transform hover:-translate-y-0.5 border border-gray-200 dark:border-gray-700 p-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Popular Posts</h3>
                    <div class="space-y-3">
                        @foreach($popularPosts as $post)
                        <div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
                            <div>
                                <p class="font-medium text-gray-900 dark:text-white">{{ $post->title }}</p>
                                <p class="text-sm text-gray-600 dark:text-gray-400">{{ $post->views }} views</p>
                            </div>
                            <div class="text-right">
                                <p class="font-semibold text-blue-600 dark:text-blue-400">{{ $post->engagement }}%</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400">engagement</p>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm hover:shadow-md transition-all duration-300 transform hover:-translate-y-0.5 border border-gray-200 dark:border-gray-700 p-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Blog Views Trend</h3>
                    <div class="relative h-64 w-full">
                        <canvas id="blogViewsChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Forum Tab -->
        <div id="forum-tab" class="tab-content" style="display: none;">
            <div class="grid grid-cols-1 lg:grid-cols-4 gap-6 mb-6">
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6 hover:shadow-md transition-all duration-300">
                    <h4 class="text-sm font-medium text-gray-600 dark:text-gray-400 mb-2">Total Questions</h4>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ number_format($forumMetrics['total_questions']) }}</p>
                </div>
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6 hover:shadow-md transition-all duration-300">
                    <h4 class="text-sm font-medium text-gray-600 dark:text-gray-400 mb-2">Total Answers</h4>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ number_format($forumMetrics['total_answers']) }}</p>
                </div>
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6 hover:shadow-md transition-all duration-300">
                    <h4 class="text-sm font-medium text-gray-600 dark:text-gray-400 mb-2">Active Contributors</h4>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ number_format($forumMetrics['active_contributors']) }}</p>
                </div>
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6 hover:shadow-md transition-all duration-300">
                    <h4 class="text-sm font-medium text-gray-600 dark:text-gray-400 mb-2">Answer Rate</h4>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ number_format($forumMetrics['answer_rate'], 1) }}%</p>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm hover:shadow-md transition-all duration-300 transform hover:-translate-y-0.5 border border-gray-200 dark:border-gray-700 p-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Top Contributors</h3>
                    <div class="space-y-3">
                        @foreach($topContributors as $contributor)
                        <div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
                            <div class="flex items-center space-x-3">
                                <img src="{{ $contributor->avatar ?? '/images/default-avatar.png' }}" class="w-8 h-8 rounded-full">
                                <div>
                                    <p class="font-medium text-gray-900 dark:text-white">{{ $contributor->name }}</p>
                                    <p class="text-sm text-gray-600 dark:text-gray-400">{{ $contributor->answers }} answers</p>
                                </div>
                            </div>
                            <div class="text-right">
                                <p class="font-semibold text-purple-600 dark:text-purple-400">{{ $contributor->points }}</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400">points</p>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm hover:shadow-md transition-all duration-300 transform hover:-translate-y-0.5 border border-gray-200 dark:border-gray-700 p-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Forum Activity</h3>
                    <div class="relative h-64 w-full">
                        <canvas id="forumActivityChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Visitors Tab -->
        <div id="visitors-tab" class="tab-content" style="display: none;">
            <div class="grid grid-cols-1 lg:grid-cols-4 gap-6 mb-6">
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6 hover:shadow-md transition-all duration-300">
                    <h4 class="text-sm font-medium text-gray-600 dark:text-gray-400 mb-2">Total Visitors</h4>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ number_format($visitorStats['total_visitors']) }}</p>
                </div>
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6 hover:shadow-md transition-all duration-300">
                    <h4 class="text-sm font-medium text-gray-600 dark:text-gray-400 mb-2">Unique Visitors</h4>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ number_format($visitorStats['unique_visitors']) }}</p>
                </div>
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6 hover:shadow-md transition-all duration-300">
                    <h4 class="text-sm font-medium text-gray-600 dark:text-gray-400 mb-2">Registered vs Anonymous</h4>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $visitorStats['registered_visitors'] }} / {{ $visitorStats['anonymous_visitors'] }}</p>
                </div>
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6 hover:shadow-md transition-all duration-300">
                    <h4 class="text-sm font-medium text-gray-600 dark:text-gray-400 mb-2">Mobile Traffic</h4>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ number_format($visitorStats['mobile_percentage'], 1) }}%</p>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm hover:shadow-md transition-all duration-300 transform hover:-translate-y-0.5 border border-gray-200 dark:border-gray-700 p-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Device Breakdown</h3>
                    <div class="relative h-64 w-full">
                        <canvas id="deviceBreakdownChart"></canvas>
                    </div>
                </div>
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm hover:shadow-md transition-all duration-300 transform hover:-translate-y-0.5 border border-gray-200 dark:border-gray-700 p-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Geographic Distribution</h3>
                    <div class="relative h-64 w-full">
                        <canvas id="geoChart"></canvas>
                    </div>
                </div>
            </div>

            <!-- Recent Visitors Table -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm hover:shadow-md transition-all duration-300 transform hover:-translate-y-0.5 border border-gray-200 dark:border-gray-700 p-6">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Recent Visitors</h3>
                    <button onclick="exportVisitorData()" class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg text-sm">
                        Export Data
                    </button>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-700">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Visitor ID</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Device</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Location</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">User</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Last Visit</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Visits</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                            @forelse($recentVisitors ?? collect() as $visitor)
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200">
                                            {{ Str::limit($visitor->visitor_id, 8) }}...
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                        <div class="flex items-center">
                                            @if($visitor->device_type === 'mobile')
                                                <i class="fas fa-mobile-alt text-orange-500 mr-2"></i>
                                            @elseif($visitor->device_type === 'tablet')
                                                <i class="fas fa-tablet-alt text-blue-500 mr-2"></i>
                                            @else
                                                <i class="fas fa-desktop text-green-500 mr-2"></i>
                                            @endif
                                            {{ $visitor->device_name ?? 'Unknown' }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                        @if($visitor->country && $visitor->city)
                                            {{ $visitor->country }}, {{ $visitor->city }}
                                        @else
                                            Unknown
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                        @if($visitor->user)
                                            <a href="{{ route('admin.users.edit', $visitor->user_id) }}" class="text-blue-600 dark:text-blue-400 hover:underline">
                                                {{ $visitor->user->first_name }} {{ $visitor->user->last_name }}
                                            </a>
                                        @else
                                            <span class="text-gray-400">Anonymous</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                        {{ $visitor->last_visit_at->format('M j, Y g:i A') }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">
                                            {{ $visitor->total_visits }}
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-8 text-center text-gray-500 dark:text-gray-400">
                                        No visitor data available
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Tab functionality
function showTab(tabName) {
    // Hide all tabs
    document.querySelectorAll('.tab-content').forEach(tab => {
        tab.style.display = 'none';
    });
    
    // Remove active class from all buttons
    document.querySelectorAll('.tab-button').forEach(button => {
        button.classList.remove('active');
    });
    
    // Show selected tab
    document.getElementById(tabName + '-tab').style.display = 'block';
    
    // Add active class to selected button
    document.querySelector(`[data-tab="${tabName}"]`).classList.add('active');
    
    // Initialize charts for the selected tab
    initializeTabCharts(tabName);
}

// Initialize charts based on active tab
function initializeTabCharts(tabName) {
    switch(tabName) {
        case 'overview':
            initializeOverviewCharts();
            break;
        case 'revenue':
            initializeRevenueCharts();
            break;
        case 'users':
            initializeUserCharts();
            break;
        case 'tests':
            initializeTestCharts();
            break;
        case 'subscriptions':
            initializeSubscriptionCharts();
            break;
        case 'blog':
            initializeBlogCharts();
            break;
        case 'forum':
            initializeForumCharts();
            break;
        case 'visitors':
            initializeVisitorCharts();
            break;
    }
}

// Overview Charts
function initializeOverviewCharts() {
    // Revenue Trends Chart
    const revenueCtx = document.getElementById('revenueChart');
    if (revenueCtx && !revenueCtx.chart) {
        revenueCtx.chart = new Chart(revenueCtx, {
            type: 'line',
            data: {
                labels: @json($revenueTrends->pluck('date')),
                datasets: [{
                    label: 'Revenue',
                    data: @json($revenueTrends->pluck('revenue')),
                    borderColor: 'rgb(34, 197, 94)',
                    backgroundColor: 'rgba(34, 197, 94, 0.1)',
                    tension: 0.4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return 'RWF ' + value.toLocaleString();
                            }
                        }
                    }
                }
            }
        });
    }

    // User Activity Chart
    const userActivityCtx = document.getElementById('userActivityChart');
    if (userActivityCtx && !userActivityCtx.chart) {
        userActivityCtx.chart = new Chart(userActivityCtx, {
            type: 'bar',
            data: {
                labels: @json($userActivityTrends->pluck('date')),
                datasets: [{
                    label: 'Active Users',
                    data: @json($userActivityTrends->pluck('active_users')),
                    backgroundColor: 'rgba(59, 130, 246, 0.8)'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    }
}

// Revenue Charts
function initializeRevenueCharts() {
    // Revenue by Plan Chart
    const revenueByPlanCtx = document.getElementById('revenueByPlanChart');
    if (revenueByPlanCtx && !revenueByPlanCtx.chart) {
        revenueByPlanCtx.chart = new Chart(revenueByPlanCtx, {
            type: 'doughnut',
            data: {
                labels: @json($revenueByPlan->pluck('plan_name')),
                datasets: [{
                    data: @json($revenueByPlan->pluck('revenue')),
                    backgroundColor: [
                        'rgba(59, 130, 246, 0.8)',
                        'rgba(34, 197, 94, 0.8)',
                        'rgba(251, 146, 60, 0.8)',
                        'rgba(147, 51, 234, 0.8)'
                    ]
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false
            }
        });
    }
}

// Visitor Charts
function initializeVisitorCharts() {
    // Device Breakdown Chart
    const deviceCtx = document.getElementById('deviceBreakdownChart');
    if (deviceCtx && !deviceCtx.chart) {
        deviceCtx.chart = new Chart(deviceCtx, {
            type: 'doughnut',
            data: {
                labels: ['Mobile', 'Desktop', 'Tablet'],
                datasets: [{
                    data: [{{ $visitorStats['mobile_visitors'] }}, {{ $visitorStats['desktop_visitors'] }}, {{ $visitorStats['tablet_visitors'] }}],
                    backgroundColor: [
                        'rgba(251, 146, 60, 0.8)',
                        'rgba(34, 197, 94, 0.8)',
                        'rgba(59, 130, 246, 0.8)'
                    ]
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false
            }
        });
    }
}

// Export functions
function exportAllAnalytics() {
    // Implement comprehensive export functionality
    window.location.href = '{{ route('admin.reports.export', 'comprehensive') }}';
}

function exportVisitorData() {
    fetch('{{ route('admin.reports.visitors.export') }}')
        .then(response => response.json())
        .then(data => {
            const blob = new Blob([convertToCSV(data.data)], { type: 'text/csv' });
            const url = window.URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = data.filename;
            document.body.appendChild(a);
            a.click();
            document.body.removeChild(a);
            window.URL.revokeObjectURL(url);
        })
        .catch(error => {
            console.error('Export failed:', error);
        });
}

function convertToCSV(data) {
    if (!data || data.length === 0) return '';
    
    const headers = Object.keys(data[0]);
    const csvHeaders = headers.join(',');
    const csvRows = data.map(row => 
        headers.map(header => `"${row[header] || ''}"`).join(',')
    );
    
    return csvHeaders + '\n' + csvRows.join('\n');
}

// Initialize overview charts on page load
document.addEventListener('DOMContentLoaded', function() {
    initializeOverviewCharts();
});

// Date range change handler
document.getElementById('dateRange').addEventListener('change', function() {
    const selectedRange = this.value;
    // Reload page with new date range
    window.location.href = `{{ route('admin.reports.index') }}?period=${selectedRange}`;
});
</script>
@endpush
