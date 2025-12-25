@extends('dashboard.layouts.app')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Community Forum</h1>
            <p class="mt-1 text-sm text-gray-500">Ask questions and share knowledge with other drivers</p>
        </div>
        <a href="{{ route('forum.create', ['locale' => app()->getLocale()]) }}" 
           class="mt-4 md:mt-0 inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
            Ask a Question
        </a>
    </div>

    <div class="flex flex-col lg:flex-row gap-8">
        <!-- Main Content -->
        <div class="lg:w-3/4">
            <!-- Search and Filter -->
            <div class="bg-white rounded-lg shadow overflow-hidden mb-6">
                <div class="p-4 border-b border-gray-200">
                    <form action="{{ route('forum.index', ['locale' => app()->getLocale()]) }}" method="GET" class="flex flex-col sm:flex-row gap-4">
                        <div class="flex-1">
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <svg class="h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                                <input type="text" name="search" value="{{ request('search') }}" 
                                       class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-md leading-5 bg-white placeholder-gray-500 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm" 
                                       placeholder="Search questions...">
                            </div>
                        </div>
                        <div class="w-full sm:w-48">
                            <select name="topic" onchange="this.form.submit()" class="block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-md">
                                <option value="">All Topics</option>
                                @foreach($topics as $topic => $count)
                                    <option value="{{ $topic }}" {{ request('topic') === $topic ? 'selected' : '' }}>
                                        {{ $topic }} ({{ $count }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <button type="submit" class="hidden sm:inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            Search
                        </button>
                    </form>
                </div>
            </div>

            <!-- Questions List -->
            <div class="bg-white shadow overflow-hidden sm:rounded-md">
                @if($questions->count() > 0)
                    <ul class="divide-y divide-gray-200">
                        @foreach($questions as $question)
                            <li class="hover:bg-gray-50">
                                <a href="{{ route('forum.show', ['locale' => app()->getLocale(), 'id' => is_array($question) ? $question['id'] : $question->id]) }}" class="block hover:bg-gray-50">
                                    <div class="px-4 py-4 sm:px-6">
                                        <div class="flex items-center justify-between">
                                            <p class="text-sm font-medium text-blue-600 truncate">
                                                {{ is_array($question) ? $question['title'] : $question->title }}
                                            </p>
                                            <div class="ml-2 flex-shrink-0 flex">
                                                <p class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                                    {{ is_array($question) ? count($question['answers']) : $question->answers_count }} {{ Str::plural('answer', is_array($question) ? count($question['answers']) : $question->answers_count) }}
                                                </p>
                                            </div>
                                        </div>
                                        <div class="mt-2 sm:flex sm:justify-between">
                                            <div class="sm:flex">
                                                <p class="flex items-center text-sm text-gray-500">
                                                    <svg class="flex-shrink-0 mr-1.5 h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                                        <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd" />
                                                    </svg>
                                                    {{ is_array($question) ? ($question['author']['firstName'] . ' ' . $question['author']['lastName']) : $question->user->name }}
                                                </p>
                                                <p class="mt-2 flex items-center text-sm text-gray-500 sm:mt-0 sm:ml-6">
                                                    <svg class="flex-shrink-0 mr-1.5 h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                                        <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd" />
                                                    </svg>
                                                    {{ timeDiffForHumans(is_array($question) ? $question['createdAt'] : $question->created_at) }}
                                                </p>
                                            </div>
                                            <div class="mt-2 flex items-center text-sm text-gray-500 sm:mt-0">
                                                <svg class="flex-shrink-0 mr-1.5 h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                                    <path d="M10 12a2 2 0 100-4 2 2 0 000 4z" />
                                                    <path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd" />
                                                </svg>
                                                {{ is_array($question) ? ($question['views'] ?? 0) : $question->views }} {{ Str::plural('view', is_array($question) ? ($question['views'] ?? 0) : $question->views) }}
                                            </div>
                                        </div>
                                        @if(isset($question['topics']) || (isset($question->topics) && $question->topics))
                                        <div class="mt-2">
                                            @foreach((is_array($question) ? $question['topics'] : $question->topics) as $topic)
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 mr-2">
                                                    {{ is_array($topic) ? ($topic['name'] ?? $topic) : $topic }}
                                                </span>
                                            @endforeach
                                        </div>
                                        @endif
                                    </div>
                                </a>
                            </li>
                        @endforeach
                    </ul>
                    <!-- Pagination -->
                    <div class="bg-white px-4 py-3 flex items-center justify-between border-t border-gray-200 sm:px-6">
                        {{ $questions->withQueryString()->links() }}
                    </div>
                @else
                    <div class="text-center py-12">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <h3 class="mt-2 text-sm font-medium text-gray-900">No questions found</h3>
                        <p class="mt-1 text-sm text-gray-500">
                            @if(request('search') || request('topic'))
                                Try adjusting your search or filter to find what you're looking for.
                            @else
                                Be the first to ask a question!
                            @endif
                        </p>
                        <div class="mt-6">
                            <a href="{{ route('dashboard.forum.create') }}" class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                <svg class="-ml-1 mr-2 h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z" clip-rule="evenodd" />
                                </svg>
                                New Question
                            </a>
                        </div>
                    </div>
                @endif
            </div>
        </div>

        <!-- Sidebar -->
        <div class="lg:w-1/4 space-y-6">
            <!-- Popular Topics -->
            <div class="bg-white shadow overflow-hidden sm:rounded-lg">
                <div class="px-4 py-5 sm:px-6 border-b border-gray-200">
                    <h3 class="text-lg leading-6 font-medium text-gray-900">Popular Topics</h3>
                </div>
                <div class="px-4 py-5 sm:p-6">
                    <div class="flow-root">
                        <ul class="-my-5 divide-y divide-gray-200">
                            @foreach($topics->take(10) as $topic => $count)
                                <li class="py-4">
                                    <div class="flex items-center space-x-4">
                                        <div class="flex-1 min-w-0">
                                            <a href="{{ route('dashboard.forum.index', ['topic' => $topic]) }}" class="text-sm font-medium text-gray-900 hover:text-blue-600 truncate">
                                                {{ $topic }}
                                            </a>
                                        </div>
                                        <div>
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                                {{ $count }}
                                            </span>
                                        </div>
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Ask a Question -->
            <div class="bg-blue-50 p-6 rounded-lg">
                <div class="text-center">
                    <svg class="mx-auto h-12 w-12 text-blue-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900">Need help?</h3>
                    <p class="mt-1 text-sm text-gray-500">Can't find what you're looking for? Ask a question to the community.</p>
                    <div class="mt-6">
                        <a href="{{ route('forum.create') }}" class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            Ask a Question
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Handle topic filter clear
    document.addEventListener('DOMContentLoaded', function() {
        const topicSelect = document.querySelector('select[name="topic"]');
        const urlParams = new URLSearchParams(window.location.search);
        
        // If no topic is selected but there's a topic in the URL, update the select
        if (!topicSelect.value && urlParams.has('topic')) {
            topicSelect.value = urlParams.get('topic');
        }
        
        // Handle clear filter button if we add one
        const clearFilter = document.getElementById('clear-filter');
        if (clearFilter) {
            clearFilter.addEventListener('click', function(e) {
                e.preventDefault();
                const url = new URL(window.location);
                url.searchParams.delete('topic');
                window.location.href = url.toString();
            });
        }
    });
</script>
@endpush

@endsection
