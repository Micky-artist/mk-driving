@php
    // Component: resources/views/components/quiz-companion-sidebar.blade.php
    // Props
    $quiz = $quiz ?? null;
    $attempt = $attempt ?? null;
    $compactMode = $compactMode ?? false;
    $showLeaderboard = $showLeaderboard ?? true;
    $showQA = $showQA ?? true;
    $showRobots = $showRobots ?? true;

    $quizId = $quiz ? (is_array($quiz) ? $quiz['id'] : $quiz->id) : null;
    $isGuest = !auth()->check();
@endphp

<div x-data="quizCompanion({
    quizId: {{ json_encode($quizId) }},
    isGuest: {{ $isGuest ? 'true' : 'false' }},
    showLeaderboard: {{ $showLeaderboard ? 'true' : 'false' }},
    showQA: {{ $showQA ? 'true' : 'false' }},
    showRobots: {{ $showRobots ? 'true' : 'false' }}
})" x-init="init()" x-destroy="destroy()"
    class="lg:h-full bg-white dark:bg-gray-800 rounded-xl flex flex-col">

    <!-- Debug Console -->
    <div x-show="false" class="fixed top-4 right-4 bg-black text-white p-4 rounded-lg text-xs font-mono max-w-md z-50">
        <div>📊 Quiz Companion Sidebar Debug</div>
        <div>Robot Messages: <span x-text="(robotMessages || []).length"></span></div>
        <div>Live Activities: <span x-text="(liveActivities || []).length"></span></div>
        <div>Leaderboard: <span x-text="(leaderboard || []).length"></span></div>
        <template x-for="msg in (robotMessages || []).slice(0, 3)" :key="msg.id">
            <div class="text-green-400">• <span x-text="msg.robot_name"></span>: <span x-text="msg.message"></span></div>
        </template>
    </div>

    <!-- Sidebar Content -->
    <div
        class="w-full lg:h-full bg-white dark:bg-gray-800 border-l border-gray-200 dark:border-gray-700 flex flex-col rounded-xl">

        <!-- Header -->
        <div class="bg-gradient-to-r from-blue-500 to-purple-600 text-white p-4 rounded-t-xl">
            <h3 class="font-semibold text-lg">{{ __('quiz.companion.title') }}</h3>
        </div>

        <!-- Feed Content -->
        <div class="flex-1 overflow-y-auto p-4 space-y-3">
            <!-- Unified Activity Feed - All messages sorted by timestamp from backend -->
            <template x-for="message in (robotMessages || [])" :key="message.id">
                <div
                    class="rounded-lg p-3 transition-all duration-200 hover:shadow-sm"
                    :class="{
                        'bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-700': message.type !== 'leaderboard_change',
                        'bg-purple-50 dark:bg-purple-900/20 border border-purple-200 dark:border-purple-700': message.type === 'leaderboard_change'
                    }">
                    <div class="flex items-start gap-3">
                        <div class="flex-shrink-0">
                            <div
                                class="w-8 h-8 rounded-full flex items-center justify-center text-white text-sm font-bold"
                                :class="{
                                    'bg-blue-500': message.type !== 'leaderboard_change',
                                    'bg-purple-500': message.type === 'leaderboard_change'
                                }">
                                <span x-show="message.type !== 'leaderboard_change'">🤖</span>
                                <span x-show="message.type === 'leaderboard_change'">🏆</span>
                            </div>
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center gap-2 mb-1">
                                <span class="font-medium"
                                    :class="{
                                        'text-blue-700 dark:text-blue-300': message.type !== 'leaderboard_change',
                                        'text-purple-700 dark:text-purple-300': message.type === 'leaderboard_change'
                                    }"
                                    x-text="message.robot_name"></span>
                                <span class="text-xs text-gray-500 dark:text-gray-400"
                                    x-text="message.timestamp_human || 'No time'"></span>
                            </div>
                            <p class="text-sm text-gray-700 dark:text-gray-200" x-text="message.message"></p>
                            <!-- Points badge for leaderboard changes -->
                            <div x-show="message.type === 'leaderboard_change' && message.points_change" 
                                 class="mt-2 inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-purple-100 dark:bg-purple-900/30 text-purple-800 dark:text-purple-200">
                                <span x-text="message.points_change"></span>
                            </div>
                        </div>
                    </div>
                </div>
            </template>

            <!-- Other User Activities -->
            <template
                x-for="activity in (liveActivities || []).filter(a => a.type === 'user_answer' && a.user_name).slice(0, 3)"
                :key="activity.user_id + '_' + activity.timestamp">
                <div
                    class="bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-700 rounded-lg p-3 transition-all duration-200 hover:shadow-sm">
                    <div class="flex items-start gap-3">
                        <div class="flex-shrink-0">
                            <div
                                class="w-8 h-8 bg-green-500 rounded-full flex items-center justify-center text-white text-sm font-bold">
                                <span x-text="activity.user_name.charAt(0).toUpperCase()"></span>
                            </div>
                        </div>
                    </div>
                </div>
            </template>

            <!-- Empty State -->
            <div x-show="(robotMessages || []).length === 0"
                class="text-center py-8">
                <div class="text-3xl mb-2">🏁</div>
                <p class="text-gray-500 dark:text-gray-400 text-sm">{{ __('quiz.companion.startQuizToSeeActivity') }}
                </p>
            </div>
        </div>

    </div>
</div>
