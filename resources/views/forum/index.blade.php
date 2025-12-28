@extends('layouts.app')

@section('content')
    <div class="bg-gray-50 dark:bg-gray-900 transition-colors duration-300">
        <!-- Main Content Container -->
        <div class="max-w-7xl mx-auto px-3 sm:px-4 lg:px-16" x-data="forumTabs()">
            <!-- Mobile Tab Navigation -->
            <div class="lg:hidden sticky top-0 z-40 bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700">
                <div class="flex">
                    <button @click="handleTabChange('discussions')"
                        class="flex-1 px-3 py-2 text-center font-medium transition-colors duration-200 border-b-2"
                        :class="activeTab === 'discussions'
                            ?
                            'text-blue-600 dark:text-blue-400 border-blue-600 dark:border-blue-400' :
                            'text-gray-500 dark:text-gray-400 border-transparent hover:text-gray-700 dark:hover:text-gray-300'">
                        <div class="flex items-center justify-center space-x-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                            </svg>
                            <span>{{ __('forum.discussions') }}</span>
                        </div>
                    </button>
                    <button @click="handleTabChange('leaderboard')"
                        class="flex-1 px-3 py-2 text-center font-medium transition-colors duration-200 border-b-2"
                        :class="activeTab === 'leaderboard'
                            ?
                            'text-blue-600 dark:text-blue-400 border-blue-600 dark:border-blue-400' :
                            'text-gray-500 dark:text-gray-400 border-transparent hover:text-gray-700 dark:hover:text-gray-300'">
                        <div class="flex items-center justify-center space-x-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                            </svg>
                            <span>{{ __('forum.leaderboard') }}</span>
                        </div>
                    </button>
                </div>
            </div>
            <!-- Desktop Navigation -->
            <div class="hidden lg:block sticky top-0 z-40 bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700">
                <!-- Loading Skeleton -->
                <div x-cloak x-data="{ loading: true }" 
                     x-init="setTimeout(() => loading = false, 100)"
                     x-show="!loading"
                     class="relative flex items-center justify-between px-6 py-3">
                    <!-- Navigation button -->
                    <button @click="handleTabChange(activeTab === 'discussions' ? 'leaderboard' : 'discussions')"
                        x-show="activeTab === 'discussions' || activeTab === 'leaderboard'"
                        class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-600 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white transition-colors">
                        <svg x-show="activeTab === 'leaderboard'" class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                        </svg>
                        <svg x-show="activeTab === 'discussions'" class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                        </svg>
                        <span x-show="activeTab === 'leaderboard'">{{ __('forum.back_to_discussions') }}</span>
                        <span x-show="activeTab === 'discussions'">{{ __('forum.leaderboard') }}</span>
                    </button>
                    
                    <!-- Center: Current view title -->
                    <div class="absolute left-1/2 transform -translate-x-1/2">
                        <h2 class="text-lg font-semibold text-gray-900 dark:text-white">
                            <span x-show="activeTab === 'discussions'">{{ __('forum.discussions') }}</span>
                            <span x-show="activeTab === 'leaderboard'">{{ __('forum.leaderboard') }}</span>
                        </h2>
                    </div>
                    
                    <!-- Empty space for balance -->
                    <div></div>
                </div>
                
                <!-- Loading State -->
                <div x-show="loading" class="p-4 animate-pulse">
                    <div class="flex justify-between items-center">
                        <div class="h-8 w-24 bg-gray-200 dark:bg-gray-700 rounded"></div>
                        <div class="h-6 w-32 bg-gray-200 dark:bg-gray-700 rounded"></div>
                        <div class="w-8"></div>
                    </div>
                </div>
            </div>
            <div class="flex flex-col lg:flex-row">
                <!-- Discussions Section -->
                <div class="flex-1 lg:flex-none lg:w-full">

                        <!-- Mobile Discussions Content -->
                        <div class="lg:hidden" x-show="activeTab === 'discussions'"
                            x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0"
                            x-transition:enter-end="opacity-100">
                            <!-- Search Bar -->
                            <div
                                class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-3 mb-3">
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                        </svg>
                                    </div>
                                    <input type="text" id="mobile-search"
                                        placeholder="{{ __('forum.search_placeholder') }}"
                                        class="w-full pl-10 pr-3 py-2 border border-gray-200 dark:border-gray-600 rounded-lg 
                                          focus:ring-2 focus:ring-blue-500 focus:border-transparent 
                                          bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 
                                          placeholder-gray-500 dark:placeholder-gray-400 transition-all duration-200">
                                </div>
                            </div>

                            <!-- Questions List -->
                            <div class="space-y-3">
                                @forelse($questions as $question)
                                    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-3 hover:shadow-md transition-all duration-200"
                                        data-question-id="{{ $question['id'] }}">
                                        <!-- Question Header with Voting -->
                                        <div class="flex items-start space-x-3">
                                            <!-- Voting Column -->
                                            <div class="flex flex-col items-center space-y-1 flex-shrink-0">
                                                <button
                                                    class="w-6 h-6 rounded hover:bg-gray-100 dark:hover:bg-gray-700 flex items-center justify-center transition-colors">
                                                    <svg class="w-4 h-4 text-gray-400 hover:text-orange-500"
                                                        fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd"
                                                            d="M3.293 9.707a1 1 0 010-1.414l6-6a1 1 0 011.414 0l6 6a1 1 0 01-1.414 1.414L11 5.414V17a1 1 0 11-2 0V5.414L4.707 9.707a1 1 0 01-1.414 0z"
                                                            clip-rule="evenodd" />
                                                    </svg>
                                                </button>
                                                <span class="text-xs font-medium text-gray-600 dark:text-gray-300">0</span>
                                                <button
                                                    class="w-6 h-6 rounded hover:bg-gray-100 dark:hover:bg-gray-700 flex items-center justify-center transition-colors">
                                                    <svg class="w-4 h-4 text-gray-400 hover:text-blue-500"
                                                        fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd"
                                                            d="M16.707 10.293a1 1 0 010 1.414l-6 6a1 1 0 01-1.414 0l-6-6a1 1 0 111.414-1.414L9 14.586V3a1 1 0 012 0v11.586l4.293-4.293a1 1 0 011.414 0z"
                                                            clip-rule="evenodd" />
                                                    </svg>
                                                </button>
                                            </div>

                                            <!-- Content Column -->
                                            <div class="flex-1 min-w-0">
                                                <!-- Question Title and Meta -->
                                                <div class="mb-2">
                                                    <h3
                                                        class="text-lg font-semibold text-gray-900 dark:text-white mb-2 line-clamp-2">
                                                        <a href="{{ route('forum.show', ['locale' => app()->getLocale(), 'id' => $question['id']]) }}"
                                                            class="hover:text-blue-600 dark:hover:text-blue-400 transition-colors">
                                                            {{ $question['title'] }}
                                                        </a>
                                                    </h3>
                                                    <div
                                                        class="flex items-center space-x-2 text-xs text-gray-500 dark:text-gray-400">
                                                        <div class="flex items-center space-x-1">
                                                            <div
                                                                class="w-5 h-5 rounded-full bg-gradient-to-br from-blue-500 to-blue-600 flex items-center justify-center text-white text-xs font-bold">
                                                                {{ substr($question['author']['firstName'], 0, 1) }}{{ substr($question['author']['lastName'], 0, 1) }}
                                                            </div>
                                                            <span>{{ $question['author']['firstName'] }}
                                                                {{ $question['author']['lastName'] }}</span>
                                                        </div>
                                                        <span>•</span>
                                                        <span>{{ timeDiffForHumans($question['createdAt']) }}</span>
                                                    </div>
                                                </div>

                                                <!-- Question Content -->
                                                <p class="text-gray-600 dark:text-gray-300 text-sm line-clamp-3 mb-3">
                                                    {{ Str::limit(strip_tags($question['content']), 200) }}
                                                </p>

                                                <!-- Action Buttons -->
                                                <div class="flex items-center space-x-4 text-xs">
                                                    <button onclick="toggleCommentBox({{ $question['id'] }}, 'question')"
                                                        class="flex items-center space-x-1 text-gray-500 hover:text-blue-600 dark:hover:text-blue-400 transition-colors">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                            viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2"
                                                                d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                                                        </svg>
                                                        <span>Reply</span>
                                                    </button>
                                                    @if (count($question['answers']) > 0)
                                                        <button
                                                            class="flex items-center space-x-1 text-gray-500 hover:text-blue-600 dark:hover:text-blue-400 transition-colors">
                                                            <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                                viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                                    stroke-width="2" d="M19 9l-7 7-7-7" />
                                                            </svg>
                                                            <span>{{ count($question['answers']) }}
                                                                {{ count($question['answers']) == 1 ? 'Reply' : 'Replies' }}</span>
                                                        </button>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Answers Section -->
                                        @if (count($question['answers']) > 0)
                                            <div class="mt-4 space-y-2">
                                                @foreach ($question['answers'] as $answer)
                                                    <div class="pl-4 border-l-2 border-gray-200 dark:border-gray-600">
                                                        <!-- Answer with Voting -->
                                                        <div class="flex items-start space-x-2"
                                                            data-answer-id="{{ $answer['id'] }}"
                                                            data-question-id="{{ $question['id'] }}">
                                                            <!-- Answer Voting -->
                                                            <div class="flex flex-col items-center space-y-1 flex-shrink-0">
                                                                <button
                                                                    class="w-5 h-5 rounded hover:bg-gray-100 dark:hover:bg-gray-700 flex items-center justify-center transition-colors">
                                                                    <svg class="w-3 h-3 text-gray-400 hover:text-orange-500"
                                                                        fill="currentColor" viewBox="0 0 20 20">
                                                                        <path fill-rule="evenodd"
                                                                            d="M3.293 9.707a1 1 0 010-1.414l6-6a1 1 0 011.414 0l6 6a1 1 0 01-1.414 1.414L11 5.414V17a1 1 0 11-2 0V5.414L4.707 9.707a1 1 0 01-1.414 0z" />
                                                                    </svg>
                                                                </button>
                                                                <span
                                                                    class="text-xs font-medium text-gray-600 dark:text-gray-300">0</span>
                                                                <button
                                                                    class="w-5 h-5 rounded hover:bg-gray-100 dark:hover:bg-gray-700 flex items-center justify-center transition-colors">
                                                                    <svg class="w-3 h-3 text-gray-400 hover:text-blue-500"
                                                                        fill="currentColor" viewBox="0 0 20 20">
                                                                        <path fill-rule="evenodd"
                                                                            d="M16.707 10.293a1 1 0 010 1.414l-6 6a1 1 0 01-1.414 0l-6-6a1 1 0 111.414-1.414L9 14.586V3a1 1 0 012 0v11.586l4.293-4.293a1 1 0 011.414 0z"
                                                                            clip-rule="evenodd" />
                                                                    </svg>
                                                                </button>
                                                            </div>

                                                            <!-- Answer Content -->
                                                            <div class="flex-1 min-w-0">
                                                                <div class="flex items-center space-x-2 mb-1">
                                                                    <div
                                                                        class="w-4 h-4 rounded-full bg-gradient-to-br from-green-500 to-green-600 flex items-center justify-center text-white text-xs font-bold">
                                                                        {{ substr($answer['author']['firstName'], 0, 1) }}{{ substr($answer['author']['lastName'], 0, 1) }}
                                                                    </div>
                                                                    <span
                                                                        class="text-xs font-medium text-gray-700 dark:text-gray-300">
                                                                        {{ $answer['author']['firstName'] }}
                                                                        {{ $answer['author']['lastName'] }}
                                                                    </span>
                                                                    <span class="text-xs text-gray-500 dark:text-gray-400">
                                                                        • {{ timeDiffForHumans($answer['createdAt']) }}
                                                                    </span>
                                                                </div>
                                                                <p
                                                                    class="text-sm text-gray-600 dark:text-gray-300 line-clamp-2 mb-2">
                                                                    {{ Str::limit(strip_tags($answer['content']), 120) }}
                                                                </p>

                                                                <!-- Reply to Answer -->
                                                                <button
                                                                    onclick="toggleCommentBox({{ $answer['id'] }}, 'answer')"
                                                                    class="flex items-center space-x-1 text-xs text-gray-500 hover:text-blue-600 dark:hover:text-blue-400 transition-colors">
                                                                    <svg class="w-3 h-3" fill="none"
                                                                        stroke="currentColor" viewBox="0 0 24 24">
                                                                        <path stroke-linecap="round"
                                                                            stroke-linejoin="round" stroke-width="2"
                                                                            d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                                                                    </svg>
                                                                    <span>Reply</span>
                                                                </button>

                                                                <!-- Reply Form (Hidden by default) -->
                                                                <div id="comment-box-{{ $answer['id'] }}"
                                                                    class="hidden mt-3">
                                                                    <form
                                                                        onsubmit="submitReply(event, {{ $answer['id'] }}, 'answer')">
                                                                        @csrf
                                                                        <textarea name="content" rows="3"
                                                                            class="w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 placeholder-gray-500 dark:placeholder-gray-400"
                                                                            placeholder="Write your reply..."></textarea>
                                                                        <div class="mt-2 flex justify-end space-x-2">
                                                                            <button type="button"
                                                                                onclick="toggleCommentBox({{ $answer['id'] }}, 'answer')"
                                                                                class="px-3 py-1 text-xs font-medium text-gray-600 dark:text-gray-300 hover:text-gray-800 dark:hover:text-gray-100 transition-colors">
                                                                                Cancel
                                                                            </button>
                                                                            <button type="submit"
                                                                                class="px-3 py-1 text-xs font-medium text-white bg-blue-600 hover:bg-blue-700 rounded-md transition-colors">
                                                                                Post Reply
                                                                            </button>
                                                                        </div>
                                                                    </form>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        @endif

                                        <!-- Reply Form for Question (Hidden by default) -->
                                        <div id="comment-box-{{ $question['id'] }}"
                                            class="hidden mt-4 pt-4 border-t border-gray-200 dark:border-gray-600">
                                            <form onsubmit="submitReply(event, {{ $question['id'] }}, 'question')">
                                                @csrf
                                                <textarea name="content" rows="3"
                                                    class="w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 placeholder-gray-500 dark:placeholder-gray-400"
                                                    placeholder="Write your answer..."></textarea>
                                                <div class="mt-2 flex justify-end space-x-2">
                                                    <button type="button"
                                                        onclick="toggleCommentBox({{ $question['id'] }}, 'question')"
                                                        class="px-3 py-1 text-xs font-medium text-gray-600 dark:text-gray-300 hover:text-gray-800 dark:hover:text-gray-100 transition-colors">
                                                        Cancel
                                                    </button>
                                                    <button type="submit"
                                                        class="px-3 py-1 text-xs font-medium text-white bg-blue-600 hover:bg-blue-700 rounded-md transition-colors">
                                                        Post Answer
                                                    </button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                @empty
                                    <div
                                        class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6 text-center">
                                        <div
                                            class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-blue-100 dark:bg-blue-900/30 mb-4">
                                            <svg class="h-6 w-6 text-blue-600 dark:text-blue-400" fill="none"
                                                stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                                    d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                        </div>
                                        <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">
                                            {{ __('forum.no_questions_title') }}</h3>
                                        <p class="text-gray-500 dark:text-gray-300 mb-4">{{ __('forum.no_questions') }}
                                        </p>
                                        <a href="{{ route('forum.create', ['locale' => app()->getLocale()]) }}"
                                            class="inline-flex items-center px-3 py-2 border border-transparent text-sm font-medium rounded-lg shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-all duration-200">
                                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M12 4v16m8-8H4" />
                                            </svg>
                                            {{ __('forum.ask_question') }}
                                        </a>
                                    </div>
                                @endforelse
                            </div>

                            <!-- Pagination -->
                            @if ($questions->hasPages())
                                <div class="mt-6">
                                    {{ $questions->links() }}
                                </div>
                            @endif
                        </div>

                        <!-- Desktop Discussions Content -->
                        <div class="hidden lg:block" x-show="activeTab === 'discussions'"
                            x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0"
                            x-transition:enter-end="opacity-100">
                            <!-- Search Bar -->
                            <div
                                class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-5 mb-6">
                                <div class="relative w-full">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                        </svg>
                                    </div>
                                    <input type="text" id="desktop-search"
                                        placeholder="{{ __('forum.search_placeholder') }}"
                                        class="w-full pl-10 pr-4 py-2.5 border border-gray-200 dark:border-gray-600 rounded-xl 
                                          focus:ring-2 focus:ring-blue-500 focus:border-transparent 
                                          bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 
                                          placeholder-gray-500 dark:placeholder-gray-400">
                                </div>
                            </div>

                            <!-- Questions List (Desktop) -->
                            <div class="space-y-4">
                                @forelse($questions as $question)
                                    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-5 hover:shadow-md transition-all duration-200"
                                        data-question-id="{{ $question['id'] }}">
                                        <!-- Question Header with Voting -->
                                        <div class="flex items-start space-x-4">
                                            <!-- Voting Column -->
                                            <div class="flex flex-col items-center space-y-2 flex-shrink-0">
                                                <button
                                                    class="w-8 h-8 rounded hover:bg-gray-100 dark:hover:bg-gray-700 flex items-center justify-center transition-colors">
                                                    <svg class="w-5 h-5 text-gray-400 hover:text-orange-500"
                                                        fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd"
                                                            d="M3.293 9.707a1 1 0 010-1.414l6-6a1 1 0 011.414 0l6 6a1 1 0 01-1.414 1.414L11 5.414V17a1 1 0 11-2 0V5.414L4.707 9.707a1 1 0 01-1.414 0z"
                                                            clip-rule="evenodd" />
                                                    </svg>
                                                </button>
                                                <span class="text-sm font-medium text-gray-600 dark:text-gray-300">0</span>
                                                <button
                                                    class="w-8 h-8 rounded hover:bg-gray-100 dark:hover:bg-gray-700 flex items-center justify-center transition-colors">
                                                    <svg class="w-5 h-5 text-gray-400 hover:text-blue-500"
                                                        fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd"
                                                            d="M16.707 10.293a1 1 0 010 1.414l-6 6a1 1 0 01-1.414 0l-6-6a1 1 0 111.414-1.414L9 14.586V3a1 1 0 012 0v11.586l4.293-4.293a1 1 0 011.414 0z"
                                                            clip-rule="evenodd" />
                                                    </svg>
                                                </button>
                                            </div>

                                            <!-- Content Column -->
                                            <div class="flex-1 min-w-0">
                                                <!-- Question Title and Meta -->
                                                <div class="mb-3">
                                                    <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-2">
                                                        <a href="{{ route('forum.show', ['locale' => app()->getLocale(), 'id' => $question['id']]) }}"
                                                            class="hover:text-blue-600 dark:hover:text-blue-400 transition-colors">
                                                            {{ $question['title'] }}
                                                        </a>
                                                    </h3>
                                                    <div
                                                        class="flex items-center space-x-3 text-sm text-gray-500 dark:text-gray-400">
                                                        <div class="flex items-center space-x-2">
                                                            <div
                                                                class="w-6 h-6 rounded-full bg-gradient-to-br from-blue-500 to-blue-600 flex items-center justify-center text-white text-xs font-bold">
                                                                {{ substr($question['author']['firstName'], 0, 1) }}{{ substr($question['author']['lastName'], 0, 1) }}
                                                            </div>
                                                            <span>{{ $question['author']['firstName'] }}
                                                                {{ $question['author']['lastName'] }}</span>
                                                        </div>
                                                        <span>•</span>
                                                        <span>{{ timeDiffForHumans($question['createdAt']) }}</span>
                                                    </div>
                                                </div>

                                                <!-- Question Content -->
                                                <p class="text-gray-600 dark:text-gray-300 line-clamp-3 mb-4">
                                                    {{ Str::limit(strip_tags($question['content']), 300) }}
                                                </p>

                                                <!-- Action Buttons -->
                                                <div class="flex items-center space-x-6 text-sm">
                                                    <button
                                                        onclick="toggleCommentBox('desktop-{{ $question['id'] }}', 'question')"
                                                        class="flex items-center space-x-2 text-gray-500 hover:text-blue-600 dark:hover:text-blue-400 transition-colors">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                            viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2"
                                                                d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                                                        </svg>
                                                        <span>Reply</span>
                                                    </button>
                                                    @if (count($question['answers']) > 0)
                                                        <button
                                                            class="flex items-center space-x-2 text-gray-500 hover:text-blue-600 dark:hover:text-blue-400 transition-colors">
                                                            <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                                viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                                    stroke-width="2" d="M19 9l-7 7-7-7" />
                                                            </svg>
                                                            <span>{{ count($question['answers']) }}
                                                                {{ count($question['answers']) == 1 ? 'Reply' : 'Replies' }}</span>
                                                        </button>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Answers Section -->
                                        @if (count($question['answers']) > 0)
                                            <div class="mt-4 space-y-3">
                                                @foreach ($question['answers'] as $answer)
                                                    <div class="pl-6 border-l-2 border-gray-200 dark:border-gray-600">
                                                        <!-- Answer with Voting -->
                                                        <div class="flex items-start space-x-3"
                                                            data-answer-id="{{ $answer['id'] }}"
                                                            data-question-id="{{ $question['id'] }}">
                                                            <!-- Answer Voting -->
                                                            <div
                                                                class="flex flex-col items-center space-y-1 flex-shrink-0">
                                                                <button
                                                                    class="w-6 h-6 rounded hover:bg-gray-100 dark:hover:bg-gray-700 flex items-center justify-center transition-colors">
                                                                    <svg class="w-4 h-4 text-gray-400 hover:text-orange-500"
                                                                        fill="currentColor" viewBox="0 0 20 20">
                                                                        <path fill-rule="evenodd"
                                                                            d="M3.293 9.707a1 1 0 010-1.414l6-6a1 1 0 011.414 0l6 6a1 1 0 01-1.414 1.414L11 5.414V17a1 1 0 11-2 0V5.414L4.707 9.707a1 1 0 01-1.414 0z"
                                                                            clip-rule="evenodd" />
                                                                    </svg>
                                                                </button>
                                                                <span
                                                                    class="text-xs font-medium text-gray-600 dark:text-gray-300">0</span>
                                                                <button
                                                                    class="w-6 h-6 rounded hover:bg-gray-100 dark:hover:bg-gray-700 flex items-center justify-center transition-colors">
                                                                    <svg class="w-4 h-4 text-gray-400 hover:text-blue-500"
                                                                        fill="currentColor" viewBox="0 0 20 20">
                                                                        <path fill-rule="evenodd"
                                                                            d="M16.707 10.293a1 1 0 010 1.414l-6 6a1 1 0 01-1.414 0l-6-6a1 1 0 111.414-1.414L9 14.586V3a1 1 0 012 0v11.586l4.293-4.293a1 1 0 011.414 0z"
                                                                            clip-rule="evenodd" />
                                                                    </svg>
                                                                </button>
                                                            </div>

                                                            <!-- Answer Content -->
                                                            <div class="flex-1 min-w-0">
                                                                <div class="flex items-center space-x-3 mb-2">
                                                                    <div
                                                                        class="w-5 h-5 rounded-full bg-gradient-to-br from-green-500 to-green-600 flex items-center justify-center text-white text-xs font-bold">
                                                                        {{ substr($answer['author']['firstName'], 0, 1) }}{{ substr($answer['author']['lastName'], 0, 1) }}
                                                                    </div>
                                                                    <span
                                                                        class="text-sm font-medium text-gray-700 dark:text-gray-300">
                                                                        {{ $answer['author']['firstName'] }}
                                                                        {{ $answer['author']['lastName'] }}
                                                                    </span>
                                                                    <span class="text-sm text-gray-500 dark:text-gray-400">
                                                                        • {{ timeDiffForHumans($answer['createdAt']) }}
                                                                    </span>
                                                                </div>
                                                                <p
                                                                    class="text-gray-600 dark:text-gray-300 line-clamp-3 mb-3">
                                                                    {{ Str::limit(strip_tags($answer['content']), 200) }}
                                                                </p>

                                                                <!-- Reply to Answer -->
                                                                <button
                                                                    onclick="toggleCommentBox('desktop-{{ $answer['id'] }}', 'answer')"
                                                                    class="flex items-center space-x-2 text-sm text-gray-500 hover:text-blue-600 dark:hover:text-blue-400 transition-colors">
                                                                    <svg class="w-4 h-4" fill="none"
                                                                        stroke="currentColor" viewBox="0 0 24 24">
                                                                        <path stroke-linecap="round"
                                                                            stroke-linejoin="round" stroke-width="2"
                                                                            d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                                                                    </svg>
                                                                    <span>Reply</span>
                                                                </button>

                                                                <!-- Reply Form (Hidden by default) -->
                                                                <div id="comment-box-desktop-{{ $answer['id'] }}"
                                                                    class="hidden mt-3">
                                                                    <form
                                                                        onsubmit="submitReply(event, {{ $answer['id'] }}, 'answer')">
                                                                        @csrf
                                                                        <textarea name="content" rows="3"
                                                                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 placeholder-gray-500 dark:placeholder-gray-400"
                                                                            placeholder="Write your reply..."></textarea>
                                                                        <div class="mt-2 flex justify-end space-x-2">
                                                                            <button type="button"
                                                                                onclick="toggleCommentBox('desktop-{{ $answer['id'] }}', 'answer')"
                                                                                class="px-3 py-1.5 text-sm font-medium text-gray-600 dark:text-gray-300 hover:text-gray-800 dark:hover:text-gray-100 transition-colors">
                                                                                Cancel
                                                                            </button>
                                                                            <button type="submit"
                                                                                class="px-3 py-1.5 text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 rounded-md transition-colors">
                                                                                Post Reply
                                                                            </button>
                                                                        </div>
                                                                    </form>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        @endif

                                        <!-- Reply Form for Question (Hidden by default) -->
                                        <div id="comment-box-desktop-{{ $question['id'] }}"
                                            class="hidden mt-4 pt-4 border-t border-gray-200 dark:border-gray-600">
                                            <form onsubmit="submitReply(event, {{ $question['id'] }}, 'question')">
                                                @csrf
                                                <textarea name="content" rows="3"
                                                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 placeholder-gray-500 dark:placeholder-gray-400"
                                                    placeholder="Write your answer..."></textarea>
                                                <div class="mt-2 flex justify-end space-x-2">
                                                    <button type="button"
                                                        onclick="toggleCommentBox('desktop-{{ $question['id'] }}', 'question')"
                                                        class="px-3 py-1.5 text-sm font-medium text-gray-600 dark:text-gray-300 hover:text-gray-800 dark:hover:text-gray-100 transition-colors">
                                                        Cancel
                                                    </button>
                                                    <button type="submit"
                                                        class="px-3 py-1.5 text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 rounded-md transition-colors">
                                                        Post Answer
                                                    </button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                @empty
                                    <div
                                        class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-12 text-center">
                                        <div
                                            class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-blue-100 dark:bg-blue-900/30 mb-4">
                                            <svg class="h-8 w-8 text-blue-600 dark:text-blue-400" fill="none"
                                                stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                                    d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                        </div>
                                        <h3 class="text-xl font-medium text-gray-900 dark:text-white mb-2">
                                            {{ __('forum.no_questions_title') }}</h3>
                                        <p class="text-gray-500 dark:text-gray-300 mb-6">{{ __('forum.no_questions') }}
                                        </p>
                                        <a href="{{ route('forum.create', ['locale' => app()->getLocale()]) }}"
                                            class="inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-xl shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-all duration-200">
                                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M12 4v16m8-8H4" />
                                            </svg>
                                            {{ __('forum.ask_question') }}
                                        </a>
                                    </div>
                                @endforelse
                            </div>

                            <!-- Pagination (Desktop) -->
                            @if ($questions->hasPages())
                                <div class="mt-8">
                                    {{ $questions->links() }}
                                </div>
                            @endif
                        </div>
                        
                        <!-- Mobile Leaderboard Content -->
                        <div class="lg:hidden" x-show="activeTab === 'leaderboard'"
                            x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0"
                            x-transition:enter-end="opacity-100">
                            @include('forum.partials.leaderboard-mobile')
                        </div>
                        
                        <!-- Desktop Leaderboard Content -->
                        <div class="hidden lg:block" x-show="activeTab === 'leaderboard'"
                            x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0"
                            x-transition:enter-end="opacity-100">
                            @include('forum.partials.leaderboard-desktop')
                        </div>
            </div>
        </div>
    </div>

    <script>
        function forumTabs() {
            return {
                activeTab: 'discussions',
                init() {
                    // Set initial tab based on URL parameter
                    const urlParams = new URLSearchParams(window.location.search);
                    const see = urlParams.get('see');
                    this.activeTab = see === 'leaderboard' ? 'leaderboard' : 'discussions';
                },
                handleTabChange(tab) {
                    this.activeTab = tab;

                    // Update URL parameter
                    const url = new URL(window.location);
                    if (tab === 'leaderboard') {
                        url.searchParams.set('see', 'leaderboard');
                    } else {
                        url.searchParams.delete('see');
                    }

                    // Update browser history without page reload
                    window.history.pushState({}, '', url);
                }
            }
        }
    </script>
@endsection

@push('scripts')
    <script>
        // Search functionality
        function initializeSearch() {
            const mobileSearch = document.getElementById('mobile-search');
            const desktopSearch = document.getElementById('desktop-search');

            function performSearch(searchTerm) {
                const url = new URL(window.location);
                if (searchTerm.trim()) {
                    url.searchParams.set('search', searchTerm);
                } else {
                    url.searchParams.delete('search');
                }
                window.location.href = url.toString();
            }

            // Add event listeners for search with debounce
            let searchTimeout;

            function handleSearch(event) {
                clearTimeout(searchTimeout);
                searchTimeout = setTimeout(() => {
                    performSearch(event.target.value);
                }, 300); // 300ms debounce
            }

            if (mobileSearch) {
                mobileSearch.addEventListener('input', handleSearch);
                // Set initial value if search parameter exists
                const urlParams = new URLSearchParams(window.location.search);
                if (urlParams.get('search')) {
                    mobileSearch.value = urlParams.get('search');
                }
            }

            if (desktopSearch) {
                desktopSearch.addEventListener('input', handleSearch);
                // Set initial value if search parameter exists
                const urlParams = new URLSearchParams(window.location.search);
                if (urlParams.get('search')) {
                    desktopSearch.value = urlParams.get('search');
                }
            }
        }

        // Initialize search when DOM is ready
        document.addEventListener('DOMContentLoaded', initializeSearch);

        // Voting functionality
        function vote(type, id, direction) {
            // Placeholder for voting functionality
            console.log(`Voting ${direction} on ${type} ${id}`);

            // Update UI temporarily
            const scoreElement = document.getElementById(`${type}-score-${id}`);
            if (scoreElement) {
                const currentScore = parseInt(scoreElement.textContent);
                scoreElement.textContent = direction === 'up' ? currentScore + 1 : currentScore - 1;
            }

            // Here you would make an API call to update the vote in the database
            // fetch('/api/vote', { method: 'POST', body: JSON.stringify({ type, id, direction }) })
        }

        // Toggle comment box - UPDATED VERSION
        function toggleCommentBox(id, parentType) {
            // Handle both mobile and desktop IDs
            const commentBox = document.getElementById(`comment-box-${id}`);
            if (commentBox) {
                commentBox.classList.toggle('hidden');
                if (!commentBox.classList.contains('hidden')) {
                    // Focus on textarea when opened
                    const textarea = commentBox.querySelector('textarea');
                    if (textarea) {
                        textarea.focus();
                    }
                }
            }
        }

        // Submit reply
        async function submitReply(event, parentId, parentType) {
            event.preventDefault();

            const form = event.target;
            const formData = new FormData(form);
            const content = formData.get('content')?.trim();

            if (!content) {
                alert('Please write a reply before submitting.');
                return;
            }

            // Check minimum length requirement
            if (content.length < 10) {
                alert('Reply must be at least 10 characters long.');
                return;
            }

            try {
                // Convert parentId to string and remove desktop- prefix if present
                const cleanParentId = String(parentId).replace('desktop-', '');

                // Determine the correct endpoint based on parent type
                const locale = '{{ app()->getLocale() }}';
                const endpoint = parentType === 'question' ?
                    `/${locale}/forum/${cleanParentId}/answers` :
                    `/${locale}/forum/${getQuestionIdFromAnswer(cleanParentId)}/answers`;

                // Prepare the data for submission with only current locale
                const data = {
                    content: {
                        '{{ app()->getLocale() }}': content
                    }
                    // Remove parent_id - backend should handle this from URL structure
                };

                const response = await fetch(endpoint, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': formData.get('_token'),
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify(data)
                });

                if (!response.ok) {
                    const errorData = await response.json().catch(() => ({}));
                    console.error('Server response:', errorData);
                    throw new Error(errorData.message || `Failed to submit reply (${response.status})`);
                }

                const result = await response.json();

                // Clear and close the comment box
                form.reset();
                toggleCommentBox(parentId, parentType);

                // Show success message
                showSuccessMessage('Your reply has been posted successfully!');

                // Optionally refresh the page to show the new reply
                setTimeout(() => {
                    window.location.reload();
                }, 1000);

            } catch (error) {
                console.error('Error submitting reply:', error);
                alert('Failed to submit reply: ' + error.message);
            }
        }

        // Helper function to get question ID from answer ID
        function getQuestionIdFromAnswer(answerId) {
            // Convert to string and remove desktop- prefix if present
            const cleanAnswerId = String(answerId).replace('desktop-', '');

            // Find the answer element by its data-answer-id attribute
            const answerElement = document.querySelector(`[data-answer-id="${cleanAnswerId}"]`);
            if (answerElement) {
                return answerElement.dataset.questionId;
            }

            // Fallback: find the closest question container
            const questionContainer = answerElement?.closest('[data-question-id]');
            return questionContainer?.dataset.questionId || window.currentQuestionId;
        }

        // Show success message
        function showSuccessMessage(message) {
            const toast = document.createElement('div');
            toast.className =
                'fixed top-4 right-4 bg-green-500 text-white px-6 py-3 rounded-lg shadow-lg z-50 transition-opacity duration-300';
            toast.textContent = message;
            document.body.appendChild(toast);

            setTimeout(() => {
                toast.style.opacity = '0';
                setTimeout(() => toast.remove(), 300);
            }, 3000);
        }

        // Toggle answers visibility
        function toggleAnswers(questionId) {
            const answersSection = document.getElementById(`answers-${questionId}`);
            if (answersSection) {
                answersSection.classList.toggle('hidden');
            }
        }
    </script>
@endpush
