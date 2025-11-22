@extends('layouts.app')

@push('scripts')
<script src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
@endpush

@section('content')
<div x-data="{ selectedPlan: null, showModal: false }">
    <!-- Background Elements -->
    <div class="fixed right-0 translate-x-[30%] sm:translate-x-[50%] -translate-y-1/3 rounded-full w-[20rem] sm:w-[30rem] md:w-[40rem] lg:w-[50rem] aspect-square bg-[#8ECAE680]/50 pointer-events-none -z-10">
        <div class="absolute bottom-0 left-1/2 -translate-x-1/2 rounded-full w-[50%] aspect-square bg-[#8ECAE680]/90"></div>
    </div>
    
    <div class="relative z-10">
        <!-- Subscription Plans Component -->
        @include('components.home.subscription-plans', [
            'plans' => $plans,
            'isPlansPage' => true
        ])

        <!-- Help Section -->
        <div class="mt-16 max-w-3xl mx-auto text-center">
            <h2 class="text-2xl font-bold text-gray-900 mb-4">{{ __('plans.need_help') }}</h2>
            <p class="text-gray-600 mb-6">{{ __('plans.contact_support') }}</p>
            <a href="{{ route('contact', ['locale' => app()->getLocale()]) }}" class="inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-md shadow-sm text-white bg-teal-600 hover:bg-teal-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-teal-500">
                {{ __('plans.contact_us') }}
            </a>
        </div>
    </div>
</div>
@endsection
