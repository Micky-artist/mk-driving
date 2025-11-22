@extends('admin.layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between mb-4">
        <h1 class="h3">Subscription Plans</h1>
        <div>
            <a href="{{ route('admin.subscription-plans.create') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i> Add New Plan
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <div class="card shadow mb-4">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Price</th>
                            <th>Duration (days)</th>
                            <th>Max Quizzes</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($plans as $plan)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="color-indicator" style="background-color: {{ $plan->color }}; width: 20px; height: 20px; border-radius: 50%; margin-right: 10px;"></div>
                                        {{ $plan->getTranslation('name', app()->getLocale()) }}
                                    </div>
                                </td>
                                <td>${{ number_format($plan->price, 2) }}</td>
                                <td>{{ $plan->duration }}</td>
                                <td>{{ $plan->max_quizzes ?? 'Unlimited' }}</td>
                                <td>
                                    <span class="badge {{ $plan->is_active ? 'bg-success' : 'bg-secondary' }}">
                                        {{ $plan->is_active ? 'Active' : 'Inactive' }}
                                    </span>
                                </td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('admin.subscription-plans.edit', $plan) }}" class="btn btn-sm btn-primary">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form action="{{ route('admin.subscription-plans.destroy', $plan) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this plan?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center">No subscription plans found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            <div class="d-flex justify-content-center mt-4">
                {{ $plans->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
