<footer class="bg-gray-900 text-white pt-12 pb-8 mt-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
            <!-- Company Info -->
            <div class="md:col-span-2">
                <h3 class="text-2xl font-bold mb-4">{{ config('app.name') }}</h3>
                <p class="text-gray-300 text-sm leading-relaxed max-w-md">
                    {{ __('forum.footer.tagline', ['app' => config('app.name')]) }}
                </p>
                <div class="flex space-x-6 mt-6">
                    <a href="#" class="text-gray-400 hover:text-white transition-colors" aria-label="Twitter">
                        <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M8.29 20.251c7.547 0 11.675-6.253 11.675-11.675 0-.178 0-.355-.012-.53A8.348 8.348 0 0022 5.92a8.19 8.19 0 01-2.357.646 4.118 4.118 0 001.804-2.27 8.224 8.224 0 01-2.605.996 4.107 4.107 0 00-6.993 3.743 11.65 11.65 0 01-8.457-4.287 4.106 4.106 0 001.27 5.477A4.072 4.072 0 012.8 9.713v.052a4.105 4.105 0 003.292 4.022 4.095 4.095 0 01-1.853.07 4.108 4.108 0 003.834 2.85A8.233 8.233 0 012 18.407a11.616 11.616 0 006.29 1.84" />
                        </svg>
                    </a>
                    <a href="#" class="text-gray-400 hover:text-white transition-colors" aria-label="Facebook">
                        <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M22 12c0-5.523-4.477-10-10-10S2 6.477 2 12c0 4.991 3.657 9.128 8.438 9.878v-6.987h-2.54V12h2.54V9.797c0-2.506 1.492-3.89 3.777-3.89 1.094 0 2.238.195 2.238.195v2.46h-1.26c-1.243 0-1.63.771-1.63 1.562V12h2.773l-.443 2.89h-2.33v6.988C18.343 21.128 22 16.991 22 12z" />
                        </svg>
                    </a>
                    <a href="#" class="text-gray-400 hover:text-white transition-colors" aria-label="Instagram">
                        <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
                            <path fill-rule="evenodd" d="M12.315 2c2.43 0 2.784.013 3.808.06 1.064.049 1.791.218 2.427.465a4.902 4.902 0 011.772 1.153 4.902 4.902 0 011.153 1.772c.247.636.416 1.363.465 2.427.048 1.067.06 1.407.06 4.123v.08c0 2.643-.012 2.987-.06 4.043-.049 1.064-.218 1.791-.465 2.427a4.902 4.902 0 01-1.153 1.772 4.902 4.902 0 01-1.772 1.153c-.636.247-1.363.416-2.427.465-1.067.048-1.407.06-4.123.06h-.08c-2.643 0-2.987-.012-4.043-.06-1.064-.049-1.791-.218-2.427-.465a4.902 4.902 0 01-1.772-1.153 4.902 4.902 0 01-1.153-1.772c-.247-.636-.416-1.363-.465-2.427-.047-1.024-.06-1.379-.06-3.808v-.63c0-2.43.013-2.784.06-3.808.049-1.064.218-1.791.465-2.427a4.902 4.902 0 011.153-1.772A4.902 4.902 0 015.45 2.525c.636-.247 1.363-.416 2.427-.465C8.901 2.013 9.256 2 11.685 2h.63z" clip-rule="evenodd" />
                        </svg>
                    </a>
                    <a href="#" class="text-gray-400 hover:text-white transition-colors" aria-label="YouTube">
                        <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M23.498 6.186a3.016 3.016 0 0 0-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 0 0 .502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 0 0 2.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 0 0 2.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z" />
                        </svg>
                    </a>
                </div>
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
                        <span class="text-gray-400 text-sm">info@mkdriving.com</span>
                    </li>
                    <li class="flex items-start">
                        <svg class="h-5 w-5 text-gray-400 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                        </svg>
                        <span class="text-gray-400 text-sm">+250 700 000 000</span>
                    </li>
                    <li class="flex items-start">
                        <svg class="h-5 w-5 text-gray-400 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                        <span class="text-gray-400 text-sm">Kigali, Rwanda</span>
                    </li>
                </ul>
            </div>
        </div>

        <!-- Copyright -->
        <div class="mt-12 border-t border-gray-800 pt-8 flex flex-col md:flex-row justify-between items-center">
            <p class="text-gray-400 text-sm text-center md:text-left">
                &copy; {{ date('Y') }} {{ config('app.name') }}. {{ __('forum.footer.all_rights_reserved') }}
            </p>
            <div class="mt-4 md:mt-0 flex space-x-6">
                <a href="#" class="text-gray-400 hover:text-white text-sm">
                    {{ __('forum.footer.privacy_policy') }}
                </a>
                <a href="#" class="text-gray-400 hover:text-white text-sm">
                    {{ __('forum.footer.terms_of_service') }}
                </a>
                <a href="#" class="text-gray-400 hover:text-white text-sm">
                    {{ __('forum.footer.cookie_policy') }}
                </a>
            </div>
        </div>
                    <li>
                        <a href="#" class="text-gray-300 hover:text-white text-sm">
                            {{ __('home.footer.terms_of_service') }}
                        </a>
                    </li>
                    <li>
                        <a href="#" class="text-gray-300 hover:text-white text-sm">
                            {{ __('home.footer.cookie_policy') }}
                        </a>
                    </li>
                    <li>
                        <a href="#" class="text-gray-300 hover:text-white text-sm">
                            {{ __('home.footer.refund_policy') }}
                        </a>
                    </li>
                </ul>
            </div>
        </div>

        <!-- Copyright -->
        <div class="mt-12 border-t border-gray-700 pt-8">
            <p class="text-gray-400 text-sm text-center">
                &copy; {{ date('Y') }} {{ config('app.name') }}. {{ __('home.footer.all_rights_reserved') }}
            </p>
        </div>
    </div>
</footer>
