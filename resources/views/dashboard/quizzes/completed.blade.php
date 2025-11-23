@extends('layouts.dashboard')

@section('title', __('Completed Quizzes'))

@push('styles')
<style>
    .attempt-card {
        transition: all 0.2s ease;
    }
    .attempt-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
    }
    .progress-ring {
        transform: rotate(-90deg);
    }
    .progress-ring__circle {
        transition: stroke-dashoffset 0.5s;
        transform-origin: 50% 50%;
    }
</style>
@endpush

@section('dashboard-content')
    <div class="min-h-screen bg-gray-50">
        <!-- Header -->
        <div class="bg-white border-b border-gray-100">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="py-6 flex items-center justify-between">
                    <div>
                        <h1 class="text-2xl font-semibold text-gray-900">{{ __('Completed Quizzes') }}</h1>
                        <p class="mt-1 text-sm text-gray-500">
                            {{ __('Review your completed quizzes and track your progress') }}
                        </p>
                    </div>
                    <div class="flex items-center space-x-2">
                        <span class="inline-flex items-center px-3 py-1.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                            {{ $quizzes->total() }} {{ __('Quizzes Completed') }}
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            @if($quizzes->count() > 0)
                <div class="space-y-6">
                    @foreach($quizzes as $quiz)
                        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                            <div class="md:flex">
                                <!-- Quiz Image/Icon -->
                                <div class="md:w-1/4 bg-gradient-to-br from-blue-500 to-indigo-600 p-6 flex flex-col items-center justify-center">
                                    <div class="relative w-24 h-24 mb-4">
                                        <svg class="w-full h-full text-white" viewBox="0 0 100 100">
                                            <circle
                                                class="text-blue-200"
                                                stroke-width="8"
                                                stroke="currentColor"
                                                fill="transparent"
                                                r="40"
                                                cx="50"
                                                cy="50"
                                            />
                                            <circle
                                                class="progress-ring__circle text-white"
                                                stroke-width="8"
                                                stroke-linecap="round"
                                                stroke="currentColor"
                                                fill="transparent"
                                                r="40"
                                                cx="50"
                                                cy="50"
                                                stroke-dasharray="{{ 2 * pi() * 40 }}"
                                                stroke-dashoffset="{{ 2 * pi() * 40 * (1 - ($quiz->best_score / $quiz->questions_count)) }}"
                                            />
                                            <text x="50" y="55" text-anchor="middle" class="text-2xl font-bold fill-current">
                                                {{ round(($quiz->best_score / $quiz->questions_count) * 100) }}%
                                            </text>
                                        </svg>
                                    </div>
                                    <h3 class="text-lg font-medium text-white text-center">{{ $quiz->title }}</h3>
                                    <p class="text-blue-100 text-sm mt-1">
                                        {{ $quiz->attempts_count }} {{ __('attempts') }} • {{ $quiz->questions_count }} {{ __('questions') }}
                                    </p>
                                </div>

                                <!-- Quiz Details -->
                                <div class="md:w-3/4 p-6">
                                    <div class="flex justify-between items-start">
                                        <div>
                                            <h2 class="text-xl font-semibold text-gray-900">{{ $quiz->title }}</h2>
                                            <p class="text-gray-600 text-sm mt-1">{{ $quiz->description }}</p>
                                        </div>
                                        <div class="flex space-x-2">
                                            <a href="{{ route('dashboard.quizzes.show', ['locale' => app()->getLocale(), 'quiz' => $quiz->id]) }}" 
                                               class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                                {{ __('Try Again') }}
                                            </a>
                                        </div>
                                    </div>

                                    <!-- Stats -->
                                    <div class="mt-6 grid grid-cols-1 md:grid-cols-3 gap-4">
                                        <div class="bg-gray-50 p-4 rounded-lg">
                                            <p class="text-sm font-medium text-gray-500">{{ __('Best Score') }}</p>
                                            <p class="text-2xl font-semibold text-gray-900">{{ $quiz->best_score }}/{{ $quiz->questions_count }}</p>
                                            <p class="text-xs text-gray-500 mt-1">{{ round(($quiz->best_score / $quiz->questions_count) * 100) }}%</p>
                                        </div>
                                        <div class="bg-gray-50 p-4 rounded-lg">
                                            <p class="text-sm font-medium text-gray-500">{{ __('Average Score') }}</p>
                                            <p class="text-2xl font-semibold text-gray-900">{{ $quiz->average_score }}/{{ $quiz->questions_count }}</p>
                                            <p class="text-xs text-gray-500 mt-1">{{ round(($quiz->average_score / $quiz->questions_count) * 100) }}%</p>
                                        </div>
                                        <div class="bg-gray-50 p-4 rounded-lg">
                                            <p class="text-sm font-medium text-gray-500">{{ __('Last Attempt') }}</p>
                                            <p class="text-2xl font-semibold text-gray-900">{{ $quiz->last_attempt->score ?? 0 }}/{{ $quiz->questions_count }}</p>
                                            <p class="text-xs text-gray-500 mt-1">
                                                @if($quiz->last_attempt && $quiz->last_attempt->completed_at)
                                                    {{ $quiz->last_attempt->completed_at->diffForHumans() }}
                                                @else
                                                    N/A
                                                @endif
                                            </p>
                                        </div>
                                    </div>

                                    <!-- Attempt History -->
                                    <div class="mt-6">
                                        <h3 class="text-sm font-medium text-gray-900 mb-3">{{ __('Attempt History') }}</h3>
                                        <div class="space-y-2">
                                            @forelse($quiz->attempts as $attempt)
                                                <div 
                                                    onclick="showAttemptDetails(event, {{ $quiz->id }}, {{ $attempt->id }})" 
                                                    class="attempt-card bg-gray-50 rounded-lg p-3 border border-gray-200 cursor-pointer hover:bg-blue-50 transition-colors"
                                                    data-quiz-id="{{ $quiz->id }}"
                                                    data-attempt-id="{{ $attempt->id }}">
                                                    <div class="flex items-center justify-between">
                                                        <div class="flex items-center">
                                                            <div class="flex-shrink-0 h-10 w-10 rounded-full bg-blue-100 flex items-center justify-center">
                                                                <span class="text-blue-600 font-medium">
                                                                    {{ round(($attempt->score / $quiz->questions_count) * 100) }}%
                                                                </span>
                                                            </div>
                                                            <div class="ml-3">
                                                                <p class="text-sm font-medium text-gray-900">
                                                                    {{ $attempt->completed_at ? $attempt->completed_at->format('M d, Y h:i A') : 'In Progress' }}
                                                                </p>
                                                                <p class="text-xs text-gray-500">
                                                                    {{ __('Score:') }} {{ $attempt->score }}%
                                                                    • {{ $attempt->time_spent_seconds ? gmdate('i\m s\s', $attempt->time_spent_seconds) : 'N/A' }}
                                                                    • <a href="#" class="text-blue-600 hover:text-blue-800" onclick="event.stopPropagation(); showAttemptDetails(event, {{ $quiz->id }}, {{ $attempt->id }}); return false;">
                                                                        {{ __('View Details') }}
                                                                    </a>
                                                                </p>
                                                            </div>
                                                        </div>
                                                        <div class="text-right">
                                                            <p class="text-sm font-medium text-gray-900">
                                                                {{ $attempt->score }}/{{ $quiz->questions_count }}
                                                            </p>
                                                            <p class="text-xs text-gray-500">
                                                                {{ formatDuration($attempt->time_spent_seconds ?? 0) }}
                                                            </p>
                                                        </div>
                                                    </div>
                                                </div>
                                            @empty
                                                <p class="text-sm text-gray-500">{{ __('No attempt history available.') }}</p>
                                            @endforelse
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Pagination -->
                @if($quizzes->hasPages())
                    <div class="mt-8">
                        {{ $quizzes->links('vendor.pagination.simple-tailwind') }}
                    </div>
                @endif
            @else
                <!-- Empty State -->
                <div class="text-center py-16 bg-white rounded-2xl shadow-sm border border-gray-100">
                    <svg class="mx-auto h-16 w-16 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    <h3 class="mt-2 text-lg font-medium text-gray-900">{{ __('No completed quizzes yet') }}</h3>
                    <p class="mt-1 text-sm text-gray-500">
                        {{ __('You haven\'t completed any quizzes yet. Complete a quiz to see your results here.') }}
                    </p>
                    <div class="mt-6">
                        <a href="{{ route('dashboard.quizzes.index', app()->getLocale()) }}" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            {{ __('Browse Quizzes') }}
                        </a>
                    </div>
                </div>
            @endif
        </main>
    </div>

    <!-- Attempt Details Modal -->
    <div id="attemptModal" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
            <div class="inline-block align-bottom bg-white rounded-lg px-4 pt-5 pb-4 text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl sm:w-full sm:p-6">
                <div>
                    <div class="flex justify-between items-start">
                        <div>
                            <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">
                                {{ __('Attempt Details') }}
                            </h3>
                            <p class="mt-1 text-sm text-gray-500" id="attempt-date"></p>
                        </div>
                        <button type="button" onclick="closeModal()" class="bg-white rounded-md text-gray-400 hover:text-gray-500 focus:outline-none">
                            <span class="sr-only">Close</span>
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                    <div class="mt-4">
                        <div class="bg-blue-50 p-4 rounded-lg mb-4">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <svg class="h-5 w-5 text-blue-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <h4 class="text-sm font-medium text-blue-800" id="attempt-score"></h4>
                                    <div class="mt-1">
                                        <div class="w-full bg-gray-200 rounded-full h-2.5">
                                            <div id="attempt-progress" class="bg-blue-600 h-2.5 rounded-full" style="width: 0%"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="space-y-4">
                            <div class="grid grid-cols-2 gap-4">
                                <div class="bg-gray-50 p-3 rounded-lg">
                                    <p class="text-xs font-medium text-gray-500">{{ __('Time Spent') }}</p>
                                    <p id="time-spent" class="text-sm font-medium text-gray-900"></p>
                                </div>
                                <div class="bg-gray-50 p-3 rounded-lg">
                                    <p class="text-xs font-medium text-gray-500">{{ __('Correct Answers') }}</p>
                                    <p id="correct-answers" class="text-sm font-medium text-gray-900"></p>
                                </div>
                            </div>

                            <div id="answers-container" class="space-y-3">
                                <!-- Answers will be populated here by JavaScript -->
                            </div>
                        </div>
                    </div>
                </div>
                <div class="mt-5 sm:mt-6">
                    <button type="button" onclick="closeModal()" class="inline-flex justify-center w-full rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:text-sm">
                        {{ __('Close') }}
                    </button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    function showAttemptDetails(event, quizId, attemptId) {
        // Prevent default behavior
        event.preventDefault();
        event.stopPropagation();
        
        // Show loading state
        const answersContainer = document.getElementById('answers-container');
        answersContainer.innerHTML = `
            <div class="flex justify-center py-8">
                <svg class="animate-spin h-8 w-8 text-blue-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <span class="ml-2 text-gray-700">Loading attempt details...</span>
            </div>
        `;

        // Show modal
        const modal = document.getElementById('attemptModal');
        modal.classList.remove('hidden');
        document.body.classList.add('overflow-hidden');
        
        // Get the locale
        const locale = '{{ app()->getLocale() }}';
        const url = `/${locale}/dashboard/quizzes/attempts/${attemptId}`;

        // Fetch attempt details
        fetch(url, {
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            credentials: 'same-origin'
        })
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            if (!data.success) {
                throw new Error(data.message || 'Failed to load attempt details');
            }

            const attempt = data.attempt;
            const totalQuestions = data.total_questions;
            const percentage = data.percentage;
            const correctCount = data.correct_count;

            // Update modal content
            document.getElementById('attempt-date').textContent = data.formatted_date;
            document.getElementById('attempt-score').textContent = 
                `${correctCount} of ${totalQuestions} correct (${percentage}%)`;
            document.getElementById('attempt-progress').style.width = `${percentage}%`;
            document.getElementById('time-spent').textContent = data.time_spent;
            document.getElementById('correct-answers').textContent = 
                `${correctCount} of ${totalQuestions}`;

                // Populate answers
                const answersContainer = document.getElementById('answers-container');
                if (attempt.user_answers && attempt.user_answers.length > 0) {
                    let answersHtml = '';
                    
                    // Sort answers by question ID or index if available
                    const sortedAnswers = [...attempt.user_answers].sort((a, b) => {
                        return (a.question?.id || 0) - (b.question?.id || 0);
                    });
                    
                    sortedAnswers.forEach((answer, index) => {
                        const isCorrect = answer.is_correct;
                        const questionText = answer.question?.text || 'Question not available';
                        const questionNumber = index + 1;
                        
                        // Find the correct answer if this one is incorrect
                        let correctAnswerText = '';
                        if (!isCorrect && answer.question?.options) {
                            const correctOption = answer.question.options.find(opt => opt.is_correct);
                            if (correctOption) {
                                correctAnswerText = correctOption.text;
                            }
                        }
                        
                        answersHtml += `
                            <div class="border rounded-lg overflow-hidden mb-3 ${isCorrect ? 'border-green-100 bg-green-50' : 'border-red-100 bg-red-50'}">
                                <div class="p-4">
                                    <div class="flex items-start">
                                        <div class="flex-shrink-0 mt-0.5">
                                            <div class="h-6 w-6 rounded-full flex items-center justify-center ${isCorrect ? 'bg-green-100 text-green-600' : 'bg-red-100 text-red-600'}">
                                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    ${isCorrect ? 
                                                        '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />' :
                                                        '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />'
                                                    }
                                                </svg>
                                            </div>
                                        </div>
                                        <div class="ml-3 flex-1">
                                            <div class="flex items-center justify-between">
                                                <h4 class="text-sm font-medium ${isCorrect ? 'text-green-800' : 'text-red-800'}">
                                                    Question ${questionNumber}: ${questionText}
                                                </h4>
                                                <span class="text-xs px-2 py-1 rounded-full ${isCorrect ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'}">
                                                    ${isCorrect ? 'Correct' : 'Incorrect'}
                                                </span>
                                            </div>
                                            
                                            ${answer.selected_option ? `
                                                <div class="mt-2">
                                                    <p class="text-xs font-medium text-gray-700">Your answer:</p>
                                                    <p class="text-sm ${isCorrect ? 'text-green-700' : 'text-red-700'} font-medium">
                                                        ${answer.selected_option.text}
                                                    </p>
                                                </div>
                                            ` : ''}
                                            
                                            ${!isCorrect && correctAnswerText ? `
                                                <div class="mt-2">
                                                    <p class="text-xs font-medium text-gray-700">Correct answer:</p>
                                                    <p class="text-sm text-green-700 font-medium">
                                                        ${correctAnswerText}
                                                    </p>
                                                </div>
                                            ` : ''}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        `;
                    });
                    
                    answersContainer.innerHTML = answersHtml || '<p class="text-sm text-gray-500">No answer details available.</p>';
                } else {
                    answersContainer.innerHTML = '<div class="text-center py-4 text-gray-500">No answer details available for this attempt.</div>';
                }
            }
        })
        .catch(error => {
            console.error('Error fetching attempt details:', error);
            const errorMessage = error.message || 'An error occurred while loading attempt details.';
            document.getElementById('answers-container').innerHTML = `
                <div class="bg-red-50 border-l-4 border-red-400 p-4">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-red-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm text-red-700">
                                ${errorMessage}
                            </p>
                            <div class="mt-2">
                                <button onclick="window.location.reload()" class="text-sm font-medium text-red-700 hover:text-red-600 focus:outline-none focus:underline transition duration-150 ease-in-out">
                                    Try again →
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            `;
        });
    }

    function closeModal() {
        document.getElementById('attemptModal').classList.add('hidden');
        document.body.classList.remove('overflow-hidden');
    }

    // Close modal when clicking outside
    document.getElementById('attemptModal').addEventListener('click', function(e) {
        if (e.target === this) {
            closeModal();
        }
    });

    // Close with Escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeModal();
        }
    });
</script>
@endpush

@push('modals')
<div id="attemptModal" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true" onclick="closeModal()"></div>
        
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
        
        <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-3xl sm:w-full">
            <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                <div class="sm:flex sm:items-start">
                    <div class="w-full">
                        <div class="flex justify-between items-start">
                            <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">
                                Quiz Attempt Details
                            </h3>
                            <button type="button" 
                                    class="text-gray-400 hover:text-gray-500 focus:outline-none"
                                    onclick="closeModal()">
                                <span class="sr-only">Close</span>
                                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>
                        
                        <div class="mt-4">
                            <div class="bg-gray-50 p-4 rounded-lg">
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                    <div>
                                        <p class="text-xs font-medium text-gray-500">Date</p>
                                        <p id="attempt-date" class="text-sm font-medium text-gray-900">-</p>
                                    </div>
                                    <div>
                                        <p class="text-xs font-medium text-gray-500">Score</p>
                                        <p id="attempt-score" class="text-sm font-medium text-gray-900">-</p>
                                    </div>
                                    <div>
                                        <p class="text-xs font-medium text-gray-500">Time Spent</p>
                                        <p id="time-spent" class="text-sm font-medium text-gray-900">-</p>
                                    </div>
                                </div>
                                
                                <div class="mt-4">
                                    <div class="flex justify-between text-sm font-medium text-gray-500 mb-1">
                                        <span>Progress</span>
                                        <span id="correct-answers">-</span>
                                    </div>
                                    <div class="w-full bg-gray-200 rounded-full h-2.5">
                                        <div id="attempt-progress" class="bg-blue-600 h-2.5 rounded-full" style="width: 0%"></div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="mt-6">
                                <h4 class="text-sm font-medium text-gray-900 mb-3">Questions & Answers</h4>
                                <div id="answers-container" class="space-y-3 max-h-96 overflow-y-auto pr-2">
                                    <!-- Will be populated by JavaScript -->
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                <button type="button" 
                        class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm"
                        onclick="closeModal()">
                    Close
                </button>
            </div>
        </div>
    </div>
</div>
@endpush