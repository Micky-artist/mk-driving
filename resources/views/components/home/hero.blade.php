<div class="w-full flex flex-col lg:flex-row gap-4 sm:gap-6">
    <div class="w-full lg:w-1/2">
        <div class="w-full aspect-[4/3] sm:aspect-[16/12] relative">
            <div class="absolute right-0 bottom-0 h-24 w-24 sm:h-32 sm:w-32 translate-y-[60%] translate-x-[60%]">
                <img src="{{ asset('images/grid-dots.svg') }}" alt="grid" class="h-full w-full">
            </div>
            <div class="w-full h-full aspect-[4/3] sm:aspect-[16/12] relative rounded-2xl sm:rounded-3xl overflow-hidden">
                <img
                    src="{{ asset('images/inside-car.png') }}"
                    alt="inside car image"
                    class="w-full h-full object-cover"
                    draggable="false"
                />
            </div>
            <div class="absolute bottom-2 left-2 max-w-40 sm:max-w-48 md:max-w-52 w-fit rounded-xl sm:rounded-2xl border-2 border-white p-2 sm:p-3 bg-white/40 backdrop-blur-xl text-white">
                <p class="font-bold text-xs sm:text-sm md:text-base">{{ __('hero.get_started') }}</p>
                <p class="text-xs sm:text-sm">
                    {{ __('hero.start_test_description') }}
                </p>
            </div>
            
            @if($guestQuiz ?? false)
                <a
                    href="{{ route('guest-quiz.show', ['locale' => app()->getLocale(), 'quiz' => $guestQuiz->id]) }}"
                    class="absolute bottom-2 right-2 max-w-40 sm:max-w-48 md:max-w-52 w-fit rounded-full border-2 border-white p-2 sm:p-3 bg-white/40 hover:bg-white/50 hover:scale-105 backdrop-blur-xl text-[#023047]"
                >
                    <p class="font-bold text-xs sm:text-sm md:text-base">{{ __('hero.start_test') }}</p>
                </a>
            @else
                <div class="absolute bottom-2 right-2 max-w-40 sm:max-w-48 md:max-w-52 w-fit rounded-full border-2 border-white p-2 sm:p-3 bg-white/40 backdrop-blur-xl text-[#023047]">
                    <p class="font-bold text-xs sm:text-sm">{{ __('hero.no_guest_quiz') }}</p>
                </div>
            @endif
        </div>
    </div>
    <div class="w-full lg:w-1/2 z-10 text-[#023047]">
        <div class="flex flex-col gap-2 sm:gap-3 md:gap-4 ml-auto max-w-lg text-center lg:text-left">
            <p class="text-sm sm:text-base md:text-lg font-medium">{{ __('hero.be_our_guest') }}</p>
            <p class="font-bold text-xl sm:text-2xl md:text-3xl lg:text-4xl leading-tight">
                {{ __('hero.title') }}
            </p>
            <p class="text-xs sm:text-sm md:text-base text-gray-600 leading-relaxed">
                {{ __('hero.subtitle') }}
            </p>
            <div class="mt-4 sm:mt-6">
                <a
                    href="{{ route('login', app()->getLocale()) }}"
                    class="bg-[#023047] hover:bg-[#023047]/90 w-fit mx-auto lg:mx-0 rounded-full text-sm sm:text-base px-6 py-2 sm:px-8 sm:py-3 text-white font-medium inline-block"
                >
                    {{ __('hero.login_button') }}
                </a>
            </div>
        </div>
    </div>
</div>
