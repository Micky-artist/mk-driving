@extends('admin.layouts.app')

@section('title', 'Quiz List')

@push('styles')
<style>
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }
    
    .fade-in {
        animation: fadeIn 0.6s ease-out forwards;
    }
    
    .fade-in-delay-1 { animation-delay: 0.1s; }
    .fade-in-delay-2 { animation-delay: 0.2s; }
    .fade-in-delay-3 { animation-delay: 0.3s; }
    
    .quiz-row {
        transition: all 0.2s ease;
    }
    
    .quiz-row:hover {
        transform: translateX(4px);
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    }
    
    .filter-section {
        background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
    }
    
    .dark .filter-section {
        background: linear-gradient(135deg, #1e293b 0%, #334155 100%);
    }
</style>
@endpush

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-2">
                Quiz List
            </h1>
            <p class="text-gray-600 dark:text-gray-400">
                Full list view with advanced filtering and sorting options.
            </p>
        </div>
        <div class="flex items-center gap-3">
            <!-- Search -->
            <div class="relative flex-1 max-w-md">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                </div>
                <input type="text" 
                       id="search-input"
                       placeholder="Search quizzes..." 
                       value="{{ request('search') }}"
                       class="bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 rounded-full pl-10 pr-4 py-3 w-full focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200">
                @if(request('search'))
                <button type="button" 
                        onclick="clearSearch()"
                        class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition-colors">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
                @endif
            </div>
            
            <!-- Add Quiz Button -->
            <a href="{{ route('admin.quizzes.create') }}" 
               class="group inline-flex items-center px-6 py-3 bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white font-semibold rounded-full shadow-lg hover:shadow-xl transform hover:scale-105 transition-all duration-200 border border-blue-500/20">
                <svg class="w-5 h-5 mr-2 group-hover:rotate-90 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                </svg>
                <span class="relative">
                    Add Quiz
                    <span class="absolute -bottom-1 left-0 right-0 h-0.5 bg-white/30 transform scale-x-0 group-hover:scale-x-100 transition-transform duration-200"></span>
                </span>
            </a>
            
            <!-- Clear Filters Button -->
            <button type="button" onclick="window.location.href='{{ route('admin.test.quizzes.list') }}'" class="px-6 py-3 bg-gray-200 hover:bg-gray-300 dark:bg-gray-600 dark:hover:bg-gray-500 text-gray-700 dark:text-gray-300 font-medium rounded-full transition-colors duration-200">
                Clear Filters
            </button>
        </div>
    </div>

    
    <!-- Filters Section -->
    <div class="filter-section bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6 fade-in fade-in-delay-1">
        <form method="GET" action="{{ route('admin.test.quizzes.list') }}" class="space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                <!-- Subscription Plan Count Filter -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Subscription Plan Count</label>
                    <select name="subscription_plan_count" class="w-full px-3 py-2 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 text-gray-900 dark:text-white rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="">All Plans</option>
                        <option value="1" {{ request('subscription_plan_count') == '1' ? 'selected' : '' }}>1 Plan</option>
                        <option value="2" {{ request('subscription_plan_count') == '2' ? 'selected' : '' }}>2 Plans</option>
                        <option value="3" {{ request('subscription_plan_count') == '3' ? 'selected' : '' }}>3 Plans</option>
                        <option value="4" {{ request('subscription_plan_count') == '4' ? 'selected' : '' }}>4 Plans</option>
                    </select>
                </div>

                <!-- Status Filter -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Status</label>
                    <select name="is_active" class="w-full px-3 py-2 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 text-gray-900 dark:text-white rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="">All Status</option>
                        <option value="true" {{ request('is_active') == 'true' ? 'selected' : '' }}>Active</option>
                        <option value="false" {{ request('is_active') == 'false' ? 'selected' : '' }}>Inactive</option>
                    </select>
                </div>

                <!-- Date Range Filter -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Date Range</label>
                    <select name="date_range" class="w-full px-3 py-2 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 text-gray-900 dark:text-white rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="">All Time</option>
                        <option value="7" {{ request('date_range') == '7' ? 'selected' : '' }}>Last 7 Days</option>
                        <option value="30" {{ request('date_range') == '30' ? 'selected' : '' }}>Last 30 Days</option>
                        <option value="365" {{ request('date_range') == '365' ? 'selected' : '' }}>Last Year</option>
                    </select>
                </div>

                <!-- Sort By -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Sort By</label>
                    <select name="sort" class="w-full px-3 py-2 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 text-gray-900 dark:text-white rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="created_at" {{ request('sort') == 'created_at' ? 'selected' : '' }}>Created Date</option>
                        <option value="title" {{ request('sort') == 'title' ? 'selected' : '' }}>Title</option>
                        <option value="questions_count" {{ request('sort') == 'questions_count' ? 'selected' : '' }}>Questions Count</option>
                        <option value="attempts_count" {{ request('sort') == 'attempts_count' ? 'selected' : '' }}>Attempts Count</option>
                        <option value="average_score" {{ request('sort') == 'average_score' ? 'selected' : '' }}>Average Score</option>
                    </select>
                </div>
            </div>

            <!-- Hidden search field -->
            @if(request('search'))
            <input type="hidden" name="search" value="{{ request('search') }}">
            @endif
        </form>
    </div>

    <!-- Quizzes Table -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 fade-in fade-in-delay-2">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-700">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                            Quiz
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                            Questions
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                            Attempts
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                            Avg Score
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                            Time Limit
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                            Status
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                            Created
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                            Actions
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse($quizzes as $quiz)
                    <tr class="quiz-row hover:bg-gray-50 dark:hover:bg-gray-700 cursor-pointer" onclick="window.location.href='{{ route('admin.quizzes.show', $quiz) }}'">
                        <!-- Quiz -->
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 h-12 w-12">
                                    <div class="h-12 w-12 rounded-full bg-gradient-to-br from-blue-500 to-blue-600 flex items-center justify-center text-white font-semibold text-sm shadow-md">
                                        {{ strtoupper(substr(json_decode($quiz->title)['en'] ?? 'Unknown', 0, 2)) }}
                                    </div>
                                </div>
                                <div class="ml-4">
                                    <div class="text-sm font-medium text-gray-900 dark:text-white">
                                        {{ json_decode($quiz->title)['en'] ?? 'Untitled' }}
                                    </div>
                                    @if ($quiz->description)
                                        <div class="text-xs text-gray-500 dark:text-gray-400">
                                            {{ Str::limit(json_decode($quiz->description)['en'] ?? '', 60) }}
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </td>
                        
                        <!-- Questions -->
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900 dark:text-white">
                                {{ $quiz->questions_count }}
                            </div>
                            <div class="text-xs text-gray-500 dark:text-gray-400">
                                questions
                            </div>
                        </td>
                        
                        <!-- Attempts -->
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900 dark:text-white">
                                {{ $quiz->attempts_count ?? 0 }}
                            </div>
                            <div class="text-xs text-gray-500 dark:text-gray-400">
                                attempts
                            </div>
                        </td>
                        
                        <!-- Avg Score -->
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900 dark:text-white">
                                {{ number_format($quiz->average_score ?? 0, 1) }}%
                            </div>
                            <div class="text-xs text-gray-500 dark:text-gray-400">
                                avg score
                            </div>
                        </td>
                        
                        <!-- Time Limit -->
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900 dark:text-white">
                                {{ $quiz->time_limit_minutes ? $quiz->time_limit_minutes . ' min' : 'No limit' }}
                            </div>
                        </td>
                        
                        <!-- Status -->
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                {{ $quiz->is_active ? 'bg-green-100 text-green-800 dark:bg-green-900/20 dark:text-green-400' : 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300' }}">
                                {{ $quiz->is_active ? 'Active' : 'Inactive' }}
                            </span>
                        </td>
                        
                        <!-- Created -->
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                            {{ $quiz->created_at->format('M j, Y') }}
                        </td>
                        
                        <!-- Actions -->
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                            <div class="flex items-center space-x-2" onclick="event.stopPropagation()">
                                <a href="{{ route('admin.quizzes.show', $quiz) }}" class="text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-300" title="View Quiz">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                    </svg>
                                </a>
                                <a href="{{ route('admin.quizzes.edit', $quiz) }}" class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-300" title="Edit Quiz">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                    </svg>
                                </a>
                                <form action="{{ route('admin.quizzes.destroy', $quiz) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this quiz?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300" title="Delete Quiz">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                        </svg>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="px-6 py-12 text-center">
                            <div class="flex flex-col items-center">
                                <svg class="w-12 h-12 text-gray-400 dark:text-gray-500 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                                </svg>
                                <p class="text-sm text-gray-500 dark:text-gray-400 font-medium">
                                    No quizzes found
                                </p>
                                <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">
                                    Try adjusting your search or filters
                                </p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <!-- Pagination -->
        @if($quizzes->hasPages())
        <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800">
            <div class="flex items-center justify-between">
                <div class="text-sm text-gray-700 dark:text-gray-300">
                    Showing results {{ $quizzes->firstItem() }} to {{ $quizzes->lastItem() }} of {{ $quizzes->total() }} total
                </div>
                {{ $quizzes->links() }}
            </div>
        </div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('search-input');
    const filterSelects = document.querySelectorAll('select[name="subscription_plan_count"], select[name="is_active"], select[name="date_range"], select[name="sort"]');
    let searchTimeout;
    
    // Search functionality
    if (searchInput) {
        searchInput.addEventListener('input', function() {
            clearTimeout(searchTimeout);
            const query = this.value.trim();
            
            searchTimeout = setTimeout(() => {
                updateUrl();
            }, 600); // Debounce search
        });
    }
    
    // Filter change functionality
    filterSelects.forEach(select => {
        select.addEventListener('change', function() {
            updateUrl();
        });
    });
    
    function updateUrl() {
        const url = new URL(window.location);
        
        // Handle search
        const searchValue = searchInput ? searchInput.value.trim() : '';
        if (searchValue) {
            url.searchParams.set('search', searchValue);
        } else {
            url.searchParams.delete('search');
        }
        
        // Handle filters
        const subscriptionPlanCount = document.querySelector('select[name="subscription_plan_count"]').value;
        const isActive = document.querySelector('select[name="is_active"]').value;
        const dateRange = document.querySelector('select[name="date_range"]').value;
        const sort = document.querySelector('select[name="sort"]').value;
        
        if (subscriptionPlanCount) {
            url.searchParams.set('subscription_plan_count', subscriptionPlanCount);
        } else {
            url.searchParams.delete('subscription_plan_count');
        }
        
        if (isActive) {
            url.searchParams.set('is_active', isActive);
        } else {
            url.searchParams.delete('is_active');
        }
        
        if (dateRange) {
            url.searchParams.set('date_range', dateRange);
        } else {
            url.searchParams.delete('date_range');
        }
        
        if (sort) {
            url.searchParams.set('sort', sort);
        } else {
            url.searchParams.delete('sort');
        }
        
        url.searchParams.set('page', '1'); // Reset to first page
        window.location.href = url.toString();
    }
});

function clearSearch() {
    const url = new URL(window.location);
    url.searchParams.delete('search');
    url.searchParams.set('page', '1');
    window.location.href = url.toString();
}
</script>
@endpush
