<div class="relative w-full bg-gradient-to-br from-blue-900 to-blue-700 dark:from-gray-900 dark:to-gray-800 overflow-hidden">
    <!-- Animated Background Elements -->
    <div class="absolute inset-0 opacity-10">
        <div class="absolute inset-0 bg-grid-white/[0.05] [mask-image:linear-gradient(to_bottom,transparent,white,transparent)]"></div>
    </div>
    
    <div class="relative mb-4 w-full">
        <!-- Title Section -->
        <div class="text-center px-4 relative z-10">
            <div class="inline-block bg-gradient-to-r from-blue-800/90 to-blue-900/90 backdrop-blur-sm rounded-2xl px-6 py-4 sm:px-8 sm:py-6 shadow-2xl border border-blue-700/50 transform rotate-[-0.5deg] mb-6">
                <h1 class="text-3xl sm:text-4xl md:text-5xl lg:text-6xl font-extrabold bg-gradient-to-r from-cyan-300 via-blue-200 to-cyan-300 bg-clip-text text-transparent mb-3 sm:mb-4 leading-tight drop-shadow-lg">
                    {{ __('home.car_animation.title') }}
                </h1>
                <div class="h-1 w-20 bg-cyan-300/70 mx-auto my-3 rounded-full"></div>
                <p class="text-base sm:text-lg md:text-xl text-blue-50 max-w-3xl mx-auto font-medium leading-relaxed">
                    {{ __('home.car_animation.subtitle') }}
                </p>
            </div>
        </div>

        <!-- Loading State -->
        <div id="animation-loading" class="flex flex-col items-center justify-center w-full overflow-hidden md:my-28 -mx-1">
            <div class="relative w-16 h-16 mb-4">
                <div class="absolute inset-0 rounded-full border-4 border-blue-400 border-t-transparent animate-spin"></div>
                <div class="absolute inset-1 rounded-full border-4 border-blue-300 border-t-transparent animate-spin animation-delay-200"></div>
            </div>
            <p class="text-blue-100 text-lg font-medium">{{ __('home.car_animation.loading') }}</p>
        </div>

        <!-- Animation Container -->
        <div id="animation-container" class="flex flex-row items-center gap-2 justify-center w-full overflow-hidden md:-my-40 -mx-1 opacity-0 transition-opacity duration-500 hidden">
            <!-- Car Animation -->
            <div id="car-animation" class="w-1/2 max-w-[80%] h-auto aspect-[4/3] md:aspect-[16/9] scale-[2] md:scale-200"></div>
            
            <!-- Bike Animation -->
            <div id="bike-animation" class="w-1/2 max-w-[50%] h-auto aspect-square scale-120 md:scale-75 -mt-4"></div>
        </div>

        <!-- CTA Button -->
        <div class="text-center px-4 relative z-10 mt-8 sm:mt-10">
            <div class="relative inline-block group">
                <div class="absolute -inset-1 bg-gradient-to-r from-cyan-400 to-blue-500 rounded-full blur opacity-75 group-hover:opacity-100 transition-all duration-300 group-hover:animate-pulse"></div>
                <a href="{{ route('register', app()->getLocale()) }}" 
                   class="relative flex items-center px-8 py-4 sm:px-10 sm:py-5 text-sm sm:text-base bg-gradient-to-r from-blue-600 to-blue-700 text-white font-bold rounded-full hover:from-blue-500 hover:to-blue-600 transition-all duration-300 transform group-hover:scale-105 shadow-xl hover:shadow-2xl border-2 border-blue-400/30">
                    <span class="drop-shadow-md">{{ __('home.car_animation.cta') }}</span>
                    <svg class="ml-3 w-5 h-5 sm:w-6 sm:h-6 transform group-hover:translate-x-1 transition-transform duration-300" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M17 8l4 4m0 0l-4 4m4-4H3"></path>
                    </svg>
                </a>
            </div>
        </div>
    </div>
    
    <!-- Decorative Elements -->
    <div class="absolute bottom-0 left-0 right-0 h-12 bg-gradient-to-t from-white/10 dark:from-gray-900/50 to-transparent"></div>
</div>

<!-- Lottie Library -->
<!-- Preload Lottie library with preconnect and preload hints -->
<link rel="preconnect" href="https://cdnjs.cloudflare.com">
<link rel="preload" as="script" href="https://cdnjs.cloudflare.com/ajax/libs/lottie-web/5.12.2/lottie.min.js">
<script src="https://cdnjs.cloudflare.com/ajax/libs/lottie-web/5.12.2/lottie.min.js" defer></script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        console.log('DOM loaded, initializing Lottie animations...');
        
        // Get DOM elements
        const animationContainer = document.getElementById('animation-container');
        const loadingElement = document.getElementById('animation-loading');
        const carContainer = document.getElementById('car-animation');
        const bikeContainer = document.getElementById('bike-animation');
        let carAnimation, bikeAnimation;

        // Function to initialize animations with optimized settings
        function initAnimations() {
            // Show loading state
            loadingElement.classList.remove('hidden');
            animationContainer.classList.add('hidden');
            animationContainer.classList.remove('flex');
            
            // Clean up existing animations if they exist
            if (carAnimation) carAnimation.destroy();
            if (bikeAnimation) bikeAnimation.destroy();

            // Load animations with optimized settings
            Promise.all([
                loadAnimationWithRetry(carContainer, '{{ asset("json/car-moving.json") }}', 3, 1000),
                loadAnimationWithRetry(bikeContainer, '{{ asset("json/bike-moving.json") }}', 3, 1000)
            ]).then(([carAnim, bikeAnim]) => {
                carAnimation = carAnim;
                bikeAnimation = bikeAnim;
                
                // Hide loading and show animations
                loadingElement.classList.add('hidden');
                animationContainer.classList.remove('hidden');
                animationContainer.classList.add('flex');
                setTimeout(() => {
                    animationContainer.style.opacity = '1';
                }, 50);
                
                console.log('All animations loaded successfully');
            }).catch(error => {
                console.error('Error loading animations:', error);
                // Fallback to static images if animations fail to load
                handleAnimationError();
            });
        }
        
        // Function to load animation with retry logic
        function loadAnimationWithRetry(container, path, maxRetries = 3, delay = 1000) {
            return new Promise((resolve, reject) => {
                let attempts = 0;
                
                const tryLoad = () => {
                    attempts++;
                    
                    const animation = lottie.loadAnimation({
                        container: container,
                        renderer: 'svg',
                        loop: true,
                        autoplay: true,
                        rendererSettings: {
                            preserveAspectRatio: 'xMidYMid meet',
                            progressiveLoad: true
                        },
                        path: path
                    });
                    
                    animation.addEventListener('DOMLoaded', () => {
                        console.log(`Animation loaded: ${path}`);
                        resolve(animation);
                    });
                    
                    animation.addEventListener('data_failed', () => {
                        animation.destroy();
                        if (attempts < maxRetries) {
                            console.log(`Retry ${attempts + 1} for ${path}...`);
                            setTimeout(tryLoad, delay * attempts);
                        } else {
                            reject(new Error(`Failed to load animation after ${maxRetries} attempts: ${path}`));
                        }
                    });
                };
                
                tryLoad();
            });
        }
        
        // Handle animation loading errors with fallback
        function handleAnimationError() {
            loadingElement.innerHTML = `
                <div class="text-center">
                    <div class="inline-block p-4 bg-blue-900/50 rounded-full mb-4">
                        <svg class="w-12 h-12 text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                        </svg>
                    </div>
                    <p class="text-blue-100">{{ __('Could not load interactive experience.') }}</p>
                    <button onclick="window.location.reload()" class="mt-4 px-6 py-2 bg-orange-500 hover:bg-orange-600 text-white rounded-full text-sm font-medium transition-colors duration-200">
                        {{ __('Try Again') }}
                    </button>
                </div>
            `;
            loadingElement.classList.remove('opacity-0');
        }

        // Initialize animations when Lottie is loaded
        if (window.lottie) {
            initAnimations();
        } else {
            // Fallback in case Lottie fails to load
            document.addEventListener('lottie_loaded', initAnimations);
        }

        // Handle window resize with debounce
        let resizeTimer;
        let isResizing = false;
        
        function handleResize() {
            if (!isResizing) {
                isResizing = true;
                loadingElement.classList.remove('hidden');
                loadingElement.style.opacity = '1';
                animationContainer.style.opacity = '0';
            }
            
            clearTimeout(resizeTimer);
            resizeTimer = setTimeout(function() {
                if (carAnimation) carAnimation.destroy();
                if (bikeAnimation) bikeAnimation.destroy();
                
                // Reinitialize with current container dimensions
                requestAnimationFrame(initAnimations);
                isResizing = false;
            }, 300);
        }
        
        window.addEventListener('resize', handleResize, { passive: true });
        
        // Add animation delay utility
        const style = document.createElement('style');
        style.textContent = `
            @keyframes spin {
                to { transform: rotate(360deg); }
            }
            .animate-spin {
                animation: spin 1s linear infinite;
            }
            .animation-delay-200 {
                animation-delay: 0.2s;
            }
            #animation-loading {
                transition: opacity 0.3s ease-in-out;
            }
        `;
        document.head.appendChild(style);
    });
</script>
