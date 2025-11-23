@extends('layouts.app')

@section('content')
<div class="py-12 bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        @include('components.home.subscription-plans', [
            'plans' => $plans,
            'user' => $user,
            'isDashboard' => false
        ])
    </div>
</div>
@endsection
