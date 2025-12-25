@extends('admin.layouts.app')

@section('title', 'Quiz Drafts')

@push('styles')
    <style>
        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .fade-in {
            animation: fadeIn 0.6s ease-out forwards;
        }

        .fade-in-delay-1 {
            animation-delay: 0.1s;
        }

        .fade-in-delay-2 {
            animation-delay: 0.2s;
        }

        .fade-in-delay-3 {
            animation-delay: 0.3s;
        }

        .quiz-card {
            transition: all 0.3s ease;
        }

        .quiz-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1);
        }
    </style>
@endpush

@section('content')
    <div class="space-y-6">
        <!-- Header -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-8">
            <div>
                <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-2">
                    Quiz Drafts
                </h1>
                <p class="text-gray-600 dark:text-gray-400">
                    Manage and continue working on quiz drafts and incomplete content.
                </p>
            </div>
<div class="flex items-center gap-4">
            <!-- Search -->
            <div class="relative max-w-md">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                </div>
                <input type="text" id="search-input" placeholder="Search by quiz title or description..."
                    class="bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 rounded-full pl-10 pr-4 py-3 w-full focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200">
                @if ($search ?? false)
                    <button type="button" onclick="clearSearch()"
                        class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition-colors">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12">
                            </path>
                        </svg>
                    </button>
                @endif
            </div>

            <!-- Create New Quiz Button -->
            <button type="button" onclick="window.location.href='{{ route('admin.quizzes.create') }}'"
                class="group inline-flex items-center px-6 py-3 bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white font-semibold rounded-full shadow-lg hover:shadow-xl transform hover:scale-105 transition-all duration-200 border border-blue-500/20">
                <svg class="w-5 h-5 mr-2 group-hover:rotate-90 transition-transform duration-200" fill="none"
                    stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6">
                    </path>
                </svg>
                <span class="relative">
                    Create Quiz
                    <span
                        class="absolute -bottom-1 left-0 right-0 h-0.5 bg-white/30 transform scale-x-0 group-hover:scale-x-100 transition-transform duration-200"></span>
                </span>
            </button>
            </div>
        </div>
    </div>
    <!-- Drafts List -->
    <div
        class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden fade-in fade-in-delay-1">
        @forelse ($drafts as $draft)
            <div class="divide-y divide-gray-200 dark:divide-gray-700">
                <div class="quiz-card p-6 hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-all duration-200">
                    <div class="flex flex-col lg:flex-row lg:items-start lg:justify-between gap-4">
                        <!-- Quiz Info -->
                        <div class="flex-1 min-w-0">
                            <div class="flex items-start gap-3">
                                <!-- Quiz Icon -->
                                <div
                                    class="flex-shrink-0 w-10 h-10 bg-orange-100 dark:bg-orange-900/20 rounded-lg flex items-center justify-center">
                                    <svg class="w-5 h-5 text-orange-600 dark:text-orange-400" fill="none"
                                        stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 12h6m-6 4h6m2 5H7a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2">
                                        </path>
                                    </svg>
                                </div>

                                <div class="flex-1 min-w-0">
                                    <!-- Quiz Title -->
                                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">
                                        @php
                                            $quizData = is_array($draft->quiz_data)
                                                ? $draft->quiz_data
                                                : json_decode($draft->quiz_data, true);
                                            $titleData = $quizData['quiz_info']['title'] ?? [];
                                            if (is_string($titleData)) {
                                                $title = $titleData;
                                            } elseif (is_array($titleData)) {
                                                $title = $titleData['en'] ?? 'Untitled Draft';
                                            } else {
                                                $title = 'Untitled Draft';
                                            }
                                            $descriptionData = $quizData['quiz_info']['description'] ?? [];
                                            if (is_string($descriptionData)) {
                                                $description = $descriptionData;
                                            } elseif (is_array($descriptionData)) {
                                                $description = $descriptionData['en'] ?? 'No description';
                                            } else {
                                                $description = 'No description';
                                            }
                                            $questionsCount = isset($quizData['questions'])
                                                ? count($quizData['questions'])
                                                : 0;
                                        @endphp
                                        {{ $title }}
                                    </h3>

                                    <!-- Quiz Description -->
                                    <p class="text-gray-600 dark:text-gray-400 mb-3 line-clamp-2">
                                        {{ $description }}
                                    </p>

                                    <!-- Meta Info -->
                                    <div class="flex flex-wrap items-center gap-2 mb-3">
                                        <!-- Questions Count -->
                                        <span
                                            class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-300">
                                            <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z">
                                                </path>
                                            </svg>
                                            {{ $questionsCount }} question{{ $questionsCount != 1 ? 's' : '' }}
                                        </span>

                                        <!-- Last Modified -->
                                        <span
                                            class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-300">
                                            <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2z">
                                                </path>
                                            </svg>
                                            {{ $draft->updated_at->format('M d, Y') }}
                                        </span>

                                        <!-- Status -->
                                        <span
                                            class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-orange-100 text-orange-800 dark:bg-orange-900/30 dark:text-orange-300">
                                            <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                            </svg>
                                            Draft
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Actions -->
                        <div class="flex flex-col sm:flex-row gap-2 lg:ml-4">
                            <!-- Continue Editing -->
                            @if ($questionsCount > 0)
                                <button type="button"
                                    onclick="window.location.href='{{ route('admin.quizzes.create.question', ['step' => $questionsCount + 1]) }}'"
                                    class="inline-flex items-center justify-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition-all duration-200">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M5 13l4 4L19 7"></path>
                                    </svg>
                                    Continue Editing
                                </button>
                            @else
                                <button type="button"
                                    onclick="window.location.href='{{ route('admin.quizzes.create.question', ['step' => 1]) }}'"
                                    class="inline-flex items-center justify-center px-4 py-2 bg-green-600 hover:bg-green-700 text-white font-medium rounded-lg transition-all duration-200">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                    </svg>
                                    Start Quiz
                                </button>
                            @endif

                            <!-- Delete -->
                            <form action="{{ route('admin.quizzes.delete-draft') }}" method="POST"
                                onsubmit="return confirm('Are you sure you want to delete this draft?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                    class="inline-flex items-center justify-center px-4 py-2 bg-red-600 hover:bg-red-700 text-white font-medium rounded-lg transition-all duration-200">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16a2 2 0 002 2v12a2 2 0 01-2 2h2a2 2 0 01-2 2z">
                                        </path>
                                    </svg>
                                    Delete
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="p-12 text-center">
                <svg class="w-16 h-16 mx-auto text-gray-400 mb-4" fill="none" stroke="currentColor"
                    viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 12h6m-6 4h6m2 5H7a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2">
                    </path>
                </svg>
                <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">No Quiz Drafts</h3>
                <p class="text-gray-500 dark:text-gray-400 mb-4">
                    Get started by creating your first quiz draft.
                </p>
                <button type="button" onclick="window.location.href='{{ route('admin.quizzes.create') }}'"
                    class="inline-flex items-center px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg shadow-sm hover:shadow-md transition-all duration-200">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                    </svg>
                    Create First Draft
                </button>
            </div>
        @endforelse
    </div>

    <!-- Pagination -->
    @if ($drafts->hasPages())
        <div class="mt-6">
            {{ $drafts->links() }}
        </div>
    @endif
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const searchInput = document.getElementById('search-input');
            let searchTimeout;


            if (searchInput) {
                searchInput.addEventListener('input', function() {
                    clearTimeout(searchTimeout);
                    const query = this.value.trim();

                    searchTimeout = setTimeout(() => {
                        const url = new URL(window.location);
                        if (query) {
                            url.searchParams.set('search', query);
                        } else {
                            url.searchParams.delete('search');
                        }
                        url.searchParams.set('page', '1'); // Reset to first page on search
                        window.location.href = url.toString();
                    }, 300);
                });
            }
        });

        function clearSearch() {
            const searchInput = document.getElementById('search-input');
            if (searchInput) {
                searchInput.value = '';
                const url = new URL(window.location);
                url.searchParams.delete('search');
                url.searchParams.set('page', '1');
                window.location.href = url.toString();
            }
        }
    </script>
@endsection
