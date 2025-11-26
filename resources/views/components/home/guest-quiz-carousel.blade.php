@php
    // Use the quizzes data passed from the home view or fallback to an empty collection
    $quizzes = $quizzes ?? collect();
    $hasQuizzes = $quizzes->isNotEmpty();
    $currentLocale = app()->getLocale();
    
    // Ensure we have a proper collection
    $quizzes = $quizzes instanceof \Illuminate\Support\Collection ? $quizzes : collect($quizzes);
    
    // Emojis for feedback
    $emojis = [
        'correct' => ['🎯', '✅', '👍', '👏', '💯', '🏆', '🥇', '🌟', '✨', '🎉'],
        'wrong' => ['❌', '😕', '🤔', '📝', '💡', '🔍', '🧠', '📚'],
        'encouragement' => [
            __('quiz.guestQuiz.correctFeedback.keepItUp'),
            __('quiz.guestQuiz.correctFeedback.greatJob'),
            __('quiz.guestQuiz.correctFeedback.youGotThis'),
            __('quiz.guestQuiz.correctFeedback.niceWork'),
            __('quiz.guestQuiz.correctFeedback.wellDone'),
            __('quiz.guestQuiz.correctFeedback.amazing'),
            __('quiz.guestQuiz.correctFeedback.fantastic'),
            __('quiz.guestQuiz.correctFeedback.superb')
        ]
    ];
    
    function getRandomEmoji($type) {
        $emojis = [
            'correct' => ['🎯', '✅', '👍', '👏', '💯', '🏆', '🥇', '🌟', '✨', '🎉'],
            'wrong' => ['❌', '😕', '🤔', '📝', '💡', '🔍', '🧠', '📚'],
            'encouragement' => [
                __('quiz.guestQuiz.correctFeedback.keepItUp'),
                __('quiz.guestQuiz.correctFeedback.greatJob'),
                __('quiz.guestQuiz.correctFeedback.youGotThis'),
                __('quiz.guestQuiz.correctFeedback.niceWork'),
                __('quiz.guestQuiz.correctFeedback.wellDone'),
                __('quiz.guestQuiz.correctFeedback.amazing'),
                __('quiz.guestQuiz.correctFeedback.fantastic'),
                __('quiz.guestQuiz.correctFeedback.superb')
            ]
        ];
        return $emojis[$type][array_rand($emojis[$type])];
    }
@endphp

@if($hasQuizzes)
<div x-data="{
    activeSlide: 0,
    totalSlides: {{ $quizzes->count() }},
    visibleSlides: 3, // Show 3 quizzes at once on desktop
    slideWidth: 100 / 3, // 3 slides visible at once
    
    getVisibleSlides() {
        // On mobile, show 1 slide at a time
        if (window.innerWidth < 768) {
            return 1;
        }
        // On tablet, show 2 slides at a time
        if (window.innerWidth < 1024) {
            return 2;
        }
        // On desktop, show 3 slides at a time
        return 3;
    },
    
    updateSlideWidth() {
        const visible = this.getVisibleSlides();
        this.visibleSlides = visible;
        this.slideWidth = 100 / visible;
        this.activeSlide = Math.min(this.activeSlide, this.totalSlides - visible);
    },
    
    nextSlide() {
        const maxSlide = this.totalSlides - this.visibleSlides;
        if (this.activeSlide < maxSlide) {
            this.activeSlide++;
        }
    },
    
    prevSlide() {
        if (this.activeSlide > 0) {
            this.activeSlide--;
        }
    },
    
    goToSlide(index) {
        this.activeSlide = index;
    },
    
    init() {
        this.updateSlideWidth();
        window.addEventListener('resize', this.updateSlideWidth.bind(this));
    }
}" class="w-full overflow-hidden">
    <!-- Navigation Arrows -->
    <button 
        @click="prevSlide()"
        class="absolute left-0 top-1/2 -translate-y-1/2 -translate-x-4 z-10 bg-white dark:bg-gray-800 rounded-full p-2 shadow-lg border border-gray-200 dark:border-gray-700 text-blue-600 dark:text-blue-400 hover:bg-blue-50 dark:hover:bg-gray-700 transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-blue-500"
        :class="{ 'opacity-50 cursor-not-allowed': activeSlide === 0 }"
        :disabled="activeSlide === 0"
        aria-label="Previous slide">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
        </svg>
    </button>
    
    <button 
        @click="nextSlide()"
        class="absolute right-0 top-1/2 -translate-y-1/2 translate-x-4 z-10 bg-white dark:bg-gray-800 rounded-full p-2 shadow-lg border border-gray-200 dark:border-gray-700 text-blue-600 dark:text-blue-400 hover:bg-blue-50 dark:hover:bg-gray-700 transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-blue-500"
        :class="{ 'opacity-50 cursor-not-allowed': activeSlide >= totalSlides - visibleSlides }"
        :disabled="activeSlide >= totalSlides - visibleSlides"
        aria-label="Next slide">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
        </svg>
    </button>

    <!-- Carousel Container -->
    <div class="relative w-full overflow-hidden" x-data="{
        // Quiz interaction state
        selectedOption: null,
        showFeedback: false,
        isCorrect: false,
        feedbackMessage: '',
        feedbackEmoji: '',
        correctAnswerId: null,
        answered: false,
        
        // Select an option
        selectOption(optionId, isCorrect, $event) {
            if (this.answered) return;
            
            this.selectedOption = optionId;
            this.isCorrect = isCorrect;
            this.answered = true;
            this.correctAnswerId = this.$el.querySelector('[data-correct-answer]')?.dataset.correctAnswer || null;
            
            // Set feedback message and emoji
            if (isCorrect) {
                this.feedbackMessage = getRandomEmoji('encouragement');
                this.feedbackEmoji = getRandomEmoji('correct');
            } else {
                this.feedbackMessage = 'Not quite right. Try again!';
                this.feedbackEmoji = getRandomEmoji('wrong');
            }
            
            this.showFeedback = true;
            
            // Auto-hide feedback after 3 seconds
            setTimeout(() => {
                this.showFeedback = false;
            }, 3000);
            
            // Prevent default action if it's a button
            if ($event && $event.target.tagName === 'BUTTON') {
                $event.preventDefault();
            }
        },
        
        // Reset the quiz state
        resetQuiz() {
            this.selectedOption = null;
            this.showFeedback = false;
            this.answered = false;
            this.correctAnswerId = null;
        }
    }">
        <!-- Feedback Toast -->
        <div x-show="showFeedback" 
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 translate-y-4"
             x-transition:enter-end="opacity-100 translate-y-0"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100 translate-y-0"
             x-transition:leave-end="opacity-0 translate-y-4"
             class="fixed bottom-8 left-1/2 transform -translate-x-1/2 z-50 flex items-center p-4 mb-4 text-sm rounded-lg shadow-lg"
             :class="{
                'bg-green-100 text-green-800 dark:bg-green-800 dark:text-green-100': isCorrect,
                'bg-red-100 text-red-800 dark:bg-red-800 dark:text-red-100': !isCorrect
             }"
             role="alert">
            <span class="text-2xl mr-2">
                <span x-text="feedbackEmoji"></span>
            </span>
            <span x-text="feedbackMessage" class="font-medium"></span>
            <button type="button" 
                    @click="showFeedback = false"
                    class="ml-4 -mx-1.5 -my-1.5 rounded-lg p-1.5 inline-flex h-8 w-8"
                    :class="{
                        'bg-green-200 text-green-500 hover:bg-green-300 focus:ring-2 focus:ring-green-400 dark:bg-green-900 dark:text-green-200 dark:hover:bg-green-800': isCorrect,
                        'bg-red-200 text-red-500 hover:bg-red-300 focus:ring-2 focus:ring-red-400 dark:bg-red-900 dark:text-red-200 dark:hover:bg-red-800': !isCorrect
                    }"
                    aria-label="Close">
                <span class="sr-only">Close</span>
                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                    <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                </svg>
            </button>
        </div>
        
        <!-- Slides -->
        <div 
            class="flex transition-transform duration-300 ease-in-out"
            :style="`transform: translateX(-${activeSlide * (100 / visibleSlides)}%);`"
            @slide-changed.window="resetQuiz()">
            @foreach($quizzes as $quiz)
                @php
                    // Get questions count and sample question
                    $questionsCount = $quiz->questions_count ?? $quiz->questions->count() ?? 0;
                            
                    // Get sample question (first question from the loaded collection)
                    $sampleQuestion = $quiz->questions->first();
                    
                    // Debug log
                    \Illuminate\Support\Facades\Log::debug('Quiz in carousel', [
                        'quiz_id' => $quiz->id,
                        'title' => $quiz->title,
                        'questions_count' => $questionsCount,
                        'sample_question' => $sampleQuestion ? 'exists' : 'none'
                    ]);
                    
                    // Handle both array and object quiz data
                    $title = is_array($quiz)
                        ? (is_array($quiz['title'] ?? null) 
                            ? ($quiz['title'][$currentLocale] ?? $quiz['title']['en'] ?? 'Untitled Quiz')
                            : ($quiz['title'] ?? 'Untitled Quiz'))
                        : (method_exists($quiz, 'getTranslation')
                            ? ($quiz->getTranslation('title', $currentLocale) ?? 'Untitled Quiz')
                            : (is_array($quiz->title ?? null)
                                ? ($quiz->title[$currentLocale] ?? $quiz->title['en'] ?? 'Untitled Quiz')
                                : ($quiz->title ?? 'Untitled Quiz')));
                            
                    $description = is_array($quiz)
                        ? (is_array($quiz['description'] ?? null)
                            ? ($quiz['description'][$currentLocale] ?? $quiz['description']['en'] ?? '')
                            : ($quiz['description'] ?? ''))
                        : (method_exists($quiz, 'getTranslation')
                            ? ($quiz->getTranslation('description', $currentLocale) ?? '')
                            : (is_array($quiz->description ?? null)
                                ? ($quiz->description[$currentLocale] ?? $quiz->description['en'] ?? '')
                                : ($quiz->description ?? '')));
                    
                    // Log debug info in development
                    if (app()->environment('local')) {\Log::debug('Quiz in carousel', [
                        'quiz_id' => $quiz->id ?? 'null',
                        'title' => $title,
                        'questions_count' => $questionsCount,
                        'sample_question' => $sampleQuestion ? (is_array($sampleQuestion) ? ($sampleQuestion['id'] ?? 'no_id') : $sampleQuestion->id) : 'none'
                    ]);}
                @endphp
                
                <div 
                    class="flex-shrink-0 w-full px-4 transition-all duration-300"
                    :class="{
                        'md:w-1/2 lg:w-1/3': visibleSlides >= 3,
                        'w-1/2': visibleSlides === 2,
                        'w-full': visibleSlides === 1
                    }">
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md overflow-hidden h-full flex flex-col border border-gray-200 dark:border-gray-700 hover:shadow-lg transition-shadow duration-300">
                        <div class="p-6 flex-1 flex flex-col">
                            <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-2 line-clamp-2" title="{{ is_array($title) ? ($title[$currentLocale] ?? $title['en'] ?? reset($title)) : $title }}">
                                {{ is_array($title) ? ($title[$currentLocale] ?? $title['en'] ?? reset($title)) : $title }}
                            </h3>
                            @if($sampleQuestion)
                                <div class="mt-4 pt-4 border-t border-gray-100 dark:border-gray-700">
                                    <p class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">
                                        {{ __('quiz.guestQuiz.tryThisSampleQuestion') }}
                                    </p>
                                    <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                                        <p class="text-gray-800 dark:text-gray-200 mb-3">
                                            @php
                                                $questionText = $sampleQuestion->text;
                                                if (is_array($questionText)) {
                                                    $questionText = $questionText[$currentLocale] ?? $questionText['en'] ?? array_values($questionText)[0] ?? '';
                                                }
                                                echo e($questionText);
                                            @endphp
                                        </p>
                                        
                                        @if($sampleQuestion->options->isNotEmpty())
                                            <ul class="space-y-2" x-data="{ answered: false, correctAnswerId: null }">
                                                @php
                                                    $correctOption = $sampleQuestion->options->firstWhere('is_correct', true);
                                                @endphp
                                                @foreach($sampleQuestion->options as $option)
                                                    @php
                                                        $optionText = $option->option_text;
                                                        if (is_array($optionText)) {
                                                            $optionText = $optionText[$currentLocale] ?? $optionText['en'] ?? array_values($optionText)[0] ?? '';
                                                        }
                                                    @endphp
                                                    <li 
                                                        @click="$dispatch('select-option', { optionId: {{ $option->id }}, isCorrect: {{ $option->is_correct ? 'true' : 'false' }} })"
                                                        :class="{
                                                            'cursor-pointer hover:bg-gray-100 dark:hover:bg-gray-700': !$data.answered,
                                                            'bg-green-100 dark:bg-green-900/30 border-green-300 dark:border-green-700': $data.answered && {{ $option->is_correct ? 'true' : 'false' }},
                                                            'bg-red-100 dark:bg-red-900/30 border-red-300 dark:border-red-700': $data.answered && $data.selectedOption === {{ $option->id }} && !{{ $option->is_correct ? 'true' : 'false' }},
                                                            'opacity-70': $data.answered && {{ !$option->is_correct ? 'true' : 'false' }}
                                                        }"
                                                        class="text-sm px-3 py-2 rounded border transition-colors duration-200 flex items-center"
                                                        :class="{
                                                            'border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-800': !$data.answered && !$data.selectedOption,
                                                            'pointer-events-none': $data.answered
                                                        }"
                                                        data-correct-answer="{{ $correctOption ? $correctOption->id : '' }}"
                                                        x-on:click="
                                                            if ($data.answered) return;
                                                            $data.answered = true;
                                                            $data.selectedOption = {{ $option->id }};
                                                            $data.correctAnswerId = {{ $correctOption ? $correctOption->id : 'null' }};
                                                            
                                                            // Emit event to parent for feedback
                                                            const isCorrect = {{ $option->is_correct ? 'true' : 'false' }};
                                                            $dispatch('answer-selected', { 
                                                                isCorrect: isCorrect,
                                                                optionId: {{ $option->id }}
                                                            });
                                                            
                                                            // Auto-hide feedback after delay
                                                            setTimeout(() => {
                                                                $data.answered = false;
                                                                $data.selectedOption = null;
                                                            }, 3000);
                                                        ">
                                                        <span class="flex-1">{{ $optionText }}</span>
                                                        <template x-if="$data.answered">
                                                            <span class="ml-2">
                                                                <template x-if="{{ $option->is_correct ? 'true' : 'false' }}">
                                                                    <span class="text-green-600 dark:text-green-400">✓</span>
                                                                </template>
                                                                <template x-if="!{{ $option->is_correct ? 'true' : 'false' }} && $data.selectedOption === {{ $option->id }}">
                                                                    <span class="text-red-600 dark:text-red-400">✗</span>
                                                                </template>
                                                            </span>
                                                        </template>
                                                    </li>
                                                @endforeach
                                            </ul>
                                            
                                            <!-- Feedback Section -->
                                            <div x-data="{
                                                showFeedback: false,
                                                isCorrect: false,
                                                feedbackMessage: '',
                                                feedbackEmoji: '',
                                                correctAnswer: null,
                                                
                                                init() {
                                                    this.$watch('showFeedback', value => {
                                                        if (value) {
                                                            setTimeout(() => {
                                                                this.showFeedback = false;
                                                            }, 3000);
                                                        }
                                                    });
                                                    
                                                    this.$on('answer-selected', ({ isCorrect, optionId }) => {
                                                        this.isCorrect = isCorrect;
                                                        this.showFeedback = true;
                                                        
                                                        if (isCorrect) {
                                                            this.feedbackMessage = '{{ getRandomEmoji('encouragement') }}';
                                                            this.feedbackEmoji = '{{ getRandomEmoji('correct') }}';
                                                        } else {
                                                            this.feedbackMessage = '{{ getRandomEmoji('wrong') }} Not quite right!';
                                                            this.feedbackEmoji = '😕';
                                                            this.correctAnswer = this.$el.closest('ul').querySelector('[data-correct-answer]')?.dataset.correctAnswer || null;
                                                        }
                                                        
                                                        // Scroll to show feedback
                                                        this.$el.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
                                                    });
                                                }
                                            }" x-show="showFeedback" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0" class="mt-4 p-3 rounded-lg text-center" :class="{
                                                'bg-green-50 dark:bg-green-900/20 text-green-800 dark:text-green-200': isCorrect,
                                                'bg-yellow-50 dark:bg-yellow-900/20 text-yellow-800 dark:text-yellow-200': !isCorrect
                                            }">
                                                <div class="flex items-center justify-center space-x-2">
                                                    <span class="text-2xl" x-text="feedbackEmoji"></span>
                                                    <span class="font-medium" x-text="feedbackMessage"></span>
                                                    <template x-if="!isCorrect && correctAnswer">
                                                        <span class="text-sm text-gray-700 dark:text-gray-300">
                                                            <span class="font-medium">{{ __('quiz.guestQuiz.correctAnswerIs') }}</span>
                                                            <span x-text="correctAnswer"></span>
                                                        </span>
                                                        <a href="#" @click.prevent="
                                                            const correctEl = document.querySelector(`[data-correct-answer='${correctAnswer}']`);
                                                            if (correctEl) {
                                                                correctEl.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
                                                                correctEl.classList.add('ring-2', 'ring-yellow-400', 'dark:ring-yellow-500');
                                                                setTimeout(() => {
                                                                    correctEl.classList.remove('ring-2', 'ring-yellow-400', 'dark:ring-yellow-500');
                                                                }, 2000);
                                                            }
                                                        " class="text-xs text-blue-600 dark:text-blue-400 hover:underline ml-2" title="Show correct answer">
                                                            {{ __('quiz.guestQuiz.showCorrectAnswer') }}
                                                        </a>
                                                    </template>
                                                </div>
                                                <div x-show="!isCorrect" class="mt-2 text-sm">
                                                    <button @click="
                                                        const ul = $el.closest('ul');
                                                        ul.querySelectorAll('li').forEach(li => {
                                                            li.style.pointerEvents = '';
                                                            li.classList.remove('opacity-70');
                                                            li.classList.remove('bg-red-100', 'dark:bg-red-900/30', 'border-red-300', 'dark:border-red-700');
                                                            li.classList.remove('bg-green-100', 'dark:bg-green-900/30', 'border-green-300', 'dark:border-green-700');
                                                        });
                                                        showFeedback = false;
                                                        $el.closest('[x-data]').__x.$data.answered = false;
                                                        $el.closest('[x-data]').__x.$data.selectedOption = null;
                                                    " class="text-xs text-blue-600 dark:text-blue-400 hover:underline">
                                                        {{ __('quiz.guestQuiz.tryAgain') }}
                                                    </button>
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            @endif
                            
                            <div class="mt-4 pt-4 border-t border-gray-100 dark:border-gray-700">
                                <div class="flex items-center justify-between mb-3">
                                    <span class="text-sm text-gray-500 dark:text-gray-400">
                                        {{ $questionsCount }} {{ trans_choice('question.questions', $questionsCount) }}
                                    </span>
                                    
                                    <span x-show="$data.answered && $data.isCorrect" class="text-xs px-2 py-1 rounded-full bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-200 flex items-center">
                                        <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                        </svg>
                                        {{ __('quiz.guestQuiz.correct') }}
                                    </span>
                                </div>
                                
                                <div class="flex items-center justify-between">
                                    <span class="text-xs text-gray-500 dark:text-gray-400">
                                        {{ __('quiz.guestQuiz.tryThisSampleQuestion') }}
                                    </span>
                                    
                                    <div class="flex space-x-2">
                                        <button x-show="$data.answered" @click="
                                            $data.answered = false;
                                            $data.selectedOption = null;
                                            document.querySelectorAll('[data-correct-answer]').forEach(el => {
                                                el.classList.remove('bg-green-100', 'dark:bg-green-900/30', 'border-green-300', 'dark:border-green-700');
                                                el.classList.remove('bg-red-100', 'dark:bg-red-900/30', 'border-red-300', 'dark:border-red-700');
                                            });
                                        " class="text-xs px-3 py-1.5 rounded-md bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-200 transition-colors">
                                            {{ __('quiz.guestQuiz.tryAgain') }}
                                        </button>
                                        
                                        <a href="{{ route('guest-quiz.show', ['locale' => $currentLocale, 'quiz' => is_array($quiz) ? ($quiz['id'] ?? '') : ($quiz->id ?? '')]) }}" 
                                           class="inline-flex items-center px-4 py-1.5 bg-blue-600 hover:bg-blue-700 text-white text-xs font-medium rounded-md transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                            {{ __('quiz.guestQuiz.startFullQuiz') }}
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 ml-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                                            </svg>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
    
    <!-- Pagination Dots -->
    @if($quizzes->count() > 1)
        <div class="flex justify-center mt-6 space-x-2">
            @foreach($quizzes as $index => $quiz)
                <button 
                    @click="goToSlide({{ $index }})"
                    class="w-2.5 h-2.5 rounded-full transition-colors duration-200 focus:outline-none"
                    :class="{ 'bg-blue-600': activeSlide === {{ $index }}, 'bg-gray-300 hover:bg-gray-400 dark:bg-gray-600 dark:hover:bg-gray-500': activeSlide !== {{ $index }} }"
                    :aria-label="{{ __('quiz.guestQuiz.goToSlide', ['number' => $index + 1]) }}">
                </button>
            @endforeach
        </div>
    @endif
</div>
@else
    <div class="text-center py-12 bg-gradient-to-br from-gray-50 to-gray-100 dark:from-gray-800 dark:to-gray-900 rounded-lg border border-dashed border-gray-200 dark:border-gray-700">
        <div class="w-16 h-16 mx-auto mb-4 rounded-full bg-blue-50 dark:bg-blue-900/30 flex items-center justify-center">
            <svg class="h-8 w-8 text-blue-500 dark:text-blue-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
        </div>
        <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">{{ __('quiz.guestQuiz.noQuizAvailable') }}</h3>
        <p class="text-gray-500 dark:text-gray-400 max-w-md mx-auto">
            {{ __('quiz.guestQuiz.noQuizzesAvailable') }}
        </p>
        <div class="mt-4 flex justify-center space-x-3">
            <button class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                <svg class="-ml-1 mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                </svg>
                {{ __('quiz.guestQuiz.exploreMore') }}
            </button>
        </div>
    </div>
@endif

<style>
    .line-clamp-2 {
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }
    
    .line-clamp-3 {
        display: -webkit-box;
        -webkit-line-clamp: 3;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }
    
    /* Hide scrollbar for Chrome, Safari and Opera */
    .hide-scrollbar::-webkit-scrollbar {
        display: none;
    }
    
    /* Hide scrollbar for IE, Edge and Firefox */
    .hide-scrollbar {
        -ms-overflow-style: none;  /* IE and Edge */
        scrollbar-width: none;  /* Firefox */
    }
    
    /* Smooth scrolling for the carousel */
    .carousel-container {
        scroll-behavior: smooth;
        -webkit-overflow-scrolling: touch;
    }
    
    /* Custom scrollbar for the carousel */
    .carousel-container::-webkit-scrollbar {
        height: 6px;
    }
    
    .carousel-container::-webkit-scrollbar-track {
        background: #f1f1f1;
        border-radius: 10px;
    }
    
    .carousel-container::-webkit-scrollbar-thumb {
        background: #888;
        border-radius: 10px;
    }
    
    .carousel-container::-webkit-scrollbar-thumb:hover {
        background: #555;
    }
    
    /* Dark mode styles */
    .dark .carousel-container::-webkit-scrollbar-track {
        background: #374151;
    }
    
    .dark .carousel-container::-webkit-scrollbar-thumb {
        background: #6b7280;
    }
    
    .dark .carousel-container::-webkit-scrollbar-thumb:hover {
        background: #9ca3af;
    }
</style>