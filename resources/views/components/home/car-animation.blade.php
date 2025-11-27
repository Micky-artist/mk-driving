<div class="relative w-full h-full">
    <div class="relative w-full h-full">
        <!-- Animation Container for layout -->
        <div id="animation-container" class="relative w-full h-full overflow-hidden min-h-[375px] md:min-h-[475px]">

            <!-- Car Animation Container -->
            <div id="car-animation-container" class="absolute top-0 left-0  -mt-32 md:-mt-28 -ml-16 md:-ml-20 z-10 w-[300px] md:w-[500px] h-auto">
                <div id="car-animation" class="w-full h-full">
                    <!-- Car shadow -->
                    <div
                        class="absolute -bottom-5 left-1/2 -translate-x-1/2 w-3/4 h-5 bg-black/10 dark:bg-black/20 rounded-full blur-sm">
                    </div>
                </div>
            </div>

            <!-- Bike Animation Container -->
            <div id="bike-animation-container" class="absolute bottom-0 right-0 -mb-6 md:-mb-10 -mr-4 md:-mr-10 z-20 w-[300px] md:w-[500px] h-auto">
                <div id="bike-animation" class="w-full h-full">
                    <!-- Bike shadow -->
                    <div
                        class="absolute -bottom-5 left-1/2 -translate-x-1/2 w-3/4 h-5 bg-black/10 dark:bg-black/20 rounded-full blur-sm">
                    </div>
                </div>
            </div>

            <!-- 3D effect on hover -->
            <style>
                @media (min-width: 768px) {
                    #car-animation-container:hover {
                        transform: translateX(-10px) rotateY(5deg);
                    }

                    #bike-animation-container:hover {
                        transform: translate(10px, -10px) rotateY(-5deg);
                    }
                }
            </style>

            <!-- Lottie Library -->
            <!-- Preload Lottie library with preconnect and preload hints -->
            <link rel="preconnect" href="https://cdnjs.cloudflare.com">
            <link rel="preload" as="script"
                href="https://cdnjs.cloudflare.com/ajax/libs/lottie-web/5.12.2/lottie.min.js">
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
                    let isInitialized = false;

                    // Function to initialize animations with optimized settings
                    function initAnimations() {
                        if (isInitialized) return;
                        isInitialized = true;

                        // Clean up existing animations if they exist
                        if (carAnimation) {
                            carAnimation.destroy();
                            carAnimation = null;
                        }
                        if (bikeAnimation) {
                            bikeAnimation.destroy();
                            bikeAnimation = null;
                        }

                        // Clear containers
                        carContainer.innerHTML = '';
                        bikeContainer.innerHTML = '';

                        // Load animations with optimized settings
                        try {
                            // Load car animation
                            carAnimation = lottie.loadAnimation({
                                container: carContainer,
                                renderer: 'svg',
                                loop: true,
                                autoplay: true,
                                path: '{{ asset('json/car-moving.json') }}?v=' + Date.now(),
                                rendererSettings: {
                                    preserveAspectRatio: 'xMidYMid meet',
                                    progressiveLoad: false
                                }
                            });

                            // Load bike animation
                            bikeAnimation = lottie.loadAnimation({
                                container: bikeContainer,
                                renderer: 'svg',
                                loop: true,
                                autoplay: true,
                                path: '{{ asset('json/bike-moving.json') }}?v=' + Date.now(),
                                rendererSettings: {
                                    preserveAspectRatio: 'xMidYMid meet',
                                    progressiveLoad: false
                                }
                            });

                            // Optimize performance and handle window resize
                            if (carAnimation) {
                                carAnimation.setSubframe(false);
                                // Adjust animation size on load
                                carAnimation.addEventListener('DOMLoaded', function() {
                                    const container = document.getElementById('car-animation');
                                    const isMobile = window.innerWidth < 768; // md breakpoint
                                    container.style.width = '100%';
                                    container.style.height = 'auto';
                                    
                                    // Scale up on mobile
                                    if (isMobile) {
                                        container.style.transform = 'scale(1.3)';
                                        container.style.transformOrigin = 'left top';
                                    }
                                });
                            }

                            if (bikeAnimation) {
                                bikeAnimation.setSubframe(false);
                                // Adjust animation size on load
                                bikeAnimation.addEventListener('DOMLoaded', function() {
                                    const container = document.getElementById('bike-animation');
                                    const isMobile = window.innerWidth < 768; // md breakpoint
                                    container.style.width = '100%';
                                    container.style.height = 'auto';
                                    
                                    // Scale up on mobile
                                    if (isMobile) {
                                        container.style.transform = 'scale(1.3)';
                                        container.style.transformOrigin = 'right bottom';
                                    }
                                });
                            }

                            console.log('Animations loaded');
                        } catch (error) {
                            console.error('Error loading animations:', error);
                        }
                    }

                    // Function to load animation with retry logic
                    function loadAnimationWithRetry(container, path, maxRetries = 3, delay = 1000) {
                        return new Promise((resolve, reject) => {
                            let attempts = 0;
                            let animation = null;

                            const cleanup = () => {
                                if (animation) {
                                    animation.removeEventListener('DOMLoaded', onLoaded);
                                    animation.removeEventListener('data_failed', onError);
                                    animation.destroy();
                                    animation = null;
                                }
                            };

                            const onLoaded = () => {
                                console.log(`Animation loaded: ${path}`);
                                cleanup();
                                resolve(animation);
                            };

                            const onError = () => {
                                cleanup();
                                if (attempts < maxRetries) {
                                    attempts++;
                                    console.log(`Retry ${attempts} for ${path}...`);
                                    setTimeout(load, delay * attempts);
                                } else {
                                    reject(new Error(
                                        `Failed to load animation after ${maxRetries} attempts: ${path}`
                                        ));
                                }
                            };

                            const load = () => {
                                try {
                                    animation = lottie.loadAnimation({
                                        container: container,
                                        renderer: 'svg',
                                        loop: true,
                                        autoplay: true,
                                        rendererSettings: {
                                            preserveAspectRatio: 'xMidYMid meet',
                                            progressiveLoad: false
                                        },
                                        path: path + (path.includes('?') ? '&' : '?') + 't=' + Date
                                        .now()
                                    });

                                    animation.addEventListener('DOMLoaded', onLoaded);
                                    animation.addEventListener('data_failed', onError);
                                } catch (error) {
                                    onError();
                                }
                            };

                            load();
                        });
                    }


                    // Initialize animations when Lottie is loaded
                    function checkLottieAndInit() {
                        if (window.lottie && window.lottie.loadAnimation) {
                            // Small delay to ensure DOM is fully ready
                            setTimeout(initAnimations, 100);
                        } else {
                            // Try again shortly if Lottie isn't loaded yet
                            setTimeout(checkLottieAndInit, 100);
                        }
                    }

                    // Start the initialization process
                    checkLottieAndInit();

                    // Handle window resize with debounce
                    let resizeTimer;
                    let isResizing = false;

                    function handleResize() {
                        if (isResizing) return;
                        isResizing = true;

                        clearTimeout(resizeTimer);
                        resizeTimer = setTimeout(function() {
                            // Only reinitialize if the window width crosses the md breakpoint (768px)
                            const currentWidth = window.innerWidth;
                            const wasMobile = currentWidth < 768;

                            setTimeout(() => {
                                const isNowMobile = window.innerWidth < 768;
                                if (wasMobile !== isNowMobile) {
                                    isInitialized = false;
                                    initAnimations();
                                }
                                isResizing = false;
                            }, 100);
                        }, 200);
                    }

                    window.addEventListener('resize', handleResize, {
                        passive: true
                    });

                });
            </script>
