@extends('admin.layouts.app')

@section('title', 'Review Quiz - Complete Creation')

@push('styles')
<style>
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }
    
    .fade-in {
        animation: fadeIn 0.6s ease-out forwards;
    }
    
    .fade-in-delay-1 { animation-delay: 0.1s; }
    .fade-in-delay-2 { animation-delay: 0.2s; }
    .fade-in-delay-3 { animation-delay: 0.3s; }
    
    .question-card {
        transition: all 0.3s ease;
    }
    
    .question-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 25px rgba(0,0,0,0.1);
    }
    
    .image-preview {
        max-width: 200px;
        max-height: 150px;
        object-fit: cover;
        border-radius: 8px;
    }
    
    .correct-option {
        background-color: #10b981;
        color: white;
    }
    
    .option-letter {
        width: 32px;
        height: 32px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 50%;
        font-weight: bold;
        font-size: 14px;
    }
</style>
@endpush

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="mb-8">
        <div class="flex items-center justify-between mb-4">
            <div>
                <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-2">
                    Review Your Road Theory Test
                </h1>
                <p class="text-gray-600 dark:text-gray-400">
                    Review all 20 questions before final submission. You can edit any question if needed.
                </p>
            </div>
            
            <div class="text-right">
                <div class="text-2xl font-bold text-green-600 dark:text-green-400">
                    20/20 Questions Complete
                </div>
                <div class="text-sm text-gray-500 dark:text-gray-400">
                    Ready for submission
                </div>
            </div>
        </div>

        <!-- Progress Bar -->
        <div class="mb-6">
            <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-3">
                <div class="bg-gradient-to-r from-green-600 to-green-700 h-3 rounded-full transition-all duration-500 ease-out" 
                     style="width: 100%"></div>
            </div>
        </div>
    </div>

    <!-- Error Alert -->
    @if (session('error'))
        <div class="mb-6 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg p-4 fade-in fade-in-delay-1">
            <div class="flex items-center">
                <svg class="w-5 h-5 text-red-600 dark:text-red-400 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <p class="text-red-800 dark:text-red-200 font-medium">{{ session('error') }}</p>
            </div>
        </div>
    @endif

    <!-- Success Alert -->
    @if (session('success'))
        <div class="mb-6 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-lg p-4 fade-in fade-in-delay-1">
            <div class="flex items-center">
                <svg class="w-5 h-5 text-green-600 dark:text-green-400 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <p class="text-green-800 dark:text-green-200 font-medium">{{ session('success') }}</p>
            </div>
        </div>
    @endif

    <!-- Quiz Completion Form -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 fade-in fade-in-delay-2 mb-6">
        <form action="{{ route('admin.quizzes.create.complete') }}" method="POST" class="p-6">
            @csrf
            
            <!-- Quiz Basic Information -->
            <div class="mb-8">
                <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-6 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    Quiz Information
                </h2>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Quiz Title - English -->
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Quiz Title (English) *
                        </label>
                        <input type="text" 
                               name="title" 
                               value="{{ old('title') }}"
                               required
                               class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white transition-all duration-200"
                               placeholder="e.g., Rwanda Road Rules Test">
                        @error('title')
                            <p class="mt-2 text-sm text-red-600 dark:text-red-400 flex items-center">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <!-- Quiz Title - Rwanda -->
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Quiz Title (Kinyarwanda) *
                        </label>
                        <input type="text" 
                               name="title_rw" 
                               value="{{ old('title_rw') }}"
                               required
                               class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white transition-all duration-200"
                               placeholder="e.g., Umwitozo w'umuhanda">
                        @error('title_rw')
                            <p class="mt-2 text-sm text-red-600 dark:text-red-400 flex items-center">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <!-- Description - English -->
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Description (English)
                        </label>
                        <textarea name="description" 
                                  rows="3"
                                  class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white transition-all duration-200 resize-none"
                                  placeholder="Brief description of what this quiz covers...">{{ old('description') }}</textarea>
                        @error('description')
                            <p class="mt-2 text-sm text-red-600 dark:text-red-400 flex items-center">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <!-- Description - Rwanda -->
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Description (Kinyarwanda)
                        </label>
                        <textarea name="description_rw" 
                                  rows="3"
                                  class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white transition-all duration-200 resize-none"
                                  placeholder="Inyandiko y'ikiganiro...">{{ old('description_rw') }}</textarea>
                        @error('description_rw')
                            <p class="mt-2 text-sm text-red-600 dark:text-red-400 flex items-center">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <!-- Time Limit -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Time Limit (minutes)
                        </label>
                        <input type="number" 
                               name="time_limit" 
                               value="{{ old('time_limit', 20) }}" 
                               min="1"
                               class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white transition-all duration-200"
                               placeholder="20">
                        @error('time_limit')
                            <p class="mt-2 text-sm text-red-600 dark:text-red-400 flex items-center">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <!-- Passing Score -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Passing Score (%)
                        </label>
                        <input type="number" 
                               name="passing_score" 
                               value="{{ old('passing_score', 60) }}" 
                               min="0" 
                               max="100"
                               class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white transition-all duration-200"
                               placeholder="60">
                        @error('passing_score')
                            <p class="mt-2 text-sm text-red-600 dark:text-red-400 flex items-center">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <!-- Subscription Plans -->
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Assign to Subscription Plans
                        </label>
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3">
                            @foreach(\App\Models\SubscriptionPlan::where('is_active', true)->get() as $plan)
                                <div class="flex items-center p-3 bg-gray-50 dark:bg-gray-700 rounded-lg border border-gray-200 dark:border-gray-600">
                                    <input type="checkbox" 
                                           name="subscription_plans[]" 
                                           value="{{ $plan->id }}"
                                           class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 dark:border-gray-600 rounded dark:bg-gray-700 dark:focus:ring-blue-600 dark:focus:border-blue-600">
                                    <label class="ml-3 text-sm text-gray-700 dark:text-gray-300">
                                        {{ $plan->getTranslation('name') }} ({{ number_format($plan->price, 0) }} RWF)
                                    </label>
                                </div>
                            @endforeach
                        </div>
                        @error('subscription_plans')
                            <p class="mt-2 text-sm text-red-600 dark:text-red-400 flex items-center">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <!-- Active Status -->
                    <div class="md:col-span-2">
                        <div class="flex items-center p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                            <input type="checkbox" 
                                   name="is_active" 
                                   value="1" 
                                   id="is_active" 
                                   {{ old('is_active', '1') ? 'checked' : '' }}
                                   class="h-5 w-5 text-blue-600 focus:ring-blue-500 border-gray-300 dark:border-gray-600 rounded dark:bg-gray-700 dark:focus:ring-blue-600 dark:focus:border-blue-600">
                            <label for="is_active" class="ml-3 text-sm text-gray-700 dark:text-gray-300 font-medium cursor-pointer flex items-center">
                                <svg class="w-4 h-4 mr-2 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                Make this quiz active (available to students)
                            </label>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Submit Button -->
            <div class="flex justify-end space-x-4 pt-6 border-t border-gray-200 dark:border-gray-700">
                <a href="{{ route('admin.quizzes.create.question', ['step' => 1]) }}" 
                   class="inline-flex items-center px-6 py-3 bg-gray-200 dark:bg-gray-700 text-gray-800 dark:text-gray-300 font-medium rounded-lg hover:bg-gray-300 dark:hover:bg-gray-600 transition-all duration-200">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    Back to Questions
                </a>
                <button type="submit" 
                        class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-green-600 to-green-700 hover:from-green-700 hover:to-green-800 text-white font-medium rounded-lg shadow-sm hover:shadow-md transition-all duration-200">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    Complete Quiz Creation
                </button>
            </div>
        </form>
    </div>

    <!-- Questions Review -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 fade-in fade-in-delay-3">
        <div class="p-6 border-b border-gray-200 dark:border-gray-700">
            <h2 class="text-xl font-semibold text-gray-900 dark:text-white flex items-center">
                <svg class="w-5 h-5 mr-2 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                Questions Review (20 total)
            </h2>
        </div>
        
        <div class="max-h-96 overflow-y-auto">
            @foreach ($draftData['questions'] as $index => $question)
                <div class="p-6 border-b border-gray-100 dark:border-gray-700 last:border-b-0">
                    <div class="flex items-start justify-between mb-4">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white flex items-center">
                            <span class="w-8 h-8 rounded-full bg-blue-100 dark:bg-blue-900/20 flex items-center justify-center text-blue-600 dark:text-blue-400 font-semibold text-sm mr-3">
                                {{ $index + 1 }}
                            </span>
                            Question {{ $index + 1 }}
                        </h3>
                        <a href="{{ route('admin.quizzes.create.question', ['step' => $index + 1]) }}" 
                           class="text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300 font-medium text-sm">
                            Edit Question
                        </a>
                    </div>

                    <!-- Question Text -->
                    <div class="mb-4">
                        <div class="text-sm text-gray-600 dark:text-gray-400 mb-1">English:</div>
                        <div class="text-gray-900 dark:text-white mb-3">{{ $question['text'] }}</div>
                        
                        <div class="text-sm text-gray-600 dark:text-gray-400 mb-1">Kinyarwanda:</div>
                        <div class="text-gray-900 dark:text-white">{{ $question['text_rw'] }}</div>
                        
                        @if(isset($question['image_url']))
                            <div class="mt-3">
                                <img src="{{ asset('storage/' . $question['image_url']) }}" class="image-preview">
                            </div>
                        @endif
                    </div>

                    <!-- Options -->
                    <div class="space-y-2">
                        <div class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Answer Options:</div>
                        @foreach ($question['options'] as $optionIndex => $option)
                            <div class="flex items-center space-x-3 p-3 {{ $optionIndex == $question['correct_answer'] ? 'bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800' : 'bg-gray-50 dark:bg-gray-700' }} rounded-lg">
                                <div class="option-letter {{ $optionIndex == $question['correct_answer'] ? 'correct-option' : 'bg-gray-200 dark:bg-gray-600 text-gray-600 dark:text-gray-400' }}">
                                    {{ chr(65 + $optionIndex) }}
                                </div>
                                <div class="flex-1">
                                    <div class="text-sm text-gray-600 dark:text-gray-400 mb-1">English:</div>
                                    <div class="text-gray-900 dark:text-white mb-2">{{ $option['text'] }}</div>
                                    
                                    <div class="text-sm text-gray-600 dark:text-gray-400 mb-1">Kinyarwanda:</div>
                                    <div class="text-gray-900 dark:text-white">{{ $option['text_rw'] }}</div>
                                    
                                    @if(isset($option['image_url']))
                                        <div class="mt-2">
                                            <img src="{{ asset('storage/' . $option['image_url']) }}" class="image-preview">
                                        </div>
                                    @endif
                                </div>
                                @if ($optionIndex == $question['correct_answer'])
                                    <div class="text-green-600 dark:text-green-400 font-medium text-sm">
                                        ✓ Correct
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>
@endsection
