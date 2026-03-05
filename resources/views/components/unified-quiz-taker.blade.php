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
})" x-init="init()" x-destroy="destroy()"
    class="flex flex-col h-full bg-gradient-to-br from-white to-gray-50 dark:from-gray-900 dark:to-gray-800 rounded-2xl shadow-lg border border-gray-100 dark:border-gray-700">

    <!-- Loading Skeleton (shown before Alpine initializes) -->
    <div x-show="!initialized"
        class="flex flex-col h-full bg-gradient-to-br from-white to-gray-50 dark:from-gray-900 dark:to-gray-800">
        @if ($showHeader)
            <div
                class="bg-gradient-to-r from-gray-50 to-gray-100 dark:from-gray-800 dark:to-gray-700 border-b border-gray-200/50 dark:border-gray-600/50 px-3 sm:px-4 py-3 md:rounded-t-2xl">
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
                <div class="w-full dark:bg-gray-600/50 bg-gray-200/50 rounded-full h-2 mt-3 shadow-inner">
                    <div
                        class="h-2 rounded-full bg-gradient-to-r from-blue-400 to-blue-600 dark:from-blue-500 dark:to-blue-400 animate-pulse shadow-sm">
                    </div>
                </div>
            </div>

            <!-- Second header line skeleton -->
            <div
                class="bg-gradient-to-r from-gray-100 to-gray-150 dark:from-gray-750 dark:to-gray-700 border-b border-gray-200/50 dark:border-gray-600/50 px-3 sm:px-4 py-2">
                <div class="flex items-center justify-between">
                    <div class="h-4 w-16 bg-gray-300/60 dark:bg-gray-600/40 rounded animate-pulse"></div>
                    <div class="flex gap-1">
                        <div
                            class="h-8 w-8 bg-gradient-to-br from-gray-200 to-gray-300 dark:from-gray-600 dark:to-gray-700 rounded-xl animate-pulse shadow-sm">
                        </div>
                        <div
                            class="h-8 w-8 bg-gradient-to-br from-gray-200 to-gray-300 dark:from-gray-600 dark:to-gray-700 rounded-xl animate-pulse shadow-sm">
                        </div>
                        <div
                            class="h-8 w-8 bg-gradient-to-br from-gray-200 to-gray-300 dark:from-gray-600 dark:to-gray-700 rounded-xl animate-pulse shadow-sm">
                        </div>
                        <div
                            class="h-8 w-8 bg-gradient-to-br from-gray-200 to-gray-300 dark:from-gray-600 dark:to-gray-700 rounded-xl animate-pulse shadow-sm">
                        </div>
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
                        <div
                            class="p-4 border border-gray-200/60 dark:border-gray-600/60 rounded-xl hover:shadow-sm transition-shadow duration-200">
                            <div class="flex items-center">
                                <div
                                    class="w-5 h-5 rounded-xl border-2 border-gray-300/60 dark:border-gray-500/60 mr-3">
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
        <div
            class="border-t border-gray-200/60 dark:border-gray-700/60 bg-gradient-to-r from-white to-gray-50 dark:from-gray-800 dark:to-gray-750 p-3 sm:p-4 md:rounded-b-2xl">
            <div class="flex justify-between items-center gap-2">
                <div
                    class="h-10 w-20 bg-gradient-to-r from-gray-200 to-gray-300 dark:from-gray-600 dark:to-gray-700 rounded-xl animate-pulse">
                </div>
                <div
                    class="h-10 w-32 bg-gradient-to-r from-blue-400 to-blue-500 dark:from-blue-500 dark:to-blue-600 rounded-xl animate-pulse">
                </div>
            </div>
        </div>
    </div>

    <!-- Quiz Content (shown when initialized) -->
    <div x-cloak class="flex flex-col lg:h-full min-h-0">
        <div x-show="initialized" x-transition class="flex flex-col lg:h-full min-h-0">

            @if ($showHeader)
                <!-- Header Line 1: Quiz Info -->
                <div x-cloak
                    class="bg-gradient-to-r from-gray-50 to-gray-100 dark:from-gray-800 dark:to-gray-700 border-b border-gray-200/50 dark:border-gray-600/50 px-3 sm:px-4 py-3 relative md:rounded-t-2xl">
                    <!-- Desktop Live Activity Notification -->
                    <div x-show="liveNotification" x-transition:enter="transition ease-out duration-300"
                        x-transition:enter-start="opacity-0 transform -translate-y-2"
                        x-transition:enter-end="opacity-100 transform translate-y-0"
                        x-transition:leave="transition ease-in duration-200"
                        x-transition:leave-start="opacity-100 transform translate-y-0"
                        x-transition:leave-end="opacity-0 transform -translate-y-2"
                        class="hidden lg:block absolute top-full left-0 right-0 text-center py-2 text-sm z-10 rounded-b-xl border-t"
                        :class="{
                            'bg-gradient-to-r from-green-500 to-green-600 text-white border-green-400/30': (liveNotification && liveNotification.type !== 'robot_companion'),
                            'bg-gradient-to-r from-purple-500 to-purple-600 text-white border-purple-400/30': (liveNotification && liveNotification.type === 'robot_companion')
                        }">
                        <div class="flex items-center justify-center gap-2">
                            <span x-show="liveNotification && liveNotification.type === 'robot_companion'" class="text-lg">🤖</span>
                            <span x-show="liveNotification && liveNotification.robot_name" class="font-semibold" x-text="(liveNotification && liveNotification.robot_name ? liveNotification.robot_name + ':' : '')"></span>
                            <span x-text="liveNotification && liveNotification.message ? liveNotification.message : ''"></span>
                            <span x-show="liveNotification && liveNotification.is_correct !== undefined" 
                                  class="ml-2" 
                                  :class="(liveNotification && liveNotification.is_correct) ? 'text-green-200' : 'text-yellow-200'"
                                  x-text="(liveNotification && liveNotification.is_correct) ? '✓' : '✗'"></span>
                        </div>
                    </div>

                    
                    <div class="flex items-center justify-between text-sm">
                        <div class="flex items-center gap-3 sm:gap-4">
                            <h1
                                class="hidden lg:block font-semibold truncate max-w-[120px] sm:max-w-[180px] md:max-w-[250px] text-gray-800 dark:text-gray-100 text-sm sm:text-base">
                                {{ $quizTitle }}</h1>
                            <div class="flex items-center gap-1.5 text-gray-600 dark:text-gray-300">
                                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <span x-text="formatTime(timeLeft)" class="font-medium text-sm sm:text-base"></span>
                            </div>
                            <!-- Replace 20Q with correct/incorrect count -->
                            <div class="flex items-center gap-1.5 text-gray-600 dark:text-gray-300">
                                <span class="font-medium text-sm sm:text-base">
                                    <span x-text="correctCount" class="text-green-600 dark:text-green-400 font-bold"></span>
                                    <span class="mx-1">/</span>
                                    <span x-text="incorrectCount" class="text-red-600 dark:text-red-400 font-bold"></span>
                                </span>
                                <span class="text-xs text-gray-500 dark:text-gray-400 hidden sm:inline">answers</span>
                            </div>
                        </div>
                        <!-- Move action buttons here -->
                        <div class="flex items-center gap-1">
                            <button @click="toggleSound"
                                class="p-2 sm:p-2.5 rounded-xl hover:bg-gray-200/80 dark:hover:bg-gray-700/80 transition-all duration-200"
                                :class="soundEnabled ?
                                    'text-blue-500 bg-gradient-to-r from-blue-50 to-blue-100 dark:from-blue-900/30 dark:to-blue-800/20' :
                                    'text-gray-400 dark:text-gray-600'"
                                :title="soundEnabled ? 'Disable sound' : 'Enable sound'">
                                <i x-show="soundEnabled" class="fas fa-volume-up"></i>
                                <i x-show="!soundEnabled" class="fas fa-volume-mute"></i>
                            </button>
                            <button @click="togglePause" :disabled="isGuest"
                                class="p-2 sm:p-2.5 rounded-xl hover:bg-gray-200/80 dark:hover:bg-gray-700/80 text-gray-600 dark:text-gray-300 disabled:opacity-50 disabled:cursor-not-allowed transition-all duration-200"
                                :title="isGuest ? __('quiz.loginToUsePause') : (isPaused ? __('quiz.resume') : __('quiz.pause'))">
                                <i x-show="!isPaused" class="fas fa-pause"></i>
                                <i x-show="isPaused" class="fas fa-play"></i>
                            </button>
                            <button @click="resetQuiz" x-show="hasPlan && !isGuest"
                                class="p-2 sm:p-2.5 rounded-xl hover:bg-red-50 dark:hover:bg-red-900/20 text-red-600 dark:text-red-400 transition-all duration-200"
                                title="{{ __('quiz.resetQuiz') }}">
                                <i class="fas fa-sync-alt"></i>
                            </button>
                            <button @click="autoAdvance = !autoAdvance" :disabled="isGuest"
                                class="p-2 sm:p-2.5 rounded-xl hover:bg-gray-200/80 dark:hover:bg-gray-700/80 disabled:opacity-50 disabled:cursor-not-allowed transition-all duration-200"
                                :class="autoAdvance ?
                                    'text-blue-500 bg-gradient-to-r from-blue-50 to-blue-100 dark:from-blue-900/30 dark:to-blue-800/20' :
                                    'text-gray-600 dark:text-gray-300'"
                                :title="isGuest ? __('quiz.loginToUseAutoAdvance') : __('quiz.toggleAutoNext')">
                                <i class="fas fa-forward"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Progress Bar -->
                    <div class="w-full dark:bg-gray-600/50 bg-gray-200/50 rounded-full h-2 mt-3">
                        <div class="h-2 rounded-full transition-all duration-500 ease-out"
                            :style="{
                                'width': Math.max(0, Math.min(100, (answeredCount / totalQuestions) * 100)) + '%',
                                'background': 'linear-gradient(to right, ' + getProgressBarColor() + ', ' +
                                    getProgressBarColor() + ')'
                            }">
                        </div>
                    </div>
                </div>
            @endif

            <!-- Quiz Content -->
            <div x-cloak
                class="flex-1 overflow-y-auto p-4 min-h-0 bg-gradient-to-br from-white/50 to-gray-50/30 dark:from-gray-900/50 dark:to-gray-800/30">
                <!-- Guest Notice -->
                <div x-show="isGuest"
                    class="mb-4 p-4 bg-gradient-to-r from-yellow-50 to-orange-50 dark:from-yellow-900/20 dark:to-orange-900/20 border-l-4 border-yellow-400 rounded-xl">
                    <p class="text-yellow-800 dark:text-yellow-200">
                        {{ __('quiz.loginToSaveProgress') }}
                    </p>
                </div>

                <!-- Current Question -->
                <div class="mb-6">
                    <h2 id="quiz-question-title"
                        class="text-xl font-semibold mb-4 text-gray-800 dark:text-gray-100 leading-relaxed"
                        x-text="currentQuestion.text"></h2>

                    <!-- Question Image -->
                    <div x-show="currentQuestion.image_url" class="mb-4">
                        <div :style="`background-image: url('${currentQuestion.image_url || '/images/road-sign-placeholder.svg'}'); background-size: contain; background-position: center; background-repeat: no-repeat; min-height: 200px;`"
                            class="w-full max-w-full rounded-2xl border border-gray-200/60 dark:border-gray-600/60 hover:shadow-sm transition-all duration-300"
                            :alt="currentQuestion.text || __('quiz.questionImage')">
                        </div>
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
                                    class="answer-option flex items-center p-4 border rounded-xl cursor-pointer transition-all duration-200 hover:-translate-y-0.5"
                                    :class="{
                                        'border-blue-500 bg-gradient-to-r from-blue-50 to-blue-100 dark:from-blue-900/20 dark:to-blue-800/20': selectedOption ===
                                            option
                                            .id && !
                                            isAnswerSubmitted,
                                        'border-green-500 bg-gradient-to-r from-green-50 to-green-100 dark:from-green-900/20 dark:to-green-800/20': isAnswerSubmitted &&
                                            option
                                            .is_correct,
                                        'border-red-500 bg-gradient-to-r from-red-50 to-red-100 dark:from-red-900/20 dark:to-red-800/20': isAnswerSubmitted &&
                                            selectedOption ===
                                            option.id && !option.is_correct,
                                        'border-gray-200/60 dark:border-gray-600/60 hover:bg-gradient-to-r hover:from-gray-50 hover:to-gray-100 dark:hover:from-gray-800 dark:hover:to-gray-700':
                                            !
                                            isAnswerSubmitted || (isAnswerSubmitted && !option.is_correct)
                                    }">
                                    <div class="flex items-center h-5">
                                        <div class="w-5 h-5 rounded-xl border-2 flex items-center justify-center transition-all duration-200"
                                            :class="{
                                                'border-blue-500 bg-gradient-to-r from-blue-500 to-blue-600': selectedOption ===
                                                    option.id && !
                                                    isAnswerSubmitted,
                                                'border-green-500 bg-gradient-to-r from-green-500 to-green-600': isAnswerSubmitted &&
                                                    option.is_correct,
                                                'border-red-500 bg-gradient-to-r from-red-500 to-red-600': isAnswerSubmitted &&
                                                    selectedOption ===
                                                    option
                                                    .id && !option.is_correct,
                                                'border-gray-300/60 dark:border-gray-500/60': !selectedOption || (
                                                    selectedOption !==
                                                    option.id && !(isAnswerSubmitted && option.is_correct))
                                            }">
                                            <div x-show="selectedOption === option.id && !isAnswerSubmitted"
                                                class="w-2.5 h-2.5 rounded-full bg-white"></div>
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
                                    <div class="ml-3 text-sm flex-1">

                                        <!-- Option Text with Conditional Display -->
                                        <p x-show="!option.image_url || (option.text && option.text.trim() !== '')"
                                            x-transition:enter="transition ease-out duration-300"
                                            class="font-medium text-gray-900 dark:text-gray-100 leading-relaxed"
                                            x-text="option.text">
                                        </p>

                                        <!-- Modern Image Container with Loading States -->
                                        <div x-show="option.image_url" class="mb-2 group relative"
                                            x-init="console.log('Option image data:', { optionId: option.id, image: option.image, imageUrl: option.image_url, text: option.text })">
                                            <!-- Loading Skeleton with Animated Road Sign -->
                                            <div x-show="option.image_url && !imageLoaded[option.id]"
                                                x-transition:enter="transition ease-out duration-300"
                                                x-transition:enter-start="opacity-0"
                                                x-transition:enter-end="opacity-100"
                                                class="w-32 h-24 bg-gradient-to-br from-blue-50 to-blue-100 dark:from-blue-900/30 dark:to-blue-800/20 rounded-xl border border-blue-200 dark:border-blue-700 flex flex-col items-center justify-center overflow-hidden">

                                                <!-- Animated Road Sign Skeleton -->
                                                <div class="relative">
                                                    <div
                                                        class="w-16 h-12 bg-blue-200 dark:bg-blue-700 rounded-sm transform rotate-45 animate-pulse opacity-60">
                                                    </div>
                                                    <div
                                                        class="absolute top-0 left-1/2 transform -translate-x-1/2 w-1 h-6 bg-blue-300 dark:bg-blue-600 animate-pulse opacity-40">
                                                    </div>
                                                </div>

                                                <!-- Loading Text -->
                                                <div
                                                    class="mt-2 text-xs text-blue-600 dark:text-blue-400 font-medium animate-pulse">
                                                    Loading...
                                                </div>

                                                <!-- Subtle shimmer effect -->
                                                <div
                                                    class="absolute inset-0 bg-gradient-to-r from-transparent via-white/20 to-transparent -skew-x-12 animate-shimmer">
                                                </div>
                                            </div>

                                            <!-- Main Image with Smooth Transition -->
                                            <div x-show="imageLoaded[option.id] && !imageError[option.id]"
                                                x-transition:enter="transition ease-out duration-500"
                                                x-transition:enter-start="opacity-0 transform scale-95"
                                                x-transition:enter-end="opacity-100 transform scale-100"
                                                class="relative group">

                                                <!-- CSS Background Image Container -->
                                                <div :style="`background-image: url('${option.image_url || '/images/road-sign-placeholder.svg'}'); background-size: cover; background-position: center;`"
                                                    class="w-32 h-24 rounded-xl" x-init="console.log('Image data at bg element:', { optionId: option.id, imageUrl: option.image_url });
                                                    imageLoaded[option.id] = true;">
                                                    <!-- Hover Overlay -->
                                                    <div
                                                        class="absolute inset-0 bg-gradient-to-t from-black/20 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300 rounded-xl">
                                                    </div>

                                                    <!-- View Larger Button -->
                                                    <button
                                                        @click="showImageModal(option.image_url, option.text || 'Option Image')"
                                                        class="absolute top-2 right-2 bg-white/90 dark:bg-black/80 backdrop-blur-sm rounded-full p-1.5 opacity-0 group-hover:opacity-100 transition-opacity duration-300 hover:bg-white dark:hover:bg-black/90">
                                                        <svg class="w-4 h-4 text-blue-600 dark:text-blue-400"
                                                            fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2"
                                                                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v3m0 0v3m0-3h3m-3 0H7">
                                                            </path>
                                                        </svg>
                                                    </button>
                                                </div>
                                            </div>

                                            <!-- Error State with Custom Road Sign Placeholder -->
                                            <div x-show="imageError[option.id]"
                                                x-transition:enter="transition ease-out duration-300"
                                                x-transition:enter-start="opacity-0"
                                                x-transition:enter-end="opacity-100"
                                                class="w-32 h-24 bg-gradient-to-br from-orange-50 to-orange-100 dark:from-orange-900/30 dark:to-orange-800/20 rounded-xl border-2 border-orange-200 dark:border-orange-700 flex flex-col items-center justify-center">

                                                <!-- Error Road Sign Icon -->
                                                <div class="relative">
                                                    <div
                                                        class="w-16 h-12 bg-orange-300 dark:bg-orange-700 rounded-sm transform rotate-45 opacity-60">
                                                    </div>
                                                    <div
                                                        class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 text-white dark:text-orange-900 font-bold text-lg">
                                                        !</div>
                                                </div>

                                                <!-- Error Text -->
                                                <span
                                                    class="text-xs text-orange-600 dark:text-orange-400 font-medium mt-1">Image</span>
                                                <span
                                                    class="text-xs text-orange-500 dark:text-orange-500">Unavailable</span>
                                            </div>
                                        </div>
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
                        <div x-show="showFeedback" class="mt-4 p-4 rounded-xl transition-all duration-300"
                            :class="{
                                'bg-gradient-to-r from-green-50 to-green-100 dark:from-green-900/20 dark:to-green-800/20 border border-green-200/60 dark:border-green-800/60': isAnswerCorrect,
                                'bg-gradient-to-r from-red-50 to-red-100 dark:from-red-900/20 dark:to-red-800/20 border border-red-200/60 dark:border-red-800/60':
                                    !
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
            <div x-cloak
                class="border-t border-gray-200/60 dark:border-gray-700/60 bg-gradient-to-r from-white to-gray-50 dark:from-gray-800 dark:to-gray-750 p-3 sm:p-4 md:rounded-b-2xl">
                <div class="flex justify-between items-center gap-2">
                    <button @click="previousQuestion" :disabled="currentQuestionIndex === 0"
                        class="px-4 py-2.5 sm:px-5 sm:py-2.5 border border-gray-300/60 dark:border-gray-600/60 rounded-xl text-sm font-medium text-gray-700 dark:text-gray-200 bg-gradient-to-r from-white to-gray-50 dark:from-gray-700 dark:to-gray-600 hover:from-gray-50 hover:to-gray-100 dark:hover:from-gray-600 dark:hover:to-gray-500 disabled:opacity-50 disabled:cursor-not-allowed transition-all duration-200 hover:-translate-y-0.5">
                        <span class="hidden sm:inline">{{ __('quiz.previous') }}</span>
                        <svg class="w-4 h-4 sm:hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M15 19l-7-7 7-7" />
                        </svg>
                    </button>

                    <div class="items-center gap-2 hidden sm:flex">
                        <span class="text-sm text-gray-500 dark:text-gray-400"
                            x-text="`${actualQuestionNumber} of ${totalQuestions}`"></span>
                    </div>

                    <!-- See More Button (shown after quiz completion) -->
                    <button x-show="quizCompleted"
                        @click="window.location.href = '{{ route('dashboard.quizzes.index', ['locale' => app()->getLocale()]) }}'"
                        class="px-4 py-2.5 sm:px-5 sm:py-2.5 border border-transparent rounded-xl text-sm font-medium text-white bg-gradient-to-r from-blue-500 to-blue-600 hover:from-blue-600 hover:to-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 flex-1 sm:flex-none transition-all duration-200 hover:-translate-y-0.5">
                        {{ __('quiz.seeMore') }}
                        <svg class="w-4 h-4 ml-1 -mr-1 sm:inline hidden" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </button>

                    <!-- Regular Next/Finish Button (hidden after quiz completion) -->
                    <button x-show="!quizCompleted" @click="nextQuestion"
                        :disabled="!userAnswers[currentQuestion.id] || (autoAdvance && !isLastQuestion) || isSubmitting"
                        class="px-4 py-2.5 sm:px-5 sm:py-2.5 border border-transparent rounded-xl text-sm font-medium text-white bg-gradient-to-r from-blue-500 to-blue-600 hover:from-blue-600 hover:to-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 disabled:opacity-50 disabled:cursor-not-allowed flex-1 sm:flex-none transition-all duration-200 hover:-translate-y-0.5 disabled:hover:translate-y-0">
                        <template x-if="isSubmitting">
                            <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white inline"
                                xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10"
                                    stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor"
                                    d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                </path>
                            </svg>
                            <span
                                x-text="isLastQuestion ? '{{ __('quiz.isSubmitting') }}' : '{{ __('quiz.isLoading') }}'"></span>
                        </template>
                        <template x-if="!isSubmitting">
                            <span
                                x-text="isGuest ? '{{ __('quiz.signUpToContinue') }}' : (isLastQuestion ? '{{ __('quiz.finish') }}' : '{{ __('quiz.next') }}')"></span>
                            <svg class="w-4 h-4 ml-1 -mr-1 sm:inline hidden" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 5l7 7-7 7" />
                            </svg>
                        </template>
                    </button>
                </div>
            </div>

            <!-- Sign Up/Login Modal -->
            <div x-cloak x-show="showLoginModal" x-cloak
                class="fixed inset-0 bg-black/60 backdrop-blur-sm flex items-center justify-center z-50 p-4">
                <div
                    class="bg-gradient-to-br from-white to-gray-50 dark:from-gray-800 dark:to-gray-750 rounded-3xl p-8 max-w-md w-full mx-4 shadow-lg border border-gray-100/60 dark:border-gray-700/60 transform transition-all duration-300 scale-100">
                    <!-- Header with icon -->
                    <div class="text-center mb-6">
                        <div
                            class="w-20 h-20 bg-gradient-to-r from-blue-500 to-orange-500 rounded-2xl flex items-center justify-center mx-auto mb-6 shadow-lg transition-all duration-300">
                            <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                        </div>
                        <h3
                            class="text-2xl font-bold bg-gradient-to-r from-blue-700 to-blue-900 dark:from-blue-300 dark:to-blue-500 bg-clip-text text-transparent mb-4">
                            {{ __('quiz.signUpRequired') }}
                        </h3>
                        <p class="text-gray-600 dark:text-gray-300 text-sm leading-relaxed">
                            {{ __('quiz.signUpRequiredMessage') }}
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
                            <span class="relative z-10">{{ __('quiz.signUp') }}</span>
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
                            <span class="relative z-10">{{ __('quiz.login') }}</span>
                            <svg class="w-4 h-4 ml-2 transform group-hover:translate-x-1 transition-transform duration-300 relative z-10"
                                fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 5l7 7-7 7" />
                            </svg>
                        </a>

                        <button @click="showLoginModal = false"
                            class="w-full px-6 py-3 border border-gray-300 dark:border-gray-600 rounded-xl text-sm font-medium text-gray-700 dark:text-gray-200 bg-gray-50 dark:bg-gray-700 hover:bg-gray-100 dark:hover:bg-gray-600 transform transition-all duration-300 hover:scale-[1.02]">
                            {{ __('quiz.cancel') }}
                        </button>
                    </div>

                    <!-- Additional info -->
                    <div class="mt-6 text-center">
                        <p class="text-xs text-gray-500 dark:text-gray-400">
                            {{ __('quiz.termsAndPrivacy') }}
                        </p>
                    </div>
                </div>
            </div>

            <!-- Quiz Submission Loading Modal -->
            <div x-cloak x-show="isSubmitting" x-cloak
                class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
                <div class="bg-white dark:bg-gray-800 rounded-lg p-6 max-w-sm w-full mx-4 text-center">
                    <div class="mb-4">
                        <div
                            class="inline-flex items-center justify-center w-12 h-12 bg-blue-100 dark:bg-blue-900/20 rounded-full">
                            <svg class="animate-spin h-6 w-6 text-blue-600" xmlns="http://www.w3.org/2000/svg"
                                fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10"
                                    stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor"
                                    d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                </path>
                            </svg>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Results Modal -->
            <div x-cloak x-show="showResultsModal" x-cloak
                class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
                <div
                    class="bg-white dark:bg-gray-800 rounded-lg p-6 max-w-md w-full mx-4 max-h-[90vh] overflow-y-auto">
                    <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">
                        {{ __('quiz.quizCompleted') }}
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

                    <!-- Recent Leaderboard Changes -->
                    <div x-show="leaderboardChanges && leaderboardChanges.length > 0"
                        class="mb-4 p-3 bg-blue-50 dark:bg-blue-900/20 rounded-lg border border-blue-200 dark:border-blue-700">
                        <div class="flex items-center space-x-2 mb-2">
                            <div class="text-lg">📊</div>
                            <h4 class="text-sm font-semibold text-blue-800 dark:text-blue-200">
                                {{ __('quiz.recentActivity') }}</h4>
                        </div>
                        <div class="space-y-2">
                            <template x-for="change in leaderboardChanges" :key="change.id">
                                <div
                                    class="flex items-center justify-between text-sm p-2 bg-white dark:bg-gray-800 rounded">
                                    <div class="flex items-center space-x-2">
                                        <div
                                            class="w-6 h-6 rounded-full bg-gradient-to-br from-blue-500 to-purple-500 flex items-center justify-center text-white text-xs font-bold">
                                            <span x-text="change.user.first_name.charAt(0)"></span>
                                        </div>
                                        <div>
                                            <p class="font-medium text-gray-900 dark:text-gray-100"
                                                x-text="change.message"></p>
                                            <p class="text-xs text-gray-500 dark:text-gray-400"
                                                x-text="change.time_ago"></p>
                                        </div>
                                    </div>
                                    <div class="text-xs font-medium text-blue-600 dark:text-blue-400"
                                        x-text="change.points_change"></div>
                                </div>
                            </template>
                        </div>
                    </div>

                    <!-- Leaderboard Position -->
                    <div x-show="updatedStats && updatedStats.leaderboardPosition !== null && updatedStats.leaderboardPosition !== undefined" class="mb-6">
                        <div
                            class="bg-gradient-to-r from-yellow-50 to-orange-50 dark:from-yellow-900/20 dark:to-orange-900/20 p-3 sm:p-4 rounded-lg border-2 border-yellow-300 dark:border-yellow-700">
                            <div class="text-center">
                                <div class="text-base sm:text-lg font-bold text-yellow-800 dark:text-yellow-200 mb-2">
                                    <span x-show="updatedStats.leaderboardPosition === 1">
                                        🏆 {{ __('quiz.firstPlace') }}
                                    </span>
                                    <span x-show="updatedStats.leaderboardPosition !== 1">
                                        {{ __('quiz.newPosition') }}: #<span
                                            x-text="updatedStats.leaderboardPosition"></span>/25
                                    </span>
                                </div>
                                <div
                                    class="text-xs sm:text-sm text-yellow-700 dark:text-yellow-300 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
                                    <div class="text-center sm:text-left">
                                        <span x-show="updatedStats.leaderboardPosition === 1">
                                            {{ __('quiz.firstPlaceMessage') }}
                                        </span>
                                        <span
                                            x-show="updatedStats.leaderboardPosition !== 1 && updatedStats.leaderboardPosition <= 10">
                                            {{ __('quiz.topTenMessage') }}
                                        </span>
                                        <span x-show="updatedStats.leaderboardPosition > 10">
                                            {{ __('quiz.keepImproving') }}
                                        </span>
                                    </div>
                                    <a href="{{ route('leaderboard') }}"
                                        class="text-xs font-medium text-yellow-600 dark:text-yellow-400 hover:text-yellow-800 dark:hover:text-yellow-200 underline whitespace-nowrap">
                                        {{ app()->getLocale() === 'rw' ? 'Reba Irushanwa' : 'View Leaderboard' }}
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Stats Display -->
                    <div class="space-y-3 mb-6">
                        <div class="bg-green-50 dark:bg-green-900/20 p-3 rounded-lg">
                            <p class="text-green-800 dark:text-green-200 font-medium">
                                {{ __('quiz.scoreEarned') }}: <span x-text="correctCount"></span>/<span
                                    x-text="totalQuestions"></span>
                                (<span x-text="currentScore"></span>%)
                            </p>
                        </div>
                        <div x-show="updatedStats" class="bg-blue-50 dark:bg-blue-900/20 p-3 rounded-lg">
                            <p class="text-blue-800 dark:text-blue-200 font-medium">
                                {{ __('quiz.averageScore') }}: <span
                                    x-text="Math.round(updatedStats.averageScore * updatedStats.totalQuestionsAnswered / 100)"></span>/<span
                                    x-text="updatedStats.totalQuestionsAnswered"></span>
                                (<span x-text="updatedStats.averageScore"></span>%)
                            </p>
                        </div>
                        <div x-show="updatedStats && updatedStats.quizComparison"
                            class="bg-purple-50 dark:bg-purple-900/20 p-3 rounded-lg">
                            <p class="text-purple-800 dark:text-purple-200">
                                {{ __('quiz.improvement') }}:
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
                    <div x-show="updatedStats && updatedStats.hasPlan" class="mt-6">
                        <div class="flex flex-col sm:flex-row gap-3">
                            <a href="#" @click="showResultsModal = false"
                                class="flex-1 text-center px-4 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-medium transition-colors duration-200">
                                {{ __('quiz.practiceAgain') }}
                            </a>
                            <a href="{{ route('dashboard.progress') }}"
                                class="flex-1 text-center px-4 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg font-medium transition-colors duration-200">
                                {{ __('quiz.checkProgress') }}
                            </a>
                        </div>
                    </div>

                    <!-- Upsell for Users without Plan -->
                    <div x-show="updatedStats && !updatedStats.hasPlan" class="mt-6">
                        <div
                            class="bg-gradient-to-r from-orange-50 to-amber-50 dark:from-orange-900/20 dark:to-amber-900/20 p-5 rounded-xl border border-orange-200 dark:border-orange-800/50">
                            <div class="flex items-start">
                                <div class="flex-shrink-0 mr-3 mt-0.5">
                                    <div
                                        class="flex items-center justify-center w-8 h-8 rounded-full bg-orange-100 dark:bg-orange-900/50 text-orange-600 dark:text-orange-300">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20"
                                            fill="currentColor">
                                            <path fill-rule="evenodd"
                                                d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-11a1 1 0 10-2 0v3.586L7.707 9.293a1 1 0 00-1.414 1.414l3 3a1 1 0 001.414 0l3-3a1 1 0 00-1.414-1.414L11 10.586V7z"
                                                clip-rule="evenodd" />
                                        </svg>
                                    </div>
                                </div>
                                <div>
                                    <h4 class="text-orange-800 dark:text-orange-200 font-semibold mb-1">
                                        {{ __('quiz.unlockMoreFeatures') }}
                                    </h4>
                                    <p class="text-orange-700 dark:text-orange-300 text-sm mb-3">
                                        {{ __('quiz.subscriptionMessage') }}
                                    </p>
                                    <a href="{{ route('plans') }}"
                                        class="inline-flex items-center justify-center w-full px-4 py-2.5 bg-gradient-to-r from-orange-500 to-yellow-500 hover:from-orange-600 hover:to-yellow-600 text-white rounded-lg font-medium transition-colors duration-200">
                                        {{ __('quiz.getPlanToPractice') }}
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 ml-1.5"
                                            viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd"
                                                d="M10.293 5.293a1 1 0 011.414 0l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414-1.414L12.586 11H5a1 1 0 110-2h7.586l-2.293-2.293a1 1 0 010-1.414z"
                                                clip-rule="evenodd" />
                                        </svg>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Close Button -->
                    <div class="mt-6 pt-4 border-t border-gray-200 dark:border-gray-700">
                        <div class="flex justify-center sm:justify-end">
                            <button @click="showResultsModal = false"
                                class="w-full sm:w-auto px-6 py-2.5 text-sm font-medium text-gray-700 dark:text-gray-200 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-600 transition-colors duration-200">
                                {{ __('quiz.close') }}
                            </button>
                        </div>
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
                    isSubmitting: false,
                    quizCompleted: false, // New state to track completion independently of modal
                    updatedStats: null,
                    currentScore: 0,
                    flaggedQuestions: new Set(),
                    timer: null,
                    startTime: null,
                    endTime: null,
                    autoAdvance: false,
                    hasPlan: false, // Will be updated in init()
                    // Image loading states
                    imageLoaded: {},
                    imageError: {},
                    xpAnimation: {
                        show: false,
                        points: 0,
                        currentPoints: 0
                    },
                    questions: config.questions.map((q, index) => ({
                        id: q.id || `q-${index}`,
                        text: q.text || q.question_text || `Question ${index + 1}`,
                        image_url: q.image_url || null,
                        options: Array.isArray(q.options) ? q.options.map((opt, optIndex) => {
                            // Debug: Log the raw image URL
                            console.log('Raw option image_url:', opt.image_url, 'Type:',
                                typeof opt.image_url);

                            const cleanedOption = {
                                id: opt.id || `opt-${index}-${optIndex}`,
                                text: opt.text || opt.option_text ||
                                    `Option ${optIndex + 1}`,
                                image_url: opt.image_url || null,
                                is_correct: opt.is_correct || opt.correct || false,
                                explanation: opt.explanation || ''
                            };

                            // Debug: Log the cleaned image URL
                            console.log('Cleaned option image:', cleanedOption
                                .image_url);

                            return cleanedOption;
                        }) : []
                    })),
                    userAnswers: {},
                    correctCount: 0,
                    incorrectCount: 0,

                    // Translations
                    translations: {
                        quiz: {
                            correctFeedback: "{{ __('quiz.correctFeedback') }}",
                            incorrectFeedback: "{{ __('quiz.incorrectFeedback') }}"
                        }
                    },

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
                    get actualQuestionNumber() {
                        // Return the real question number (1-based) in the original order
                        return this.currentQuestionIndex + 1;
                    },
                    get answeredCount() {
                        return Object.keys(this.userAnswers).length;
                    },

                    // Sound functionality
                    soundEnabled: true,
                    correctSound: null,
                    incorrectSound: null,
                    liveNotification: null,
                    leaderboardChanges: [],
                    notificationTimer: null,
                    
                    // Robot companions
                    robotResponses: [],
                    robotSummary: [],
                    robotNotificationTimer: null,

                    // Initialize audio elements
                    initSounds() {
                        // Create audio objects for WAV files
                        this.correctSound = new Audio('/assets/sounds/correct.wav');
                        this.incorrectSound = new Audio('/assets/sounds/incorrect.wav');

                        // Set volume for both sounds
                        this.correctSound.volume = 0.5;
                        this.incorrectSound.volume = 0.5;

                        // Preload sounds
                        this.correctSound.load();
                        this.incorrectSound.load();
                    },

                    // Play correct sound
                    playCorrectSound() {
                        if (this.correctSound) {
                            this.correctSound.currentTime = 0;
                            this.correctSound.play().catch(error => {
                                console.log('Correct sound play failed:', error);
                            });
                        }
                    },

                    // Play incorrect sound
                    playIncorrectSound() {
                        if (this.incorrectSound) {
                            this.incorrectSound.currentTime = 0;
                            this.incorrectSound.play().catch(error => {
                                console.log('Incorrect sound play failed:', error);
                            });
                        }
                    },

                    // Play sound effect
                    playSound(isCorrect) {
                        if (!this.soundEnabled) return;

                        try {
                            if (isCorrect) {
                                this.playCorrectSound();
                            } else {
                                this.playIncorrectSound();
                            }
                        } catch (error) {
                            console.log('Sound system error:', error);
                        }
                    },

                    // Toggle sound on/off
                    toggleSound() {
                        this.soundEnabled = !this.soundEnabled;
                        // Save preference to localStorage (for both guests and authenticated users)
                        localStorage.setItem(`quiz_${this.quizId}_sound_enabled`, this.soundEnabled
                            .toString());
                    },

                    // Load sound preference
                    loadSoundPreference() {
                        const saved = localStorage.getItem(`quiz_${this.quizId}_sound_enabled`);
                        if (saved !== null) {
                            this.soundEnabled = saved === 'true';
                        }
                    },

                    // Initialize the quiz
                    init() {
                        // Initialize sounds first
                        this.initSounds();
                        this.loadSoundPreference();
                        // Restore timer state first
                        this.validateAndRestoreTimer();

                        // Restore pause state
                        this.restorePauseState();

                        // Fetch user plan info if not guest
                        if (!this.isGuest) {
                            this.fetchUserPlan();
                            // Check for existing attempt or create new one
                            this.initializeAttempt();
                        }

                        this.startTime = new Date();

                        // Don't start timer automatically - wait for first answer

                        this.loadProgress();

                        // Initialize image loading states
                        this.initializeImageStates();

                        // Mark as initialized to hide loading state
                        this.initialized = true;
                        
                        // Fetch historical activity immediately when quiz loads
                        this.fetchLiveActivities();
                        
                        // Add page unload cleanup
                        window.addEventListener('beforeunload', () => {
                            this.destroy();
                        });
                        
                        // Add visibility change cleanup (when user switches tabs)
                        document.addEventListener('visibilitychange', () => {
                            if (document.hidden) {
                                // Optionally pause timer when tab is not visible
                                if (this.timer && !this.isPaused) {
                                    this.pauseQuiz();
                                }
                            }
                        });
                    },

                    // Clean up when navigating away from quiz
                    destroy() {
                        console.log('Cleaning up quiz state...');
                        
                        // Clear timer
                        if (this.timer) {
                            clearInterval(this.timer);
                            this.timer = null;
                        }
                        
                        // Clear quiz state from memory
                        this.currentQuestionIndex = 0;
                        this.selectedOption = null;
                        this.isAnswerSubmitted = false;
                        this.isAnswerCorrect = false;
                        this.showFeedback = false;
                        this.feedbackMessage = '';
                        this.userAnswers = {};
                        this.correctCount = 0;
                        this.incorrectCount = 0;
                        this.quizCompleted = false;
                        this.isSubmitting = false;
                        this.startTime = null;
                        this.endTime = null;
                        this.initialized = false;
                        
                        // Clear image states
                        this.imageLoaded = {};
                        this.imageError = {};
                        
                        // Don't clear localStorage data as user might want to resume later
                        // But clear any in-memory progress
                        console.log('Quiz state cleaned up');
                    },

                    // Initialize image loading states for all options
                    initializeImageStates() {
                        this.questions.forEach(question => {
                            question.options.forEach(option => {
                                if (option.image_url) {
                                    this.imageLoaded[option.id] = false;
                                    this.imageError[option.id] = false;
                                }
                            });
                        });
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
                        if (!confirm('{{ __('quiz.resetConfirmation') }}')) {
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

                        // Reset backend attempt if exists
                        if (this.currentAttempt && !this.isGuest) {
                            this.resetBackendAttempt();
                        }

                        // Load first question
                        this.loadQuestionState();

                        // Restart timer
                        clearInterval(this.timer);
                        this.startTimer();
                    },

                    // Reset backend attempt
                    async resetBackendAttempt() {
                        if (!this.currentAttempt) return;

                        try {
                            const response = await fetch(
                                `/api/attempts/${this.currentAttempt.id}/reset`, {
                                    method: 'POST',
                                    credentials: 'same-origin',
                                    headers: {
                                        'Content-Type': 'application/json',
                                        'X-CSRF-TOKEN': document.querySelector(
                                            'meta[name="csrf-token"]').content
                                    }
                                });

                            if (response.ok) {
                                console.log('Backend attempt reset successfully');
                            } else {
                                console.warn('Failed to reset backend attempt');
                            }
                        } catch (error) {
                            console.error('Error resetting backend attempt:', error);
                        }
                    },

                    // Question navigation
                    nextQuestion() {
                        // If user is a guest, show login modal instead of proceeding
                        if (this.isGuest) {
                            this.showLoginModal = true;
                            return;
                        }

                        // Check if current question is answered
                        const currentQuestionId = this.currentQuestion.id;
                        const isCurrentQuestionAnswered = this.userAnswers[currentQuestionId] !== undefined;

                        // If current question is not answered, don't allow navigation
                        if (!isCurrentQuestionAnswered) {
                            // Show a subtle hint that the question needs to be answered
                            this.showFeedback = true;
                            this.feedbackMessage = "Please answer this question before continuing.";
                            return;
                        }

                        if (this.isLastQuestion) {
                            this.finishQuiz();
                            return;
                        }

                        // Proceed to next question
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

                        // Play sound effect for feedback
                        this.playSound(option.is_correct);

                        // Set feedback message
                        this.feedbackMessage = option.is_correct ?
                            this.translations.quiz.correctFeedback :
                            this.translations.quiz.incorrectFeedback;

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

                            // Also save immediately to backend
                            this.saveCurrentAnswer();
                        }

                        // Auto-advance if enabled
                        if (this.autoAdvance) {
                            setTimeout(() => {
                                if (!this.isLastQuestion) {
                                    this.nextQuestion();
                                } else {
                                    // Scroll to next button if it's the last question
                                    this.$nextTick(() => {
                                        const nextButton = Array.from(this.$el
                                                .querySelectorAll('button'))
                                            .find(btn => btn.textContent?.includes(
                                                'Finish'));
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

                            // Determine if the answer is correct if not already set
                            if (savedAnswer.isCorrect === null) {
                                const selectedOption = this.currentQuestion.options.find(opt => opt.id ===
                                    savedAnswer.optionId);
                                this.isAnswerCorrect = selectedOption ? selectedOption.is_correct : false;
                                // Update the saved answer with the correct status
                                savedAnswer.isCorrect = this.isAnswerCorrect;
                            } else {
                                this.isAnswerCorrect = savedAnswer.isCorrect;
                            }

                            this.showFeedback = true;
                            this.feedbackMessage = this.isAnswerCorrect ?
                                this.translations.quiz.correctFeedback :
                                this.translations.quiz.incorrectFeedback;

                            // Update counts to ensure accuracy
                            this.updateAnswerCounts();
                        } else {
                            this.selectedOption = null;
                            this.isAnswerSubmitted = false;
                            this.isAnswerCorrect = false;
                            this.showFeedback = false;
                            this.feedbackMessage = '';
                        }

                        // Initialize image loading states for current question options
                        this.currentQuestion.options.forEach(option => {
                            if (option.image_url && !this.imageLoaded[option.id]) {
                                this.imageLoaded[option.id] = false;
                                this.imageError[option.id] = false;
                            }
                        });

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
                        if (this.isGuest || !this.currentAttempt) return;

                        // Save current answer to backend
                        this.saveCurrentAnswer();
                    },

                    async initializeAttempt() {
                        // If we already have an attempt from config, use it
                        if (this.currentAttempt) {
                            this.loadAttemptState();
                            return;
                        }

                        // Otherwise, check for existing incomplete attempt or create new one
                        try {
                            const response = await fetch(
                                `/{{ app()->getLocale() }}/api/quizzes/${this.quizId}/attempt`, {
                                    method: 'GET',
                                    credentials: 'same-origin',
                                    headers: {
                                        'Content-Type': 'application/json',
                                        'X-CSRF-TOKEN': document.querySelector(
                                            'meta[name="csrf-token"]').content
                                    }
                                });

                            if (response.ok) {
                                const data = await response.json();
                                this.currentAttempt = data.attempt;
                                console.log('Using attempt:', this.currentAttempt.id);

                                // Load the attempt state if it has existing answers
                                this.loadAttemptState();
                            } else {
                                console.error('Failed to get/create attempt');
                            }
                        } catch (error) {
                            console.error('Error getting/creating attempt:', error);
                        }
                    },

                    loadAttemptState() {
                        if (!this.currentAttempt) return;

                        // First, fetch fresh progress from database
                        this.fetchAttemptProgress();

                        // Then load any existing answers from the attempt (legacy support)
                        if (this.currentAttempt.answers) {
                            const answers = this.currentAttempt.answers;
                            Object.keys(answers).forEach(questionId => {
                                this.userAnswers[questionId] = {
                                    optionId: answers[questionId],
                                    isCorrect: null, // We'll determine this when loading the question
                                    timestamp: new Date().toISOString()
                                };
                            });

                            // Update counts based on loaded answers
                            this.updateAnswerCounts();
                        }
                    },

                    updateAnswerCounts() {
                        // Reset counts
                        this.correctCount = 0;
                        this.incorrectCount = 0;

                        // Count correct and incorrect answers
                        Object.values(this.userAnswers).forEach(answer => {
                            if (answer.isCorrect === true) {
                                this.correctCount++;
                            } else if (answer.isCorrect === false) {
                                this.incorrectCount++;
                            }
                        });
                    },

                    async saveCurrentAnswer() {
                        if (!this.selectedOption || this.isGuest) return;

                        try {
                            const currentQuestion = this.questions[this.currentQuestionIndex];
                            const response = await fetch(`/api/attempts/${this.currentAttempt.id}`, {
                                method: 'PUT',
                                credentials: 'same-origin',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': document.querySelector(
                                        'meta[name="csrf-token"]').content
                                },
                                body: JSON.stringify({
                                    answers: [{
                                        question_id: currentQuestion.id,
                                        option_id: this.selectedOption,
                                        time_spent: this.timeTaken
                                    }],
                                    time_taken: this.timeTaken
                                })
                            });

                            if (response.ok) {
                                const data = await response.json();
                                console.log('Answer saved successfully');
                                
                                // Fetch live activities after submitting answer
                                console.log('Fetching live activities after answer submission...');
                                this.fetchLiveActivities();
                                
                                // Handle robot companion responses
                                if (data.robot_responses && data.robot_responses.length > 0) {
                                    this.robotResponses = data.robot_responses;
                                    this.showRobotCompanionNotifications();
                                    
                                    // Emit for companion sidebar
                                    window.dispatchEvent(new CustomEvent('robotResponses', {
                                        detail: {
                                            robotResponses: data.robot_responses
                                        }
                                    }));
                                }
                            } else {
                                console.warn('Failed to save answer:', response.status);
                            }
                        } catch (error) {
                            console.error('Error saving answer:', error);
                        }
                    },

                    // Fetch live activities after submitting answer
                    async fetchLiveActivities() {
                        try {
                            console.log('Making API call to /api/live-activities...');
                            const response = await fetch('/api/live-activities?quiz_id=' + this.quizId, {
                                headers: {
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content,
                                    'Accept': 'application/json'
                                }
                            });
                            
                            if (response.ok) {
                                const data = await response.json();
                                console.log('Live activities API response:', data);
                                
                                if (data.success) {
                                    console.log('Emitting liveActivityUpdate event with:', {
                                        activities: data.activities || [],
                                        notification: data.notification
                                    });
                                    // Emit for companion sidebar
                                    window.dispatchEvent(new CustomEvent('liveActivityUpdate', {
                                        detail: {
                                            activities: data.activities || [],
                                            notification: data.notification
                                        }
                                    }));
                                }
                            } else {
                                console.error('Live activities API returned error:', response.status);
                            }
                        } catch (error) {
                            console.error('Error fetching live activities:', error);
                        }
                    },

                    // Quiz completion
                    finishQuiz() {
                        clearInterval(this.timer);
                        this.endTime = new Date();

                        // Set submitting state
                        this.isSubmitting = true;

                        // Calculate score
                        const score = Math.round(
                            (this.correctCount / this.totalQuestions) * 100
                        );

                        // Mark quiz as completed
                        this.quizCompleted = true;

                        // Emit quiz completion event
                        window.dispatchEvent(new CustomEvent('quizCompleted', {
                            detail: { quizId: this.quizId }
                        }));

                        // Save results
                        this.saveResults(score);

                        // Start 3-second timeout for stats update and modal (only if not guest)
                        setTimeout(() => {
                            if (!this.isGuest) {
                                this.fetchUpdatedStats();
                                this.loadLeaderboardChanges();
                                this.loadRobotSummary();
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
                                // Reset submitting state
                                this.isSubmitting = false;
                            })
                            .catch(error => {
                                console.error('Error saving results:', error);
                                // Reset submitting state
                                this.isSubmitting = false;
                            });
                    },

                    loadProgress() {
                        // Load progress from database via API if user is authenticated
                        if (!this.isGuest && this.currentAttempt && this.currentAttempt.id) {
                            this.fetchAttemptProgress();
                        }
                    },

                    // Fetch current attempt progress from database
                    async fetchAttemptProgress() {
                        try {
                            const response = await fetch(`/api/attempts/${this.currentAttempt.id}`, {
                                method: 'GET',
                                credentials: 'same-origin',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': document.querySelector(
                                        'meta[name="csrf-token"]').content
                                }
                            });

                            if (response.ok) {
                                const data = await response.json();
                                if (data.success && data.attempt) {
                                    this.syncProgressFromDatabase(data.attempt);
                                }
                            }
                        } catch (error) {
                            console.error('Error fetching attempt progress:', error);
                        }
                    },

                    // Sync progress from database response
                    syncProgressFromDatabase(attemptData) {
                        // Update user answers from database
                        if (attemptData.user_answers && attemptData.user_answers.length > 0) {
                            const updatedAnswers = {};

                            attemptData.user_answers.forEach(answer => {
                                if (answer.question_id && answer.option_id) {
                                    updatedAnswers[answer.question_id] = {
                                        optionId: answer.option_id,
                                        isCorrect: Boolean(answer
                                        .is_correct), // Ensure boolean conversion
                                        timestamp: answer.time_spent || Date.now()
                                    };
                                }
                            });

                            this.userAnswers = updatedAnswers;

                            // Update answer counts to reflect database state
                            this.updateAnswerCounts();

                            // Navigate to the first unanswered question based on database data
                            this.navigateToFirstUnanswered();
                        }
                    },

                    // Navigate to the first unanswered question based on current answers
                    navigateToFirstUnanswered() {
                        let firstUnansweredIndex = 0;
                        for (let i = 0; i < this.questions.length; i++) {
                            if (!this.userAnswers[this.questions[i].id]) {
                                firstUnansweredIndex = i;
                                break;
                            }
                            // If all questions up to current index are answered, continue to next
                            if (i === this.questions.length - 1) {
                                // All questions are answered, go to the last one
                                firstUnansweredIndex = this.questions.length - 1;
                            }
                        }

                        this.currentQuestionIndex = firstUnansweredIndex;
                        this.loadQuestionState();
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
                        // Progress is now managed by database, no localStorage to clear
                        console.log('Progress cleared - database will handle reset');
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
                    },

                    // Notification methods
                    loadLeaderboardChanges() {
                        fetch('/api/leaderboard/changes')
                            .then(response => response.json())
                            .then(data => {
                                this.leaderboardChanges = data.changes || [];
                                this.showLiveNotifications();
                            })
                            .catch(error => console.error('Error loading leaderboard changes:', error));
                    },

                    showLiveNotifications() {
                        // Show header notification if there are recent changes
                        if (this.leaderboardChanges.length > 0 && !this.isGuest) {
                            const latestChange = this.leaderboardChanges[0];
                            this.liveNotification = {
                                message: latestChange.message,
                                type: 'info'
                            };

                            // Hide notification after 5 seconds
                            setTimeout(() => {
                                this.liveNotification = null;
                            }, 5000);
                        }
                    },

                    // Robot companion methods
                    showRobotCompanionNotifications() {
                        if (this.robotResponses.length === 0) return;

                        // Show individual robot messages with staggered timing
                        this.robotResponses.forEach((robot, index) => {
                            setTimeout(() => {
                                // Show live notification on desktop
                                this.liveNotification = {
                                    message: robot.message,
                                    type: 'robot_companion',
                                    robot_name: robot.robot_name,
                                    is_correct: robot.is_correct
                                };

                                // Hide after 4 seconds
                                setTimeout(() => {
                                    this.liveNotification = null;
                                }, 4000);
                            }, index * 1000); // Stagger notifications by 1 second
                        });
                    },

                    async loadRobotSummary() {
                        if (this.isGuest) return;

                        try {
                            const response = await fetch(`/api/robot/summary?quiz_id=${this.quizId}`, {
                                credentials: 'same-origin',
                                headers: {
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                                }
                            });

                            if (response.ok) {
                                const data = await response.json();
                                this.robotSummary = data.robot_summary || [];
                            }
                        } catch (error) {
                            console.error('Error loading robot summary:', error);
                        }
                    },

                    hideNotification() {
                        this.liveNotification = null;
                    },
                    
                                    }));
            });
        </script>
    @endpush
</div>
