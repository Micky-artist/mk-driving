@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-12">
    <div class="max-w-3xl mx-auto bg-white rounded-lg shadow-md overflow-hidden">
        <div class="p-8 text-center">
            <div class="w-20 h-20 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-6">
                <svg class="w-12 h-12 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
            </div>
            
            <h1 class="text-3xl font-bold text-gray-900 mb-4">Subscription Successful!</h1>
            
            <p class="text-lg text-gray-600 mb-8">
                Thank you for subscribing to <strong>{{ $subscription->subscriptionPlan->name }}</strong> plan.
                @if($subscription->status === 'ACTIVE')
                    Your subscription is now active.
                @elseif($subscription->status === 'PENDING')
                    Your subscription is being processed. You'll receive a confirmation email shortly.
                @endif
            </p>
            
            <div class="bg-gray-50 p-6 rounded-lg mb-8 text-left">
                <h2 class="text-xl font-semibold mb-4">Subscription Details</h2>
                <div class="grid md:grid-cols-2 gap-4">
                    <div>
                        <p class="text-sm text-gray-500">Plan</p>
                        <p class="font-medium">{{ $subscription->subscriptionPlan->name }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Amount</p>
                        <p class="font-medium">${{ number_format($subscription->amount, 2) }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Start Date</p>
                        <p class="font-medium">{{ $subscription->start_date->format('M d, Y') }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">
                            @if($subscription->status === 'ACTIVE')
                                Next Billing Date
                            @else
                                Activation Date
                            @endif
                        </p>
                        <p class="font-medium">{{ $subscription->end_date->format('M d, Y') }}</p>
                    </div>
                    <div class="md:col-span-2">
                        <p class="text-sm text-gray-500">Status</p>
                        @php
                            $statusClasses = [
                                'ACTIVE' => 'bg-green-100 text-green-800',
                                'PENDING' => 'bg-yellow-100 text-yellow-800',
                                'CANCELLED' => 'bg-red-100 text-red-800',
                                'EXPIRED' => 'bg-gray-100 text-gray-800',
                            ][$subscription->status] ?? 'bg-gray-100 text-gray-800';
                        @endphp
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium {{ $statusClasses }}">
                            {{ ucfirst(strtolower($subscription->status)) }}
                        </span>
                    </div>
                </div>
            </div>
            
            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                <a href="{{ route('dashboard') }}" class="inline-flex items-center justify-center px-6 py-3 border border-transparent text-base font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    Go to Dashboard
                </a>
                <a href="{{ route('subscriptions.index') }}" class="inline-flex items-center justify-center px-6 py-3 border border-gray-300 text-base font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    View All Subscriptions
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
