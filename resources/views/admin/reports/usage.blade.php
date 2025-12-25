@extends('admin.layouts.app')

@section('title', 'Usage Analytics')

@section('content')
<div class="container-fluid px-4 py-6">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">Usage Analytics</h1>
        <div>
            <form method="GET" class="d-inline-block me-2">
                <select name="period" class="form-select form-select-sm" onchange="this.form.submit()">
                    <option value="7" {{ request('period') == '7' ? 'selected' : '' }}>7 days</option>
                    <option value="30" {{ request('period') == '30' ? 'selected' : '' }}>30 days</option>
                    <option value="90" {{ request('period') == '90' ? 'selected' : '' }}>90 days</option>
                </select>
            </form>
            <a href="{{ route('admin.reports.export', 'usage') }}" class="btn btn-outline-primary btn-sm">
                <i class="fas fa-download me-2"></i>Export Excel
            </a>
        </div>
    </div>

    <!-- Daily Active Users Chart -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Daily Active Users</h6>
                </div>
                <div class="card-body">
                    <div class="chart-container">
                        <canvas id="dauChart" width="400" height="200"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Quiz Completion Rates -->
        <div class="col-lg-8 mb-4">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-success">Quiz Completion Rates</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Quiz Name</th>
                                    <th>Completion Rate</th>
                                    <th>Avg Completion Time</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($completionRates as $quiz)
                                <tr>
                                    <td>{{ $quiz['quiz_name'] }}</td>
                                    <td>
                                        <div class="progress">
                                            <div class="progress-bar {{ $quiz['completion_rate'] >= 80 ? 'bg-success' : ($quiz['completion_rate'] >= 60 ? 'bg-warning' : 'bg-danger') }}" 
                                                 role="progressbar" 
                                                 style="width: {{ $quiz['completion_rate'] }}%">
                                                {{ number_format($quiz['completion_rate'], 1) }}%
                                            </div>
                                        </div>
                                    </td>
                                    <td>{{ number_format($quiz['avg_completion_time'], 1) }} minutes</td>
                                    <td>
                                        @if($quiz['completion_rate'] >= 80)
                                            <span class="badge bg-success">Excellent</span>
                                        @elseif($quiz['completion_rate'] >= 60)
                                            <span class="badge bg-warning">Good</span>
                                        @else
                                            <span class="badge bg-danger">Needs Improvement</span>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Peak Usage Hours -->
        <div class="col-lg-4 mb-4">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-info">Peak Usage Hours</h6>
                </div>
                <div class="card-body">
                    <div class="chart-container">
                        <canvas id="peakHoursChart" width="400" height="300"></canvas>
                    </div>
                    <div class="mt-3">
                        <small class="text-muted">Busiest Hour:</small>
                        <strong class="text-primary">
                            {{ $peakHours->first()->hour }}:00 - 
                            {{ $peakHours->first()->attempts }} attempts
                        </strong>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Usage Summary Cards -->
    <div class="row">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Avg Daily Users
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ number_format($dailyActiveUsers->avg('active_users'), 0) }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-users fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Avg Completion Rate
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ number_format($completionRates->avg('completion_rate'), 1) }}%
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-check-circle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Avg Session Time
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ number_format($completionRates->avg('avg_completion_time'), 1) }} minutes
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-clock fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Total Attempts
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ number_format($peakHours->sum('attempts')) }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-chart-line fa-2x text-gray-300"></i>
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
    // Daily Active Users Chart
    const dauCtx = document.getElementById('dauChart').getContext('2d');
    const dauChart = new Chart(dauCtx, {
        type: 'line',
        data: {
            labels: @json($dailyActiveUsers->pluck('date')),
            datasets: [{
                label: 'Active Users',
                data: @json($dailyActiveUsers->pluck('active_users')),
                borderColor: 'rgb(75, 192, 192)',
                backgroundColor: 'rgba(75, 192, 192, 0.2)',
                tension: 0.1,
                fill: true
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });

    // Peak Hours Chart
    const peakCtx = document.getElementById('peakHoursChart').getContext('2d');
    const peakChart = new Chart(peakCtx, {
        type: 'bar',
        data: {
            labels: @json($peakHours->pluck('hour')->map(function($hour) { return $hour . ':00'; })),
            datasets: [{
                label: '{{ __("admin.reports.attempts") }}',
                data: @json($peakHours->pluck('attempts')),
                backgroundColor: 'rgba(54, 162, 235, 0.8)',
                borderColor: 'rgba(54, 162, 235, 1)',
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true
                }
            },
            plugins: {
                legend: {
                    display: false
                }
            }
        }
    });
</script>
@endpush
