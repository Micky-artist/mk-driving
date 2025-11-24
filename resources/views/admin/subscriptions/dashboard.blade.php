@extends('admin.layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between mb-4">
        <h1 class="h3">@lang('admin.subscriptions.dashboard_title')</h1>
        <div>
            <a href="{{ route('admin.subscriptions.pending') }}" class="btn btn-primary">
                @lang('admin.subscriptions.view_pending')
            </a>
            <a href="{{ route('admin.subscriptions.active') }}" class="btn btn-success">
                @lang('admin.subscriptions.view_active')
            </a>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="row mb-4">
        <div class="col-md-3 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                @lang('admin.subscriptions.total_subscriptions')</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['total'] }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-calendar fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                @lang('admin.subscriptions.active_subscriptions')</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['active'] }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-check-circle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                @lang('admin.subscriptions.pending_subscriptions')</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['pending'] }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-clock fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                @lang('admin.subscriptions.expiring_soon')</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['expiring_soon'] }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-clock fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Subscriptions -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">@lang('admin.subscriptions.recent_subscriptions')</h6>
            <a href="{{ route('admin.subscriptions.create') }}" class="btn btn-sm btn-primary">
                <i class="fas fa-plus"></i> @lang('admin.common.add_new')
            </a>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>@lang('admin.subscriptions.user')</th>
                            <th>@lang('admin.subscriptions.plan')</th>
                            <th>@lang('admin.subscriptions.status')</th>
                            <th>@lang('admin.subscriptions.starts_at')</th>
                            <th>@lang('admin.subscriptions.ends_at')</th>
                            <th>@lang('admin.common.actions')</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recentSubscriptions as $subscription)
                            <tr>
                                <td>{{ $subscription->user->name ?? 'N/A' }}</td>
                                <td>{{ $subscription->plan->name ?? 'N/A' }}</td>
                                <td>
                                    <span class="badge badge-{{ $subscription->status === 'active' ? 'success' : 'warning' }}">
                                        {{ ucfirst($subscription->status) }}
                                    </span>
                                </td>
                                <td>{{ $subscription->starts_at?->format('Y-m-d H:i') ?? 'N/A' }}</td>
                                <td>{{ $subscription->ends_at?->format('Y-m-d H:i') ?? 'N/A' }}</td>
                                <td>
                                    <a href="{{ route('admin.subscriptions.show', $subscription) }}" 
                                       class="btn btn-sm btn-info" 
                                       title="@lang('admin.common.view')">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center">
                                    @lang('admin.subscriptions.no_subscriptions_found')
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
