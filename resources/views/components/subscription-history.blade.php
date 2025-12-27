{{-- Subscription History Component --}}
<div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
    <!-- Header -->
    <div class="bg-gray-50 dark:bg-gray-900/50 px-8 py-6 border-b border-gray-200 dark:border-gray-700">
        <div class="flex items-center justify-between">
            <div class="flex items-center space-x-4">
                <div class="p-3 bg-blue-100 dark:bg-blue-900/30 rounded-xl">
                    <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                    </svg>
                </div>
                <div>
                    <h2 class="text-2xl font-bold text-gray-900 dark:text-white">{{ __('subscription.history') }}</h2>
                    <p class="text-gray-600 dark:text-gray-400 text-sm">{{ __('subscription.history_subtitle', ['count' => $userSubscriptions->count()]) }}</p>
                </div>
            </div>
            @if ($userSubscriptions->count() > 0)
                <button onclick="toggleHistory()" class="flex items-center space-x-2 px-4 py-2 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700 text-gray-700 dark:text-gray-300 font-medium rounded-lg transition-all duration-200 border border-gray-200 dark:border-gray-600">
                    <span id="historyToggleText">{{ __('subscription.hide_history') }}</span>
                    <svg id="historyToggleIcon" class="w-4 h-4 transform transition-transform duration-200" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                    </svg>
                </button>
            @endif
        </div>
    </div>
    
    <!-- Content -->
    <div id="subscriptionHistory" class="p-8 space-y-6">
        @if ($userSubscriptions->count() > 0)
            @foreach ($userSubscriptions->sortByDesc('created_at') as $subscription)
                @php
                    $plan = $subscription->plan;
                    $statusConfig = match($subscription->status) {
                        'ACTIVE' => ['color' => 'emerald', 'bg' => 'emerald', 'text' => 'emerald', 'icon' => 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z'],
                        'EXPIRED' => ['color' => 'red', 'bg' => 'red', 'text' => 'red', 'icon' => 'M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z'],
                        'CANCELLED' => ['color' => 'gray', 'bg' => 'gray', 'text' => 'gray', 'icon' => 'M6 18L18 6M6 6l12 12'],
                        'PENDING' => ['color' => 'amber', 'bg' => 'amber', 'text' => 'amber', 'icon' => 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z'],
                        default => ['color' => 'gray', 'bg' => 'gray', 'text' => 'gray', 'icon' => 'M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z']
                    };
                    
                    $statusText = match($subscription->status) {
                        'ACTIVE' => __('subscription.active'),
                        'EXPIRED' => __('subscription.expired'),
                        'CANCELLED' => __('subscription.cancelled'), 
                        'PENDING' => __('subscription.pending'),
                        default => __('subscription.unknown')
                    };
                    
                    // Handle plan name localization properly
                    $planName = 'Plan';
                    if (isset($plan->name)) {
                        $nameData = $plan->name;
                        if (is_string($nameData)) {
                            $nameData = json_decode($nameData, true) ?: [];
                        }
                        if (is_array($nameData)) {
                            $planName = $nameData[app()->getLocale()] ?? $nameData['en'] ?? $nameData['rw'] ?? 'Plan';
                        } else {
                            $planName = $nameData;
                        }
                    }
                @endphp
                
                <!-- Subscription Card -->
                <div class="group relative bg-gray-50 dark:bg-gray-900/50 rounded-xl border border-gray-200 dark:border-gray-700 p-6 hover:shadow-lg hover:border-blue-300 dark:hover:border-blue-600 transition-all duration-300">
                    <!-- Status Badge -->
                    <div class="absolute top-4 right-4">
                        <div class="flex items-center space-x-2 px-3 py-1.5 bg-{{ $statusConfig['bg'] }}-100 dark:bg-{{ $statusConfig['bg'] }}-900/30 rounded-full border border-{{ $statusConfig['bg'] }}-200 dark:border-{{ $statusConfig['bg'] }}-700">
                            <div class="w-2 h-2 bg-{{ $statusConfig['color'] }}-500 rounded-full animate-pulse"></div>
                            <span class="text-xs font-semibold text-{{ $statusConfig['text'] }}-800 dark:text-{{ $statusConfig['text'] }}-200">{{ $statusText }}</span>
                        </div>
                    </div>
                    
                    <!-- Main Content -->
                    <div class="pr-32">
                        <!-- Plan Name -->
                        <div class="flex items-center space-x-3 mb-4">
                            <div class="p-3 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-xl shadow-lg">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v13m0-13V6a2 2 0 112 2h-2zm0 0V5.5A2.5 2.5 0 109.5 8H12zm-7 4h14M5 12a2 2 0 110-4h14a2 2 0 110 4M5 12v7a2 2 0 002 2h10a2 2 0 002-2v-7" />
                                </svg>
                            </div>
                            <div>
                                @if ($subscription->status === 'ACTIVE')
                                    <a href="{{ route('dashboard.quizzes.index', ['locale' => app()->getLocale()]) }}" 
                                       class="text-xl font-bold text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300 transition-colors duration-200">
                                        {{ $planName }}
                                        <svg class="w-4 h-4 inline ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                                        </svg>
                                    </a>
                                @else
                                    <h3 class="text-xl font-bold text-gray-900 dark:text-white">{{ $planName }}</h3>
                                @endif
                                <p class="text-sm text-gray-600 dark:text-gray-400">{{ __('subscription.plan_type') }}</p>
                            </div>
                        </div>
                        
                        <!-- Details Grid -->
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                            <!-- Start Date -->
                            <div class="flex items-center space-x-3 p-3 bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700">
                                <div class="p-2 bg-blue-100 dark:bg-blue-900/30 rounded-lg">
                                    <svg class="w-4 h-4 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wide">{{ __('subscription.started') }}</p>
                                    <p class="text-sm font-semibold text-gray-900 dark:text-white">{{ $subscription->starts_at->format('m/d/Y') }}</p>
                                </div>
                            </div>
                            
                            <!-- End Date -->
                            @if ($subscription->ends_at)
                                <div class="flex items-center space-x-3 p-3 bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700">
                                    <div class="p-2 bg-{{ $statusConfig['bg'] }}-100 dark:bg-{{ $statusConfig['bg'] }}-900/30 rounded-lg">
                                        <svg class="w-4 h-4 text-{{ $statusConfig['text'] }}-600 dark:text-{{ $statusConfig['text'] }}-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                    </div>
                                    <div>
                                        <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wide">{{ __('subscription.expires') }}</p>
                                        <p class="text-sm font-semibold text-gray-900 dark:text-white">{{ $subscription->ends_at->format('m/d/Y') }}</p>
                                        @if ($subscription->status === 'ACTIVE' && $subscription->ends_at->isFuture())
                                            <p class="text-xs text-{{ $statusConfig['text'] }}-600 dark:text-{{ $statusConfig['text'] }}-400">
                                                {{ __('subscription.expires_in', ['time' => $subscription->ends_at->diffForHumans(now(), true)]) }}
                                            </p>
                                        @elseif ($subscription->status === 'EXPIRED' || ($subscription->ends_at && $subscription->ends_at->isPast()))
                                            <p class="text-xs text-red-600 dark:text-red-400">
                                                {{ __('subscription.expired_since', ['time' => $subscription->ends_at->diffForHumans(now(), true)]) }}
                                            </p>
                                        @endif
                                    </div>
                                </div>
                            @endif
                            
                            <!-- Amount -->
                            <div class="flex items-center space-x-3 p-3 bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700">
                                <div class="p-2 bg-green-100 dark:bg-green-900/30 rounded-lg">
                                    <svg class="w-4 h-4 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1" />
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wide">{{ __('subscription.amount') }}</p>
                                    <p class="text-sm font-semibold text-gray-900 dark:text-white">{{ number_format($subscription->amount, 0) }} RWF</p>
                                </div>
                            </div>
                            
                            <!-- Payment Method -->
                            @if ($subscription->payment_method)
                                <div class="flex items-center space-x-3 p-3 bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700">
                                    <div class="p-2 bg-purple-100 dark:bg-purple-900/30 rounded-lg">
                                        <svg class="w-4 h-4 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                                        </svg>
                                    </div>
                                    <div>
                                        <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wide">{{ __('subscription.payment_method') }}</p>
                                        <p class="text-sm font-semibold text-gray-900 dark:text-white">{{ str_replace('_', ' ', ucfirst($subscription->payment_method)) }}</p>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                    
                    <!-- Action Button -->
                    <div class="absolute top-4 right-4 flex flex-col items-end space-y-2">
                        @if ($subscription->status === 'EXPIRED' || ($subscription->status === 'ACTIVE' && $subscription->ends_at && $subscription->ends_at->isPast()))
                            <a href="{{ route('plans', ['locale' => app()->getLocale()]) }}" 
                               class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold rounded-lg transition-colors duration-200 shadow-sm">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                </svg>
                                {{ __('subscription.renew_now') }}
                            </a>
                        @elseif ($subscription->status === 'ACTIVE')
                            <div class="inline-flex items-center px-4 py-2 bg-emerald-600 text-white text-sm font-semibold rounded-lg shadow-sm">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                {{ __('subscription.current') }}
                            </div>
                        @endif
                    </div>
                </div>
            @endforeach
        @else
            <!-- Empty State -->
            <div class="text-center py-16">
                <div class="mx-auto flex items-center justify-center h-20 w-20 rounded-2xl bg-gradient-to-br from-gray-100 to-gray-200 dark:from-gray-700 dark:to-gray-800 mb-6">
                    <svg class="w-10 h-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                </div>
                <h3 class="text-2xl font-bold text-gray-900 dark:text-white mb-3">{{ __('subscription.no_history') }}</h3>
                <p class="text-gray-600 dark:text-gray-400 mb-8 max-w-md mx-auto">{{ __('subscription.no_history_message') }}</p>
                <a href="#pricing-plans" class="inline-flex items-center px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-lg transition-colors duration-200 shadow-sm">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4" />
                    </svg>
                    {{ __('subscription.view_plans') }}
                </a>
            </div>
        @endif
    </div>
</div>

<script>
function toggleHistory() {
    const history = document.getElementById('subscriptionHistory');
    const toggleText = document.getElementById('historyToggleText');
    const toggleIcon = document.getElementById('historyToggleIcon');
    
    if (history.style.display === 'none') {
        history.style.display = 'block';
        toggleText.textContent = '{{ __('subscription.hide_history') }}';
        toggleIcon.style.transform = 'rotate(0deg)';
    } else {
        history.style.display = 'none';
        toggleText.textContent = '{{ __('subscription.show_history') }}';
        toggleIcon.style.transform = 'rotate(-90deg)';
    }
}
</script>
