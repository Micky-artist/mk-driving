@php
    // Get the current locale
    $locale = app()->getLocale();

    // Get the first question and its options
    $question = $guestQuiz['questions'][0] ?? null;
    $options = $question ? $question->options : [];

    // If no question is provided, show a default message
    if (!$question) {
        $questionText = __('quiz.guestQuiz.testYourKnowledge');
        $options = [];
    } else {
        // Get the localized question text
        $questionText = $question->getTranslation('question_text', $locale) ?? $question->question_text;

        // Ensure options are properly localized
        $options = $options
            ->map(function ($option) use ($locale) {
                return [
                    'id' => $option->id,
                    'text' => $option->getTranslation('option_text', $locale) ?? $option->option_text,
                    'is_correct' => $option->is_correct,
                ];
            })
            ->toArray();
    }

    // Plan colors mapping
    $planColors = [
        'basic' => 'from-blue-500 to-blue-600',
        'standard' => 'from-purple-500 to-pink-500',
        'premium' => 'from-orange-500 to-amber-500',
        'gold' => 'from-yellow-500 to-amber-500',
        'platinum' => 'from-gray-400 to-gray-600',
        'enterprise' => 'from-green-500 to-teal-500',
        'pro' => 'from-indigo-500 to-purple-600',
        'business' => 'from-red-500 to-pink-500',
        'starter' => 'from-green-400 to-cyan-500',
        'ultimate' => 'from-purple-700 to-indigo-700',
    ];

    // Check if this is a guest quiz
    $isGuestQuiz = $guestQuiz['is_guest_quiz'] ?? false;

    // Set plan information based on quiz type
    if ($isGuestQuiz) {
        $planName = __('quiz.guestQuiz.free');
        $planSlug = 'free';
        $colorClass = 'from-green-500 to-teal-500';
    } else {
        $planName = $guestQuiz['subscription_plan']['name'] ?? __('quiz.guestQuiz.premium');
        $planSlug = $guestQuiz['subscription_plan']['slug'] ?? 'premium';
        $colorClass = $planColors[$planSlug] ?? 'from-orange-500 to-amber-500';
    }
@endphp

<div x-data="{
    selected: null,
    showAnswer: false,
    isCorrect: false,
    showContinue: false,
    isLocked: {{ $isGuestQuiz ? 'false' : 'true' }},

    selectAnswer(optionId) {
        if (this.showAnswer) return;

        this.selected = optionId;
        this.showAnswer = true;
        this.isCorrect = {{ json_encode($options) }}.find(opt => opt.id === optionId)?.is_correct || false;
    },

    getFeedbackText() {
    if (!this.showAnswer) return '';
    const correctOption = {{ json_encode($options) }}.find(opt => opt.is_correct);
    return this.isCorrect ?
        `{{ __('quiz.correctAnswerFeedback') }}` :
        `{{ __('quiz.incorrectAnswerFeedback') }}{{ ' ' }}` + (correctOption?.text || '');
},

    getOptionClasses(option) {
        if (!this.showAnswer) {
            return this.selected === option.id ?
                'border-blue-500 bg-blue-50 dark:bg-blue-900/20' :
                'border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700';
        }

        if (option.is_correct) {
            return 'border-green-500 bg-green-50 dark:bg-green-900/20';
        }

        if (this.selected === option.id && !this.isCorrect) {
            return 'border-red-500 bg-red-50 dark:bg-red-900/20';
        }

        return 'border-gray-200 dark:border-gray-700';
    },

    getCheckboxClasses(option) {
        if (!this.showAnswer) {
            return this.selected === option.id ?
                'border-blue-500 bg-blue-500' :
                'border-gray-300 dark:border-gray-500';
        }

        if (option.is_correct) {
            return 'border-green-500 bg-green-500';
        }

        if (this.selected === option.id && !this.isCorrect) {
            return 'border-red-500';
        }

        return 'border-gray-300 dark:border-gray-500';
    },

    shouldShowCheck(option) {
        if (this.showAnswer) {
            return option.is_correct || (this.selected === option.id && this.isCorrect);
        }
        return this.selected === option.id;
    }
}" class="relative">
    <!-- Plan Badge -->
    <div
        class="absolute top-3 right-3 bg-gradient-to-r {{ $colorClass }} text-white text-xs font-bold px-3 py-1 rounded-full shadow-md z-10">
        {{ strtoupper($planName) }}
    </div>

    @if (!$isGuestQuiz)
        <!-- Locked Overlay for Premium Quizzes -->
        <div x-show="isLocked" x-transition:enter="transition-opacity duration-300" x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            class="absolute inset-0 bg-gradient-to-br from-blue-900/90 to-blue-700/90 rounded-xl z-20 flex flex-col items-center justify-center p-6 text-center text-white backdrop-blur-sm"
            style="top: 40%;">
            <div class="bg-blue-600/30 rounded-full p-4 mb-4">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                        d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                </svg>
            </div>
            <h3 class="text-xl font-bold mb-2">
                {{ __('home.guestQuiz.quiz_locked', ['planName' => $planName]) }}
            </h3>
            <p class="text-blue-100 mb-6">
                {{ __('home.guestQuiz.subscribe_to_unlock', ['planName' => $planName]) }}
            </p>
            <a href="{{ route('register', app()->getLocale()) }}"
                class="bg-orange-500 hover:bg-orange-600 text-white font-bold py-3 px-6 rounded-lg shadow-lg hover:shadow-xl transition-all duration-300 transform hover:scale-105">
                {{ __('home.guestQuiz.sign_up_to_unlock') }}
            </a>
        </div>
    @endif

    <!-- Question Header -->
    <div class="bg-gradient-to-r from-blue-600 to-blue-700 px-6 py-4">
        <h3 class="text-xl font-bold text-white">
            {{ __('home.guestQuiz.practice_here') }}
        </h3>
    </div>

    @if ($question)
        <div class="pt-2">
            <!-- Question -->
            <!-- Question -->
            <p class="text-gray-700 dark:text-gray-200 font-medium mb-4 leading-tight">
                {!! $question->getTranslation('text', app()->getLocale()) ??
                    ($question->text['en'] ?? 'Question text not available') !!}
            </p>

            <!-- Feedback Message -->
            <div x-show="showAnswer" x-transition class="mb-4 p-3 rounded-lg"
                :class="isCorrect ? 'bg-green-50 text-green-800 dark:bg-green-900/30 dark:text-green-200' :
                    'bg-red-50 text-red-800 dark:bg-red-900/30 dark:text-red-200'">
                <p x-html="getFeedbackText()"></p>
            </div>

            <!-- Answer Grid -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 mb-5">
                @foreach ($options as $option)
                    <div class="mb-3">
                        <label
                            class="flex items-center p-3 border rounded-lg cursor-pointer transition-colors duration-200"
                            :class="getOptionClasses({{ json_encode($option) }})"
                            @click="selectAnswer({{ $option['id'] }})">
                            <div class="flex items-center h-5">
                                <input type="radio" name="answer" :value="{{ $option['id'] }}"
                                    class="h-4 w-4 border-gray-300 focus:ring-2 focus:ring-blue-500"
                                    :class="getCheckboxClasses({{ json_encode($option) }})"
                                    :checked="selected === {{ $option['id'] }}" :disabled="showAnswer">
                            </div>
                            <div class="ms-3 text-sm">
                                <p class="font-medium text-gray-700 dark:text-gray-200">
                                    {{ $option['text'] }}
                                </p>
                            </div>
                        </label>
                    </div>
                @endforeach
            </div>

            <!-- CTA Button -->
            <div class="text-center" x-init="console.log('CTA section mounted', { showAnswer: $data.showAnswer })">
                <template x-if="!showAnswer" x-init="console.log('Initial CTA visible')">
                    @auth
                        <a href="/{{ app()->getLocale() }}/dashboard/quizzes/11"
                            class="inline-flex items-center justify-center w-full bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white font-medium py-3 px-6 rounded-lg shadow-md hover:shadow-lg transition-all duration-300 transform hover:-translate-y-0.5 relative z-10">
                            {{ __('quiz.guestQuiz.startFullQuiz') }}
                            <svg class="w-4 h-4 ml-2 -mr-1" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                    d="M10.293 5.293a1 1 0 011.414 0l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414-1.414L12.586 11H5a1 1 0 110-2h7.586l-2.293-2.293a1 1 0 010-1.414z"
                                    clip-rule="evenodd" />
                            </svg>
                        </a>
                    @else
                        <a href="{{ route('guest-quiz.show', ['locale' => app()->getLocale(), 'quiz' => $guestQuiz['id']]) }}"
                            class="inline-flex items-center justify-center w-full bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white font-medium py-3 px-6 rounded-lg shadow-md hover:shadow-lg transition-all duration-300 transform hover:-translate-y-0.5 relative z-10">
                            {{ __('quiz.guestQuiz.startFullQuiz') }}
                            <svg class="w-4 h-4 ml-2 -mr-1" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                    d="M10.293 5.293a1 1 0 011.414 0l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414-1.414L12.586 11H5a1 1 0 110-2h7.586l-2.293-2.293a1 1 0 010-1.414z"
                                    clip-rule="evenodd" />
                            </svg>
                        </a>
                    @endauth
                </template>

                <template x-if="showAnswer" x-init="console.log('Post-answer UI visible', { isCorrect: $data.isCorrect })">
                    <div class="space-y-2">
                        <div x-show="isCorrect" class="animate-bounce">
                            <p class="text-green-600 dark:text-green-400 font-medium mb-2 text-center">
                                🎉 {{ __('home.guestQuiz.great_job') }}
                            </p>
                        </div>

                        @auth
                            <a href="/{{ app()->getLocale() }}/dashboard/quizzes/11"
                                class="inline-flex items-center justify-center w-full bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white font-medium py-3 px-6 rounded-lg shadow-lg hover:shadow-xl transition-all duration-300 transform hover:scale-105">
                                <span>{{ __('home.guestQuiz.challenge_yourself') }}</span>
                                <svg class="w-5 h-5 ml-2 animate-pulse" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M13 7l5 5m0 0l-5 5m5-5H6"></path>
                                </svg>
                            </a>
                        @else
                            <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-4 mb-4">
                                <p class="text-green-600 dark:text-green-400 font-medium mb-4 text-center">
                                    {{ __('home.guestQuiz.challenge_yourself') }}
                                </p>
                                <a href="{{ route('guest-quiz.show', ['locale' => app()->getLocale(), 'quiz' => $guestQuiz['id']]) }}"
                                   class="w-full inline-flex items-center justify-center bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white font-medium py-2 px-6 rounded-lg shadow-md hover:shadow-lg transition-all duration-300 transform hover:-translate-y-0.5">
                                    {{ __('home.guestQuiz.startQuiz') }}
                                    <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path>
                                    </svg>
                                </a>
                            </div>
                        @endauth
                    </div>
                </template>

                <p class="mt-3 text-xs text-gray-500 dark:text-gray-400 relative z-10">
                    <span x-show="!showAnswer">
                        {{ trans_choice('home.guestQuiz.challenging_questions', $guestQuiz['question_count'] ?? 0, ['count' => $guestQuiz['question_count'] ?? 0]) }}
                        • {{ __('home.guestQuiz.no_registration') }}
                 <span x-show="showAnswer" class="text-blue-500 font-medium">
                        {{ __('home.guestQuiz.unlock_questions') }}
                    </span>
                </p>
            </div>
        </div>
    @else
        <div class="p-0 text-center text-blue-100">
            {{ __('quiz.guestQuiz.noQuizAvailable') }}
        </div>
    @endif
</div>
