@php
    $subscription = $subscription ?? null;
    $isActive = $isActive ?? false;
    $isPending = $isPending ?? false;
    $plan = $subscription->subscriptionPlan ?? null;
    
    if (!$subscription || !$plan) {
        return;
    }
    
    $statusClass = [
        'ACTIVE' => 'bg-green-100 text-green-800',
        'PENDING' => 'bg-yellow-100 text-yellow-800',
        'CANCELLED' => 'bg-red-100 text-red-800',
        'EXPIRED' => 'bg-gray-100 text-gray-800',
    ][$subscription->status] ?? 'bg-gray-100 text-gray-800';
    
    $statusLabel = ucfirst(strtolower($subscription->status));
    
    if ($isActive) {
        $statusLabel = 'Active';
    } elseif ($isPending) {
        $statusLabel = 'Pending';
    }
@endphp

<div class="bg-white rounded-lg shadow-md overflow-hidden border border-gray-200">
    <div class="p-6">
        <div class="flex justify-between items-start mb-4">
            <h3 class="text-xl font-bold text-gray-900">{{ $plan->name }}</h3>
            <span class="text-xs font-semibold px-2.5 py-0.5 rounded-full {{ $statusClass }}">
                {{ $statusLabel }}
            </span>
        </div>
        
        <div class="mb-6">
            <span class="text-3xl font-bold text-gray-900">${{ number_format($plan->price, 2) }}</span>
            <span class="text-gray-600">/{{ $plan->billing_cycle }}</span>
        </div>
        
        <div class="space-y-4 mb-6">
            <div>
                <p class="text-sm text-gray-500">Start Date</p>
                <p>{{ $subscription->start_date->format('M d, Y') }}</p>
            </div>
            
            <div>
                <p class="text-sm text-gray-500">
                    @if($isActive)
                        Renews on
                    @elseif($isPending)
                        Will activate on
                    @else
                        Expires on
                    @endif
                </p>
                <p>{{ $subscription->end_date->format('M d, Y') }}</p>
            </div>
        </div>
        
        @if($isActive && $subscription->status !== 'CANCELLED')
            <form action="{{ route('subscriptions.cancel', $subscription) }}" method="POST" class="mt-4">
                @csrf
                @method('DELETE')
                <button type="submit" class="w-full text-center text-red-600 hover:text-red-800 font-medium py-2 px-4 border border-red-200 rounded-lg hover:bg-red-50 transition duration-200">
                    Cancel Subscription
                </button>
            </form>
        @endif
        
        @if($isPending)
            <div class="mt-4 text-sm text-yellow-700 bg-yellow-50 p-3 rounded-lg">
                Your subscription is pending activation. You'll receive a confirmation email once it's activated.
            </div>
        @endif
    </div>
</div>
