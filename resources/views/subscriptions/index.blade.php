@extends('layouts.app')

@push('scripts')
<script src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
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
