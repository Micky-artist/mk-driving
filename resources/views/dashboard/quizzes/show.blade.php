@extends('layouts.dashboard')

@push('styles')
<style>
    .question-container {
        display: none;
        animation: fadeIn 0.3s ease-in-out;
    }
    .question-container.active {
        display: block;
    }
    .answer-option {
        transition: all 0.2s ease;
    }
    .answer-option.selected {
        border-color: #3b82f6;
        background-color: #eff6ff;
    }
    .answer-option.correct {
        border-color: #10b981;
        background-color: #ecfdf5;
    }
    .answer-option.incorrect {
        border-color: #ef4444;
        background-color: #fef2f2;
    }
    .answer-feedback {
        display: none;
        margin-top: 1rem;
        padding: 0.75rem;
        border-radius: 0.5rem;
    }
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }
    #progressBar {
        transition: width 0.3s ease;
    }
    
    /* Locked answer styles */
    .answer-locked .answer-option {
        opacity: 0.7;
        cursor: not-allowed;
    }

    .answer-locked .answer-option.selected {
        opacity: 1;
        border-left: 4px solid #3b82f6;
        background-color: #f8fafc;
    }

    .answer-locked input[type="radio"] {
        cursor: not-allowed;
    }
    
    .answer-locked .answer-option.correct {
        background-color: #ecfdf5;
        border-color: #10b981;
    }
    
    .answer-locked .answer-option.incorrect {
        background-color: #fef2f2;
        border-color: #ef4444;
    }
    
    .score-badge {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.25rem 0.75rem;
        border-radius: 0.375rem;
        font-size: 0.875rem;
        font-weight: 500;
        background-color: #f3f4f6;
    }
    
    .score-correct {
        color: #065f46;
    }
    
    .score-incorrect {
        color: #991b1b;
    }
    
    .upsell-banner {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border-radius: 0.75rem;
        padding: 1.5rem;
        color: white;
        margin-bottom: 1.5rem;
    }
    
    .stat-card {
        background: white;
        border-radius: 0.75rem;
        padding: 1.5rem;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        text-align: center;
    }
    
    .stat-value {
        font-size: 2rem;
        font-weight: bold;
        margin-bottom: 0.25rem;
    }
    
    .stat-label {
        color: #6b7280;
        font-size: 0.875rem;
    }
    
    .modal-backdrop {
        backdrop-filter: blur(4px);
    }
    
    .pulse-animation {
        animation: pulse 2s infinite;
    }
    
    @keyframes pulse {
        0%, 100% { opacity: 1; }
        50% { opacity: 0.8; }
    }
    
    .gradient-border {
        position: relative;
        padding: 2px;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border-radius: 0.5rem;
    }
    
    .gradient-border-inner {
        background: white;
        border-radius: 0.4rem;
        padding: 1rem;
    }
    
    .bookmark-btn {
        transition: all 0.2s ease;
    }
    
    .bookmark-btn:hover {
        transform: scale(1.1);
    }
    
    .bookmark-btn.bookmarked {
        color: #f59e0b;
        fill: #f59e0b;
    }
    
    .streak-badge {
        background: linear-gradient(135deg, #fbbf24 0%, #f59e0b 100%);
        color: white;
        padding: 0.25rem 0.75rem;
        border-radius: 9999px;
        font-size: 0.875rem;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        gap: 0.25rem;
    }
    
    .achievement-popup {
        position: fixed;
        top: 1rem;
        right: 1rem;
        background: white;
        border-radius: 0.5rem;
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
        padding: 1rem;
        z-index: 100;
        animation: slideInRight 0.3s ease-out;
        max-width: 20rem;
    }
    
    @keyframes slideInRight {
        from {
            transform: translateX(100%);
            opacity: 0;
        }
        to {
            transform: translateX(0);
            opacity: 1;
        }
    }
</style>
@endpush

@section('dashboard-content')
<div class="py-6">
    <div class="max-w-4xl mx-auto">
        <!-- Quiz Header -->
        <div class="bg-white rounded-xl shadow-md overflow-hidden mb-6">
            <div class="p-6">
                <div class="flex items-start justify-between mb-2">
                    <div class="flex-1">
                        <h1 class="text-2xl font-bold text-gray-900 mb-2">{{ $quiz->getTranslation('title', app()->getLocale()) }}</h1>
                        <p class="text-gray-600 mb-4">{{ $quiz->getTranslation('description', app()->getLocale()) }}</p>
                    </div>
                    <button type="button" 
                            id="bookmarkQuiz" 
                            class="bookmark-btn p-2 hover:bg-gray-100 rounded-lg transition-colors duration-200 {{ $isBookmarked ? 'text-yellow-500' : 'text-gray-400' }}"
                            data-quiz-id="{{ $quiz->id }}"
                            title="{{ $isBookmarked ? 'Remove from bookmarks' : 'Add to bookmarks' }}">
                        <svg class="w-6 h-6" fill="{{ $isBookmarked ? 'currentColor' : 'none' }}" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z"></path>
                        </svg>
                    </button>
                </div>
                
                <!-- User Stats Bar -->
                <div class="flex flex-wrap items-center gap-4 mb-4 pb-4 border-b border-gray-200">
                    <div class="flex items-center text-sm text-gray-600">
                        <svg class="w-5 h-5 mr-1.5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <span>{{ $quiz->time_limit_minutes }} minutes</span>
                    </div>
                    <div class="flex items-center text-sm text-gray-600">
                        <svg class="w-5 h-5 mr-1.5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        <span>{{ $quiz->questions->count() }} questions</span>
                    </div>
                    @if($userAttempts > 0)
                    <div class="flex items-center text-sm text-gray-600">
                        <svg class="w-5 h-5 mr-1.5 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                        </svg>
                        <span>Attempt {{ $userAttempts + 1 }}</span>
                    </div>
                    @endif
                    @if($bestScore)
                    <div class="flex items-center text-sm text-gray-600">
                        <svg class="w-5 h-5 mr-1.5 text-yellow-500" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                        </svg>
                        <span>Best: {{ $bestScore }}%</span>
                    </div>
                    @endif
                    @if($userStreak > 0)
                    <span class="streak-badge">
                        🔥 {{ $userStreak }} day streak
                    </span>
                    @endif
                </div>
                
                <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                    <div class="w-full">
                        <div class="flex justify-between items-center text-sm text-gray-600 mb-1 flex-wrap gap-2">
                            <div class="flex items-center gap-3">
                                <span>Question <span id="currentQuestion">1</span> of {{ $quiz->questions->count() }}</span>
                                <span class="score-badge">
                                    <span class="score-correct">✓ <span id="correctCount">0</span></span>
                                    <span class="text-gray-400">|</span>
                                    <span class="score-incorrect">✗ <span id="incorrectCount">0</span></span>
                                </span>
                                
                                <!-- Auto Next Toggle -->
                                <label class="flex items-center text-sm text-gray-700 cursor-pointer bg-gray-50 px-3 py-1 rounded-lg border border-gray-200 hover:bg-gray-100 transition-colors">
                                    <input type="checkbox" id="autoNextToggle" checked class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                                    <span class="ml-2 font-medium">Auto Next</span>
                                </label>
                            </div>
                            <div class="flex items-center gap-3">
                                <span id="timer" class="font-mono font-semibold text-blue-600">{{ $quiz->time_limit_minutes }}:00</span>
                                <button type="button" id="pauseQuiz" class="px-3 py-1 text-sm text-gray-600 hover:text-gray-800 hover:bg-gray-100 rounded transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 9v6m4-6v6m7-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                </button>
                                <button type="button" id="resetQuiz" class="px-3 py-1 text-sm text-red-600 hover:text-red-800 hover:bg-red-50 rounded transition-colors">
                                    Reset Quiz
                                </button>
                            </div>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-2.5">
                            <div id="progressBar" class="bg-blue-600 h-2.5 rounded-full transition-all duration-300" style="width: 0%"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quiz Content -->
        <form id="quizForm" action="{{ route('dashboard.quizzes.submit', ['locale' => app()->getLocale(), 'quiz' => $quiz->id, 'attempt' => $attempt]) }}" method="POST">
            @csrf
            <input type="hidden" name="quiz_id" value="{{ $quiz->id }}">
            <input type="hidden" name="attempt_id" value="{{ $attempt->id }}">
            <input type="hidden" name="time_taken" id="timeTaken">
            <input type="hidden" name="end_time" id="endTime">
            <input type="hidden" name="paused_time" id="pausedTime" value="0">
            
            @foreach($quiz->questions as $index => $question)
                <div class="question-container bg-white rounded-xl shadow-sm p-6 mb-6" 
                     id="question-{{ $question->id }}" 
                     data-question-id="{{ $question->id }}"
                     data-correct-answer="{{ $question->correct_option_id }}">
                    
                    <div class="mb-6">
                        <div class="flex items-start justify-between mb-4">
                            <h3 class="text-lg font-medium text-gray-900 flex-1">
                                <span class="font-semibold">Question {{ $index + 1 }}:</span> 
                                {{ $question->getTranslation('text', app()->getLocale()) }}
                            </h3>
                            <button type="button" class="flag-question ml-3 p-2 hover:bg-gray-100 rounded-lg transition-colors" data-question-id="{{ $question->id }}">
                                <svg class="w-5 h-5 text-gray-400 hover:text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 21v-4m0 0V5a2 2 0 012-2h6.5l1 1H21l-3 6 3 6h-8.5l-1-1H5a2 2 0 00-2 2zm9-13.5V9"></path>
                                </svg>
                            </button>
                        </div>
                        
                        @if($question->image_path)
                            <div class="mb-4">
                                <img src="{{ asset('storage/' . $question->image_path) }}" 
                                     alt="Question Image" 
                                     class="max-w-full h-auto rounded-lg border border-gray-200">
                            </div>
                        @endif
                        
                        <div class="space-y-3 mb-4 question-options">
                            @foreach($question->options as $option)
                                <label class="answer-option block p-4 border border-gray-200 rounded-lg cursor-pointer hover:bg-gray-50 transition-colors">
                                    <div class="flex items-center">
                                        <input type="radio" 
                                               name="answers[{{ $question->id }}]" 
                                               value="{{ $option->id }}" 
                                               class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300">
                                        <span class="ml-3 text-gray-700">
                                            {{ $option->getTranslation('option_text', app()->getLocale()) }}
                                        </span>
                                    </div>
                                </label>
                            @endforeach
                        </div>
                        
                        <div class="answer-feedback"></div>
                        
                        @if($question->explanation)
                        <div class="explanation hidden mt-4 p-4 bg-blue-50 border-l-4 border-blue-500 rounded">
                            <p class="text-sm text-blue-900">
                                <span class="font-semibold">Explanation:</span> 
                                {{ $question->getTranslation('explanation', app()->getLocale()) }}
                            </p>
                        </div>
                        @endif
                    </div>
                    
                    <div class="flex justify-between pt-4 border-t border-gray-100">
                        <button type="button" 
                                class="btn-prev px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors {{ $loop->first ? 'invisible' : '' }}">
                            Previous
                        </button>
                        
                        @if($loop->last)
                            <button type="submit" 
                                    class="ml-auto px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                                Submit Quiz
                            </button>
                        @else
                            <button type="button" 
                                    class="btn-next px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors opacity-50 cursor-not-allowed ml-auto"
                                    disabled>
                                Next
                            </button>
                        @endif
                    </div>
                </div>
            @endforeach
        </form>
    </div>
</div>

<!-- Pause Modal -->
<div id="pauseModal" class="hidden fixed inset-0 bg-black bg-opacity-50 modal-backdrop items-center justify-center p-4 z-50">
    <div class="bg-white rounded-xl max-w-md w-full p-6">
        <div class="text-center">
            <div class="inline-flex items-center justify-center w-16 h-16 bg-yellow-100 rounded-full mb-4">
                <svg class="w-8 h-8 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 9v6m4-6v6m7-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
            <h2 class="text-2xl font-bold mb-2 text-gray-900">Quiz Paused</h2>
            <p class="text-gray-600 mb-6">Take a break. Your progress is saved.</p>
            <div class="flex flex-col gap-3">
                <button id="resumeQuiz" class="w-full px-6 py-3 bg-blue-600 text-white font-semibold rounded-lg hover:bg-blue-700 transition-colors">
                    Resume Quiz
                </button>
                <a href="{{ route('dashboard.quizzes.index', ['locale' => app()->getLocale()]) }}" class="w-full px-6 py-3 bg-gray-100 text-gray-700 font-semibold rounded-lg hover:bg-gray-200 transition-colors text-center">
                    Exit Quiz
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Results Modal -->
<div id="resultsModal" class="hidden fixed inset-0 bg-black bg-opacity-50 modal-backdrop flex items-center justify-center p-4 z-50">
    <div class="bg-white rounded-xl max-w-2xl w-full p-6 max-h-[90vh] overflow-y-auto">
        <div class="text-center mb-6">
            <div class="inline-flex items-center justify-center w-20 h-20 bg-blue-100 rounded-full mb-4">
                <svg class="w-10 h-10 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
            <h2 class="text-3xl font-bold mb-2 text-gray-900">Quiz Completed!</h2>
            <div class="text-5xl font-bold text-blue-600 mb-2" id="resultsScore">0%</div>
            <p class="text-gray-600 mb-4">
                You got <span id="correctAnswers" class="font-semibold text-green-600">0</span> out of 
                <span class="font-semibold">{{ $quiz->questions->count() }}</span> questions correct
            </p>
            
            <!-- Pass/Fail Badge -->
            <div id="passBadge" class="hidden inline-flex items-center px-4 py-2 rounded-full text-sm font-semibold mb-4">
                <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                </svg>
                <span id="passText"></span>
            </div>
        </div>

        <!-- Stats Grid -->
        <div class="grid grid-cols-4 gap-4 mb-6">
            <div class="stat-card">
                <div class="stat-value text-green-600" id="statCorrect">0</div>
                <div class="stat-label">Correct</div>
            </div>
            <div class="stat-card">
                <div class="stat-value text-red-600" id="statIncorrect">0</div>
                <div class="stat-label">Incorrect</div>
            </div>
            <div class="stat-card">
                <div class="stat-value text-blue-600" id="statTimeTaken">0:00</div>
                <div class="stat-label">Time</div>
            </div>
            <div class="stat-card">
                <div class="stat-value text-purple-600" id="statAccuracy">0%</div>
                <div class="stat-label">Accuracy</div>
            </div>
        </div>

        <!-- Performance Comparison -->
        @if($bestScore)
        <div class="mb-6 p-4 bg-gray-50 rounded-lg">
            <h3 class="font-semibold text-gray-900 mb-3">Your Progress</h3>
            <div class="space-y-2">
                <div class="flex justify-between text-sm">
                    <span class="text-gray-600">Current Score:</span>
                    <span class="font-semibold" id="currentScoreComparison">0%</span>
                </div>
                <div class="flex justify-between text-sm">
                    <span class="text-gray-600">Previous Best:</span>
                    <span class="font-semibold">{{ $bestScore }}%</span>
                </div>
                <div class="flex justify-between text-sm">
                    <span class="text-gray-600">Improvement:</span>
                    <span class="font-semibold" id="improvement">0%</span>
                </div>
            </div>
        </div>
        @endif

        <!-- Action Buttons -->
        <div class="flex flex-col sm:flex-row gap-3">
            <button id="reviewAnswers" class="flex-1 px-6 py-3 bg-blue-600 text-white font-semibold rounded-lg hover:bg-blue-700 transition-colors">
                Review Answers
            </button>
            <button id="resetFromResults" class="flex-1 px-6 py-3 bg-gray-100 text-gray-700 font-semibold rounded-lg hover:bg-gray-200 transition-colors">
                Retake Quiz
            </button>
            <a href="{{ route('dashboard.quizzes.index', ['locale' => app()->getLocale()]) }}" class="flex-1 px-6 py-3 bg-gray-100 text-gray-700 font-semibold rounded-lg hover:bg-gray-200 transition-colors text-center">
                Back to Quizzes
            </a>
        </div>

        <!-- Share Section -->
        <div class="mt-6 pt-6 border-t border-gray-200">
            <p class="text-center text-sm text-gray-600 mb-3">Share your achievement</p>
            <div class="flex justify-center gap-3">
                <button onclick="shareOnTwitter()" class="px-4 py-2 bg-blue-400 text-white rounded-lg hover:bg-blue-500 transition-colors">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M23 3a10.9 10.9 0 01-3.14 1.53 4.48 4.48 0 00-7.86 3v1A10.66 10.66 0 013 4s-4 9 5 13a11.64 11.64 0 01-7 2c9 5 20 0 20-11.5a4.5 4.5 0 00-.08-.83A7.72 7.72 0 0023 3z"></path>
                    </svg>
                </button>
                <button onclick="shareOnFacebook()" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M18 2h-3a5 5 0 00-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 011-1h3z"></path>
                    </svg>
                </button>
                <button onclick="copyResultLink()" class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                    </svg>
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Achievement Popup (will be shown dynamically) -->
<div id="achievementPopup" class="achievement-popup hidden">
    <div class="flex items-start gap-3">
        <div class="flex-shrink-0">
            <svg class="w-8 h-8 text-yellow-500" fill="currentColor" viewBox="0 0 20 20">
                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
            </svg>
        </div>
        <div class="flex-1">
            <h4 class="font-bold text-gray-900" id="achievementTitle">Achievement Unlocked!</h4>
            <p class="text-sm text-gray-600" id="achievementText"></p>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
(function() {
    'use strict';
    
    document.addEventListener('DOMContentLoaded', function() {
        // Time tracking variables
        let questionStartTime = Date.now();
        let currentQuestionId = null;
        
        // Store quiz configuration safely
        const QUIZ_CONFIG = {
            id: '{{ $quiz->id ?? "" }}',
            timeLimit: {{ $quiz->time_limit_minutes ?? 10 }},
            totalQuestions: {{ $quiz->questions->count() ?? 0 }},
            passingScore: {{ $quiz->passing_score ?? 70 }},
            bestScore: {{ $bestScore ?? 'null' }}
        };
        
        // Verify quiz data is available
        if (!QUIZ_CONFIG.id) {
            console.error('Quiz ID is missing!');
            alert('Error: Quiz data not loaded properly. Please refresh the page.');
            return;
        }
        
        console.log('Quiz initialized:', QUIZ_CONFIG);
        
        // Handle bookmark toggle
        const bookmarkBtn = document.getElementById('bookmarkQuiz');
        
        if (bookmarkBtn) {
            bookmarkBtn.addEventListener('click', async function() {
                const quizId = this.dataset.quizId;
                const icon = this.querySelector('svg');
                
                try {
                    const response = await fetch('/api/quizzes/' + quizId + '/bookmark', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        },
                        credentials: 'same-origin'
                    });
                    
                    const data = await response.json();
                    
                    if (response.ok) {
                        const isBookmarked = data.status === 'added';
                        
                        if (isBookmarked) {
                            this.classList.remove('text-gray-400');
                            this.classList.add('text-yellow-500');
                            this.title = 'Remove from bookmarks';
                            icon.setAttribute('fill', 'currentColor');
                            showToast('Quiz added to bookmarks', 'success');
                        } else {
                            this.classList.remove('text-yellow-500');
                            this.classList.add('text-gray-400');
                            this.title = 'Add to bookmarks';
                            icon.setAttribute('fill', 'none');
                            showToast('Quiz removed from bookmarks', 'info');
                        }
                    } else {
                        throw new Error(data.message || 'Failed to update bookmark');
                    }
                } catch (error) {
                    console.error('Error toggling bookmark:', error);
                    showToast(error.message || 'An error occurred', 'error');
                }
            });
        }
        
        // Show toast notification
        function showToast(message, type) {
            type = type || 'info';
            // Simple implementation - you can replace with a proper toast library
            const toast = document.createElement('div');
            toast.className = 'fixed top-4 right-4 px-6 py-3 rounded-lg shadow-lg text-white z-50 ' + 
                (type === 'success' ? 'bg-green-500' : type === 'error' ? 'bg-red-500' : 'bg-blue-500');
            toast.textContent = message;
            document.body.appendChild(toast);
            
            setTimeout(function() {
                toast.style.opacity = '0';
                toast.style.transition = 'opacity 0.3s';
                setTimeout(function() {
                    document.body.removeChild(toast);
                }, 300);
            }, 3000);
        }
        
        // DOM Elements
        const quizForm = document.getElementById('quizForm');
        const questionContainers = document.querySelectorAll('.question-container');
        const currentQuestionEl = document.getElementById('currentQuestion');
        const progressBar = document.getElementById('progressBar');
        const resultsModal = document.getElementById('resultsModal');
        const pauseModal = document.getElementById('pauseModal');
        const resultsScore = document.getElementById('resultsScore');
        const correctAnswersEl = document.getElementById('correctAnswers');
        const reviewButton = document.getElementById('reviewAnswers');
        const resetFromResultsButton = document.getElementById('resetFromResults');
        const correctCountEl = document.getElementById('correctCount');
        const incorrectCountEl = document.getElementById('incorrectCount');
        const statCorrectEl = document.getElementById('statCorrect');
        const statIncorrectEl = document.getElementById('statIncorrect');
        const statTimeTakenEl = document.getElementById('statTimeTaken');
        const statAccuracyEl = document.getElementById('statAccuracy');
        const pauseButton = document.getElementById('pauseQuiz');
        const resumeButton = document.getElementById('resumeQuiz');
        
        // Verify essential elements exist
        if (!quizForm || !questionContainers.length) {
            console.error('Essential quiz elements are missing!');
            alert('Error: Quiz interface not loaded properly. Please refresh the page.');
            return;
        }
        
        console.log('Found ' + questionContainers.length + ' questions');
        
        // Quiz state
        let currentQuestionIndex = 0;
        let userAnswers = {};
        let quizResults = {
            correct: 0,
            incorrect: 0
        };
        let timeLeft = QUIZ_CONFIG.timeLimit * 60;
        let timerInterval = null;
        let quizStartTime = null;
        let isQuizCompleted = false;
        let isPaused = false;
        let totalPausedTime = 0;
        let pauseStartTime = null;
        let flaggedQuestions = new Set();
        
        // Storage keys
        const STORAGE_KEYS = {
            progress: 'quizProgress_' + QUIZ_CONFIG.id,
            startTime: 'quizStartTime_' + QUIZ_CONFIG.id,
            autoNext: 'autoNextEnabled_' + QUIZ_CONFIG.id
        };
        
        // Initialize auto-next state from localStorage, default to true if not set
        let autoNextEnabled = localStorage.getItem(STORAGE_KEYS.autoNext) === null ? 
            true : localStorage.getItem(STORAGE_KEYS.autoNext) === 'true';
        
        // Initialize auto-next toggle
        const autoNextToggle = document.getElementById('autoNextToggle');
        if (autoNextToggle) {
            autoNextToggle.checked = autoNextEnabled;
            
            console.log('Auto-next initialized:', autoNextEnabled);
            
            autoNextToggle.addEventListener('change', function() {
                autoNextEnabled = this.checked;
                localStorage.setItem(STORAGE_KEYS.autoNext, String(this.checked));
                
                console.log('Auto-next changed to:', autoNextEnabled);
                
                // Clear any pending auto-advance when toggling off
                if (!autoNextEnabled && window.autoAdvanceTimeout) {
                    clearTimeout(window.autoAdvanceTimeout);
                    window.autoAdvanceTimeout = null;
                    console.log('Cleared pending auto-advance');
                }
            });
        }
        
        // Flag question functionality
        document.querySelectorAll('.flag-question').forEach(function(button) {
            button.addEventListener('click', function() {
                const questionId = this.dataset.questionId;
                const svg = this.querySelector('svg');
                
                if (flaggedQuestions.has(questionId)) {
                    flaggedQuestions.delete(questionId);
                    svg.classList.remove('text-yellow-500');
                    svg.classList.add('text-gray-400');
                } else {
                    flaggedQuestions.add(questionId);
                    svg.classList.remove('text-gray-400');
                    svg.classList.add('text-yellow-500');
                }
                
                saveProgress();
            });
        });
        
        // Pause functionality
        if (pauseButton) {
            pauseButton.addEventListener('click', function() {
                if (!isPaused) {
                    pauseQuiz();
                }
            });
        }
        
        if (resumeButton) {
            resumeButton.addEventListener('click', function() {
                resumeQuiz();
            });
        }
        
        function pauseQuiz() {
            isPaused = true;
            pauseStartTime = Date.now();
            
            // Stop timer
            if (timerInterval) {
                clearInterval(timerInterval);
                timerInterval = null;
            }
            
            // Show pause modal
            pauseModal.classList.remove('hidden');
            
            // Save progress
            saveProgress();
        }
        
        function resumeQuiz() {
            if (pauseStartTime) {
                totalPausedTime += Math.floor((Date.now() - pauseStartTime) / 1000);
                pauseStartTime = null;
            }
            
            isPaused = false;
            
            // Hide pause modal
            pauseModal.classList.add('hidden');
            
            // Restart timer
            startTimer();
        }
        
        // Reset the quiz
        function resetQuiz() {
            if (!confirm('Are you sure you want to reset the quiz? All your progress will be lost.')) {
                return;
            }
            
            // Stop any running timers
            if (timerInterval) {
                clearInterval(timerInterval);
                timerInterval = null;
            }
            
            // Clear any pending timeouts
            if (window.autoAdvanceTimeout) {
                clearTimeout(window.autoAdvanceTimeout);
                window.autoAdvanceTimeout = null;
            }
            
            // Reset state variables
            userAnswers = {};
            quizResults = { correct: 0, incorrect: 0 };
            currentQuestionIndex = 0;
            timeLeft = QUIZ_CONFIG.timeLimit * 60;
            quizStartTime = null;
            isQuizCompleted = false;
            isPaused = false;
            totalPausedTime = 0;
            pauseStartTime = null;
            flaggedQuestions.clear();
            
            // Clear all client-side storage
            localStorage.removeItem(STORAGE_KEYS.progress);
            localStorage.removeItem(STORAGE_KEYS.startTime);
            
            // Reset all form inputs
            const formInputs = quizForm.querySelectorAll('input[type="radio"]');
            formInputs.forEach(function(input) {
                input.checked = false;
                input.disabled = false;
            });
            
            // Clear all answer feedback
            document.querySelectorAll('.answer-feedback').forEach(function(el) {
                el.innerHTML = '';
                el.style.display = 'none';
                el.className = 'answer-feedback';
            });
            
            // Hide all explanations
            document.querySelectorAll('.explanation').forEach(function(el) {
                el.classList.add('hidden');
            });
            
            // Reset answer options
            document.querySelectorAll('.answer-option').forEach(function(option) {
                option.classList.remove('selected', 'correct', 'incorrect');
            });
            
            // Remove locked state from all questions
            questionContainers.forEach(function(container) {
                container.classList.remove('answer-locked', 'active');
            });
            
            // Reset flags
            document.querySelectorAll('.flag-question svg').forEach(function(svg) {
                svg.classList.remove('text-yellow-500');
                svg.classList.add('text-gray-400');
            });
            
            // Hide results modal if open
            resultsModal.classList.add('hidden');
            pauseModal.classList.add('hidden');
            
            // Reset UI elements
            correctCountEl.textContent = '0';
            incorrectCountEl.textContent = '0';
            updateTimerDisplay();
            updateProgressBar();
            
            // Show first question
            showQuestion(0);
            
            // Restart timer
            startTimer();
        }
        
        // Initialize quiz
        function initQuiz() {
            console.log('Initializing quiz...');
            
            // Add reset button event listeners
            const resetButton = document.getElementById('resetQuiz');
            if (resetButton) {
                resetButton.addEventListener('click', resetQuiz);
            }
            
            if (resetFromResultsButton) {
                resetFromResultsButton.addEventListener('click', resetQuiz);
            }
            
            // Load saved progress if exists
            loadProgress();
            
            // Show first question
            showQuestion(currentQuestionIndex);
            
            // Start timer
            startTimer();
            
            console.log('Quiz initialized successfully');
        }
        
        // Load saved progress
        function loadProgress() {
            const savedProgress = localStorage.getItem(STORAGE_KEYS.progress);
            if (!savedProgress) {
                console.log('No saved progress found');
                return;
            }
            
            try {
                const progress = JSON.parse(savedProgress);
                console.log('Loading saved progress:', progress);
                
                userAnswers = progress.answers || {};
                quizResults = progress.results || { correct: 0, incorrect: 0 };
                currentQuestionIndex = progress.currentQuestionIndex || 0;
                timeLeft = progress.timeLeft || timeLeft;
                totalPausedTime = progress.totalPausedTime || 0;
                
                if (progress.flaggedQuestions) {
                    flaggedQuestions = new Set(progress.flaggedQuestions);
                    // Restore flag UI
                    flaggedQuestions.forEach(function(qId) {
                        const button = document.querySelector('[data-question-id="' + qId + '"]');
                        if (button) {
                            const svg = button.querySelector('svg');
                            if (svg) {
                                svg.classList.remove('text-gray-400');
                                svg.classList.add('text-yellow-500');
                            }
                        }
                    });
                }
                
                updateTimerDisplay();
                updateProgressBar();
                updateScoreDisplay();
                
                // Restore selected answers
                Object.keys(userAnswers).forEach(function(questionId) {
                    const answerId = userAnswers[questionId];
                    const input = document.querySelector('input[name="answers[' + questionId + ']"][value="' + answerId + '"]');
                    if (input) {
                        input.checked = true;
                        input.disabled = true;
                        input.closest('.answer-option').classList.add('selected');
                        
                        const questionContainer = input.closest('.question-container');
                        if (questionContainer) {
                            questionContainer.classList.add('answer-locked');
                            const isCorrect = checkAnswer(questionContainer, answerId, true);
                            showFeedback(questionContainer, isCorrect);
                        }
                    }
                });
                
                console.log('Progress loaded successfully');
            } catch (e) {
                console.error('Error parsing saved progress:', e);
                localStorage.removeItem(STORAGE_KEYS.progress);
            }
        }
        
        // Show question by index
        function showQuestion(index) {
            // Record time spent on the previous question
            if (currentQuestionId) {
                const timeSpent = Math.round((Date.now() - questionStartTime) / 1000); // in seconds
                const questionEl = document.querySelector(`#question-${currentQuestionId}`);
                if (questionEl) {
                    const currentTimeSpent = parseInt(questionEl.dataset.timeSpent || '0');
                    questionEl.dataset.timeSpent = currentTimeSpent + timeSpent;
                    console.log(`Time spent on question ${currentQuestionId}: ${questionEl.dataset.timeSpent}s`);
                }
            }
            
            console.log('Showing question:', index + 1);
            
            // Hide all questions
            questionContainers.forEach(function(container) {
                container.classList.remove('active');
            });
            
            // Show current question
            const currentQuestion = questionContainers[index];
            if (!currentQuestion) {
                console.error('Question container not found for index:', index);
                return;
            }
            
            // Update current question and reset timer
            currentQuestionId = currentQuestion.dataset.questionId;
            questionStartTime = Date.now();
            
            currentQuestion.classList.add('active');
            currentQuestionIndex = index;
            
            // Update UI
            currentQuestionEl.textContent = index + 1;
            updateProgressBar();
            updateNavigationButtons();
            
            // Scroll to top of question
            currentQuestion.scrollIntoView({ behavior: 'smooth', block: 'start' });
        }
        
        // Check if the selected answer is correct
        function checkAnswer(questionContainer, selectedValue, skipFeedback) {
            skipFeedback = skipFeedback || false;
            
            const questionId = questionContainer.dataset.questionId;
            const correctAnswer = questionContainer.dataset.correctAnswer;
            const isCorrect = selectedValue === correctAnswer;
            
            console.log('Checking answer:', { questionId: questionId, selected: selectedValue, correct: correctAnswer, isCorrect: isCorrect });
            
            // Update UI
            const answerOptions = questionContainer.querySelectorAll('.answer-option');
            answerOptions.forEach(function(option) {
                const input = option.querySelector('input[type="radio"]');
                if (!input) return;
                
                option.classList.remove('correct', 'incorrect');
                
                if (input.value === selectedValue) {
                    option.classList.add(isCorrect ? 'correct' : 'incorrect');
                } else if (input.value === correctAnswer) {
                    option.classList.add('correct');
                }
            });
            
            // Show feedback if not skipping
            if (!skipFeedback) {
                showFeedback(questionContainer, isCorrect);
                
                // Show explanation
                const explanation = questionContainer.querySelector('.explanation');
                if (explanation) {
                    explanation.classList.remove('hidden');
                }
            }
            
            return isCorrect;
        }
        
        // Show feedback for the answer
        function showFeedback(questionContainer, isCorrect) {
            const feedbackEl = questionContainer.querySelector('.answer-feedback');
            if (!feedbackEl) return;
            
            feedbackEl.className = 'answer-feedback p-4 rounded-lg ' + 
                (isCorrect ? 'bg-green-50 text-green-800 border-l-4 border-green-500' : 
                             'bg-red-50 text-red-800 border-l-4 border-red-500');
            
            feedbackEl.innerHTML = '<div class="flex items-start">' +
                '<div class="flex-shrink-0">' + (isCorrect ? '✅' : '❌') + '</div>' +
                '<div class="ml-3"><p class="font-semibold">' + 
                (isCorrect ? 'Correct!' : 'Incorrect') + '</p></div></div>';
            
            feedbackEl.style.display = 'block';
        }
        
        // Update progress bar
        function updateProgressBar() {
            const progress = ((currentQuestionIndex + 1) / questionContainers.length) * 100;
            progressBar.style.width = progress + '%';
        }
        
        // Update score display
        function updateScoreDisplay() {
            correctCountEl.textContent = quizResults.correct;
            incorrectCountEl.textContent = quizResults.incorrect;
        }
        
        // Update navigation buttons
        function updateNavigationButtons() {
            const currentQuestion = questionContainers[currentQuestionIndex];
            if (!currentQuestion) return;
            
            const questionId = currentQuestion.dataset.questionId;
            const isAnswered = userAnswers[questionId] !== undefined;
            
            // Update next button state
            const nextButton = currentQuestion.querySelector('.btn-next');
            if (nextButton) {
                nextButton.disabled = !isAnswered;
                nextButton.classList.toggle('opacity-50', !isAnswered);
                nextButton.classList.toggle('cursor-not-allowed', !isAnswered);
            }
            
            // Update previous button visibility
            const prevButton = currentQuestion.querySelector('.btn-prev');
            if (prevButton) {
                prevButton.classList.toggle('invisible', currentQuestionIndex === 0);
            }
        }
        
        // Start the quiz timer
        function startTimer() {
            // Store the start time if not already set
            if (!quizStartTime) {
                quizStartTime = Date.now();
                localStorage.setItem(STORAGE_KEYS.startTime, quizStartTime);
            } else {
                // Load from storage if available
                const savedStartTime = localStorage.getItem(STORAGE_KEYS.startTime);
                if (savedStartTime) {
                    quizStartTime = parseInt(savedStartTime);
                }
            }
            
            updateTimerDisplay();
            
            // Clear any existing interval
            if (timerInterval) {
                clearInterval(timerInterval);
            }
            
            timerInterval = setInterval(function() {
                if (timeLeft > 0 && !isQuizCompleted && !isPaused) {
                    timeLeft--;
                    updateTimerDisplay();
                    saveProgress();
                } else if (timeLeft === 0 && !isQuizCompleted) {
                    clearInterval(timerInterval);
                    timerInterval = null;
                    isQuizCompleted = true;
                    
                    // Auto-submit the quiz when time runs out
                    alert('Time\'s up! Submitting your answers...');
                    submitQuizForm(true);
                }
            }, 1000);
        }
        
        // Update the timer display
        function updateTimerDisplay() {
            const minutes = Math.floor(timeLeft / 60);
            const seconds = timeLeft % 60;
            const timerEl = document.getElementById('timer');
            if (!timerEl) return;
            
            timerEl.textContent = String(minutes).padStart(2, '0') + ':' + String(seconds).padStart(2, '0');
            
            // Change color when time is running low
            if (timeLeft <= 60) {
                timerEl.classList.add('text-red-600', 'pulse-animation');
                timerEl.classList.remove('text-blue-600', 'text-orange-600');
            } else if (timeLeft <= 300) {
                timerEl.classList.add('text-orange-600');
                timerEl.classList.remove('text-blue-600', 'text-red-600');
                timerEl.classList.remove('pulse-animation');
            }
        }
        
        // Save progress to localStorage
        function saveProgress() {
            const progress = {
                currentQuestionIndex: currentQuestionIndex,
                timeLeft: timeLeft,
                answers: userAnswers,
                results: quizResults,
                totalPausedTime: totalPausedTime,
                flaggedQuestions: Array.from(flaggedQuestions)
            };
            localStorage.setItem(STORAGE_KEYS.progress, JSON.stringify(progress));
        }
        
        // Format time for display
        function formatTime(seconds) {
            const minutes = Math.floor(seconds / 60);
            const secs = seconds % 60;
            return minutes + ':' + String(secs).padStart(2, '0');
        }
        
        // Show quiz results
        function showResults(correctAnswers, timeTaken) {
            const totalQuestions = questionContainers.length;
            const percentage = Math.round((correctAnswers / totalQuestions) * 100);
            const passingScore = QUIZ_CONFIG.passingScore;
            const passed = percentage >= passingScore;
            
            console.log('Showing results:', { correctAnswers: correctAnswers, percentage: percentage, passed: passed });
            
            // Update results modal
            resultsScore.textContent = percentage + '%';
            correctAnswersEl.textContent = correctAnswers;
            statCorrectEl.textContent = correctAnswers;
            statIncorrectEl.textContent = quizResults.incorrect;
            statTimeTakenEl.textContent = formatTime(timeTaken);
            statAccuracyEl.textContent = percentage + '%';
            
            // Show pass/fail badge
            const passBadge = document.getElementById('passBadge');
            const passText = document.getElementById('passText');
            if (passBadge && passText) {
                passBadge.classList.remove('hidden');
                if (passed) {
                    passBadge.classList.add('bg-green-100', 'text-green-800');
                    passBadge.classList.remove('bg-red-100', 'text-red-800');
                    passText.textContent = 'Passed!';
                } else {
                    passBadge.classList.add('bg-red-100', 'text-red-800');
                    passBadge.classList.remove('bg-green-100', 'text-green-800');
                    passText.textContent = 'Need ' + passingScore + '% to pass';
                }
            }
            
            // Update comparison if previous best exists
            const bestScore = QUIZ_CONFIG.bestScore;
            if (bestScore !== null) {
                const currentScoreComp = document.getElementById('currentScoreComparison');
                const improvementEl = document.getElementById('improvement');
                
                if (currentScoreComp) {
                    currentScoreComp.textContent = percentage + '%';
                }
                
                if (improvementEl) {
                    const improvement = percentage - bestScore;
                    improvementEl.textContent = (improvement > 0 ? '+' : '') + improvement + '%';
                    improvementEl.classList.toggle('text-green-600', improvement > 0);
                    improvementEl.classList.toggle('text-red-600', improvement < 0);
                    improvementEl.classList.toggle('text-gray-600', improvement === 0);
                    
                    // Show achievement if improved
                    if (improvement > 0) {
                        showAchievement('Personal Best!', 'You improved by ' + improvement + '%!');
                    }
                }
            }
            
            // Check for other achievements
            if (percentage === 100) {
                showAchievement('Perfect Score!', 'You answered all questions correctly!');
            } else if (percentage >= 90) {
                showAchievement('Excellent!', 'You scored above 90%!');
            }
            
            // Show modal
            resultsModal.classList.remove('hidden');
            
            // Add animation
            setTimeout(function() {
                const modalContent = resultsModal.querySelector('.bg-white');
                if (modalContent) {
                    modalContent.classList.add('animate-fadeIn');
                }
            }, 100);
        }
        
        // Show achievement popup
        function showAchievement(title, text) {
            const popup = document.getElementById('achievementPopup');
            const titleEl = document.getElementById('achievementTitle');
            const textEl = document.getElementById('achievementText');
            
            if (!popup || !titleEl || !textEl) return;
            
            titleEl.textContent = title;
            textEl.textContent = text;
            popup.classList.remove('hidden');
            
            // Auto-hide after 5 seconds
            setTimeout(function() {
                popup.classList.add('hidden');
            }, 5000);
        }
        
        // Submit quiz form
function submitQuizForm(isTimeUp = false) {
    if (isQuizCompleted) {
        console.log('Quiz already completed, skipping submission');
        return;
    }
    
    console.log('Submitting quiz...');
    isQuizCompleted = true;
    
    // Clear the timer and any pending timeouts
    if (timerInterval) {
        clearInterval(timerInterval);
        timerInterval = null;
    }
    if (window.autoAdvanceTimeout) {
        clearTimeout(window.autoAdvanceTimeout);
        window.autoAdvanceTimeout = null;
    }
    
    // Calculate time taken
    const endTime = Date.now();
    const startTime = parseInt(localStorage.getItem(STORAGE_KEYS.startTime)) || quizStartTime;
    const timeTaken = Math.round((endTime - startTime) / 1000) - totalPausedTime;
    
    console.log('Time taken:', timeTaken, 'seconds');
    
    // Set the end time and time taken in the form
    document.getElementById('endTime').value = new Date(endTime).toISOString();
    document.getElementById('timeTaken').value = timeTaken;
    document.getElementById('pausedTime').value = totalPausedTime;
    
    // Calculate final score and collect answers
    let correctAnswers = 0;
    const answers = {};
    const timeSpent = {};
    
    questionContainers.forEach(container => {
        const questionId = container.dataset.questionId;
        const selectedInput = container.querySelector('input[type="radio"]:checked');
        const correctAnswer = container.dataset.correctAnswer;
        
        // Track correct answers for UI
        if (selectedInput && selectedInput.value === correctAnswer) {
            correctAnswers++;
        }
        
        // Build answers object
        if (selectedInput) {
            answers[questionId] = selectedInput.value;
        }
        
        // Track time spent per question
        timeSpent[questionId] = parseInt(container.dataset.timeSpent || '0');
    });
    
    console.log('Final score:', correctAnswers, '/', questionContainers.length);
    
    // Show results
    showResults(correctAnswers, timeTaken);
    
    // Clear saved progress
    localStorage.removeItem(STORAGE_KEYS.progress);
    localStorage.removeItem(STORAGE_KEYS.startTime);
    
    // Prepare submission data
    const submissionData = new FormData(document.getElementById('quizForm'));
    // Add additional data points
    submissionData.append('answers', JSON.stringify(answers));
    submissionData.append('time_spent', JSON.stringify(timeSpent));
    submissionData.append('time_taken', timeTaken);
    submissionData.append('end_time', new Date(endTime).toISOString());
    submissionData.append('paused_time', totalPausedTime);
    submissionData.append('time_up', isTimeUp);
    submissionData.append('attempt_id', '{{ $attempt->id }}');
    
    // Log the submission data for debugging
    console.log('Submitting to:', quizForm.action);
    console.log('Submission data:', submissionData);
    
    // Submit the form asynchronously with FormData
    fetch(quizForm.action, {
        method: 'POST',
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: submissionData
    })
    .then(response => {
        if (!response.ok) {
            return response.json().then(err => {
                throw new Error(err.message || 'Failed to submit quiz');
            });
        }
        return response.json();
    })
    .then(data => {
        console.log('Quiz submitted successfully:', data);
        
        // Update streak if provided
        if (data.streak) {
            const streakBadge = document.querySelector('.streak-badge');
            if (streakBadge) {
                streakBadge.innerHTML = '🔥 ' + data.streak + ' day streak';
            }
        }
    })
    .catch(error => {
        console.error('Error submitting quiz:', error);
        // Optionally show error to user
        alert('Failed to submit quiz: ' + error.message);
    });
}
        
        // Answer selection
        questionContainers.forEach(function(container) {
            const inputs = container.querySelectorAll('input[type="radio"]');
            const questionId = container.dataset.questionId;
            
            inputs.forEach(function(input) {
                input.addEventListener('change', function() {
                    // Prevent changes if already answered
                    if (userAnswers[questionId] !== undefined) {
                        console.log('Question already answered, ignoring change');
                        return;
                    }
                    
                    console.log('Answer selected for question:', questionId, 'value:', this.value);
                    
                    const answerOptions = container.querySelectorAll('.answer-option');
                    
                    // Clear previous selection
                    answerOptions.forEach(function(opt) {
                        opt.classList.remove('selected');
                    });
                    
                    // Mark selected answer
                    this.closest('.answer-option').classList.add('selected');
                    
                    // Update user answers and lock the question
                    userAnswers[questionId] = this.value;
                    
                    // Lock all inputs for this question
                    const allInputs = container.querySelectorAll('input[type="radio"]');
                    allInputs.forEach(function(inp) {
                        inp.disabled = true;
                    });
                    container.classList.add('answer-locked');
                    
                    // Check answer and show feedback
                    const isCorrect = checkAnswer(container, this.value);
                    
                    // Update score
                    if (isCorrect) {
                        quizResults.correct++;
                    } else {
                        quizResults.incorrect++;
                    }
                    updateScoreDisplay();
                    
                    // Update navigation buttons
                    updateNavigationButtons();
                    
                    console.log('Answer processed. Auto-next enabled:', autoNextEnabled);
                    
                    // Schedule auto-advance if enabled
                    if (autoNextEnabled && currentQuestionIndex < questionContainers.length - 1) {
                        console.log('Scheduling auto-advance in 1.5 seconds');
                        
                        // Clear any existing timeout
                        if (window.autoAdvanceTimeout) {
                            clearTimeout(window.autoAdvanceTimeout);
                            window.autoAdvanceTimeout = null;
                        }
                        
                        // Set new timeout for auto-advance
                        window.autoAdvanceTimeout = setTimeout(function() {
                            console.log('Auto-advance executing. State check:', autoNextEnabled);
                            
                            if (autoNextEnabled && currentQuestionIndex < questionContainers.length - 1) {
                                showQuestion(currentQuestionIndex + 1);
                            } else {
                                console.log('Auto-advance cancelled - state changed or last question');
                            }
                            window.autoAdvanceTimeout = null;
                        }, 1500);
                    } else {
                        console.log('Auto-advance NOT scheduled. Enabled:', autoNextEnabled, 'Has next:', currentQuestionIndex < questionContainers.length - 1);
                    }
                    
                    saveProgress();
                });
            });
        });
        
        // Delegate click events for next/previous buttons
        document.addEventListener('click', function(e) {
            // Handle next button click
            if (e.target.classList.contains('btn-next') || e.target.closest('.btn-next')) {
                e.preventDefault();
                const button = e.target.classList.contains('btn-next') ? e.target : e.target.closest('.btn-next');
                if (button && !button.disabled && currentQuestionIndex < questionContainers.length - 1) {
                    console.log('Next button clicked');
                    // Clear any pending auto-advance
                    if (window.autoAdvanceTimeout) {
                        clearTimeout(window.autoAdvanceTimeout);
                        window.autoAdvanceTimeout = null;
                    }
                    showQuestion(currentQuestionIndex + 1);
                }
            }
            // Handle previous button click
            else if (e.target.classList.contains('btn-prev') || e.target.closest('.btn-prev')) {
                e.preventDefault();
                const button = e.target.classList.contains('btn-prev') ? e.target : e.target.closest('.btn-prev');
                if (button && currentQuestionIndex > 0) {
                    console.log('Previous button clicked');
                    // Clear any pending auto-advance
                    if (window.autoAdvanceTimeout) {
                        clearTimeout(window.autoAdvanceTimeout);
                        window.autoAdvanceTimeout = null;
                    }
                    showQuestion(currentQuestionIndex - 1);
                }
            }
        });
        
        // Form submission
        if (quizForm) {
            quizForm.addEventListener('submit', function(e) {
                e.preventDefault();
                console.log('Form submitted');
                
                if (isQuizCompleted) {
                    console.log('Quiz already completed');
                    return;
                }
                
                submitQuizForm(false);
            });
        }
        
        // Review answers button
        if (reviewButton) {
            reviewButton.addEventListener('click', function() {
                console.log('Review answers clicked');
                resultsModal.classList.add('hidden');
                showQuestion(0);
            });
        }
        
        // Share functions
        window.shareOnTwitter = function() {
            const percentage = Math.round((quizResults.correct / questionContainers.length) * 100);
            const text = 'I just scored ' + percentage + '% on this quiz! Can you beat my score?';
            const url = window.location.href;
            window.open('https://twitter.com/intent/tweet?text=' + encodeURIComponent(text) + '&url=' + encodeURIComponent(url), '_blank');
        };
        
        window.shareOnFacebook = function() {
            const url = window.location.href;
            window.open('https://www.facebook.com/sharer/sharer.php?u=' + encodeURIComponent(url), '_blank');
        };
        
        window.copyResultLink = function() {
            const url = window.location.href;
            if (navigator.clipboard && navigator.clipboard.writeText) {
                navigator.clipboard.writeText(url).then(function() {
                    showToast('Link copied to clipboard!', 'success');
                }).catch(function(err) {
                    console.error('Failed to copy:', err);
                    showToast('Failed to copy link', 'error');
                });
            } else {
                // Fallback for older browsers
                const textArea = document.createElement('textarea');
                textArea.value = url;
                textArea.style.position = 'fixed';
                textArea.style.left = '-999999px';
                textArea.style.top = '-999999px';
                document.body.appendChild(textArea);
                textArea.focus();
                textArea.select();
                try {
                    document.execCommand('copy');
                    showToast('Link copied to clipboard!', 'success');
                } catch (err) {
                    console.error('Failed to copy:', err);
                    showToast('Failed to copy link', 'error');
                }
                document.body.removeChild(textArea);
            }
        };
        
        // Clear any pending auto-advance when leaving the page
        window.addEventListener('beforeunload', function() {
            if (window.autoAdvanceTimeout) {
                clearTimeout(window.autoAdvanceTimeout);
            }
        });
        
        // Prevent accidental page reload
        window.addEventListener('beforeunload', function(e) {
            if (!isQuizCompleted && Object.keys(userAnswers).length > 0) {
                e.preventDefault();
                e.returnValue = '';
                return '';
            }
        });
        
        // Initialize the quiz
        initQuiz();
    });
})();
</script>
@endpush