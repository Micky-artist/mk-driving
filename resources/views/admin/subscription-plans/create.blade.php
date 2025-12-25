@extends('admin.layouts.app')

@section('title', 'Create Plan')

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
</style>
@endpush

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-2">
                Create Subscription Plan
            </h1>
            <p class="text-gray-600 dark:text-gray-400">
                Create a new subscription plan. Provide details in both English and Kinyarwanda.
            </p>
        </div>
        <div class="flex items-center gap-3">
            <!-- Back to Plans Button -->
            <a href="{{ route('admin.subscription-plans.index') }}" 
               class="inline-flex items-center px-4 py-3 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-full shadow-sm hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Back to Plans
            </a>
        </div>
    </div>

    <!-- Create Plan Form -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 fade-in fade-in-delay-1">
        <form action="{{ route('admin.subscription-plans.store') }}" method="POST" class="space-y-6">
            @csrf
            
            <div class="px-6 py-4 sm:p-6">
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <!-- Plan Names (Multi-language) -->
                    <div class="lg:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Plan Name
                        </label>
                        <div class="space-y-3">
                            <div>
                                <label for="name_en" class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">
                                    English (en)
                                </label>
                                <input type="text" 
                                       name="name[en]" 
                                       id="name_en" 
                                       required
                                       placeholder="Enter plan name in English"
                                       class="mt-1 block w-full px-4 py-3 border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm dark:bg-gray-700 dark:text-white">
                            </div>
                            <div>
                                <label for="name_rw" class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">
                                    Kinyarwanda (rw)
                                </label>
                                <input type="text" 
                                       name="name[rw]" 
                                       id="name_rw" 
                                       required
                                       placeholder="Enter plan name in Kinyarwanda"
                                       class="mt-1 block w-full px-4 py-3 border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm dark:bg-gray-700 dark:text-white">
                            </div>
                        </div>
                    </div>
                    
                    <!-- Plan Descriptions (Multi-language) -->
                    <div class="lg:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Description
                        </label>
                        <div class="space-y-3">
                            <div>
                                <label for="description_en" class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">
                                    English (en)
                                </label>
                                <textarea name="description[en]" 
                                          id="description_en" 
                                          rows="3"
                                          required
                                          placeholder="Enter plan description in English"
                                          class="mt-1 block w-full px-4 py-3 border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm dark:bg-gray-700 dark:text-white"></textarea>
                            </div>
                            <div>
                                <label for="description_rw" class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">
                                    Kinyarwanda (rw)
                                </label>
                                <textarea name="description[rw]" 
                                          id="description_rw" 
                                          rows="3"
                                          required
                                          placeholder="Enter plan description in Kinyarwanda"
                                          class="mt-1 block w-full px-4 py-3 border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm dark:bg-gray-700 dark:text-white"></textarea>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <!-- Price -->
                    <div>
                        <label for="price" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Price (RWF)
                        </label>
                        <input type="number" 
                               name="price" 
                               id="price" 
                               step="1"
                               min="0"
                               required
                               placeholder="Enter price"
                               class="mt-1 block w-full px-4 py-3 border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm dark:bg-gray-700 dark:text-white">
                    </div>
                    
                    <!-- Duration -->
                    <div>
                        <label for="duration" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Duration (days)
                        </label>
                        <input type="number" 
                               name="duration" 
                               id="duration" 
                               min="1"
                               required
                               placeholder="Enter duration in days"
                               class="mt-1 block w-full px-4 py-3 border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm dark:bg-gray-700 dark:text-white">
                    </div>
                </div>
                
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <!-- Color -->
                    <div>
                        <label for="color" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Color
                        </label>
                        <input type="color" 
                               name="color" 
                               id="color" 
                               value="#3B82F6"
                               class="mt-1 block w-full h-12 px-2 py-1 border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm dark:bg-gray-700">
                    </div>
                    
                    <!-- Max Quizzes -->
                    <div>
                        <label for="max_quizzes" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Max Quizzes
                        </label>
                        <input type="number" 
                               name="max_quizzes" 
                               id="max_quizzes" 
                               min="1"
                               required
                               placeholder="Enter maximum number of quizzes"
                               class="mt-1 block w-full px-4 py-3 border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm dark:bg-gray-700 dark:text-white">
                    </div>
                </div>
                
                <!-- Status -->
                <div>
                    <label for="is_active" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Status
                    </label>
                    <select name="is_active" 
                            id="is_active" 
                            required
                            class="mt-1 block w-full px-4 py-3 border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm dark:bg-gray-700 dark:text-white">
                        <option value="1">Active</option>
                        <option value="0">Inactive</option>
                    </select>
                </div>
            </div>
            
            <!-- Form Actions -->
            <div class="bg-gray-50 dark:bg-gray-700 px-6 py-4 sm:px-6 sm:flex sm:flex-row-reverse">
                <button type="submit" 
                        class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-6 py-3 bg-gradient-to-r from-orange-600 to-orange-700 hover:from-orange-700 hover:to-orange-800 text-base font-medium text-white hover:shadow-xl transform hover:scale-105 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-orange-500 sm:ml-3 sm:w-auto sm:text-sm transition-all duration-200">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                    </svg>
                    Create Plan
                </button>
                <a href="{{ route('admin.subscription-plans.index') }}" 
                   class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 dark:border-gray-600 shadow-sm px-6 py-3 bg-white dark:bg-gray-800 text-base font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                    Cancel
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
