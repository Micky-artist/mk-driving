@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gradient-to-b from-gray-50 to-gray-100 dark:from-gray-900 dark:to-gray-800 transition-colors duration-300">
    <!-- Hero Section -->
    <div class="bg-gradient-to-r from-blue-600 to-blue-700 text-white py-4 shadow-lg">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <h1 class="text-4xl font-bold mb-3 fade-in">{{ __('forum.page_title') }}</h1>
            <p class="text-blue-100 text-lg max-w-3xl mx-auto fade-in delay-100">{{ __('forum.page_description') }}</p>
        </div>
    </div>

    <!-- Main Content -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="flex flex-col md:flex-row gap-8">
            <!-- Questions List -->
            <div class="w-full md:w-2/3">
                <!-- Filters -->
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-4 mb-8 transition-all duration-300 transform hover:shadow-lg">
                    <div class="flex flex-wrap items-center justify-between gap-4">
                        <div class="flex flex-wrap gap-2">
                            <button class="px-4 py-2 rounded-full text-sm font-medium transition-all duration-200
                                      bg-blue-100 dark:bg-blue-900/50 text-blue-800 dark:text-blue-100 border border-blue-200 dark:border-blue-800 hover:bg-blue-200 dark:hover:bg-blue-800/70">
                                {{ __('forum.all_questions') }}
                            </button>
                            <button class="px-4 py-2 rounded-full text-sm font-medium transition-all duration-200
                                      text-gray-700 dark:text-gray-300 border border-gray-200 dark:border-gray-700 hover:bg-gray-100 dark:hover:bg-gray-700/50">
                                {{ __('forum.answered') }}
                            </button>
                            <button class="px-4 py-2 rounded-full text-sm font-medium transition-all duration-200
                                      text-gray-700 dark:text-gray-300 border border-gray-200 dark:border-gray-700 hover:bg-gray-100 dark:hover:bg-gray-700/50">
                                {{ __('forum.unanswered') }}
                            </button>
                        </div>
                        <div class="relative w-full md:w-64">
                            <input type="text" 
                                   placeholder="{{ __('forum.search_placeholder') }}" 
                                   class="w-full pl-10 pr-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-xl 
                                          focus:ring-2 focus:ring-blue-500 focus:border-transparent 
                                          bg-white/80 dark:bg-gray-700/50 text-gray-900 dark:text-gray-100 
                                          placeholder-gray-500 dark:placeholder-gray-400 transition-all duration-200">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Questions -->
                <div class="space-y-6">
                    @forelse($questions as $question)
                        @include('forum.partials.question-card', ['question' => $question])
                    @empty
                        <div class="bg-white rounded-lg shadow p-6 text-center">
                            <p class="text-gray-500">{{ __('forum.no_questions') }}</p>
                            <a href="{{ route('forum.create', ['locale' => app()->getLocale()]) }}" 
                               class="mt-4 inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">
                                {{ __('forum.ask_question') }}
                            </a>
                        </div>
                    @endforelse
                </div>

                <!-- Pagination -->
                @if($questions->hasPages())
                <div class="mt-8">
                    {{ $questions->links() }}
                </div>
                @endif
            </div>

            <!-- Sidebar -->
            <div class="w-full md:w-1/3 space-y-6">
                <!-- Ask Question Button -->
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-6 transition-all duration-300 transform hover:shadow-lg">
                    <div class="flex items-center mb-4">
                        <div class="p-2 rounded-lg bg-blue-100 dark:bg-blue-900/50 text-blue-600 dark:text-blue-300 mr-3">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">{{ __('forum.need_help') }}</h3>
                    </div>
                    <p class="text-gray-600 dark:text-gray-300 mb-6">{{ __('forum.ask_community') }}</p>
                    <a href="{{ route('forum.create', ['locale' => app()->getLocale()]) }}" 
                       class="w-full flex items-center justify-center px-6 py-3 border border-transparent text-base font-medium rounded-xl shadow-sm text-white bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-all duration-200 transform hover:-translate-y-0.5">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        {{ __('forum.ask_question') }}
                    </a>
                </div>

                <!-- Categories -->
                <div class="bg-white dark:bg-gray-800/90 rounded-xl shadow-md p-6 transition-all duration-300 hover:shadow-lg">
                    <div class="flex items-center mb-4">
                        <div class="p-2 rounded-lg bg-blue-100 dark:bg-blue-900/50 text-blue-600 dark:text-blue-300 mr-3">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                            </svg>
                        </div>
                        <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">{{ __('forum.categories') }}</h3>
                    </div>
                    <div class="space-y-3">
                        <a href="#" class="flex items-center justify-between px-3 py-2 rounded-lg transition-all duration-200
                                         text-blue-600 dark:text-blue-300 hover:bg-blue-50 dark:hover:bg-gray-700/50">
                            <span class="font-medium">{{ __('forum.all_questions') }}</span>
                            <span class="bg-blue-100 dark:bg-blue-900/50 text-blue-800 dark:text-blue-100 text-xs font-medium px-2.5 py-1 rounded-full">
                                {{ $totalQuestions ?? 0 }}
                            </span>
                        </a>
                        @if(isset($categories) && $categories->count() > 0)
                            @foreach($categories as $category)
                            <a href="#" class="flex items-center justify-between px-3 py-2 rounded-lg transition-all duration-200
                                             text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700/50 hover:text-blue-600 dark:hover:text-blue-300">
                                <span>{{ $category->name }}</span>
                                <span class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ $category->questions_count ?? 0 }}</span>
                            </a>
                            @endforeach
                        @endif
                    </div>
                </div>

                <!-- Popular Tags -->
                <div class="bg-white dark:bg-gray-800/90 rounded-xl shadow-md p-6 mt-6 transition-all duration-300 hover:shadow-lg">
                    <div class="flex items-center mb-4">
                        <div class="p-2 rounded-lg bg-orange-100 dark:bg-orange-900/30 text-orange-600 dark:text-orange-300 mr-3">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A2 2 0 013 12V7a4 4 0 014-4z" />
                            </svg>
                        </div>
                        <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">{{ __('forum.tags') }}</h3>
                    </div>
                    <div class="flex flex-wrap gap-2">
                        @if(isset($tags) && $tags->count() > 0)
                            @foreach($tags as $tag)
                                <a href="#" class="inline-flex items-center px-3 py-1.5 rounded-full text-xs font-medium transition-all duration-200
                                                bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600
                                                text-gray-800 dark:text-gray-200 hover:-translate-y-0.5 hover:shadow-sm">
                                    #{{ $tag->name }}
                                </a>
                            @endforeach
                        @else
                            <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('forum.no_tags') }}</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
