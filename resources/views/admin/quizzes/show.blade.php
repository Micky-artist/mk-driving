@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3">{{ $quiz->getTranslation('title', 'en') }}</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.quizzes.index') }}">Quizzes</a></li>
                    <li class="breadcrumb-item active" aria-current="page">View</li>
                </ol>
            </nav>
        </div>
        <div>
            <a href="{{ route('admin.quizzes.edit', $quiz) }}" class="btn btn-primary">
                <i class="fas fa-edit"></i> Edit
            </a>
            <a href="{{ route('admin.quizzes.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left"></i> Back to List
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-md-8">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Quiz Details</h5>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <h6>English</h6>
                            <p class="mb-1"><strong>Title:</strong> {{ $quiz->getTranslation('title', 'en') }}</p>
                            <p><strong>Description:</strong> {{ $quiz->getTranslation('description', 'en') }}</p>
                        </div>
                        <div class="col-md-6">
                            <h6>Kinyarwanda</h6>
                            <p class="mb-1"><strong>Title:</strong> {{ $quiz->getTranslation('title', 'rw') }}</p>
                            <p><strong>Description:</strong> {{ $quiz->getTranslation('description', 'rw') }}</p>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>Topics:</strong>
                                @if(is_array($quiz->topics))
                                    @foreach($quiz->topics as $topic)
                                        <span class="badge bg-secondary me-1">{{ $topic }}</span>
                                    @endforeach
                                @endif
                            </p>
                            <p><strong>Time Limit:</strong> {{ $quiz->time_limit_minutes }} minutes</p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Status:</strong>
                                <span class="badge {{ $quiz->is_active ? 'bg-success' : 'bg-secondary' }}">
                                    {{ $quiz->is_active ? 'Active' : 'Inactive' }}
                                </span>
                            </p>
                            <p><strong>Subscription Plan:</strong>
                                @if($quiz->is_guest_quiz)
                                    <span class="badge bg-info">Guest Quiz</span>
                                @elseif($quiz->subscriptionPlan)
                                    {{ $quiz->subscriptionPlan->name }}
                                @else
                                    <span class="text-muted">None</span>
                                @endif
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Questions ({{ $quiz->questions->count() }})</h5>
                </div>
                <div class="card-body">
                    @foreach($quiz->questions as $index => $question)
                        <div class="card mb-3">
                            <div class="card-header bg-light">
                                <h6 class="mb-0">Question #{{ $index + 1 }}</h6>
                            </div>
                            <div class="card-body">
                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <p><strong>English:</strong> {{ $question->getTranslation('text', 'en') }}</p>
                                    </div>
                                    <div class="col-md-6">
                                        <p><strong>Kinyarwanda:</strong> {{ $question->getTranslation('text', 'rw') }}</p>
                                    </div>
                                </div>

                                <h6>Options:</h6>
                                <div class="row">
                                    @foreach($question->options as $oIndex => $option)
                                        <div class="col-md-6 mb-2">
                                            <div class="p-2 rounded {{ $oIndex == $question->correct_option_index ? 'bg-success bg-opacity-10 border border-success' : 'bg-light' }}">
                                                <div class="d-flex">
                                                    <div class="flex-shrink-0 me-2">
                                                        <span class="badge {{ $oIndex == $question->correct_option_index ? 'bg-success' : 'bg-secondary' }}">
                                                            {{ chr(65 + $oIndex) }}
                                                        </span>
                                                    </div>
                                                    <div>
                                                        <div><strong>EN:</strong> {{ $option->getTranslation('text', 'en') }}</div>
                                                        <div class="text-muted"><strong>RW:</strong> {{ $option->getTranslation('text', 'rw') }}</div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Quiz Statistics</h5>
                </div>
                <div class="card-body">
                    <div class="text-center mb-4">
                        <div class="display-4">{{ $quiz->questions_count ?? 0 }}</div>
                        <div class="text-muted">Questions</div>
                    </div>
                    
                    <div class="mb-3">
                        <div class="d-flex justify-content-between mb-1">
                            <span>Completion Rate</span>
                            <span>75%</span>
                        </div>
                        <div class="progress" style="height: 10px;">
                            <div class="progress-bar bg-success" role="progressbar" style="width: 75%" 
                                 aria-valuenow="75" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <div class="d-flex justify-content-between mb-1">
                            <span>Average Score</span>
                            <span>68%</span>
                        </div>
                        <div class="progress" style="height: 10px;">
                            <div class="progress-bar bg-info" role="progressbar" style="width: 68%" 
                                 aria-valuenow="68" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                    </div>
                    
                    <hr>
                    
                    <div class="d-grid gap-2">
                        <a href="#" class="btn btn-outline-primary">
                            <i class="fas fa-chart-bar"></i> View Analytics
                        </a>
                        <a href="#" class="btn btn-outline-secondary">
                            <i class="fas fa-download"></i> Export Results
                        </a>
                    </div>
                </div>
            </div>
            
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Quick Actions</h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="{{ route('admin.quizzes.edit', $quiz) }}" class="btn btn-primary">
                            <i class="fas fa-edit"></i> Edit Quiz
                        </a>
                        <form action="{{ route('admin.quizzes.toggle-guest', $quiz) }}" method="POST" class="d-grid">
                            @csrf
                            <button type="submit" class="btn {{ $quiz->is_guest_quiz ? 'btn-warning' : 'btn-outline-secondary' }}">
                                <i class="fas {{ $quiz->is_guest_quiz ? 'fa-user-minus' : 'fa-user-plus' }}"></i>
                                {{ $quiz->is_guest_quiz ? 'Remove from Guest Quizzes' : 'Make Guest Quiz' }}
                            </button>
                        </form>
                        <button type="button" class="btn btn-outline-danger" data-bs-toggle="modal" data-bs-target="#deleteModal">
                            <i class="fas fa-trash"></i> Delete Quiz
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteModalLabel">Confirm Deletion</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete this quiz? This action cannot be undone.</p>
                <p class="mb-0"><strong>Quiz:</strong> {{ $quiz->getTranslation('title', 'en') }}</p>
                <p class="text-danger"><strong>Warning:</strong> This will also delete all related questions, options, and attempt data.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <form action="{{ route('admin.quizzes.destroy', $quiz) }}" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Delete Quiz</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .progress {
        background-color: #e9ecef;
        border-radius: 0.25rem;
    }
    .progress-bar {
        transition: width 0.6s ease;
    }
</style>
@endpush
