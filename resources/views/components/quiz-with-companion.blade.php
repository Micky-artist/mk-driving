@php
    // Component: resources/views/components/quiz-with-companion.blade.php
    // Props
    $quiz = $quiz ?? null;
    $attempt = $attempt ?? null;
    $showHeader = $showHeader ?? true;
    $compactMode = $compactMode ?? false;
    $allowNavigation = $allowNavigation ?? true;
    $showCompanion = $showCompanion ?? true;
@endphp

<div class="rounded-xl bg-gradient-to-br from-gray-50 to-gray-100 dark:from-gray-900 dark:to-gray-800 lg:p-6 p-0">
    <!-- Desktop Layout: Side-by-Side -->
    <div class="hidden lg:grid grid-cols-3 lg:h-[calc(100vh-6rem)] w-full gap-4">
        <!-- Main Quiz Area -->
        <div class="col-span-2 min-h-0">
            <x-unified-quiz-taker 
                :quiz="$quiz"
                :attempt="$attempt"
                :showHeader="$showHeader"
                :compactMode="$compactMode"
                :allowNavigation="$allowNavigation" />
        </div>

        <!-- Companion Sidebar (Desktop) -->
        @if($showCompanion)
        <div class="col-span-1 min-h-0">
            <x-quiz-companion-sidebar 
                :quiz="$quiz"
                :attempt="$attempt"
                :showLeaderboard="true"
                :showQA="true"
                :showRobots="true" />
        </div>
        @endif
    </div>

    <!-- Mobile Layout: Both components in full height -->
    <div class="lg:hidden">
        <!-- Quiz Taker (Full height on top) -->
        <div>
            <x-unified-quiz-taker 
                :quiz="$quiz"
                :attempt="$attempt"
                :showHeader="$showHeader"
                :compactMode="$compactMode"
                :allowNavigation="$allowNavigation" />
        </div>

        <!-- Companion Sidebar (Full height below) -->
        @if($showCompanion)
        <div class="bg-white dark:bg-gray-800 border-t border-gray-200 dark:border-gray-700">
            <x-quiz-companion-sidebar 
                :quiz="$quiz"
                :attempt="$attempt"
                :showLeaderboard="true"
                :showQA="true"
                :showRobots="true" />
        </div>
        @endif
    </div>
</div>

<script>
    // Communication between quiz taker and companion sidebar
    document.addEventListener('alpine:initialized', () => {
        // Listen for robot responses from quiz component
        window.addEventListener('robotResponses', (event) => {
            // Dispatch to companion sidebar
            window.dispatchEvent(new CustomEvent('robotCompanionUpdate', {
                detail: {
                    robotResponses: event.detail.robotResponses
                }
            }));
        });

        // Listen for quiz completion
        window.addEventListener('quizCompleted', () => {
            window.dispatchEvent(new CustomEvent('quizCompleted', {
                detail: { quizId: {{ $quiz->id ?? 'null' }} }
            }));
        });
    });
</script>
