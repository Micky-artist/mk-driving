@extends('admin.layouts.app')

@section('title', 'Quiz Attempts Management')

@push('styles')
<style>
    .attempt-table {
        /* Using pure Tailwind classes instead of @apply */
    }
    
    .search-input {
        /* Using pure Tailwind classes instead of @apply */
    }
    
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
</style>
@endpush

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-2">
                Quiz Attempts
            </h1>
            <p class="text-gray-600 dark:text-gray-400">
                View and manage user quiz attempts
            </p>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
        <form method="GET" action="{{ route('admin.quiz.attempts.index') }}" class="space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <!-- Quiz Filter -->
                <div>
                    <label for="quiz_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Quiz
                    </label>
                    <select name="quiz_id" id="quiz_id" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white">
                        <option value="">All Quizzes</option>
                        @foreach($quizzes as $id => $title)
                            <option value="{{ $id }}" {{ request('quiz_id') == $id ? 'selected' : '' }}>
                                {{ $title }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Score Percentage Filter -->
                <div>
                    <label for="score_filter" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Score Range
                    </label>
                    <select name="score_filter" id="score_filter" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white">
                        <option value="">All Scores</option>
                        <option value="90-100" {{ request('score_filter') == '90-100' ? 'selected' : '' }}>90% - 100% (Excellent)</option>
                        <option value="70-89" {{ request('score_filter') == '70-89' ? 'selected' : '' }}>70% - 89% (Good)</option>
                        <option value="50-69" {{ request('score_filter') == '50-69' ? 'selected' : '' }}>50% - 69% (Average)</option>
                        <option value="0-49" {{ request('score_filter') == '0-49' ? 'selected' : '' }}>0% - 49% (Poor)</option>
                    </select>
                </div>

                <!-- Status Filter -->
                <div>
                    <label for="status" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Status
                    </label>
                    <select name="status" id="status" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white">
                        <option value="">All Statuses</option>
                        <option value="PENDING" {{ request('status') == 'PENDING' ? 'selected' : '' }}>Pending</option>
                        <option value="IN_PROGRESS" {{ request('status') == 'IN_PROGRESS' ? 'selected' : '' }}>In Progress</option>
                        <option value="COMPLETED" {{ request('status') == 'COMPLETED' ? 'selected' : '' }}>Completed</option>
                        <option value="FAILED" {{ request('status') == 'FAILED' ? 'selected' : '' }}>Failed</option>
                        <option value="TIMEOUT" {{ request('status') == 'TIMEOUT' ? 'selected' : '' }}>Timeout</option>
                    </select>
                </div>
            </div>

            <div class="flex items-center gap-4">
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800">
                    Filter
                </button>
                <a href="{{ route('admin.quiz.attempts.index') }}" class="px-4 py-2 text-gray-700 dark:text-gray-300 bg-gray-100 dark:bg-gray-700 rounded-md hover:bg-gray-200 dark:hover:bg-gray-600">
                    Clear
                </a>
            </div>
        </form>
    </div>

    <!-- Attempts Table -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-900/50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                            User
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                            Quiz
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                            Status
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                            Score
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                            Started
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                            Duration
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                            Actions
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse($attempts as $attempt)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-10 w-10">
                                        <div class="h-10 w-10 rounded-full bg-blue-500 flex items-center justify-center">
                                            <span class="text-white font-medium">
                                                {{ strtoupper(substr($attempt->user->first_name ?? 'U', 0, 1)) }}
                                            </span>
                                        </div>
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-medium text-gray-900 dark:text-white">
                                            <a href="{{ route('admin.users.show', $attempt->user_id) }}" 
                                               class="hover:text-blue-600 dark:hover:text-blue-400">
                                                {{ $attempt->user->name ?? 'Unknown User' }}
                                            </a>
                                        </div>
                                        <div class="text-sm text-gray-500 dark:text-gray-400">
                                            {{ $attempt->user->email ?? 'No email' }}
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900 dark:text-white">
                                    {{ $attempt->quiz->title ?? 'Unknown Quiz' }}
                                </div>
                                <div class="text-sm text-gray-500 dark:text-gray-400">
                                    {{ $attempt->total_questions }} questions
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @switch($attempt->status)
                                    @case('PENDING')
                                        <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800 dark:bg-yellow-900/20 dark:text-yellow-400">
                                            Pending
                                        </span>
                                        @break
                                    @case('IN_PROGRESS')
                                        <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800 dark:bg-blue-900/20 dark:text-blue-400">
                                            In Progress
                                        </span>
                                        @break
                                    @case('COMPLETED')
                                        <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800 dark:bg-green-900/20 dark:text-green-400">
                                            Completed
                                        </span>
                                        @break
                                    @case('FAILED')
                                        <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800 dark:bg-red-900/20 dark:text-red-400">
                                            Failed
                                        </span>
                                        @break
                                    @case('TIMEOUT')
                                        <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-purple-100 text-purple-800 dark:bg-purple-900/20 dark:text-purple-400">
                                            Timeout
                                        </span>
                                        @break
                                    @default
                                        <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800 dark:bg-gray-900/20 dark:text-gray-400">
                                            {{ $attempt->status }}
                                        </span>
                                @endswitch
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($attempt->score !== null && $attempt->total_questions > 0)
                                    <div class="flex items-center">
                                        <div class="text-sm font-medium text-gray-900 dark:text-white">
                                            {{ number_format($attempt->score_percentage, 1) }}%
                                        </div>
                                        <div class="ml-2">
                                            @if($attempt->score_percentage >= 90)
                                                <span class="text-green-500">●</span>
                                            @elseif($attempt->score_percentage >= 70)
                                                <span class="text-blue-500">●</span>
                                            @elseif($attempt->score_percentage >= 50)
                                                <span class="text-yellow-500">●</span>
                                            @else
                                                <span class="text-red-500">●</span>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="text-sm text-gray-500 dark:text-gray-400">
                                        {{ $attempt->score }}/{{ $attempt->total_questions }}
                                    </div>
                                @else
                                    <div class="text-sm text-gray-500 dark:text-gray-400">
                                        Not scored
                                    </div>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                <div>{{ $attempt->started_at ? $attempt->started_at->format('M j, Y') : 'N/A' }}</div>
                                <div class="text-xs">{{ $attempt->started_at ? $attempt->started_at->format('H:i') : '' }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                @if($attempt->time_spent_seconds)
                                    <div>{{ gmdate('H:i:s', $attempt->time_spent_seconds) }}</div>
                                @else
                                    <div>N/A</div>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <a href="{{ route('admin.quiz.attempts.show', $attempt) }}" 
                                   class="text-blue-600 dark:text-blue-400 hover:text-blue-900 dark:hover:text-blue-300">
                                    View Details
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center text-gray-500 dark:text-gray-400">
                                <div class="flex flex-col items-center">
                                    <svg class="w-12 h-12 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                    </svg>
                                    <p class="text-lg font-medium mb-2">No quiz attempts found</p>
                                    <p class="text-sm">No quiz attempts match the current filters.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($attempts->hasPages())
            <div class="bg-white dark:bg-gray-800 px-4 py-3 flex items-center justify-between border-t border-gray-200 dark:border-gray-700 sm:px-6">
                <div class="flex-1 flex justify-between sm:hidden">
                    {{ $attempts->links() }}
                </div>
                <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
                    <div>
                        <p class="text-sm text-gray-700 dark:text-gray-300">
                            Showing
                            <span class="font-medium">{{ $attempts->firstItem() }}</span>
                            to
                            <span class="font-medium">{{ $attempts->lastItem() }}</span>
                            of
                            <span class="font-medium">{{ $attempts->total() }}</span>
                            results
                        </p>
                    </div>
                    <div>
                        {{ $attempts->links() }}
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>
@endsection
