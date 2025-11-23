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
        <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            @if(isset($quizzes) && $quizzes->count() > 0)
                <div class="grid gap-6 grid-cols-1 sm:grid-cols-2 lg:grid-cols-3">
                    @foreach($quizzes as $quiz)
                        <div class="group relative bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden hover:shadow-md transition-all duration-200 hover:-translate-y-1">
                            <!-- Quiz Image/Icon -->
                            <div class="h-40 bg-gradient-to-r from-blue-500 to-indigo-600 flex items-center justify-center">
                                <svg class="h-16 w-16 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                            </div>
                            
                            <!-- Quiz Content -->
                            <div class="p-6">
                                <div class="flex items-center justify-between mb-3">
                                    <h2 class="text-xl font-semibold text-gray-900 group-hover:text-blue-600 transition-colors">
                                        {{ $quiz->title ?? 'Untitled Quiz' }}
                                    </h2>
                                    @if($quiz->attempts->isNotEmpty())
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                            {{ __('Attempted') }}
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                            {{ __('New') }}
                                        </span>
                                    @endif
                                </div>
                                
                                <p class="text-gray-600 text-sm mb-4 line-clamp-2">
                                    {{ $quiz->description ?? 'Test your knowledge with this quiz.' }}
                                </p>
                                
                                <div class="flex items-center justify-between text-sm text-gray-500 mb-4">
                                    <span class="flex items-center">
                                        <svg class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                        </svg>
                                        {{ $quiz->questions_count ?? 0 }} {{ __('Questions') }}
                                    </span>
                                </div>
                                
                                <div class="pt-4 border-t border-gray-100">
                                    <a href="{{ route('dashboard.quizzes.show', ['locale' => app()->getLocale(), 'quiz' => $quiz->id]) }}" 
                                       class="w-full flex items-center justify-center px-4 py-2 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors duration-200">
                                        {{ $quiz->attempts->isNotEmpty() ? __('Continue') : __('Start Quiz') }}
                                        <svg class="ml-2 -mr-1 w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                                        </svg>
                                    </a>
                                </div>
                            </div>
                            
                            <!-- Subtle hover effect -->
                            <div class="absolute inset-0 border-2 border-transparent group-hover:border-blue-200 rounded-2xl pointer-events-none transition-all duration-200"></div>
                        </div>
                    @endforeach
                </div>

                <!-- Pagination -->
                @if($quizzes->hasPages())
                    <div class="mt-10">
                        {{ $quizzes->links('vendor.pagination.simple-tailwind') }}
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
