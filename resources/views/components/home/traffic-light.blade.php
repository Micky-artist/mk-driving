<div class="traffic-light-container absolute right-40 md:right-24 top-100 md:top-40 z-30 md:block">
    <!-- Title -->
    <div class="text-center">
        <h3 class="text-xs font-bold text-gray-700 dark:text-gray-300">
            {{ __('home.trafficLight.title') }}
        </h3>
    </div>
    
    <div class="traffic-light w-10 h-24 md:w-10 md:h-24 bg-gray-800 dark:bg-gray-900 rounded-xl p-2 flex flex-col items-center justify-between shadow-2xl border-4 border-gray-700 dark:border-gray-600 relative">
        <!-- Red Light -->
        <div class="light-container relative w-10 h-10 md:w-12 md:h-12 rounded-full mb-1">
            <div class="light red w-full h-full rounded-full bg-red-600 dark:bg-red-500 shadow-inner flex items-center justify-center transition-all duration-300">
                <div class="w-3/4 h-3/4 rounded-full bg-red-400 opacity-50"></div>
                <div class="absolute inset-0 rounded-full shadow-[inset_0_-2px_8px_rgba(0,0,0,0.6)]"></div>
                <div class="absolute inset-0 rounded-full bg-gradient-to-br from-white/20 via-transparent to-transparent"></div>
            </div>
            <div class="absolute left-full ml-4 top-1/2 -translate-y-1/2 whitespace-nowrap">
                <span class="text-[10px] md:text-xs font-medium text-gray-700 dark:text-gray-300 light-label red-label">
                    {{ __('home.trafficLight.lights.red.' . app()->getLocale()) }}
                </span>
            </div>
        </div>
        
        <!-- Yellow Light -->
        <div class="light-container relative w-10 h-10 md:w-12 md:h-12 rounded-full my-1">
            <div class="light yellow w-full h-full rounded-full bg-yellow-500 dark:bg-yellow-400 shadow-inner flex items-center justify-center transition-all duration-300">
                <div class="w-3/4 h-3/4 rounded-full bg-yellow-300 opacity-50"></div>
                <div class="absolute inset-0 rounded-full shadow-[inset_0_-2px_8px_rgba(0,0,0,0.6)]"></div>
                <div class="absolute inset-0 rounded-full bg-gradient-to-br from-white/20 via-transparent to-transparent"></div>
            </div>
            <div class="absolute left-full ml-4 top-1/2 -translate-y-1/2 whitespace-nowrap">
                <span class="text-[10px] md:text-xs font-medium text-gray-700 dark:text-gray-300 light-label yellow-label">
                    {{ __('home.trafficLight.lights.yellow.' . app()->getLocale()) }}
                </span>
            </div>
        </div>
        
        <!-- Green Light -->
        <div class="light-container relative w-10 h-10 md:w-12 md:h-12 rounded-full mt-1">
            <div class="light green w-full h-full rounded-full bg-green-600 dark:bg-green-500 shadow-inner flex items-center justify-center transition-all duration-300">
                <div class="w-3/4 h-3/4 rounded-full bg-green-400 opacity-50"></div>
                <div class="absolute inset-0 rounded-full shadow-[inset_0_-2px_8px_rgba(0,0,0,0.6)]"></div>
                <div class="absolute inset-0 rounded-full bg-gradient-to-br from-white/20 via-transparent to-transparent"></div>
            </div>
            <div class="absolute left-full ml-4 top-1/2 -translate-y-1/2 whitespace-nowrap">
                <span class="text-[10px] md:text-xs font-medium text-gray-700 dark:text-gray-300 light-label green-label">
                    {{ __('home.trafficLight.lights.green.' . app()->getLocale()) }}
                </span>
            </div>
        </div>
        
        <!-- Traffic light stand -->
        <div class="absolute -bottom-8 left-1/2 -translate-x-1/2 w-4 h-8 bg-gray-700 dark:bg-gray-800 rounded-b-sm"></div>
        <div class="absolute -bottom-10 left-1/2 -translate-x-1/2 w-8 h-2 bg-gray-600 dark:bg-gray-700 rounded-full"></div>
    </div>
</div>

<style>
    /* Inactive light states - dimmed */
    .light {
        opacity: 0.3;
        transition: all 0.3s ease-in-out;
    }
    
    .light-label {
        opacity: 0.4;
        transition: opacity 0.3s ease-in-out;
    }
    
    /* Pulsing animation for the active light */
    @keyframes pulse {
        0%, 100% { 
            transform: scale(1);
            opacity: 1;
        }
        50% { 
            transform: scale(1.05);
            opacity: 1;
        }
    }
    
    @keyframes glow {
        0%, 100% { 
            filter: drop-shadow(0 0 8px currentColor);
        }
        50% { 
            filter: drop-shadow(0 0 15px currentColor);
        }
    }
    
    /* Active light states */
    .light.active {
        opacity: 1;
        animation: pulse 2s ease-in-out infinite;
    }
    
    .light.red.active {
        box-shadow: 0 0 25px 8px rgba(239, 68, 68, 0.6),
                    0 0 40px 12px rgba(239, 68, 68, 0.3);
    }
    
    .light.yellow.active {
        box-shadow: 0 0 25px 8px rgba(234, 179, 8, 0.6),
                    0 0 40px 12px rgba(234, 179, 8, 0.3);
    }
    
    .light.green.active {
        box-shadow: 0 0 25px 8px rgba(34, 197, 94, 0.6),
                    0 0 40px 12px rgba(34, 197, 94, 0.3);
    }
    
    /* Active label highlight */
    .light.red.active ~ div .red-label {
        opacity: 1;
        font-weight: 600;
        color: #ef4444; /* Red color for active state */
    }
    
    .light.yellow.active ~ div .yellow-label {
        opacity: 1;
        font-weight: 600;
        color: #eab308; /* Yellow color for active state */
    }
    
    .light.green.active ~ div .green-label {
        opacity: 1;
        font-weight: 600;
        color: #22c55e; /* Green color for active state */
    }
    
    /* Hover effects */
    .traffic-light {
        transition: transform 0.2s ease-in-out;
    }
    
    .traffic-light:hover {
        transform: translateY(-2px);
    }
    
    .light-container {
        cursor: pointer;
    }
    
    .light-container:hover .light {
        opacity: 0.6;
    }
    
    .light-container:hover .light.active {
        opacity: 1;
    }
    
    /* Responsive adjustments */
    @media (max-width: 768px) {
        .traffic-light-container {
            right: 0.5rem;
            top: 4rem;
        }
        
        .light.active {
            box-shadow: 0 0 15px 5px currentColor;
        }
    }
    
    /* Dark mode enhancements */
    @media (prefers-color-scheme: dark) {
        .light.red.active {
            box-shadow: 0 0 30px 10px rgba(239, 68, 68, 0.7),
                        0 0 50px 15px rgba(239, 68, 68, 0.4);
        }
        
        .light.yellow.active {
            box-shadow: 0 0 30px 10px rgba(234, 179, 8, 0.7),
                        0 0 50px 15px rgba(234, 179, 8, 0.4);
        }
        
        .light.green.active {
            box-shadow: 0 0 30px 10px rgba(34, 197, 94, 0.7),
                        0 0 50px 15px rgba(34, 197, 94, 0.4);
        }
    }
    
    /* Accessibility - reduced motion */
    @media (prefers-reduced-motion: reduce) {
        .light.active {
            animation: none;
        }
        
        .light {
            transition: opacity 0.3s ease-in-out;
        }
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const lights = document.querySelectorAll('.light');
        let currentLight = 0;
        
        function cycleLights() {
            // Remove active class from all lights
            lights.forEach(light => light.classList.remove('active'));
            
            // Add active class to current light
            lights[currentLight].classList.add('active');
            
            // Determine timing based on light color
            let delay;
            if (currentLight === 0) { // Red
                delay = 5000;
            } else if (currentLight === 1) { // Yellow
                delay = 2000;
            } else { // Green
                delay = 5000;
            }
            
            // Move to next light
            currentLight = (currentLight + 1) % lights.length;
            
            // Schedule next change
            setTimeout(cycleLights, delay);
        }
        
        // Optional: Click to manually cycle
        lights.forEach((light, index) => {
            light.parentElement.addEventListener('click', function() {
                lights.forEach(l => l.classList.remove('active'));
                light.classList.add('active');
                currentLight = (index + 1) % lights.length;
            });
        });
        
        // Start the light cycle after a brief delay
        setTimeout(cycleLights, 1000);
    });
</script>