@php
    // Component: resources/views/components/quiz/unified-quiz-taker.blade.php
    // Props
    $quiz = $quiz ?? null;
    $attempt = $attempt ?? null;
    $showHeader = $showHeader ?? true;
    $compactMode = $compactMode ?? false;
    $allowNavigation = $allowNavigation ?? true;

    // Extract quiz data
    $quizId = $quiz ? (is_array($quiz) ? $quiz['id'] : $quiz->id) : null;
    $quizTitle = $quiz
        ? (is_array($quiz)
            ? $quiz['title']
            : $quiz->getTranslation('title', app()->getLocale()))
        : 'Quiz';
    $timeLimit = $quiz ? (is_array($quiz) ? $quiz['time_limit_minutes'] : $quiz->time_limit_minutes) : 10;
    $totalQuestions = $quiz ? (is_array($quiz) ? count($quiz['questions']) : $quiz->questions->count()) : 0;
    $isGuest = !auth()->check();
    $currentQuestionIndex = 0;
    $questions = $quiz ? (is_array($quiz) ? $quiz['questions'] : $quiz->questions) : [];
@endphp

<div x-data="quizTaker({
    quizId: {{ json_encode($quizId) }},
    totalQuestions: {{ $totalQuestions }},
    timeLimit: {{ $timeLimit }},
    isGuest: {{ $isGuest ? 'true' : 'false' }},
    questions: {{ json_encode($questions) }},
    currentAttempt: {{ json_encode($attempt) }},
    allowNavigation: {{ $allowNavigation ? 'true' : 'false' }}
})" x-init="init()" class="flex flex-col h-full bg-white dark:bg-gray-900">

    <!-- Loading Skeleton (shown before Alpine initializes) -->
    <div x-show="!initialized" class="flex flex-col h-full bg-white dark:bg-gray-900">
        @if ($showHeader)
            <div class="bg-gray-50 dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700 px-3 sm:px-4 py-2">
                <div class="flex items-center justify-between text-sm">
                    <div class="flex items-center gap-3 sm:gap-4">
                        <!-- Title skeleton -->
                        <div class="h-4 w-32 bg-gray-300/60 dark:bg-gray-600/40 rounded animate-pulse"></div>
                        <div class="flex items-center gap-1.5 text-gray-600 dark:text-gray-300">

                            <div class="h-4 w-8 bg-gray-300/60 dark:bg-gray-600/40 rounded animate-pulse"></div>
                        </div>
                        <div class="hidden sm:flex items-center gap-1.5 text-gray-600 dark:text-gray-300">

                            <div class="h-4 w-6 bg-gray-300/60 dark:bg-gray-600/40 rounded animate-pulse"></div>
                        </div>
                    </div>
                    <div class="flex items-center gap-2">
                        <div class="h-4 w-8 bg-gray-300/60 dark:bg-gray-600/40 rounded animate-pulse"></div>
                        <div class="h-4 w-8 bg-gray-300/60 dark:bg-gray-600/40 rounded animate-pulse"></div>
                    </div>
                </div>
                <!-- Progress bar skeleton -->
                <div class="w-full dark:bg-gray-700 bg-gray-200 rounded-full h-1.5 mt-2">
                    <div class="h-1.5 rounded-full bg-gray-300/60 dark:bg-gray-600/40 animate-pulse"></div>
                </div>
            </div>

            <!-- Second header line skeleton -->
            <div class="bg-gray-100 dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700 px-3 sm:px-4 py-1">
                <div class="flex items-center justify-between">
                    <div class="h-4 w-16 bg-gray-300/60 dark:bg-gray-600/40 rounded animate-pulse"></div>
                    <div class="flex gap-1">
                        <div class="h-8 w-8 bg-gray-300/60 dark:bg-gray-600/40 rounded-full animate-pulse"></div>
                        <div class="h-8 w-8 bg-gray-300/60 dark:bg-gray-600/40 rounded-full animate-pulse"></div>
                        <div class="h-8 w-8 bg-gray-300/60 dark:bg-gray-600/40 rounded-full animate-pulse"></div>
                        <div class="h-8 w-8 bg-gray-300/60 dark:bg-gray-600/40 rounded-full animate-pulse"></div>
                    </div>
                </div>
            </div>
        @endif

        <!-- Question content skeleton -->
        <div class="flex-1 overflow-y-auto p-4 min-h-0">
            <div class="mb-6">
                <!-- Question title skeleton -->
                <div class="h-6 w-3/4 bg-gray-300/60 dark:bg-gray-600/40 rounded animate-pulse mb-4"></div>

                <!-- Answer options skeleton -->
                <div class="space-y-3">
                    @for ($i = 0; $i < 4; $i++)
                        <div class="p-4 border border-gray-200 dark:border-gray-600 rounded-lg">
                            <div class="flex items-center">
                                <div class="w-5 h-5 rounded-full border-2 border-gray-300 dark:border-gray-500 mr-3">
                                </div>
                                <div class="flex-1">
                                    <div class="h-4 w-full bg-gray-300/60 dark:bg-gray-600/40 rounded animate-pulse">
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endfor
                </div>
            </div>
        </div>

        <!-- Controls skeleton -->
        <div class="border-t border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 p-2 sm:p-4">
            <div class="flex justify-between items-center gap-2">
                <div class="h-10 w-20 bg-gray-300/60 dark:bg-gray-600/40 rounded animate-pulse"></div>
                <div class="h-10 w-32 bg-gray-300/60 dark:bg-gray-600/40 rounded animate-pulse"></div>
            </div>
        </div>
    </div>

    <!-- Quiz Content (shown when initialized) -->
    <div x-cloak class="flex flex-col h-full min-h-0">
        <div x-show="initialized" x-transition class="flex flex-col h-full min-h-0">

            @if ($showHeader)
                <!-- Header Line 1: Quiz Info -->
                <div x-cloak
                    class="bg-gray-50 dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700 px-3 sm:px-4 py-2">
                    <div class="flex items-center justify-between text-sm">
                        <div class="flex items-center gap-3 sm:gap-4">
                            <h1
                                class="font-semibold truncate max-w-[120px] sm:max-w-[180px] md:max-w-[250px] text-gray-800 dark:text-gray-100 text-sm sm:text-base">
                                {{ $quizTitle }}</h1>
                            <div class="flex items-center gap-1.5 text-gray-600 dark:text-gray-300">
                                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <span x-text="formatTime(timeLeft)" class="font-medium text-sm sm:text-base"></span>
                            </div>
                            <div class="hidden sm:flex items-center gap-1.5 text-gray-600 dark:text-gray-300">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                                <span class="font-medium">{{ $totalQuestions }}Q</span>
                            </div>
                        </div>
                        <div class="flex items-center gap-2">
                            <span
                                class="bg-green-500 text-white px-2 py-1 rounded text-xs sm:text-sm min-w-[36px] text-center font-medium">
                                <span x-text="correctCount">0</span>
                            </span>
                            <span
                                class="bg-red-500 text-white px-2 py-1 rounded text-xs sm:text-sm min-w-[36px] text-center font-medium">
                                <span x-text="incorrectCount">0</span>
                            </span>
                        </div>
                    </div>

                    <!-- Progress Bar -->
                    <div class="w-full dark:bg-gray-700 bg-gray-200 rounded-full h-1.5 mt-2">
                        <div class="h-1.5 rounded-full transition-colors duration-300"
                            :style="{
                                'width': (currentQuestionIndex >= 19 ? 100 : ((currentQuestionIndex / totalQuestions) *
                                    100)) + '%',
                                'background-color': getProgressBarColor()
                            }">
                        </div>
                    </div>
                </div>

                <!-- Header Line 2: Question Navigation -->
                <div x-cloak
                    class="bg-gray-100 dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700 px-3 sm:px-4 py-1 text-sm flex items-center justify-between text-gray-800 dark:text-gray-200">
                    <div class="flex items-center gap-2">
                        <span class="font-medium text-sm sm:text-base">Q.<span
                                x-text="currentQuestionIndex + 1"></span><span class="sm:inline hidden">/<span
                                    x-text="totalQuestions"></span></span></span>
                        <span x-show="showFeedback" x-text="isAnswerCorrect ? '✓' : '✗'"
                            :class="isAnswerCorrect ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400'"
                            class="text-sm font-bold">
                        </span>
                    </div>
                    <div class="flex items-center gap-1">
                        <button @click="togglePause" :disabled="isGuest"
                            class="p-2 sm:p-2.5 rounded-full hover:bg-gray-200 dark:hover:bg-gray-700 text-gray-600 dark:text-gray-300 disabled:opacity-50 disabled:cursor-not-allowed"
                            :title="isGuest ? 'Login to use pause' : (isPaused ? 'Resume' : 'Pause')">
                            <i x-show="!isPaused" class="fas fa-pause"></i>
                            <i x-show="isPaused" class="fas fa-play"></i>
                        </button>
                        <button @click="resetQuiz" x-show="hasPlan && !isGuest"
                            class="p-2 sm:p-2.5 rounded-full hover:bg-gray-200 dark:hover:bg-gray-700 text-red-600 dark:text-red-400"
                            title="Reset quiz">
                            <i class="fas fa-sync-alt"></i>
                        </button>
                        <button @click="flagQuestion" :disabled="isGuest"
                            class="p-2 sm:p-2.5 rounded-full hover:bg-gray-200 dark:hover:bg-gray-700 disabled:opacity-50 disabled:cursor-not-allowed"
                            :class="isQuestionFlagged ? 'text-yellow-500' : 'text-gray-600 dark:text-gray-300'"
                            :title="isGuest ? 'Login to flag questions' : 'Flag question'">
                            <i class="fas fa-flag"></i>
                        </button>
                        <button @click="autoAdvance = !autoAdvance" :disabled="isGuest"
                            class="p-2 sm:p-2.5 rounded-full hover:bg-gray-200 dark:hover:bg-gray-700 disabled:opacity-50 disabled:cursor-not-allowed"
                            :class="autoAdvance ? 'text-blue-500' : 'text-gray-600 dark:text-gray-300'"
                            :title="isGuest ? 'Login to use auto-advance' : 'Toggle auto-next'">
                            <i class="fas fa-forward"></i>
                        </button>
                    </div>
                </div>
            @endif

            <!-- Quiz Content -->
            <div x-cloak class="flex-1 overflow-y-auto p-4 min-h-0">
                <!-- Guest Notice -->
                <div x-show="isGuest"
                    class="mb-4 p-4 bg-yellow-50 dark:bg-yellow-900/20 border-l-4 border-yellow-400">
                    <p class="text-yellow-800 dark:text-yellow-200">
                        {{ __('Please login to save your progress and access all features.') }}
                    </p>
                </div>

                <!-- Current Question -->
                <div class="mb-6">
                    <h2 id="quiz-question-title" class="text-xl font-semibold mb-4 text-gray-800 dark:text-gray-100"
                        x-text="currentQuestion.text"></h2>

                    <!-- Question Image -->
                    <div x-show="currentQuestion.image" class="mb-4">
                        <img :src="currentQuestion.image" alt="Question image"
                            class="max-w-full h-auto rounded-lg border border-gray-200 dark:border-gray-700">
                    </div>

                    <!-- Answer Options -->
                    <div class="space-y-3" :class="{ 'answer-locked': isAnswerSubmitted }">
                        <template x-for="(option, index) in currentQuestion.options" :key="option.id">
                            <div>
                                <input type="radio" :id="'option-' + option.id"
                                    :name="'question-' + currentQuestion.id" :value="option.id"
                                    x-model="selectedOption" :disabled="isAnswerSubmitted" class="hidden peer"
                                    @change="handleOptionSelect(option)">
                                <label :for="'option-' + option.id"
                                    class="answer-option flex items-center p-4 border rounded-lg cursor-pointer transition-colors"
                                    :class="{
                                        'border-blue-500 bg-blue-50 dark:bg-blue-900/20': selectedOption === option
                                            .id && !
                                            isAnswerSubmitted,
                                        'border-green-500 bg-green-50 dark:bg-green-900/20': isAnswerSubmitted && option
                                            .is_correct,
                                        'border-red-500 bg-red-50 dark:bg-red-900/20': isAnswerSubmitted &&
                                            selectedOption ===
                                            option.id && !option.is_correct,
                                        'border-gray-200 dark:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-800':
                                            !
                                            isAnswerSubmitted || (isAnswerSubmitted && !option.is_correct)
                                    }">
                                    <div class="flex items-center h-5">
                                        <div class="w-5 h-5 rounded-full border flex items-center justify-center"
                                            :class="{
                                                'border-blue-500 bg-blue-500': selectedOption === option.id && !
                                                    isAnswerSubmitted,
                                                'border-green-500 bg-green-500': isAnswerSubmitted && option.is_correct,
                                                'border-red-500 bg-red-500': isAnswerSubmitted && selectedOption ===
                                                    option
                                                    .id && !option.is_correct,
                                                'border-gray-300 dark:border-gray-500': !selectedOption || (
                                                    selectedOption !==
                                                    option.id && !(isAnswerSubmitted && option.is_correct))
                                            }">
                                            <div x-show="selectedOption === option.id && !isAnswerSubmitted"
                                                class="w-3 h-3 rounded-full bg-white"></div>
                                            <svg x-show="isAnswerSubmitted && option.is_correct"
                                                class="w-3 h-3 text-white" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3"
                                                    d="M5 13l4 4L19 7" />
                                            </svg>
                                            <svg x-show="isAnswerSubmitted && selectedOption === option.id && !option.is_correct"
                                                class="w-3 h-3 text-white" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3"
                                                    d="M6 18L18 6M6 6l12 12" />
                                            </svg>
                                        </div>
                                    </div>
                                    <div class="ml-3 text-sm">
                                        <p class="font-medium text-gray-900 dark:text-gray-100" x-text="option.text">
                                        </p>
                                    </div>
                                </label>

                                <!-- Explanation (shown after answer is submitted) -->
                                <div x-show="isAnswerSubmitted && option.is_correct && option.explanation"
                                    class="mt-2 ml-8 text-sm text-gray-600 dark:text-gray-300">
                                    <p x-text="option.explanation"></p>
                                </div>
                            </div>
                        </template>

                        <!-- Feedback section -->
                        <div x-show="showFeedback" class="mt-4 p-4 rounded-lg"
                            :class="{
                                'bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800': isAnswerCorrect,
                                'bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800': !
                                    isAnswerCorrect &&
                                    isAnswerSubmitted
                            }">
                            <div x-show="isAnswerCorrect"
                                class="flex items-center text-green-700 dark:text-green-300">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M5 13l4 4L19 7" />
                                </svg>
                                <span x-text="feedbackMessage"></span>
                            </div>
                            <div x-show="!isAnswerCorrect && isAnswerSubmitted"
                                class="flex items-center text-red-700 dark:text-red-300">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M6 18L18 6M6 6l12 12" />
                                </svg>
                                <span x-text="feedbackMessage"></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quiz Controls -->
            <div x-cloak class="border-t border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 p-2 sm:p-4">
                <div class="flex justify-between items-center gap-2">
                    <button @click="previousQuestion" :disabled="currentQuestionIndex === 0"
                        class="px-3 py-2 sm:px-4 sm:py-2 border border-gray-300 dark:border-gray-600 rounded-md text-sm font-medium text-gray-700 dark:text-gray-200 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 disabled:opacity-50 disabled:cursor-not-allowed">
                        <span class="hidden sm:inline">Previous</span>
                        <svg class="w-4 h-4 sm:hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M15 19l-7-7 7-7" />
                        </svg>
                    </button>

                    <div class="items-center gap-2 hidden sm:flex">
                        <span class="text-sm text-gray-500 dark:text-gray-400"
                            x-text="`${currentQuestionIndex + 1} of ${totalQuestions}`"></span>
                    </div>

                    <button @click="nextQuestion"
                        class="px-3 py-2 sm:px-4 sm:py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 disabled:opacity-50 disabled:cursor-not-allowed flex-1 sm:flex-none">
                        <span x-text="isGuest ? 'Sign Up to Continue' : (isLastQuestion ? 'Finish' : 'Next')"></span>
                        <svg class="w-4 h-4 ml-1 -mr-1 sm:inline hidden" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </button>
                </div>
            </div>

            <!-- Sign Up/Login Modal -->
            <div x-cloak x-show="showLoginModal" x-cloak
                class="fixed inset-0 bg-black/60 backdrop-blur-sm flex items-center justify-center z-50 p-4">
                <div
                    class="bg-white dark:bg-gray-800 rounded-2xl p-8 max-w-md w-full mx-4 shadow-2xl border border-gray-100 dark:border-gray-700 transform transition-all duration-300 scale-100">
                    <!-- Header with icon -->
                    <div class="text-center mb-6">
                        <div
                            class="w-16 h-16 bg-gradient-to-r from-blue-500 to-orange-500 rounded-full flex items-center justify-center mx-auto mb-4 shadow-lg">
                            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                        </div>
                        <h3
                            class="text-xl font-bold bg-gradient-to-r from-blue-700 to-blue-900 dark:from-blue-300 dark:to-blue-500 bg-clip-text text-transparent mb-3">
                            Sign Up Required
                        </h3>
                        <p class="text-gray-600 dark:text-gray-300 text-sm leading-relaxed">
                            Please login or sign up to continue with the quiz and save your progress.
                        </p>
                    </div>

                    <!-- Action buttons -->
                    <div class="space-y-3">
                        <a href="{{ route('register', ['redirect' => url()->current()]) }}"
                            class="group relative w-full flex items-center justify-center px-6 py-3 bg-gradient-to-r from-green-600 to-green-700 hover:from-green-700 hover:to-green-800 text-white font-medium rounded-xl shadow-md hover:shadow-lg transform transition-all duration-300 hover:scale-[1.02] hover:-translate-y-0.5">
                            <div
                                class="absolute inset-0 bg-gradient-to-r from-green-400/20 to-green-500/20 rounded-xl opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                            </div>
                            <svg class="w-5 h-5 mr-2 relative z-10" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
                            </svg>
                            <span class="relative z-10">Sign Up</span>
                            <svg class="w-4 h-4 ml-2 transform group-hover:translate-x-1 transition-transform duration-300 relative z-10"
                                fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 5l7 7-7 7" />
                            </svg>
                        </a>

                        <a href="{{ route('login', ['redirect' => url()->current()]) }}"
                            class="group relative w-full flex items-center justify-center px-6 py-3 bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white font-medium rounded-xl shadow-md hover:shadow-lg transform transition-all duration-300 hover:scale-[1.02] hover:-translate-y-0.5">
                            <div
                                class="absolute inset-0 bg-gradient-to-r from-blue-400/20 to-blue-500/20 rounded-xl opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                            </div>
                            <svg class="w-5 h-5 mr-2 relative z-10" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1" />
                            </svg>
                            <span class="relative z-10">Login</span>
                            <svg class="w-4 h-4 ml-2 transform group-hover:translate-x-1 transition-transform duration-300 relative z-10"
                                fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 5l7 7-7 7" />
                            </svg>
                        </a>

                        <button @click="showLoginModal = false"
                            class="w-full px-6 py-3 border border-gray-300 dark:border-gray-600 rounded-xl text-sm font-medium text-gray-700 dark:text-gray-200 bg-gray-50 dark:bg-gray-700 hover:bg-gray-100 dark:hover:bg-gray-600 transform transition-all duration-300 hover:scale-[1.02]">
                            Cancel
                        </button>
                    </div>

                    <!-- Additional info -->
                    <div class="mt-6 text-center">
                        <p class="text-xs text-gray-500 dark:text-gray-400">
                            By signing up, you agree to our Terms of Service and Privacy Policy
                        </p>
                    </div>
                </div>
            </div>

            <!-- Results Modal -->
            <div x-cloak x-show="showResultsModal" x-cloak
                class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
                <div
                    class="bg-white dark:bg-gray-800 rounded-lg p-6 max-w-md w-full mx-4 max-h-[90vh] overflow-y-auto">
                    <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">
                        {{ __('Quiz Completed!') }}
                    </h3>

                    <!-- XP Animation Container -->
                    <div x-show="xpAnimation.show" x-transition:enter="transition ease-out duration-500"
                        x-transition:enter-start="opacity-0 transform scale-75"
                        x-transition:enter-end="opacity-100 transform scale-100"
                        class="mb-6 p-4 rounded-lg relative overflow-hidden xp-pulse"
                        :class="{
                            'bg-yellow-50 dark:bg-yellow-900/20 border-2 border-yellow-300 dark:border-yellow-700': xpAnimation
                                .points >=
                                50,
                            'bg-blue-50 dark:bg-blue-900/20 border-2 border-blue-300 dark:border-blue-700': xpAnimation
                                .points >= 30 && xpAnimation.points <
                                50,
                            'bg-green-50 dark:bg-green-900/20 border-2 border-green-300 dark:border-green-700': xpAnimation
                                .points < 30
                        }">
                        <div class="flex items-center justify-center space-x-3">
                            <!-- Diamond Emoji -->
                            <div class="relative">
                                <div class="text-4xl">💎</div>
                                <!-- Traffic Light Emojis -->
                                <div class="absolute -top-2 -right-2 text-xl animate-ping">🟢</div>
                                <div class="absolute -bottom-2 -left-2 text-xl animate-ping"
                                    style="animation-delay: 0.5s">🟢
                                </div>
                            </div>
                            <!-- XP Points Counter -->
                            <div
                                class="text-2xl font-bold bg-gradient-to-r from-blue-600 to-purple-600 bg-clip-text text-transparent">
                                +<span x-text="xpAnimation.currentPoints"></span> XP
                            </div>
                        </div>
                        <!-- Animated Confetti Background -->
                        <div class="absolute inset-0 overflow-hidden opacity-20 pointer-events-none">
                            <template x-for="i in 20" :key="i">
                                <div class="absolute w-2 h-2 rounded-full"
                                    :class="['bg-blue-500', 'bg-green-500', 'bg-yellow-500', 'bg-purple-500', 'bg-pink-500'][
                                        Math.floor(
                                            Math.random() * 5)
                                    ]"
                                    :style="`
                                                                                                                             left: ${Math.random() * 100}%;
                                                                                                                             top: ${Math.random() * 100}%;
                                                                                                                             animation: confetti ${1 + Math.random() * 3}s linear infinite;
                                                                                                                             transform: scale(${0.5 + Math.random()});
                                                                                                                             opacity: ${0.2 + Math.random() * 0.8};
                                                                                                                             animation-delay: ${Math.random() * 2}s;
                                                                                                                         `">
                                </div>
                            </template>
                        </div>
                    </div>

                    <!-- Stats Display -->
                    <div class="space-y-3 mb-6">
                        <div class="bg-green-50 dark:bg-green-900/20 p-3 rounded-lg">
                            <p class="text-green-800 dark:text-green-200 font-medium">
                                {{ __('Score Earned') }}: <span x-text="currentScore"></span>%
                            </p>
                        </div>
                        <div x-show="updatedStats" class="bg-blue-50 dark:bg-blue-900/20 p-3 rounded-lg">
                            <p class="text-blue-800 dark:text-blue-200">
                                {{ __('Average Score') }}: <span x-text="updatedStats.averageScore"></span>%
                            </p>
                        </div>
                        <div x-show="updatedStats && updatedStats.quizComparison"
                            class="bg-purple-50 dark:bg-purple-900/20 p-3 rounded-lg">
                            <p class="text-purple-800 dark:text-purple-200">
                                {{ __('Improvement') }}:
                                <span x-show="updatedStats.quizComparison.improvement > 0" class="text-green-600">
                                    +<span x-text="updatedStats.quizComparison.improvement"></span>%
                                </span>
                                <span x-show="updatedStats.quizComparison.improvement <= 0" class="text-red-600">
                                    <span x-text="updatedStats.quizComparison.improvement"></span>%
                                </span>
                            </p>
                        </div>
                    </div>

                    <!-- Action Links for Users with Plan -->
                    <div x-show="updatedStats && updatedStats.hasPlan" class="space-y-2">
                        <a href="#" @click="showResultsModal = false"
                            class="block w-full text-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                            {{ __('Practice Again') }}
                        </a>
                        <a href="{{ route('dashboard.quizzes.index') }}"
                            class="block w-full text-center px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700">
                            {{ __('More Quizzes') }}
                        </a>
                        <a href="{{ route('plans') }}"
                            class="block w-full text-center px-4 py-2 bg-yellow-600 text-white rounded-lg hover:bg-yellow-700">
                            {{ __('Upgrade Subscription') }}
                        </a>
                        <a href="{{ route('dashboard.quizzes.progress') }}"
                            class="block w-full text-center px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">
                            {{ __('Check Progress') }}
                        </a>
                    </div>

                    <!-- Upsell for Users without Plan -->
                    <div x-show="updatedStats && !updatedStats.hasPlan" class="space-y-3">
                        <div
                            class="bg-yellow-50 dark:bg-yellow-900/20 p-4 rounded-lg border border-yellow-200 dark:border-yellow-800">
                            <h4 class="text-yellow-800 dark:text-yellow-200 font-semibold mb-2">
                                {{ __('Unlock More Features!') }}
                            </h4>
                            <p class="text-yellow-700 dark:text-yellow-300 text-sm mb-3">
                                {{ __('Get a subscription to practice unlimited quizzes and track your progress.') }}
                            </p>
                            <a href="{{ route('plans') }}"
                                class="block w-full text-center px-4 py-2 bg-yellow-600 text-white rounded-lg hover:bg-yellow-700">
                                {{ __('Get Plan to Practice More') }}
                            </a>
                        </div>
                    </div>

                    <!-- Close Button -->
                    <div class="mt-6 flex justify-end">
                        <button @click="showResultsModal = false"
                            class="px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-md text-sm font-medium text-gray-700 dark:text-gray-200 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
                            {{ __('Close') }}
                        </button>
                    </div>
                </div>
            </div>
        </div> <!-- Close quiz content wrapper -->
    </div> <!-- Close main component -->

    <style>
        @keyframes confetti {
            0% {
                transform: translateY(-100vh) rotate(0deg) scale(var(--scale, 1));
                opacity: 1;
            }

            100% {
                transform: translateY(100vh) rotate(360deg) scale(var(--scale, 1));
                opacity: 0;
            }
        }

        .xp-badge {
            position: relative;
            overflow: hidden;
        }

        .xp-badge::after {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(255, 255, 255, 0.8) 0%, rgba(255, 255, 255, 0) 70%);
            transform: rotate(30deg);
            animation: shine 2s infinite;
            opacity: 0;
        }

        @keyframes shine {
            0% {
                transform: translateX(-100%) rotate(30deg);
                opacity: 0;
            }

            20% {
                opacity: 0.8;
            }

            50% {
                opacity: 0.8;
            }

            100% {
                transform: translateX(100%) rotate(30deg);
                opacity: 0;
            }
        }

        .xp-pulse {
            animation: xpPulse 1s infinite alternate;
        }

        @keyframes xpPulse {
            from {
                transform: scale(1);
                box-shadow: 0 0 0 0 rgba(59, 130, 246, 0.4);
            }

            to {
                transform: scale(1.05);
                box-shadow: 0 0 0 10px rgba(59, 130, 246, 0);
            }
        }
    </style>

    @push('scripts')
        <script>
            document.addEventListener('alpine:init', () => {
                Alpine.data('quizTaker', (config) => ({
                    // Quiz state
                    quizId: config.quizId,
                    totalQuestions: config.totalQuestions,
                    timeLimit: config.timeLimit * 60, // Convert to seconds
                    timeLeft: config.timeLimit * 60, // Will be updated in init()
                    currentQuestionIndex: 0,
                    selectedOption: null,
                    isAnswerSubmitted: false,
                    isAnswerCorrect: false,
                    showFeedback: false,
                    feedbackMessage: '',
                    isPaused: false,
                    isGuest: config.isGuest,
                    initialized: false, // Loading state
                    showLoginModal: false,
                    showResultsModal: false,
                    updatedStats: null,
                    currentScore: 0,
                    flaggedQuestions: new Set(),
                    timer: null,
                    startTime: null,
                    endTime: null,
                    autoAdvance: false,
                    hasPlan: false, // Will be updated in init()
                    xpAnimation: {
                        show: false,
                        points: 0,
                        currentPoints: 0
                    },
                    questions: config.questions.map((q, index) => ({
                        id: q.id || `q-${index}`,
                        text: q.text || q.question_text || `Question ${index + 1}`,
                        image: q.image_path ? `/storage/${q.image_path}` : null,
                        options: Array.isArray(q.options) ? q.options.map((opt, optIndex) => ({
                            id: opt.id || `opt-${index}-${optIndex}`,
                            text: opt.text || opt.option_text ||
                                `Option ${optIndex + 1}`,
                            is_correct: opt.is_correct || opt.correct || false,
                            explanation: opt.explanation || ''
                        })) : []
                    })),
                    userAnswers: {},
                    correctCount: {{ $attempt && $attempt->answers ? collect($attempt->answers)->filter(fn($a) => $a['is_correct'])->count() : 0 }},
                    incorrectCount: {{ $attempt && $attempt->answers ? collect($attempt->answers)->filter(fn($a) => !$a['is_correct'])->count() : 0 }},

                    // Computed properties
                    get currentQuestion() {
                        return this.questions[this.currentQuestionIndex];
                    },
                    get isLastQuestion() {
                        return this.currentQuestionIndex === this.questions.length - 1;
                    },
                    get isFirstQuestion() {
                        return this.currentQuestionIndex === 0;
                    },
                    get isQuestionFlagged() {
                        return this.flaggedQuestions.has(this.currentQuestion.id);
                    },
                    get progressPercentage() {
                        return ((this.currentQuestionIndex + 1) / this.totalQuestions) * 100;
                    },

                    // Initialize the quiz
                    init() {
                        // Restore timer state first
                        this.validateAndRestoreTimer();

                        // Restore pause state
                        this.restorePauseState();

                        // Fetch user plan info if not guest
                        if (!this.isGuest) {
                            this.fetchUserPlan();
                        }

                        this.startTime = new Date();

                        // Don't start timer automatically - wait for first answer

                        this.loadProgress();

                        // Mark as initialized to hide loading state
                        this.initialized = true;

                        // Load saved answers if any (only if user is logged in)
                        if (!this.isGuest && localStorage.getItem(`quiz_${this.quizId}_answers`)) {
                            this.userAnswers = JSON.parse(localStorage.getItem(
                                `quiz_${this.quizId}_answers`));

                            // Update counts
                            this.correctCount = Object.values(this.userAnswers).filter(a => a.isCorrect)
                                .length;
                            this.incorrectCount = Object.values(this.userAnswers).filter(a => !a.isCorrect)
                                .length;

                            // If we have a saved question index, go to it
                            const savedIndex = localStorage.getItem(`quiz_${this.quizId}_current_index`);
                            if (savedIndex !== null) {
                                this.currentQuestionIndex = parseInt(savedIndex);
                                this.loadQuestionState();
                            }
                        }

                        // Load flagged questions (only if user is logged in)
                        if (!this.isGuest && localStorage.getItem(`quiz_${this.quizId}_flagged`)) {
                            const flagged = JSON.parse(localStorage.getItem(`quiz_${this.quizId}_flagged`));
                            this.flaggedQuestions = new Set(flagged);
                        }
                    },

                    // Get saved time left or return default
                    getSavedTimeLeft(defaultTime) {
                        const saved = localStorage.getItem(`quiz_${this.quizId}_time_left`);
                        const savedTimestamp = localStorage.getItem(`quiz_${this.quizId}_time_timestamp`);

                        if (saved && savedTimestamp) {
                            const savedTime = parseInt(saved);
                            const timestamp = parseInt(savedTimestamp);
                            const now = Date.now();
                            const elapsedSeconds = Math.floor((now - timestamp) / 1000);

                            // Account for time elapsed since page was closed
                            const adjustedTime = savedTime - elapsedSeconds;

                            // Return the adjusted time, but don't go below 0 or above the limit
                            return Math.max(0, Math.min(adjustedTime, this.timeLimit));
                        }

                        return defaultTime;
                    },

                    // Validate and restore timer state
                    validateAndRestoreTimer() {
                        const savedTime = localStorage.getItem(`quiz_${this.quizId}_time_left`);
                        const savedTimestamp = localStorage.getItem(`quiz_${this.quizId}_time_timestamp`);

                        if (savedTime && savedTimestamp) {
                            const savedTimeInt = parseInt(savedTime);
                            const timestamp = parseInt(savedTimestamp);
                            const now = Date.now();
                            const elapsedSeconds = Math.floor((now - timestamp) / 1000);

                            // If less than 5 minutes have passed, restore the adjusted time
                            if (elapsedSeconds < 300) { // 5 minutes threshold
                                const adjustedTime = savedTimeInt - elapsedSeconds;
                                this.timeLeft = Math.max(0, Math.min(adjustedTime, this.timeLimit));
                            } else {
                                // Too much time passed, reset to full time
                                this.clearTimerState();
                                this.timeLeft = this.timeLimit;
                            }
                        }
                    },

                    // Clear timer state
                    clearTimerState() {
                        localStorage.removeItem(`quiz_${this.quizId}_time_left`);
                        localStorage.removeItem(`quiz_${this.quizId}_time_timestamp`);
                    },

                    // Timer functions
                    startTimer() {
                        // Clear any existing timer
                        if (this.timer) {
                            clearInterval(this.timer);
                        }

                        this.timer = setInterval(() => {
                            // Only decrement time if not paused
                            if (!this.isPaused) {
                                this.timeLeft--;

                                // Save time left and timestamp every second (only when not paused)
                                localStorage.setItem(`quiz_${this.quizId}_time_left`, this
                                    .timeLeft);
                                localStorage.setItem(`quiz_${this.quizId}_time_timestamp`, Date
                                    .now());

                                if (this.timeLeft <= 0) {
                                    this.timeLeft = 0;
                                    this.finishQuiz();
                                }
                            }
                        }, 1000);
                    },

                    togglePause() {
                        this.isPaused = !this.isPaused;

                        if (this.isPaused) {
                            // Stop the timer completely when paused
                            clearInterval(this.timer);
                            this.timer = null;

                            // Save complete current state to localStorage
                            this.saveCurrentState();

                            // Save pause state
                            localStorage.setItem(`quiz_${this.quizId}_is_paused`, 'true');
                            localStorage.setItem(`quiz_${this.quizId}_pause_timestamp`, Date.now());

                            this.showPauseModal();
                        } else {
                            // Resume: restart the timer immediately
                            this.startTimer();

                            // Clear pause state
                            localStorage.removeItem(`quiz_${this.quizId}_is_paused`);
                            localStorage.removeItem(`quiz_${this.quizId}_pause_timestamp`);

                            // Save current time and timestamp
                            localStorage.setItem(`quiz_${this.quizId}_time_left`, this.timeLeft);
                            localStorage.setItem(`quiz_${this.quizId}_time_timestamp`, Date.now());
                        }
                    },

                    // Save complete current state (only if user is logged in)
                    saveCurrentState() {
                        if (this.isGuest) return;

                        // Save timer state
                        localStorage.setItem(`quiz_${this.quizId}_time_left`, this.timeLeft);
                        localStorage.setItem(`quiz_${this.quizId}_time_timestamp`, Date.now());

                        // Save question state
                        localStorage.setItem(`quiz_${this.quizId}_current_index`, this
                            .currentQuestionIndex);

                        // Save answers
                        localStorage.setItem(`quiz_${this.quizId}_answers`, JSON.stringify(this
                            .userAnswers));

                        // Save counts
                        localStorage.setItem(`quiz_${this.quizId}_correct_count`, this.correctCount);
                        localStorage.setItem(`quiz_${this.quizId}_incorrect_count`, this.incorrectCount);

                        // Save flagged questions
                        localStorage.setItem(`quiz_${this.quizId}_flagged`, JSON.stringify(Array.from(this
                            .flaggedQuestions)));

                        // Save other state
                        localStorage.setItem(`quiz_${this.quizId}_auto_advance`, this.autoAdvance);
                    },

                    // Restore pause state
                    restorePauseState() {
                        const wasPaused = localStorage.getItem(`quiz_${this.quizId}_is_paused`);
                        const pauseTimestamp = localStorage.getItem(`quiz_${this.quizId}_pause_timestamp`);

                        if (wasPaused === 'true' && pauseTimestamp) {
                            const timestamp = parseInt(pauseTimestamp);
                            const now = Date.now();
                            const elapsedMinutes = (now - timestamp) / (1000 * 60);

                            // If paused for more than 30 minutes, auto-resume
                            if (elapsedMinutes > 30) {
                                this.isPaused = false;
                                localStorage.removeItem(`quiz_${this.quizId}_is_paused`);
                                localStorage.removeItem(`quiz_${this.quizId}_pause_timestamp`);
                            } else {
                                // Keep paused state - don't start timer
                                this.isPaused = true;
                            }
                        } else {
                            // Not paused, ensure timer will start
                            this.isPaused = false;
                        }
                    },

                    // Fetch user plan information
                    async fetchUserPlan() {
                        try {
                            const response = await fetch('/api/subscriptions/active', {
                                method: 'GET',
                                credentials: 'same-origin',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': document.querySelector(
                                        'meta[name="csrf-token"]').content
                                }
                            });

                            if (response.ok) {
                                const contentType = response.headers.get('content-type');
                                if (contentType && contentType.includes('application/json')) {
                                    const data = await response.json();
                                    this.hasPlan = data.length >
                                    0; // Has active subscription if array is not empty
                                } else {
                                    console.warn('Subscriptions API returned non-JSON response');
                                    this.hasPlan = false;
                                }
                            } else {
                                console.warn('Subscriptions API response not ok:', response.status);
                                this.hasPlan = false;
                            }
                        } catch (error) {
                            console.error('Error fetching user plan:', error);
                            this.hasPlan = false;
                        }
                    },

                    // Reset quiz to start
                    resetQuiz() {
                        if (!confirm(
                                'Are you sure you want to reset the quiz? All progress will be lost.')) {
                            return;
                        }

                        // Clear all progress
                        this.clearProgress();
                        this.clearTimerState();

                        // Reset state
                        this.currentQuestionIndex = 0;
                        this.selectedOption = null;
                        this.isAnswerSubmitted = false;
                        this.isAnswerCorrect = false;
                        this.showFeedback = false;
                        this.feedbackMessage = '';
                        this.isPaused = false;
                        this.correctCount = 0;
                        this.incorrectCount = 0;
                        this.userAnswers = {};
                        this.flaggedQuestions.clear();
                        this.timeLeft = this.timeLimit;
                        this.startTime = new Date();

                        // Clear pause state
                        localStorage.removeItem(`quiz_${this.quizId}_is_paused`);
                        localStorage.removeItem(`quiz_${this.quizId}_pause_timestamp`);

                        // Load first question
                        this.loadQuestionState();

                        // Restart timer
                        clearInterval(this.timer);
                        this.startTimer();
                    },

                    // Question navigation
                    nextQuestion() {
                        // If user is a guest, show login modal instead of proceeding
                        if (this.isGuest) {
                            this.showLoginModal = true;
                            return;
                        }

                        if (this.isLastQuestion) {
                            this.finishQuiz();
                            return;
                        }

                        // For authenticated users, proceed normally
                        if (!this.isAnswerSubmitted) {
                            // Allow navigation without submitting answer
                            // but don't save the answer
                        }

                        this.saveQuestionState();
                        this.currentQuestionIndex++;
                        this.loadQuestionState();
                    },

                    previousQuestion() {
                        if (this.currentQuestionIndex > 0) {
                            this.saveQuestionState();
                            this.currentQuestionIndex--;
                            this.loadQuestionState();
                        }
                    },

                    // Handle option selection
                    handleOptionSelect(option) {
                        if (this.isAnswerSubmitted) return;

                        // Start timer on first answer if not already started and user is logged in
                        if (!this.timer && !this.isGuest) {
                            this.startTimer();
                        }

                        // Auto-unpause if quiz is paused
                        if (this.isPaused) {
                            this.isPaused = false;
                            this.startTimer();
                            localStorage.removeItem(`quiz_${this.quizId}_is_paused`);
                            localStorage.removeItem(`quiz_${this.quizId}_pause_timestamp`);
                            localStorage.setItem(`quiz_${this.quizId}_time_left`, this.timeLeft);
                            localStorage.setItem(`quiz_${this.quizId}_time_timestamp`, Date.now());
                        }

                        this.selectedOption = option.id;
                        this.isAnswerCorrect = option.is_correct;
                        this.isAnswerSubmitted = true;
                        this.showFeedback = true;

                        // Set feedback message
                        this.feedbackMessage = option.is_correct ?
                            'Correct! Well done! 🎉' :
                            'Incorrect. The correct answer has been highlighted.';

                        // Update counts
                        if (option.is_correct) {
                            this.correctCount++;
                        } else {
                            this.incorrectCount++;
                        }

                        // Save answer
                        this.userAnswers[this.currentQuestion.id] = {
                            optionId: option.id,
                            isCorrect: option.is_correct,
                            timestamp: new Date().toISOString()
                        };

                        // Save to localStorage (only if user is logged in)
                        if (!this.isGuest) {
                            localStorage.setItem(
                                `quiz_${this.quizId}_answers`,
                                JSON.stringify(this.userAnswers)
                            );
                        }

                        // Auto-advance if enabled
                        if (this.autoAdvance) {
                            setTimeout(() => {
                                if (!this.isLastQuestion) {
                                    this.nextQuestion();
                                } else {
                                    // Scroll to next button if it's the last question
                                    this.$nextTick(() => {
                                        const nextButton = this.$el.querySelector(
                                            'button:has(span:contains("Finish"))');
                                        if (nextButton) {
                                            nextButton.scrollIntoView({
                                                behavior: 'smooth',
                                                block: 'center'
                                            });
                                        }
                                    });
                                }
                            }, 1500);
                        }
                    },

                    // UI helpers
                    formatTime(seconds) {
                        const mins = Math.floor(seconds / 60);
                        const secs = seconds % 60;
                        return `${mins}:${secs < 10 ? '0' : ''}${secs}`;
                    },

                    getProgressBarColor() {
                        const totalAnswered = this.correctCount + this.incorrectCount;
                        if (totalAnswered === 0) return '#10b981'; // Default green when no answers

                        const correctPercentage = (this.correctCount / totalAnswered) * 100;

                        if (correctPercentage >= 80) return '#10b981'; // Green
                        if (correctPercentage >= 60) return '#84cc16'; // Light green
                        if (correctPercentage >= 40) return '#eab308'; // Yellow
                        if (correctPercentage >= 20) return '#f97316'; // Orange
                        return '#ef4444'; // Red
                    },

                    
                    // Load question state
                    loadQuestionState() {
                        const savedAnswer = this.userAnswers[this.currentQuestion.id];
                        if (savedAnswer) {
                            this.selectedOption = savedAnswer.optionId;
                            this.isAnswerSubmitted = true;
                            this.isAnswerCorrect = savedAnswer.isCorrect;
                            this.showFeedback = true;
                            this.feedbackMessage = savedAnswer.isCorrect ?
                                'Correct! Well done! 🎉' :
                                'Incorrect. The correct answer has been highlighted.';
                        } else {
                            this.selectedOption = null;
                            this.isAnswerSubmitted = false;
                            this.isAnswerCorrect = false;
                            this.showFeedback = false;
                            this.feedbackMessage = '';
                        }

                        // Scroll to top of question
                        this.$nextTick(() => {
                            const questionElement = this.$el.querySelector(
                                '.question-container.active');
                            if (questionElement) {
                                questionElement.scrollIntoView({
                                    behavior: 'smooth',
                                    block: 'start'
                                });
                            }
                        });

                        // Save current question index (only if user is logged in)
                        if (!this.isGuest) {
                            localStorage.setItem(
                                `quiz_${this.quizId}_current_index`,
                                this.currentQuestionIndex
                            );
                        }
                    },

                    saveQuestionState() {
                        // Already handled in submitAnswer
                    },

                    // Quiz completion
                    finishQuiz() {
                        clearInterval(this.timer);
                        this.endTime = new Date();

                        // Calculate score
                        const score = Math.round(
                            (this.correctCount / this.totalQuestions) * 100
                        );

                        // Save results
                        this.saveResults(score);

                        // Start 3-second timeout for stats update and modal (only if not guest)
                        setTimeout(() => {
                            if (!this.isGuest) {
                                this.fetchUpdatedStats();
                            } else {
                                // For guests, show modal immediately
                                this.showResultsModal = true;
                                this.clearProgress();
                            }
                        }, 3000);
                    },

                    // Data persistence
                    saveResults(score) {
                        if (this.isGuest) return;

                        // Submit quiz results to backend
                        fetch('/api/quiz/submit', {
                                method: 'POST',
                                credentials: 'same-origin',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')
                                        .content
                                },
                                body: JSON.stringify({
                                    quiz_id: this.quizId,
                                    score: score,
                                    correct_answers: this.correctCount,
                                    total_questions: this.totalQuestions,
                                    time_spent: (this.timeLimit * 60) - this.timeLeft,
                                    answers: this.userAnswers
                                })
                            })
                            .then(response => {
                                const contentType = response.headers.get('content-type');
                                if (contentType && contentType.includes('application/json')) {
                                    return response.json();
                                } else {
                                    console.warn('Quiz submit API returned non-JSON response');
                                    return {
                                        success: false,
                                        message: 'Invalid response format'
                                    };
                                }
                            })
                            .then(data => {
                                console.log('Results saved:', data);
                                // Store current score for modal display
                                this.currentScore = score;
                            })
                            .catch(error => {
                                console.error('Error saving results:', error);
                            });
                    },

                    loadProgress() {
                        // Load from localStorage if needed
                    },

                    // Animate XP points
                    animateXpPoints(points) {
                        this.xpAnimation.points = points;
                        this.xpAnimation.currentPoints = 0;
                        this.xpAnimation.show = true;

                        const increment = 5;
                        const steps = Math.ceil(points / increment);
                        const stepDelay = 200; // 200ms between increments

                        let currentStep = 0;
                        const interval = setInterval(() => {
                            currentStep++;
                            this.xpAnimation.currentPoints = Math.min(currentStep * increment,
                                points);

                            if (currentStep >= steps) {
                                clearInterval(interval);
                                setTimeout(() => {
                                    this.xpAnimation.show = false;
                                }, 2000);
                            }
                        }, stepDelay);
                    },

                    clearProgress() {
                        // Clear saved state (only if user is logged in)
                        if (!this.isGuest) {
                            localStorage.removeItem(`quiz_${this.quizId}_answers`);
                            localStorage.removeItem(`quiz_${this.quizId}_current_index`);
                            localStorage.removeItem(`quiz_${this.quizId}_time_left`);
                            localStorage.removeItem(`quiz_${this.quizId}_time_timestamp`);
                            localStorage.removeItem(`quiz_${this.quizId}_flagged`);
                        }
                    },

                    // Additional features
                    flagQuestion() {
                        if (this.flaggedQuestions.has(this.currentQuestion.id)) {
                            this.flaggedQuestions.delete(this.currentQuestion.id);
                        } else {
                            this.flaggedQuestions.add(this.currentQuestion.id);
                        }

                        // Save flagged questions (only if user is logged in)
                        if (!this.isGuest) {
                            localStorage.setItem(
                                `quiz_${this.quizId}_flagged`,
                                JSON.stringify(Array.from(this.flaggedQuestions))
                            );
                        }
                    },

                    showPauseModal() {
                        // Pause state is now indicated by the button UI change
                        // No blocking modal needed
                    },

                    // Fetch updated stats after quiz completion
                    async fetchUpdatedStats() {
                        try {
                            const response = await fetch('/api/user/stats', {
                                method: 'GET',
                                credentials: 'same-origin',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': document.querySelector(
                                        'meta[name="csrf-token"]').content
                                }
                            });

                            if (response.ok) {
                                const contentType = response.headers.get('content-type');
                                if (contentType && contentType.includes('application/json')) {
                                    this.updatedStats = await response.json();
                                    this.updateUnifiedNavBar();

                                    // Trigger XP animation if user gained points
                                    if (this.updatedStats.xp && this.updatedStats.xpGained > 0) {
                                        this.animateXpPoints(this.updatedStats.xpGained);
                                    }
                                } else {
                                    console.warn('API returned non-JSON response, using fallback');
                                }

                                this.showResultsModal = true;
                                this.clearProgress();
                            } else {
                                console.warn('API response not ok:', response.status);
                                this.showResultsModal = true;
                                this.clearProgress();
                            }
                        } catch (error) {
                            console.error('Error fetching updated stats:', error);
                            // Fallback: show modal with basic data
                            this.showResultsModal = true;
                            this.clearProgress();
                        }
                    },

                    // Update unified navigation bar with new stats
                    updateUnifiedNavBar() {
                        if (!this.updatedStats) return;

                        // Dispatch custom event for unified-nav-bar to listen to
                        window.dispatchEvent(new CustomEvent('statsUpdated', {
                            detail: {
                                xp: this.updatedStats.xp,
                                averageScore: this.updatedStats.averageScore,
                                leaderboardPosition: this.updatedStats.leaderboardPosition,
                                streak: this.updatedStats.streak
                            }
                        }));
                    }
                }));
            });
        </script>
    @endpush
