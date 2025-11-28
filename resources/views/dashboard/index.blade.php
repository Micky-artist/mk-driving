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
    <div class="space-y-4 sm:space-y-2">
        <!-- Current Subscription -->
        <div class="space-y-4">
            <div class="bg-white dark:bg-gray-800 rounded-xl overflow-hidden shadow-sm dark:shadow-none border border-gray-200 dark:border-gray-700">
                <!-- Subscription Header -->
                <div class="p-2 bg-gradient-to-r from-blue-500 to-blue-600 dark:from-blue-800 dark:to-blue-900 text-white">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-1 md:gap-4">
                        <div class="space-y-1">
                            <div class="flex items-center gap-3">
                                <div class="p-2 bg-white/20 dark:bg-white/10 rounded-lg">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                                        stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                                    </svg>
                                </div>
                                <div>
                                    <h2 class="text-xl md:text-2xl font-bold">{{ __('dashboard.current_subscription') }}</h2>
                                </div>
                            </div>
                        </div>

                        @if ($currentSubscriptions->count() > 0)
                            @php
                                $nearestExpiry = $currentSubscriptions->min('ends_at');
                            @endphp
                            <div class="flex items-center gap-2 sm:gap-4">
                                <p class="text-blue-100 text-sm whitespace-nowrap">{{ __('dashboard.valid_until') }}</p>
                                <p class="text-base sm:text-lg font-semibold">
                                    @if ($nearestExpiry)
                                        {{ $nearestExpiry->format('M d, Y') }}
                                    @else
                                        {{ __('dashboard.no_end_date') }}
                                    @endif
                                </p>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Plan Cards -->
                @if ($currentSubscriptions->count() > 0)
                    <div class="px-0 py-2 md:p-2 bg-gray-50 dark:bg-gray-800">
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    @foreach ($currentSubscriptions->take(3) as $subscription)
                        @php
                            // Get plan name with fallback
                            $planName = is_string($subscription->plan->name)
                                ? json_decode($subscription->plan->name, true)
                                : $subscription->plan->name;
                            $planDisplayName = $planName[app()->getLocale()] ?? ($planName['en'] ?? 'N/A');

                            // Get plan description with fallback
                            $planDescription = is_string($subscription->plan->description)
                                ? json_decode($subscription->plan->description, true)
                                : $subscription->plan->description ?? [];
                            $planDisplayDescription =
                                $planDescription[app()->getLocale()] ?? ($planDescription['en'] ?? '');

                            // Calculate time remaining and progress
                            $startDate = $subscription->starts_at ?? now();
                            $endDate = $subscription->ends_at;
                            $hoursRemaining = $endDate ? now()->diffInHours($endDate, false) : null;
                            $totalHours = $endDate ? $startDate->diffInHours($endDate) : 0;
                            $hoursUsed = $endDate ? $startDate->diffInHours(now()) : 0;

                            // Format time remaining (hours if < 24h, otherwise days, no decimals)
                            $timeRemaining = null;
                            $timeUnit = '';
                            if ($hoursRemaining !== null) {
                                if ($hoursRemaining < 24) {
                                    $timeRemaining = max(0, $hoursRemaining);
                                    $timeUnit = $timeRemaining === 1 ? 'hour' : 'hours';
                                } else {
                                    $timeRemaining = max(0, (int) ($hoursRemaining / 24));
                                    $timeUnit = $timeRemaining === 1 ? 'day' : 'days';
                                }
                            }

                            $progressPercent =
                                $totalHours > 0 ? min(100, max(0, round(($hoursUsed / $totalHours) * 100))) : 0;

                            // For backward compatibility with the gradient logic below
                            $daysRemaining = $hoursRemaining !== null ? (int) ($hoursRemaining / 24) : null;

                            // Define gradient based on days remaining
                            $gradient = 'from-blue-600 to-blue-700';
                            if ($daysRemaining !== null && $daysRemaining < 7) {
                                $gradient = 'from-yellow-500 to-yellow-600';
                            }
                            if ($daysRemaining !== null && $daysRemaining < 3) {
                                $gradient = 'from-red-500 to-red-600';
                            }
                        @endphp

                        <div
                            class="bg-white dark:bg-gray-800 rounded-lg shadow-md overflow-hidden border border-gray-200 dark:border-gray-700 hover:shadow-lg transition-all duration-200 transform hover:-translate-y-1 flex flex-col h-full">
                            <!-- Card Header -->
                            <div class="bg-gradient-to-r {{ $gradient }} p-5 text-white flex-shrink-0">
                                <div class="flex justify-between items-start gap-3 mb-4">
                                    <div class="flex-1 min-w-0">
                                        <h3 class="text-lg font-bold truncate">{{ $planDisplayName }}</h3>
                                        @if (!empty($planDisplayDescription))
                                            <p class="text-sm text-white/90 mt-1 line-clamp-2">
                                                {{ $planDisplayDescription }}
                                            </p>
                                        @endif
                                    </div>
                                    @php
                                        $userQuizAttempts = auth()
                                            ->user()
                                            ->quizAttempts()
                                            ->whereHas('quiz', function ($query) use ($subscription) {
                                                $query->where(
                                                    'subscription_plan_slug',
                                                    $subscription->subscription_plan_slug,
                                                );
                                            })
                                            ->where('status', 'completed')
                                            ->get();
                                        $hasAttempts = $userQuizAttempts->isNotEmpty();
                                        $averageScore = $hasAttempts
                                            ? round($userQuizAttempts->avg('score_percentage'))
                                            : 0;
                                    @endphp
                                    <div class="bg-white/20 dark:bg-white/10 rounded-lg p-2 text-center flex-shrink-0"
                                        style="min-width: 80px;">
                                        <div class="text-xs text-white/90 dark:text-white/80 whitespace-nowrap">
                                            {{ $hasAttempts ? __('dashboard.quizzes.average_score') : __('dashboard.quizzes.attempts') }}
                                        </div>
                                        <div class="text-2xl font-bold leading-tight mt-1">
                                            @if ($hasAttempts)
                                                {{ $averageScore }}%
                                            @else
                                                0
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                <div
                                    class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-white/20 dark:bg-white/10">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 mr-1" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                    </svg>
                                    {{ $subscription->quizzes_count ?? 0 }}
                                    {{ trans_choice('dashboard.quizzes.available', $subscription->quizzes_count ?? 0) }}
                                </div>
                            </div>

                            <!-- Progress Section -->
                            <div class="p-5 bg-gradient-to-b from-gray-50 to-white dark:from-gray-800 dark:to-gray-700 border-b border-gray-100 dark:border-gray-700 flex-shrink-0">
                                <div class="flex justify-between items-center text-sm mb-2">
                                    <span class="text-gray-600 dark:text-gray-300 font-medium">{{ __('dashboard.quizzes.progress') }}</span>
                                    <span class="font-semibold text-gray-900 dark:text-white">{{ $progressPercent }}%</span>
                                </div>
                                <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2.5 overflow-hidden">
                                    <div class="bg-gradient-to-r {{ $gradient }} h-2.5 rounded-full transition-all duration-500 ease-out"
                                        style="width: {{ $progressPercent }}%;"></div>
                                </div>
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-2 flex items-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 mr-1" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                    @if ($endDate)
                                        {{ __('dashboard.valid_until') }}: <span
                                            class="font-medium ml-1">{{ $endDate->format('M d, Y') }}</span>
                                    @else
                                        {{ __('dashboard.no_end_date') }}
                                    @endif
                                </p>
                            </div>

                            <!-- Status Section -->
                            <div class="p-5 bg-white dark:bg-gray-800 flex-shrink-0">
                                <div class="flex justify-between items-center">
                                    <div class="min-w-0 flex-1">
                                        <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1">
                                            {{ __('dashboard.status') }}</p>
                                        <p class="font-semibold text-gray-900 truncate">
                                            @if ($subscription->status === 'ACTIVE')
                                                {{ __('dashboard.subscription.active') }}
                                            @elseif($subscription->status === 'CANCELLED')
                                                {{ __('dashboard.subscription.cancelled') }}
                                            @elseif($subscription->status === 'EXPIRED')
                                                {{ __('dashboard.subscription.expired') }}
                                            @else
                                                {{ ucfirst(strtolower($subscription->status)) }}
                                            @endif
                                        </p>
                                    </div>
                                    <span
                                        class="inline-flex items-center px-3 py-1.5 rounded-full text-xs font-semibold flex-shrink-0 ml-3 {{ $subscription->status === 'ACTIVE'
                                            ? 'bg-green-100 text-green-800'
                                            : ($subscription->status === 'PENDING'
                                                ? 'bg-yellow-100 text-yellow-800'
                                                : 'bg-gray-100 text-gray-800') }}">
                                        <span
                                            class="w-1.5 h-1.5 rounded-full mr-1.5 {{ $subscription->status === 'ACTIVE'
                                                ? 'bg-green-500'
                                                : ($subscription->status === 'PENDING'
                                                    ? 'bg-yellow-500'
                                                    : 'bg-gray-500') }}"></span>
                                        @if ($subscription->status === 'ACTIVE')
                                            {{ __('dashboard.subscription.active') }}
                                        @elseif($subscription->status === 'PENDING')
                                            {{ __('dashboard.subscription.pending') }}
                                        @elseif($subscription->status === 'CANCELLED')
                                            {{ __('dashboard.subscription.cancelled') }}
                                        @elseif($subscription->status === 'EXPIRED')
                                            {{ __('dashboard.subscription.expired') }}
                                        @else
                                            {{ $subscription->status }}
                                        @endif
                                    </span>
                                </div>
                            </div>
                        </div>
                    @endforeach
                        </div>

                        @if ($currentSubscriptions->count() > 3)
                            <div class="text-center mt-4 px-6 pb-6">
                                <a href="{{ route('dashboard.quizzes.index', ['locale' => app()->getLocale()]) }}"
                                    class="inline-flex items-center text-blue-600 hover:text-blue-800 font-medium text-sm transition-colors">
                                    {{ __('dashboard.view_all_quizzes') }}
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 ml-1" fill="none" viewBox="0 0 24 24"
                                        stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                    </svg>
                                </a>
                            </div>
                        @endif
                    </div>
                @else
                <div class="bg-white rounded-lg shadow-md p-8 text-center border border-gray-200">
                    <div
                        class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-yellow-100 text-yellow-600 mb-4">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">{{ __('subscription.no_subscription') }}</h3>
                    <p class="text-gray-600 mb-6 max-w-md mx-auto">{{ __('subscription.upgrade_message') }}</p>
                    <div class="flex flex-col sm:flex-row justify-center gap-3">
                        <a href="{{ route('plans', ['locale' => app()->getLocale()]) }}"
                            class="px-6 py-3 border border-transparent text-base font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors">
                            {{ __('dashboard.subscription.subscribe_now') }}
                        </a>
                    </div>
                </div>
            @endif
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6">
            <!-- Available Quizzes -->
            <a href="{{ route('dashboard.quizzes.index', ['locale' => app()->getLocale()]) }}" class="block group">
                <div
                    class="bg-white rounded-lg shadow p-4 sm:p-6 hover:shadow-md transition-shadow duration-200 border border-transparent hover:border-blue-200">
                    <div class="flex items-center justify-between">
                        <div>
                            <p
                                class="text-sm font-medium text-gray-500 group-hover:text-blue-600 transition-colors truncate">
                                {{ __('Available Quizzes') }}</p>
                            <p
                                class="mt-1 text-2xl font-semibold text-blue-600 group-hover:text-blue-700 transition-colors">
                                {{ $stats['total_quizzes'] }}</p>
                        </div>
                        <div class="p-3 rounded-full bg-blue-50 group-hover:bg-blue-100 text-blue-600 transition-colors">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                        </div>
                    </div>
                    <div class="mt-2 text-right">
                        <span class="text-xs text-blue-500 font-medium inline-flex items-center">
                            {{ __('View all') }}
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 ml-0.5" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M9 5l7 7-7 7" />
                            </svg>
                        </span>
                    </div>
                </div>
            </a>

            <!-- In Progress Quizzes -->
            <a href="{{ route('dashboard.quizzes.in-progress', ['locale' => app()->getLocale()]) }}" class="block group">
                <div
                    class="bg-white rounded-lg shadow p-4 sm:p-6 hover:shadow-md transition-shadow duration-200 border border-transparent hover:border-purple-200">
                    <div class="flex items-center justify-between">
                        <div>
                            <p
                                class="text-sm font-medium text-gray-500 group-hover:text-purple-600 transition-colors truncate">
                                {{ __('In Progress') }}</p>
                            <p
                                class="mt-1 text-2xl font-semibold text-purple-600 group-hover:text-purple-700 transition-colors">
                                {{ $stats['in_progress_count'] }}</p>
                        </div>
                        <div
                            class="p-3 rounded-full bg-purple-50 group-hover:bg-purple-100 text-purple-600 transition-colors">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                    </div>
                    <div class="mt-2 text-right">
                        <span class="text-xs text-purple-500 font-medium inline-flex items-center">
                            {{ __('Continue learning') }}
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 ml-0.5" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor">
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
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                </div>
            </div>


            <!-- Average Score -->
            <div class="bg-white rounded-lg shadow p-4 sm:p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-500 truncate">{{ __('dashboard.stats.average_score') }}
                        </p>
                        <p class="mt-1 text-2xl font-semibold text-purple-600">
                            {{ number_format($stats['average_score'], 1) }}%</p>
                    </div>
                    <div class="p-3 rounded-full bg-purple-100 text-purple-600">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
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
                        <h2 class="text-lg font-medium text-gray-900">
                            {{ __('dashboard.subscription_plans.available_plans') }}</h2>
                        <p class="mt-1 text-sm text-gray-500">{{ __('dashboard.subscription_plans.upgrade_description') }}
                        </p>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-3 sm:gap-4">
                    @forelse($availablePlans as $plan)
                        @php
                            $isCurrentPlan = $currentSubscriptions->contains('subscription_plan_id', $plan->id);
                            $planName = is_string($plan->name) ? json_decode($plan->name, true) : $plan->name;
                            $localizedName = $planName[app()->getLocale()] ?? ($planName['en'] ?? 'Unnamed Plan');
                        @endphp

                        <div
                            class="p-3 sm:p-4 border rounded-lg transition-all hover:shadow-md {{ $isCurrentPlan ? 'bg-green-50 border-green-200' : 'hover:border-gray-300' }}">
                            <div class="flex items-start justify-between mb-2">
                                <h3 class="font-semibold text-xs sm:text-sm leading-tight">{{ $localizedName }}</h3>
                                @if ($isCurrentPlan)
                                    <span
                                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
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
                            <a href="{{ route('subscriptions', ['locale' => app()->getLocale()]) }}"
                                class="mt-2 inline-block w-full">
                                <button type="button"
                                    class="w-full flex justify-center py-1.5 px-3 border border-gray-300 rounded-md shadow-sm text-xs sm:text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 disabled:opacity-50 disabled:cursor-not-allowed"
                                    {{ $isCurrentPlan ? 'disabled' : '' }}>
                                    {{ $isCurrentPlan ? __('dashboard.subscription_plans.current_plan') : __('dashboard.subscription_plans.view_details') }}
                                </button>
                            </a>
                        </div>
                    @empty
                        <div class="col-span-full text-center py-6">
                            <p class="text-sm text-gray-500">{{ __('dashboard.subscription_plans.no_plans_available') }}
                            </p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- New Quizzes -->
        <!-- New Quizzes Section -->
        <div class="space-y-6">
            <div class="flex items-center justify-between">
                <h2 class="text-2xl font-bold text-gray-900">
                    @if ($currentSubscriptions->isEmpty())
                        {{ __('dashboard.quizzes.premium_quizzes') }}
                    @else
                        {{ __('dashboard.quizzes.new_quizzes') }}
                    @endif
                </h2>
                @if (Route::has('quizzes.index'))
                    <a href="{{ route('dashboard.quizzes.index', ['locale' => app()->getLocale()]) }}"
                        class="inline-flex items-center text-blue-600 hover:text-blue-800 font-medium text-sm transition-colors">
                        {{ $currentSubscriptions->isEmpty() ? __('dashboard.quizzes.view_all_plans') : __('dashboard.quizzes.view_all_quizzes') }}
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 ml-1" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </a>
                @endif
            </div>

            @if ($newQuizzes->count() > 0)
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach ($newQuizzes as $quiz)
                        <x-quiz.quiz-card :quiz="$quiz" />
                    @endforeach
                </div>

                @if ($currentSubscriptions->isEmpty())
                    <div class="mt-6 text-center">
                        <a href="{{ route('plans', ['locale' => app()->getLocale()]) }}"
                            class="inline-flex items-center justify-center px-6 py-3 border border-transparent text-base font-medium rounded-md text-white bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 shadow-lg hover:shadow-xl transition-all duration-300 transform hover:-translate-y-0.5">
                            {{ __('dashboard.quizzes.unlock_all_quizzes') }}
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 ml-2" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M14 5l7 7m0 0l-7 7m7-7H3" />
                            </svg>
                        </a>
                    </div>
                @endif
            @else
                <div class="bg-white rounded-xl shadow-sm p-8 text-center border-2 border-dashed border-gray-200">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 mx-auto text-gray-400" fill="none"
                        viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                            d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z" />
                    </svg>
                    <h3 class="mt-2 text-lg font-medium text-gray-900">
                        {{ __('dashboard.quizzes.no_new_quizzes_title') }}
                    </h3>
                    <p class="mt-1 text-sm text-gray-500">
                        {{ __('dashboard.quizzes.no_new_quizzes_description') }}
                    </p>
                    @if ($currentSubscriptions->isEmpty())
                        <div class="mt-6">
                            <a href="{{ route('plans', ['locale' => app()->getLocale()]) }}"
                                class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                {{ __('dashboard.quizzes.browse_plans') }}
                            </a>
                        </div>
                    @endif
                </div>
            @endif
        </div>

        <!-- In Progress Quizzes -->
        @if ($inProgressQuizzes->count() > 0)
            <div class="bg-white rounded-lg shadow overflow-hidden">
                <div class="p-4 sm:p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h2 class="text-lg font-medium text-gray-900">{{ __('dashboard.quizzes.continue_learning') }}</h2>
                        @if (Route::has('my-quizzes'))
                            <a href="{{ route('my-quizzes') }}"
                                class="text-sm font-medium text-blue-600 hover:text-blue-500">{{ __('View all') }}</a>
                        @endif
                    </div>
                    <div class="space-y-4">
                        @foreach ($inProgressQuizzes as $attempt)
                            <div class="border rounded-lg p-4 hover:shadow-md transition-shadow">
                                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
                                    <div class="flex-1">
                                        <h3 class="font-medium text-gray-900">{{ $attempt->quiz->title }}</h3>
                                        <div class="mt-1 flex items-center text-sm text-gray-500">
                                            <span class="mr-4">
                                                {{ __('dashboard.upgrade_now') }}
                                                {{ $attempt->created_at->format('M d, Y') }}
                                            </span>
                                            <span class="flex items-center">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1"
                                                    fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                </svg>
                                                {{ $attempt->time_spent }} / {{ $attempt->quiz->time_limit_minutes }}
                                                {{ __('dashboard.quizzes.min') }}
                                            </span>
                                        </div>
                                    </div>
                                    <div class="mt-3 sm:mt-0 sm:ml-4">
                                        <a href="{{ route('quizzes.attempt', ['quiz' => $attempt->quiz_id, 'locale' => app()->getLocale()]) }}"
                                            class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                            {{ __('dashboard.quizzes.continue') }}
                                        </a>
                                    </div>
                                </div>
                                <div class="mt-3">
                                    @php
                                        $progressPercentage =
                                            $attempt->total_questions > 0
                                                ? round(($attempt->current_question / $attempt->total_questions) * 100)
                                                : 0;
                                    @endphp
                                    <div class="flex justify-between text-sm mb-1">
                                        <span>{{ __('dashboard.quizzes.progress') }}</span>
                                        <span>{{ $progressPercentage }}%</span>
                                    </div>
                                    <div class="w-full bg-gray-200 rounded-full h-2">
                                        <div class="bg-blue-600 h-2 rounded-full"
                                            style="width: {{ $progressPercentage }}%"></div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        @endif

        <!-- Completed Quizzes -->
        @if ($completedQuizzes->count() > 0)
            <div class="bg-white rounded-lg shadow overflow-hidden">
                <div class="p-4 sm:p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h2 class="text-lg font-medium text-gray-900">{{ __('dashboard.quizzes.recently_completed') }}
                        </h2>
                        <a href="{{ route('dashboard.quizzes.index', ['locale' => app()->getLocale()]) }}"
                            class="text-sm font-medium text-blue-600 hover:text-blue-500">{{ __('dashboard.quizzes.view_all') }}</a>
                    </div>

                    <div class="space-y-4">
                        @foreach ($completedQuizzes->take(5) as $attempt)
                            @php
                                $quiz = $attempt->quiz;
                                $quizTitle = is_array($quiz->title)
                                    ? $quiz->title[app()->getLocale()] ?? ($quiz->title['en'] ?? 'Untitled Quiz')
                                    : $quiz->title;
                                $score = $attempt->score ?? 0;
                                $totalMarks = $quiz->questions->sum('marks');
                                $percentage = $totalMarks > 0 ? round(($score / $totalMarks) * 100) : 0;
                            @endphp

                            <div
                                class="flex justify-between items-center p-4 border rounded-lg hover:shadow-md transition-shadow">
                                <div>
                                    <h4 class="font-semibold text-gray-900">
                                        {{ $quizTitle }}
                                    </h4>
                                    <p class="text-sm text-gray-600">
                                        {{ __('dashboard.quizzes.score') }}: {{ $score }}/{{ $totalMarks }}
                                        ({{ $percentage }}%)
                                    </p>
                                </div>
                                <span
                                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                    {{ __('dashboard.quizzes.completed') }}
                                </span>
                            </div>
                        @endforeach

                        @if ($completedQuizzes->count() > 5)
                            <div class="text-center mt-4">
                                <a href="{{ route('quizzes.index', ['locale' => app()->getLocale()]) }}"
                                    class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
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
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1"
                                d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                        </svg>
                        <h3 class="mt-2 text-sm font-medium text-gray-900">
                            {{ __('dashboard.quizzes.no_completed_quizzes') }}</h3>
                        <p class="mt-1 text-sm text-gray-500">
                            {{ __('dashboard.quizzes.complete_to_see_results') }}
                        </p>
                        <div class="mt-6">
                            <a href="{{ route('dashboard.quizzes.index', ['locale' => app()->getLocale()]) }}"
                                class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                {{ __('dashboard.quizzes.browse_quizzes') }}
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>
@endsection
