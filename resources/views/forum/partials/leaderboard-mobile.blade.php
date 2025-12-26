<!-- Mobile Weekly Leaderboard -->
<div class="space-y-3">
    <!-- Weekly Leaderboard Header -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-2">
        <div class="flex items-center justify-between mb-2">
            <h3 class="text-base font-semibold text-gray-900 dark:text-white flex items-center space-x-1">
                <svg class="w-4 h-4 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                </svg>
                <span>{{ __('forum.weekly_leaderboard') }}</span>
            </h3>
            @auth
                @if($userPoints)
                    <div class="text-xs text-gray-500 dark:text-gray-400 text-right">
                        <div class="font-medium text-gray-900 dark:text-white">#{{ $userRank }}</div>
                        <div>{{ $userPoints['weekly'] }} {{ __('forum.points') }}</div>
                    </div>
                @endif
            @endauth
        </div>
        
        <div class="text-center">
            <p class="text-xs text-gray-600 dark:text-gray-400">{{ __('forum.leaderboard_resets_weekly') }}</p>
        </div>
    </div>

    <!-- Leaderboard List -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
        @forelse($leaderboard as $index => $entry)
            <div class="p-3 border-b border-gray-100 dark:border-gray-700 last:border-b-0
                         {{ $entry['user']['id'] === Auth::id() ? 'bg-blue-50 dark:bg-blue-900/20' : '' }}">
                <div class="flex items-center space-x-3">
                    <!-- Rank Badge -->
                    <div class="flex-shrink-0 w-8 h-8 rounded-full flex items-center justify-center font-bold text-sm
                                @if($index === 0)
                                    bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-400
                                @elseif($index === 1)
                                    bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300
                                @elseif($index === 2)
                                    bg-orange-100 text-orange-800 dark:bg-orange-900/30 dark:text-orange-400
                                @else
                                    bg-gray-50 text-gray-600 dark:bg-gray-800 dark:text-gray-400
                                @endif">
                        {{ $index + 1 }}
                    </div>
                    
                    <!-- User Avatar -->
                    <div class="flex-shrink-0 w-10 h-10 rounded-full bg-gradient-to-br from-blue-500 to-blue-600 flex items-center justify-center text-white font-bold">
                        {{ substr($entry['user']['first_name'], 0, 1) }}{{ substr($entry['user']['last_name'], 0, 1) }}
                    </div>
                    
                    <!-- User Info -->
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center space-x-2">
                            <p class="text-sm font-medium text-gray-900 dark:text-white truncate">
                                {{ $entry['user']['first_name'] }} {{ $entry['user']['last_name'] }}
                            </p>
                            @if($entry['user']['id'] === Auth::id())
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-300">
                                    {{ __('forum.you') }}
                                </span>
                            @endif
                        </div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">
                            {{ $entry['points'] }} {{ __('forum.points') }}
                        </p>
                    </div>
                    
                    <!-- Trophy Icon for Top 3 -->
                    @if($index < 3)
                        <div class="flex-shrink-0">
                            @if($index === 0)
                                <svg class="w-6 h-6 text-yellow-500" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M10.394 2.08a1 1 0 00-.788 0l-7 3a1 1 0 000 1.84L5.25 8.051a.999.999 0 01.356-.257l4-1.714a1 1 0 11.788 1.838L7.667 9.088l1.94.831a1 1 0 00.787 0l7-3a1 1 0 000-1.838l-7-3zM3.31 9.397L5 10.12v4.102a8.969 8.969 0 00-1.05-.174 1 1 0 01-.89-.89 11.115 11.115 0 01.25-3.762zM9.3 16.573A9.026 9.026 0 007 14.935v-3.957l1.818.78a3 3 0 002.364 0l5.508-2.361a11.026 11.026 0 01.25 3.762 1 1 0 01-.89.89 8.968 8.968 0 00-5.35 2.524 1 1 0 01-1.4 0zM6 18a1 1 0 001-1v-2.065a8.935 8.935 0 00-2-.712V17a1 1 0 001 1z"/>
                                </svg>
                            @elseif($index === 1)
                                <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M10.394 2.08a1 1 0 00-.788 0l-7 3a1 1 0 000 1.84L5.25 8.051a.999.999 0 01.356-.257l4-1.714a1 1 0 11.788 1.838L7.667 9.088l1.94.831a1 1 0 00.787 0l7-3a1 1 0 000-1.838l-7-3zM3.31 9.397L5 10.12v4.102a8.969 8.969 0 00-1.05-.174 1 1 0 01-.89-.89 11.115 11.115 0 01.25-3.762zM9.3 16.573A9.026 9.026 0 007 14.935v-3.957l1.818.78a3 3 0 002.364 0l5.508-2.361a11.026 11.026 0 01.25 3.762 1 1 0 01-.89.89 8.968 8.968 0 00-5.35 2.524 1 1 0 01-1.4 0zM6 18a1 1 0 001-1v-2.065a8.935 8.935 0 00-2-.712V17a1 1 0 001 1z"/>
                                </svg>
                            @else
                                <svg class="w-6 h-6 text-orange-600" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M10.394 2.08a1 1 0 00-.788 0l-7 3a1 1 0 000 1.84L5.25 8.051a.999.999 0 01.356-.257l4-1.714a1 1 0 11.788 1.838L7.667 9.088l1.94.831a1 1 0 00.787 0l7-3a1 1 0 000-1.838l-7-3zM3.31 9.397L5 10.12v4.102a8.969 8.969 0 00-1.05-.174 1 1 0 01-.89-.89 11.115 11.115 0 01.25-3.762zM9.3 16.573A9.026 9.026 0 007 14.935v-3.957l1.818.78a3 3 0 002.364 0l5.508-2.361a11.026 11.026 0 01.25 3.762 1 1 0 01-.89.89 8.968 8.968 0 00-5.35 2.524 1 1 0 01-1.4 0zM6 18a1 1 0 001-1v-2.065a8.935 8.935 0 00-2-.712V17a1 1 0 001 1z"/>
                                </svg>
                            @endif
                        </div>
                    @endif
                </div>
            </div>
        @empty
            <div class="p-4 text-center">
                <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-gray-100 dark:bg-gray-800 mb-3">
                    <svg class="h-6 w-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                    </svg>
                </div>
                <p class="text-gray-500 dark:text-gray-400 text-sm">{{ __('forum.no_leaderboard_data') }}</p>
            </div>
        @endforelse
    </div>
</div>
