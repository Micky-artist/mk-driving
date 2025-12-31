<!-- Forum Introduction Section -->
<div class="py-4 sm:py-6 lg:py-8 bg-white dark:bg-gray-900">
    <div class="max-w-7xl mx-auto px-3 sm:px-4 lg:px-6">
        <!-- Section Header -->
        <x-section-header :title="__('forum.community')" :href="route('forum.index', app()->getLocale())" />

        <!-- Main Content Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 sm:gap-6 max-w-7xl mx-auto">
        
        <!-- Leaderboard Card -->
        @if(isset($forumData['leaderboard']) && count($forumData['leaderboard']) >= 3)
            <div class="bg-white dark:bg-gray-800/80 backdrop-blur-sm rounded-2xl shadow-lg hover:shadow-2xl transition-all duration-300 border border-gray-100 dark:border-gray-700/50 overflow-hidden">
                <!-- Leaderboard Header -->
                <div class="p-2 sm:p-6 border-b border-gray-100 dark:border-gray-700/50">
                    <div class="flex items-center space-x-3 mb-4">
                        <div class="w-12 h-12 rounded-full border-2 border-blue-500 bg-white dark:bg-gray-800 flex items-center justify-center">
                            <svg class="w-6 h-6 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-xl font-bold text-gray-900 dark:text-white">
                                {{ __('forum.weekly_leaderboard') }}
                            </h3>
                            <p class="text-sm text-gray-500 dark:text-gray-400">
                                {{ __('forum.top_performers') }}
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Top 3 Podium -->
                <div class="p-2 sm:p-6">
                    <div class="flex items-end justify-center space-x-2 md:space-x-4">
                        <!-- 2nd Place -->
                        <div class="flex flex-col items-center">
                            <div class="relative">
                                <div class="w-12 h-12 md:w-16 md:h-16 rounded-full bg-gradient-to-br from-gray-400 to-gray-500 flex items-center justify-center text-white font-bold text-sm md:text-lg shadow-lg">
                                    {{ $forumData['leaderboard'][1]['user']['initials'] }}
                                </div>
                                <div class="absolute -top-1 -right-1 md:-top-2 md:-right-2 w-6 h-6 md:w-8 md:h-8 bg-gray-100 dark:bg-gray-700 rounded-full flex items-center justify-center">
                                    <svg class="w-3 h-3 md:w-5 md:h-5 text-gray-600 dark:text-gray-300" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M10.394 2.08a1 1 0 00-.788 0l-7 3a1 1 0 000 1.84L5.25 8.051a.999.999 0 01.356-.257l4-1.714a1 1 0 11.788 1.838L7.667 9.088l1.94.831a1 1 0 00.787 0l7-3a1 1 0 000-1.838l-7-3zM3.31 9.397L5 10.12v4.102a8.969 8.969 0 00-1.05-.174 1 1 0 01-.89-.89 11.115 11.115 0 01.25-3.762zM9.3 16.573A9.026 9.026 0 007 14.935v-3.957l1.818.78a3 3 0 002.364 0l5.508-2.361a11.026 11.026 0 01.25 3.762 1 1 0 01-.89.89 8.968 8.968 0 00-5.35 2.524 1 1 0 01-1.4 0zM6 18a1 1 0 001-1v-2.065a8.935 8.935 0 00-2-.712V17a1 1 0 001 1z"/>
                                    </svg>
                                </div>
                            </div>
                            <div class="mt-2 md:mt-3 text-center">
                                <p class="text-xs md:text-sm font-medium text-gray-900 dark:text-white truncate max-w-[80px] md:max-w-[100px]">
                                    {{ $forumData['leaderboard'][1]['user']['firstName'] }} {{ $forumData['leaderboard'][1]['user']['lastName'] }}
                                </p>
                                <p class="text-sm md:text-lg font-bold text-gray-600 dark:text-gray-300">{{ $forumData['leaderboard'][1]['points'] }}</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400">{{ __('forum.points_label') }}</p>
                            </div>
                            <div class="mt-1 md:mt-2 w-16 h-12 md:w-20 md:h-16 bg-gray-100 dark:bg-gray-700 rounded-t-lg flex items-center justify-center">
                                <span class="text-sm md:text-lg font-bold text-gray-600 dark:text-gray-300">2</span>
                            </div>
                        </div>

                        <!-- 1st Place -->
                        <div class="flex flex-col items-center -mt-2 md:-mt-4">
                            <div class="relative">
                                <div class="w-16 h-16 md:w-20 md:h-20 rounded-full bg-gradient-to-br from-yellow-400 to-yellow-600 flex items-center justify-center text-white font-bold text-lg md:text-xl shadow-xl ring-4 ring-yellow-200 dark:ring-yellow-800">
                                    {{ $forumData['leaderboard'][0]['user']['initials'] }}
                                </div>
                                <div class="absolute -top-1 -right-1 md:-top-2 md:-right-2 w-8 h-8 md:w-10 md:h-10 bg-yellow-100 dark:bg-yellow-900/30 rounded-full flex items-center justify-center">
                                    <svg class="w-4 h-4 md:w-6 md:h-6 text-yellow-600 dark:text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M10.394 2.08a1 1 0 00-.788 0l-7 3a1 1 0 000 1.84L5.25 8.051a.999.999 0 01.356-.257l4-1.714a1 1 0 11.788 1.838L7.667 9.088l1.94.831a1 1 0 00.787 0l7-3a1 1 0 000-1.838l-7-3zM3.31 9.397L5 10.12v4.102a8.969 8.969 0 00-1.05-.174 1 1 0 01-.89-.89 11.115 11.115 0 01.25-3.762zM9.3 16.573A9.026 9.026 0 007 14.935v-3.957l1.818.78a3 3 0 002.364 0l5.508-2.361a11.026 11.026 0 01.25 3.762 1 1 0 01-.89.89 8.968 8.968 0 00-5.35 2.524 1 1 0 01-1.4 0zM6 18a1 1 0 001-1v-2.065a8.935 8.935 0 00-2-.712V17a1 1 0 001 1z"/>
                                    </svg>
                                </div>
                            </div>
                            <div class="mt-2 md:mt-3 text-center">
                                <p class="text-xs md:text-sm font-medium text-gray-900 dark:text-white truncate max-w-[100px] md:max-w-[120px]">
                                    {{ $forumData['leaderboard'][0]['user']['firstName'] }} {{ $forumData['leaderboard'][0]['user']['lastName'] }}
                                </p>
                                <p class="text-base md:text-xl font-bold text-yellow-600 dark:text-yellow-400">{{ $forumData['leaderboard'][0]['points'] }}</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400">{{ __('forum.points_label') }}</p>
                            </div>
                            <div class="mt-1 md:mt-2 w-16 h-16 md:w-20 md:h-20 bg-yellow-100 dark:bg-yellow-900/30 rounded-t-lg flex items-center justify-center">
                                <span class="text-base md:text-xl font-bold text-yellow-600 dark:text-yellow-400">1</span>
                            </div>
                        </div>

                        <!-- 3rd Place -->
                        <div class="flex flex-col items-center">
                            <div class="relative">
                                <div class="w-12 h-12 md:w-16 md:h-16 rounded-full bg-gradient-to-br from-orange-400 to-orange-600 flex items-center justify-center text-white font-bold text-sm md:text-lg shadow-lg">
                                    {{ $forumData['leaderboard'][2]['user']['initials'] }}
                                </div>
                                <div class="absolute -top-1 -right-1 md:-top-2 md:-right-2 w-6 h-6 md:w-8 md:h-8 bg-orange-100 dark:bg-orange-900/30 rounded-full flex items-center justify-center">
                                    <svg class="w-3 h-3 md:w-5 md:h-5 text-orange-600 dark:text-orange-400" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M10.394 2.08a1 1 0 00-.788 0l-7 3a1 1 0 000 1.84L5.25 8.051a.999.999 0 01.356-.257l4-1.714a1 1 0 11.788 1.838L7.667 9.088l1.94.831a1 1 0 00.787 0l7-3a1 1 0 000-1.838l-7-3zM3.31 9.397L5 10.12v4.102a8.969 8.969 0 00-1.05-.174 1 1 0 01-.89-.89 11.115 11.115 0 01.25-3.762zM9.3 16.573A9.026 9.026 0 007 14.935v-3.957l1.818.78a3 3 0 002.364 0l5.508-2.361a11.026 11.026 0 01.25 3.762 1 1 0 01-.89.89 8.968 8.968 0 00-5.35 2.524 1 1 0 01-1.4 0zM6 18a1 1 0 001-1v-2.065a8.935 8.935 0 00-2-.712V17a1 1 0 001 1z"/>
                                    </svg>
                                </div>
                            </div>
                            <div class="mt-2 md:mt-3 text-center">
                                <p class="text-xs md:text-sm font-medium text-gray-900 dark:text-white truncate max-w-[80px] md:max-w-[100px]">
                                    {{ $forumData['leaderboard'][2]['user']['firstName'] }} {{ $forumData['leaderboard'][2]['user']['lastName'] }}
                                </p>
                                <p class="text-sm md:text-lg font-bold text-orange-600 dark:text-orange-400">{{ $forumData['leaderboard'][2]['points'] }}</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400">{{ __('forum.points_label') }}</p>
                            </div>
                            <div class="mt-1 md:mt-2 w-16 h-10 md:w-20 md:h-14 bg-orange-100 dark:bg-orange-900/30 rounded-t-lg flex items-center justify-center">
                                <span class="text-sm md:text-lg font-bold text-orange-600 dark:text-orange-400">3</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Action Footer -->
                <div class="p-6 border-t border-gray-100 dark:border-gray-700/50">
                    <div class="text-center">
                        <a href="{{ route('forum.index', app()->getLocale()) }}" 
                           class="inline-flex items-center px-4 py-2 text-sm font-medium text-blue-600 dark:text-blue-400 hover:text-blue-700 dark:hover:text-blue-300 transition-colors">
                            {{ __('forum.view_full_leaderboard') }}
                            <svg class="ml-2 w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                            </svg>
                        </a>
                    </div>
                </div>
            </div>
        @endif

        <!-- Top Question Card -->
        @if(isset($forumData['topQuestion']))
            <div class="bg-white dark:bg-gray-800/80 backdrop-blur-sm rounded-2xl shadow-lg hover:shadow-2xl transition-all duration-300 border border-gray-100 dark:border-gray-700/50 overflow-hidden">
                <!-- Question Header -->
                <div class="p-6 border-b border-gray-100 dark:border-gray-700/50">
                    <div class="flex items-start justify-between mb-4">
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-gradient-to-r from-blue-100 to-indigo-100 text-blue-800 dark:from-blue-900/50 dark:to-indigo-900/50 dark:text-blue-200">
                            {{ $forumData['topQuestion']['topics'][0] ?? __('forum.general') }}
                        </span>
                        <div class="flex items-center space-x-2 text-xs text-gray-500 dark:text-gray-400">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                            </svg>
                            <span>{{ $forumData['topQuestion']['stats']['views'] }} {{ __('forum.views') }}</span>
                        </div>
                    </div>
                    
                    <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-3 line-clamp-2">
                        {{ is_string($forumData['topQuestion']['title']) ? json_decode($forumData['topQuestion']['title'], true)[app()->getLocale()] ?? $forumData['topQuestion']['title'] : $forumData['topQuestion']['title'] }}
                    </h3>
                    
                    <div class="flex items-center space-x-3 text-sm text-gray-500 dark:text-gray-400">
                        <div class="flex items-center space-x-2">
                            <div class="w-8 h-8 rounded-full border-2 border-blue-500 bg-white dark:bg-gray-800 flex items-center justify-center text-blue-500 font-bold text-xs">
                                {{ $forumData['topQuestion']['author']['initials'] }}
                            </div>
                            <span>{{ $forumData['topQuestion']['author']['fullName'] }}</span>
                        </div>
                        <span>•</span>
                        <span>{{ $forumData['topQuestion']['stats']['timeAgo'] }}</span>
                    </div>
                </div>

                <!-- Question Content -->
                <div class="p-6">
                    <p class="text-gray-700 dark:text-gray-300 mb-4 line-clamp-3">
                        {{ is_string($forumData['topQuestion']['excerpt']) ? json_decode($forumData['topQuestion']['excerpt'], true)[app()->getLocale()] ?? $forumData['topQuestion']['excerpt'] : $forumData['topQuestion']['excerpt'] }}
                    </p>

                    <!-- Stats -->
                    <div class="flex items-center justify-between mb-4">
                        <div class="flex items-center space-x-4">
                            <a href="{{ route('forum.show', ['locale' => app()->getLocale(), 'id' => $forumData['topQuestion']['id']]) }}" 
                               class="group flex items-center space-x-1 text-gray-500 dark:text-gray-400 hover:text-blue-600 dark:hover:text-blue-400 transition-colors duration-200 cursor-pointer">
                                <svg class="w-4 h-4 group-hover:scale-110 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                                </svg>
                                <span class="text-sm">{{ $forumData['topQuestion']['stats']['answersCount'] }} {{ __('forum.replies') }}</span>
                            </a>
                        </div>
                    </div>

                    <!-- Top Answer Preview -->
                    @if(isset($forumData['topQuestion']['topAnswers']) && count($forumData['topQuestion']['topAnswers']) > 0)
                        <div class="border-t border-gray-100 dark:border-gray-700/50 pt-4">
                            <div class="flex items-center justify-between mb-3">
                                <h4 class="text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('forum.top_answer') }}</h4>
                                @if($forumData['topQuestion']['stats']['answersCount'] > 1)
                                    <span class="text-xs text-gray-500 dark:text-gray-400">
                                        +{{ $forumData['topQuestion']['stats']['answersCount'] - 1 }} {{ __('forum.more_replies') }}
                                    </span>
                                @endif
                            </div>
                            @foreach($forumData['topQuestion']['topAnswers'] as $answer)
                                <div class="flex items-start space-x-3 p-3 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
                                    <div class="w-6 h-6 rounded-full bg-gradient-to-r from-green-500 to-blue-500 flex items-center justify-center text-white font-bold text-xs flex-shrink-0">
                                        {{ $answer['author']['initials'] }}
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm text-gray-700 dark:text-gray-300 line-clamp-2">
                                            {{ is_string($answer['excerpt']) ? json_decode($answer['excerpt'], true)[app()->getLocale()] ?? $answer['excerpt'] : $answer['excerpt'] }}
                                        </p>
                                        <div class="flex items-center space-x-2 mt-1">
                                            <span class="text-xs text-gray-500 dark:text-gray-400">{{ $answer['author']['fullName'] }}</span>
                                            @if($answer['stats']['votes'] > 0)
                                                <span class="text-xs text-green-600 dark:text-green-400">+{{ $answer['stats']['votes'] }}</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>

                <!-- Action Footer -->
                <div class="p-4 sm:p-6 border-t border-gray-100 dark:border-gray-700/50">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 sm:gap-0">
                        <a href="{{ route('forum.index', app()->getLocale()) }}" 
                           class="inline-flex items-center justify-center px-4 py-2.5 text-sm font-medium text-blue-600 dark:text-blue-400 hover:text-blue-700 dark:hover:text-blue-300 transition-all duration-300 transform hover:scale-[1.02] w-full sm:w-auto">
                            <span>{{ __('forum.read_more') }}</span>
                            <svg class="ml-2 w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                            </svg>
                        </a>
                        <a href="{{ route('forum.index', app()->getLocale()) }}" 
                           class="inline-flex items-center justify-center px-4 py-2.5 text-sm text-blue-600 dark:text-blue-400 hover:text-blue-700 dark:hover:text-blue-300 transition-colors duration-200 w-full sm:w-auto rounded-lg hover:bg-blue-50 dark:hover:bg-blue-900/20">
                            {{ __('forum.view_all_questions') }}
                            <svg class="ml-1 w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                            </svg>
                        </a>
                    </div>
                </div>
            </div>
        @endif
    </div>

    <!-- Section Footer -->
    <div class="text-center my-8">
        <a href="{{ route('forum.index', app()->getLocale()) }}" 
           class="inline-flex items-center px-6 py-3 text-base font-medium text-blue-600 dark:text-blue-400 border-2 border-blue-500 hover:bg-blue-50 dark:hover:bg-blue-900/20 rounded-lg transition-colors shadow-lg">
            {{ __('forum.explore_forum') }}
            <svg class="ml-2 w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3" />
            </svg>
        </a>
    </div>
</div>