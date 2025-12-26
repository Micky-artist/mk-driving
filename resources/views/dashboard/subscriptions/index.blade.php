@extends('layouts.app')

@section('content')
    <!-- Include unified navbar for dashboard -->
    <x-unified-navbar :showUserStats="true" />
    
    <div class="pt-16"><div class="container mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-8">
        <h1 class="text-3xl font-bold">Subscription Plans</h1>
        <a href="{{ route('dashboard') }}" class="text-blue-600 hover:text-blue-800">
            &larr; Back to Dashboard
        </a>
    </div>

    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
            {{ session('error') }}
        </div>
    @endif

    <!-- Active Subscriptions -->
    @if($activeSubscriptions->isNotEmpty())
        <div class="mb-12">
            <h2 class="text-2xl font-semibold mb-4">Your Active Subscriptions</h2>
            <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($activeSubscriptions as $subscription)
                    @include('subscriptions.partials.subscription-card', [
                        'subscription' => $subscription,
                        'isActive' => true
                    ])
                @endforeach
            </div>
        </div>
    @endif

    <!-- Pending Subscriptions -->
    @if($pendingSubscriptions->isNotEmpty())
        <div class="mb-12">
            <h2 class="text-2xl font-semibold mb-4">Pending Subscriptions</h2>
            <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($pendingSubscriptions as $subscription)
                    @include('subscriptions.partials.subscription-card', [
                        'subscription' => $subscription,
                        'isPending' => true
                    ])
                @endforeach
            </div>
        </div>
    @endif

    <!-- Available Plans -->
    <div class="mb-12">
        <h2 class="text-2xl font-semibold mb-6">Available Plans</h2>
        @if($plans->isEmpty())
            <p class="text-gray-600">No subscription plans available at the moment.</p>
        @else
            <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($plans as $plan)
                    @include('subscriptions.partials.plan-card', ['plan' => $plan])
                @endforeach
            </div>
        @endif
    </div>
</div>
@endsection
