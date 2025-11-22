@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3">Quizzes Management</h1>
        <div>
            <a href="{{ route('admin.quizzes.create') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i> Create New Quiz
            </a>
            <a href="{{ route('admin.quizzes.export.template') }}" class="btn btn-outline-secondary">
                <i class="fas fa-download"></i> Download Template
            </a>
            <button type="button" class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#importModal">
                <i class="fas fa-upload"></i> Import Quizzes
            </button>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Title</th>
                            <th>Topics</th>
                            <th>Questions</th>
                            <th>Time Limit</th>
                            <th>Subscription Plan</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($quizzes as $quiz)
                            <tr>
                                <td>
                                    <a href="{{ route('admin.quizzes.show', $quiz) }}">
                                        {{ $quiz->getTranslation('title', 'en') }}
                                    </a>
                                    @if($quiz->is_guest_quiz)
                                        <span class="badge bg-info">Guest Quiz</span>
                                    @endif
                                </td>
                                <td>
                                    @if(is_array($quiz->topics))
                                        @foreach($quiz->topics as $topic)
                                            <span class="badge bg-secondary me-1">{{ $topic }}</span>
                                        @endforeach
                                    @endif
                                </td>
                                <td>{{ $quiz->questions_count ?? 0 }}</td>
                                <td>{{ $quiz->time_limit_minutes }} mins</td>
                                <td>
                                    {{ $quiz->subscriptionPlan ? $quiz->subscriptionPlan->name : 'N/A' }}
                                </td>
                                <td>
                                    <span class="badge {{ $quiz->is_active ? 'bg-success' : 'bg-secondary' }}">
                                        {{ $quiz->is_active ? 'Active' : 'Inactive' }}
                                    </span>
                                </td>
                                <td>
                                    <div class="btn-group">
                                        <a href="{{ route('admin.quizzes.edit', $quiz) }}" 
                                           class="btn btn-sm btn-outline-primary">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form action="{{ route('admin.quizzes.destroy', $quiz) }}" 
                                              method="POST" 
                                              onsubmit="return confirm('Are you sure you want to delete this quiz?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                        <form action="{{ route('admin.quizzes.toggle-guest', $quiz) }}" 
                                              method="POST">
                                            @csrf
                                            <button type="submit" 
                                                    class="btn btn-sm {{ $quiz->is_guest_quiz ? 'btn-warning' : 'btn-outline-secondary' }}"
                                                    title="{{ $quiz->is_guest_quiz ? 'Remove from Guest Quizzes' : 'Make Guest Quiz' }}">
                                                <i class="fas {{ $quiz->is_guest_quiz ? 'fa-user-minus' : 'fa-user-plus' }}"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center">No quizzes found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Import Modal -->
<div class="modal fade" id="importModal" tabindex="-1" aria-labelledby="importModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('admin.quizzes.import') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="importModalLabel">Import Quizzes</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="quizFile" class="form-label">CSV File</label>
                        <input class="form-control" type="file" id="quizFile" name="file" accept=".csv" required>
                        <div class="form-text">Upload a CSV file with quiz data. <a href="{{ route('admin.quizzes.export.template') }}">Download template</a></div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Import</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Initialize any required JavaScript here
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize tooltips
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
    });
</script>
@endpush
