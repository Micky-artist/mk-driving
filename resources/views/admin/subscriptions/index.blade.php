@extends('admin.layouts.app')

@section('content')
    <!-- Pending Subscriptions Section -->
    <div class="mb-8">
        <div class="mb-6 flex justify-between items-center">
            <h2 class="text-xl font-semibold">Pending Subscriptions</h2>
            <div class="text-sm text-gray-500">
                {{ $subscriptions->total() }} {{ Str::plural('subscription', $subscriptions->total()) }} pending
            </div>
        </div>

        @if($subscriptions->isEmpty())
            <div class="bg-white p-6 rounded-lg shadow">
                <p class="text-gray-500">No pending subscription requests.</p>
            </div>
        @else
        <div class="bg-white rounded-lg shadow overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">User Details</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Plan & Payment</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($subscriptions as $subscription)
                            <tr class="hover:bg-gray-50">
                                <!-- User Details -->
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0 h-10 w-10">
                                            <div class="h-10 w-10 rounded-full bg-blue-100 flex items-center justify-center text-blue-600 font-semibold">
                                                {{ strtoupper(substr($subscription->user->first_name, 0, 1)) }}{{ strtoupper(substr($subscription->user->last_name, 0, 1)) }}
                                            </div>
                                        </div>
                                        <div class="ml-4">
                                            <div class="text-sm font-medium text-gray-900">{{ $subscription->user->full_name }}</div>
                                            <div class="text-sm text-gray-500">{{ $subscription->user->email }}</div>
                                            @if($subscription->user->phone)
                                                <div class="mt-1 flex items-center text-sm text-gray-500">
                                                    <svg class="flex-shrink-0 mr-1.5 h-4 w-4 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                                        <path d="M2 3a1 1 0 011-1h2.153a1 1 0 01.986.836l.74 4.435a1 1 0 01-.54 1.06l-1.548.773a11.037 11.037 0 006.105 6.105l.774-1.548a1 1 0 011.059-.54l4.435.74a1 1 0 01.836.986V17a1 1 0 01-1 1h-2C7.82 18 2 12.18 2 5V3z" />
                                                    </svg>
                                                    {{ $subscription->user->phone }}
                                                </div>
                                            @endif
                                            <div class="mt-1 text-xs text-gray-400">
                                                Requested {{ $subscription->created_at->diffForHumans() }}
                                            </div>
                                        </div>
                                    </div>
                                </td>

                                <!-- Plan & Payment Details -->
                                <td class="px-6 py-4">
                                    <div class="text-sm font-medium text-gray-900">
                                        {{ $subscription->plan->name[app()->getLocale()] ?? $subscription->plan->name['en'] }}
                                        <span class="text-sm font-normal text-gray-500">
                                            ({{ number_format($subscription->amount, 2) }} {{ $subscription->plan->currency }})
                                        </span>
                                    </div>
                                    
                                    @if($subscription->payment_method || $subscription->payment_reference)
                                        <div class="mt-2 text-sm text-gray-500">
                                            @if($subscription->payment_method)
                                                <div class="flex items-center">
                                                    <span class="capitalize">{{ $subscription->payment_method }}</span>
                                                    @if($subscription->payment_reference)
                                                        <span class="mx-2 text-gray-300">•</span>
                                                        <span class="font-mono text-xs bg-gray-100 px-2 py-0.5 rounded">
                                                            {{ $subscription->payment_reference }}
                                                        </span>
                                                    @endif
                                                </div>
                                            @endif
                                            
                                            @if($subscription->payment_proof_url)
                                                <div class="mt-1">
                                                    <a href="{{ $subscription->payment_proof_url }}" target="_blank" class="text-blue-600 hover:text-blue-800 text-sm flex items-center">
                                                        <svg class="h-4 w-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                                        </svg>
                                                        View Payment Proof
                                                    </a>
                                                </div>
                                            @endif
                                        </div>
                                    @endif
                                </td>

                                <!-- Status -->
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2.5 py-0.5 inline-flex text-xs leading-5 font-semibold rounded-full 
                                        {{ $subscription->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                        {{ $subscription->status === 'active' ? 'bg-green-100 text-green-800' : '' }}
                                        {{ $subscription->status === 'rejected' ? 'bg-red-100 text-red-800' : '' }}
                                    ">
                                        {{ ucfirst($subscription->status) }}
                                    </span>
                                    
                                    @if($subscription->status === 'pending' && $subscription->created_at->diffInDays() > 2)
                                        <div class="mt-1 text-xs text-yellow-600">
                                            Pending for {{ $subscription->created_at->diffInDays() }} days
                                        </div>
                                    @endif
                                </td>

                                <!-- Actions -->
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    @if($subscription->status === 'pending')
                                        <div class="flex space-x-2 justify-end">
                                            <form action="{{ route('admin.subscriptions.approve', $subscription) }}" method="POST" class="inline">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded-md shadow-sm text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                                                    Approve
                                                </button>
                                            </form>
                                            <form action="{{ route('admin.subscriptions.reject', $subscription) }}" method="POST" class="inline">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded-md shadow-sm text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                                                    Reject
                                                </button>
                                            </form>
                                        </div>
                                    @else
                                        <span class="text-gray-400 text-sm">
                                            {{ $subscription->status === 'active' ? 'Approved' : 'Rejected' }} 
                                            @if($subscription->updated_at)
                                                {{ $subscription->updated_at->diffForHumans() }}
                                            @endif
                                        </span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="px-6 py-3 bg-gray-50 border-t border-gray-200">
                {{ $subscriptions->links() }}
            </div>
        </div>
    @endif
    </div>

    <!-- Pending Payments Section -->
    <div class="mb-8">
        <div class="mb-6 flex justify-between items-center">
            <h2 class="text-xl font-semibold">Pending Payments</h2>
            <div class="text-sm text-gray-500">
                {{ $payments->total() }} {{ Str::plural('payment', $payments->total()) }} pending
            </div>
        </div>

        @if($payments->isEmpty())
            <div class="bg-white p-6 rounded-lg shadow">
                <p class="text-gray-500">No pending payment requests.</p>
            </div>
        @else
            <div class="bg-white rounded-lg shadow overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">User Details</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Plan & Payment</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($payments as $payment)
                                <tr class="hover:bg-gray-50">
                                    <!-- User Details -->
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="flex-shrink-0 h-10 w-10">
                                                <div class="h-10 w-10 rounded-full bg-blue-100 flex items-center justify-center text-blue-600 font-semibold">
                                                    {{ strtoupper(substr($payment->user->first_name, 0, 1)) }}{{ strtoupper(substr($payment->user->last_name, 0, 1)) }}
                                                </div>
                                            </div>
                                            <div class="ml-4">
                                                <div class="text-sm font-medium text-gray-900">{{ $payment->user->full_name }}</div>
                                                <div class="text-sm text-gray-500">{{ $payment->user->email }}</div>
                                                @if($payment->phone_number)
                                                    <div class="mt-1 flex items-center text-sm text-gray-500">
                                                        <svg class="flex-shrink-0 mr-1.5 h-4 w-4 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                                            <path d="M2 3a1 1 0 011-1h2.153a1 1 0 01.986.836l.74 4.435a1 1 0 01-.54 1.06l-1.548.773a11.037 11.037 0 006.105 6.105l.774-1.548a1 1 0 011.059-.54l4.435.74a1 1 0 01.836.986V17a1 1 0 01-1 1h-2C7.82 18 2 12.18 2 5V3z" />
                                                        </svg>
                                                        {{ $payment->phone_number }}
                                                    </div>
                                                @endif
                                                <div class="mt-1 text-xs text-gray-400">
                                                    Requested {{ $payment->created_at->diffForHumans() }}
                                                </div>
                                            </div>
                                        </div>
                                    </td>

                                    <!-- Plan & Payment Details -->
                                    <td class="px-6 py-4">
                                        @if($payment->plan)
                                            <div class="text-sm font-medium text-gray-900">
                                                {{ $payment->plan->name[app()->getLocale()] ?? $payment->plan->name['en'] ?? 'N/A' }}
                                                <span class="text-sm font-normal text-gray-500">
                                                    ({{ number_format($payment->amount, 2) }} {{ $payment->currency }})
                                                </span>
                                            </div>
                                        @else
                                            <div class="text-sm text-gray-500">Plan not found</div>
                                            <div class="text-sm text-gray-500">
                                                {{ number_format($payment->amount, 2) }} {{ $payment->currency }}
                                            </div>
                                        @endif
                                        
                                        @if($payment->payment_method)
                                            <div class="mt-2 text-sm text-gray-500">
                                                <div class="flex items-center">
                                                    <span class="capitalize">{{ $payment->payment_method }}</span>
                                                    @if($payment->reference)
                                                        <span class="mx-2 text-gray-300">•</span>
                                                        <span class="font-mono text-xs bg-gray-100 px-2 py-0.5 rounded">
                                                            {{ $payment->reference }}
                                                        </span>
                                                    @endif
                                                </div>
                                            </div>
                                        @endif
                                    </td>

                                    <!-- Status -->
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-2.5 py-0.5 inline-flex text-xs leading-5 font-semibold rounded-full 
                                            {{ $payment->status === 'PENDING' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                            {{ $payment->status === 'COMPLETED' ? 'bg-green-100 text-green-800' : '' }}
                                            {{ $payment->status === 'FAILED' || $payment->status === 'REJECTED' ? 'bg-red-100 text-red-800' : '' }}
                                        ">
                                            {{ ucfirst(strtolower($payment->status)) }}
                                        </span>
                                        
                                        @if($payment->status === 'PENDING' && $payment->created_at->diffInDays() > 1)
                                            <div class="mt-1 text-xs text-yellow-600">
                                                Pending for {{ $payment->created_at->diffInDays() }} days
                                            </div>
                                        @endif
                                    </td>

                                    <!-- Actions -->
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium actions-cell">
                                        @if($payment->status === 'PENDING')
                                            <div class="flex space-x-2 justify-end">
                                                <form action="{{ route('admin.payments.approve', $payment->id) }}" method="POST" class="approve-payment-form">
                                                    @csrf
                                                    @method('PATCH')
                                                    <button type="submit" class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded-md shadow-sm text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                                                        <span class="btn-text">Approve</span>
                                                        <svg class="animate-spin -ml-1 mr-1 h-4 w-4 text-white hidden" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                                        </svg>
                                                    </button>
                                                </form>
                                                <form action="{{ route('admin.payments.reject', $payment->id) }}" method="POST" class="reject-payment-form">
                                                    @csrf
                                                    @method('PATCH')
                                                    <button type="submit" class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded-md shadow-sm text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                                                        <span class="btn-text">Reject</span>
                                                        <svg class="animate-spin -ml-1 mr-1 h-4 w-4 text-white hidden" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                                        </svg>
                                                    </button>
                                                </form>
                                            </div>
                                        @else
                                            <span class="text-gray-400 text-sm">
                                                {{ $payment->status === 'COMPLETED' ? 'Approved' : 'Rejected' }} 
                                                @if($payment->updated_at)
                                                    {{ $payment->updated_at->diffForHumans() }}
                                                @endif
                                            </span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="px-6 py-3 bg-gray-50 border-t border-gray-200">
                    {{ $payments->links() }}
                </div>
            </div>
        @endif
    </div>

    @push('scripts')
    <script>
        // Handle payment approval/rejection forms
        function handlePaymentForm(form, successStatus, successMessage) {
            form.addEventListener('submit', async function(e) {
                e.preventDefault();
                
                const button = this.querySelector('button[type="submit"]');
                const buttonText = button.querySelector('.btn-text');
                const spinner = button.querySelector('svg');
                const row = this.closest('tr');
                
                // Show loading state
                button.disabled = true;
                button.classList.add('opacity-75', 'cursor-not-allowed');
                buttonText.textContent = successStatus === 'COMPLETED' ? 'Approving...' : 'Rejecting...';
                spinner.classList.remove('hidden');
                
                try {
                    const response = await fetch(this.action, {
                        method: 'PATCH',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest',
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify({
                            _method: 'PATCH',
                            _token: document.querySelector('meta[name="csrf-token"]').content
                        })
                    });
                    
                    const data = await response.json();
                    
                    if (response.ok) {
                        // Show success message
                        const successAlert = document.createElement('div');
                        successAlert.className = 'mb-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded relative';
                        successAlert.role = 'alert';
                        successAlert.innerHTML = `
                            <span class="block sm:inline">${successMessage}</span>
                            <span class="absolute top-0 bottom-0 right-0 px-4 py-3" onclick="this.parentElement.remove()">
                                <svg class="fill-current h-6 w-6 text-green-500" role="button" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                    <title>Close</title>
                                    <path d="M14.348 14.849a1.2 1.2 0 0 1-1.697 0L10 11.819l-2.651 3.029a1.2 1.2 0 1 1-1.697-1.697l2.758-3.15-2.759-3.152a1.2 1.2 0 1 1 1.697-1.697L10 8.183l2.651-3.031a1.2 1.2 0 1 1 1.697 1.697l-2.758 3.152 2.758 3.15a1.2 1.2 0 0 1 0 1.698z"/>
                                </svg>
                            </span>
                        `;
                        
                        // Insert alert before the table
                        const table = document.querySelector('table');
                        table.parentNode.insertBefore(successAlert, table);
                        
                        // Update status cell
                        const statusCell = row.querySelector('.status-cell');
                        if (statusCell) {
                            statusCell.innerHTML = `
                                <span class="px-2.5 py-0.5 inline-flex text-xs leading-5 font-semibold rounded-full 
                                    ${successStatus === 'COMPLETED' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'}">
                                    ${successStatus === 'COMPLETED' ? 'Approved' : 'Rejected'}
                                </span>
                            `;
                        }
                        
                        // Remove action buttons
                        const actionsCell = row.querySelector('.actions-cell');
                        if (actionsCell) {
                            actionsCell.innerHTML = `
                                <span class="text-gray-400 text-sm">
                                    ${successStatus === 'COMPLETED' ? 'Approved' : 'Rejected'} just now
                                </span>
                            `;
                        }
                        
                        // Remove alert after 5 seconds
                        setTimeout(() => {
                            successAlert.remove();
                        }, 5000);
                        
                    } else {
                        throw new Error(data.message || 'An error occurred');
                    }
                } catch (error) {
                    console.error('Error:', error);
                    
                    // Show error message
                    const errorAlert = document.createElement('div');
                    errorAlert.className = 'mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded relative';
                    errorAlert.role = 'alert';
                    errorAlert.innerHTML = `
                        <span class="block sm:inline">${error.message}</span>
                        <span class="absolute top-0 bottom-0 right-0 px-4 py-3" onclick="this.parentElement.remove()">
                            <svg class="fill-current h-6 w-6 text-red-500" role="button" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                <title>Close</title>
                                <path d="M14.348 14.849a1.2 1.2 0 0 1-1.697 0L10 11.819l-2.651 3.029a1.2 1.2 0 1 1-1.697-1.697l2.758-3.15-2.759-3.152a1.2 1.2 0 1 1 1.697-1.697L10 8.183l2.651-3.031a1.2 1.2 0 1 1 1.697 1.697l-2.758 3.152 2.758 3.15a1.2 1.2 0 0 1 0 1.698z"/>
                            </svg>
                        </span>
                    `;
                    
                    // Insert alert before the table
                    const table = document.querySelector('table');
                    table.parentNode.insertBefore(errorAlert, table);
                    
                    // Remove alert after 5 seconds
                    setTimeout(() => {
                        errorAlert.remove();
                    }, 5000);
                    
                    // Reset button state
                    button.disabled = false;
                    button.classList.remove('opacity-75', 'cursor-not-allowed');
                    buttonText.textContent = successStatus === 'COMPLETED' ? 'Approve' : 'Reject';
                    spinner.classList.add('hidden');
                }
            });
        }

        // Initialize all payment forms
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('.approve-payment-form').forEach(form => {
                handlePaymentForm(form, 'COMPLETED', 'Payment approved successfully!');
            });
            
            document.querySelectorAll('.reject-payment-form').forEach(form => {
                handlePaymentForm(form, 'REJECTED', 'Payment rejected successfully!');
            });
        });

        // Add confirmation for reject actions
        document.addEventListener('DOMContentLoaded', function() {
            // Subscription reject confirmation
            const subscriptionRejectForms = document.querySelectorAll('form[action*="subscriptions"][action*="reject"]');
            subscriptionRejectForms.forEach(form => {
                form.addEventListener('submit', function(e) {
                    if (!confirm('Are you sure you want to reject this subscription request?')) {
                        e.preventDefault();
                    }
                });
            });

            // Payment reject confirmation
            const paymentRejectForms = document.querySelectorAll('form[action*="payments"][action*="reject"]');
            paymentRejectForms.forEach(form => {
                form.addEventListener('submit', function(e) {
                    if (!confirm('Are you sure you want to reject this payment?')) {
                        e.preventDefault();
                    }
                });
            });
        });
    </script>
    @endpush
@endsection
