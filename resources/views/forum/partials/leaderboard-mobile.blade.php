<!-- Mobile Weekly Leaderboard -->
<div class="space-y-3" x-data="leaderboardTabs()" x-init="init()">
    <!-- Weekly Leaderboard Header -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-4">
        <div class="flex items-center justify-center mb-3">
            <div class="flex items-center space-x-4">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white flex items-center space-x-2">
                    <svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                    </svg>
                    <span>{{ __('forum.weekly_leaderboard') }}</span>
                </h3>
            </div>
        </div>

        <!-- User Position and Points -->
        @auth
            @if ($userPoints)
                <div class="text-center">
                    <span class="text-sm font-medium text-gray-900 dark:text-white">{{ __('forum.your_weekly_rank') }}:
                        #{{ $userRank }}</span>
                    <span
                        class="text-xs text-gray-500 dark:text-gray-400 ml-2">{{ __('forum.points', ['points' => $userPoints['weekly']]) }}</span>
                </div>
            @endif
        @endauth
    </div>

    <!-- Top 3 Podium -->
    @if (count($leaderboard) >= 3)
        <div
            class="bg-gradient-to-br from-white to-gray-50 dark:from-gray-800 dark:to-gray-900 rounded-2xl shadow-lg border border-gray-200 dark:border-gray-700 py-3 p-4 backdrop-blur-sm">
            <div class="flex items-end justify-center space-x-2">
                <!-- 2nd Place -->
                <div
                    class="flex flex-col items-center group hover:transform hover:scale-105 transition-all duration-300">
                    <div class="relative">
                        <div
                            class="w-12 h-12 rounded-full bg-gradient-to-br from-gray-400 to-gray-500 flex items-center justify-center text-white font-bold text-sm shadow-lg group-hover:shadow-xl transition-shadow">
                            {{ substr($leaderboard[1]['user']['first_name'], 0, 1) }}{{ substr($leaderboard[1]['user']['last_name'], 0, 1) }}
                        </div>
                        <div
                            class="absolute -top-1 -right-1 w-5 h-5 bg-gray-100 dark:bg-gray-700 rounded-full flex items-center justify-center shadow-md">
                            <svg class="w-3 h-3 text-gray-600 dark:text-gray-300" fill="currentColor"
                                viewBox="0 0 20 20">
                                <path
                                    d="M10.394 2.08a1 1 0 00-.788 0l-7 3a1 1 0 000 1.84L5.25 8.051a.999.999 0 01.356-.257l4-1.714a1 1 0 11.788 1.838L7.667 9.088l1.94.831a1 1 0 00.787 0l7-3a1 1 0 000-1.838l-7-3zM3.31 9.397L5 10.12v4.102a8.969 8.969 0 00-1.05-.174 1 1 0 01-.89-.89 11.115 11.115 0 01.25-3.762zM9.3 16.573A9.026 9.026 0 007 14.935v-3.957l1.818.78a3 3 0 002.364 0l5.508-2.361a11.026 11.026 0 01.25 3.762 1 1 0 01-.89.89 8.968 8.968 0 00-5.35 2.524 1 1 0 01-1.4 0zM6 18a1 1 0 001-1v-2.065a8.935 8.935 0 00-2-.712V17a1 1 0 001 1z" />
                            </svg>
                        </div>
                    </div>
                    <div class="mt-2 text-center">
                        <p
                            class="text-xs font-medium text-gray-900 dark:text-white truncate max-w-[70px] group-hover:text-gray-700 dark:group-hover:text-gray-200 transition-colors">
                            {{ $leaderboard[1]['user']['first_name'] }} {{ $leaderboard[1]['user']['last_name'] }}
                        </p>
                        <p class="text-sm font-bold text-gray-600 dark:text-gray-300">{{ $leaderboard[1]['points'] }}
                        </p>
                        <p class="text-xs text-gray-500 dark:text-gray-400">{{ __('forum.points') }}</p>
                    </div>
                    <div
                        class="mt-1 w-12 h-10 bg-gradient-to-b from-gray-100 to-gray-200 dark:from-gray-700 dark:to-gray-600 rounded-t-lg flex items-center justify-center shadow-md">
                        <span class="text-sm font-bold text-gray-600 dark:text-gray-300">2</span>
                    </div>
                </div>

                <!-- 1st Place -->
                <div
                    class="flex flex-col items-center -mt-1 group hover:transform hover:scale-105 transition-all duration-300">
                    <div class="relative">
                        <div
                            class="w-14 h-14 rounded-full bg-gradient-to-br from-yellow-400 to-orange-500 flex items-center justify-center text-white font-bold text-lg shadow-xl ring-2 ring-yellow-200 dark:ring-yellow-800 group-hover:shadow-2xl transition-shadow">
                            {{ substr($leaderboard[0]['user']['first_name'], 0, 1) }}{{ substr($leaderboard[0]['user']['last_name'], 0, 1) }}
                        </div>
                        <div
                            class="absolute -top-1 -right-1 w-6 h-6 bg-yellow-100 dark:bg-yellow-900/30 rounded-full flex items-center justify-center shadow-md">
                            <svg class="w-3 h-3 text-yellow-600 dark:text-yellow-400" fill="currentColor"
                                viewBox="0 0 20 20">
                                <path
                                    d="M10.394 2.08a1 1 0 00-.788 0l-7 3a1 1 0 000 1.84L5.25 8.051a.999.999 0 01.356-.257l4-1.714a1 1 0 11.788 1.838L7.667 9.088l1.94.831a1 1 0 00.787 0l7-3a1 1 0 000-1.838l-7-3zM3.31 9.397L5 10.12v4.102a8.969 8.969 0 00-1.05-.174 1 1 0 01-.89-.89 11.115 11.115 0 01.25-3.762zM9.3 16.573A9.026 9.026 0 007 14.935v-3.957l1.818.78a3 3 0 002.364 0l5.508-2.361a11.026 11.026 0 01.25 3.762 1 1 0 01-.89.89 8.968 8.968 0 00-5.35 2.524 1 1 0 01-1.4 0zM6 18a1 1 0 001-1v-2.065a8.935 8.935 0 00-2-.712V17a1 1 0 001 1z" />
                            </svg>
                        </div>
                    </div>
                    <div class="mt-2 text-center">
                        <p
                            class="text-xs font-medium text-gray-900 dark:text-white truncate max-w-[80px] group-hover:text-gray-700 dark:group-hover:text-gray-200 transition-colors">
                            {{ $leaderboard[0]['user']['first_name'] }} {{ $leaderboard[0]['user']['last_name'] }}
                        </p>
                        <p class="text-lg font-bold text-yellow-600 dark:text-yellow-400">
                            {{ $leaderboard[0]['points'] }}</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400">{{ __('forum.points') }}</p>
                    </div>
                    <div
                        class="mt-1 w-14 h-14 bg-gradient-to-b from-yellow-100 to-yellow-200 dark:from-yellow-900/30 dark:to-yellow-800/40 rounded-t-lg flex items-center justify-center shadow-lg">
                        <span class="text-lg font-bold text-yellow-600 dark:text-yellow-400">1</span>
                    </div>
                </div>

                <!-- 3rd Place -->
                <div
                    class="flex flex-col items-center group hover:transform hover:scale-105 transition-all duration-300">
                    <div class="relative">
                        <div
                            class="w-12 h-12 rounded-full bg-gradient-to-br from-orange-400 to-orange-600 flex items-center justify-center text-white font-bold text-sm shadow-lg group-hover:shadow-xl transition-shadow">
                            {{ substr($leaderboard[2]['user']['first_name'], 0, 1) }}{{ substr($leaderboard[2]['user']['last_name'], 0, 1) }}
                        </div>
                        <div
                            class="absolute -top-1 -right-1 w-5 h-5 bg-orange-100 dark:bg-orange-900/30 rounded-full flex items-center justify-center shadow-md">
                            <svg class="w-3 h-3 text-orange-600 dark:text-orange-400" fill="currentColor"
                                viewBox="0 0 20 20">
                                <path
                                    d="M10.394 2.08a1 1 0 00-.788 0l-7 3a1 1 0 000 1.84L5.25 8.051a.999.999 0 01.356-.257l4-1.714a1 1 0 11.788 1.838L7.667 9.088l1.94.831a1 1 0 00.787 0l7-3a1 1 0 000-1.838l-7-3zM3.31 9.397L5 10.12v4.102a8.969 8.969 0 00-1.05-.174 1 1 0 01-.89-.89 11.115 11.115 0 01.25-3.762zM9.3 16.573A9.026 9.026 0 007 14.935v-3.957l1.818.78a3 3 0 002.364 0l5.508-2.361a11.026 11.026 0 01.25 3.762 1 1 0 01-.89.89 8.968 8.968 0 00-5.35 2.524 1 1 0 01-1.4 0zM6 18a1 1 0 001-1v-2.065a8.935 8.935 0 00-2-.712V17a1 1 0 001 1z" />
                            </svg>
                        </div>
                    </div>
                    <div class="mt-2 text-center">
                        <p
                            class="text-xs font-medium text-gray-900 dark:text-white truncate max-w-[70px] group-hover:text-gray-700 dark:group-hover:text-gray-200 transition-colors">
                            {{ $leaderboard[2]['user']['first_name'] }} {{ $leaderboard[2]['user']['last_name'] }}
                        </p>
                        <p class="text-sm font-bold text-orange-600 dark:text-orange-400">
                            {{ $leaderboard[2]['points'] }}</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400">{{ __('forum.points') }}</p>
                    </div>
                    <div
                        class="mt-1 w-12 h-10 bg-gradient-to-b from-orange-100 to-orange-200 dark:from-orange-900/30 dark:to-orange-800/40 rounded-t-lg flex items-center justify-center shadow-md">
                        <span class="text-sm font-bold text-orange-600 dark:text-orange-400">3</span>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Rest of Leaderboard -->
    <div
        class="bg-gradient-to-br from-white to-gray-50 dark:from-gray-800 dark:to-gray-900 rounded-2xl shadow-lg border border-gray-200 dark:border-gray-700 overflow-hidden backdrop-blur-sm">
        @forelse($leaderboard as $index => $entry)
            @if ($index >= 3)
                <div
                    class="group p-3 border-b border-gray-100 dark:border-gray-700 last:border-b-0 hover:bg-gradient-to-r hover:from-blue-50 hover:to-indigo-50 dark:hover:from-blue-900/20 dark:hover:to-indigo-900/20 transition-all duration-200 cursor-pointer
                     {{ $entry['user']['id'] === Auth::id() ? 'bg-gradient-to-r from-blue-50 to-indigo-50 dark:from-blue-900/20 dark:to-indigo-900/20' : '' }}">
                    <div class="flex items-center space-x-3">
                        <!-- User Avatar with hover effect -->
                        <div class="flex-shrink-0 relative group">
                            <div
                                class="w-10 h-10 rounded-full bg-gradient-to-br from-blue-500 to-blue-600 flex items-center justify-center text-white font-bold text-sm shadow-md group-hover:shadow-lg transition-all duration-200 group-hover:scale-105">
                                {{ substr($entry['user']['first_name'], 0, 1) }}{{ substr($entry['user']['last_name'], 0, 1) }}
                            </div>
                            @if ($entry['user']['id'] === Auth::id())
                                <span
                                    class="absolute -bottom-1 -right-1 w-3 h-3 bg-blue-500 rounded-full flex items-center justify-center text-white text-xs font-bold shadow-md">YOU</span>
                            @endif
                        </div>

                        <!-- User Info -->
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center space-x-2 mb-1">
                                <p
                                    class="text-sm font-medium text-gray-900 dark:text-white truncate group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors">
                                    {{ $entry['user']['first_name'] }} {{ $entry['user']['last_name'] }}
                                </p>
                                @if ($entry['user']['id'] === Auth::id())
                                    <span
                                        class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-300">
                                        {{ __('forum.you') }}
                                    </span>
                                @elseif($index + 1 <= 5)
                                    <span
                                        class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300">
                                        Top 5
                                    </span>
                                @elseif($index + 1 <= 10)
                                    <span
                                        class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-300">
                                        Top 10
                                    </span>
                                @endif
                            </div>
                            <p class="text-xs text-gray-500 dark:text-gray-400">
                                {{ __('forum.joined') }}
                                {{ \Carbon\Carbon::parse($entry['user']['createdAt'])->diffForHumans() }}
                            </p>
                        </div>

                        <!-- Points with enhanced styling -->
                        <div class="flex-shrink-0 text-right">
                            <p class="text-sm font-bold bg-clip-text text-transparent"
                                style="background-image: linear-gradient(to right, 
                                   @if ($index + 1 <= 5) #059669, #047857
                                   @elseif($index + 1 <= 10) 
                                       #2563eb, #1d4ed8
                                   @else 
                                       #4b5563, #374151 @endif)">
                                {{ $entry['points'] }}
                            </p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">{{ __('forum.points') }}</p>
                        </div>
                    </div>
                </div>
            @endif
        @empty
            <div class="p-6 text-center">
                <div
                    class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-gray-100 dark:bg-gray-800 mb-3">
                    <svg class="h-6 w-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                    </svg>
                </div>
                <p class="text-gray-500 dark:text-gray-400 text-sm">{{ __('forum.no_leaderboard_data') }}</p>
            </div>
        @endforelse
    </div>
</div>
