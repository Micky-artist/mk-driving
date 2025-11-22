@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gray-50">
    <!-- Hero Section -->
    <div class="bg-blue-600 text-white py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <h1 class="text-3xl font-bold">{{ __('forum.page_title') }}</h1>
            <p class="mt-2 text-blue-100">{{ __('forum.page_description') }}</p>
        </div>
    </div>

    <!-- Main Content -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="flex flex-col md:flex-row gap-8">
            <!-- Questions List -->
            <div class="w-full md:w-2/3">
                <!-- Filters -->
                <div class="bg-white rounded-lg shadow p-4 mb-6">
                    <div class="flex flex-wrap items-center justify-between gap-4">
                        <div class="flex space-x-2">
                            <button class="px-4 py-2 rounded-full text-sm font-medium bg-blue-100 text-blue-800">
                                {{ __('forum.all_questions') }}
                            </button>
                            <button class="px-4 py-2 rounded-full text-sm font-medium text-gray-600 hover:bg-gray-100">
                                {{ __('forum.answered') }}
                            </button>
                            <button class="px-4 py-2 rounded-full text-sm font-medium text-gray-600 hover:bg-gray-100">
                                {{ __('forum.unanswered') }}
                            </button>
                        </div>
                        <div class="relative w-full md:w-auto">
                            <input type="text" 
                                   placeholder="{{ __('forum.search_placeholder') }}" 
                                   class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd" />
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
            <div class="w-full md:w-1/3">
                <!-- Ask Question Button -->
                <div class="bg-white rounded-lg shadow p-6 mb-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">{{ __('forum.need_help') }}</h3>
                    <p class="text-gray-600 mb-4">{{ __('forum.ask_community') }}</p>
                    <a href="{{ route('forum.create', ['locale' => app()->getLocale()]) }}" 
                       class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        {{ __('forum.ask_question') }}
                    </a>
                </div>

                <!-- Categories -->
                <div class="bg-white rounded-lg shadow p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">{{ __('forum.categories') }}</h3>
                    <div class="space-y-2">
                        <a href="#" class="flex items-center justify-between text-blue-600 hover:text-blue-800">
                            <span>{{ __('forum.all_questions') }}</span>
                            <span class="bg-blue-100 text-blue-800 text-xs font-semibold px-2.5 py-0.5 rounded-full">
                                {{ $totalQuestions ?? 0 }}
                            </span>
                        </a>
                        @if(isset($categories) && $categories->count() > 0)
                            @foreach($categories as $category)
                            <a href="#" class="flex items-center justify-between text-gray-600 hover:text-blue-600">
                                <span>{{ $category->name }}</span>
                                <span class="text-gray-500 text-sm">{{ $category->questions_count ?? 0 }}</span>
                            </a>
                            @endforeach
                        @endif
                    </div>
                </div>

                <!-- Popular Tags -->
                <div class="bg-white rounded-lg shadow p-6 mt-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">{{ __('forum.tags') }}</h3>
                    <div class="flex flex-wrap gap-2">
                        @if(isset($tags) && $tags->count() > 0)
                            @foreach($tags as $tag)
                                <a href="#" class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800 hover:bg-gray-200">
                                    {{ $tag->name }}
                                </a>
                            @endforeach
                        @else
                            <p class="text-sm text-gray-500">{{ __('forum.no_tags') }}</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
