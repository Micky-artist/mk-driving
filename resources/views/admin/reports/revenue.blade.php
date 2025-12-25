@extends('admin.layouts.app')

@section('title', 'Revenue Analytics')

@section('content')
<div class="container-fluid px-4 py-6">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">Revenue Analytics</h1>
        <div>
            <form method="GET" class="d-inline-block me-2">
                <select name="period" class="form-select form-select-sm" onchange="this.form.submit()">
                    <option value="7" {{ request('period') == '7' ? 'selected' : '' }}>7 days</option>
                    <option value="30" {{ request('period') == '30' ? 'selected' : '' }}>30 days</option>
                    <option value="90" {{ request('period') == '90' ? 'selected' : '' }}>90 days</option>
                </select>
            </form>
            <a href="{{ route('admin.reports.export', 'revenue') }}" class="btn btn-outline-primary btn-sm">
                <i class="fas fa-download me-2"></i>Export Excel
            </a>
        </div>
    </div>

    <!-- Key Revenue Metrics -->
    <div class="row mb-4">
        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                {{ __('admin.reports.monthly_recurring_revenue') }}
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">RWF {{ number_format($mrr, 0) }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-calendar-alt fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                {{ __('admin.reports.customer_acquisition_cost') }}
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">RWF {{ number_format($cac, 0) }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-user-plus fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                {{ __('admin.reports.active_subscriptions') }}
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $revenueByPlan->sum('count') }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-credit-card fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Revenue Trends Chart -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Revenue Trends</h6>
                </div>
                <div class="card-body">
                    <div class="chart-container">
                        <canvas id="revenueChart" width="400" height="200"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Revenue by Subscription Plan -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-success">Revenue by Plan</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-8">
                            <div class="chart-container">
                                <canvas id="planRevenueChart" width="400" height="300"></canvas>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="table-responsive">
                                <table class="table table-sm">
                                    <thead>
                                        <tr>
                                            <th>{{ __('admin.reports.plan') }}</th>
                                            <th>{{ __('admin.reports.subscriptions') }}</th>
                                            <th>{{ __('admin.reports.revenue') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($revenueByPlan as $plan)
                                        <tr>
                                            <td>{{ $plan->plan->name ?? 'Unknown' }}</td>
                                            <td>{{ $plan->count }}</td>
                                            <td>RWF {{ number_format($plan->total_revenue, 0) }}</td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Revenue Trends Chart
    const revenueCtx = document.getElementById('revenueChart').getContext('2d');
    const revenueChart = new Chart(revenueCtx, {
        type: 'line',
        data: {
            labels: @json($revenueTrends->pluck('date')),
            datasets: [
                {
                    label: 'Daily Revenue',
                    data: @json($revenueTrends->pluck('revenue')),
                    borderColor: 'rgb(75, 192, 192)',
                    backgroundColor: 'rgba(75, 192, 192, 0.2)',
                    tension: 0.1,
                    yAxisID: 'y'
                },
                {
                    label: 'New Subscriptions',
                    data: @json($revenueTrends->pluck('subscriptions')),
                    borderColor: 'rgb(255, 99, 132)',
                    backgroundColor: 'rgba(255, 99, 132, 0.2)',
                    tension: 0.1,
                    yAxisID: 'y1'
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    type: 'linear',
                    display: true,
                    position: 'left',
                    beginAtZero: true,
                    title: {
                        display: true,
                        text: 'Revenue (RWF)'
                    }
                },
                y1: {
                    type: 'linear',
                    display: true,
                    position: 'right',
                    beginAtZero: true,
                    title: {
                        display: true,
                        text: 'Subscriptions'
                    },
                    grid: {
                        drawOnChartArea: false
                    }
                }
            }
        }
    });

    // Plan Revenue Chart
    const planCtx = document.getElementById('planRevenueChart').getContext('2d');
    const planChart = new Chart(planCtx, {
        type: 'doughnut',
        data: {
            labels: @json($revenueByPlan->pluck('plan.name')),
            datasets: [{
                data: @json($revenueByPlan->pluck('total_revenue')),
                backgroundColor: [
                    'rgba(255, 99, 132, 0.8)',
                    'rgba(54, 162, 235, 0.8)',
                    'rgba(255, 206, 86, 0.8)',
                    'rgba(75, 192, 192, 0.8)',
                    'rgba(153, 102, 255, 0.8)'
                ]
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom'
                }
            }
        }
    });
</script>
@endpush
