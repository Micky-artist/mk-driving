<!-- Welcome Modal for New Users -->
@if(session('show_welcome_modal'))
<div id="welcomeModal" class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <!-- Background overlay -->
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true"></div>

        <!-- Modal panel -->
        <div class="inline-block align-bottom bg-white dark:bg-gray-800 rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-md sm:w-full">
            <div class="bg-blue-600 dark:bg-blue-700 px-6 py-4">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <svg class="h-8 w-8 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z" />
                        </svg>
                    </div>
                    <div class="ml-4">
                        <h3 class="text-xl font-bold text-white" id="modal-title">
                            {{ __('dashboard.welcome.title', ['name' => auth()->user()->first_name]) }}
                        </h3>
                    </div>
                </div>
            </div>

            <div class="px-6 py-4">
                <p class="text-gray-700 dark:text-gray-300 text-sm mb-4">
                    {{ __('dashboard.welcome.welcome_message') }}
                </p>

                <div class="space-y-2">
                    @php
                        $latestFreeQuiz = \App\Models\Quiz::where('is_active', true)
                            ->whereNull('subscription_plan_slug')
                            ->orderBy('created_at', 'desc')
                            ->first();
                    @endphp
                    @if($latestFreeQuiz)
                        <a href="{{ route('dashboard.quizzes.show', ['locale' => app()->getLocale(), 'quiz' => $latestFreeQuiz->id]) }}" 
                           class="block w-full bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-3 hover:bg-blue-100 dark:hover:bg-blue-900/30 transition-colors text-center">
                    @else
                        <a href="{{ route('dashboard.quizzes.index', ['locale' => app()->getLocale()]) }}" 
                           class="block w-full bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-3 hover:bg-blue-100 dark:hover:bg-blue-900/30 transition-colors text-center">
                    @endif
                        <span class="text-blue-800 dark:text-blue-200 font-medium">
                            {{ __('dashboard.welcome.free_quiz_title') }}
                        </span>
                    </a>

                    <a href="{{ route('plans', ['locale' => app()->getLocale()]) }}" 
                       class="block w-full bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-3 hover:bg-blue-100 dark:hover:bg-blue-900/30 transition-colors text-center">
                        <span class="text-blue-800 dark:text-blue-200 font-medium">
                            {{ __('dashboard.welcome.subscription_title') }}
                        </span>
                    </a>

                    <a href="{{ route('forum.index', ['locale' => app()->getLocale(), 'see' => 'leaderboard']) }}" 
                       class="block w-full bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-3 hover:bg-blue-100 dark:hover:bg-blue-900/30 transition-colors text-center">
                        <span class="text-blue-800 dark:text-blue-200 font-medium">
                            {{ __('dashboard.welcome.leaderboard_title') }}
                        </span>
                    </a>
                </div>
            </div>

            <div class="bg-gray-50 dark:bg-gray-700 px-6 py-3 flex justify-end">
                <button type="button" 
                        onclick="closeWelcomeModal()" 
                        class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-lg transition-colors">
                    {{ __('dashboard.welcome.close') }}
                </button>
            </div>
        </div>
    </div>
</div>

<script>
function closeWelcomeModal() {
    const modal = document.getElementById('welcomeModal');
    modal.remove();
    // Clear the session flag via AJAX
    fetch('{{ route("dashboard.clear-welcome-modal", ["locale" => app()->getLocale()]) }}', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Content-Type': 'application/json'
        }
    });
}

// Auto-close after 15 seconds if no interaction
setTimeout(() => {
    const modal = document.getElementById('welcomeModal');
    if (modal) {
        closeWelcomeModal();
    }
}, 15000);
</script>

@php
// Clear the session flag so it doesn't show again
session()->forget('show_welcome_modal');
@endphp
@endif
