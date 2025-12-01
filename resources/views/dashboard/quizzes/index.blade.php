@extends('layouts.dashboard')

@section('title', __('My Quizzes'))

@section('dashboard-content')
    <div class="min-h-screen bg-gray-50">
        <!-- Header -->
        <div class="bg-white border-b border-gray-100">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="py-6 flex items-center justify-between">
                    <h1 class="text-2xl font-semibold text-gray-900">{{ __('My Quizzes') }}</h1>
                    <div class="flex items-center space-x-2">
                        <span class="inline-flex items-center px-3 py-1.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                            {{ $quizzes->total() }} {{ __('Quizzes') }}
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <main class="max-w-7xl mx-auto px-4 sm:px-2 py-6">
            @if(isset($quizzes) && $quizzes->count() > 0)
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach($quizzes as $quiz)
                        @php
                            // Calculate progress for the quiz
                            $totalQuestions = $quiz->questions_count ?? 0;
                            $attemptedQuestions = 0;
                            
                            // Safely get the number of attempted questions
                            if ($quiz->attempts->isNotEmpty()) {
                                $latestAttempt = $quiz->attempts->first();
                                $attemptedQuestions = isset($latestAttempt->answers) ? $latestAttempt->answers->count() : 0;
                            }
                            
                            $progressPercent = $totalQuestions > 0 ? min(100, round(($attemptedQuestions / $totalQuestions) * 100)) : 0;
                            
                            // Define gradient based on progress
                            $gradient = 'from-blue-600 to-blue-700';
                            if ($progressPercent > 50 && $progressPercent < 80) {
                                $gradient = 'from-yellow-500 to-yellow-600';
                            } elseif ($progressPercent >= 80) {
                                $gradient = 'from-green-500 to-green-600';
                            }
                            
                            // Get the latest attempt
                            $latestAttempt = $quiz->attempts->sortByDesc('created_at')->first();
                            $score = $latestAttempt ? $latestAttempt->score_percentage : 0;
                        @endphp
                        
                        <div class="bg-white rounded-lg shadow-md overflow-hidden border border-gray-200 hover:shadow-lg transition-all duration-200 transform hover:-translate-y-1 flex flex-col">
                            <!-- Card Header -->
                            <div class="bg-gradient-to-r {{ $gradient }} p-5 text-white flex-shrink-0">
                                <div class="flex justify-between items-start gap-3 mb-4">
                                    <div class="flex-1 min-w-0">
                                        <h3 class="text-lg font-bold truncate">{{ $quiz->title ?? 'Untitled Quiz' }}</h3>
                                        @if(!empty($quiz->description))
                                            <p class="text-sm text-white/90 mt-1 line-clamp-2">
                                                {{ $quiz->description }}
                                            </p>
                                        @endif
                                    </div>
                                    <div class="bg-white/20 rounded-lg p-2 text-center flex-shrink-0" style="min-width: 80px;">
                                        <div class="text-xs text-white/90 whitespace-nowrap">
                                            {{ $quiz->attempts->isNotEmpty() ? __('dashboard.quizzes.average_score') : __('dashboard.quizzes.attempts') }}
                                        </div>
                                        <div class="text-2xl font-bold leading-tight mt-1">
                                            @if($quiz->attempts->isNotEmpty())
                                                {{ round($score) }}%
                                            @else
                                                0
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-white/20">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                    </svg>
                                    {{ $totalQuestions }} {{ trans_choice('dashboard.questions', $totalQuestions) }}
                                </div>
                            </div>
                            
                            <!-- Progress Section -->
                            <div class="p-5 bg-gradient-to-b from-gray-50 to-white border-b border-gray-100 flex-shrink-0">
                                <div class="flex justify-between items-center text-sm mb-2">
                                    <span class="text-gray-600 font-medium">{{ __('dashboard.quizzes.progress') }}</span>
                                    <span class="font-semibold text-gray-900">{{ $progressPercent }}%</span>
                                </div>
                                <div class="w-full bg-gray-200 rounded-full h-2.5 overflow-hidden">
                                    <div class="bg-gradient-to-r {{ $gradient }} h-2.5 rounded-full transition-all duration-500 ease-out" 
                                         style="width: {{ $progressPercent }}%;"></div>
                                </div>
                                <p class="text-xs text-gray-500 mt-2 flex items-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    {{ __('dashboard.time_limit', ['minutes' => 20]) }}
                                </p>
                            </div>
                            
                            <!-- Status Section -->
                            <div class="p-5 bg-white flex-shrink-0">
                                <div class="flex justify-between items-center">
                                    <div class="min-w-0 flex-1">
                                        <p class="text-xs text-gray-500 uppercase tracking-wider mb-1">{{ __('dashboard.status') }}</p>
                                        <p class="font-semibold text-gray-900 truncate">
                                            @if($quiz->attempts->isNotEmpty())
                                                {{ __('dashboard.quizzes.in_progress') }}
                                            @else
                                                {{ __('dashboard.quizzes.not_started') }}
                                            @endif
                                        </p>
                                    </div>
                                    <span class="inline-flex items-center px-3 py-1.5 rounded-full text-xs font-semibold flex-shrink-0 ml-3 {{ 
                                        $quiz->attempts->isNotEmpty() ? 'bg-blue-100 text-blue-800' : 'bg-gray-100 text-gray-800'
                                    }}">
                                        <span class="w-1.5 h-1.5 rounded-full mr-1.5 {{ 
                                            $quiz->attempts->isNotEmpty() ? 'bg-blue-500' : 'bg-gray-500'
                                        }}"></span>
                                        @if($quiz->attempts->isNotEmpty())
                                            {{ __('dashboard.quizzes.in_progress') }}
                                        @else
                                            {{ __('dashboard.quizzes.not_started') }}
                                        @endif
                                    </span>
                                </div>
                                
                                <div class="mt-4">
                                @php
                                    $user = auth()->user();
                                    $hasActiveSubscription = $user->activeSubscriptions()->exists();
                                    $canRetake = true;
                                    $nextRetakeTime = null;
                                    
                                    // Check if user can retake the quiz
                                    if ($quiz->attempts->isNotEmpty() && !$hasActiveSubscription) {
                                        $lastAttempt = $quiz->attempts->where('completed_at', '!==', null)->sortByDesc('completed_at')->first();
                                        if ($lastAttempt) {
                                            $canRetake = $lastAttempt->completed_at->addHours(24)->isPast();
                                            if (!$canRetake) {
                                                $nextRetakeTime = $lastAttempt->completed_at->addHours(24);
                                            }
                                        }
                                    }
                                @endphp
                                
                                @php
                                    $buttonText = $quiz->attempts->isNotEmpty() ? __('Continue Quiz') : __('Start Quiz');
                                    $buttonClass = 'w-full flex items-center justify-center px-4 py-2 border border-transparent rounded-lg text-sm font-medium text-white focus:outline-none focus:ring-2 focus:ring-offset-2 transition-colors duration-200';
                                    
                                    if ($canRetake || $hasActiveSubscription) {
                                        $buttonClass .= ' bg-blue-600 hover:bg-blue-700 focus:ring-blue-500';
                                    } else {
                                        $buttonClass .= ' bg-blue-600 hover:bg-blue-700 focus:ring-blue-500 opacity-75';
                                    }
                                @endphp
                                
                                <button type="button" 
                                        onclick="event.preventDefault(); @if($canRetake || $hasActiveSubscription) window.location.href='{{ route('dashboard.quizzes.show', ['locale' => app()->getLocale(), 'quiz' => $quiz->id]) }}' @else showRetakeRestriction('{{ $nextRetakeTime ? $nextRetakeTime->diffForHumans() : '' }}') @endif"
                                        class="{{ $buttonClass }}">
                                    {{ $buttonText }}
                                    <svg class="ml-2 -mr-1 w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                                    </svg>
                                </button>
                            </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Pagination -->
                @if($quizzes->hasPages())
                    <div class="mt-10">
                        {{ $quizzes->links() }}
                    </div>
                @endif
            @else
                <!-- Empty State -->
                <div class="text-center py-16">
                    <svg class="mx-auto h-16 w-16 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    <h3 class="mt-2 text-lg font-medium text-gray-900">{{ __('No quizzes available') }}</h3>
                    <p class="mt-1 text-sm text-gray-500">
                        {{ __('There are no quizzes available at the moment. Please check back later.') }}
                    </p>
                </div>
            @endif
        </main>
    </div>
@endsection

@push('styles')
<style>
    .line-clamp-2 {
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }
    
    .shadow-soft {
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.02), 0 4px 6px -2px rgba(0, 0, 0, 0.01);
    }
    
    .transition-all {
        transition-property: all;
        transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
        transition-duration: 200ms;
    }
</style>
@endpush

@push('scripts')
<script>
    // Function to show retake restriction popup
    function showRetakeRestriction(timeUntilRetake) {
        Swal.fire({
            title: '{{ __("quiz.resetRestricted") }}',
            html: `
                <div class="text-left">
                    <div class="bg-gradient-to-r from-blue-50 to-blue-50 border-l-4 border-blue-500 p-4 rounded-lg mb-4">
                        <div class="flex items-start">
                            <div class="flex-shrink-0 mt-0.5">
                                <svg class="h-5 w-5 text-blue-600" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                                </svg>
                            </div>
                            <div class="ml-3">
                                <h3 class="text-sm font-medium text-gray-900">{{ __('quiz.quizLimitReached') }}</h3>
                                <div class="mt-1 text-sm text-gray-700">
                                    <p>{{ __('quiz.quizLimitMessage', ['time' => '__TIME_PLACEHOLDER__']) }}</p>
                                    <p class="mt-2 font-medium text-blue-700">{{ __('quiz.upgradeForUnlimited') }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="mt-4 p-4 bg-gray-50 rounded-lg border border-gray-200">
                        <h4 class="text-sm font-medium text-gray-900 mb-2">{{ __('quiz.upgradeBenefits') ?? 'Premium Benefits' }}:</h4>
                        <ul class="text-sm text-gray-700 space-y-1.5">
                            <li class="flex items-start">
                                <svg class="h-5 w-5 text-green-500 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                                {{ __('quiz.unlimitedAttempts') ?? 'Unlimited quiz attempts' }}
                            </li>
                            <li class="flex items-start">
                                <svg class="h-5 w-5 text-green-500 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                                {{ __('quiz.noWaitingTime') ?? 'No waiting time between attempts' }}
                            </li>
                            <li class="flex items-start">
                                <svg class="h-5 w-5 text-green-500 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                                {{ __('quiz.fullAccess') ?? 'Full access to all premium features' }}
                            </li>
                        </ul>
                    </div>
                </div>
            `.replace('__TIME_PLACEHOLDER__', timeUntilRetake),
            icon: 'info',
            showCloseButton: true,
            showCancelButton: true,
            showConfirmButton: true,
            confirmButtonText: '{{ __("quiz.upgradeNow") }}',
            cancelButtonText: '{{ __("quiz.cancel") }}',
            focusConfirm: false,
            customClass: {
                confirmButton: 'w-full sm:w-auto bg-gradient-to-r from-orange-500 to-orange-600 hover:from-orange-600 hover:to-orange-700 text-white font-medium py-2.5 px-6 rounded-lg transition-all duration-200 shadow-md hover:shadow-lg',
                cancelButton: 'w-full sm:w-auto mt-3 sm:mt-0 sm:ml-3 bg-white border border-gray-300 text-gray-700 font-medium py-2.5 px-6 rounded-lg hover:bg-gray-50 transition-all duration-200',
                closeButton: 'text-gray-400 hover:text-gray-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 rounded-md',
                popup: 'p-6 sm:p-8',
                title: 'text-xl font-bold text-gray-900 mb-4',
                htmlContainer: 'text-left',
            },
            buttonsStyling: false,
            showClass: {
                popup: 'animate-fade-in-up animate-duration-200'
            },
            hideClass: {
                popup: 'animate-fade-out-down animate-duration-200'
            }
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = '{{ url(app()->getLocale() . "/plans") }}';
            }
        });
    }
</script>
@endpush
