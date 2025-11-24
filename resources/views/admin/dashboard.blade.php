@extends('admin.layouts.app')

@section('title', 'Admin Dashboard')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <h1 class="h3 mb-4">Dashboard</h1>
        </div>
    </div>
    
    <!-- Add your dashboard widgets here -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Welcome to the Admin Dashboard</h5>
                    <p class="card-text">Use the navigation to manage different sections of the application.</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
