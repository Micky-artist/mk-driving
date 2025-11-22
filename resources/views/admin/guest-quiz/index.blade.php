@extends('admin.layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Guest Quiz Management</h1>
            <p class="text-gray-600">Manage which quiz is available to guest users</p>
        </div>
    </div>

    @if (session('success'))
        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6" role="alert">
            <p>{{ session('success') }}</p>
        </div>
    @endif

    @if (session('error'))
        <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6" role="alert">
            <p>{{ session('error') }}</p>
        </div>
    @endif

    @if (session('info'))
        <div class="bg-blue-100 border-l-4 border-blue-500 text-blue-700 p-4 mb-6" role="alert">
            <p>{{ session('info') }}</p>
        </div>
    @endif

    <!-- Current Guest Quiz Info -->
    @if($currentGuestQuiz)
        <div class="bg-blue-50 border-l-4 border-blue-500 p-4 mb-6">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-blue-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-sm text-blue-700">
                        <strong>Current Guest Quiz:</strong> {{ $currentGuestQuiz->title['en'] }}
                        @if($currentGuestQuiz->questions_count > 0)
                            <span class="ml-2 text-sm">
                                ({{ $currentGuestQuiz->questions_count }} questions)
                            </span>
                        @endif
                    </p>
                </div>
            </div>
        </div>
    @else
        <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 mb-6">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-yellow-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-sm text-yellow-700">
                        No guest quiz is currently set. Please select a quiz to make it available for guest users.
                    </p>
                </div>
            </div>
        </div>
    @endif

    <!-- Instructions -->
    <div class="bg-white shadow overflow-hidden sm:rounded-lg mb-6">
        <div class="px-4 py-5 sm:px-6">
            <h3 class="text-lg leading-6 font-medium text-gray-900 flex items-center">
                <svg class="h-5 w-5 text-blue-600 mr-2" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.643.304 1.254.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                </svg>
                Instructions
            </h3>
        </div>
        <div class="border-t border-gray-200 px-4 py-5 sm:p-0">
            <dl class="sm:divide-y sm:divide-gray-200">
                <div class="py-4 sm:py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                    <dt class="text-sm font-medium text-gray-500">Guest Quiz</dt>
                    <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                        <ul class="list-disc pl-5 space-y-1">
                            <li>Only one quiz can be set as the guest quiz at a time</li>
                            <li>When you select a new quiz as guest quiz, the previous one will be automatically unset</li>
                            <li>The guest quiz will be available to users who haven't logged in yet</li>
                            <li>Guest users can only attempt this quiz once</li>
                        </ul>
                    </dd>
                </div>
            </dl>
        </div>
    </div>

    <!-- Quizzes List -->
    <div class="bg-white shadow overflow-hidden sm:rounded-lg">
        <div class="px-4 py-5 sm:px-6">
            <h3 class="text-lg leading-6 font-medium text-gray-900">Available Quizzes</h3>
            <p class="mt-1 max-w-2xl text-sm text-gray-500">Select a quiz to make it available to guest users</p>
        </div>
        <div class="border-t border-gray-200">
            <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6 p-6">
                @forelse($quizzes as $quiz)
                    <div class="border rounded-lg overflow-hidden transition-all duration-200 hover:shadow-lg {{ $quiz->is_guest_quiz ? 'ring-2 ring-blue-500 bg-blue-50' : '' }}">
                        <div class="p-5">
                            <div class="flex justify-between items-start">
                                <div class="flex flex-col gap-2">
                                    <div class="flex gap-2">
                                        @if($quiz->is_guest_quiz)
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                                <svg class="h-3 w-3 mr-1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                                </svg>
                                                Guest Quiz
                                            </span>
                                        @endif
                                        @if(!$quiz->is_active)
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                                Inactive
                                            </span>
                                        @endif
                                    </div>
                                    @if($quiz->subscriptionPlan)
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                                            {{ $quiz->subscriptionPlan->name['en'] }}
                                        </span>
                                    @endif
                                </div>
                            </div>
                            
                            <h3 class="mt-2 text-lg font-medium text-gray-900">
                                {{ $quiz->title['en'] }}
                            </h3>
                            
                            <p class="mt-1 text-sm text-gray-500 line-clamp-3">
                                {{ $quiz->description['en'] ?? 'No description available.' }}
                            </p>
                            
                            <div class="mt-4 flex justify-between items-center text-sm text-gray-500">
                                <span>{{ $quiz->questions_count }} questions</span>
                                <span>{{ $quiz->is_active ? 'Active' : 'Inactive' }}</span>
                            </div>
                            
                            <div class="mt-4">
                                <form action="{{ route('admin.guest-quiz.set', $quiz->id) }}" method="POST">
                                    @csrf
                                    <button type="submit" 
                                            class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white {{ $quiz->is_guest_quiz ? 'bg-blue-600 hover:bg-blue-700' : 'bg-gray-600 hover:bg-gray-700' }} focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
                                            {{ !$quiz->is_active ? 'disabled' : '' }}
                                            onclick="return confirm('Are you sure you want to set this as the guest quiz?')">
                                        {{ $quiz->is_guest_quiz ? 'Current Guest Quiz' : 'Set as Guest Quiz' }}
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-span-3 text-center py-10">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <h3 class="mt-2 text-sm font-medium text-gray-900">No quizzes found</h3>
                        <p class="mt-1 text-sm text-gray-500">Create a quiz first to set it as the guest quiz.</p>
                        <div class="mt-6">
                            <a href="{{ route('admin.quizzes.create') }}" class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                <svg class="-ml-1 mr-2 h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z" clip-rule="evenodd" />
                                </svg>
                                New Quiz
                            </a>
                        </div>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection
