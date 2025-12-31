@extends('layouts.app')

@section('content')
    <div class="bg-gray-50 dark:bg-gray-900 min-h-screen transition-colors duration-300">
        <!-- Main Content Container -->
        <div class="max-w-5xl mx-auto px-3 sm:px-4 lg:px-6 py-4 sm:py-6">

            <!-- Header Section -->
            <div
                class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-4 sm:p-6 mb-4">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-4">
                    <div>
                        <h1 class="text-2xl sm:text-3xl font-bold text-gray-900 dark:text-white mb-2">
                            {{ __('forum.page_title') }}
                        </h1>
                        <p class="text-gray-600 dark:text-gray-400">
                            {{ __('forum.discussions_description') }}
                        </p>
                    </div>
                    <button onclick="toggleAskQuestionForm()"
                        class="inline-flex items-center justify-center space-x-2 px-4 sm:px-6 py-2.5 sm:py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors duration-200 font-medium whitespace-nowrap">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        <span>{{ __('forum.ask_question') }}</span>
                    </button>
                </div>

                <!-- Ask Question Form (Hidden by default) -->
                <div id="ask-question-form" class="hidden mt-6 p-4 bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700">
                    <form onsubmit="submitNewQuestion(event)" class="space-y-3">
                        @csrf
                        <textarea name="content" rows="4" placeholder="{{ __('forum.whats_your_question') }}"
                            class="w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg 
                                   focus:ring-2 focus:ring-blue-500 focus:border-transparent 
                                   bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 
                                   placeholder-gray-500 dark:placeholder-gray-400 resize-none"
                            required minlength="10" maxlength="255"></textarea>
                        <div class="flex justify-end gap-2">
                            <button type="button" onclick="toggleAskQuestionForm()"
                                class="px-3 py-1.5 text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-md transition-colors">
                                {{ __('forum.cancel') }}
                            </button>
                            <button type="submit"
                                class="px-3 py-1.5 text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 rounded-md transition-colors">
                                {{ __('forum.post_question') }}
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Search Bar and Leaderboard -->
                <div class="flex items-center gap-2 sm:gap-3">
                    <div class="relative flex-1">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                        </div>
                        <input type="text" id="search-discussions" placeholder="{{ __('forum.search_discussions') }}"
                            class="w-full pl-10 pr-4 py-2.5 border border-gray-200 dark:border-gray-600 rounded-lg 
                                  focus:ring-2 focus:ring-blue-500 focus:border-transparent 
                                  bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 
                                  placeholder-gray-500 dark:placeholder-gray-400 transition-all duration-200">
                    </div>
                    <a href="{{ route('leaderboard', ['locale' => app()->getLocale()]) }}"
                        class="flex items-center space-x-1.5 sm:space-x-2 px-3 sm:px-4 py-2.5 bg-orange-500 hover:bg-orange-600 text-white rounded-lg transition-colors duration-200 whitespace-nowrap">
                        <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                        </svg>
                        <span class="inline font-medium">{{ __('forum.leaderboard') }}</span>
                    </a>
                </div>
            </div>

            <!-- Discussions List -->
            <div id="discussions-container" class="space-y-3 sm:space-y-4">
                @forelse($questions as $question)
                    <div class="discussion-item bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 hover:shadow-md transition-all duration-200"
                        data-question-id="{{ $question['id'] }}">

                        <!-- Question Container -->
                        <div class="flex gap-2 sm:gap-3 p-3 sm:p-4">
                            <!-- Voting Column -->
                            <div class="flex flex-col items-center gap-1 flex-shrink-0">
                                <button onclick="handleVote('question', {{ $question['id'] }}, 'up')"
                                    data-vote-type="question" data-vote-id="{{ $question['id'] }}" data-vote-direction="up"
                                    class="vote-btn group p-1 hover:bg-gray-100 dark:hover:bg-gray-700 rounded transition-colors">
                                    <svg class="w-5 h-5 sm:w-6 sm:h-6 text-gray-400 group-hover:text-orange-500 transition-colors"
                                        fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd"
                                            d="M3.293 9.707a1 1 0 010-1.414l6-6a1 1 0 011.414 0l6 6a1 1 0 01-1.414 1.414L11 5.414V17a1 1 0 11-2 0V5.414L4.707 9.707a1 1 0 01-1.414 0z"
                                            clip-rule="evenodd" />
                                    </svg>
                                </button>
                                <span
                                    class="vote-score text-sm sm:text-base font-bold text-gray-700 dark:text-gray-300 min-w-[2rem] text-center"
                                    data-score-id="{{ $question['id'] }}" data-score-type="question">
                                    {{ $question['votes'] ?? 0 }}
                                </span>
                                <button onclick="handleVote('question', {{ $question['id'] }}, 'down')"
                                    data-vote-type="question" data-vote-id="{{ $question['id'] }}"
                                    data-vote-direction="down"
                                    class="vote-btn group p-1 hover:bg-gray-100 dark:hover:bg-gray-700 rounded transition-colors">
                                    <svg class="w-5 h-5 sm:w-6 sm:h-6 text-gray-400 group-hover:text-blue-500 transition-colors"
                                        fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd"
                                            d="M16.707 10.293a1 1 0 010 1.414l-6 6a1 1 0 01-1.414 0l-6-6a1 1 0 111.414-1.414L9 14.586V3a1 1 0 012 0v11.586l4.293-4.293a1 1 0 011.414 0z"
                                            clip-rule="evenodd" />
                                    </svg>
                                </button>
                            </div>

                            <!-- Content Column -->
                            <div class="flex-1 min-w-0">
                                <!-- Question Header -->
                                <div class="flex items-start justify-between gap-2 mb-2">
                                    <div class="flex-1 min-w-0">
                                        <h2
                                            class="text-base sm:text-lg font-semibold text-gray-900 dark:text-white mb-1.5 leading-tight">
                                            {{ $question['title'] }}
                                        </h2>
                                        <div
                                            class="flex items-center flex-wrap gap-x-2 gap-y-1 text-xs text-gray-500 dark:text-gray-400">
                                            <div class="flex items-center gap-1.5">
                                                <div
                                                    class="w-5 h-5 sm:w-6 sm:h-6 rounded-full bg-gradient-to-br from-blue-500 to-blue-600 flex items-center justify-center text-white text-xs font-bold">
                                                    {{ substr($question['author']['firstName'], 0, 1) }}{{ substr($question['author']['lastName'], 0, 1) }}
                                                </div>
                                                <span class="font-medium text-gray-700 dark:text-gray-300">
                                                    {{ $question['author']['firstName'] }}
                                                    {{ $question['author']['lastName'] }}
                                                </span>
                                            </div>
                                            <span>•</span>
                                            <span>{{ timeDiffForHumans($question['createdAt']) }}</span>
                                        </div>
                                    </div>

                                    <!-- Actions Dropdown -->
                                    @if (Auth::check() && Auth::id() === ($question['user_id'] ?? null))
                                        <div class="relative" data-dropdown="question-{{ $question['id'] }}">
                                            <button onclick="toggleDropdown('question-{{ $question['id'] }}')"
                                                class="p-1.5 hover:bg-gray-100 dark:hover:bg-gray-700 rounded transition-colors">
                                                <svg class="w-5 h-5 text-gray-500 dark:text-gray-400" fill="currentColor"
                                                    viewBox="0 0 20 20">
                                                    <path
                                                        d="M10 6a2 2 0 110-4 2 2 0 010 4zM10 12a2 2 0 110-4 2 2 0 010 4zM10 18a2 2 0 110-4 2 2 0 010 4z" />
                                                </svg>
                                            </button>
                                            <div
                                                class="dropdown-menu hidden absolute mt-1 bg-white dark:bg-gray-800 rounded-lg shadow-lg border border-gray-200 dark:border-gray-700 z-50 py-1  w-auto right-0 min-w-[180px]">
                                                <button onclick="editQuestion({{ $question['id'] }})"
                                                    class="w-full text-left px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 flex items-center gap-2 transition-colors">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                    </svg>
                                                    <span>Edit</span>
                                                </button>
                                                <button onclick="deleteQuestion({{ $question['id'] }})"
                                                    class="w-full text-left px-4 py-2 text-sm text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20 flex items-center gap-2 transition-colors">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                    </svg>
                                                    <span>Delete</span>
                                                </button>
                                            </div>
                                        </div>
                                    @endif
                                </div>

                                <!-- Action Bar -->
                                <div class="flex items-center gap-3 sm:gap-4 text-xs sm:text-sm">
                                    <button onclick="toggleReplyBox('question-{{ $question['id'] }}')"
                                        class="flex items-center gap-1.5 text-blue-600 dark:text-blue-400 hover:text-blue-700 dark:hover:text-blue-300 font-medium transition-colors">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                                        </svg>
                                        <span>{{ __('forum.reply') }}</span>
                                    </button>
                                    @if (count($question['answers']) > 0)
                                        <button onclick="toggleAnswers('question-{{ $question['id'] }}')"
                                            class="flex items-center gap-1.5 text-gray-600 dark:text-gray-400 hover:text-blue-600 dark:hover:text-blue-400 font-medium transition-colors">
                                            <svg class="w-4 h-4 answers-toggle-icon transition-transform" fill="none"
                                                stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M19 9l-7 7-7-7" />
                                            </svg>
                                            <span>{{ count($question['answers']) }}
                                                {{ count($question['answers']) == 1 ? __('forum.reply') : __('forum.replies') }}</span>
                                        </button>
                                    @endif
                                </div>

                                <!-- Reply Form (Hidden by default) -->
                                <div id="reply-box-question-{{ $question['id'] }}"
                                    class="hidden mt-4 pt-4 border-t border-gray-200 dark:border-gray-600">
                                    <form onsubmit="submitReply(event, {{ $question['id'] }}, 'question')"
                                        class="space-y-2">
                                        @csrf
                                        <textarea name="content" rows="3" placeholder="{{ __('forum.write_your_answer') }}..."
                                            class="w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg 
                                                   focus:ring-2 focus:ring-blue-500 focus:border-transparent 
                                                   bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 
                                                   placeholder-gray-500 dark:placeholder-gray-400 resize-none"
                                            required></textarea>
                                        <div class="flex justify-end gap-2">
                                            <button type="button"
                                                onclick="toggleReplyBox('question-{{ $question['id'] }}')"
                                                class="px-3 py-1.5 text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-md transition-colors">
                                                {{ __('forum.cancel') }}
                                            </button>
                                            <button type="submit"
                                                class="px-3 py-1.5 text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 rounded-md transition-colors">
                                                {{ __('forum.post_answer') }}
                                            </button>
                                        </div>
                                    </form>
                                </div>

                                <!-- Answers Section -->
                                @if(count($question['answers']) > 0)
                                    <div id="answers-question-{{ $question['id'] }}" class="hidden mt-4 space-y-3">
                                        @foreach($question['answers'] as $answer)
                                            <div class="answer-item border-l-2 border-gray-300 dark:border-gray-600 pl-3 sm:pl-4" 
                                                data-answer-id="{{ $answer['id'] }}"
                                                data-question-id="{{ $question['id'] }}">
                                                
                                                <div class="flex gap-2 sm:gap-3">
                                                    <!-- Answer Voting Column -->
                                                    <div class="flex flex-col items-center gap-1 flex-shrink-0">
                                                        <button onclick="handleVote('answer', {{ $answer['id'] }}, 'up')"
                                                            data-vote-type="answer"
                                                            data-vote-id="{{ $answer['id'] }}"
                                                            data-vote-direction="up"
                                                            class="vote-btn group p-1 hover:bg-gray-100 dark:hover:bg-gray-700 rounded transition-colors">
                                                            <svg class="w-4 h-4 sm:w-5 sm:h-5 text-gray-400 group-hover:text-orange-500 transition-colors" 
                                                                fill="currentColor" viewBox="0 0 20 20">
                                                                <path fill-rule="evenodd"
                                                                    d="M3.293 9.707a1 1 0 010-1.414l6-6a1 1 0 011.414 0l6 6a1 1 0 01-1.414 1.414L11 5.414V17a1 1 0 11-2 0V5.414L4.707 9.707a1 1 0 01-1.414 0z"
                                                                    clip-rule="evenodd" />
                                                            </svg>
                                                        </button>
                                                        <span class="vote-score text-xs sm:text-sm font-bold text-gray-700 dark:text-gray-300 min-w-[1.5rem] text-center"
                                                            data-score-id="{{ $answer['id'] }}"
                                                            data-score-type="answer">
                                                            {{ $answer['votes'] ?? 0 }}
                                                        </span>
                                                        <button onclick="handleVote('answer', {{ $answer['id'] }}, 'down')"
                                                            data-vote-type="answer"
                                                            data-vote-id="{{ $answer['id'] }}"
                                                            data-vote-direction="down"
                                                            class="vote-btn group p-1 hover:bg-gray-100 dark:hover:bg-gray-700 rounded transition-colors">
                                                            <svg class="w-4 h-4 sm:w-5 sm:h-5 text-gray-400 group-hover:text-blue-500 transition-colors" 
                                                                fill="currentColor" viewBox="0 0 20 20">
                                                                <path fill-rule="evenodd"
                                                                    d="M16.707 10.293a1 1 0 010 1.414l-6 6a1 1 0 01-1.414 0l-6-6a1 1 0 111.414-1.414L9 14.586V3a1 1 0 012 0v11.586l4.293-4.293a1 1 0 011.414 0z"
                                                                    clip-rule="evenodd" />
                                                            </svg>
                                                        </button>
                                                    </div>

                                                    <!-- Answer Content Column -->
                                                    <div class="flex-1 min-w-0">
                                                        <!-- Answer Header -->
                                                        <div class="flex items-start justify-between gap-2 mb-2">
                                                            <div class="flex items-center flex-wrap gap-x-2 gap-y-1 text-xs text-gray-500 dark:text-gray-400">
                                                                <div class="flex items-center gap-1.5">
                                                                    <div class="w-5 h-5 rounded-full bg-gradient-to-br from-green-500 to-green-600 flex items-center justify-center text-white text-xs font-bold">
                                                                        {{ substr($answer['author']['firstName'], 0, 1) }}{{ substr($answer['author']['lastName'], 0, 1) }}
                                                                    </div>
                                                                    <span class="font-medium text-gray-700 dark:text-gray-300">
                                                                        {{ $answer['author']['firstName'] }} {{ $answer['author']['lastName'] }}
                                                                    </span>
                                                                </div>
                                                                <span>•</span>
                                                                <span>{{ timeDiffForHumans($answer['createdAt']) }}</span>
                                                            </div>

                                                            <!-- Answer Actions Dropdown -->
                                                            @if(Auth::check() && Auth::id() === ($answer['user_id'] ?? null))
                                                                <div class="relative" data-dropdown="answer-{{ $answer['id'] }}">
                                                                    <button onclick="toggleDropdown('answer-{{ $answer['id'] }}')"
                                                                        class="p-1 hover:bg-gray-100 dark:hover:bg-gray-700 rounded transition-colors">
                                                                        <svg class="w-4 h-4 text-gray-500 dark:text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                                                            <path d="M10 6a2 2 0 110-4 2 2 0 010 4zM10 12a2 2 0 110-4 2 2 0 010 4zM10 18a2 2 0 110-4 2 2 0 010 4z" />
                                                                        </svg>
                                                                    </button>
                                                                    <div class="dropdown-menu hidden absolute right-0 mt-1 w-auto min-w-[80px] bg-white dark:bg-gray-800 rounded-lg shadow-lg border border-gray-200 dark:border-gray-700 z-50 py-1">
                                                                        <button onclick="editAnswer({{ $answer['id'] }})"
                                                                            class="w-full text-left px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 flex items-center gap-2 transition-colors">
                                                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                                                    d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                                            </svg>
                                                                            <span>Edit</span>
                                                                        </button>
                                                                        <button onclick="deleteAnswer({{ $answer['id'] }})"
                                                                            class="w-full text-left px-4 py-2 text-sm text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20 flex items-center gap-2 transition-colors">
                                                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                                                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                                            </svg>
                                                                            <span>Delete</span>
                                                                        </button>
                                                                    </div>
                                                                </div>
                                                            @endif
                                                        </div>

                                                        <!-- Answer Content -->
                                                        <div class="prose prose-sm dark:prose-invert max-w-none mb-2">
                                                            <p class="text-gray-700 dark:text-gray-300 text-sm leading-relaxed whitespace-pre-wrap">{{ strip_tags($answer['content']) }}</p>
                                                        </div>

                                                        <!-- Answer Action Bar -->
                                                        <div class="flex items-center gap-3 text-xs">
                                                            <button onclick="toggleReplyBox('answer-{{ $answer['id'] }}')"
                                                                class="flex items-center gap-1.5 text-gray-600 dark:text-gray-400 hover:text-blue-600 dark:hover:text-blue-400 font-medium transition-colors">
                                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                                        d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6" />
                                                                </svg>
                                                                <span>{{ __('forum.reply') }}</span>
                                                            </button>
                                                        </div>

                                                        <!-- Reply Form for Answer -->
                                                        <div id="reply-box-answer-{{ $answer['id'] }}" class="hidden mt-3">
                                                            <form onsubmit="submitReply(event, {{ $answer['id'] }}, 'answer')" class="space-y-2">
                                                                @csrf
                                                                <textarea name="content" rows="2"
                                                                    placeholder="{{ __('forum.write_your_reply') }}..."
                                                                    class="w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg 
                                                                           focus:ring-2 focus:ring-blue-500 focus:border-transparent 
                                                                           bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 
                                                                           placeholder-gray-500 dark:placeholder-gray-400 resize-none"
                                                                    required></textarea>
                                                                <div class="flex justify-end gap-2">
                                                                    <button type="button"
                                                                        onclick="toggleReplyBox('answer-{{ $answer['id'] }}')"
                                                                        class="px-3 py-1.5 text-xs font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-md transition-colors">
                                                                        {{ __('forum.cancel') }}
                                                                    </button>
                                                                    <button type="submit"
                                                                        class="px-3 py-1.5 text-xs font-medium text-white bg-blue-600 hover:bg-blue-700 rounded-md transition-colors">
                                                                        {{ __('forum.post_reply') }}
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
                            </div>
                        </div>
                    </div>
                @empty
                    <div
                        class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-8 sm:p-12 text-center">
                        <div
                            class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-blue-100 dark:bg-blue-900/30 mb-4">
                            <svg class="h-8 w-8 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-2">
                            {{ __('forum.no_questions_title') }}
                        </h3>
                        <p class="text-gray-600 dark:text-gray-400 mb-6">
                            {{ __('forum.no_questions') }}
                        </p>
                        <button onclick="toggleAskQuestionForm()"
                            class="inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-lg shadow-sm text-white bg-blue-600 hover:bg-blue-700 transition-all duration-200">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 4v16m8-8H4" />
                            </svg>
                            {{ __('forum.ask_question') }}
                        </button>
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
    </div>

    <!-- Toast Notification Container -->
    <div id="toast-container" class="fixed top-4 right-4 z-50 space-y-2"></div>

@endsection

@push('scripts')
    <script>
        // Global state
        const APP_LOCALE = '{{ app()->getLocale() }}';
        const CSRF_TOKEN = '{{ csrf_token() }}';

        // Utility Functions
        function showToast(message, type = 'success') {
            const container = document.getElementById('toast-container');
            const toast = document.createElement('div');
            const bgColor = type === 'success' ? 'bg-green-500' : type === 'error' ? 'bg-red-500' : 'bg-blue-500';

            toast.className =
                `${bgColor} text-white px-6 py-3 rounded-lg shadow-lg transform transition-all duration-300 ease-in-out opacity-0 translate-x-4`;
            toast.textContent = message;

            container.appendChild(toast);

            // Trigger animation
            setTimeout(() => {
                toast.classList.remove('opacity-0', 'translate-x-4');
            }, 10);

            // Remove toast
            setTimeout(() => {
                toast.classList.add('opacity-0', 'translate-x-4');
                setTimeout(() => toast.remove(), 300);
            }, 3000);
        }

        function debounce(func, wait) {
            let timeout;
            return function executedFunction(...args) {
                const later = () => {
                    clearTimeout(timeout);
                    func(...args);
                };
                clearTimeout(timeout);
                timeout = setTimeout(later, wait);
            };
        }

        // Toggle Functions
        function toggleDropdown(id) {
            const dropdown = document.querySelector(`[data-dropdown="${id}"] .dropdown-menu`);
            if (!dropdown) return;

            // Close all other dropdowns
            document.querySelectorAll('.dropdown-menu').forEach(menu => {
                if (menu !== dropdown) menu.classList.add('hidden');
            });

            dropdown.classList.toggle('hidden');
        }

        function toggleReplyBox(id) {
            const replyBox = document.getElementById(`reply-box-${id}`);
            if (!replyBox) return;

            replyBox.classList.toggle('hidden');

            if (!replyBox.classList.contains('hidden')) {
                const textarea = replyBox.querySelector('textarea');
                if (textarea) {
                    textarea.focus();
                    // Auto-resize textarea
                    textarea.style.height = 'auto';
                    textarea.style.height = textarea.scrollHeight + 'px';
                }
            }
        }

        function toggleAnswers(id) {
            const answersSection = document.getElementById(`answers-${id}`);
            const toggleIcon = document.querySelector(`button[onclick="toggleAnswers('${id}')"] .answers-toggle-icon`);

            if (!answersSection) return;

            answersSection.classList.toggle('hidden');

            if (toggleIcon) {
                if (answersSection.classList.contains('hidden')) {
                    toggleIcon.style.transform = 'rotate(0deg)';
                } else {
                    toggleIcon.style.transform = 'rotate(180deg)';
                }
            }
        }

        // Voting System
        async function handleVote(type, id, direction) {
            const scoreElement = document.querySelector(`[data-score-id="${id}"][data-score-type="${type}"]`);
            const upButton = document.querySelector(
                `[data-vote-id="${id}"][data-vote-type="${type}"][data-vote-direction="up"]`);
            const downButton = document.querySelector(
                `[data-vote-id="${id}"][data-vote-type="${type}"][data-vote-direction="down"]`);

            if (!scoreElement || !upButton || !downButton) return;

            // Store original state
            const originalScore = parseInt(scoreElement.textContent);
            const wasUpvoted = upButton.querySelector('svg').classList.contains('text-orange-500');
            const wasDownvoted = downButton.querySelector('svg').classList.contains('text-blue-500');

            // Optimistic UI update
            let scoreChange = 0;

            if (direction === 'up') {
                if (wasUpvoted) {
                    scoreChange = -1;
                    upButton.querySelector('svg').classList.remove('text-orange-500');
                    upButton.querySelector('svg').classList.add('text-gray-400');
                } else {
                    scoreChange = wasDownvoted ? 2 : 1;
                    upButton.querySelector('svg').classList.remove('text-gray-400');
                    upButton.querySelector('svg').classList.add('text-orange-500');
                    if (wasDownvoted) {
                        downButton.querySelector('svg').classList.remove('text-blue-500');
                        downButton.querySelector('svg').classList.add('text-gray-400');
                    }
                }
            } else {
                if (wasDownvoted) {
                    scoreChange = 1;
                    downButton.querySelector('svg').classList.remove('text-blue-500');
                    downButton.querySelector('svg').classList.add('text-gray-400');
                } else {
                    scoreChange = wasUpvoted ? -2 : -1;
                    downButton.querySelector('svg').classList.remove('text-gray-400');
                    downButton.querySelector('svg').classList.add('text-blue-500');
                    if (wasUpvoted) {
                        upButton.querySelector('svg').classList.remove('text-orange-500');
                        upButton.querySelector('svg').classList.add('text-gray-400');
                    }
                }
            }

            // Update score with animation
            const newScore = originalScore + scoreChange;
            scoreElement.textContent = newScore;
            scoreElement.style.transform = 'scale(1.3)';
            setTimeout(() => {
                scoreElement.style.transform = 'scale(1)';
            }, 200);

            try {
                const response = await fetch(`/${APP_LOCALE}/forum/${type}/${id}/vote`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': CSRF_TOKEN,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        vote: direction
                    })
                });

                const data = await response.json();

                if (!data.success) {
                    throw new Error(data.message || 'Vote failed');
                }

                // Update with server-confirmed score
                scoreElement.textContent = data.votes;

            } catch (error) {
                console.error('Voting error:', error);
                // Rollback on error
                scoreElement.textContent = originalScore;
                if (wasUpvoted) {
                    upButton.querySelector('svg').classList.add('text-orange-500');
                    upButton.querySelector('svg').classList.remove('text-gray-400');
                }
                if (wasDownvoted) {
                    downButton.querySelector('svg').classList.add('text-blue-500');
                    downButton.querySelector('svg').classList.remove('text-gray-400');
                }
                showToast('Failed to vote. Please try again.', 'error');
            }
        }

        // Submit Reply
        async function submitReply(event, parentId, parentType) {
            event.preventDefault();
            
            console.log('submitReply called', { parentId, parentType });

            const form = event.target;
            const formData = new FormData(form);
            const content = formData.get('content')?.trim();

            console.log('Form data', { content, contentLength: content?.length });

            if (!content || content.length < 10) {
                showToast('Reply must be at least 10 characters long.', 'error');
                return;
            }

            const submitButton = form.querySelector('button[type="submit"]');
            const originalButtonText = submitButton.textContent;
            submitButton.disabled = true;
            submitButton.textContent = 'Posting...';

            try {
                const questionId = parentType === 'question' ? parentId :
                    document.querySelector(`[data-answer-id="${parentId}"]`)?.dataset.questionId;

                console.log('Question ID found', { questionId, parentType, parentId });

                if (!questionId) throw new Error('Question ID not found');

                const url = `/${APP_LOCALE}/forum/${questionId}/answers`;
                console.log('Making request to', url);

                const response = await fetch(url, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': CSRF_TOKEN,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        content: {
                            [APP_LOCALE]: content
                        },
                        parent_id: parentType === 'answer' ? parentId : null
                    })
                });

                console.log('Response status', response.status);
                const data = await response.json();
                console.log('Response data', data);

                if (!data.success) {
                    throw new Error(data.message || 'Failed to post reply');
                }

                // Clear form and close reply box
                form.reset();
                toggleReplyBox(`${parentType}-${parentId}`);

                showToast('Reply posted successfully!', 'success');

                // Optimistically add the new reply to the DOM
                if (data.answer) {
                    addReplyToDOM(data.answer, parentType, parentId);
                } else {
                    // Fallback: reload if no answer data returned
                    setTimeout(() => window.location.reload(), 1000);
                }

            } catch (error) {
                console.error('Reply submission error:', error);
                showToast(error.message || 'Failed to post reply', 'error');
            } finally {
                submitButton.disabled = false;
                submitButton.textContent = originalButtonText;
            }
        }

        // Delete Question
        async function deleteQuestion(questionId) {
            if (!confirm('Are you sure you want to delete this question? This will also delete all answers.')) {
                return;
            }

            try {
                const response = await fetch(`/${APP_LOCALE}/forum/questions/${questionId}`, {
                    method: 'DELETE',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': CSRF_TOKEN,
                        'Accept': 'application/json'
                    }
                });

                const data = await response.json();

                if (!data.success) {
                    throw new Error(data.message || 'Failed to delete question');
                }

                // Remove from DOM with animation
                const questionElement = document.querySelector(`[data-question-id="${questionId}"]`);
                if (questionElement) {
                    questionElement.style.opacity = '0';
                    questionElement.style.transform = 'scale(0.95)';
                    setTimeout(() => questionElement.remove(), 300);
                }

                showToast('Question deleted successfully!', 'success');

            } catch (error) {
                console.error('Delete error:', error);
                showToast(error.message || 'Failed to delete question', 'error');
            }
        }

        // Delete Answer
        async function deleteAnswer(answerId) {
            if (!confirm('Are you sure you want to delete this answer?')) {
                return;
            }

            try {
                const response = await fetch(`/${APP_LOCALE}/forum/answers/${answerId}`, {
                    method: 'DELETE',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': CSRF_TOKEN,
                        'Accept': 'application/json'
                    }
                });

                const data = await response.json();

                if (!data.success) {
                    throw new Error(data.message || 'Failed to delete answer');
                }

                // Remove from DOM with animation
                const answerElement = document.querySelector(`[data-answer-id="${answerId}"]`);
                if (answerElement) {
                    answerElement.style.opacity = '0';
                    answerElement.style.transform = 'translateX(-10px)';
                    setTimeout(() => answerElement.remove(), 300);
                }

                showToast('Answer deleted successfully!', 'success');

            } catch (error) {
                console.error('Delete error:', error);
                showToast(error.message || 'Failed to delete answer', 'error');
            }
        }

        // Add reply to DOM optimistically
        function addReplyToDOM(answer, parentType, parentId) {
            console.log('Adding reply to DOM', { answer, parentType, parentId });
            
            const answerHTML = createAnswerHTML(answer);
            
            if (parentType === 'question') {
                // Add to question's answers section
                const answersContainer = document.getElementById(`answers-question-${parentId}`);
                if (answersContainer) {
                    answersContainer.classList.remove('hidden');
                    const answersDiv = answersContainer.querySelector('.space-y-3') || answersContainer;
                    answersDiv.insertAdjacentHTML('beforeend', answerHTML);
                    
                    // Update reply count
                    updateReplyCount(parentId, 'increment');
                }
            } else if (parentType === 'answer') {
                // Add as nested reply under an answer
                const answerElement = document.querySelector(`[data-answer-id="${parentId}"]`);
                if (answerElement) {
                    // Find or create reply container for this answer
                    let replyContainer = answerElement.querySelector('#reply-box-' + parentId + ' + .reply-list');
                    if (!replyContainer) {
                        replyContainer = document.createElement('div');
                        replyContainer.className = 'reply-list mt-3 space-y-2';
                        answerElement.appendChild(replyContainer);
                    }
                    replyContainer.insertAdjacentHTML('beforeend', answerHTML);
                }
            }
        }

        // Create HTML for a new answer
        function createAnswerHTML(answer) {
            const initials = answer.author ? 
                (answer.author.firstName?.[0] || '') + (answer.author.lastName?.[0] || '') : 
                'A';
            const authorName = answer.author ? 
                `${answer.author.firstName} ${answer.author.lastName}` : 
                'Anonymous';
            
            // Handle content - it might be a JSON string or object
            let contentText = '';
            if (typeof answer.content === 'string') {
                try {
                    const contentObj = JSON.parse(answer.content);
                    contentText = contentObj[APP_LOCALE] || contentObj.rw || contentObj.en || Object.values(contentObj)[0] || '';
                } catch (e) {
                    contentText = answer.content;
                }
            } else if (typeof answer.content === 'object') {
                contentText = answer.content[APP_LOCALE] || answer.content.rw || answer.content.en || Object.values(answer.content)[0] || '';
            } else {
                contentText = answer.content || '';
            }
            
            return `
                <div class="answer-item border-l-2 border-gray-300 dark:border-gray-600 pl-3 sm:pl-4" 
                     data-answer-id="${answer.id}" data-question-id="${answer.question_id}">
                    <div class="flex gap-2 sm:gap-3">
                        <div class="flex flex-col items-center gap-1 flex-shrink-0">
                            <button onclick="handleVote('answer', ${answer.id}, 'up')"
                                data-vote-type="answer" data-vote-id="${answer.id}" data-vote-direction="up"
                                class="vote-btn group p-1 hover:bg-gray-100 dark:hover:bg-gray-700 rounded transition-colors">
                                <svg class="w-4 h-4 sm:w-5 sm:h-5 text-gray-400 group-hover:text-orange-500 transition-colors" 
                                     fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M3.293 9.707a1 1 0 010-1.414l6-6a1 1 0 011.414 0l6 6a1 1 0 01-1.414 1.414L11 5.414V17a1 1 0 11-2 0V5.414L4.707 9.707a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                                </svg>
                            </button>
                            <span class="vote-score text-xs sm:text-sm font-bold text-gray-700 dark:text-gray-300 min-w-[1.5rem] text-center"
                                  data-score-id="${answer.id}" data-score-type="answer">0</span>
                            <button onclick="handleVote('answer', ${answer.id}, 'down')"
                                data-vote-type="answer" data-vote-id="${answer.id}" data-vote-direction="down"
                                class="vote-btn group p-1 hover:bg-gray-100 dark:hover:bg-gray-700 rounded transition-colors">
                                <svg class="w-4 h-4 sm:w-5 sm:h-5 text-gray-400 group-hover:text-blue-500 transition-colors" 
                                     fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 10.293a1 1 0 010 1.414l-6 6a1 1 0 01-1.414 0l-6-6a1 1 0 111.414-1.414L9 14.586V3a1 1 0 012 0v11.586l4.293-4.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                </svg>
                            </button>
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="flex items-start justify-between gap-2 mb-2">
                                <div class="flex items-center flex-wrap gap-x-2 gap-y-1 text-xs text-gray-500 dark:text-gray-400">
                                    <div class="flex items-center gap-1.5">
                                        <div class="w-5 h-5 rounded-full bg-gradient-to-br from-green-500 to-green-600 flex items-center justify-center text-white text-xs font-bold">
                                            ${initials}
                                        </div>
                                        <span class="font-medium text-gray-700 dark:text-gray-300">${authorName}</span>
                                    </div>
                                    <span>•</span>
                                    <span>just now</span>
                                </div>
                            </div>
                            <div class="mb-2">
                                <p class="text-gray-700 dark:text-gray-300 text-sm leading-relaxed whitespace-pre-wrap">${contentText}</p>
                            </div>
                            <div class="flex items-center gap-3 text-xs">
                                <button onclick="toggleReplyBox('answer-${answer.id}')"
                                    class="flex items-center gap-1.5 text-blue-600 dark:text-blue-400 hover:text-blue-700 dark:hover:text-blue-300 font-medium transition-colors">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6" />
                                    </svg>
                                    <span>Reply</span>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            `;
        }

        // Update reply count
        function updateReplyCount(questionId, action) {
            const replyButton = document.querySelector(`button[onclick*="toggleAnswers('${questionId}')"] span`);
            if (replyButton) {
                const currentText = replyButton.textContent;
                const currentCount = parseInt(currentText) || 0;
                const newCount = action === 'increment' ? currentCount + 1 : Math.max(0, currentCount - 1);
                const replyText = newCount === 1 ? 'Reply' : 'Replies';
                replyButton.textContent = `${newCount} ${replyText}`;
            }
        }

        // Edit Functions
        async function editQuestion(questionId) {
            const questionElement = document.querySelector(`[data-question-id="${questionId}"]`);
            const contentElement = questionElement.querySelector('p');
            const currentContent = contentElement.textContent.trim();

            // Replace content with editable textarea
            const editForm = createEditForm(currentContent, 'question', questionId);
            contentElement.parentNode.replaceChild(editForm, contentElement);
            
            // Focus on textarea
            const textarea = editForm.querySelector('textarea');
            textarea.focus();
            textarea.setSelectionRange(textarea.value.length, textarea.value.length);
        }

        async function editAnswer(answerId) {
            const answerElement = document.querySelector(`[data-answer-id="${answerId}"]`);
            const contentElement = answerElement.querySelector('p');
            const currentContent = contentElement.textContent.trim();

            // Replace content with editable textarea
            const editForm = createEditForm(currentContent, 'answer', answerId);
            contentElement.parentNode.replaceChild(editForm, contentElement);
            
            // Focus on textarea
            const textarea = editForm.querySelector('textarea');
            textarea.focus();
            textarea.setSelectionRange(textarea.value.length, textarea.value.length);
        }

        function createEditForm(currentContent, type, id) {
            const form = document.createElement('div');
            form.className = 'edit-form space-y-3';
            form.innerHTML = `
                <textarea 
                    class="w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg 
                           focus:ring-2 focus:ring-blue-500 focus:border-transparent 
                           bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 
                           placeholder-gray-500 dark:placeholder-gray-400 resize-none"
                    rows="4"
                    placeholder="Edit your ${type}..."
                    minlength="10"
                    required>${currentContent}</textarea>
                <div class="flex justify-end gap-2">
                    <button type="button" onclick="cancelEdit('${type}', ${id})"
                        class="px-3 py-1.5 text-xs font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-md transition-colors">
                        Cancel
                    </button>
                    <button type="button" onclick="saveEdit('${type}', ${id})"
                        class="px-3 py-1.5 text-xs font-medium text-white bg-blue-600 hover:bg-blue-700 rounded-md transition-colors">
                        Save
                    </button>
                </div>
            `;
            return form;
        }

        function cancelEdit(type, id) {
            // Reload the page to restore original content
            window.location.reload();
        }

        async function saveEdit(type, id) {
            const editForm = document.querySelector('.edit-form');
            const textarea = editForm.querySelector('textarea');
            const newContent = textarea.value.trim();

            if (!newContent || newContent.length < 10) {
                showToast(`${type.charAt(0).toUpperCase() + type.slice(1)} must be at least 10 characters long.`, 'error');
                return;
            }

            // Disable buttons during save
            const saveBtn = editForm.querySelector('button[onclick*="saveEdit"]');
            const cancelBtn = editForm.querySelector('button[onclick*="cancelEdit"]');
            saveBtn.disabled = true;
            saveBtn.textContent = 'Saving...';
            cancelBtn.disabled = true;

            try {
                const url = type === 'question' 
                    ? `/${APP_LOCALE}/forum/questions/${id}`
                    : `/${APP_LOCALE}/forum/answers/${id}`;
                
                const body = type === 'question'
                    ? {
                        title: { [APP_LOCALE]: 'Question Title' }, // We'll need to get the actual title
                        content: { [APP_LOCALE]: newContent }
                    }
                    : {
                        content: { [APP_LOCALE]: newContent }
                    };

                const response = await fetch(url, {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': CSRF_TOKEN,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify(body)
                });

                const data = await response.json();

                if (!data.success) {
                    throw new Error(data.message || `Failed to update ${type}`);
                }

                // Replace edit form with updated content
                const contentDiv = document.createElement('div');
                contentDiv.innerHTML = `<p class="text-gray-700 dark:text-gray-300 text-sm leading-relaxed whitespace-pre-wrap">${newContent}</p>`;
                editForm.parentNode.replaceChild(contentDiv, editForm);

                showToast(`${type.charAt(0).toUpperCase() + type.slice(1)} updated successfully!`, 'success');

            } catch (error) {
                console.error(`Edit ${type} error:`, error);
                showToast(error.message || `Failed to update ${type}`, 'error');
                
                // Re-enable buttons on error
                saveBtn.disabled = false;
                saveBtn.textContent = 'Save';
                cancelBtn.disabled = false;
            }
        }

        // Toggle Ask Question Form
        function toggleAskQuestionForm() {
            const form = document.getElementById('ask-question-form');
            form.classList.toggle('hidden');
            
            // Focus on textarea when opening
            if (!form.classList.contains('hidden')) {
                const textarea = form.querySelector('textarea');
                setTimeout(() => textarea.focus(), 100);
            }
        }

        // Submit New Question
        async function submitNewQuestion(event) {
            event.preventDefault();

            const form = event.target;
            const formData = new FormData(form);
            const content = formData.get('content')?.trim();

            if (!content || content.length < 10) {
                showToast('Question must be at least 10 characters long.', 'error');
                return;
            }

            const submitButton = form.querySelector('button[type="submit"]');
            const originalButtonText = submitButton.textContent;
            submitButton.disabled = true;
            submitButton.textContent = 'Posting...';

            try {
                const response = await fetch(`/${APP_LOCALE}/forum`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': CSRF_TOKEN,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        content: {
                            [APP_LOCALE]: content
                        }
                    })
                });

                const data = await response.json();

                if (!data.success) {
                    throw new Error(data.message || 'Failed to post question');
                }

                // Clear form and close
                form.reset();
                toggleAskQuestionForm();

                showToast('Question posted successfully!', 'success');

                // Reload to show new question
                setTimeout(() => window.location.reload(), 1000);

            } catch (error) {
                console.error('Question submission error:', error);
                
                // Handle redirect for email verification
                if (error.message && error.message.includes('verify')) {
                    if (data.redirect) {
                        window.location.href = data.redirect;
                        return;
                    }
                }
                
                showToast(error.message || 'Failed to post question', 'error');
            } finally {
                submitButton.disabled = false;
                submitButton.textContent = originalButtonText;
            }
        }

        // Share Function
        function shareQuestion(questionId) {
            const url = `${window.location.origin}/${APP_LOCALE}/forum/${questionId}`;

            if (navigator.share) {
                navigator.share({
                    title: 'Check out this discussion',
                    url: url
                }).catch(() => {});
            } else {
                navigator.clipboard.writeText(url).then(() => {
                    showToast('Link copied to clipboard!', 'success');
                }).catch(() => {
                    showToast('Failed to copy link', 'error');
                });
            }
        }

        // Search Functionality
        const searchInput = document.getElementById('search-discussions');
        if (searchInput) {
            const handleSearch = debounce((searchTerm) => {
                const url = new URL(window.location);
                if (searchTerm.trim()) {
                    url.searchParams.set('search', searchTerm);
                } else {
                    url.searchParams.delete('search');
                }
                window.location.href = url.toString();
            }, 500);

            searchInput.addEventListener('input', (e) => handleSearch(e.target.value));

            // Set initial value
            const urlParams = new URLSearchParams(window.location.search);
            if (urlParams.get('search')) {
                searchInput.value = urlParams.get('search');
            }
        }

        // Close dropdowns when clicking outside
        document.addEventListener('click', (e) => {
            if (!e.target.closest('[data-dropdown]')) {
                document.querySelectorAll('.dropdown-menu').forEach(menu => {
                    menu.classList.add('hidden');
                });
            }
        });

        // Auto-resize textareas
        document.addEventListener('input', (e) => {
            if (e.target.tagName === 'TEXTAREA') {
                e.target.style.height = 'auto';
                e.target.style.height = e.target.scrollHeight + 'px';
            }
        });

        // Initialize: Show answers that are already open (based on URL hash or other logic)
        document.addEventListener('DOMContentLoaded', () => {
            console.log('Forum discussions loaded');
        });
    </script>
@endpush
