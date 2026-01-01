@extends('layouts.app')

@section('content')
    <div class="bg-gray-50 dark:bg-gray-900 transition-colors duration-300 min-h-screen">
        <!-- Main Content Container -->
        <div class="max-w-7xl mx-auto px-3 sm:px-4 lg:px-16 py-8">
            <!-- Header -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6 mb-8">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-3">
                        <div class="w-12 h-12 bg-gradient-to-br from-orange-500 to-orange-600 rounded-lg flex items-center justify-center">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                            </svg>
                        </div>
                        <div>
                            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">{{ __('forum.weekly_leaderboard') }}</h1>
                            <p class="text-gray-600 dark:text-gray-400">{{ __('forum.leaderboard_description') }}</p>
                        </div>
                    </div>
                    
                    <!-- Back to Forum Link -->
                    <a href="{{ route('forum.index', ['locale' => app()->getLocale()]) }}" 
                       class="inline-flex items-center px-4 py-2 text-sm font-medium text-blue-600 dark:text-blue-400 hover:bg-blue-50 dark:hover:bg-blue-900/20 rounded-lg transition-colors duration-200">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                        </svg>
                        {{ __('forum.back_to_forum') }}
                    </a>
                </div>
                
                <!-- User Position and Points -->
                @auth
                    @if($isAdmin)
                        <div class="mt-4 p-4 bg-amber-50 dark:bg-amber-900/20 rounded-lg border border-amber-200 dark:border-amber-800">
                            <div class="flex items-center space-x-3">
                                <div class="w-10 h-10 bg-amber-500 rounded-full flex items-center justify-center text-white font-bold">
                                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.643.304 1.254.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-amber-900 dark:text-amber-100">{{ __('forum.admin_status') }}</p>
                                    <p class="text-xs text-amber-700 dark:text-amber-300">{{ __('forum.admin_leaderboard_note') }}</p>
                                </div>
                            </div>
                        </div>
                    @elseif($userPoints)
                        <div class="mt-4 p-4 bg-blue-50 dark:bg-blue-900/20 rounded-lg border border-blue-200 dark:border-blue-800">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center space-x-3">
                                    <div class="w-10 h-10 bg-blue-500 rounded-full flex items-center justify-center text-white font-bold">
                                        {{ substr(Auth::user()->first_name, 0, 1) }}{{ substr(Auth::user()->last_name, 0, 1) }}
                                    </div>
                                    <div>
                                        <p class="text-sm font-medium text-blue-900 dark:text-blue-100">{{ __('forum.your_weekly_rank') }}: #{{ $userRank ?? 'N/A' }}</p>
                                        <p class="text-xs text-blue-700 dark:text-blue-300">{{ __('forum.points', ['points' => $userPoints['weekly'] ?? 0]) }}</p>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <p class="text-lg font-bold text-blue-600 dark:text-blue-400">{{ $userPoints['weekly'] ?? 0 }}</p>
                                    <p class="text-xs text-blue-600 dark:text-blue-400">{{ __('forum.points_label') }}</p>
                                </div>
                            </div>
                        </div>
                    @endif
                @endauth
            </div>

            <!-- Leaderboard Content -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Main Leaderboard -->
                <div class="lg:col-span-2">
                    <!-- Top 3 Podium -->
                    @if(count($leaderboard) >= 3)
                        <div class="bg-gradient-to-br from-white to-gray-50 dark:from-gray-800 dark:to-gray-900 rounded-2xl shadow-lg border border-gray-200 dark:border-gray-700 py-6 p-8 mb-8 backdrop-blur-sm">
                            <div class="flex items-end justify-center space-x-4 md:space-x-8">
                                <!-- 2nd Place -->
                                <div class="flex flex-col items-center group hover:transform hover:scale-105 transition-all duration-300">
                                    <div class="relative">
                                        <div class="w-16 h-16 md:w-20 md:h-20 rounded-full bg-gradient-to-br from-gray-400 to-gray-500 flex items-center justify-center text-white font-bold text-lg md:text-xl shadow-lg group-hover:shadow-xl transition-shadow">
                                            {{ substr($leaderboard[1]['user']['first_name'], 0, 1) }}{{ substr($leaderboard[1]['user']['last_name'], 0, 1) }}
                                        </div>
                                        <div class="absolute -top-2 -right-2 w-8 h-8 bg-gray-100 dark:bg-gray-700 rounded-full flex items-center justify-center shadow-md">
                                            <svg class="w-5 h-5 text-gray-600 dark:text-gray-300" fill="currentColor" viewBox="0 0 20 20">
                                                <path d="M10.394 2.08a1 1 0 00-.788 0l-7 3a1 1 0 000 1.84L5.25 8.051a.999.999 0 01.356-.257l4-1.714a1 1 0 11.788 1.838L7.667 9.088l1.94.831a1 1 0 00.787 0l7-3a1 1 0 000-1.838l-7-3zM3.31 9.397L5 10.12v4.102a8.969 8.969 0 00-1.05-.174 1 1 0 01-.89-.89 11.115 11.115 0 01.25-3.762zM9.3 16.573A9.026 9.026 0 007 14.935v-3.957l1.818.78a3 3 0 002.364 0l5.508-2.361a11.026 11.026 0 01.25 3.762 1 1 0 01-.89.89 8.968 8.968 0 00-5.35 2.524 1 1 0 01-1.4 0zM6 18a1 1 0 001-1v-2.065a8.935 8.935 0 00-2-.712V17a1 1 0 001 1z"/>
                                            </svg>
                                        </div>
                                    </div>
                                    <div class="mt-4 text-center">
                                        <p class="text-sm font-medium text-gray-900 dark:text-white truncate max-w-[100px] md:max-w-[120px] group-hover:text-gray-700 dark:group-hover:text-gray-200 transition-colors">
                                            {{ $leaderboard[1]['user']['first_name'] }} {{ $leaderboard[1]['user']['last_name'] }}
                                        </p>
                                        <p class="text-xl md:text-2xl font-bold text-gray-600 dark:text-gray-300">{{ $leaderboard[1]['points'] }}</p>
                                        <p class="text-xs text-gray-500 dark:text-gray-400">{{ __('forum.points_label') }}</p>
                                    </div>
                                    <div class="mt-4 w-20 h-16 md:w-24 md:h-20 bg-gradient-to-b from-gray-100 to-gray-200 dark:from-gray-700 dark:to-gray-600 rounded-t-lg flex items-center justify-center shadow-md">
                                        <span class="text-xl md:text-2xl font-bold text-gray-600 dark:text-gray-300">2</span>
                                    </div>
                                </div>

                                <!-- 1st Place -->
                                <div class="flex flex-col items-center -mt-4 md:-mt-6 group hover:transform hover:scale-105 transition-all duration-300">
                                    <div class="relative">
                                        <div class="w-20 h-20 md:w-24 md:h-24 rounded-full bg-gradient-to-br from-yellow-400 to-orange-500 flex items-center justify-center text-white font-bold text-xl md:text-2xl shadow-xl ring-4 ring-yellow-200 dark:ring-yellow-800 group-hover:shadow-2xl transition-shadow">
                                            {{ substr($leaderboard[0]['user']['first_name'], 0, 1) }}{{ substr($leaderboard[0]['user']['last_name'], 0, 1) }}
                                        </div>
                                        <div class="absolute -top-2 -right-2 w-10 h-10 bg-yellow-100 dark:bg-yellow-900/30 rounded-full flex items-center justify-center shadow-md">
                                            <svg class="w-6 h-6 text-yellow-600 dark:text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                                                <path d="M10.394 2.08a1 1 0 00-.788 0l-7 3a1 1 0 000 1.84L5.25 8.051a.999.999 0 01.356-.257l4-1.714a1 1 0 11.788 1.838L7.667 9.088l1.94.831a1 1 0 00.787 0l7-3a1 1 0 000-1.838l-7-3zM3.31 9.397L5 10.12v4.102a8.969 8.969 0 00-1.05-.174 1 1 0 01-.89-.89 11.115 11.115 0 01.25-3.762zM9.3 16.573A9.026 9.026 0 007 14.935v-3.957l1.818.78a3 3 0 002.364 0l5.508-2.361a11.026 11.026 0 01.25 3.762 1 1 0 01-.89.89 8.968 8.968 0 00-5.35 2.524 1 1 0 01-1.4 0zM6 18a1 1 0 001-1v-2.065a8.935 8.935 0 00-2-.712V17a1 1 0 001 1z"/>
                                            </svg>
                                        </div>
                                    </div>
                                    <div class="mt-4 text-center">
                                        <p class="text-sm font-medium text-gray-900 dark:text-white truncate max-w-[120px] md:max-w-[140px] group-hover:text-gray-700 dark:group-hover:text-gray-200 transition-colors">
                                            {{ $leaderboard[0]['user']['first_name'] }} {{ $leaderboard[0]['user']['last_name'] }}
                                        </p>
                                        <p class="text-2xl md:text-3xl font-bold text-yellow-600 dark:text-yellow-400">{{ $leaderboard[0]['points'] }}</p>
                                        <p class="text-xs text-gray-500 dark:text-gray-400">{{ __('forum.points_label') }}</p>
                                    </div>
                                    <div class="mt-4 w-20 h-20 md:w-24 md:h-24 bg-gradient-to-b from-yellow-100 to-yellow-200 dark:from-yellow-900/30 dark:to-yellow-800/40 rounded-t-lg flex items-center justify-center shadow-lg">
                                        <span class="text-2xl md:text-3xl font-bold text-yellow-600 dark:text-yellow-400">1</span>
                                    </div>
                                </div>

                                <!-- 3rd Place -->
                                <div class="flex flex-col items-center group hover:transform hover:scale-105 transition-all duration-300">
                                    <div class="relative">
                                        <div class="w-16 h-16 md:w-20 md:h-20 rounded-full bg-gradient-to-br from-orange-400 to-orange-600 flex items-center justify-center text-white font-bold text-lg md:text-xl shadow-lg group-hover:shadow-xl transition-shadow">
                                            {{ substr($leaderboard[2]['user']['first_name'], 0, 1) }}{{ substr($leaderboard[2]['user']['last_name'], 0, 1) }}
                                        </div>
                                        <div class="absolute -top-2 -right-2 w-8 h-8 bg-orange-100 dark:bg-orange-900/30 rounded-full flex items-center justify-center shadow-md">
                                            <svg class="w-5 h-5 text-orange-600 dark:text-orange-400" fill="currentColor" viewBox="0 0 20 20">
                                                <path d="M10.394 2.08a1 1 0 00-.788 0l-7 3a1 1 0 000 1.84L5.25 8.051a.999.999 0 01.356-.257l4-1.714a1 1 0 11.788 1.838L7.667 9.088l1.94.831a1 1 0 00.787 0l7-3a1 1 0 000-1.838l-7-3zM3.31 9.397L5 10.12v4.102a8.969 8.969 0 00-1.05-.174 1 1 0 01-.89-.89 11.115 11.115 0 01.25-3.762zM9.3 16.573A9.026 9.026 0 007 14.935v-3.957l1.818.78a3 3 0 002.364 0l5.508-2.361a11.026 11.026 0 01.25 3.762 1 1 0 01-.89.89 8.968 8.968 0 00-5.35 2.524 1 1 0 01-1.4 0zM6 18a1 1 0 001-1v-2.065a8.935 8.935 0 00-2-.712V17a1 1 0 001 1z"/>
                                            </svg>
                                        </div>
                                    </div>
                                    <div class="mt-4 text-center">
                                        <p class="text-sm font-medium text-gray-900 dark:text-white truncate max-w-[100px] md:max-w-[120px] group-hover:text-gray-700 dark:group-hover:text-gray-200 transition-colors">
                                            {{ $leaderboard[2]['user']['first_name'] }} {{ $leaderboard[2]['user']['last_name'] }}
                                        </p>
                                        <p class="text-xl md:text-2xl font-bold text-orange-600 dark:text-orange-400">{{ $leaderboard[2]['points'] }}</p>
                                        <p class="text-xs text-gray-500 dark:text-gray-400">{{ __('forum.points_label') }}</p>
                                    </div>
                                    <div class="mt-4 w-20 h-16 md:w-24 md:h-20 bg-gradient-to-b from-orange-100 to-orange-200 dark:from-orange-900/30 dark:to-orange-800/40 rounded-t-lg flex items-center justify-center shadow-md">
                                        <span class="text-xl md:text-2xl font-bold text-orange-600 dark:text-orange-400">3</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif

                    <!-- Rest of Leaderboard -->
                    <div class="bg-gradient-to-br from-white to-gray-50 dark:from-gray-800 dark:to-gray-900 rounded-2xl shadow-lg border border-gray-200 dark:border-gray-700 overflow-hidden backdrop-blur-sm">
                        @forelse($leaderboard as $index => $entry)
                            @if($index >= 3)
                                <div class="group p-4 border-b border-gray-100 dark:border-gray-700 last:border-b-0 hover:bg-gradient-to-r hover:from-blue-50 hover:to-indigo-50 dark:hover:from-blue-900/20 dark:hover:to-indigo-900/20 transition-all duration-200 cursor-pointer
                                     {{ $entry['user']['id'] === Auth::id() ? 'bg-gradient-to-r from-blue-50 to-indigo-50 dark:from-blue-900/20 dark:to-indigo-900/20' : '' }}">
                                    <div class="flex items-center space-x-4">
                                        <!-- Rank Number -->
                                        <div class="flex-shrink-0 w-8 text-center">
                                            <span class="text-lg font-bold text-gray-500 dark:text-gray-400">{{ $index + 1 }}</span>
                                        </div>
                                        
                                        <!-- User Avatar with hover effect -->
                                        <div class="flex-shrink-0 relative group">
                                            <div class="w-12 h-12 rounded-full bg-gradient-to-br from-blue-500 to-blue-600 flex items-center justify-center text-white font-bold shadow-md group-hover:shadow-lg transition-all duration-200 group-hover:scale-105">
                                                {{ substr($entry['user']['first_name'], 0, 1) }}{{ substr($entry['user']['last_name'], 0, 1) }}
                                            </div>
                                            @if($entry['user']['id'] === Auth::id())
                                                <span class="absolute -bottom-1 -right-1 w-4 h-4 bg-blue-500 rounded-full flex items-center justify-center text-white text-xs font-bold shadow-md">YOU</span>
                                            @endif
                                        </div>
                                        
                                        <!-- User Info -->
                                        <div class="flex-1 min-w-0">
                                            <div class="flex items-center space-x-2 mb-1">
                                                <p class="text-sm font-medium text-gray-900 dark:text-white truncate group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors">
                                                    {{ $entry['user']['first_name'] }} {{ $entry['user']['last_name'] }}
                                                </p>
                                                @if($entry['user']['id'] === Auth::id())
                                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-300">
                                                        {{ __('forum.you') }}
                                                    </span>
                                                @elseif($index + 1 <= 5)
                                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300">
                                                        Top 5
                                                    </span>
                                                @elseif($index + 1 <= 10)
                                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-300">
                                                        Top 10
                                                    </span>
                                                @endif
                                            </div>
                                            <p class="text-xs text-gray-500 dark:text-gray-400">
                                                {{ __('forum.joined') }} {{ timeDiffForHumans($entry['user']['createdAt']) }}
                                            </p>
                                        </div>
                                        
                                        <!-- Points with enhanced styling -->
                                        <div class="flex-shrink-0 text-right">
                                            <p class="text-lg font-bold bg-clip-text text-transparent"
                                               style="background-image: linear-gradient(to right, 
                                                   @if($index + 1 <= 5) 
                                                       #059669, #047857
                                                   @elseif($index + 1 <= 10) 
                                                       #2563eb, #1d4ed8
                                                   @else 
                                                       #4b5563, #374151
                                                   @endif)">
                                                {{ $entry['points'] }}
                                            </p>
                                            <p class="text-xs text-gray-500 dark:text-gray-400">{{ __('forum.points_label') }}</p>
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

                <!-- Sidebar -->
                <div class="lg:col-span-1">
                    <!-- How to Earn Points -->
                    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6 mb-6">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">{{ __('forum.how_to_earn_points') }}</h3>
                        <ul class="space-y-3 text-sm text-gray-600 dark:text-gray-400">
                            <li class="flex items-start">
                                <svg class="w-5 h-5 text-green-500 mr-2 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                </svg>
                                <span>{{ __('forum.points_for_question') }}</span>
                            </li>
                            <li class="flex items-start">
                                <svg class="w-5 h-5 text-green-500 mr-2 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                </svg>
                                <span>{{ __('forum.points_for_answer') }}</span>
                            </li>
                            <li class="flex items-start">
                                <svg class="w-5 h-5 text-green-500 mr-2 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                </svg>
                                <span>{{ __('forum.points_for_daily_visit') }}</span>
                            </li>
                            <li class="flex items-start">
                                <svg class="w-5 h-5 text-green-500 mr-2 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                </svg>
                                <span>{{ __('forum.points_for_quiz_completion') }}</span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
