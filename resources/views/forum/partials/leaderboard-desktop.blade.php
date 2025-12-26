<!-- Desktop Weekly Leaderboard -->
<div class="space-y-6">
    <!-- Weekly Leaderboard Header -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-xl font-semibold text-gray-900 dark:text-white flex items-center space-x-2">
                <svg class="w-6 h-6 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                </svg>
                <span>{{ __('forum.weekly_leaderboard') }}</span>
            </h3>
            @auth
                @if($userPoints)
                    <div class="text-right">
                        <div class="text-sm font-medium text-gray-900 dark:text-white">{{ __('forum.your_weekly_rank') }}: #{{ $userRank }}</div>
                        <div class="text-xs text-gray-500 dark:text-gray-400">{{ $userPoints['weekly'] }} {{ __('forum.points') }}</div>
                    </div>
                @endif
            @endauth
        </div>
        
        <div class="text-center">
            <p class="text-sm text-gray-600 dark:text-gray-400">{{ __('forum.leaderboard_resets_weekly') }}</p>
        </div>
    </div>

    <!-- Top 3 Podium -->
    @if(count($leaderboard) >= 3)
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
            <div class="flex items-end justify-center space-x-4">
                <!-- 2nd Place -->
                <div class="flex flex-col items-center">
                    <div class="relative">
                        <div class="w-16 h-16 rounded-full bg-gradient-to-br from-gray-400 to-gray-500 flex items-center justify-center text-white font-bold text-lg shadow-lg">
                            {{ substr($leaderboard[1]['user']['first_name'], 0, 1) }}{{ substr($leaderboard[1]['user']['last_name'], 0, 1) }}
                        </div>
                        <div class="absolute -top-2 -right-2 w-8 h-8 bg-gray-100 dark:bg-gray-700 rounded-full flex items-center justify-center">
                            <svg class="w-5 h-5 text-gray-600 dark:text-gray-300" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M10.394 2.08a1 1 0 00-.788 0l-7 3a1 1 0 000 1.84L5.25 8.051a.999.999 0 01.356-.257l4-1.714a1 1 0 11.788 1.838L7.667 9.088l1.94.831a1 1 0 00.787 0l7-3a1 1 0 000-1.838l-7-3zM3.31 9.397L5 10.12v4.102a8.969 8.969 0 00-1.05-.174 1 1 0 01-.89-.89 11.115 11.115 0 01.25-3.762zM9.3 16.573A9.026 9.026 0 007 14.935v-3.957l1.818.78a3 3 0 002.364 0l5.508-2.361a11.026 11.026 0 01.25 3.762 1 1 0 01-.89.89 8.968 8.968 0 00-5.35 2.524 1 1 0 01-1.4 0zM6 18a1 1 0 001-1v-2.065a8.935 8.935 0 00-2-.712V17a1 1 0 001 1z"/>
                            </svg>
                        </div>
                    </div>
                    <div class="mt-3 text-center">
                        <p class="text-sm font-medium text-gray-900 dark:text-white truncate max-w-[100px]">
                            {{ $leaderboard[1]['user']['first_name'] }} {{ $leaderboard[1]['user']['last_name'] }}
                        </p>
                        <p class="text-lg font-bold text-gray-600 dark:text-gray-300">{{ $leaderboard[1]['points'] }}</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400">{{ __('forum.points') }}</p>
                    </div>
                    <div class="mt-2 w-20 h-16 bg-gray-100 dark:bg-gray-700 rounded-t-lg flex items-center justify-center">
                        <span class="text-lg font-bold text-gray-600 dark:text-gray-300">2</span>
                    </div>
                </div>

                <!-- 1st Place -->
                <div class="flex flex-col items-center -mt-4">
                    <div class="relative">
                        <div class="w-20 h-20 rounded-full bg-gradient-to-br from-yellow-400 to-yellow-600 flex items-center justify-center text-white font-bold text-xl shadow-xl ring-4 ring-yellow-200 dark:ring-yellow-800">
                            {{ substr($leaderboard[0]['user']['first_name'], 0, 1) }}{{ substr($leaderboard[0]['user']['last_name'], 0, 1) }}
                        </div>
                        <div class="absolute -top-2 -right-2 w-10 h-10 bg-yellow-100 dark:bg-yellow-900/30 rounded-full flex items-center justify-center">
                            <svg class="w-6 h-6 text-yellow-600 dark:text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M10.394 2.08a1 1 0 00-.788 0l-7 3a1 1 0 000 1.84L5.25 8.051a.999.999 0 01.356-.257l4-1.714a1 1 0 11.788 1.838L7.667 9.088l1.94.831a1 1 0 00.787 0l7-3a1 1 0 000-1.838l-7-3zM3.31 9.397L5 10.12v4.102a8.969 8.969 0 00-1.05-.174 1 1 0 01-.89-.89 11.115 11.115 0 01.25-3.762zM9.3 16.573A9.026 9.026 0 007 14.935v-3.957l1.818.78a3 3 0 002.364 0l5.508-2.361a11.026 11.026 0 01.25 3.762 1 1 0 01-.89.89 8.968 8.968 0 00-5.35 2.524 1 1 0 01-1.4 0zM6 18a1 1 0 001-1v-2.065a8.935 8.935 0 00-2-.712V17a1 1 0 001 1z"/>
                            </svg>
                        </div>
                    </div>
                    <div class="mt-3 text-center">
                        <p class="text-sm font-medium text-gray-900 dark:text-white truncate max-w-[120px]">
                            {{ $leaderboard[0]['user']['first_name'] }} {{ $leaderboard[0]['user']['last_name'] }}
                        </p>
                        <p class="text-xl font-bold text-yellow-600 dark:text-yellow-400">{{ $leaderboard[0]['points'] }}</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400">{{ __('forum.points') }}</p>
                    </div>
                    <div class="mt-2 w-20 h-20 bg-yellow-100 dark:bg-yellow-900/30 rounded-t-lg flex items-center justify-center">
                        <span class="text-xl font-bold text-yellow-600 dark:text-yellow-400">1</span>
                    </div>
                </div>

                <!-- 3rd Place -->
                <div class="flex flex-col items-center">
                    <div class="relative">
                        <div class="w-16 h-16 rounded-full bg-gradient-to-br from-orange-400 to-orange-600 flex items-center justify-center text-white font-bold text-lg shadow-lg">
                            {{ substr($leaderboard[2]['user']['first_name'], 0, 1) }}{{ substr($leaderboard[2]['user']['last_name'], 0, 1) }}
                        </div>
                        <div class="absolute -top-2 -right-2 w-8 h-8 bg-orange-100 dark:bg-orange-900/30 rounded-full flex items-center justify-center">
                            <svg class="w-5 h-5 text-orange-600 dark:text-orange-400" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M10.394 2.08a1 1 0 00-.788 0l-7 3a1 1 0 000 1.84L5.25 8.051a.999.999 0 01.356-.257l4-1.714a1 1 0 11.788 1.838L7.667 9.088l1.94.831a1 1 0 00.787 0l7-3a1 1 0 000-1.838l-7-3zM3.31 9.397L5 10.12v4.102a8.969 8.969 0 00-1.05-.174 1 1 0 01-.89-.89 11.115 11.115 0 01.25-3.762zM9.3 16.573A9.026 9.026 0 007 14.935v-3.957l1.818.78a3 3 0 002.364 0l5.508-2.361a11.026 11.026 0 01.25 3.762 1 1 0 01-.89.89 8.968 8.968 0 00-5.35 2.524 1 1 0 01-1.4 0zM6 18a1 1 0 001-1v-2.065a8.935 8.935 0 00-2-.712V17a1 1 0 001 1z"/>
                            </svg>
                        </div>
                    </div>
                    <div class="mt-3 text-center">
                        <p class="text-sm font-medium text-gray-900 dark:text-white truncate max-w-[100px]">
                            {{ $leaderboard[2]['user']['first_name'] }} {{ $leaderboard[2]['user']['last_name'] }}
                        </p>
                        <p class="text-lg font-bold text-orange-600 dark:text-orange-400">{{ $leaderboard[2]['points'] }}</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400">{{ __('forum.points') }}</p>
                    </div>
                    <div class="mt-2 w-20 h-14 bg-orange-100 dark:bg-orange-900/30 rounded-t-lg flex items-center justify-center">
                        <span class="text-lg font-bold text-orange-600 dark:text-orange-400">3</span>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Rest of Leaderboard -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
        @forelse($leaderboard as $index => $entry)
            @if($index >= 3)
                <div class="p-4 border-b border-gray-100 dark:border-gray-700 last:border-b-0 hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors duration-150
                             {{ $entry['user']['id'] === Auth::id() ? 'bg-blue-50 dark:bg-blue-900/20' : '' }}">
                    <div class="flex items-center space-x-4">
                        <!-- Rank -->
                        <div class="flex-shrink-0 w-8 h-8 rounded-full bg-gray-100 dark:bg-gray-700 flex items-center justify-center text-sm font-bold text-gray-600 dark:text-gray-300">
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
                        </div>
                        
                        <!-- Points -->
                        <div class="flex-shrink-0 text-right">
                            <p class="text-sm font-bold text-gray-900 dark:text-white">{{ $entry['points'] }}</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">{{ __('forum.points') }}</p>
                        </div>
                    </div>
                </div>
            @endif
        @empty
            <div class="p-8 text-center">
                <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-gray-100 dark:bg-gray-800 mb-4">
                    <svg class="h-8 w-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                    </svg>
                </div>
                <p class="text-gray-500 dark:text-gray-400">{{ __('forum.no_leaderboard_data') }}</p>
            </div>
        @endforelse
    </div>
</div>
