<div x-data="notification()" class="fixed bottom-0 right-0 w-full sm:max-w-sm p-4 space-y-4 z-50">
    <template x-for="notification in notifications" :key="notification.id">
        <div 
            x-show="!notification.hidden"
            x-transition:enter="transform ease-out duration-300 transition"
            x-transition:enter-start="translate-y-2 opacity-0 sm:translate-y-0 sm:translate-x-2"
            x-transition:enter-end="translate-y-0 opacity-100 sm:translate-x-0"
            x-transition:leave="transition ease-in duration-100"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="rounded-lg p-4 shadow-lg"
            :class="{
                'bg-blue-50 border-l-4 border-blue-500 text-blue-800': notification.type === 'info',
                'bg-orange-50 border-l-4 border-orange-500 text-orange-800': notification.type === 'warning',
                'bg-green-50 border-l-4 border-green-500 text-green-800': notification.type === 'success',
                'bg-red-50 border-l-4 border-red-500 text-red-800': notification.type === 'error'
            }"
        >
            <div class="flex">
                <div class="flex-shrink-0">
                    <template x-if="notification.type === 'success'">
                        <svg class="h-5 w-5 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                    </template>
                    <template x-if="notification.type === 'error'">
                        <svg class="h-5 w-5 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </template>
                    <template x-if="notification.type === 'warning'">
                        <svg class="h-5 w-5 text-orange-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                    </template>
                    <template x-if="notification.type === 'info'">
                        <svg class="h-5 w-5 text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </template>
                </div>
                <div class="ml-3">
                    <p x-text="notification.message" class="text-sm font-medium"></p>
                </div>
                <div class="ml-4 flex-shrink-0 flex">
                    <button @click="hide(notification.id)" class="inline-flex text-gray-400 hover:text-gray-500 focus:outline-none">
                        <span class="sr-only">Close</span>
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            </div>
        </div>
    </template>
</div>

<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('notification', () => ({
        notifications: [],
        init() {
            // Listen for new notifications
            window.addEventListener('notify', (e) => {
                this.show(e.detail.type, e.detail.message, e.detail.duration);
            });
        },
        show(type, message, duration = 5000) {
            const id = Date.now().toString();
            this.notifications.push({
                id,
                type,
                message,
                hidden: false
            });

            // Auto-hide after duration
            if (duration > 0) {
                setTimeout(() => {
                    this.hide(id);
                }, duration);
            }
        },
        hide(id) {
            const index = this.notifications.findIndex(n => n.id === id);
            if (index !== -1) {
                this.notifications[index].hidden = true;
                // Remove from array after animation
                setTimeout(() => {
                    this.notifications = this.notifications.filter(n => n.id !== id);
                }, 300);
            }
        },
        // Helper methods for different notification types
        success(message, duration = 5000) {
            this.show('success', message, duration);
        },
        error(message, duration = 5000) {
            this.show('error', message, duration);
        },
        warning(message, duration = 5000) {
            this.show('warning', message, duration);
        },
        info(message, duration = 5000) {
            this.show('info', message, duration);
        }
    }));

    // Global notification function
    window.notify = {
        success: (message, duration) => {
            window.dispatchEvent(new CustomEvent('notify', { 
                detail: { type: 'success', message, duration }
            }));
        },
        error: (message, duration) => {
            window.dispatchEvent(new CustomEvent('notify', { 
                detail: { type: 'error', message, duration }
            }));
        },
        warning: (message, duration) => {
            window.dispatchEvent(new CustomEvent('notify', { 
                detail: { type: 'warning', message, duration }
            }));
        },
        info: (message, duration) => {
            window.dispatchEvent(new CustomEvent('notify', { 
                detail: { type: 'info', message, duration }
            }));
        }
    };
});
</script>
