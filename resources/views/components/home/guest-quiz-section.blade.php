<div class="bg-white rounded-xl p-6 shadow-md">
    <div class="text-center">
        <h3 class="text-lg font-semibold mb-4">{{ __('hero.test_your_knowledge') }}</h3>
        <p class="text-sm text-gray-600 mb-6">
            {{ __('hero.guest_quiz_description') }}
        </p>
        @if($guestQuiz ?? false)
            <a 
                href="{{ route('guest-quiz.show', ['locale' => app()->getLocale(), 'quiz' => $guestQuiz->id]) }}"
                class="inline-block bg-[#023047] hover:bg-[#023047]/90 text-white font-medium py-2 px-6 rounded-full transition-colors"
            >
                {{ __('hero.start_quiz') }}
            </a>
        @else
            <button 
                disabled
                class="bg-gray-300 text-gray-500 font-medium py-2 px-6 rounded-full cursor-not-allowed"
            >
                {{ __('hero.quiz_not_available') }}
            </button>
        @endif
    </div>
</div>
