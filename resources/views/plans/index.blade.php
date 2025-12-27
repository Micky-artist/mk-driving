@extends('layouts.app')

@push('scripts')
<script src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    console.log('=== PLANS PAGE DEBUG ===');
    
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
    
    <div class="relative z-10">
        <!-- Subscription Plans Component -->
        @include('components.home.subscription-plans', [
            'plans' => $plans,
            'isPlansPage' => true
        ])
    </div>
</div>
@endsection
