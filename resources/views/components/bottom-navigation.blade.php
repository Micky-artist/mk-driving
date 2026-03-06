@props(['showUserStats' => false])

<!-- Bottom Navigation Bar for Mobile -->
<nav class="md:hidden fixed bottom-0 left-0 right-0 bg-white dark:bg-gray-800 border-t border-gray-200 dark:border-gray-700 z-40 shadow-lg transform transition-transform duration-300 ease-in-out"
     x-data="bottomNavigation()"
     :class="{ 'translate-y-0': !isHidden, 'translate-y-full': isHidden }"
     x-init="initScrollListener()"
     x-watch="updateBodyPadding()">
    <div class="grid grid-cols-5 h-16">
        <!-- Home -->
        <a href="{{ route('home', ['locale' => app()->getLocale()]) }}" 
           class="flex flex-col items-center justify-center space-y-1 transition-colors duration-200 group"
           :class="{ 'text-blue-600 dark:text-blue-400': isActive('home'), 'text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300': !isActive('home') }">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                      d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
            </svg>
            <span class="text-xs font-medium">{{ __('navigation.home') }}</span>
        </a>

        <!-- Quizzes -->
        <a href="{{ route('dashboard.quizzes.index', ['locale' => app()->getLocale()]) }}" 
           class="flex flex-col items-center justify-center space-y-1 transition-colors duration-200 group"
           :class="{ 'text-blue-600 dark:text-blue-400': isActive('quizzes'), 'text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300': !isActive('quizzes') }">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                      d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
            </svg>
            <span class="text-xs font-medium">{{ __('navigation.quizzes_short') }}</span>
        </a>

        <!-- Leaderboard -->
        <a href="{{ route('leaderboard', ['locale' => app()->getLocale()]) }}" 
           class="flex flex-col items-center justify-center space-y-1 transition-colors duration-200 group"
           :class="{ 'text-blue-600 dark:text-blue-400': isActive('leaderboard'), 'text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300': !isActive('leaderboard') }">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                      d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z" />
            </svg>
            <span class="text-xs font-medium">{{ __('navigation.leaderboard_short') }}</span>
        </a>

        <!-- Forum -->
        <a href="{{ route('forum.index', ['locale' => app()->getLocale()]) }}" 
           class="flex flex-col items-center justify-center space-y-1 transition-colors duration-200 group"
           :class="{ 'text-blue-600 dark:text-blue-400': isActive('forum'), 'text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300': !isActive('forum') }">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                      d="M17 8h2a2 2 0 012 2v6a2 2 0 01-2 2h-2v4l-4-4H9a1.994 1.994 0 01-1.414-.586m0 0L11 14h4a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2v4l.586-.586z" />
            </svg>
            <span class="text-xs font-medium">{{ __('forum.page_title') }}</span>
        </a>

        <!-- Dashboard -->
        <a href="{{ route('dashboard', ['locale' => app()->getLocale()]) }}" 
           class="flex flex-col items-center justify-center space-y-1 transition-colors duration-200 group"
           :class="{ 'text-blue-600 dark:text-blue-400': isActive('dashboard'), 'text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300': !isActive('dashboard') }">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                      d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
            </svg>
            <span class="text-xs font-medium">{{ __('navigation.dashboard') }}</span>
        </a>
    </div>
</nav>

<script>
    function bottomNavigation() {
        return {
            isHidden: false,
            lastScrollY: window.scrollY,
            
            initScrollListener() {
                window.addEventListener('scroll', () => {
                    this.handleScroll();
                });
                
                // Initialize body padding
                this.updateBodyPadding();
                
                // Update padding on resize
                window.addEventListener('resize', () => {
                    this.updateBodyPadding();
                });
            },
            
            handleScroll() {
                const currentScrollY = window.scrollY;
                const scrollDirection = currentScrollY > this.lastScrollY ? 'down' : 'up';
                
                // Immediate response - hide on any scroll down, show on any scroll up
                if (scrollDirection === 'down') {
                    this.isHidden = true;
                } else if (scrollDirection === 'up') {
                    this.isHidden = false;
                }
                
                this.lastScrollY = currentScrollY;
            },
            
            updateBodyPadding() {
                const isMobile = window.innerWidth < 768;
                if (isMobile) {
                    // Smooth padding transition
                    document.body.style.paddingBottom = this.isHidden ? '0' : '4rem';
                    document.body.style.transition = 'padding-bottom 0.2s ease-out';
                } else {
                    document.body.style.paddingBottom = '0';
                }
            },
            
            isActive(section) {
                const currentRoute = '{{ request()->route()->getName() ?? '' }}';
                
                switch(section) {
                    case 'home':
                        return currentRoute === 'home' || currentRoute.startsWith('home.');
                    case 'dashboard':
                        return currentRoute === 'dashboard' || 
                               currentRoute.startsWith('dashboard.progress') ||
                               currentRoute.startsWith('dashboard.stats');
                    case 'quizzes':
                        return currentRoute.startsWith('dashboard.quizzes') || 
                               currentRoute.startsWith('guest-quiz.');
                    case 'leaderboard':
                        return currentRoute === 'leaderboard' || currentRoute.startsWith('leaderboard.');
                    case 'forum':
                        return currentRoute.startsWith('forum.');
                    default:
                        return false;
                }
            }
        }
    }
</script>
