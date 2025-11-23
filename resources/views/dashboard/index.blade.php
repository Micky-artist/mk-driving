@extends('layouts.dashboard')

@push('styles')
<style>
    .progress-bar {
        height: 0.5rem;
        background-color: #e5e7eb;
        border-radius: 0.25rem;
        overflow: hidden;
    }
    .progress-fill {
        height: 100%;
        background-color: #3b82f6;
        border-radius: 0.25rem;
    }
</style>
@endpush

@section('title')
{{ __('dashboard.title') }}
@endsection

@section('dashboard-content')
<div class="space-y-4 sm:space-y-6">
    <!-- Welcome Header -->
    <div class="bg-gradient-to-r from-blue-600 to-purple-600 rounded-lg p-4 sm:p-6 text-white">
        <h1 class="text-xl sm:text-2xl font-bold mb-2">{{ Auth::user()->first_name }}, {{ __('dashboard.welcome_back') }}!</h1>
        <p class="text-blue-100 text-sm sm:text-base">{{ __('dashboard.welcome_message') }}</p>
    </div>

    <!-- Current Subscription -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="p-4 sm:p-6">
            <div class="flex items-center gap-2 text-sm sm:text-base font-medium">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-500" viewBox="0 0 20 20" fill="currentColor">
                    <path d="M4 4a2 2 0 00-2 2v1h16V6a2 2 0 00-2-2H4z" />
                    <path fill-rule="evenodd" d="M18 9H2v5a2 2 0 002 2h12a2 2 0 002-2V9zM4 13a1 1 0 011-1h1a1 1 0 110 2H5a1 1 0 01-1-1zm5-1a1 1 0 100 2h1a1 1 0 100-2H9z" clip-rule="evenodd" />
                </svg>
                {{ __('dashboard.current_subscription') }}
            </div>
            
            @if($currentSubscriptions->count() > 0)
                <div class="mt-4 space-y-4">
                    @foreach($currentSubscriptions as $subscription)
                        <div class="border rounded-lg p-4">
                            <div class="flex justify-between items-start">
                                <div>
                                    <h3 class="font-medium">{{ $subscription->plan->name }}</h3>
                                    <p class="text-sm text-gray-500">
                                        {{ __('dashboard.valid_until') }}: {{ $subscription->end_date->format('M d, Y') }}
                                    </p>
                                </div>
                                <span class="px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                    {{ __('dashboard.subscription.active') }}
                                </span>
                            </div>
                            <div class="mt-3">
                                <div class="flex justify-between text-sm mb-1">
                                    <span>{{ str_replace(':days', $subscription->days_remaining, __('dashboard.days_remaining')) }}: {{ now()->diffInDays($subscription->end_date) }}</span>
                                    <span>{{ round(($subscription->end_date->diffInDays(now()) / $subscription->end_date->diffInDays($subscription->start_date)) * 100) }}%</span>
                                </div>
                                <div class="w-full bg-gray-200 rounded-full h-2">
                                    <div class="bg-blue-600 h-2 rounded-full" style="width: {{ ($subscription->end_date->diffInDays(now()) / $subscription->end_date->diffInDays($subscription->start_date)) * 100 }}%"></div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="mt-4 text-center py-6 border-2 border-dashed rounded-lg">
                    <p class="text-gray-500 mb-4">{{ __('dashboard.subscription.no_active') }}</p>
                    <a href="{{ route('plans', ['locale' => app()->getLocale()]) }}" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        {{ __('dashboard.subscription.subscribe_now') }}
                    </a>
                </div>
            @endif
        </div>
    </div><!-- Stats Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6">
        <!-- Available Quizzes -->
        <a href="{{ route('dashboard.quizzes.index', ['locale' => app()->getLocale()]) }}" class="block group">
            <div class="bg-white rounded-lg shadow p-4 sm:p-6 hover:shadow-md transition-shadow duration-200 border border-transparent hover:border-blue-200">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-500 group-hover:text-blue-600 transition-colors truncate">{{ __('Available Quizzes') }}</p>
                        <p class="mt-1 text-2xl font-semibold text-blue-600 group-hover:text-blue-700 transition-colors">{{ $stats['total_quizzes'] }}</p>
                    </div>
                    <div class="p-3 rounded-full bg-blue-50 group-hover:bg-blue-100 text-blue-600 transition-colors">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                    </div>
                </div>
                <div class="mt-2 text-right">
                    <span class="text-xs text-blue-500 font-medium inline-flex items-center">
                        {{ __('View all') }}
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 ml-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M9 5l7 7-7 7" />
                        </svg>
                    </span>
                </div>
            </div>
        </a>

        <!-- In Progress Quizzes -->
        <a href="{{ route('dashboard.quizzes.in-progress', ['locale' => app()->getLocale()]) }}" class="block group">
            <div class="bg-white rounded-lg shadow p-4 sm:p-6 hover:shadow-md transition-shadow duration-200 border border-transparent hover:border-purple-200">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-500 group-hover:text-purple-600 transition-colors truncate">{{ __('In Progress') }}</p>
                        <p class="mt-1 text-2xl font-semibold text-purple-600 group-hover:text-purple-700 transition-colors">{{ $stats['in_progress_count'] }}</p>
                    </div>
                    <div class="p-3 rounded-full bg-purple-50 group-hover:bg-purple-100 text-purple-600 transition-colors">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                </div>
                <div class="mt-2 text-right">
                    <span class="text-xs text-purple-500 font-medium inline-flex items-center">
                        {{ __('Continue learning') }}
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 ml-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M9 5l7 7-7 7" />
                        </svg>
                    </span>
                </div>
            </div>
        </a>

        <!-- Completed Quizzes -->
        <div class="bg-white rounded-lg shadow p-4 sm:p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500 truncate">{{ __('dashboard.stats.completed') }}</p>
                    <p class="mt-1 text-2xl font-semibold text-green-600">{{ $stats['completed_count'] }}</p>
                </div>
                <div class="p-3 rounded-full bg-green-100 text-green-600">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
            </div>
        </div>


        <!-- Average Score -->
        <div class="bg-white rounded-lg shadow p-4 sm:p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500 truncate">{{ __('dashboard.stats.average_score') }}</p>
                    <p class="mt-1 text-2xl font-semibold text-purple-600">{{ number_format($stats['average_score'], 1) }}%</p>
                </div>
                <div class="p-3 rounded-full bg-purple-100 text-purple-600">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                    </svg>
                </div>
            </div>
        </div>
    </div>


    <!-- Available Subscription Plans -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="p-4 sm:p-6">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h2 class="text-lg font-medium text-gray-900">{{ __('dashboard.subscription_plans.available_plans') }}</h2>
                    <p class="mt-1 text-sm text-gray-500">{{ __('dashboard.subscription_plans.upgrade_description') }}</p>
                </div>
            </div>
            
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-3 sm:gap-4">
                @forelse($availablePlans as $plan)
                    @php
                        $isCurrentPlan = $currentSubscriptions->contains('subscription_plan_id', $plan->id);
                        $planName = is_string($plan->name) ? json_decode($plan->name, true) : $plan->name;
                        $localizedName = $planName[app()->getLocale()] ?? $planName['en'] ?? 'Unnamed Plan';
                    @endphp
                    
                    <div class="p-3 sm:p-4 border rounded-lg transition-all hover:shadow-md {{ $isCurrentPlan ? 'bg-green-50 border-green-200' : 'hover:border-gray-300' }}">
                        <div class="flex items-start justify-between mb-2">
                            <h3 class="font-semibold text-xs sm:text-sm leading-tight">{{ $localizedName }}</h3>
                            @if($isCurrentPlan)
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                    {{ __('dashboard.subscription_plans.current') }}
                                </span>
                            @endif
                        </div>
                        <div class="space-y-1">
                            <p class="text-base sm:text-lg font-bold text-green-600">
                                {{ $plan->price == 0 ? __('dashboard.subscription_plans.free') : number_format($plan->price) . ' RWF' }}
                            </p>
                            <p class="text-xs text-gray-600">{{ $plan->duration }} {{ __('dashboard.days') }}</p>
                        </div>
                        <a href="{{ route('subscriptions', ['locale' => app()->getLocale()]) }}" class="mt-2 inline-block w-full">
                            <button 
                                type="button"
                                class="w-full flex justify-center py-1.5 px-3 border border-gray-300 rounded-md shadow-sm text-xs sm:text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 disabled:opacity-50 disabled:cursor-not-allowed"
                                {{ $isCurrentPlan ? 'disabled' : '' }}
                            >
                                {{ $isCurrentPlan ? __('dashboard.subscription_plans.current_plan') : __('dashboard.subscription_plans.view_details') }}
                            </button>
                        </a>
                    </div>
                @empty
                    <div class="col-span-full text-center py-6">
                        <p class="text-sm text-gray-500">{{ __('dashboard.subscription_plans.no_plans_available') }}</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    <!-- New Quizzes -->
    @if($newQuizzes->count() > 0)
        <div class="bg-white rounded-lg shadow overflow-hidden">
            <div class="p-4 sm:p-6">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-lg font-medium text-gray-900">{{ __('dashboard.quizzes.new') }}</h2>
                    @if(Route::has('quizzes.index'))
                        <a href="{{ route('dashboard.quizzes.index', ['locale' => app()->getLocale()]) }}" class="text-sm font-medium text-blue-600 hover:text-blue-500">{{ __('dashboard.quizzes.view_all') }}</a>
                    @endif
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                    @foreach($newQuizzes as $quiz)
                        <div class="border rounded-lg overflow-hidden hover:shadow-md transition-shadow">
                            <div class="p-4">
                                <div class="flex justify-between items-start">
                                    <h3 class="font-medium text-gray-900">{{ $quiz->title }}</h3>
                                    @if($quiz->subscription_plan_id)
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                            {{ $quiz->subscriptionPlan->name }}
                                        </span>
                                    @endif
                                </div>
                                <p class="mt-1 text-sm text-gray-500">{{ $quiz->description }}</p>
                                <div class="mt-4 flex items-center justify-between">
                                    <div class="flex items-center text-sm text-gray-500">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        {{ $quiz->time_limit_minutes }} {{ __('dashboard.quizzes.min') }}
                                    </div>
                                    <a href="{{ route('dashboard.quizzes.show', ['locale' => app()->getLocale(), 'quiz' => $quiz]) }}" class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                        {{ __('dashboard.quizzes.start_quiz') }}
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    @endif

    <!-- In Progress Quizzes -->
    @if($inProgressQuizzes->count() > 0)
        <div class="bg-white rounded-lg shadow overflow-hidden">
            <div class="p-4 sm:p-6">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-lg font-medium text-gray-900">{{ __('dashboard.quizzes.continue_learning') }}</h2>
                    @if(Route::has('my-quizzes'))
                        <a href="{{ route('my-quizzes') }}" class="text-sm font-medium text-blue-600 hover:text-blue-500">{{ __('View all') }}</a>
                    @endif
                </div>
                <div class="space-y-4">
                    @foreach($inProgressQuizzes as $attempt)
                        <div class="border rounded-lg p-4 hover:shadow-md transition-shadow">
                            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
                                <div class="flex-1">
                                    <h3 class="font-medium text-gray-900">{{ $attempt->quiz->title }}</h3>
                                    <div class="mt-1 flex items-center text-sm text-gray-500">
                                        <span class="mr-4">
                                            {{ __('dashboard.upgrade_now') }} {{ $attempt->created_at->format('M d, Y') }}
                                        </span>
                                        <span class="flex items-center">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                            {{ $attempt->time_spent }} / {{ $attempt->quiz->time_limit_minutes }} {{ __('dashboard.quizzes.min') }}
                                        </span>
                                    </div>
                                </div>
                                <div class="mt-3 sm:mt-0 sm:ml-4">
                                    <a href="{{ route('quizzes.attempt', ['quiz' => $attempt->quiz_id, 'locale' => app()->getLocale()]) }}" class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                        {{ __('dashboard.quizzes.continue') }}
                                    </a>
                                </div>
                            </div>
                            <div class="mt-3">
                                @php
                                    $progressPercentage = $attempt->total_questions > 0 
                                        ? round(($attempt->current_question / $attempt->total_questions) * 100)
                                        : 0;
                                @endphp
                                <div class="flex justify-between text-sm mb-1">
                                    <span>{{ __('dashboard.quizzes.progress') }}</span>
                                    <span>{{ $progressPercentage }}%</span>
                                </div>
                                <div class="w-full bg-gray-200 rounded-full h-2">
                                    <div class="bg-blue-600 h-2 rounded-full" style="width: {{ $progressPercentage }}%"></div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    @endif

    <!-- Completed Quizzes -->
    @if($completedQuizzes->count() > 0)
        <div class="bg-white rounded-lg shadow overflow-hidden">
            <div class="p-4 sm:p-6">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-lg font-medium text-gray-900">{{ __('dashboard.quizzes.recently_completed') }}</h2>
                    <a href="{{ route('dashboard.quizzes.index', ['locale' => app()->getLocale()]) }}" class="text-sm font-medium text-blue-600 hover:text-blue-500">{{ __('dashboard.quizzes.view_all') }}</a>
                </div>
                
                <div class="space-y-4">
                    @foreach($completedQuizzes->take(5) as $attempt)
                        @php
                            $quiz = $attempt->quiz;
                            $quizTitle = is_array($quiz->title) ? $quiz->title[app()->getLocale()] ?? $quiz->title['en'] ?? 'Untitled Quiz' : $quiz->title;
                            $score = $attempt->score ?? 0;
                            $totalMarks = $quiz->questions->sum('marks');
                            $percentage = $totalMarks > 0 ? round(($score / $totalMarks) * 100) : 0;
                        @endphp
                        
                        <div class="flex justify-between items-center p-4 border rounded-lg hover:shadow-md transition-shadow">
                            <div>
                                <h4 class="font-semibold text-gray-900">
                                    {{ $quizTitle }}
                                </h4>
                                <p class="text-sm text-gray-600">
                                    {{ __('dashboard.quizzes.score') }}: {{ $score }}/{{ $totalMarks }} ({{ $percentage }}%)
                                </p>
                            </div>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                {{ __('dashboard.quizzes.completed') }}
                            </span>
                        </div>
                    @endforeach
                    
                    @if($completedQuizzes->count() > 5)
                        <div class="text-center mt-4">
                            <a href="{{ route('quizzes.index', ['locale' => app()->getLocale()]) }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                {{ __('dashboard.quizzes.view_all_completed') }}
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    @else
        <div class="bg-white rounded-lg shadow overflow-hidden">
            <div class="p-4 sm:p-6">
                <div class="text-center py-12">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900">{{ __('dashboard.quizzes.no_completed_quizzes') }}</h3>
                    <p class="mt-1 text-sm text-gray-500">
                        {{ __('dashboard.quizzes.complete_to_see_results') }}
                    </p>
                    <div class="mt-6">
                        <a href="{{ route('dashboard.quizzes.index', ['locale' => app()->getLocale()]) }}" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            {{ __('dashboard.quizzes.browse_quizzes') }}
                        </a>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
@endsection