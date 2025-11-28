<footer class="bg-blue-900 text-white pt-12 pb-8 mt-4">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            <!-- Company Info -->
            <div class="md:col-span-2">
                <!-- Social Media Icons -->
                <div class="flex space-x-5 mb-4">
                    <!-- WhatsApp Button -->
                    <a href="https://wa.me/250798611161" target="_blank" class="bg-green-500 hover:bg-green-600 text-white rounded-full p-4 transition-all duration-300 transform hover:scale-110 hover:shadow-lg">
                        <i class="fab fa-whatsapp text-2xl w-8 h-8 flex items-center justify-center"></i>
                    </a>
                    
                    <!-- Instagram Button -->
                    <a href="https://instagram.com/mkdrivingschool_" target="_blank" class="bg-gradient-to-r from-pink-500 via-purple-500 to-yellow-500 hover:opacity-90 rounded-full p-4 transition-all duration-300 transform hover:scale-110 hover:shadow-lg">
                        <i class="fab fa-instagram text-2xl w-8 h-8 flex items-center justify-center text-white"></i>
                    </a>
                    
                    <!-- Email Button -->
                    <a href="mailto:mkscholars250@gmail.com" class="bg-blue-500 hover:bg-blue-600 text-white rounded-full p-4 transition-all duration-300 transform hover:scale-110 hover:shadow-lg">
                        <i class="fas fa-envelope text-2xl w-8 h-8 flex items-center justify-center"></i>
                    </a>
                </div>
                <h3 class="text-2xl font-bold mb-4">{{ config('app.name') }}</h3>
                <p class="text-gray-300 text-sm leading-relaxed max-w-md">
                    {{ __('forum.footer.tagline', ['app' => config('app.name')]) }}
                </p>
            </div>

            <!-- Quick Links -->
            <div>
                <h3 class="text-sm font-semibold text-gray-200 uppercase tracking-wider">{{ __('forum.footer.quick_links') }}</h3>
                <ul class="mt-4 space-y-2">
                    <li>
                        <a href="{{ route('home', ['locale' => app()->getLocale()]) }}" class="text-gray-400 hover:text-white text-sm transition-colors">
                            {{ __('navigation.home') }}
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('forum.index', ['locale' => app()->getLocale()]) }}" class="text-gray-400 hover:text-white text-sm transition-colors">
                            {{ __('forum.page_title') }}
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('home', ['locale' => app()->getLocale(), '#' => 'pricing']) }}" class="text-gray-400 hover:text-white text-sm transition-colors">
                            {{ __('navigation.pricing_plans') }}
                        </a>
                    </li>
                    <li>
                        <a href="#" class="text-gray-400 hover:text-white text-sm transition-colors">
                            {{ __('navigation.about_us') }}
                        </a>
                    </li>
                    <li>
                        <a href="#contact" class="text-gray-400 hover:text-white text-sm transition-colors">
                            {{ __('forum.footer.contact_us') }}
                        </a>
                    </li>
                </ul>
            </div>

            <!-- Contact Info -->
            <div>
                <h3 class="text-sm font-semibold text-gray-200 uppercase tracking-wider">{{ __('forum.footer.contact_us') }}</h3>
                <ul class="mt-4 space-y-3">
                    <li class="flex items-start">
                        <svg class="h-5 w-5 text-gray-400 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                        </svg>
                        <a href="mailto:mkscholars250@gmail.com" class="text-gray-300 hover:text-white text-sm transition-colors">mkscholars250@gmail.com</a>
                    </li>
                    <li class="flex items-start">
                        <svg class="h-5 w-5 text-gray-400 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                        </svg>
                        <a href="tel:+250798611161" class="text-gray-300 hover:text-white text-sm transition-colors">+250 798 611 161</a>
                    </li>
                    <li class="flex items-start">
                        <svg class="h-5 w-5 text-gray-400 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                        <span class="text-gray-300 text-sm">Kigali, Rwanda</span>
                    </li>
                </ul>
            </div>
        </div>

        <!-- Copyright -->
        <div class="mt-12 border-t border-gray-800 pt-8 flex flex-col md:flex-row justify-between items-center">
            <p class="text-gray-300 text-sm text-center md:text-left">
                &copy; {{ date('Y') }} {{ config('app.name') }}. {{ __('forum.footer.all_rights_reserved') }}
            </p>
            <div class="mt-4 md:mt-0 flex space-x-6">
                <a href="#" class="text-gray-300 hover:text-white text-sm">
                    {{ __('forum.footer.privacy_policy') }}
                </a>
                <a href="#" class="text-gray-300 hover:text-white text-sm">
                    {{ __('forum.footer.terms_of_service') }}
                </a>
                <a href="#" class="text-gray-400 hover:text-white text-sm">
                    {{ __('forum.footer.cookie_policy') }}
                </a>
            </div>
        </div>
    </div>
</footer>
