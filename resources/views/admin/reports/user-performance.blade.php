@extends('admin.layouts.app')

@section('title', 'User Performance')

@section('content')
<div class="container-fluid px-4 py-6">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">User Performance</h1>
        <div>
            <form method="GET" class="d-inline-block me-2">
                <select name="period" class="form-select form-select-sm" onchange="this.form.submit()">
                    <option value="7" {{ request('period') == '7' ? 'selected' : '' }}>7 days</option>
                    <option value="30" {{ request('period') == '30' ? 'selected' : '' }}>30 days</option>
                    <option value="90" {{ request('period') == '90' ? 'selected' : '' }}>90 days</option>
                </select>
            </form>
            <a href="{{ route('admin.reports.export', 'user-performance') }}" class="btn btn-outline-primary btn-sm">
                <i class="fas fa-download me-2"></i>Export Excel
            </a>
        </div>
    </div>

    <!-- Pass Rates by Quiz -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Pass Rates by Quiz</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Quiz Name</th>
                                    <th>Total Attempts</th>
                                    <th>Passed Attempts</th>
                                    <th>Pass Rate</th>
                                    <th>Average Score</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($passRatesByQuiz as $quiz)
                                <tr>
                                    <td>{{ $quiz['quiz_name'] }}</td>
                                    <td>{{ $quiz['total_attempts'] }}</td>
                                    <td>{{ $quiz['passed_attempts'] }}</td>
                                    <td>
                                        <div class="progress">
                                            <div class="progress-bar {{ $quiz['pass_rate'] >= 70 ? 'bg-success' : ($quiz['pass_rate'] >= 50 ? 'bg-warning' : 'bg-danger') }}" 
                                                 role="progressbar" 
                                                 style="width: {{ $quiz['pass_rate'] }}%">
                                                {{ number_format($quiz['pass_rate'], 1) }}%
                                            </div>
                                        </div>
                                    </td>
                                    <td>{{ number_format($quiz['average_score'], 1) }}%</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Top Performing Users -->
        <div class="col-lg-6 mb-4">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-success">Top Performing Users</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>User</th>
                                    <th>Attempts</th>
                                    <th>Avg Score</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($topUsers as $user)
                                <tr>
                                    <td>{{ $user->name }}</td>
                                    <td>{{ $user->quiz_attempts_count }}</td>
                                    <td>
                                        <span class="badge bg-success">{{ number_format($user->quiz_attempts_avg_score, 1) }}%</span>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Common Failure Points -->
        <div class="col-lg-6 mb-4">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-danger">Common Failure Points</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Question</th>
                                    <th>Failure Count</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($failedQuestions as $question)
                                <tr>
                                    <td class="text-truncate" style="max-width: 300px;" title="{{ $question->question_text }}">
                                        {{ Str::limit($question->question_text, 80) }}
                                    </td>
                                    <td>
                                        <span class="badge bg-danger">{{ $question->failure_count }}</span>
                                    </td>
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
@endsection
