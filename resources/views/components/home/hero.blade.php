<div class="w-full flex flex-col lg:flex-row gap-4 sm:gap-6">
    <!-- Image - Hidden on mobile, shown on lg screens and up -->
    <div class="hidden lg:block lg:w-1/2">
        <div class="w-full aspect-[4/3] sm:aspect-[16/12] relative fade-in">
            <div class="w-full h-full aspect-[4/3] sm:aspect-[16/12] relative rounded-2xl sm:rounded-3xl overflow-hidden">
                <img
                    src="{{ asset('images/inside-car.png') }}"
                    alt="inside car image"
                    class="w-full h-full object-cover transition-transform duration-500 hover:scale-105"
                    draggable="false"
                />
            </div>
            <div class="absolute bottom-2 left-2 max-w-40 sm:max-w-48 md:max-w-52 w-fit rounded-xl sm:rounded-2xl border-2 border-white p-2 sm:p-3 bg-white/40 backdrop-blur-xl text-white fade-in delay-100">
                <p class="font-bold text-xs sm:text-sm md:text-base dark:text-gray-900">{{ __('hero.get_started') }}</p>
                <p class="text-xs sm:text-sm dark:text-gray-900">
                    {{ __('hero.start_test_description') }}
                </p>
            </div>
            
            @if($guestQuiz ?? false)
                <a
                    href="{{ route('guest-quiz.show', ['locale' => app()->getLocale(), 'quiz' => $guestQuiz->id]) }}"
                    class="absolute bottom-2 right-2 max-w-40 sm:max-w-48 md:max-w-52 w-fit rounded-full border-2 border-white p-2 sm:p-3 bg-white/40 hover:bg-white/50 hover:scale-105 backdrop-blur-xl text-[#023047] dark:text-gray-900 transition-all duration-300 transform fade-in delay-200"
                >
                    <p class="font-bold text-xs sm:text-sm md:text-base">{{ __('hero.start_test') }}</p>
                </a>
            @else
                <div class="absolute bottom-2 right-2 max-w-40 sm:max-w-48 md:max-w-52 w-fit rounded-full border-2 border-white p-2 sm:p-3 bg-white/40 backdrop-blur-xl text-[#023047] dark:text-gray-900 fade-in delay-200">
                    <p class="font-bold text-xs sm:text-sm">{{ __('hero.no_guest_quiz') }}</p>
                </div>
            @endif
        </div>
    </div>
    <!-- Text Content - Full width on mobile, half on desktop -->
    <div class="w-full lg:w-1/2 z-10 text-[#023047] dark:text-white">
        <div class="flex flex-col gap-3 sm:gap-4 md:gap-5 mx-auto lg:ml-auto max-w-2xl lg:max-w-none px-4 sm:px-6 lg:px-0 text-center lg:text-left">
            <p class="text-base sm:text-lg md:text-xl font-medium fade-in text-[#023047] dark:text-blue-100">{{ __('hero.be_our_guest') }}</p>
            <h1 class="font-bold text-3xl sm:text-4xl md:text-5xl lg:text-6xl leading-tight fade-in delay-100">
                {{ __('hero.title') }}
            </h1>
            <p class="text-sm sm:text-base md:text-lg text-gray-700 dark:text-gray-300 leading-relaxed fade-in delay-200 mt-2">
                {{ __('hero.subtitle') }}
                <span class="font-semibold text-[#FF7B00] dark:text-orange-400">{{ __('hero.upsell_benefit') }}</span>
            </p>
            <div class="mt-6 sm:mt-8 fade-in delay-300">
                <a
                    href="{{ route('login', app()->getLocale()) }}"
                    class="bg-[#023047] hover:bg-[#023047]/90 w-full sm:w-fit mx-auto lg:mx-0 rounded-full text-base sm:text-lg px-8 py-3 sm:px-10 sm:py-3.5 text-white font-medium inline-block transition-all duration-300 transform hover:scale-105 shadow-md hover:shadow-lg"
                >
                    {{ __('hero.login_button') }}
                </a>
            </div>
        </div>
    </div>
    <!-- Mobile Image - Only shown on mobile and tablet, hidden on desktop (lg and up) -->
    <div class="block lg:hidden w-full mt-6">
        <div class="w-full aspect-[4/3] sm:aspect-[16/12] relative fade-in">
            <div class="w-full h-full aspect-[4/3] sm:aspect-[16/12] relative rounded-2xl sm:rounded-3xl overflow-hidden">
                <img
                    src="{{ asset('images/inside-car.png') }}"
                    alt="inside car image"
                    class="w-full h-full object-cover transition-transform duration-500 hover:scale-105"
                    draggable="false"
                />
            </div>
            <div class="absolute bottom-2 left-2 max-w-40 sm:max-w-48 md:max-w-52 w-fit rounded-xl sm:rounded-2xl border-2 border-white p-2 sm:p-3 bg-white/40 backdrop-blur-xl text-white fade-in delay-100">
                <p class="font-bold text-xs sm:text-sm md:text-base dark:text-gray-900">{{ __('hero.get_started') }}</p>
                <p class="text-xs sm:text-sm dark:text-gray-900">
                    {{ __('hero.start_test_description') }}
                </p>
            </div>
            
            @if($guestQuiz ?? false)
                <a
                    href="{{ route('guest-quiz.show', ['locale' => app()->getLocale(), 'quiz' => $guestQuiz->id]) }}"
                    class="absolute bottom-2 right-2 max-w-40 sm:max-w-48 md:max-w-52 w-fit rounded-full border-2 border-white p-2 sm:p-3 bg-white/40 hover:bg-white/50 hover:scale-105 backdrop-blur-xl text-[#023047] dark:text-gray-900 transition-all duration-300 transform fade-in delay-200"
                >
                    <p class="font-bold text-xs sm:text-sm md:text-base">{{ __('hero.start_test') }}</p>
                </a>
            @else
                <div class="absolute bottom-2 right-2 max-w-40 sm:max-w-48 md:max-w-52 w-fit rounded-full border-2 border-white p-2 sm:p-3 bg-white/40 backdrop-blur-xl text-[#023047] dark:text-gray-900 fade-in delay-200">
                    <p class="font-bold text-xs sm:text-sm">{{ __('hero.no_guest_quiz') }}</p>
                </div>
            @endif
        </div>
    </div>
</div>
