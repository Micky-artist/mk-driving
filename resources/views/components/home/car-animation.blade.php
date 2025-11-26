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

        <!-- Animation Container -->
        <div class="flex flex-row items-center gap-2 justify-center w-full overflow-hidden md:-my-40 -mx-1">
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
<script src="https://cdnjs.cloudflare.com/ajax/libs/lottie-web/5.12.2/lottie.min.js"></script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        console.log('DOM loaded, initializing Lottie animations...');
        
        // Animation containers
        const carContainer = document.getElementById('car-animation');
        const bikeContainer = document.getElementById('bike-animation');
        let carAnimation, bikeAnimation;

        // Function to initialize animations
        function initAnimations() {
            // Clean up existing animations if they exist
            if (carAnimation) carAnimation.destroy();
            if (bikeAnimation) bikeAnimation.destroy();

            // Car Animation
            carAnimation = lottie.loadAnimation({
                container: carContainer,
                renderer: 'svg',
                loop: true,
                autoplay: true,
                path: '{{ asset("json/car-moving.json") }}'
            });
            
            carAnimation.addEventListener('DOMLoaded', function() {
                console.log('Car animation loaded successfully');
            });

            // Bike Animation
            bikeAnimation = lottie.loadAnimation({
                container: bikeContainer,
                renderer: 'svg',
                loop: true,
                autoplay: true,
                path: '{{ asset("json/bike-moving.json") }}'
            });
            
            bikeAnimation.addEventListener('DOMLoaded', function() {
                console.log('Bike animation loaded successfully');
                bikeAnimation.goToAndPlay(0, true);
            });
            
            // Error handling
            carAnimation.addEventListener('data_failed', function() {
                console.error('Failed to load car animation');
            });
            
            bikeAnimation.addEventListener('data_failed', function() {
                console.error('Failed to load bike animation');
            });
        }

        // Initialize animations on load
        initAnimations();

        // Handle window resize
        let resizeTimer;
        window.addEventListener('resize', function() {
            clearTimeout(resizeTimer);
            resizeTimer = setTimeout(function() {
                initAnimations();
            }, 250);
        });
    });
</script>
