@extends('layouts.dashboard')

@section('title', 'Quizzes')

@section('dashboard-content')
    <div class="container mx-auto px-4 py-6">
        <h1 class="text-2xl font-bold text-gray-800 mb-6">Quizzes</h1>
        
        @if(isset($quizzes) && $quizzes->count() > 0)
            <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
                @foreach($quizzes as $quiz)
                    <div class="bg-white rounded-lg shadow-md overflow-hidden border border-gray-200 hover:shadow-lg transition-shadow duration-300">
                        <div class="p-6">
                            <h2 class="text-xl font-semibold text-gray-800 mb-2">{{ $quiz->title ?? 'No title' }}</h2>
                            <p class="text-gray-600 mb-4">{{ $quiz->description ?? 'No description' }}</p>
                            <div class="flex items-center justify-between">
                                <span class="text-sm text-gray-500">
                                    {{ $quiz->questions_count ?? 0 }} questions
                                </span>
                                @if($quiz->attempts->isNotEmpty())
                                    <span class="text-sm font-medium text-green-600">
                                        Attempted
                                    </span>
                                @endif
                            </div>
                        </div>
                        <div class="bg-gray-50 px-6 py-3 flex justify-end">
                            <a href="{{ route('dashboard.quizzes.show', ['locale' => app()->getLocale(), 'quiz' => $quiz->id]) }}" 
                               class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition">
                                Start Quiz
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="mt-8">
                {{ $quizzes->links() }}
            </div>
        @else
            <div class="bg-white rounded-lg shadow p-6 text-center">
                <p class="text-gray-600">No quizzes available at the moment.</p>
            </div>
        @endif
    </div>
@endsection
