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
    
    <!-- Sidebar Content -->
    <div class="w-full lg:h-full bg-white dark:bg-gray-800 border-l border-gray-200 dark:border-gray-700 flex flex-col rounded-xl">
        
        <!-- Feed Content -->
        <div class="flex-1 overflow-y-auto p-4 space-y-3">
            <!-- Robot Activities -->
            <template x-for="message in (robotMessages || [])" :key="message.id">
                <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-700 rounded-lg p-3 transition-all duration-200 hover:shadow-sm">
                    <div class="flex items-start gap-3">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 bg-blue-500 rounded-full flex items-center justify-center text-white text-sm font-bold">
                                🤖
                            </div>
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center gap-2 mb-1">
                                <span class="font-medium text-blue-700 dark:text-blue-300" x-text="message.robot_name"></span>
                                <span class="text-xs text-gray-500 dark:text-gray-400" x-text="message.timestamp_human || 'No time'"></span>
                                                            </div>
                            <p class="text-sm text-gray-700 dark:text-gray-200" x-text="message.message"></p>
                        </div>
                    </div>
                </div>
            </template>

            <!-- Other User Activities -->
            <template x-for="activity in (liveActivities || []).filter(a => a.type === 'user_answer' && a.user_name).slice(0, 3)" :key="activity.user_id + '_' + activity.timestamp">
                <div class="bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-700 rounded-lg p-3 transition-all duration-200 hover:shadow-sm">
                    <div class="flex items-start gap-3">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 bg-green-500 rounded-full flex items-center justify-center text-white text-sm font-bold">
                                <span x-text="activity.user_name.charAt(0).toUpperCase()"></span>
                            </div>
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center gap-2 mb-1">
                                <span class="font-medium text-green-700 dark:text-green-300" x-text="activity.user_name"></span>
                                <span class="text-xs text-gray-500 dark:text-gray-400" x-text="formatTime(activity.timestamp)"></span>
                            </div>
                            <p class="text-sm text-gray-700 dark:text-gray-200">
                                <!-- Activity message will be shown here without badges -->
                            </p>
                        </div>
                    </div>
                </div>
            </template>

            <!-- Leaderboard Changes -->
            <template x-for="(user, index) in (leaderboard || [])" :key="user.id">
                <div class="flex items-center gap-3 p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
                    <div class="w-6 h-6 rounded-full flex items-center justify-center font-bold text-xs"
                         :class="index === 0 ? 'bg-yellow-400 text-white' : index === 1 ? 'bg-gray-300 text-white' : index === 2 ? 'bg-orange-400 text-white' : 'bg-gray-200 text-gray-700'"
                         x-text="index + 1"></div>
                    <div class="flex-1">
                        <div class="font-medium text-sm" x-text="user.name"></div>
                        <div class="text-xs text-gray-500" x-text="user.points + ' ' + __('quiz.companion.points')"></div>
                    </div>
                    <div class="text-right">
                        <div class="font-bold text-sm text-blue-600" x-text="user.leaderboard_score || user.points"></div>
                    </div>
                </div>
            </template>

            <!-- Questions -->
            <template x-for="question in (questions || [])" :key="question.id">
                <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-3">
                    <div class="flex items-start gap-3">
                        <div class="w-8 h-8 bg-blue-500 rounded-full flex items-center justify-center text-white text-sm font-bold">
                            <span x-text="question.user_name.charAt(0).toUpperCase()"></span>
                        </div>
                        <div class="flex-1">
                            <div class="flex items-center gap-2 mb-1">
                                <span class="font-medium text-sm" x-text="question.user_name"></span>
                                <span class="text-xs text-gray-500" x-text="question.created_at"></span>
                            </div>
                            <p class="text-sm text-gray-600 dark:text-gray-200" x-text="question.question"></p>
                        </div>
                    </div>
                </div>
            </template>

            <!-- Empty State -->
            <div x-show="(robotMessages || []).length === 0 && (liveActivities || []).filter(a => a.type === 'user_answer').length === 0 && (leaderboard || []).length === 0 && (questions || []).length === 0" class="text-center py-8">
                <div class="text-3xl mb-2">🏁</div>
                <p class="text-gray-500 dark:text-gray-400 text-sm">{{ __('quiz.companion.startQuizToSeeActivity') }}</p>
            </div>
        </div>

        <!-- Question Input Section -->
        @if(!$isGuest)
        <div class="border-t border-gray-200 dark:border-gray-700 p-4">
            <form @submit.prevent="submitQuestion()">
                <div class="mb-3">
                    <textarea x-model="newQuestion" 
                            placeholder="{{ __('quiz.companion.askQuestionPlaceholder') }}"
                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-sm resize-none focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-gray-100"
                            rows="2"
                            maxlength="500"></textarea>
                </div>
                <button type="submit" 
                        :disabled="!newQuestion.trim() || submittingQuestion"
                        class="w-full bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg text-sm font-medium disabled:opacity-50 transition-colors duration-200">
                    <span x-show="!submittingQuestion">{{ __('quiz.companion.askQuestion') }}</span>
                    <span x-show="submittingQuestion">{{ __('quiz.companion.posting') }}</span>
                </button>
            </form>
        </div>
        @else
        <div class="border-t border-gray-200 dark:border-gray-700 p-4">
            <div class="text-center text-gray-500 text-sm">
                <p>{{ __('quiz.companion.loginToAskQuestions') }}</p>
            </div>
        </div>
        @endif
    </div>
</div>
