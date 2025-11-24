@extends('admin.layouts.app')

@section('content')
    <div class="mb-6">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-2xl font-semibold">Subscription Details</h2>
            <a href="{{ route('admin.subscriptions.pending') }}" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-800 rounded-md text-sm font-medium">
                &larr; Back to Subscriptions
            </a>
        </div>

        <div class="bg-white rounded-lg shadow overflow-hidden">
            <div class="p-6">
                <!-- Subscription Header -->
                <div class="pb-6 border-b border-gray-200">
                    <div class="flex flex-col md:flex-row md:items-center md:justify-between">
                        <div>
                            <h3 class="text-lg font-medium text-gray-900">
                                {{ $subscription->plan->name[app()->getLocale()] ?? $subscription->plan->name['en'] }}
                            </h3>
                            <p class="mt-1 text-sm text-gray-500">
                                Subscription #{{ $subscription->id }}
                            </p>
                        </div>
                        <div class="mt-4 md:mt-0">
                            <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full 
                                {{ $subscription->status === 'active' ? 'bg-green-100 text-green-800' : '' }}
                                {{ $subscription->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                {{ $subscription->status === 'cancelled' || $subscription->status === 'expired' ? 'bg-red-100 text-red-800' : '' }}
                                {{ $subscription->status === 'completed' ? 'bg-blue-100 text-blue-800' : '' }}">
                                {{ ucfirst($subscription->status) }}
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Subscription Details -->
                <div class="mt-6 grid grid-cols-1 gap-6 md:grid-cols-2">
                    <!-- User Information -->
                    <div>
                        <h4 class="text-sm font-medium text-gray-500 mb-3">USER INFORMATION</h4>
                        <div class="flex items-start">
                            <div class="flex-shrink-0 h-10 w-10 rounded-full bg-blue-100 flex items-center justify-center text-blue-600 font-semibold">
                                {{ strtoupper(substr($subscription->user->first_name, 0, 1)) }}{{ strtoupper(substr($subscription->user->last_name, 0, 1)) }}
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-900">{{ $subscription->user->full_name }}</p>
                                <p class="text-sm text-gray-500">{{ $subscription->user->email }}</p>
                                @if($subscription->user->phone)
                                    <p class="mt-1 text-sm text-gray-500">{{ $subscription->user->phone }}</p>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Plan Information -->
                    <div>
                        <h4 class="text-sm font-medium text-gray-500 mb-3">PLAN INFORMATION</h4>
                        <div class="space-y-1">
                            <p class="text-sm text-gray-900">{{ $subscription->plan->name[app()->getLocale()] ?? $subscription->plan->name['en'] }}</p>
                            <p class="text-sm text-gray-500">
                                {{ number_format($subscription->amount, 2) }} {{ $subscription->plan->currency }}
                                @if($subscription->billing_cycle === 'monthly')
                                    / {{ __('Month') }}
                                @elseif($subscription->billing_cycle === 'yearly')
                                    / {{ __('Year') }}
                                @endif
                            </p>
                            @if($subscription->trial_ends_at)
                                <p class="text-sm text-gray-500">
                                    {{ __('Trial ends') }}: {{ $subscription->trial_ends_at->format('M d, Y') }}
                                </p>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Subscription Dates -->
                <div class="mt-8 pt-6 border-t border-gray-200">
                    <h4 class="text-sm font-medium text-gray-500 mb-3">SUBSCRIPTION DATES</h4>
                    <div class="grid grid-cols-1 gap-6 md:grid-cols-3">
                        <div>
                            <p class="text-sm font-medium text-gray-900">Created At</p>
                            <p class="text-sm text-gray-500">{{ $subscription->created_at->format('M d, Y') }}</p>
                        </div>
                        @if($subscription->starts_at)
                            <div>
                                <p class="text-sm font-medium text-gray-900">Start Date</p>
                                <p class="text-sm text-gray-500">{{ $subscription->starts_at->format('M d, Y') }}</p>
                            </div>
                        @endif
                        @if($subscription->ends_at)
                            <div>
                                <p class="text-sm font-medium text-gray-900">
                                    {{ $subscription->status === 'cancelled' ? 'Cancelled On' : 'Ends At' }}
                                </p>
                                <p class="text-sm text-gray-500">{{ $subscription->ends_at->format('M d, Y') }}</p>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Payment History -->
                @if($subscription->payments->count() > 0)
                    <div class="mt-8 pt-6 border-t border-gray-200">
                        <h4 class="text-sm font-medium text-gray-500 mb-3">PAYMENT HISTORY</h4>
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Payment ID</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Amount</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($subscription->payments as $payment)
                                        <tr>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                                #{{ $payment->id }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                {{ $payment->created_at->format('M d, Y') }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                {{ number_format($payment->amount, 2) }} {{ $payment->currency }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <span class="px-2.5 py-0.5 inline-flex text-xs leading-5 font-semibold rounded-full 
                                                    {{ $payment->status === 'COMPLETED' ? 'bg-green-100 text-green-800' : '' }}
                                                    {{ $payment->status === 'PENDING' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                                    {{ in_array($payment->status, ['FAILED', 'REJECTED']) ? 'bg-red-100 text-red-800' : '' }}
                                                    ">
                                                    {{ ucfirst(strtolower($payment->status)) }}
                                                </span>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                @endif

                <!-- Actions -->
                <div class="mt-8 pt-6 border-t border-gray-200 flex justify-end space-x-3">
                    @if($subscription->status === 'pending')
                        <form action="{{ route('admin.subscriptions.approve', $subscription) }}" method="POST" class="inline">
                            @csrf
                            <button type="submit" class="px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                                Approve Subscription
                            </button>
                        </form>
                        <form action="{{ route('admin.subscriptions.reject', $subscription) }}" method="POST" class="inline">
                            @csrf
                            <button type="submit" class="px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                                Reject
                            </button>
                        </form>
                    @elseif($subscription->status === 'active' && $subscription->ends_at > now())
                        <form action="{{ route('admin.subscriptions.cancel', $subscription) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to cancel this subscription?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                                Cancel Subscription
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
