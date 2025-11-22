@extends('admin.layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between mb-4">
        <h1 class="h3">Create New Subscription Plan</h1>
        <div>
            <a href="{{ route('admin.subscription-plans.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Back to List
            </a>
        </div>
    </div>

    @include('admin.subscription-plans.form')
</div>
@endsection
