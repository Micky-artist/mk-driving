<footer class="bg-gradient-to-b from-blue-900 to-blue-950 text-white mt-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <!-- Main Footer Content -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-8 mb-8">
            <!-- About Section -->
            <div class="col-span-1 md:col-span-2">
                <div class="flex items-center mb-4">
                    <img class="h-10 w-auto" src="{{ asset('logo.png') }}" alt="MK DRIVING ACADEMY Logo">
                    <span class="ml-3 text-xl font-bold bg-gradient-to-r from-orange-400 to-orange-300 bg-clip-text text-transparent">
                        MK DRIVING ACADEMY
                    </span>
                </div>
                <p class="text-blue-100 text-sm leading-relaxed">
                    {{ __('home.footer.tagline') }}
                </p>
                <div class="mt-4 flex space-x-2">
                    <!-- WhatsApp Button -->
                    <button onclick="window.open('https://wa.me/250798611161', '_blank')" class="bg-green-500 hover:bg-green-600 text-white rounded-full w-10 h-10 flex items-center justify-center transition-all duration-300 transform hover:scale-110">
                        <i class="fab fa-whatsapp text-sm"></i>
                    </button>
                    
                    <!-- Instagram Button -->
                    <button onclick="window.open('https://instagram.com/mkdrivingschool_', '_blank')" class="bg-gradient-to-r from-pink-500 via-purple-500 to-yellow-500 hover:opacity-90 rounded-full w-10 h-10 flex items-center justify-center transition-all duration-300 transform hover:scale-110">
                        <i class="fab fa-instagram text-sm text-white"></i>
                    </button>
                    <a href="tel:250798611161" class="flex items-center text-blue-300 hover:text-white transition-colors">
                        <svg class="h-5 w-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                        </svg>
                        <span class="text-sm">+250 798 611 161</span>
                    </a>
                </div>
            </div>

            <!-- Quick Links -->
            <div>
                <h3 class="text-sm font-semibold text-orange-400 uppercase tracking-wider mb-4">{{ __('home.footer.quick_links') }}</h3>
                <ul class="space-y-2">
                    <li><a href="{{ route('home') }}" class="text-blue-100 hover:text-white transition-colors text-sm">{{ __('navigation.home') }}</a></li>
                    <li><a href="{{ route('quizzes') }}" class="text-blue-100 hover:text-white transition-colors text-sm">{{ __('navigation.quizzes') }}</a></li>
                    <li><a href="#" class="text-blue-100 hover:text-white transition-colors text-sm">{{ __('home.footer.news') }}</a></li>
                    <li><a href="#" class="text-blue-100 hover:text-white transition-colors text-sm">{{ __('home.footer.forum') }}</a></li>
                </ul>
            </div>

            <!-- Legal -->
            <div>
                <h3 class="text-sm font-semibold text-orange-400 uppercase tracking-wider mb-4">{{ __('home.footer.legal') }}</h3>
                <ul class="space-y-2">
                    <li><a href="#" class="text-blue-100 hover:text-white transition-colors text-sm">{{ __('home.footer.terms_of_service') }}</a></li>
                    <li><a href="#" class="text-blue-100 hover:text-white transition-colors text-sm">{{ __('home.footer.privacy_policy') }}</a></li>
                    <li><a href="#" class="text-blue-100 hover:text-white transition-colors text-sm">{{ __('home.footer.cookie_policy') }}</a></li>
                    <li><a href="#" class="text-blue-100 hover:text-white transition-colors text-sm">{{ __('home.footer.refund_policy') }}</a></li>
                </ul>
            </div>
        </div>

        <!-- Bottom Bar -->
        <div class="border-t border-blue-800 pt-8">
            <div class="flex flex-col md:flex-row justify-between items-center">
                <p class="text-blue-300 text-sm">
                    &copy; {{ date('Y') }} MK DRIVING ACADEMY. {{ __('home.footer.all_rights_reserved') }}
                </p>
                <div class="mt-4 md:mt-0 flex space-x-2">
                    <a href="#" class="text-blue-300 hover:text-white text-sm">{{ __('home.footer.contact') }}</a>
                    <span class="text-blue-700">|</span>
                    <a href="#" class="text-blue-300 hover:text-white text-sm">{{ __('home.footer.terms_of_service') }}</a>
                    <span class="text-blue-700">|</span>
                    <a href="#" class="text-blue-300 hover:text-white text-sm">{{ __('home.footer.privacy_policy') }}</a>
                </div>
            </div>
        </div>
    </div>
</footer>
