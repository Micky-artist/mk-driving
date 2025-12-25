@extends('admin.layouts.app')

@section('title', 'Content Effectiveness')

@section('content')
<div class="container-fluid px-4 py-6">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">Content Effectiveness</h1>
        <div>
            <form method="GET" class="d-inline-block me-2">
                <select name="period" class="form-select form-select-sm" onchange="this.form.submit()">
                    <option value="7" {{ request('period') == '7' ? 'selected' : '' }}>7 days</option>
                    <option value="30" {{ request('period') == '30' ? 'selected' : '' }}>30 days</option>
                    <option value="90" {{ request('period') == '90' ? 'selected' : '' }}>90 days</option>
                </select>
            </form>
            <a href="{{ route('admin.reports.export', 'content-effectiveness') }}" class="btn btn-outline-primary btn-sm">
                <i class="fas fa-download me-2"></i>Export Excel
            </a>
        </div>
    </div>

    <!-- Content Overview Cards -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Easy Questions
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $questionDifficulty->where('difficulty', 'easy')->count() }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-smile fa-2x text-gray-300"></i>
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
                                Medium Questions
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $questionDifficulty->where('difficulty', 'medium')->count() }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <div class="fas fa-meh fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-danger shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">
                                Hard Questions
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $questionDifficulty->where('difficulty', 'hard')->count() }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-frown fa-2x text-gray-300"></i>
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
                                Avg Success Rate
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ number_format($questionDifficulty->avg('success_rate'), 1) }}%
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-chart-pie fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Question Difficulty Analysis -->
        <div class="col-lg-8 mb-4">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Question Difficulty Analysis</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Question</th>
                                    <th>Attempts</th>
                                    <th>Success Rate</th>
                                    <th>Difficulty</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($questionDifficulty->take(20) as $question)
                                <tr>
                                    <td class="text-truncate" style="max-width: 400px;" title="{{ $question['question_text'] }}">
                                        {{ Str::limit($question['question_text'], 100) }}
                                    </td>
                                    <td>{{ $question['total_attempts'] }}</td>
                                    <td>
                                        <div class="progress">
                                            <div class="progress-bar {{ $question['success_rate'] >= 70 ? 'bg-success' : ($question['success_rate'] >= 40 ? 'bg-warning' : 'bg-danger') }}" 
                                                 role="progressbar" 
                                                 style="width: {{ $question['success_rate'] }}%">
                                                {{ number_format($question['success_rate'], 1) }}%
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge bg-{{ $question['difficulty'] == 'easy' ? 'success' : ($question['difficulty'] == 'medium' ? 'warning' : 'danger') }}">
                                            {{ ucfirst($question['difficulty']) }}
                                        </span>
                                    </td>
                                    <td>
                                        @if($question['difficulty'] == 'hard')
                                            <button class="btn btn-sm btn-outline-warning" onclick="reviewQuestion({{ $question['question_id'] }})">
                                                <i class="fas fa-edit"></i> Review
                                            </button>
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

        <!-- Topic Performance -->
        <div class="col-lg-4 mb-4">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-info">Topic Performance</h6>
                </div>
                <div class="card-body">
                    @if($topicPerformance->isNotEmpty())
                        <div class="chart-container mb-3">
                            <canvas id="topicChart" width="400" height="300"></canvas>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Topic</th>
                                        <th>Success Rate</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($topicPerformance as $topic)
                                    <tr>
                                        <td>{{ $topic['category'] }}</td>
                                        <td>
                                            <span class="badge bg-{{ $topic['success_rate'] >= 70 ? 'success' : ($topic['success_rate'] >= 50 ? 'warning' : 'danger') }}">
                                                {{ number_format($topic['success_rate'], 1) }}%
                                            </span>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-2"></i>
                            {{ __('admin.reports.no_topic_data') }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Content Recommendations -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-warning">Content Recommendations</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6 class="text-danger"><i class="fas fa-exclamation-triangle me-2"></i>Urgent Attention</h6>
                            <ul class="list-unstyled">
                                @foreach($questionDifficulty->where('difficulty', 'hard')->take(5) as $question)
                                    <li class="mb-2">
                                        <small>{{ Str::limit($question['question_text'], 80) }}</small>
                                        <br>
                                        <span class="badge bg-danger">{{ number_format($question['success_rate'], 1) }}% Success Rate</span>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                        <div class="col-md-6">
                            <h6 class="text-success"><i class="fas fa-check-circle me-2"></i>Well Performing</h6>
                            <ul class="list-unstyled">
                                @foreach($questionDifficulty->where('difficulty', 'easy')->take(5) as $question)
                                    <li class="mb-2">
                                        <small>{{ Str::limit($question['question_text'], 80) }}</small>
                                        <br>
                                        <span class="badge bg-success">{{ number_format($question['success_rate'], 1) }}% Success Rate</span>
                                    </li>
                                @endforeach
                            </ul>
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
    // Topic Performance Chart
    @if($topicPerformance->isNotEmpty())
    const topicCtx = document.getElementById('topicChart').getContext('2d');
    const topicChart = new Chart(topicCtx, {
        type: 'radar',
        data: {
            labels: @json($topicPerformance->pluck('category')),
            datasets: [{
                label: '{{ __("admin.reports.success_rate") }}',
                data: @json($topicPerformance->pluck('success_rate')),
                borderColor: 'rgba(75, 192, 192, 1)',
                backgroundColor: 'rgba(75, 192, 192, 0.2)',
                pointBackgroundColor: 'rgba(75, 192, 192, 1)',
                pointBorderColor: '#fff',
                pointHoverBackgroundColor: '#fff',
                pointHoverBorderColor: 'rgba(75, 192, 192, 1)'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                r: {
                    beginAtZero: true,
                    max: 100,
                    ticks: {
                        stepSize: 20
                    }
                }
            }
        }
    });
    @endif

    function reviewQuestion(questionId) {
        if (confirm('Are you sure you want to review this question?')) {
            window.open('/admin/questions/' + questionId + '/edit', '_blank');
        }
    }
</script>
@endpush
