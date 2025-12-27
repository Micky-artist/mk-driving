@extends('layouts.app')

@push('scripts')
<script src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    console.log('=== PLANS PAGE DEBUG ===');
    console.log('User Subscriptions:', @json($userSubscriptions ?? 'undefined'));
    console.log('User Subscriptions Count:', {{ isset($userSubscriptions) ? $userSubscriptions->count() : 'undefined' }});
    console.log('Auth Check:', {{ Auth::check() ? 'true' : 'false' }});
    
    @foreach($plans as $plan)
    @php
        $isCurrent = $plan['is_current'] ?? false;
        if (is_callable($isCurrent)) {
            $isCurrent = $isCurrent();
        }
    @endphp
    console.log('Plan {{ $plan["id"] }} ({{ $plan["slug"] }}): is_current = {{ $isCurrent ? 'true' : 'false' }}');
    @endforeach
    
    console.log('=== END DEBUG ===');
});
</script>
@endpush

@section('content')
<div x-data="{ selectedPlan: null, showModal: false }">
    
    <div class="relative z-10 space-y-8">
        <!-- Subscription Plans Component -->
        @include('components.home.subscription-plans', [
            'plans' => $plans,
            'isPlansPage' => true
        ])
        {{-- Subscription History (only show if user has subscriptions) --}}
        @if (isset($userSubscriptions) && $userSubscriptions->count() > 0)
            @include('components.subscription-history', [
                'userSubscriptions' => $userSubscriptions
            ])
        @endif
        
    </div>
</div>
@endsection
