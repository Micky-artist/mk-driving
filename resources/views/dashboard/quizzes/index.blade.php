@extends('layouts.app')

@section('title', __('common.my_quizzes'))

@section('content')
    
    <div class="min-h-screen bg-gray-50 dark:bg-gray-900">
        <!-- Header -->
        <div class="bg-white dark:bg-gray-800 border-b border-gray-100 dark:border-gray-700">
            <div class="max-w-7xl mx-auto px-1  sm:px-6 lg:px-8">
                <!-- Title and Count Row -->
                <div class="py-4 flex items-center justify-between">
                    <h1 class="text-xl sm:text-2xl font-semibold text-gray-900 dark:text-white">{{ __('common.my_quizzes') }}</h1>
                    <span class="inline-flex items-center px-2.5 sm:px-3 py-1.5 rounded-full text-xs font-medium bg-blue-100 dark:bg-blue-900/20 text-blue-800 dark:text-blue-300">
                        {{ $quizzes->total() }} {{ __('common.quizzes') }}
                    </span>
                </div>
                
                <!-- Quick Filter Links -->
                <div class="pb-4 flex gap-1.5 sm:gap-2 overflow-x-auto scrollbar-hide">
                    <a href="{{ route('dashboard.quizzes.index', ['locale' => app()->getLocale()]) }}" 
                       class="inline-flex items-center px-3 sm:px-4 py-2 text-xs sm:text-sm font-medium rounded-lg transition-colors whitespace-nowrap flex-shrink-0 {{ 
                           request()->get('see') === null 
                               ? 'bg-blue-600 text-white' 
                               : 'bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-600'
                       }}">
                        <svg class="w-3.5 h-3.5 sm:w-4 sm:h-4 mr-1.5 sm:mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                        {{ __('dashboard.quizzes.all_quizzes') }}
                    </a>
                    <a href="{{ route('dashboard.quizzes.index', ['locale' => app()->getLocale(), 'see' => 'in-progress']) }}" 
                       class="inline-flex items-center px-3 sm:px-4 py-2 text-xs sm:text-sm font-medium rounded-lg transition-colors whitespace-nowrap flex-shrink-0 {{ 
                           request()->get('see') === 'in-progress' 
                               ? 'bg-blue-600 text-white' 
                               : 'bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-600'
                       }}">
                        <svg class="w-3.5 h-3.5 sm:w-4 sm:h-4 mr-1.5 sm:mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        {{ __('dashboard.quizzes.in_progress') }}
                    </a>
                    <a href="{{ route('dashboard.quizzes.index', ['locale' => app()->getLocale(), 'see' => 'completed']) }}" 
                       class="inline-flex items-center px-3 sm:px-4 py-2 text-xs sm:text-sm font-medium rounded-lg transition-colors whitespace-nowrap flex-shrink-0 {{ 
                           request()->get('see') === 'completed' 
                               ? 'bg-blue-600 text-white' 
                               : 'bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-600'
                       }}">
                        <svg class="w-3.5 h-3.5 sm:w-4 sm:h-4 mr-1.5 sm:mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        {{ __('dashboard.quizzes.completed') }}
                    </a>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <main class="max-w-7xl mx-auto px-2 sm:px-6 lg:px-8 py-6">
            @if(isset($quizzes) && $quizzes->count() > 0)
                <!-- Quiz Stats Overview -->
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
                    <div class="bg-white dark:bg-gray-800 rounded-xl p-4 border border-gray-200 dark:border-gray-700">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm text-gray-600 dark:text-gray-400">{{ __('dashboard.stats.total_quizzes') }}</p>
                                <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $quizzes->total() }}</p>
                            </div>
                            <div class="w-10 h-10 bg-blue-100 dark:bg-blue-900/20 rounded-lg flex items-center justify-center">
                                <svg class="w-5 h-5 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                            </div>
                        </div>
                    </div>
                    
                    <div class="bg-white dark:bg-gray-800 rounded-xl p-4 border border-gray-200 dark:border-gray-700">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm text-gray-600 dark:text-gray-400">{{ __('dashboard.quizzes.completed') }}</p>
                                <p class="text-2xl font-bold text-green-600 dark:text-green-400">{{ $stats['completed_count'] }}</p>
                            </div>
                            <div class="w-10 h-10 bg-green-100 dark:bg-green-900/20 rounded-lg flex items-center justify-center">
                                <svg class="w-5 h-5 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </div>
                        </div>
                    </div>
                    
                    <div class="bg-white dark:bg-gray-800 rounded-xl p-4 border border-gray-200 dark:border-gray-700">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm text-gray-600 dark:text-gray-400">{{ __('dashboard.quizzes.in_progress') }}</p>
                                <p class="text-2xl font-bold text-yellow-600 dark:text-yellow-400">{{ $stats['in_progress_count'] }}</p>
                            </div>
                            <div class="w-10 h-10 bg-yellow-100 dark:bg-yellow-900/20 rounded-lg flex items-center justify-center">
                                <svg class="w-5 h-5 text-yellow-600 dark:text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </div>
                        </div>
                    </div>
                    
                    <div class="bg-white dark:bg-gray-800 rounded-xl p-4 border border-gray-200 dark:border-gray-700">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm text-gray-600 dark:text-gray-400">{{ __('dashboard.quizzes.average_score') }}</p>
                                <p class="text-2xl font-bold text-purple-600 dark:text-purple-400">{{ $stats['average_score'] }}%</p>
                            </div>
                            <div class="w-10 h-10 bg-purple-100 dark:bg-purple-900/20 rounded-lg flex items-center justify-center">
                                <svg class="w-5 h-5 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Completed Quizzes Section - Only show when currentStatus is completed -->
                @if(isset($currentStatus) && $currentStatus === 'completed')
                    <div class="mb-12">
                        <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-6">
                            {{ __('dashboard.quizzes.completed_quizzes') }}
                            <span class="ml-2 text-sm font-normal text-gray-500 dark:text-gray-400">
                                ({{ isset($availableCompletedQuizzes) ? $availableCompletedQuizzes->count() : 0 }} {{ __('dashboard.quizzes.available_text') }}, {{ isset($unavailableCompletedQuizzes) ? $unavailableCompletedQuizzes->count() : 0 }} {{ __('dashboard.quizzes.view_only') }})
                            </span>
                        </h2>
                        
                        @if(isset($availableCompletedQuizzes) && $availableCompletedQuizzes->count() > 0)
                            <!-- Available Completed Quizzes -->
                            <div class="mb-8">
                                <h3 class="text-lg font-medium text-gray-800 dark:text-gray-200 mb-4">
                                    {{ __('dashboard.quizzes.available_for_retake') }}
                                    <span class="ml-2 text-sm font-normal text-green-600">
                                        ({{ $availableCompletedQuizzes->count() }})
                                    </span>
                                </h3>
                                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                                    @foreach($availableCompletedQuizzes as $quiz)
                                        <x-quiz.quiz-card :quiz="$quiz" />
                                    @endforeach
                                </div>
                            </div>
                        @endif
                        
                        @if(isset($unavailableCompletedQuizzes) && $unavailableCompletedQuizzes->count() > 0)
                            <!-- Unavailable Completed Quizzes -->
                            <div class="bg-gray-50 dark:bg-gray-800/50 rounded-xl p-6 border border-gray-200 dark:border-gray-700">
                                <div class="flex items-center justify-between mb-6">
                                    <h3 class="text-lg font-medium text-gray-800 dark:text-gray-200">
                                        {{ __('dashboard.quizzes.view_only_completed') }}
                                        <span class="ml-2 text-sm font-normal text-orange-600">
                                            ({{ $unavailableCompletedQuizzes->count() }})
                                        </span>
                                    </h3>
                                    <a href="{{ route('plans', ['locale' => app()->getLocale()]) }}" 
                                       class="inline-flex items-center px-4 py-2 bg-blue-600 text-white font-medium rounded-lg hover:bg-blue-700 transition-colors duration-200 shadow-md hover:shadow-lg">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        {{ __('dashboard.quizzes.upgrade_to_retake') }}
                                    </a>
                                </div>
                                
                                <p class="text-gray-600 dark:text-gray-300 mb-6">
                                    {{ __('dashboard.quizzes.completed_upgrade_message') }}
                                </p>
                                
                                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                                    @foreach($unavailableCompletedQuizzes as $quiz)
                                        <x-quiz.quiz-card :quiz="$quiz" />
                                    @endforeach
                                </div>
                            </div>
                        @endif
                        
                        <!-- Pagination for completed quizzes -->
                        @if(isset($quizzes) && $quizzes->hasPages())
                            <div class="mt-10 flex flex-col sm:flex-row items-center justify-between gap-4">
                                <div class="text-sm text-gray-600 dark:text-gray-400">
                                    {{ __('dashboard.quizzes.showing', ['from' => $quizzes->firstItem(), 'to' => $quizzes->lastItem(), 'total' => $quizzes->total()]) }}
                                </div>
                                <div class="flex items-center gap-2">
                                    {{ $quizzes->links() }}
                                </div>
                            </div>
                        @endif
                        
                        @if((!isset($availableCompletedQuizzes) || $availableCompletedQuizzes->count() === 0) && (!isset($unavailableCompletedQuizzes) || $unavailableCompletedQuizzes->count() === 0))
                            <!-- Empty State for Completed -->
                            <div class="text-center py-16 bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700">
                                <svg class="mx-auto h-16 w-16 text-gray-400 dark:text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <h3 class="mt-2 text-lg font-medium text-gray-900 dark:text-white">{{ __('dashboard.quizzes.no_completed_quizzes') }}</h3>
                                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                                    {{ __('dashboard.quizzes.no_completed_message') }}
                                </p>
                            </div>
                        @endif
                    </div>
                @endif
                
                <!-- Available Quizzes Section - Hide when currentStatus is completed -->
                @if(!isset($currentStatus) || $currentStatus !== 'completed')
                    @if(isset($quizzes) && $quizzes->count() > 0)
                    <div class="mb-12">
                        <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-6">
                            {{ __('dashboard.quizzes.available_quizzes') }}
                            <span class="ml-2 text-sm font-normal text-gray-500 dark:text-gray-400">
                                ({{ $quizzes->total() }})
                            </span>
                        </h2>
                        
                        <!-- Enhanced Quizzes Grid -->
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                            @foreach($quizzes as $quiz)
                                <x-quiz.quiz-card :quiz="$quiz" />
                            @endforeach
                        </div>

                        <!-- Enhanced Pagination -->
                        @if($quizzes->hasPages())
                            <div class="mt-10 flex flex-col sm:flex-row items-center justify-between gap-4">
                                <div class="text-sm text-gray-600 dark:text-gray-400">
                                    {{ __('dashboard.quizzes.showing', ['from' => $quizzes->firstItem(), 'to' => $quizzes->lastItem(), 'total' => $quizzes->total()]) }}
                                </div>
                                <div class="flex items-center gap-2">
                                    {{ $quizzes->links() }}
                                </div>
                            </div>
                        @endif
                    </div>
                @endif
                @endif

                <!-- Locked Quizzes Section -->
                @if(isset($lockedQuizzes) && $lockedQuizzes->count() > 0 && !auth()->user()->isAdmin())
                    <div class="bg-gray-50 dark:bg-gray-800/50 rounded-xl p-6 border border-gray-200 dark:border-gray-700">
                        <div class="flex items-center justify-between mb-6">
                            <h2 class="text-xl font-semibold text-gray-900 dark:text-white">
                                {{ __('dashboard.quizzes.purchase_required') }}
                                <span class="ml-2 text-sm font-normal text-gray-500 dark:text-gray-400">
                                    ({{ $lockedQuizzes->count() }})
                                </span>
                            </h2>
                            <a href="{{ route('plans', ['locale' => app()->getLocale()]) }}" 
                               class="inline-flex items-center px-4 py-2 bg-blue-600 text-white font-medium rounded-lg hover:bg-blue-700 transition-colors duration-200 shadow-md hover:shadow-lg">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                {{ __('dashboard.quizzes.purchase_plan') }}
                            </a>
                        </div>
                        
                        <p class="text-gray-600 dark:text-gray-300 mb-6">
                            {{ __('dashboard.quizzes.purchase_message') }}
                        </p>
                        
                        <!-- Locked Quizzes Grid -->
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                            @foreach($lockedQuizzes->take(8) as $quiz)
                                <x-quiz.quiz-card :quiz="$quiz" />
                            @endforeach
                        </div>
                    </div>
                @endif
            @else
                <!-- Empty State -->
                <div class="text-center py-16">
                    <svg class="mx-auto h-16 w-16 text-gray-400 dark:text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    <h3 class="mt-2 text-lg font-medium text-gray-900 dark:text-white">{{ __('No quizzes available') }}</h3>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
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
    
    .scrollbar-hide {
        -ms-overflow-style: none;
        scrollbar-width: none;
    }
    
    .scrollbar-hide::-webkit-scrollbar {
        display: none;
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
