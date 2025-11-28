@extends('admin.layouts.app')

@section('title', __('dashboard.title'))

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <h1 class="h3 mb-4">@lang('dashboard.title')</h1>
        </div>
    </div>
    
    <!-- Add your dashboard widgets here -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">@lang('dashboard.welcome_back')</h5>
                    <p class="card-text">@lang('dashboard.welcome_message')</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
