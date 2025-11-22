@foreach($subscriptions as $subscription)
    <tr>
        <td class="align-middle">
            <div class="d-flex align-items-center">
                <div class="symbol symbol-50px me-5">
                    <span class="symbol-label bg-light-{{ $subscription->status }} fs-3 fw-bolder">
                        {{ substr($subscription->user->first_name, 0, 1) }}
                    </span>
                </div>
                <div class="d-flex flex-column">
                    <a href="{{ route('admin.subscriptions.show', $subscription) }}" class="text-dark text-hover-primary fw-bolder">
                        {{ $subscription->user->first_name }} {{ $subscription->user->last_name }}
                    </a>
                    <span class="text-muted fw-bold">{{ $subscription->user->email }}</span>
                </div>
            </div>
        </td>
        <td class="align-middle">
            <div class="d-flex align-items-center">
                <div class="symbol symbol-30px me-3">
                    <div class="symbol-label" style="background-color: {{ $subscription->subscriptionPlan->color }}; width: 12px; height: 12px;"></div>
                </div>
                <span class="text-dark fw-bolder">{{ $subscription->subscriptionPlan->getTranslation('name', 'en') }}</span>
            </div>
        </td>
        <td class="align-middle">
            <span class="text-dark fw-bolder d-block">
                {{ number_format($subscription->amount, 2) }} RWF
            </span>
            <span class="text-muted fw-bold">{{ $subscription->subscriptionPlan->duration }} days</span>
        </td>
        <td class="align-middle">
            @php
                $statusClass = [
                    'PENDING' => 'warning',
                    'ACTIVE' => 'success',
                    'EXPIRED' => 'dark',
                    'CANCELLED' => 'danger'
                ][$subscription->status->value];
            @endphp
            <span class="badge badge-light-{{ $statusClass }} fw-bolder">{{ $subscription->status->value }}</span>
        </td>
        <td class="align-middle">
            @php
                $paymentStatusClass = [
                    'PENDING' => 'warning',
                    'COMPLETED' => 'success',
                    'FAILED' => 'danger'
                ][$subscription->payment_status->value];
            @endphp
            <span class="badge badge-light-{{ $paymentStatusClass }}">{{ $subscription->payment_status->value }}</span>
        </td>
        <td class="text-end">
            <div class="d-flex justify-content-end">
                <a href="{{ route('admin.subscriptions.show', $subscription) }}" class="btn btn-icon btn-bg-light btn-active-color-primary btn-sm me-1">
                    <i class="fas fa-eye"></i>
                </a>
                @if($subscription->status->value === 'PENDING')
                    <button type="button" class="btn btn-icon btn-bg-light btn-active-color-success btn-sm me-1 approve-btn" data-id="{{ $subscription->id }}">
                        <i class="fas fa-check"></i>
                    </button>
                    <button type="button" class="btn btn-icon btn-bg-light btn-active-color-danger btn-sm reject-btn" data-id="{{ $subscription->id }}">
                        <i class="fas fa-times"></i>
                    </button>
                @elseif($subscription->status->value === 'ACTIVE')
                    <button type="button" class="btn btn-icon btn-bg-light btn-active-color-danger btn-sm cancel-btn" data-id="{{ $subscription->id }}">
                        <i class="fas fa-ban"></i>
                    </button>
                @endif
            </div>
        </td>
    </tr>
@endforeach
