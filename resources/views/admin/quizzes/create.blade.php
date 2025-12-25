@extends('admin.layouts.app')

@section('title', 'Create New Quiz')

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
    
    .option-input {
        transition: all 0.2s ease;
    }
    
    .option-input:focus {
        transform: scale(1.02);
    }
    
    .image-preview {
        max-width: 200px;
        max-height: 150px;
        object-fit: cover;
        border-radius: 8px;
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
                    Create New Road Theory Test
                </h1>
                <p class="text-gray-600 dark:text-gray-400">
                    Create a comprehensive road theory test with 20 questions for your driving school students.
                </p>
            </div>
            
            <!-- Auto-save indicator -->
            <div id="auto-save-indicator" class="px-4 py-2 bg-gray-100 dark:bg-gray-700 rounded-lg border border-gray-200 dark:border-gray-600">
                <span class="text-sm text-gray-500 dark:text-gray-400">
                    💾 Auto-save enabled
                </span>
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

    <!-- Create Form -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 fade-in fade-in-delay-2">
        <form action="{{ route('admin.quizzes.initialize') }}" method="POST" enctype="multipart/form-data" class="p-6">
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
                        <p class="mt-2 text-sm text-gray-500 dark:text-gray-400 flex items-center">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            Standard: 20 minutes for road theory test
                        </p>
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
                        <p class="mt-2 text-sm text-gray-500 dark:text-gray-400 flex items-center">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            Standard: 60% for road theory test
                        </p>
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

            <!-- Form Actions -->
            <div class="flex justify-end mt-8 pt-6 border-t border-gray-200 dark:border-gray-700">          
                <div class="flex space-x-4">
                    <a href="{{ route('admin.quizzes.index') }}" 
                       class="inline-flex items-center px-6 py-3 bg-gray-200 dark:bg-gray-700 text-gray-800 dark:text-gray-300 font-medium rounded-lg hover:bg-gray-300 dark:hover:bg-gray-600 transition-all duration-200">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                        </svg>
                        Cancel
                    </a>
                    <button type="submit" 
                            class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white font-medium rounded-lg shadow-sm hover:shadow-md transition-all duration-200">
                        Next: Create Questions
                        <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                        </svg>
                    </button>
                </div>
            </div>
        </form>
    </div>

    <!-- Quick Tips -->
    <div class="bg-blue-50 dark:bg-blue-900/20 rounded-xl border border-blue-200 dark:border-blue-800 p-6 fade-in fade-in-delay-3">
        <div class="flex items-start">
            <svg class="w-6 h-6 text-blue-600 dark:text-blue-400 mr-3 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            <div>
                <h3 class="text-lg font-semibold text-blue-900 dark:text-blue-100 mb-2">Quiz Creation Process</h3>
                <ul class="text-sm text-blue-800 dark:text-blue-200 space-y-1">
                    <li>• First, provide basic quiz information (title, description, settings)</li>
                    <li>• Click "Next: Create Questions" to start adding questions step-by-step</li>
                    <li>• Create exactly 20 questions for a complete road theory test</li>
                    <li>• Each question should have 4 multiple choice options (A, B, C, D)</li>
                    <li>• Questions can include text, images, or both</li>
                    <li>• Mark one correct answer per question</li>
                    <li>• Your progress is automatically saved as you work</li>
                    <li>• Assign to appropriate subscription plans for access control</li>
                </ul>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
// Load draft functionality for basic quiz info
function loadDraftFromDB() {
    fetch('{{ route('admin.quizzes.load-draft') }}', {
        method: 'GET',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success && data.data && data.data.quiz_info) {
            populateForm(data.data.quiz_info);
            console.log('Loaded draft from database:', data.data.quiz_info);
        } else {
            console.log('No draft found in database');
        }
    })
    .catch(error => {
        console.error('Error loading draft:', error);
    });
}

// Populate form with saved quiz info
function populateForm(quizInfo) {
    if (!quizInfo) return;
    
    console.log('Populating form with saved quiz info:', quizInfo);
    
    // Basic quiz info
    if (quizInfo.title) document.querySelector('input[name="title"]').value = quizInfo.title;
    if (quizInfo.title_rw) document.querySelector('input[name="title_rw"]').value = quizInfo.title_rw;
    if (quizInfo.description) document.querySelector('textarea[name="description"]').value = quizInfo.description;
    if (quizInfo.description_rw) document.querySelector('textarea[name="description_rw"]').value = quizInfo.description_rw;
    if (quizInfo.time_limit) document.querySelector('input[name="time_limit"]').value = quizInfo.time_limit;
    if (quizInfo.passing_score) document.querySelector('input[name="passing_score"]').value = quizInfo.passing_score;
    if (quizInfo.is_active !== undefined) document.querySelector('input[name="is_active"]').checked = quizInfo.is_active;
    
    // Subscription plans
    if (quizInfo.subscription_plans) {
        quizInfo.subscription_plans.forEach(planId => {
            const checkbox = document.querySelector(`input[name="subscription_plans[]"][value="${planId}"]`);
            if (checkbox) checkbox.checked = true;
        });
    }
}

// Initialize form on page load
document.addEventListener('DOMContentLoaded', function() {
    // Try to load saved draft
    loadDraftFromDB();
});
</script>
@endpush
@endsection
