<footer class="bg-gradient-to-b from-blue-900 to-blue-950 text-white">
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
                <div class="mt-4 flex space-x-4">
                    <a href="#" class="text-blue-300 hover:text-white transition-colors">
                        <span class="sr-only">Facebook</span>
                        <svg class="h-6 w-6" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path fill-rule="evenodd" d="M22 12c0-5.523-4.477-10-10-10S2 6.477 2 12c0 4.991 3.657 9.128 8.438 9.878v-6.987h-2.54V12h2.54V9.797c0-2.506 1.492-3.89 3.777-3.89 1.094 0 2.238.195 2.238.195v2.46h-1.26c-1.243 0-1.63.771-1.63 1.562V12h2.773l-.443 2.89h-2.33v6.988C18.343 21.128 22 16.991 22 12z" clip-rule="evenodd" />
                        </svg>
                    </a>
                    <a href="#" class="text-blue-300 hover:text-white transition-colors">
                        <span class="sr-only">Twitter</span>
                        <svg class="h-6 w-6" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path d="M8.29 20.251c7.547 0 11.675-6.253 11.675-11.675 0-.178 0-.355-.012-.53A8.348 8.348 0 0022 5.92a8.19 8.19 0 01-2.357.646 4.118 4.118 0 001.804-2.27 8.224 8.224 0 01-2.605.996 4.107 4.107 0 00-6.993 3.743 11.65 11.65 0 01-8.457-4.287 4.106 4.106 0 001.27 5.477A4.072 4.072 0 012.8 9.713v.052a4.105 4.105 0 003.292 4.022 4.095 4.095 0 01-1.853.07 4.108 4.108 0 003.834 2.85A8.233 8.233 0 012 18.407a11.616 11.616 0 006.29 1.84" />
                        </svg>
                    </a>
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
                    <li><a href="{{ route('quizzes.index') }}" class="text-blue-100 hover:text-white transition-colors text-sm">{{ __('navigation.quizzes') }}</a></li>
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
                <div class="mt-4 md:mt-0 flex space-x-6">
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
