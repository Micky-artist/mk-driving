@extends('admin.layouts.app')

@push('styles')
<style>
    .subscription-card {
        transition: all 0.3s ease;
        border: 1px solid #e4e6ef;
        border-radius: 0.65rem;
    }
    .subscription-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 0.5rem 1.5rem 0.5rem rgba(0, 0, 0, 0.05);
    }
    .subscription-avatar {
        width: 50px;
        height: 50px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: bold;
        font-size: 1.25rem;
        color: #fff;
    }
    .tab-content {
        padding: 1.5rem 0;
    }
    .search-box {
        max-width: 300px;
    }
    .stats-card {
        border-left: 4px solid;
        border-radius: 0.5rem;
    }
    .stats-icon {
        font-size: 2rem;
        opacity: 0.3;
    }
</style>
@endpush

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-6">
        <div>
            <h1 class="h3 mb-1">Subscription Management</h1>
            <p class="text-muted mb-0">Approve, reject, or manage user subscriptions</p>
        </div>
        <div class="search-box">
            <div class="input-group">
                <span class="input-group-text bg-transparent"><i class="fas fa-search"></i></span>
                <input type="text" id="searchInput" class="form-control form-control-solid" placeholder="Search subscriptions..." value="{{ request('search') }}">
                @if(request('search'))
                    <button id="clearSearch" class="btn btn-icon btn-active-light-primary" type="button">
                        <i class="fas fa-times"></i>
                    </button>
                @endif
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success d-flex align-items-center p-5 mb-6">
            <i class="fas fa-check-circle me-3"></i>
            <div class="d-flex flex-column">
                <h4 class="mb-1">Success</h4>
                <span>{{ session('success') }}</span>
            </div>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger d-flex align-items-center p-5 mb-6">
            <i class="fas fa-exclamation-circle me-3"></i>
            <div class="d-flex flex-column">
                <h4 class="mb-1">Error</h4>
                <span>{{ session('error') }}</span>
            </div>
        </div>
    @endif

    <!-- Stats Cards -->
    <div class="row g-6 mb-6">
        <div class="col-xl-3 col-sm-6">
            <div class="card bg-light-primary stats-card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <span class="text-muted fw-bold fs-6 d-block">Total Subscriptions</span>
                            <span class="text-dark fw-bolder fs-3">{{ $stats['total'] }}</span>
                        </div>
                        <div class="text-primary">
                            <i class="fas fa-users stats-icon"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-sm-6">
            <div class="card bg-light-warning stats-card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <span class="text-muted fw-bold fs-6 d-block">Pending Approval</span>
                            <span class="text-dark fw-bolder fs-3">{{ $stats['pending'] }}</span>
                        </div>
                        <div class="text-warning">
                            <i class="fas fa-clock stats-icon"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-sm-6">
            <div class="card bg-light-success stats-card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <span class="text-muted fw-bold fs-6 d-block">Active</span>
                            <span class="text-dark fw-bolder fs-3">{{ $stats['active'] }} <small class="text-muted fs-7">({{ $stats['active_percentage'] }}%)</small></span>
                        </div>
                        <div class="text-success">
                            <i class="fas fa-check-circle stats-icon"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-sm-6">
            <div class="card bg-light-info stats-card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <span class="text-muted fw-bold fs-6 d-block">Total Revenue</span>
                            <span class="text-dark fw-bolder fs-3">{{ $stats['revenue_formatted'] }} RWF</span>
                        </div>
                        <div class="text-info">
                            <i class="fas fa-dollar-sign stats-icon"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header border-0 pt-6">
            <div class="card-title">
                <ul class="nav nav-pills nav-pills-custom" role="tablist">
                    <li class="nav-item me-2 mb-2">
                        <a class="nav-link btn btn-outline btn-outline-dashed btn-active-light-primary {{ $status === 'all' ? 'active' : '' }}" 
                           href="{{ route('admin.subscriptions.index', ['status' => 'all']) }}">
                            <span class="nav-text">All</span>
                            <span class="badge badge-light-primary">{{ $statusCounts['all'] ?? 0 }}</span>
                        </a>
                    </li>
                    <li class="nav-item me-2 mb-2">
                        <a class="nav-link btn btn-outline btn-outline-dashed btn-active-light-warning {{ $status === 'pending' ? 'active' : '' }}" 
                           href="{{ route('admin.subscriptions.index', ['status' => 'pending']) }}">
                            <span class="nav-text">Pending</span>
                            <span class="badge badge-light-warning">{{ $statusCounts[\App\Enums\SubscriptionStatus::PENDING->value] ?? 0 }}</span>
                        </a>
                    </li>
                    <li class="nav-item me-2 mb-2">
                        <a class="nav-link btn btn-outline btn-outline-dashed btn-active-light-success {{ $status === 'active' ? 'active' : '' }}" 
                           href="{{ route('admin.subscriptions.index', ['status' => 'active']) }}">
                            <span class="nav-text">Active</span>
                            <span class="badge badge-light-success">{{ $statusCounts[\App\Enums\SubscriptionStatus::ACTIVE->value] ?? 0 }}</span>
                        </a>
                    </li>
                    <li class="nav-item me-2 mb-2">
                        <a class="nav-link btn btn-outline btn-outline-dashed btn-active-light-danger {{ $status === 'cancelled' ? 'active' : '' }}" 
                           href="{{ route('admin.subscriptions.index', ['status' => 'cancelled']) }}">
                            <span class="nav-text">Cancelled</span>
                            <span class="badge badge-light-danger">{{ $statusCounts[\App\Enums\SubscriptionStatus::CANCELLED->value] ?? 0 }}</span>
                        </a>
                    </li>
                    <li class="nav-item mb-2">
                        <a class="nav-link btn btn-outline btn-outline-dashed btn-active-light-dark {{ $status === 'expired' ? 'active' : '' }}" 
                           href="{{ route('admin.subscriptions.index', ['status' => 'expired']) }}">
                            <span class="nav-text">Expired</span>
                            <span class="badge badge-light-dark">{{ $statusCounts[\App\Enums\SubscriptionStatus::EXPIRED->value] ?? 0 }}</span>
                        </a>
                    </li>
                </ul>
            </div>
        </div>
        <div class="card-body pt-0">
            <div id="subscriptionList" class="tab-content">
                @include('admin.subscriptions.partials.subscription-list', ['subscriptions' => $subscriptions, 'status' => $status])
            </div>
        </div>
        <div class="card-footer d-flex justify-content-between align-items-center flex-wrap">
            <div class="d-flex align-items-center py-3">
                <span class="text-muted">Showing {{ $subscriptions->firstItem() ?? 0 }} to {{ $subscriptions->lastItem() ?? 0 }} of {{ $subscriptions->total() }} entries</span>
            </div>
            <div class="d-flex justify-content-end py-3">
                {{ $subscriptions->appends(request()->query())->links() }}
            </div>
        </div>
    </div>
                        </button>
                        @if(request('search'))
                            <a href="{{ route('admin.subscriptions.index', ['status' => $status]) }}" class="btn btn-secondary ms-2">
                                <i class="fas fa-times"></i>
                            </a>
                        @endif
                    </form>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="mb-3">
                <div class="btn-group" role="group">
                    <a href="{{ route('admin.subscriptions.index') }}" class="btn {{ $status === 'all' ? 'btn-primary' : 'btn-outline-primary' }}">
                        All
                    </a>
                    @foreach(\App\Enums\SubscriptionStatus::cases() as $statusOption)
                        <a href="{{ route('admin.subscriptions.index', ['status' => strtolower($statusOption->name)]) }}" 
                           class="btn {{ $status === strtolower($statusOption->name) ? 'btn-primary' : 'btn-outline-primary' }}">
                            {{ $statusOption->name }}
                            @if($statusCounts[$statusOption->value] ?? 0 > 0)
                                <span class="badge bg-{{ $status === strtolower($statusOption->name) ? 'light' : 'primary' }} ms-1">
                                    {{ $statusCounts[$statusOption->value] ?? 0 }}
                                </span>
                            @endif
                        </a>
                    @endforeach
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-bordered" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>Reference</th>
                            <th>User</th>
                            <th>Plan</th>
                            <th>Amount</th>
                            <th>Period</th>
                            <th>Status</th>
                            <th>Payment</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($subscriptions as $subscription)
                            <tr>
                                <td>
                                    <a href="{{ route('admin.subscriptions.show', $subscription) }}">
                                        {{ $subscription->payment_reference }}
                                    </a>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="avatar-sm me-2">
                                            <span class="avatar-title rounded-circle bg-light text-dark">
                                                {{ substr($subscription->user->first_name, 0, 1) }}{{ substr($subscription->user->last_name, 0, 1) }}
                                            </span>
                                        </div>
                                        <div>
                                            <div>{{ $subscription->user->full_name }}</div>
                                            <div class="text-muted small">{{ $subscription->user->email }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge" style="background-color: {{ $subscription->subscriptionPlan->color }}; color: white;">
                                        {{ $subscription->subscriptionPlan->name }}
                                    </span>
                                </td>
                                <td>${{ number_format($subscription->amount, 2) }}</td>
                                <td>
                                    @if($subscription->start_date && $subscription->end_date)
                                        <div>{{ $subscription->start_date->format('M d, Y') }} - {{ $subscription->end_date->format('M d, Y') }}</div>
                                        <div class="progress" style="height: 5px;">
                                            @php
                                                $totalDays = $subscription->start_date->diffInDays($subscription->end_date);
                                                $daysLeft = now()->diffInDays($subscription->end_date, false);
                                                $percentage = $totalDays > 0 ? min(100, max(0, (($totalDays - $daysLeft) / $totalDays) * 100)) : 0;
                                                $color = match(true) {
                                                    $daysLeft <= 0 => 'bg-danger',
                                                    $daysLeft <= 7 => 'bg-warning',
                                                    default => 'bg-success'
                                                };
                                            @endphp
                                            <div class="progress-bar {{ $color }}" role="progressbar" style="width: {{ $percentage }}%" 
                                                 aria-valuenow="{{ $percentage }}" aria-valuemin="0" aria-valuemax="100">
                                            </div>
                                        </div>
                                        <small class="text-muted">{{ $daysLeft > 0 ? "$daysLeft days left" : 'Expired' }}</small>
                                    @else
                                        <span class="text-muted">N/A</span>
                                    @endif
                                </td>
                                <td>
                                    @php
                                        $statusClass = match($subscription->status) {
                                            \App\Enums\SubscriptionStatus::PENDING->value => 'bg-warning',
                                            \App\Enums\SubscriptionStatus::ACTIVE->value => 'bg-success',
                                            \App\Enums\SubscriptionStatus::EXPIRED->value => 'bg-secondary',
                                            \App\Enums\SubscriptionStatus::CANCELLED->value => 'bg-danger',
                                            default => 'bg-secondary',
                                        };
                                    @endphp
                                    <span class="badge {{ $statusClass }}">
                                        {{ $subscription->status }}
                                    </span>
                                </td>
                                <td>
                                    @php
                                        $paymentClass = match($subscription->payment_status) {
                                            \App\Enums\PaymentStatus::PENDING->value => 'bg-warning',
                                            \App\Enums\PaymentStatus::COMPLETED->value => 'bg-success',
                                            \App\Enums\PaymentStatus::FAILED->value => 'bg-danger',
                                            default => 'bg-secondary',
                                        };
                                    @endphp
                                    <span class="badge {{ $paymentClass }}">
                                        {{ $subscription->payment_status }}
                                    </span>
                                    @if($subscription->payment_method)
                                        <div class="small text-muted">{{ $subscription->payment_method }}</div>
                                    @endif
                                </td>
                                <td>
                                    <div class="btn-group">
                                        <a href="{{ route('admin.subscriptions.show', $subscription) }}" 
                                           class="btn btn-sm btn-primary" 
                                           title="View Details">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        
                                        @if($subscription->status === \App\Enums\SubscriptionStatus::PENDING->value)
                                            <button type="button" 
                                                    class="btn btn-sm btn-success approve-btn" 
                                                    data-id="{{ $subscription->id }}"
                                                    title="Approve">
                                                <i class="fas fa-check"></i>
                                            </button>
                                            
                                            <button type="button" 
                                                    class="btn btn-sm btn-danger reject-btn" 
                                                    data-id="{{ $subscription->id }}"
                                                    title="Reject">
                                                <i class="fas fa-times"></i>
                                            </button>
                                        @endif
                                        
                                        @if($subscription->status === \App\Enums\SubscriptionStatus::ACTIVE->value)
                                            <button type="button" 
                                                    class="btn btn-sm btn-warning cancel-btn" 
                                                    data-id="{{ $subscription->id }}"
                                                    title="Cancel">
                                                <i class="fas fa-ban"></i>
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center">No subscriptions found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            <div class="d-flex justify-content-center mt-4">
                {{ $subscriptions->appends(request()->query())->links() }}
            </div>
        </div>
    </div>
</div>

<!-- Approve Subscription Modal -->
<div class="modal fade" id="approveSubscriptionModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form id="approveForm" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-header">
                    <h5 class="modal-title" id="approveModalLabel">Approve Subscription</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p>Are you sure you want to approve this subscription?</p>
                    <div class="form-group">
                        <label for="approveNotes">Notes (Optional)</label>
                        <textarea class="form-control" id="approveNotes" name="notes" rows="3" placeholder="Add any notes here..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success">Approve</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Reject Subscription Modal -->
<div class="modal fade" id="rejectSubscriptionModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form id="rejectForm" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-header">
                    <h5 class="modal-title" id="rejectModalLabel">Reject Subscription</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p>Are you sure you want to reject this subscription?</p>
                    <div class="form-group">
                        <label for="rejectReason">Reason for rejection <span class="text-danger">*</span></label>
                        <textarea class="form-control" id="rejectReason" name="reason" rows="3" required placeholder="Please provide a reason for rejection..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger">Reject</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Cancel Subscription Modal -->
<div class="modal fade" id="cancelSubscriptionModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form id="cancelSubscriptionForm" action="" method="POST">
                @csrf
                <input type="hidden" name="subscription_id" value="">
                <div class="modal-header">
                    <h5 class="modal-title">Cancel Subscription</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>Please provide a reason for cancelling this subscription:</p>
                    <div class="mb-3">
                        <label for="cancelReason" class="form-label required">Reason</label>
                        <textarea class="form-control" id="cancelReason" name="reason" rows="3" required placeholder="Enter the reason for cancellation..."></textarea>
                        <div class="invalid-feedback">Please provide a reason for cancellation.</div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-warning">Cancel Subscription</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Payment Proof Modal -->
<div class="modal fade" id="paymentProofModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Payment Proof</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center">
                <img src="" alt="Payment Proof" class="img-fluid" id="paymentProofImage">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
                <a href="#" class="btn btn-primary" id="downloadProof">
                    <i class="fas fa-download me-2"></i>Download
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Toast Container -->
<div class="position-fixed bottom-0 end-0 p-3" style="z-index: 11">
    <div id="toastContainer" class="toast-container">
        <!-- Toasts will be inserted here by JavaScript -->
    </div>
</div>

@push('scripts')
<script src="{{ asset('js/admin/subscriptions.js') }}"></script>
@endpush

@endsection
