@extends('layouts.dashboard')

@push('meta')
    <meta name="csrf-token" content="{{ csrf_token() }}">
@endpush

@php
    $title = __('Guest Quiz');
    $activeRoute = 'guest-quiz';
@endphp

@push('styles')
<style>
    .question-container {
        display: none;
    }
    .question-container.active {
        display: block;
        animation: fadeIn 0.3s ease-in-out;
    }
    .answer-option {
        transition: all 0.2s ease;
    }
    .answer-option:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
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
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }
    #progressBar {
        transition: width 0.3s ease;
    }
</style>
@endpush

@section('dashboard-content')
<div class="py-6">
            <div class="text-center mb-8">
                <h1 class="text-3xl font-bold text-gray-900 mb-2">{{ $quiz->getTranslation('title', app()->getLocale()) }}</h1>
                <p class="text-lg text-gray-600">{{ $quiz->getTranslation('description', app()->getLocale()) }}</p>
            </div>

            <div class="bg-white rounded-xl shadow-md overflow-hidden">
                <!-- Quiz Header -->
                <div class="p-6 border-b border-gray-200">
                    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center space-y-4 sm:space-y-0">
                        <div>
                            <h2 class="text-xl font-semibold text-gray-800" id="questionCounter">
                                {{ __('Question') }} <span id="currentQuestion">1</span> {{ __('of') }} <span id="totalQuestions">{{ $quiz->questions->count() }}</span>
                            </h2>
                            <div class="w-full bg-gray-200 rounded-full h-2.5 mt-2">
                                <div id="progressBar" class="bg-blue-600 h-2.5 rounded-full" style="width: 0%"></div>
                            </div>
                        </div>
                        <div class="flex items-center text-sm text-gray-600">
                            <svg class="w-5 h-5 mr-1 text-yellow-500" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd" />
                            </svg>
                            <span id="timer">00:00</span>
                        </div>
                    </div>
                </div>

                <!-- Quiz Content -->
                <div class="p-6">
                    <form id="quizForm" action="{{ route('guest-quiz.submit', ['locale' => app()->getLocale(), 'quiz' => $quiz->id]) }}" method="POST">
                        @csrf
                        <input type="hidden" name="quiz_id" value="{{ $quiz->id }}">
                        
                        @foreach($quiz->questions as $index => $question)
                            <div class="question-container {{ $index === 0 ? 'active' : '' }}" id="question-{{ $question->id }}" data-question-index="{{ $index }}">
                                <div class="mb-6">
                                    <h3 class="text-lg font-medium text-gray-900 mb-4">{{ $question->getTranslation('text', app()->getLocale()) }}</h3>
                                    
                                    @if($question->image_path)
                                        <div class="mb-4">
                                            <img src="{{ asset('storage/' . $question->image_path) }}" alt="Question Image" class="max-w-full h-auto rounded-lg">
                                        </div>
                                    @endif
                                    
                                    <div class="space-y-3">
                                        @foreach($question->answers as $answer)
                                            <label class="answer-option block p-4 border border-gray-200 rounded-lg cursor-pointer hover:bg-gray-50 transition-colors">
                                                <div class="flex items-center">
                                                    <input type="radio" 
                                                           name="answers[{{ $question->id }}]" 
                                                           value="{{ $answer->id }}" 
                                                           class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300"
                                                           data-question-id="{{ $question->id }}">
                                                    <span class="ml-3 text-gray-700">{{ $answer->getTranslation('text', app()->getLocale()) }}</span>
                                                </div>
                                            </label>
                                        @endforeach
                                    </div>
                                    
                                    @if($question->explanation)
                                        <div class="mt-4 p-4 bg-blue-50 rounded-lg text-sm text-blue-700 explanation hidden">
                                            <strong>{{ __('Explanation') }}:</strong>
                                            <p class="mt-1">{{ $question->getTranslation('explanation', app()->getLocale()) }}</p>
                                        </div>
                                    @endif
                                </div>
                                
                                <div class="flex justify-between mt-6">
                                    <button type="button" class="btn-prev px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition-colors {{ $index === 0 ? 'invisible' : '' }}">
                                        {{ __('Previous') }}
                                    </button>
                                    
                                    @if($index < $quiz->questions->count() - 1)
                                        <button type="button" class="btn-next ml-auto px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                                            {{ __('Next') }}
                                        </button>
                                    @else
                                        <button type="button" id="submitQuiz" class="ml-auto px-6 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors">
                                            {{ __('Submit Quiz') }}
                                        </button>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </form>
                </div>
            </div>
            
            <!-- Results Modal -->
            <div id="resultsModal" class="hidden fixed inset-0 bg-black bg-opacity-50 items-center justify-center p-4 z-50">
                <div class="bg-white rounded-xl max-w-md w-full p-6 max-h-[90vh] overflow-y-auto">
                    <div class="text-center">
                        <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-green-100">
                            <svg class="h-10 w-10 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                        </div>
                        <h3 class="text-xl font-medium text-gray-900 mt-4" id="resultsTitle"></h3>
                        <div class="mt-2">
                            <p class="text-gray-600">{{ __('You scored') }} <span id="resultsScore" class="font-bold text-2xl">0</span>%</p>
                            <p class="text-sm text-gray-500 mt-1">
                                <span id="correctAnswers">0</span> {{ __('out of') }} {{ $quiz->questions->count() }} {{ __('questions correct') }}
                            </p>
                        </div>
                        
                        <div class="mt-6">
                            <a href="{{ route('home', ['locale' => app()->getLocale()]) }}" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                {{ __('Back to Home') }}
                            </a>
                            <button type="button" id="reviewAnswers" class="ml-3 inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                {{ __('Review Answers') }}
                            </button>
                        </div>
                    </div>
                </div>
            </div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const quizForm = document.getElementById('quizForm');
        const questionContainers = document.querySelectorAll('.question-container');
        const currentQuestionEl = document.getElementById('currentQuestion');
        const totalQuestionsEl = document.getElementById('totalQuestions');
        const progressBar = document.getElementById('progressBar');
        const nextButtons = document.querySelectorAll('.btn-next');
        const prevButtons = document.querySelectorAll('.btn-prev');
        const submitButton = document.getElementById('submitQuiz');
        const resultsModal = document.getElementById('resultsModal');
        const resultsTitle = document.getElementById('resultsTitle');
        const resultsScore = document.getElementById('resultsScore');
        const correctAnswersEl = document.getElementById('correctAnswers');
        const reviewButton = document.getElementById('reviewAnswers');
        const timerEl = document.getElementById('timer');
        
        let currentQuestionIndex = 0;
        let timeLeft = {{ $quiz->time_limit * 60 || 1800 }}; // Default to 30 minutes if not set
        let timerInterval;
        let userAnswers = {};
        let quizResults = null;
        
        // Initialize quiz
        function initQuiz() {
            totalQuestionsEl.textContent = questionContainers.length;
            updateProgressBar();
            startTimer();
            
            // Load saved progress if exists
            const savedProgress = localStorage.getItem('guestQuizProgress');
            if (savedProgress) {
                const progress = JSON.parse(savedProgress);
                if (progress.quizId === {{ $quiz->id }}) {
                    userAnswers = progress.answers || {};
                    currentQuestionIndex = progress.currentQuestionIndex || 0;
                    timeLeft = progress.timeLeft || timeLeft;
                    updateTimerDisplay();
                    
                    // Restore selected answers
                    Object.entries(userAnswers).forEach(([questionId, answerId]) => {
                        const input = document.querySelector(`input[name="answers[${questionId}]"][value="${answerId}"]`);
                        if (input) {
                            input.checked = true;
                            input.closest('.answer-option').classList.add('selected');
                        }
                    });
                }
            }
            
            showQuestion(currentQuestionIndex);
        }
        
        // Show question by index
        function showQuestion(index) {
            // Hide all questions
            questionContainers.forEach(container => {
                container.classList.remove('active');
            });
            
            // Show current question
            questionContainers[index].classList.add('active');
            currentQuestionIndex = index;
            currentQuestionEl.textContent = index + 1;
            updateProgressBar();
            updateNavigationButtons();
            
            // Save progress
            saveProgress();
        }
        
        // Update progress bar
        function updateProgressBar() {
            const progress = ((currentQuestionIndex + 1) / questionContainers.length) * 100;
            progressBar.style.width = `${progress}%`;
        }
        
        // Update navigation buttons
        function updateNavigationButtons() {
            const prevButtons = document.querySelectorAll('.btn-prev');
            const nextButtons = document.querySelectorAll('.btn-next');
            
            prevButtons.forEach(btn => {
                if (currentQuestionIndex === 0) {
                    btn.classList.add('invisible');
                } else {
                    btn.classList.remove('invisible');
                }
            });
            
            nextButtons.forEach(btn => {
                const questionId = btn.closest('.question-container').dataset.questionIndex;
                const questionInputs = document.querySelectorAll(`#question-${questionId} input[type="radio"]`);
                const isAnswered = Array.from(questionInputs).some(input => input.checked);
                
                if (isAnswered) {
                    btn.disabled = false;
                    btn.classList.remove('opacity-50', 'cursor-not-allowed');
                } else {
                    btn.disabled = true;
                    btn.classList.add('opacity-50', 'cursor-not-allowed');
                }
            });
        }
        
        // Start timer
        function startTimer() {
            clearInterval(timerInterval);
            updateTimerDisplay();
            
            timerInterval = setInterval(() => {
                timeLeft--;
                updateTimerDisplay();
                
                if (timeLeft <= 0) {
                    clearInterval(timerInterval);
                    submitQuiz();
                }
            }, 1000);
        }
        
        // Update timer display
        function updateTimerDisplay() {
            const minutes = Math.floor(timeLeft / 60);
            const seconds = timeLeft % 60;
            timerEl.textContent = `${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;
        }
        
        // Save progress to localStorage
        function saveProgress() {
            const progress = {
                quizId: {{ $quiz->id }},
                currentQuestionIndex: currentQuestionIndex,
                timeLeft: timeLeft,
                answers: userAnswers
            };
            localStorage.setItem('guestQuizProgress', JSON.stringify(progress));
        }
        
        // Submit quiz
        async function submitQuiz() {
            clearInterval(timerInterval);
            
            // Show loading state
            const submitButton = document.querySelector('.btn-submit');
            if (submitButton) {
                submitButton.disabled = true;
                submitButton.innerHTML = `
                    <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white inline" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    {{ __('Submitting...') }}
                `;
            }
            
            try {
                // Create a FormData object to send the data
                const formData = new FormData();
                formData.append('_token', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));
                formData.append('answers', JSON.stringify(userAnswers));
                
                const response = await fetch('{{ route("guest-quiz.submit", ['locale' => app()->getLocale(), 'quiz' => $quiz->id]) }}', {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: formData
                });
                
                const data = await response.json();
                
                if (!response.ok) {
                    throw new Error(data.message || 'Failed to submit quiz');
                }
                
                const data = await response.json();
                
                if (data.success) {
                    quizResults = data.results;
                    showResults(quizResults);
                    // Clear saved progress on successful submission
                    localStorage.removeItem('guestQuizProgress');
                } else {
                    alert('There was an error submitting your quiz. ' + (data.message || 'Please try again.'));
                }
            } catch (error) {
                console.error('Error submitting quiz:', error);
                console.error('Error details:', error);
                let errorMessage = 'There was an error submitting your quiz. ';
                if (error.message) {
                    errorMessage += error.message;
                } else {
                    errorMessage += 'Please check your connection and try again.';
                }
                alert(errorMessage);
            }
        }
        
        // Show quiz results
        function showResults(results) {
            resultsScore.textContent = results.score;
            correctAnswersEl.textContent = results.correct_answers;
            
            if (results.passed) {
                resultsTitle.textContent = '{{ __("Congratulations!") }}';
                resultsTitle.classList.add('text-green-600');
            } else {
                resultsTitle.textContent = '{{ __("Quiz Completed") }}';
                resultsTitle.classList.add('text-blue-600');
            }
            
            resultsModal.classList.remove('hidden');
        }
        
        // Event listeners
        questionContainers.forEach(container => {
            const inputs = container.querySelectorAll('input[type="radio"]');
            inputs.forEach(input => {
                input.addEventListener('change', function() {
                    const questionId = this.name.match(/\[(\d+)\]/)[1];
                    userAnswers[questionId] = this.value;
                    
                    // Update UI
                    const answerOptions = container.querySelectorAll('.answer-option');
                    answerOptions.forEach(option => option.classList.remove('selected'));
                    this.closest('.answer-option').classList.add('selected');
                    
                    // Enable next button if it's disabled
                    const nextButton = container.querySelector('.btn-next');
                    if (nextButton && nextButton.disabled) {
                        nextButton.disabled = false;
                        nextButton.classList.remove('opacity-50', 'cursor-not-allowed');
                    }
                    
                    saveProgress();
                });
            });
        });
        
        nextButtons.forEach(button => {
            button.addEventListener('click', function() {
                if (currentQuestionIndex < questionContainers.length - 1) {
                    showQuestion(currentQuestionIndex + 1);
                }
            });
        });
        
        prevButtons.forEach(button => {
            button.addEventListener('click', function() {
                if (currentQuestionIndex > 0) {
                    showQuestion(currentQuestionIndex - 1);
                }
            });
        });
        
        if (submitButton) {
            submitButton.addEventListener('click', function() {
                if (confirm('{{ __("Are you sure you want to submit your quiz?") }}')) {
                    submitQuiz();
                }
            });
        }
        
        reviewButton.addEventListener('click', function() {
            resultsModal.classList.add('hidden');
            showQuestion(0);
            
            // Highlight correct/incorrect answers
            if (quizResults) {
                quizResults.details.forEach((result, index) => {
                    const questionContainer = questionContainers[index];
                    const inputs = questionContainer.querySelectorAll('input[type="radio"]');
                    
                    inputs.forEach(input => {
                        const answerOption = input.closest('.answer-option');
                        if (input.checked) {
                            answerOption.classList.add(result.correct ? 'correct' : 'incorrect');
                        } else if (input.value === result.correct_answer_id) {
                            answerOption.classList.add('correct');
                        }
                        
                        // Disable all inputs in review mode
                        input.disabled = true;
                    });
                    
                    // Show explanation if available
                    const explanation = questionContainer.querySelector('.explanation');
                    if (explanation) {
                        explanation.classList.remove('hidden');
                    }
                });
            }
        });
        
        // Initialize quiz
        initQuiz();
        
        // Save progress before page unload
        window.addEventListener('beforeunload', (event) => {
            if (Object.keys(userAnswers).length > 0 && !quizResults) {
                event.preventDefault();
                event.returnValue = 'You have unsaved changes. Are you sure you want to leave?';
                return event.returnValue;
            }
        });
    });
</script>
@endpush
