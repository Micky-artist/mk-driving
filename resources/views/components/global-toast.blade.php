@php
// Component: resources/views/components/global-toast.blade.php
// Global toast notification system
@endphp

<div x-data="globalToast()" x-init="init()" class="fixed top-0 left-0 right-0 z-[60] pointer-events-none lg:hidden">
    <!-- Toast Container -->
    <div x-show="show" 
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 transform -translate-y-full"
         x-transition:enter-end="opacity-100 transform translate-y-0"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100 transform translate-y-0"
         x-transition:leave-end="opacity-0 transform -translate-y-full"
         class="pointer-events-auto bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700 shadow-lg">
        
        <div class="flex items-center gap-3 px-4 py-3">
            <!-- Icon -->
            <div class="flex-shrink-0">
                <div x-show="type === 'robot'" class="w-8 h-8 bg-blue-500 rounded-full flex items-center justify-center text-white text-sm font-bold">
                    🤖
                </div>
                <div x-show="type === 'user'" class="w-8 h-8 bg-green-500 rounded-full flex items-center justify-center text-white text-sm font-bold">
                    👤
                </div>
                <div x-show="type === 'system'" class="w-8 h-8 bg-gray-500 rounded-full flex items-center justify-center text-white text-sm font-bold">
                    ℹ️
                </div>
            </div>
            
            <!-- Content -->
            <div class="flex-1 min-w-0">
                <div class="font-medium text-gray-900 dark:text-white text-sm" x-text="title"></div>
                <div class="text-gray-600 dark:text-gray-300 text-xs" x-text="message"></div>
            </div>
            
            <!-- Close Button -->
            <button @click="hide()" 
                    class="flex-shrink-0 text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>
    </div>
</div>

<script>
function globalToast() {
    return {
        show: false,
        type: 'robot',
        title: '',
        message: '',
        timeout: null,
        
        init() {
            console.log('Global toast component initialized');
            
            // Listen for global toast events
            window.addEventListener('showGlobalToast', (event) => {
                console.log('showGlobalToast event received:', event.detail);
                this.showToast(event.detail);
            });
            
            // Listen for companion updates
            window.addEventListener('robotCompanionUpdate', (event) => {
                console.log('robotCompanionUpdate event received:', event.detail);
                const latestActivity = event.detail.robotMessages?.[0];
                if (latestActivity) {
                    this.showToast({
                        type: 'robot',
                        title: latestActivity.robot_name || latestActivity.learner_name,
                        message: latestActivity.message
                    });
                }
            });
        },
        
        showToast(data) {
            this.type = data.type || 'robot';
            this.title = data.title || '';
            this.message = data.message || '';
            this.show = true;
            
            // Auto-hide after 5 seconds
            if (this.timeout) {
                clearTimeout(this.timeout);
            }
            
            this.timeout = setTimeout(() => {
                this.hide();
            }, 5000);
        },
        
        hide() {
            this.show = false;
            if (this.timeout) {
                clearTimeout(this.timeout);
                this.timeout = null;
            }
        }
    };
}
</script>
