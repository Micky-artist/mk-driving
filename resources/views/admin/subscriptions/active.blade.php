@extends('admin.layouts.app')

@section('content')
    <!-- Active Subscriptions Section -->
    <div class="mb-8">
        <div class="mb-6 flex justify-between items-center">
            <h2 class="text-xl font-semibold">Active Subscriptions</h2>
            <div class="text-sm text-gray-500">
                {{ $subscriptions->total() }} {{ Str::plural('subscription', $subscriptions->total()) }} active
            </div>
        </div>

        @if($subscriptions->isEmpty())
            <div class="bg-white p-6 rounded-lg shadow">
                <p class="text-gray-500">No active subscriptions found.</p>
            </div>
        @else
            <div class="bg-white rounded-lg shadow overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">User Details</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Plan & Period</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($subscriptions as $subscription)
                                <tr class="hover:bg-gray-50">
                                    <!-- User Details -->
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="flex-shrink-0 h-10 w-10">
                                                <div class="h-10 w-10 rounded-full bg-blue-100 flex items-center justify-center text-blue-600 font-semibold">
                                                    {{ strtoupper(substr($subscription->user->name, 0, 2)) }}
                                                </div>
                                            </div>
                                            <div class="ml-4">
                                                <div class="text-sm font-medium text-gray-900">{{ $subscription->user->name }}</div>
                                                <div class="text-sm text-gray-500">{{ $subscription->user->email }}</div>
                                                <div class="text-xs text-gray-500">{{ $subscription->user->phone ?? 'No phone' }}</div>
                                            </div>
                                        </div>
                                    </td>

                                    <!-- Plan & Period -->
                                    <td class="px-6 py-4">
                                        <div class="text-sm font-medium text-gray-900">
                                            @php
                                                $planName = is_string($subscription->plan->name) 
                                                    ? json_decode($subscription->plan->name, true) 
                                                    : $subscription->plan->name;
                                            @endphp
                                            {{ $planName[app()->getLocale()] ?? $planName['en'] ?? 'N/A' }}
                                        </div>
                                        <div class="text-sm text-gray-500">
                                            {{ $subscription->starts_at->format('M j, Y') }} - {{ $subscription->ends_at->format('M j, Y') }}
                                        </div>
                                        <div class="text-xs text-gray-400">
                                            {{ $subscription->created_at->diffForHumans() }}
                                        </div>
                                    </td>

                                    <!-- Status -->
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-2.5 py-0.5 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                            Active
                                        </span>
                                    </td>

                                    <!-- Actions -->
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <a href="{{ route('admin.subscriptions.show', $subscription) }}" class="text-blue-600 hover:text-blue-900">View</a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="px-6 py-3 bg-gray-50 border-t border-gray-200">
                    {{ $subscriptions->links() }}
                </div>
            </div>
        @endif
    </div>
@endsection
