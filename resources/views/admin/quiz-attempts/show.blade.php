@extends('admin.layouts.app')

@section('title', 'Quiz Attempt Details')

@push('styles')
<style>
    .attempt-detail {
        /* Using pure Tailwind classes instead of @apply */
    }
    
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }
    
    .fade-in {
        animation: fadeIn 0.6s ease-out forwards;
    }
</style>
@endpush

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-2">
                Quiz Attempt Details
            </h1>
            <p class="text-gray-600 dark:text-gray-400">
                Review user's quiz attempt and answers
            </p>
        </div>
        <div class="flex items-center gap-4">
            <a href="{{ route('admin.quiz.attempts.index') }}" 
               class="px-4 py-2 text-gray-700 dark:text-gray-300 bg-gray-100 dark:bg-gray-700 rounded-md hover:bg-gray-200 dark:hover:bg-gray-600">
                Back to Attempts
            </a>
        </div>
    </div>

    <!-- Attempt Summary -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <div>
                <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">User</h3>
                <p class="text-lg font-semibold text-gray-900 dark:text-white">
                    {{ $attempt->user->name ?? 'Unknown User' }}
                </p>
                <p class="text-sm text-gray-500 dark:text-gray-400">
                    {{ $attempt->user->email ?? 'No email' }}
                </p>
            </div>
            
            <div>
                <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">Quiz</h3>
                <p class="text-lg font-semibold text-gray-900 dark:text-white">
                    {{ $attempt->quiz->title ?? 'Unknown Quiz' }}
                </p>
                <p class="text-sm text-gray-500 dark:text-gray-400">
                    {{ $attempt->total_questions }} questions
                </p>
            </div>
            
            <div>
                <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">Status</h3>
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
            </div>
            
            <div>
                <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">Score</h3>
                @if($attempt->score !== null && $attempt->total_questions > 0)
                    <p class="text-lg font-semibold text-gray-900 dark:text-white">
                        {{ $attempt->score }}/{{ $attempt->total_questions }}
                    </p>
                    <p class="text-sm text-gray-500 dark:text-gray-400">
                        {{ number_format($attempt->score_percentage, 1) }}%
                    </p>
                @else
                    <p class="text-lg font-semibold text-gray-900 dark:text-white">
                        Not scored
                    </p>
                @endif
            </div>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-6 pt-6 border-t border-gray-200 dark:border-gray-700">
            <div>
                <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">Started</h3>
                <p class="text-lg font-semibold text-gray-900 dark:text-white">
                    {{ $attempt->started_at ? $attempt->started_at->format('M j, Y H:i:s') : 'N/A' }}
                </p>
            </div>
            
            <div>
                <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">Completed</h3>
                <p class="text-lg font-semibold text-gray-900 dark:text-white">
                    {{ $attempt->completed_at ? $attempt->completed_at->format('M j, Y H:i:s') : 'N/A' }}
                </p>
            </div>
            
            <div>
                <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">Duration</h3>
                <p class="text-lg font-semibold text-gray-900 dark:text-white">
                    @if($attempt->time_spent_seconds)
                        {{ gmdate('H:i:s', $attempt->time_spent_seconds) }}
                    @else
                        N/A
                    @endif
                </p>
            </div>
        </div>
    </div>

    <!-- Answers Section -->
    @if($attempt->answers && count($attempt->answers) > 0)
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white">User Answers</h2>
            </div>
            <div class="divide-y divide-gray-200 dark:divide-gray-700">
                @foreach($attempt->answers as $index => $answer)
                    <div class="p-6">
                        <div class="flex items-start justify-between mb-4">
                            <div class="flex-1">
                                <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-2">
                                    Question {{ $index + 1 }}
                                </h3>
                                <p class="text-gray-900 dark:text-white">
                                    {{ $answer['question_text'] ?? 'Question text not available' }}
                                </p>
                            </div>
                            @if(isset($answer['is_correct']))
                                @if($answer['is_correct'])
                                    <span class="ml-4 px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800 dark:bg-green-900/20 dark:text-green-400">
                                        Correct
                                    </span>
                                @else
                                    <span class="ml-4 px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800 dark:bg-red-900/20 dark:text-red-400">
                                        Incorrect
                                    </span>
                                @endif
                            @endif
                        </div>
                        
                        <!-- User's Answer -->
                        <div class="mt-4">
                            <h4 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-2">User's Answer:</h4>
                            <div class="bg-gray-50 dark:bg-gray-700/50 rounded-md p-3">
                                @if(isset($answer['selected_option']))
                                    <p class="text-gray-900 dark:text-white">
                                        {{ $answer['selected_option'] }}
                                    </p>
                                @elseif(isset($answer['answer_text']))
                                    <p class="text-gray-900 dark:text-white">
                                        {{ $answer['answer_text'] }}
                                    </p>
                                @else
                                    <p class="text-gray-500 dark:text-gray-400">
                                        No answer provided
                                    </p>
                                @endif
                            </div>
                        </div>
                        
                        <!-- Correct Answer (if available) -->
                        @if(isset($answer['correct_answer']) && !$answer['is_correct'])
                            <div class="mt-4">
                                <h4 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-2">Correct Answer:</h4>
                                <div class="bg-green-50 dark:bg-green-900/20 rounded-md p-3">
                                    <p class="text-green-900 dark:text-green-400">
                                        {{ $answer['correct_answer'] }}
                                    </p>
                                </div>
                            </div>
                        @endif
                        
                        <!-- Explanation (if available) -->
                        @if(isset($answer['explanation']))
                            <div class="mt-4">
                                <h4 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-2">Explanation:</h4>
                                <div class="bg-blue-50 dark:bg-blue-900/20 rounded-md p-3">
                                    <p class="text-blue-900 dark:text-blue-400">
                                        {{ $answer['explanation'] }}
                                    </p>
                                </div>
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>
    @else
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-12 text-center">
            <div class="flex flex-col items-center">
                <svg class="w-12 h-12 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                <p class="text-lg font-medium text-gray-900 dark:text-white mb-2">No Answers Available</p>
                <p class="text-sm text-gray-500 dark:text-gray-400">
                    This quiz attempt doesn't have any recorded answers yet.
                </p>
            </div>
        </div>
    @endif
</div>
@endsection
