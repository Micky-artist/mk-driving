@extends('layouts.dashboard')

@section('title', __('dashboard.subscription_history'))

@push('styles')
<style>
    .status-badge {
        @apply px-2.5 py-0.5 rounded-full text-xs font-medium;
    }
    .status-active {
        @apply bg-green-100 text-green-800;
    }
    .status-expired {
        @apply bg-gray-100 text-gray-800;
    }
    .status-canceled {
        @apply bg-red-100 text-red-800;
    }
    .status-pending {
        @apply bg-yellow-100 text-yellow-800;
    }
</style>
@endpush

@section('dashboard-content')
<div class="space-y-4 sm:space-y-6">
    <!-- Page Header -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="px-4 py-5 sm:px-6 border-b border-gray-200">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h2 class="text-xl font-semibold text-gray-900">
                        {{ __('dashboard.subscription_history') }}
                    </h2>
                    <p class="mt-1 text-sm text-gray-500">
                        {{ __('dashboard.view_your_subscription_history') }}
                    </p>
                </div>
                <a href="{{ route('dashboard', ['locale' => app()->getLocale()]) }}" 
                   class="mt-3 sm:mt-0 inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    <svg xmlns="http://www.w3.org/2000/svg" class="-ml-1 mr-2 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    {{ __('dashboard.back_to_dashboard') }}
                </a>
            </div>
        </div>
    </div>

    <!-- Subscription History -->
    <div class="bg-white shadow overflow-hidden sm:rounded-lg">
        @if($subscriptions->count() > 0)
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                {{ __('dashboard.plan') }}
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                {{ __('dashboard.status') }}
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                {{ __('dashboard.dates') }}
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                {{ __('dashboard.amount') }}
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                {{ __('dashboard.details') }}
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($subscriptions as $subscription)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">{{ $subscription->plan_name }}</div>
                                    <div class="text-sm text-gray-500">{{ $subscription->created_at }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($subscription->is_active)
                                        <span class="status-badge status-active">
                                            {{ __('dashboard.active') }}
                                        </span>
                                    @else
                                        <span class="status-badge status-expired">
                                            {{ ucfirst(strtolower($subscription->status)) }}
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    <div>{{ $subscription->start_date }} - {{ $subscription->end_date }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ number_format($subscription->price) }} RWF
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <a href="#" class="text-blue-600 hover:text-blue-900">{{ __('dashboard.view_details') }}</a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            <!-- Pagination -->
            <div class="bg-white px-4 py-3 border-t border-gray-200 sm:px-6">
                {{ $subscriptions->links() }}
            </div>
        @else
            <div class="text-center py-12">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M12 8v13m0-13V6a2 2 0 112 2h-2zm0 0V5.5A2.5 2.5 0 109.5 8H12zm-7 4h14M5 12a2 2 0 110-4h14a2 2 0 110 4M5 12v7a2 2 0 002 2h10a2 2 0 002-2v-7" />
                </svg>
                <h3 class="mt-2 text-sm font-medium text-gray-900">{{ __('dashboard.no_subscriptions_found') }}</h3>
                <p class="mt-1 text-sm text-gray-500">{{ __('dashboard.get_started_with_a_plan') }}</p>
                <div class="mt-6">
                    <a href="{{ route('plans', ['locale' => app()->getLocale()]) }}" class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        <svg class="-ml-1 mr-2 h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                        </svg>
                        {{ __('dashboard.browse_plans') }}
                    </a>
                </div>
            </div>
        @endif
    </div>
</div>
@endsection
