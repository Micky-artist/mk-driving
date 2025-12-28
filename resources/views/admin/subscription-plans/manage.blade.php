@extends('admin.layouts.app')

@section('title', $plan->getTranslation('name') . ' - Subscriptions')

@push('styles')
<style>
    .stat-card {
        /* Using pure Tailwind classes instead of @apply */
    }
    
    .subscription-table {
        /* Using pure Tailwind classes instead of @apply */
    }
    
    .search-input {
        /* Using pure Tailwind classes instead of @apply */
    }
    
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
            <!-- Breadcrumb -->
            <nav class="flex mb-4" aria-label="Breadcrumb">
                <ol class="inline-flex items-center space-x-1 md:space-x-3">
                    <li class="inline-flex items-center">
                        <a href="{{ route('admin.subscription-plans.index') }}" class="text-gray-700 hover:text-blue-600 dark:text-gray-300 dark:hover:text-blue-400">
                            <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z"></path>
                            </svg>
                            Plan Management
                        </a>
                    </li>
                    <li>
                        <div class="flex items-center">
                            <svg class="w-6 h-6 text-gray-400 mx-1" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                            </svg>
                            <span class="text-gray-500 dark:text-gray-400">{{ $plan->getTranslation('name') }}</span>
                        </div>
                    </li>
                </ol>
            </nav>
            
            <div class="flex items-center gap-4 mb-2">
                <h1 class="text-3xl font-bold text-gray-900 dark:text-white">
                    {{ $plan->getTranslation('name') }}
                </h1>
                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium 
                    {{ $plan->is_active ? 'bg-green-100 text-green-800 dark:bg-green-900/20 dark:text-green-400' : 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300' }}">
                    {{ $plan->is_active ? 'Active' : 'Inactive' }}
                </span>
            </div>
            <p class="text-gray-600 dark:text-gray-400">
                {{ $plan->getTranslation('description') }}
            </p>
            <div class="flex items-center gap-4 mt-2 text-sm text-gray-500 dark:text-gray-400">
                <span>{{ number_format($plan->price, 0) }} RWF</span>
                <span>•</span>
                <span>{{ $plan->duration ?? 'N/A' }} days</span>
            </div>
        </div>
        <div class="flex items-center gap-3">
            <!-- Search -->
            <div class="relative flex-1 max-w-md">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                </div>
                <input type="text" 
                       id="search-input"
                       placeholder="Search by user name, email, or phone..." 
                       class="bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 rounded-full pl-10 pr-4 py-3 w-full focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200">
                @if($search ?? false)
                <button type="button" 
                        onclick="clearSearch()"
                        class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition-colors">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
                @endif
            </div>
            
            <!-- Back to Plans Button -->
            <a href="{{ route('admin.subscription-plans.index') }}" 
               class="inline-flex items-center px-4 py-3 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-full shadow-sm hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Back to Plans
            </a>
            
            <!-- Edit Plan Button -->
            <button onclick="toggleEditModal()" 
               class="inline-flex items-center px-4 py-3 bg-gradient-to-r from-orange-600 to-orange-700 hover:from-orange-700 hover:to-orange-800 text-white font-semibold rounded-full shadow-lg hover:shadow-xl transform hover:scale-105 transition-all duration-200 border border-orange-500/20">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                </svg>
                Edit Plan
            </a>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-8">
        <!-- Total Subscriptions -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm hover:shadow-md transition-all duration-300 transform hover:-translate-y-0.5 border border-gray-200 dark:border-gray-700 hover:border-blue-300 dark:hover:border-blue-600 fade-in fade-in-delay-1">
            <div class="p-6">
                <div class="flex items-center justify-between mb-4">
                    <div class="p-3 bg-blue-100 dark:bg-blue-900/20 rounded-lg">
                        <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-3.356A3 3 0 0014 12V4a3 3 0 00-3-3H7a3 3 0 00-3 3v8a3 3 0 003 3h14v-2h-5a2 2 0 00-2-2v-4a2 2 0 012-2h4a2 2 0 012 2V4a2 2 0 01-2 2z"></path>
                        </svg>
                    </div>
                    <span class="text-xs font-medium text-blue-600 dark:text-blue-400 bg-blue-100 dark:bg-blue-900/20 px-2 py-1 rounded-full">
                        Total
                    </span>
                </div>
                <h3 class="text-2xl font-bold text-gray-900 dark:text-white mb-1">{{ number_format($stats['total']) }}</h3>
                <p class="text-sm text-gray-600 dark:text-gray-400">Total Subscriptions</p>
            </div>
        </div>

        <!-- Active Subscriptions -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm hover:shadow-md transition-all duration-300 transform hover:-translate-y-0.5 border border-gray-200 dark:border-gray-700 hover:border-green-300 dark:hover:border-green-600 fade-in fade-in-delay-2">
            <div class="p-6">
                <div class="flex items-center justify-between mb-4">
                    <div class="p-3 bg-green-100 dark:bg-green-900/20 rounded-lg">
                        <svg class="w-6 h-6 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <span class="text-xs font-medium text-green-600 dark:text-green-400 bg-green-100 dark:bg-green-900/20 px-2 py-1 rounded-full">
                        Active
                    </span>
                </div>
                <h3 class="text-2xl font-bold text-gray-900 dark:text-white mb-1">{{ number_format($stats['active']) }}</h3>
                <p class="text-sm text-gray-600 dark:text-gray-400">Active Subscriptions</p>
            </div>
        </div>

        <!-- Pending Subscriptions -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm hover:shadow-md transition-all duration-300 transform hover:-translate-y-0.5 border border-gray-200 dark:border-gray-700 hover:border-yellow-300 dark:hover:border-yellow-600 fade-in fade-in-delay-3">
            <div class="p-6">
                <div class="flex items-center justify-between mb-4">
                    <div class="p-3 bg-yellow-100 dark:bg-yellow-900/20 rounded-lg">
                        <svg class="w-6 h-6 text-yellow-600 dark:text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <span class="text-xs font-medium text-yellow-600 dark:text-yellow-400 bg-yellow-100 dark:bg-yellow-900/20 px-2 py-1 rounded-full">
                        Pending
                    </span>
                </div>
                <h3 class="text-2xl font-bold text-gray-900 dark:text-white mb-1">{{ number_format($stats['pending']) }}</h3>
                <p class="text-sm text-gray-600 dark:text-gray-400">Pending Subscriptions</p>
            </div>
        </div>

        <!-- Revenue -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm hover:shadow-md transition-all duration-300 transform hover:-translate-y-0.5 border border-gray-200 dark:border-gray-700 hover:border-orange-300 dark:hover:border-orange-600 fade-in fade-in-delay-3">
            <div class="p-6">
                <div class="flex items-center justify-between mb-4">
                    <div class="p-3 bg-orange-100 dark:bg-orange-900/20 rounded-lg">
                        <svg class="w-6 h-6 text-orange-600 dark:text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <span class="text-xs font-medium text-orange-600 dark:text-orange-400 bg-orange-100 dark:bg-orange-900/20 px-2 py-1 rounded-full">
                        Revenue
                    </span>
                </div>
                <h3 class="text-2xl font-bold text-gray-900 dark:text-white mb-1">{{ number_format($stats['revenue'], 0) }} RWF</h3>
                <p class="text-sm text-gray-600 dark:text-gray-400">Total Revenue</p>
            </div>
        </div>
    </div>

    <!-- Subscriptions Table -->
    <div class="subscription-table fade-in fade-in-delay-3">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-700">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                            User
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                            Amount
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                            Subscription Status
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                            Payment Status
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                            Period
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                            Created
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse($subscriptions as $subscription)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors cursor-pointer" onclick="window.location.href='{{ route('admin.users.show', $subscription->user->id) }}'">
                        <!-- User -->
                        <td class="px-6 py-3 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 h-10 w-10">
                                    <div class="h-10 w-10 rounded-full bg-gradient-to-br from-blue-500 to-blue-600 flex items-center justify-center text-white font-semibold text-sm shadow-md">
                                        {{ strtoupper(substr($subscription->user->first_name, 0, 1)) }}{{ strtoupper(substr($subscription->user->last_name, 0, 1)) }}
                                    </div>
                                </div>
                                <div class="ml-3">
                                    <div class="text-sm font-medium text-gray-900 dark:text-white">
                                        {{ $subscription->user->full_name }}
                                    </div>
                                    <div class="text-xs text-gray-500 dark:text-gray-400">
                                        {{ $subscription->user->email }}
                                    </div>
                                </div>
                            </div>
                        </td>
                        
                        <!-- Amount -->
                        <td class="px-6 py-3 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900 dark:text-white">
                                {{ number_format($subscription->amount, 2) }} {{ $subscription->currency ?? 'RWF' }}
                            </div>
                            @if($subscription->payment_method)
                                <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                    {{ ucfirst($subscription->payment_method) }}
                                </div>
                            @endif
                        </td>
                        
                        <!-- Status -->
                        <td class="px-6 py-3 whitespace-nowrap">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                {{ $subscription->status === \App\Enums\SubscriptionStatus::ACTIVE ? 'bg-green-100 text-green-800 dark:bg-green-900/20 dark:text-green-400' : '' }}
                                {{ $subscription->status === \App\Enums\SubscriptionStatus::PENDING ? 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/20 dark:text-yellow-400' : '' }}
                                {{ $subscription->status === \App\Enums\SubscriptionStatus::CANCELLED ? 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300' : '' }}">
                                {{ ucfirst(strtolower($subscription->status)) }}
                            </span>
                        </td>
                        
                        <!-- Payment Status -->
                        <td class="px-6 py-3 whitespace-nowrap">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                {{ $subscription->payment_status === \App\Enums\PaymentStatus::COMPLETED || $subscription->payment_status === \App\Enums\PaymentStatus::SUCCESSFUL ? 'bg-green-100 text-green-800 dark:bg-green-900/20 dark:text-green-400' : '' }}
                                {{ $subscription->payment_status === \App\Enums\PaymentStatus::PENDING ? 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/20 dark:text-yellow-400' : '' }}
                                {{ $subscription->payment_status === \App\Enums\PaymentStatus::FAILED || $subscription->payment_status === \App\Enums\PaymentStatus::REJECTED || $subscription->payment_status === \App\Enums\PaymentStatus::CANCELLED ? 'bg-red-100 text-red-800 dark:bg-red-900/20 dark:text-red-400' : '' }}">
                                {{ ucfirst(strtolower($subscription->payment_status)) }}
                            </span>
                        </td>
                        
                        <!-- Period -->
                        <td class="px-6 py-3 whitespace-nowrap">
                            <div class="text-sm text-gray-900 dark:text-white">
                                {{ $subscription->starts_at->format('M j, Y') }}
                            </div>
                            <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                to {{ $subscription->ends_at->format('M j, Y') }}
                            </div>
                        </td>
                        
                        <!-- Created -->
                        <td class="px-6 py-3 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                            {{ $subscription->created_at->format('M j, Y') }}
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center">
                            <div class="flex flex-col items-center">
                                <svg class="w-12 h-12 text-gray-400 dark:text-gray-500 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-3.356A3 3 0 0014 12V4a3 3 0 00-3-3H7a3 3 0 00-3 3v8a3 3 0 003 3h14v-2h-5a2 2 0 00-2-2v-4a2 2 0 012-2h4a2 2 0 012 2V4a2 2 0 01-2 2z"></path>
                                </svg>
                                <p class="text-sm text-gray-500 dark:text-gray-400 font-medium">
                                    No subscriptions found for this plan
                                </p>
                                <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">
                                    Try adjusting your search or filters
                                </p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <!-- Pagination -->
        @if($subscriptions->hasPages())
        <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800">
            <div class="flex items-center justify-between">
                <div class="text-sm text-gray-700 dark:text-gray-300">
                    Showing results {{ $subscriptions->firstItem() }} to {{ $subscriptions->lastItem() }} of {{ $subscriptions->total() }} total
                </div>
                {{ $subscriptions->links() }}
            </div>
        </div>
        @endif
    </div>
    
    <!-- Edit Plan Modal -->
    <div id="editPlanModal" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" onclick="toggleEditModal()"></div>

            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

            <div class="inline-block align-bottom bg-white dark:bg-gray-800 rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl sm:w-full">
                <form action="{{ route('admin.subscription-plans.update', $plan->id) }}" method="POST" class="space-y-6">
                    @csrf
                    @method('PUT')
                    
                    <div class="bg-white dark:bg-gray-800 px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <div class="sm:flex sm:items-start">
                            <div class="w-full">
                                <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-white" id="modal-title">
                                    Edit Plan: {{ $plan->getTranslation('name') }}
                                </h3>
                                
                                <div class="mt-6 grid grid-cols-1 gap-6">
                                    <!-- Plan Names (Multi-language) -->
                                    <div>
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
                                                       value="{{ $plan->name['en'] ?? '' }}"
                                                       required
                                                       placeholder="Enter plan name in English"
                                                       class="mt-1 block w-full border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm dark:bg-gray-700 dark:text-white px-3 py-2">
                                            </div>
                                            <div>
                                                <label for="name_rw" class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">
                                                    Kinyarwanda (rw)
                                                </label>
                                                <input type="text" 
                                                       name="name[rw]" 
                                                       id="name_rw" 
                                                       value="{{ $plan->name['rw'] ?? '' }}"
                                                       required
                                                       placeholder="Enter plan name in Kinyarwanda"
                                                       class="mt-1 block w-full border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm dark:bg-gray-700 dark:text-white px-3 py-2">
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <!-- Plan Descriptions (Multi-language) -->
                                    <div>
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
                                                          class="mt-1 block w-full border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm dark:bg-gray-700 dark:text-white px-3 py-2">{{ $plan->description['en'] ?? '' }}</textarea>
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
                                                          class="mt-1 block w-full border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm dark:bg-gray-700 dark:text-white px-3 py-2">{{ $plan->description['rw'] ?? '' }}</textarea>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="grid grid-cols-2 gap-6">
                                        <!-- Price -->
                                        <div>
                                            <label for="price" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                                Price (RWF)
                                            </label>
                                            <input type="number" 
                                                   name="price" 
                                                   id="price" 
                                                   value="{{ $plan->price }}"
                                                   step="0.01"
                                                   min="0"
                                                   required
                                                   class="mt-1 block w-full border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm dark:bg-gray-700 dark:text-white px-3 py-2">
                                        </div>
                                        
                                        <!-- Duration -->
                                        <div>
                                            <label for="duration" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                                Duration (days)
                                            </label>
                                            <input type="number" 
                                                   name="duration" 
                                                   id="duration" 
                                                   value="{{ $plan->duration }}"
                                                   min="1"
                                                   required
                                                   class="mt-1 block w-full border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm dark:bg-gray-700 dark:text-white px-3 py-2">
                                        </div>
                                    </div>
                                    
                                    <div class="grid grid-cols-2 gap-6">
                                        <!-- Color -->
                                        <div>
                                            <label for="color" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                                Color
                                            </label>
                                            <input type="color" 
                                                   name="color" 
                                                   id="color" 
                                                   value="{{ $plan->color ?? '#3B82F6' }}"
                                                   class="mt-1 block w-full h-10 border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm dark:bg-gray-700 px-3 py-2">
                                        </div>
                                        
                                        <!-- Max Quizzes -->
                                        <div>
                                            <label for="max_quizzes" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                                Max Quizzes
                                            </label>
                                            <input type="number" 
                                                   name="max_quizzes" 
                                                   id="max_quizzes" 
                                                   value="{{ $plan->max_quizzes }}"
                                                   min="1"
                                                   required
                                                   class="mt-1 block w-full border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm dark:bg-gray-700 dark:text-white px-3 py-2">
                                        </div>
                                    </div>
                                    
                                    <!-- Status -->
                                    <div>
                                        <label for="is_active" class="block text-sm font-medium text-gray-700 dark:text-gray-300 px-3 py-2">
                                            Status
                                        </label>
                                        <select name="is_active" 
                                                id="is_active" 
                                                class="mt-1 block w-full border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm dark:bg-gray-700 dark:text-white px-3 py-2">
                                            <option value="1" {{ $plan->is_active ? 'selected' : '' }}>Active</option>
                                            <option value="0" {{ !$plan->is_active ? 'selected' : '' }}>Inactive</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="bg-gray-50 dark:bg-gray-700 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                        <button type="submit" 
                                class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:ml-3 sm:w-auto sm:text-sm">
                            Save Changes
                        </button>
                        <button type="button" 
                                onclick="toggleEditModal()"
                                class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 dark:border-gray-600 shadow-sm px-4 py-2 bg-white dark:bg-gray-800 text-base font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                            Cancel
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('search-input');
    let searchTimeout;
    
    if (searchInput) {
        searchInput.addEventListener('input', function() {
            clearTimeout(searchTimeout);
            const query = this.value.trim();
            
            searchTimeout = setTimeout(() => {
                const url = new URL(window.location);
                if (query) {
                    url.searchParams.set('search', query);
                } else {
                    url.searchParams.delete('search');
                }
                url.searchParams.set('page', '1'); // Reset to first page on search
                window.location.href = url.toString();
            }, 600); // Debounce search
        });
        
        // Maintain search input value on page load
        const urlParams = new URLSearchParams(window.location.search);
        const searchValue = urlParams.get('search');
        if (searchValue) {
            searchInput.value = searchValue;
        }
    }
    
    // Clear search function
    window.clearSearch = function() {
        const url = new URL(window.location);
        url.searchParams.delete('search');
        url.searchParams.set('page', '1');
        window.location.href = url.toString();
    };
    
    // Toggle edit modal function
    window.toggleEditModal = function() {
        const modal = document.getElementById('editPlanModal');
        modal.classList.toggle('hidden');
    };
});
</script>
@endpush
