@extends('layouts.app')

@section('title', __('Help Center'))

@push('styles')
<style>
    .category-card {
        transition: all 0.3s ease;
    }
    .category-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
    }
    .category-icon {
        padding: 0.75rem;
        background-color: #f1f5f9;
        border-radius: 0.5rem;
    }
    .search-input {
        padding-left: 2.5rem;
        border-radius: 9999px;
        border-width: 2px;
    }
    .search-input:focus {
        --tw-ring-color: #94a3b8;
    }
    
    /* Accordion styles */
    .accordion-item {
        border-bottom: 1px solid #e2e8f0;
    }
    
    .accordion-button {
        width: 100%;
        text-align: left;
        padding: 1.25rem 1.5rem;
        font-weight: 500;
        color: #1e293b;
        background-color: transparent;
        border: none;
        cursor: pointer;
        display: flex;
        justify-content: space-between;
        align-items: center;
        transition: all 0.2s;
    }
    
    .accordion-button:hover {
        background-color: #f8fafc;
    }
    
    .accordion-button:focus {
        outline: none;
        background-color: #f8fafc;
    }
    
    .accordion-button svg {
        transition: transform 0.2s ease-in-out;
    }
    
    .accordion-button[aria-expanded="true"] svg {
        transform: rotate(180deg);
    }
    
    .accordion-content {
        overflow: hidden;
        max-height: 0;
        transition: max-height 0.3s ease-out;
    }
    
    .accordion-content-inner {
        padding: 0 1.5rem 1.5rem;
        color: #475569;
        line-height: 1.6;
    }
    
    /* No results state */
    .no-results {
        padding: 3rem 1.5rem;
        text-align: center;
        color: #64748b;
    }
    
    .no-results svg {
        margin: 0 auto 1rem;
    }
</style>
@endpush

@section('content')
    <!-- Include unified navbar for dashboard -->
    <x-unified-navbar :showUserStats="true" />
    
    <div class="pt-16"><div class="flex-1 bg-slate-50">
    <main class="container mx-auto py-6 sm:py-8 px-3 sm:px-4 lg:px-6">
        <!-- Hero Section -->
        <section class="text-center py-8 sm:py-12">
            <h1 class="text-3xl sm:text-4xl lg:text-5xl font-bold text-slate-800">{{ __('Help Center') }}</h1>
            <p class="mt-4 text-base sm:text-lg text-slate-600 max-w-2xl mx-auto">
                {{ __("We're here to help. Find answers to your questions or get in touch with our support team.") }}
            </p>
            <div class="mt-6 sm:mt-8 max-w-lg mx-auto relative">
                <div class="absolute left-3 sm:left-4 top-1/2 -translate-y-1/2 text-slate-400">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 sm:h-5 sm:w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </div>
                <input 
                    type="search" 
                    x-model="query"
                    placeholder="{{ __('Search for questions...') }}" 
                    class="w-full h-10 sm:h-12 pl-10 sm:pl-12 pr-4 border-2 border-slate-200 focus:ring-2 focus:ring-slate-300 focus:border-slate-300 rounded-full text-sm sm:text-base"
                >
                <button type="button" x-show="query" @click="query = ''" class="absolute right-3 sm:right-4 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 sm:h-5 sm:w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </section>

        <!-- Categories Section -->
        <section class="py-8 sm:py-12">
            <h2 class="text-2xl sm:text-3xl font-bold text-center text-slate-800 mb-6 sm:mb-8">{{ __('Browse by Category') }}</h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-6">
                @foreach($categories as $category)
                    <div class="bg-white rounded-lg shadow-sm border border-slate-100 overflow-hidden category-card">
                        <div class="flex items-center p-4 sm:p-6">
                            <div class="category-icon mr-3 sm:mr-4">
                                @switch($category['icon'])
                                    @case('book-open')
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 sm:h-8 sm:w-8 text-slate-800" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                                        </svg>
                                        @break
                                    @case('life-buoy')
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 sm:h-8 sm:w-8 text-slate-800" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        @break
                                    @case('user')
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 sm:h-8 sm:w-8 text-slate-800" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                        </svg>
                                        @break
                                    @case('message-square')
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 sm:h-8 sm:w-8 text-slate-800" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                                        </svg>
                                        @break
                                    @case('shield-check')
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 sm:h-8 sm:w-8 text-slate-800" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                                        </svg>
                                        @break
                                @endswitch
                            </div>
                            <div>
                                <h3 class="text-sm sm:text-base font-semibold text-slate-800">{{ $category['title'] }}</h3>
                                <p class="text-xs sm:text-sm text-slate-500 mt-1">{{ $category['description'] }}</p>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </section>

        <!-- FAQ Section -->
        <section class="py-8 sm:py-12 max-w-4xl mx-auto" x-data="search">
            <h2 class="text-2xl sm:text-3xl font-bold text-center text-slate-800 mb-6 sm:mb-8">{{ __('Frequently Asked Questions') }}</h2>
            <div class="bg-white rounded-lg shadow-sm border border-slate-100 overflow-hidden">
                <template x-if="filteredItems({{ Js::from($faqItems) }}).length === 0">
                    <div class="no-results">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 mx-auto text-slate-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <p class="text-lg font-medium text-slate-700">No results found</p>
                        <p class="mt-1 text-slate-500">We couldn't find any questions matching your search.</p>
                    </div>
                </template>
                
                <div class="divide-y divide-slate-100" x-show="filteredItems({{ Js::from($faqItems) }}).length > 0">
                    <template x-for="(item, index) in filteredItems({{ Js::from($faqItems) }})" :key="index">
                        <div class="accordion-item" x-data="{ isOpen: false }">
                            <button 
                                @click="isOpen = !isOpen"
                                class="accordion-button"
                                :aria-expanded="isOpen"
                                :aria-controls="'faq-' + index"
                            >
                                <span x-text="item.question"></span>
                                <svg class="w-5 h-5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                </svg>
                            </button>
                            <div 
                                :id="'faq-' + index" 
                                class="accordion-content" 
                                :aria-hidden="!isOpen"
                                x-show="isOpen"
                                x-collapse
                            >
                                <div class="accordion-content-inner" x-html="item.answer"></div>
                            </div>
                        </div>
                    </template>
                </div>
            </div>
        </section>

        <!-- Contact Section -->
        <section class="py-8 sm:py-12 text-center">
            <div class="max-w-2xl mx-auto">
                <h2 class="text-2xl sm:text-3xl font-bold text-slate-800 mb-4">{{ __('Still need help?') }}</h2>
                <p class="text-slate-600 mb-6">{{ __("Can't find what you're looking for? Our support team is here to help.") }}</p>
                <a 
                    href="mailto:support@mkdriving.com" 
                    class="inline-flex items-center justify-center px-6 py-3 border border-transparent text-sm sm:text-base font-medium rounded-full text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                    </svg>
                    {{ __('Contact Support') }}
                </a>
            </div>
        </section>
    </main>
</div>

@push('scripts')
<script src="{{ asset('js/help-center.js') }}" defer></script>
@endpush
@endsection
