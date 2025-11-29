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

        .feedback-correct {
            background-color: #ecfdf5;
            color: #065f46;
        }

        .feedback-incorrect {
            background-color: #fef2f2;
            color: #991b1b;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
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

            0%,
            100% {
                opacity: 1;
            }

            50% {
                opacity: 0.8;
            }
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
    </style>
@endpush

@section('dashboard-content')
    <div class="py-6">
        <div class="max-w-4xl mx-auto">
            <!-- Quiz Header -->
            <div class="bg-white rounded-xl shadow-md overflow-hidden mb-6">
                <div class="p-6">
                    <h1 class="text-2xl font-bold text-gray-900 mb-2">
                        {{ $quiz->getTranslation('title', app()->getLocale()) }}</h1>
                    <p class="text-gray-600 mb-6">{{ $quiz->getTranslation('description', app()->getLocale()) }}</p>

                    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                        <div class="w-full">
                            <div class="flex justify-between items-center text-sm text-gray-600 mb-1 flex-wrap gap-2">
                                <div class="flex items-center gap-3">
                                    <span>{{ __('quiz.question') }} <span id="currentQuestion">1</span> {{ __('quiz.of') }}
                                        {{ $quiz->questions->count() }}</span>
                                    <span class="score-badge">
                                        <span class="score-correct">✓ <span id="correctCount">0</span></span>
                                        <span class="text-gray-400">|</span>
                                        <span class="score-incorrect">✗ <span id="incorrectCount">0</span></span>
                                    </span>
                                    <label
                                        class="flex items-center text-sm text-gray-700 cursor-pointer bg-gray-50 px-3 py-1 rounded-lg border border-gray-200 hover:bg-gray-100 transition-colors">
                                        <input type="checkbox" id="autoNextToggle" checked
                                            class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                                        <span class="ml-2 font-medium">{{ strtolower(__('quiz.auto')) }}</span>
                                    </label>
                                </div>
                                <div class="flex items-center gap-3">
                                    <span id="timer" class="font-mono font-semibold text-blue-600">20:00</span>
                                    <button type="button" id="resetQuiz"
                                        class="px-3 py-1 text-sm text-red-600 hover:text-red-800 hover:bg-red-50 rounded transition-colors">
                                        {{ __('quiz.resetQuiz') }}
                                    </button>
                                </div>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-2.5">
                                <div id="progressBar" class="bg-blue-600 h-2.5 rounded-full transition-all duration-300"
                                    style="width: 0%"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quiz Content -->
            <form id="quizForm"
                action="{{ route('guest-quiz.submit', ['locale' => app()->getLocale(), 'quiz' => $quiz->id]) }}"
                method="POST">
                @csrf
                <input type="hidden" name="quiz_id" value="{{ $quiz->id }}">
                <input type="hidden" name="time_taken" id="timeTaken">
                <input type="hidden" name="end_time" id="endTime">

                @foreach ($quiz->questions as $index => $question)
                    <div class="question-container bg-white rounded-xl shadow-sm p-6 mb-6" id="question-{{ $question->id }}"
                        data-question-id="{{ $question->id }}" data-correct-answer="{{ $question->correct_option_id }}">

                        <div class="mb-6">
                            <h3 class="text-lg font-bold mb-2">
                                <span class="font-semibold">{{ __('quiz.question') }} {{ $index + 1 }}:</span>
                                {{ $question->getTranslation('text', app()->getLocale()) }}
                            </h3>

                            @if ($question->image_path)
                                <div class="mb-4">
                                    <img src="{{ asset('storage/' . $question->image_path) }}"
                                        alt="{{ __('quiz.questionImage') }}"
                                        class="max-w-full h-auto rounded-lg border border-gray-200">
                                </div>
                            @endif

                            <div class="space-y-3 mb-4 question-options">
                                @foreach ($question->options as $option)
                                    <label
                                        class="answer-option block p-4 border border-gray-200 rounded-lg cursor-pointer hover:bg-gray-50 transition-colors">
                                        <div class="flex items-center">
                                            <input type="radio" name="answers[{{ $question->id }}]"
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
                        </div>

                        <div class="flex justify-between pt-4 border-t border-gray-100">
                            <button type="button"
                                class="btn-prev px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors {{ $loop->first ? 'invisible' : '' }}">
                                {{ __('quiz.previous') }}
                            </button>

                            @if ($loop->last)
                                <button type="submit"
                                    class="ml-auto px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                                    {{ __('quiz.submitQuiz') }}
                                </button>
                            @else
                                <button type="button"
                                    class="btn-next px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors opacity-50 cursor-not-allowed ml-auto"
                                    disabled>
                                    {{ __('quiz.next') }}
                                </button>
                            @endif
                        </div>
                    </div>
                @endforeach
            </form>

            <!-- Subscription Upsell - Only show for guests -->
            @guest
                <div class="mt-6 pt-4 border-t border-gray-100">
                    <div class="text-center">
                        <p class="text-sm text-gray-600 mb-3">{{ __('quiz.wantMorePractice') }}</p>
                        <div class="bg-gradient-to-r from-blue-50 to-purple-50 p-3 rounded-lg">
                            <p class="text-xs text-gray-600 mb-2">{{ __('quiz.unlockFullAccess') }}</p>
                            <a href="{{ route('plans', ['locale' => app()->getLocale()]) }}"
                                class="inline-block px-4 py-2 bg-gradient-to-r from-blue-600 to-purple-600 text-white text-sm font-medium rounded-md hover:from-blue-700 hover:to-purple-700 transition-colors">
                                {{ __('quiz.viewPlans') }}
                            </a>
                        </div>
                    </div>
                </div>
            @endguest
        </div>
    </div>

    <!-- Results Modal -->
    <div id="resultsModal"
        class="hidden fixed inset-0 bg-black bg-opacity-50 modal-backdrop flex items-center justify-center p-2 sm:p-4 z-50">
        <div
            class="bg-white rounded-xl w-full max-w-md sm:max-w-2xl mx-2 sm:mx-4 p-4 sm:p-6 max-h-[90vh] overflow-y-auto relative">
            <!-- Close Button -->
            <button type="button" id="closeResultsModal"
                class="absolute top-3 right-3 sm:top-4 sm:right-4 text-gray-400 hover:text-gray-600 focus:outline-none">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>

            <div class="text-center mb-4 sm:mb-6">
                <div
                    class="inline-flex items-center justify-center w-16 h-16 sm:w-20 sm:h-20 bg-blue-100 rounded-full mb-3 sm:mb-4">
                    <svg class="w-8 h-8 sm:w-10 sm:h-10 text-blue-600" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <h2 class="text-2xl sm:text-3xl font-bold mb-1 sm:mb-2 text-gray-900">{{ __('quiz.quizCompleted') }}</h2>
                <div class="text-4xl sm:text-5xl font-bold text-blue-600 mb-1 sm:mb-2" id="resultsScore">0%</div>
                <p class="text-gray-600 text-sm sm:text-base mb-3 sm:mb-4">
                    @php
                        $correctSpan = '<span id="correctAnswers" class="font-semibold text-green-600">0</span>';
                        $totalSpan = '<span class="font-semibold">' . $quiz->questions->count() . '</span>';
                    @endphp
                    {!! __('quiz.youGotXOutOfY', ['correct' => $correctSpan, 'total' => $totalSpan]) !!}
                </p>
            </div>

            <!-- Stats Grid -->
            <div class="grid grid-cols-3 gap-2 sm:gap-3 mb-4 sm:mb-6">
                <div class="stat-card p-2 sm:p-3">
                    <div class="stat-value text-green-600 text-xl sm:text-2xl" id="statCorrect">0</div>
                    <div class="stat-label text-xs sm:text-sm">{{ __('quiz.correct') }}</div>
                </div>
                <div class="stat-card p-2 sm:p-3">
                    <div class="stat-value text-red-600 text-xl sm:text-2xl" id="statIncorrect">0</div>
                    <div class="stat-label text-xs sm:text-sm">{{ __('quiz.incorrect') }}</div>
                </div>
                <div class="stat-card p-2 sm:p-3">
                    <div class="stat-value text-blue-600 text-xl sm:text-2xl" id="statTimeTaken">0:00</div>
                    <div class="stat-label text-xs sm:text-sm">{{ __('quiz.timeTaken') }}</div>
                </div>
            </div>

            <!-- Upsell Banner -->
            @guest
                <div class="bg-gradient-to-r from-purple-600 to-indigo-600 rounded-lg p-4 sm:p-6 mb-4 sm:mb-6">
                    <div class="flex flex-col sm:flex-row items-center sm:items-start gap-3 sm:gap-4">
                        <div class="flex-shrink-0">
                            <svg class="w-10 h-10 sm:w-12 sm:h-12 text-white opacity-90" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                            </svg>
                        </div>
                        <div class="text-center sm:text-left">
                            <h3 class="text-lg sm:text-xl font-bold mb-1 sm:mb-2 text-white">{{ __('quiz.wantMoreQuizzes') }}
                            </h3>
                            <p class="text-white text-opacity-90 text-sm sm:text-base mb-3 sm:mb-4">{{ __('quiz.signupCta') }}
                            </p>
                            <div class="flex flex-col sm:flex-row gap-2 sm:gap-3 justify-center sm:justify-start">
                                <a href="{{ route('register', ['locale' => app()->getLocale()]) }}"
                                    class="px-4 sm:px-6 py-2 bg-white text-purple-600 font-semibold rounded-lg hover:bg-gray-100 transition-colors text-sm sm:text-base">
                                    {{ __('quiz.signUpFree') }}
                                </a>
                                <a href="{{ route('login', ['locale' => app()->getLocale()]) }}"
                                    class="px-4 sm:px-6 py-2 bg-transparent border-2 border-white text-white font-semibold rounded-lg hover:bg-white hover:text-purple-600 transition-colors text-sm sm:text-base">
                                    {{ __('quiz.logIn') }}
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            @endguest

            @auth
                <div class="gradient-border mb-4 sm:mb-6">
                    <div class="gradient-border-inner p-3 sm:p-4">
                        <div class="flex flex-col sm:flex-row items-center sm:items-start gap-3 sm:gap-4">
                            <div class="flex-shrink-0">
                                <svg class="w-8 h-8 sm:w-10 sm:h-10 text-purple-600" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z">
                                    </path>
                                </svg>
                            </div>
                            <div class="text-center sm:text-left">
                                <h3 class="text-base sm:text-lg font-bold mb-1 sm:mb-2 text-gray-900">
                                    {{ __('quiz.upgradeToPremium') }}</h3>
                                <p class="text-gray-600 text-sm sm:text-base mb-2 sm:mb-3">{{ __('quiz.premiumBenefits') }}
                                </p>
                                <a href="{{ route('plans', ['locale' => app()->getLocale()]) }}"
                                    class="inline-flex items-center justify-center sm:justify-start px-4 sm:px-5 py-2 bg-gradient-to-r from-purple-600 to-indigo-600 text-white font-semibold rounded-lg hover:from-purple-700 hover:to-indigo-700 transition-colors text-sm sm:text-base">
                                    {{ __('quiz.viewPlans') }}
                                    <svg class="w-3 h-3 sm:w-4 sm:h-4 ml-1 sm:ml-2" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 5l7 7-7 7"></path>
                                    </svg>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            @endauth

            <!-- Action Buttons -->
            <div class="flex flex-col sm:flex-row gap-3">
                <button id="reviewAnswers"
                    class="flex-1 px-4 sm:px-6 py-2 sm:py-3 bg-blue-600 text-white font-semibold rounded-lg hover:bg-blue-700 transition-colors text-sm sm:text-base">
                    {{ __('quiz.reviewAnswers') }}
                </button>
                <button id="resetFromResults"
                    class="flex-1 px-4 sm:px-6 py-2 sm:py-3 bg-gray-100 text-gray-700 font-semibold rounded-lg hover:bg-gray-200 transition-colors text-sm sm:text-base">
                    {{ __('quiz.takeQuizAgain') }}
                </button>
            </div>
        </div>
    </div>
@endsection

<!-- Signup Nudge Modal - Only show for guests -->
@guest
<div id="signupNudgeModal"
    class="fixed inset-0 bg-black/70 backdrop-blur-sm z-50 flex items-center justify-center p-4 opacity-0 transition-opacity duration-300 pointer-events-none">
    <div class="bg-gradient-to-br from-white to-blue-50 rounded-2xl max-w-md w-full overflow-hidden shadow-2xl transform transition-all duration-300 scale-95"
        style="box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);">
        <!-- Decorative elements -->
        <div class="absolute top-0 left-0 w-full h-2 bg-gradient-to-r from-blue-500 to-indigo-600"></div>
        <div class="absolute -top-10 -right-10 w-24 h-24 rounded-full bg-blue-100 opacity-30"></div>
        <div class="absolute -bottom-8 -left-8 w-20 h-20 rounded-full bg-indigo-100 opacity-30"></div>

        <div class="relative z-10 p-8">

            <!-- Content -->
            <div class="text-center">
                <!-- Animated Checkmark -->
                <div class="mx-auto flex items-center justify-center h-20 w-20 rounded-full bg-gradient-to-br from-blue-100 to-indigo-100 mb-6 transform transition-all duration-500 hover:scale-110">
                    <div class="relative">
                        <svg class="w-10 h-10 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M5 13l4 4L19 7"></path>
                        </svg>
                    </div>
                </div>
                
                <h3 class="text-2xl font-bold text-gray-900 mb-3 font-sans">{{ __('quiz.signupNudge.title') }}</h3>
                <p class="text-gray-600 mb-6 leading-relaxed">{{ __('quiz.signupNudge.message') }}</p>

                <div class="space-y-4">
                    <!-- Primary CTA -->
                    @php
                        $currentUrl = url()->current();
                        $registerUrl = route('register', [
                            'locale' => app()->getLocale(),
                            'return_to' => $currentUrl
                        ]);
                        $loginUrl = route('login', [
                            'locale' => app()->getLocale(),
                            'return_to' => $currentUrl
                        ]);
                    @endphp
                    <button 
                        class="w-full px-6 py-3.5 bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white font-semibold rounded-lg shadow-md hover:shadow-lg transform transition-all duration-200 hover:-translate-y-0.5 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
                        onclick="window.location.href='{{ $registerUrl }}'; return false;">
                        {{ __('quiz.signupNudge.signUpFree') }}
                    </button>
                    
                    <!-- Secondary Action -->
                    <div class="pt-1">
                        <button 
                            class="inline-flex items-center text-blue-600 hover:text-blue-800 font-medium text-sm transition-colors bg-transparent border-none p-0 cursor-pointer"
                            onclick="window.location.href='{{ $loginUrl }}'; return false;">
                            <span>{{ __('quiz.signupNudge.haveAccount') }}</span>
                            <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Footer -->
        <div class="bg-gray-50 px-6 py-4 text-center border-t border-gray-100">
            <a href="{{ route('home', ['locale' => app()->getLocale()]) }}"
                class="text-sm text-gray-500 hover:text-gray-700 font-medium transition-colors">
                {{ __('quiz.signupNudge.backHomepage') }}
            </a>
        </div>
    </div>
</div>
@endguest

<style>
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }
    
    #signupNudgeModal.active {
        opacity: 1;
        pointer-events: auto;
    }
    
    #signupNudgeModal.active > div {
        animation: fadeIn 0.4s cubic-bezier(0.4, 0, 0.2, 1) forwards;
    }
</style>

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Localized messages
            const messages = {
                resetConfirmation: '{{ __('quiz.resetConfirmation') }}'
            };

            // DOM Elements
            const quizForm = document.getElementById('quizForm');
            const questionContainers = document.querySelectorAll('.question-container');
            const currentQuestionEl = document.getElementById('currentQuestion');
            const progressBar = document.getElementById('progressBar');
            const resultsModal = document.getElementById('resultsModal');
            const resultsScore = document.getElementById('resultsScore');
            const correctAnswersEl = document.getElementById('correctAnswers');
            const reviewButton = document.getElementById('reviewAnswers');
            const resetFromResultsButton = document.getElementById('resetFromResults');
            const correctCountEl = document.getElementById('correctCount');
            const incorrectCountEl = document.getElementById('incorrectCount');
            const statCorrectEl = document.getElementById('statCorrect');
            const statIncorrectEl = document.getElementById('statIncorrect');
            const statTimeTakenEl = document.getElementById('statTimeTaken');

            // Quiz state
            let currentQuestionIndex = 0;
            let userAnswers = {};
            let quizResults = {
                correct: 0,
                incorrect: 0
            };
            let timeLeft = 20 * 60; // 20 minutes in seconds
            let timerInterval = null;
            let quizStartTime = null;
            let isQuizCompleted = false;
            let isQuizLocked = false;
            let hasAnsweredQuestion = false;
            
            // Function to show the signup nudge modal
            const showNudge = () => {
                const modal = document.getElementById('signupNudgeModal');
                if (modal) {
                    modal.style.display = 'flex';
                    modal.classList.add('flex');
                    modal.classList.remove('hidden');
                    // Add active class for animation
                    setTimeout(() => {
                        modal.classList.add('active');
                    }, 10);
                }
            };

            // Check if quiz was locked in a previous session and user is not logged in
            const savedLockState = localStorage.getItem('isQuizLockedForGuest') === 'true';
            const isLoggedIn = @json(auth()->check());
            
            if (savedLockState && !isLoggedIn) {
                isQuizLocked = true;
                
                // Check if they've answered any questions
                const answeredQuestions = JSON.parse(localStorage.getItem('userAnswers') || '{}');
                hasAnsweredQuestion = Object.keys(answeredQuestions).length > 0;
                // Show the nudge when the DOM is ready
                if (document.readyState === 'complete') {
                    showNudge();
                } else {
                    window.addEventListener('load', showNudge);
                }
            }

            // Initialize auto-next state from localStorage, default to false if not set
            let autoNextEnabled = localStorage.getItem('autoNextEnabled') === 'true';

            // Initialize auto-next toggle
            const autoNextToggle = document.getElementById('autoNextToggle');
            if (autoNextToggle) {
                autoNextToggle.checked = autoNextEnabled;

                autoNextToggle.addEventListener('change', function() {
                    autoNextEnabled = this.checked;
                    localStorage.setItem('autoNextEnabled', this.checked);

                    // Clear any pending auto-advance when toggling off
                    if (!autoNextEnabled && window.autoAdvanceTimeout) {
                        clearTimeout(window.autoAdvanceTimeout);
                        window.autoAdvanceTimeout = null;
                    }
                });
            }

            // Reset the quiz
            function resetQuiz() {
                if (confirm(messages.resetConfirmation)) {
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
                    quizResults = {
                        correct: 0,
                        incorrect: 0
                    };
                    currentQuestionIndex = 0;
                    timeLeft = 20 * 60;
                    quizStartTime = null;
                    isQuizCompleted = false;

                    // Clear all client-side storage
                    localStorage.removeItem('quizProgress');
                    localStorage.removeItem('quizStartTime');

                    // Reset all form inputs
                    const formInputs = quizForm.querySelectorAll('input[type="radio"]');
                    formInputs.forEach(input => {
                        input.checked = false;
                        input.disabled = false;
                    });

                    // Clear all answer feedback
                    document.querySelectorAll('.answer-feedback').forEach(el => {
                        el.innerHTML = '';
                        el.style.display = 'none';
                        el.className = 'answer-feedback';
                    });

                    // Reset answer options
                    document.querySelectorAll('.answer-option').forEach(option => {
                        option.classList.remove('selected', 'correct', 'incorrect');
                    });

                    // Remove locked state from all questions
                    questionContainers.forEach(container => {
                        container.classList.remove('answer-locked', 'active');
                    });

                    // Hide results modal if open
                    resultsModal.classList.add('hidden');

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
            }

            // Signup nudge modal
            const signupNudgeModal = document.getElementById('signupNudgeModal');
            
            // Function to show the signup nudge modal and lock the quiz
            function showSignupNudgeModal() {
                // Don't show the modal if user is logged in
                if (isLoggedIn) return;
                
                if (signupNudgeModal) {
                    signupNudgeModal.style.display = 'flex';
                    // Lock the quiz for guests
                    isQuizLocked = true;
                    localStorage.setItem('isQuizLockedForGuest', 'true');
                    
                    // Disable all answer inputs when quiz is completed
                    document.querySelectorAll('.answer-option input[type="radio"]').forEach(input => {
                        input.disabled = true;
                    });
                    
                    // Don't disable navigation buttons here - we want to allow navigation even when locked
                    return true;
                }
                return false;
            }
            
            // Handle successful login/signup (this would be called after successful authentication)
            window.handleAuthSuccess = function() {
                if (signupNudgeModal) {
                    signupNudgeModal.style.display = 'none';
                    isQuizLocked = false;
                    localStorage.removeItem('isQuizLockedForGuest');
                    
                    // Re-enable all answer inputs
                    document.querySelectorAll('.answer-option input[type="radio"]').forEach(input => {
                        input.disabled = false;
                    });
                    
                    // Navigation buttons will be handled by the click handler
                }
            };

            // Close modal when clicking the X button or clicking outside the modal
            document.getElementById('closeResultsModal').addEventListener('click', function() {
                if (!isQuizLocked) { // Only allow closing if quiz is not locked
                    resultsModal.classList.add('hidden');
                }
            });

            // Close modal when clicking outside the modal content
            resultsModal.addEventListener('click', function(e) {
                if (e.target === resultsModal && !isQuizLocked) {
                    resultsModal.classList.add('hidden');
                }
            });


            // Initialize quiz
            function initQuiz() {
                // Don't initialize if quiz is locked
                if (isQuizLocked) {
                    showSignupNudgeModal();
                    return;
                }

                // Add reset button event listeners
                const resetButton = document.getElementById('resetQuiz');
                if (resetButton) {
                    resetButton.addEventListener('click', resetQuiz);
                }

                if (resetFromResultsButton) {
                    resetFromResultsButton.addEventListener('click', resetQuiz);
                }

                // Show first question
                showQuestion(currentQuestionIndex);

                // Start timer
                startTimer();

                // Load saved progress if exists
                const savedProgress = localStorage.getItem('quizProgress');
                if (savedProgress) {
                    try {
                        const progress = JSON.parse(savedProgress);
                        if (progress.quizId === {{ $quiz->id }}) {
                            userAnswers = progress.answers || {};
                            quizResults = progress.results || {
                                correct: 0,
                                incorrect: 0
                            };
                            currentQuestionIndex = progress.currentQuestionIndex || 0;
                            timeLeft = progress.timeLeft || timeLeft;
                            updateTimerDisplay();
                            updateProgressBar();
                            updateScoreDisplay();

                            // Restore selected answers
                            Object.entries(userAnswers).forEach(([questionId, answerId]) => {
                                const input = document.querySelector(
                                    `input[name="answers[${questionId}]"][value="${answerId}"]`);
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

                            // Show the current question
                            showQuestion(currentQuestionIndex);
                        }
                    } catch (e) {
                        console.error('Error parsing saved progress:', e);
                        localStorage.removeItem('quizProgress');
                    }
                }
            }

            // Show question by index
            function showQuestion(index) {
                // Hide all questions
                questionContainers.forEach(container => {
                    container.classList.remove('active');
                });

                // Show current question
                const currentQuestion = questionContainers[index];
                currentQuestion.classList.add('active');
                currentQuestionIndex = index;

                // Update UI
                currentQuestionEl.textContent = index + 1;
                updateProgressBar();
                updateNavigationButtons();

                // Scroll to top of question
                currentQuestion.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }

            // Check if the selected answer is correct
            function checkAnswer(questionContainer, selectedValue, skipFeedback = false) {
                // Don't proceed if the user is logged in
                if (isLoggedIn) return false;
                
                const questionId = questionContainer.dataset.questionId;
                const correctAnswer = questionContainer.dataset.correctAnswer;
                const isCorrect = selectedValue === correctAnswer;
                
                // Show signup nudge for guests after answering their first question
                if (currentQuestionIndex === 0 && !isQuizLocked && !isLoggedIn) {
                    // Set the lock state when a question is answered (guests only)
                    localStorage.setItem('isQuizLockedForGuest', 'true');
                    // Small delay to let the answer feedback show first
                    setTimeout(showSignupNudgeModal, 1000);
                }

                // Update UI
                const answerOptions = questionContainer.querySelectorAll('.answer-option');
                answerOptions.forEach(option => {
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
                }

                return isCorrect;
            }

            // Show feedback for the answer
            function showFeedback(questionContainer, isCorrect) {
                const feedbackEl = questionContainer.querySelector('.answer-feedback');
                if (!feedbackEl) return;

                feedbackEl.className = `answer-feedback p-4 rounded-lg ${
            isCorrect ? 'bg-green-50 text-green-800 border-l-4 border-green-500' : 'bg-red-50 text-red-800 border-l-4 border-red-500'
        }`;

                feedbackEl.innerHTML = `
            <div class="flex items-start">
                <div class="flex-shrink-0">
                    ${isCorrect ? '✅' : '❌'}
                </div>
                <div class="ml-3">
                    <p class="font-semibold">
                        ${isCorrect ? `{{ __('quiz.correct') }}` : `{{ __('quiz.incorrect') }}`}
                    </p>
                    ${questionContainer.querySelector('.explanation') ? 
                        `<div class="mt-1 text-sm">
                                    ${questionContainer.querySelector('.explanation').innerHTML}
                                </div>` : ''
                    }
                </div>
            </div>
        `;

                feedbackEl.style.display = 'block';
            }

            // Update progress bar
            function updateProgressBar() {
                const progress = ((currentQuestionIndex + 1) / questionContainers.length) * 100;
                progressBar.style.width = `${progress}%`;
            }

            // Update score display
            function updateScoreDisplay() {
                correctCountEl.textContent = quizResults.correct;
                incorrectCountEl.textContent = quizResults.incorrect;
            }

            // Update navigation buttons
            function updateNavigationButtons() {
                const currentQuestion = questionContainers[currentQuestionIndex];
                const questionId = currentQuestion.dataset.questionId;
                const isAnswered = userAnswers[questionId] !== undefined;

                // Update next button state - only disable if current question isn't answered
                // Don't disable if quiz is locked (we'll handle that in the click handler)
                if (!isQuizLocked) {
                    const nextButton = currentQuestion.querySelector('.btn-next');
                    if (nextButton) {
                        nextButton.disabled = !isAnswered;
                        nextButton.classList.toggle('opacity-50', !isAnswered);
                        nextButton.classList.toggle('cursor-not-allowed', !isAnswered);
                    }
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
                    localStorage.setItem('quizStartTime', quizStartTime);
                } else {
                    // Load from storage if available
                    const savedStartTime = localStorage.getItem('quizStartTime');
                    if (savedStartTime) {
                        quizStartTime = parseInt(savedStartTime);
                    }
                }

                updateTimerDisplay();

                timerInterval = setInterval(() => {
                    if (timeLeft > 0 && !isQuizCompleted) {
                        timeLeft--;
                        // Save progress after answering
                        saveProgress();
                        
                        // Track that we've answered at least one question
                        hasAnsweredQuestion = true;
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
                timerEl.textContent =
                    `${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;

                // Change color when time is running low
                if (timeLeft <= 60) {
                    timerEl.classList.add('text-red-600', 'pulse-animation');
                    timerEl.classList.remove('text-blue-600');
                } else if (timeLeft <= 300) {
                    timerEl.classList.add('text-orange-600');
                    timerEl.classList.remove('text-blue-600');
                }
            }

            // Save progress to localStorage
            function saveProgress() {
                const progress = {
                    quizId: {{ $quiz->id }},
                    currentQuestionIndex: currentQuestionIndex,
                    timeLeft: timeLeft,
                    answers: userAnswers,
                    results: quizResults
                };
                localStorage.setItem('quizProgress', JSON.stringify(progress));
            }

            // Format time for display
            function formatTime(seconds) {
                const minutes = Math.floor(seconds / 60);
                const secs = seconds % 60;
                return `${minutes}:${secs.toString().padStart(2, '0')}`;
            }

            // Show quiz results
            function showResults(correctAnswers, timeTaken) {
                const totalQuestions = questionContainers.length;
                const percentage = Math.round((correctAnswers / totalQuestions) * 100);

                // Update results modal
                resultsScore.textContent = `${percentage}%`;
                correctAnswersEl.textContent = correctAnswers;
                statCorrectEl.textContent = correctAnswers;
                statIncorrectEl.textContent = quizResults.incorrect;
                statTimeTakenEl.textContent = formatTime(timeTaken);

                // Show modal
                resultsModal.classList.remove('hidden');

                // Add animation
                setTimeout(() => {
                    resultsModal.querySelector('.bg-white').classList.add('animate-fadeIn');
                }, 100);
            }

            // Submit quiz form
            function submitQuizForm(isTimeUp = false) {
                if (isQuizCompleted) return;

                isQuizCompleted = true;

                // Clear the timer
                if (timerInterval) {
                    clearInterval(timerInterval);
                    timerInterval = null;
                }

                // Clear any pending auto-advance
                if (window.autoAdvanceTimeout) {
                    clearTimeout(window.autoAdvanceTimeout);
                    window.autoAdvanceTimeout = null;
                }

                // Calculate time taken
                const endTime = Date.now();
                const startTime = parseInt(localStorage.getItem('quizStartTime')) || quizStartTime;
                const timeTaken = Math.round((endTime - startTime) / 1000); // in seconds

                // Set the end time and time taken in the form
                document.getElementById('endTime').value = new Date(endTime).toISOString();
                document.getElementById('timeTaken').value = timeTaken;

                // Add time_up flag if applicable
                if (isTimeUp) {
                    const timeUpInput = document.createElement('input');
                    timeUpInput.type = 'hidden';
                    timeUpInput.name = 'time_up';
                    timeUpInput.value = '1';
                    quizForm.appendChild(timeUpInput);
                }

                // Calculate final score
                let correctAnswers = 0;
                questionContainers.forEach(container => {
                    const questionId = container.dataset.questionId;
                    const selectedAnswer = userAnswers[questionId];
                    const correctAnswer = container.dataset.correctAnswer;

                    if (selectedAnswer && selectedAnswer === correctAnswer) {
                        correctAnswers++;
                    }
                });

                // Show results
                showResults(correctAnswers, timeTaken);

                // Clear saved progress and start time
                localStorage.removeItem('quizProgress');
                localStorage.removeItem('quizStartTime');

                // Submit the form asynchronously
                fetch(quizForm.action, {
                        method: 'POST',
                        body: new FormData(quizForm),
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json'
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        console.log('Quiz submitted successfully:', data);
                    })
                    .catch(error => {
                        console.error('Error submitting quiz:', error);
                    });
            }

            // Event listeners

            // Show signup nudge modal with animation
            function showSignupNudge() {
                // Never show the nudge for logged-in users
                if (isLoggedIn) return;
                
                const modal = document.getElementById('signupNudgeModal');
                if (modal) {
                    // Show the modal
                    modal.style.display = 'flex';
                    // Trigger reflow to enable animation
                    void modal.offsetWidth;
                    modal.classList.add('active');
                    
                    // Lock the quiz after showing the nudge
                    isQuizLocked = true;
                    localStorage.setItem('isQuizLockedForGuest', 'true');
                    
                    // Disable all inputs
                    document.querySelectorAll('.answer-option input[type="radio"]').forEach(input => {
                        input.disabled = true;
                    });
                    
                    // Disable navigation
                    document.querySelectorAll('.btn-next, .btn-prev, #submitQuiz').forEach(btn => {
                        btn.disabled = true;
                    });
                }
            }
            
            // Close signup nudge modal
            function closeSignupNudge() {
                const modal = document.getElementById('signupNudgeModal');
                if (modal) {
                    modal.classList.remove('active');
                    setTimeout(() => {
                        modal.style.display = 'none';
                    }, 300);
                }
            }
            
            // Close modal when clicking outside
            document.addEventListener('click', function(e) {
                console.log('Document click detected', e.target);
                const modal = document.getElementById('signupNudgeModal');
                if (e.target === modal) {
                    console.log('Clicked on modal overlay');
                    // Don't allow closing by clicking outside
                    return false;
                }
            });
            
            // Debug: Log all clicks on the document
            document.addEventListener('click', function(e) {
                console.log('Click detected on:', e.target);
                if (e.target.closest('#signupNudgeModal')) {
                    console.log('Click inside modal');
                }
            }, true);

            // Answer selection
            questionContainers.forEach(container => {
                const inputs = container.querySelectorAll('input[type="radio"]');
                const questionId = container.dataset.questionId;

                inputs.forEach(input => {
                    input.addEventListener('change', function() {
                        // Prevent changes if already answered
                        if (userAnswers[questionId] !== undefined) {
                            return;
                        }

                        const answerOptions = container.querySelectorAll('.answer-option');

                        // Clear previous selection
                        answerOptions.forEach(opt => opt.classList.remove('selected'));

                        // Mark selected answer
                        this.closest('.answer-option').classList.add('selected');

                        // Update user answers and lock the question
                        userAnswers[questionId] = this.value;

                        // Lock all inputs for this question
                        const allInputs = container.querySelectorAll('input[type="radio"]');
                        allInputs.forEach(inp => {
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
                        
                        // Schedule auto-advance if enabled
                        if (autoNextEnabled && currentQuestionIndex < questionContainers.length - 1) {
                            // Show signup nudge after answering first question and moving to next
                            if (Object.keys(userAnswers).length === 1) {
                                // Show the next question first
                                showQuestion(currentQuestionIndex + 1);
                                // Then show the nudge
                                setTimeout(showSignupNudge, 300);
                                return;
                            }
                            // Clear any existing timeout to prevent multiple auto-advances
                            if (window.autoAdvanceTimeout) {
                                clearTimeout(window.autoAdvanceTimeout);
                                window.autoAdvanceTimeout = null;
                            }

                            // Set new timeout for auto-advance
                            window.autoAdvanceTimeout = setTimeout(() => {
                                // Double-check autoNextEnabled in case it was changed during the delay
                                if (autoNextEnabled && currentQuestionIndex <
                                    questionContainers.length - 1) {
                                    showQuestion(currentQuestionIndex + 1);
                                }
                                window.autoAdvanceTimeout = null;
                            }, 1500);
                        }

                        saveProgress();
                    });
                });
            });

            // Delegate click events for next/previous buttons
            document.addEventListener('click', function(e) {
                // Show signup nudge if quiz is locked and user is not logged in
                if (isQuizLocked && !isLoggedIn) {
                    e.preventDefault();
                    showNudge();
                    return;
                }
                
                // Handle next button click
                if (e.target.classList.contains('btn-next') || e.target.closest('.btn-next')) {
                    e.preventDefault();
                    const button = e.target.classList.contains('btn-next') ? e.target : e.target.closest(
                        '.btn-next');
                    if (button && !button.disabled && currentQuestionIndex < questionContainers.length -
                        1) {
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
                    const button = e.target.classList.contains('btn-prev') ? e.target : e.target.closest(
                        '.btn-prev');
                    if (button && currentQuestionIndex > 0) {
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

                    if (isQuizCompleted) return;

                    submitQuizForm(false);
                });
            }

            // Review answers button
            if (reviewButton) {
                reviewButton.addEventListener('click', function() {
                    resultsModal.classList.add('hidden');
                    showQuestion(0);
                });
            }

            // Clear any pending auto-advance when leaving the page
            window.addEventListener('beforeunload', function() {
                if (window.autoAdvanceTimeout) {
                    clearTimeout(window.autoAdvanceTimeout);
                    window.autoAdvanceTimeout = null;
                }
            });

            // Initialize the quiz
            initQuiz();
        });

        // Social sharing functions
        function shareOnTwitter() {
            const score = document.getElementById('resultsScore').textContent;
            const text = `I just scored ${score} on this quiz! Can you beat my score?`;
            const url = window.location.href;
            window.open(`https://twitter.com/intent/tweet?text=${encodeURIComponent(text)}&url=${encodeURIComponent(url)}`,
                '_blank', 'width=600,height=400');
        }

        function shareOnFacebook() {
            const url = window.location.href;
            window.open(`https://www.facebook.com/sharer/sharer.php?u=${encodeURIComponent(url)}`, '_blank',
                'width=600,height=400');
        }

        function copyResultLink() {
            const url = window.location.href;
            navigator.clipboard.writeText(url).then(() => {
                alert('Link copied to clipboard!');
            }).catch(err => {
                console.error('Failed to copy link:', err);
                // Fallback for older browsers
                const textArea = document.createElement('textarea');
                textArea.value = url;
                document.body.appendChild(textArea);
                textArea.select();
                try {
                    document.execCommand('copy');
                    alert('Link copied to clipboard!');
                } catch (err) {
                    alert('Failed to copy link. Please copy manually: ' + url);
                }
                document.body.removeChild(textArea);
            });
        }
    </script>
@endpush
