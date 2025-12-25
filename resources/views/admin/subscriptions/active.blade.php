@extends('admin.layouts.app')

@section('content')
    <!-- Active Subscriptions Section -->
    <div class="mb-8">
        <div class="mb-6 flex justify-between items-center">
            <h2 class="text-xl font-semibold text-gray-900 dark:text-white">Active Subscriptions</h2>
            <div class="text-sm text-gray-500 dark:text-gray-400">
                {{ $subscriptions->total() }} {{ Str::plural('subscription', $subscriptions->total()) }} active
            </div>
        </div>

        @if($subscriptions->isEmpty())
            <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow">
                <p class="text-gray-500 dark:text-gray-400">No active subscriptions found.</p>
            </div>
        @else
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-700">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">User Details</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Plan & Period</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Status</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                            @foreach($subscriptions as $subscription)
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 cursor-pointer" onclick="window.location.href='{{ route('admin.users.show', $subscription->user->id) }}'">
                                    <!-- User Details -->
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="flex-shrink-0 h-10 w-10">
                                                <div class="h-10 w-10 rounded-full bg-blue-100 dark:bg-blue-900/20 flex items-center justify-center text-blue-600 dark:text-blue-400 font-semibold">
                                                    {{ strtoupper(substr($subscription->user->name, 0, 2)) }}
                                                </div>
                                            </div>
                                            <div class="ml-4">
                                                <div class="text-sm font-medium text-gray-900 dark:text-white">{{ $subscription->user->name }}</div>
                                                <div class="text-sm text-gray-500 dark:text-gray-400">{{ $subscription->user->email }}</div>
                                                <div class="text-xs text-gray-500 dark:text-gray-400">{{ $subscription->user->phone ?? 'No phone' }}</div>
                                            </div>
                                        </div>
                                    </td>

                                    <!-- Plan & Period -->
                                    <td class="px-6 py-4">
                                        <div class="text-sm font-medium text-gray-900 dark:text-white">
                                            @php
                                                $planName = $subscription->plan->name['en'] ?? 'Unknown Plan';
                                            @endphp
                                            {{ $planName[app()->getLocale()] ?? $planName['en'] ?? 'N/A' }}
                                        </div>
                                        <div class="text-sm text-gray-500 dark:text-gray-400">
                                            {{ $subscription->starts_at->format('M j, Y') }} - {{ $subscription->ends_at->format('M j, Y') }}
                                        </div>
                                        <div class="text-xs text-gray-400 dark:text-gray-500">
                                            {{ $subscription->created_at->diffForHumans() }}
                                        </div>
                                    </td>

                                    <!-- Status -->
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-2.5 py-0.5 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 dark:bg-green-900/20 text-green-800 dark:text-green-400">
                                            Active
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="px-6 py-3 bg-gray-50 dark:bg-gray-700 border-t border-gray-200 dark:border-gray-700">
                    {{ $subscriptions->links() }}
                </div>
            </div>
        @endif
    </div>
@endsection
