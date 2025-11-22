<div class="my-16">
    <div class="text-center mb-10">
        <h2 class="text-2xl sm:text-3xl font-bold text-gray-900 mb-4">{{ __('home.offers.title') }}</h2>
        <p class="text-gray-600 max-w-3xl mx-auto">
            {{ __('home.offers.subtitle') }}
        </p>
    </div>

    <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-6">
        <div class="bg-white p-6 rounded-xl shadow-sm hover:shadow-md transition-shadow">
            <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center mb-4">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
            <h3 class="text-lg font-semibold mb-2">{{ __('home.offers.features.expertInstructors') }}</h3>
            <p class="text-gray-600 text-sm">
                {{ __('home.offers.features.expertInstructorsDesc', ['default' => 'Learn from experienced driving instructors with our expert tips.']) }}
            </p>
        </div>

        <div class="bg-white p-6 rounded-xl shadow-sm hover:shadow-md transition-shadow">
            <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center mb-4">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11a7 7 0 01-7 7m0 0a7 7 0 01-7-7m7 7v4m0 0H8m4 0h4m-6-18a3 3 0 01-3 3H3v2h3a3 3 0 013 3v2h2V7z" />
                </svg>
            </div>
            <h3 class="text-lg font-semibold mb-2">{{ __('home.offers.features.flexibleScheduling') }}</h3>
            <p class="text-gray-600 text-sm">
                {{ __('home.offers.features.flexibleSchedulingDesc', ['default' => 'Flexible scheduling to fit your busy lifestyle.']) }}
            </p>
        </div>

        <div class="bg-white p-6 rounded-xl shadow-sm hover:shadow-md transition-shadow">
            <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center mb-4">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-purple-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                </svg>
            </div>
            <h3 class="text-lg font-semibold mb-2">{{ __('home.offers.features.practiceTests') }}</h3>
            <p class="text-gray-600 text-sm">
                {{ __('home.offers.features.practiceTestsDesc', ['default' => 'Learn to drive with our fleet of modern, well-maintained vehicles.']) }}
            </p>
        </div>

        <div class="bg-white p-6 rounded-xl shadow-sm hover:shadow-md transition-shadow">
            <div class="w-12 h-12 bg-orange-100 rounded-lg flex items-center justify-center mb-4">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-orange-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
            <h3 class="text-lg font-semibold mb-2">{{ __('home.offers.features.progressTracking') }}</h3>
            <p class="text-gray-600 text-sm">
                {{ __('home.offers.features.progressTrackingDesc', ['default' => 'Comprehensive preparation to help you pass your road test with confidence.']) }}
            </p>
        </div>
    </div>
</div>
