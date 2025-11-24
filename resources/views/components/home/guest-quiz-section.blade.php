<div class="bg-white dark:bg-gray-800/50 backdrop-blur-sm rounded-xl p-6 shadow-md transition-colors duration-200 border border-gray-100 dark:border-gray-700/50">
    <div class="text-center">
        <h3 class="text-lg font-semibold mb-4 text-gray-900 dark:text-white">{{ __('hero.test_your_knowledge') }}</h3>
        <p class="text-sm text-gray-600 dark:text-gray-300 mb-6">
            {{ __('hero.guest_quiz_description') }}
        </p>
        @if($guestQuiz ?? false)
            <a 
                href="{{ route('guest-quiz.show', ['locale' => app()->getLocale(), 'quiz' => $guestQuiz->id]) }}"
                class="inline-block bg-[#023047] hover:bg-[#023047]/90 dark:bg-blue-600 dark:hover:bg-blue-700 text-white font-medium py-2 px-6 rounded-full transition-colors duration-200"
            >
                {{ __('hero.start_quiz') }}
            </a>
        @else
            <button 
                disabled
                class="bg-gray-300 dark:bg-gray-700 text-gray-500 dark:text-gray-400 font-medium py-2 px-6 rounded-full cursor-not-allowed transition-colors duration-200"
            >
                {{ __('hero.quiz_not_available') }}
            </button>
        @endif
    </div>
</div>
