@extends('admin.layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between mb-4">
        <h1 class="h3">Subscription Details</h1>
        <div>
            <a href="{{ route('admin.subscriptions.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Back to List
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
    @endif

    <div class="row">
        <div class="col-lg-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">Subscription Information</h6>
                    <div>
                        @if($subscription->status === \App\Enums\SubscriptionStatus::PENDING->value)
                            <button type="button" 
                                    class="btn btn-sm btn-success approve-btn" 
                                    data-id="{{ $subscription->id }}">
                                <i class="fas fa-check"></i> Approve
                            </button>
                            <button type="button" 
                                    class="btn btn-sm btn-danger reject-btn" 
                                    data-id="{{ $subscription->id }}">
                                <i class="fas fa-times"></i> Reject
                            </button>
                        @elseif($subscription->status === \App\Enums\SubscriptionStatus::ACTIVE->value)
                            <button type="button" 
                                    class="btn btn-sm btn-warning cancel-btn" 
                                    data-id="{{ $subscription->id }}">
                                <i class="fas fa-ban"></i> Cancel
                            </button>
                        @endif
                    </div>
                </div>
                <div class="card-body">
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <h5 class="mb-3">Subscription Details</h5>
                            <table class="table table-borderless">
                                <tr>
                                    <th width="40%">Reference:</th>
                                    <td>{{ $subscription->payment_reference }}</td>
                                </tr>
                                <tr>
                                    <th>Status:</th>
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
                                </tr>
                                <tr>
                                    <th>Plan:</th>
                                    <td>
                                        <span class="badge" style="background-color: {{ $subscription->subscriptionPlan->color }}; color: white;">
                                            {{ $subscription->subscriptionPlan->name }}
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Amount:</th>
                                    <td>${{ number_format($subscription->amount, 2) }}</td>
                                </tr>
                                <tr>
                                    <th>Payment Status:</th>
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
                                    </td>
                                </tr>
                                <tr>
                                    <th>Payment Method:</th>
                                    <td>{{ $subscription->payment_method ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <th>Transaction ID:</th>
                                    <td>{{ $subscription->transaction_id ?? 'N/A' }}</td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <h5 class="mb-3">Subscription Period</h5>
                            <table class="table table-borderless">
                                <tr>
                                    <th width="40%">Start Date:</th>
                                    <td>{{ $subscription->start_date ? $subscription->start_date->format('M d, Y') : 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <th>End Date:</th>
                                    <td>{{ $subscription->end_date ? $subscription->end_date->format('M d, Y') : 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <th>Duration:</th>
                                    <td>
                                        @if($subscription->start_date && $subscription->end_date)
                                            {{ $subscription->start_date->diffInDays($subscription->end_date) }} days
                                        @else
                                            N/A
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th>Time Remaining:</th>
                                    <td>
                                        @if($subscription->end_date)
                                            @php
                                                $daysLeft = now()->diffInDays($subscription->end_date, false);
                                                if ($daysLeft > 0) {
                                                    echo "$daysLeft days left";
                                                } elseif ($daysLeft === 0) {
                                                    echo 'Expires today';
                                                } else {
                                                    echo 'Expired ' . abs($daysLeft) . ' days ago';
                                                }
                                            @endphp
                                        @else
                                            N/A
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th>Created At:</th>
                                    <td>{{ $subscription->created_at->format('M d, Y H:i:s') }}</td>
                                </tr>
                                <tr>
                                    <th>Updated At:</th>
                                    <td>{{ $subscription->updated_at->format('M d, Y H:i:s') }}</td>
                                </tr>
                                @if($subscription->cancelled_at)
                                    <tr>
                                        <th>Cancelled At:</th>
                                        <td>{{ $subscription->cancelled_at->format('M d, Y H:i:s') }}</td>
                                    </tr>
                                    @if($subscription->cancelledBy)
                                        <tr>
                                            <th>Cancelled By:</th>
                                            <td>{{ $subscription->cancelledBy->name }}</td>
                                        </tr>
                                    @endif
                                @endif
                            </table>
                        </div>
                    </div>

                    @if($subscription->features && count($subscription->features) > 0)
                        <div class="mb-4">
                            <h5>Plan Features</h5>
                            <ul class="list-group">
                                @foreach($subscription->features as $feature)
                                    <li class="list-group-item">
                                        <i class="fas fa-check text-success me-2"></i> {{ $feature }}
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    @if($subscription->admin_notes || $subscription->notes)
                        <div class="mb-4">
                            <h5>Notes</h5>
                            @if($subscription->admin_notes)
                                <div class="card mb-2">
                                    <div class="card-header bg-light">
                                        <strong>Admin Notes</strong>
                                    </div>
                                    <div class="card-body">
                                        {!! nl2br(e($subscription->admin_notes)) !!}
                                    </div>
                                </div>
                            @endif
                            @if($subscription->notes)
                                <div class="card">
                                    <div class="card-header bg-light">
                                        <strong>User Notes</strong>
                                    </div>
                                    <div class="card-body">
                                        {!! nl2br(e($subscription->notes)) !!}
                                    </div>
                                </div>
                            @endif
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">User Information</h6>
                </div>
                <div class="card-body text-center">
                    <div class="mb-3">
                        <div class="avatar-circle-lg mx-auto mb-2">
                            <span class="initials">
                                {{ substr($subscription->user->first_name, 0, 1) }}{{ substr($subscription->user->last_name, 0, 1) }}
                            </span>
                        </div>
                        <h5 class="mb-1">{{ $subscription->user->full_name }}</h5>
                        <p class="text-muted mb-2">{{ $subscription->user->email }}</p>
                        <span class="badge bg-primary">{{ ucfirst($subscription->user->role) }}</span>
                    </div>
                    <hr>
                    <div class="text-start">
                        <h6 class="mb-3">Contact Information</h6>
                        <p class="mb-2">
                            <i class="fas fa-phone me-2"></i>
                            {{ $subscription->user->phone ?? 'N/A' }}
                        </p>
                        <p class="mb-0">
                            <i class="fas fa-envelope me-2"></i>
                            <a href="mailto:{{ $subscription->user->email }}">{{ $subscription->user->email }}</a>
                        </p>
                    </div>
                </div>
            </div>

            @if($subscription->payment_proof_url)
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Payment Proof</h6>
                    </div>
                    <div class="card-body text-center">
                        @if(str_ends_with(strtolower($subscription->payment_proof_url), ['.jpg', '.jpeg', '.png', '.gif']))
                            <img src="{{ asset('storage/' . $subscription->payment_proof_url) }}" 
                                 alt="Payment Proof" 
                                 class="img-fluid mb-3" 
                                 style="max-height: 200px;">
                        @else
                            <div class="p-3 bg-light rounded">
                                <i class="fas fa-file-alt fa-3x mb-3"></i>
                                <p class="mb-2">{{ $subscription->payment_proof_name ?? 'Payment Proof' }}</p>
                            </div>
                        @endif
                        <a href="{{ asset('storage/' . $subscription->payment_proof_url) }}" 
                           class="btn btn-sm btn-primary mt-2" 
                           target="_blank"
                           download>
                            <i class="fas fa-download me-1"></i> Download
                        </a>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Approve Modal -->
<div class="modal fade" id="approveModal" tabindex="-1" role="dialog" aria-labelledby="approveModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
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

<!-- Reject Modal -->
<div class="modal fade" id="rejectModal" tabindex="-1" role="dialog" aria-labelledby="rejectModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
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

<!-- Cancel Modal -->
<div class="modal fade" id="cancelModal" tabindex="-1" role="dialog" aria-labelledby="cancelModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form id="cancelForm" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-header">
                    <h5 class="modal-title" id="cancelModalLabel">Cancel Subscription</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p>Are you sure you want to cancel this subscription?</p>
                    <div class="form-group">
                        <label for="cancelReason">Reason for cancellation <span class="text-danger">*</span></label>
                        <textarea class="form-control" id="cancelReason" name="reason" rows="3" required placeholder="Please provide a reason for cancellation..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">No, Keep It</button>
                    <button type="submit" class="btn btn-warning">Yes, Cancel Subscription</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('styles')
<style>
    .avatar-circle-lg {
        width: 80px;
        height: 80px;
        background-color: #4e73df;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 24px;
        font-weight: bold;
    }
    
    .initials {
        text-transform: uppercase;
    }
    
    .progress {
        height: 5px;
        border-radius: 3px;
    }
    
    .progress-bar {
        transition: width 0.6s ease;
    }
</style>
@endpush

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Handle approve button click
        document.querySelectorAll('.approve-btn').forEach(button => {
            button.addEventListener('click', function() {
                const subscriptionId = this.getAttribute('data-id');
                const form = document.getElementById('approveForm');
                form.action = `/admin/subscriptions/${subscriptionId}/approve`;
                $('#approveModal').modal('show');
            });
        });

        // Handle reject button click
        document.querySelectorAll('.reject-btn').forEach(button => {
            button.addEventListener('click', function() {
                const subscriptionId = this.getAttribute('data-id');
                const form = document.getElementById('rejectForm');
                form.action = `/admin/subscriptions/${subscriptionId}/reject`;
                $('#rejectModal').modal('show');
            });
        });

        // Handle cancel button click
        document.querySelectorAll('.cancel-btn').forEach(button => {
            button.addEventListener('click', function() {
                const subscriptionId = this.getAttribute('data-id');
                const form = document.getElementById('cancelForm');
                form.action = `/admin/subscriptions/${subscriptionId}/cancel`;
                $('#cancelModal').modal('show');
            });
        });
    });
</script>
@endpush

@endsection
