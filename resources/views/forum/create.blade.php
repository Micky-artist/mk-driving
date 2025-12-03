@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-gray-50 via-white to-gray-100 dark:from-gray-900 dark:via-gray-800 dark:to-gray-900 transition-colors duration-300">
    <!-- Hero Section -->
    <div class="relative overflow-hidden bg-gradient-to-r from-blue-600 to-blue-700 text-white py-2 md:py-4 shadow-xl">
        <!-- Decorative elements -->
        <div class="absolute top-0 left-0 w-full h-full opacity-10">
            <div class="absolute top-0 right-0 w-1/3 h-1/3 bg-blue-300 rounded-full filter blur-3xl"></div>
            <div class="absolute bottom-0 left-0 w-1/4 h-1/4 bg-indigo-300 rounded-full filter blur-3xl"></div>
        </div>
        
        <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <h1 class="text-4xl md:text-5xl font-bold mb-4 fade-in bg-clip-text text-transparent bg-gradient-to-r from-white to-blue-100">
                {{ __('forum.ask_question') }}
            </h1>
            <p class="text-blue-100 text-lg md:text-xl max-w-3xl mx-auto fade-in delay-100 leading-relaxed">
                {{ __('forum.ask_community') }}
            </p>
        </div>
    </div>

    <!-- Main Content -->
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <div class="mb-6">
            <a href="{{ route('forum.index', ['locale' => app()->getLocale()]) }}" 
               class="inline-flex items-center text-sm font-medium text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300 transition-colors duration-200">
                <svg class="w-5 h-5 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                {{ __('forum.back_to_forum') }}
            </a>
        </div>

        <div class="bg-white dark:bg-gray-800/90 backdrop-blur-sm rounded-2xl shadow-xl overflow-hidden border border-gray-100 dark:border-gray-700/50 transition-all duration-300 transform hover:shadow-2xl">
            <div class="px-6 py-5 border-b border-gray-100 dark:border-gray-700/50">
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">{{ __('forum.ask_question') }}</h1>
                <p class="mt-1.5 text-sm text-gray-500 dark:text-gray-400">{{ __('forum.ask_community_help') }}</p>
            </div>
            
            <form action="{{ route('forum.store', ['locale' => app()->getLocale()]) }}" method="POST" class="p-6 md:p-8">
                @csrf
                
                <!-- Title -->
                <div class="mb-8">
                    <label for="title" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2.5">
                        {{ __('forum.question.title_placeholder') }}
                        <span class="text-red-500">*</span>
                    </label>
                    <div class="mt-1">
                        <div class="relative rounded-lg shadow-sm">
                            <input type="text" 
                                   name="title[{{ app()->getLocale() }}]" 
                                   id="title" 
                                   class="block w-full px-4 py-3 text-base border-0 rounded-xl bg-gray-50 dark:bg-gray-700/50 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200 @error('title.*') ring-2 ring-red-500 @enderror"
                                   value="{{ old('title.'.app()->getLocale()) }}" 
                                   placeholder="{{ __('forum.question.title_placeholder') }}"
                                   required>
                            @error('title.*')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                            <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                <svg class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                        </div>
                        @error('title')
                            <p class="mt-2 text-sm text-red-600 flex items-center">
                                <svg class="w-4 h-4 mr-1.5" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h2a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                                </svg>
                                {{ $message }}
                            </p>
                        @enderror
                        <p class="mt-2 text-xs text-gray-500 dark:text-gray-400">
                            {{ __('forum.question.title_tip') }}
                        </p>
                    </div>
                </div>

                <!-- Content -->
                <div class="mb-8">
                    <label for="content" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2.5">
                        {{ __('forum.question.body_placeholder') }}
                        <span class="text-red-500">*</span>
                    </label>
                    <div class="mt-1">
                        <div class="rounded-lg shadow-sm">
                            <textarea id="content" 
                                     name="content[{{ app()->getLocale() }}]" 
                                     rows="8"
                                     class="block w-full px-4 py-3 text-base border-0 rounded-xl bg-gray-50 dark:bg-gray-700/50 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200 @error('content.*') ring-2 ring-red-500 @enderror"
                                     placeholder="{{ __('forum.question.body_placeholder') }}"
                                     required>{{ old('content.'.app()->getLocale()) }}</textarea>
                            @error('content.*')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        @error('content')
                            <p class="mt-2 text-sm text-red-600 flex items-center">
                                <svg class="w-4 h-4 mr-1.5" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h2a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                                </svg>
                                {{ $message }}
                            </p>
                        @enderror
                        <div class="mt-2 flex items-center text-xs text-gray-500 dark:text-gray-400">
                            <svg class="w-4 h-4 mr-1.5 text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            {{ __('forum.question.body_tip') }}
                        </div>
                    </div>
                </div>

                <!-- Tags -->
                <div class="mb-8">
                    <label for="tags" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2.5">
                        {{ __('forum.tags') }}
                        <span class="text-xs font-normal text-gray-500 dark:text-gray-400 ml-1">({{ __('forum.optional') }})</span>
                    </label>
                    <div class="mt-1">
                        <input type="text" 
                               name="tags" 
                               id="tags" 
                               class="block w-full px-4 py-3 text-base border-0 rounded-xl bg-gray-50 dark:bg-gray-700/50 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200"
                               placeholder="{{ __('forum.tags_placeholder') }}"
                               value="{{ old('tags') }}">
                        <p class="mt-2 text-xs text-gray-500 dark:text-gray-400">
                            {{ __('forum.tags_tip') }}
                        </p>
                    </div>
                </div>

                <!-- Submit Button -->
                <div class="pt-6 border-t border-gray-100 dark:border-gray-700/50">
                    <div class="flex flex-col sm:flex-row justify-end gap-3">
                        <a href="{{ route('forum.index', ['locale' => app()->getLocale()]) }}" 
                           class="inline-flex justify-center items-center px-6 py-3 border border-gray-300 dark:border-gray-600 rounded-xl text-sm font-medium text-gray-700 dark:text-gray-200 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-all duration-200">
                            {{ __('common.cancel') }}
                        </a>
                        <button type="submit" 
                                class="inline-flex justify-center items-center px-6 py-3 border border-transparent rounded-xl shadow-sm text-sm font-medium text-white bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-all duration-200 transform hover:-translate-y-0.5 hover:shadow-md">
                            <svg class="w-5 h-5 mr-2 -ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                            </svg>
                            {{ __('forum.question.submit') }}
                        </button>
                    </div>
                    <p class="mt-3 text-xs text-center sm:text-right text-gray-500 dark:text-gray-400">
                        {{ __('forum.terms_notice') }}
                    </p>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
