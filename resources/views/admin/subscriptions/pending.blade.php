@extends('admin.layouts.app')
@php use Illuminate\Support\Str; @endphp

@push('head')
<meta name="csrf-token" content="{{ csrf_token() }}">
@endpush

@section('content')
    <div class="container mx-auto px-4 py-6 dark:bg-gray-900 min-h-screen">
        <!-- Header Section -->
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-8">
            <div>
                <h1 class="text-2xl font-bold text-gray-800 dark:text-white">@lang('admin.subscriptions.pending_requests')</h1>
                <p class="text-gray-700 dark:text-gray-400 mt-1">Review and manage subscription and payment requests</p>
            </div>
            <div class="flex items-center">
                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-orange-100 text-orange-800 dark:bg-orange-900 dark:text-orange-200">
                    <span class="w-2 h-2 mr-2 rounded-full bg-orange-500"></span>
                    <span class="pending-count">{{ $items->total() }}</span> <span class="pending-text">{{ Str::plural('Request', $items->total()) }} Pending</span>
                </span>
            </div>
        </div>

        <div id="items-container">
            @if($items->isEmpty())
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
                    <div class="text-center p-10">
                        <div class="mx-auto w-16 h-16 flex items-center justify-center rounded-full bg-blue-50 dark:bg-blue-900/30 text-blue-500 dark:text-blue-400 mb-4">
                            <i class="fas fa-inbox text-2xl"></i>
                        </div>
                        <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-1">No Pending Requests</h3>
                        <p class="text-gray-500 dark:text-gray-400">There are no pending subscription or payment requests at the moment.</p>
                    </div>
                </div>
            @else
                <div class="grid gap-4" id="payment-cards-grid">
                    @foreach($items as $item)
                        @php
                            $isPayment = isset($item->payment_id);
                            $user = $item->user ?? null;
                            $plan = $item->plan ?? null;
                            $createdAt = $item->created_at;
                            $isNew = $createdAt->diffInHours() < 24;
                        @endphp
                        
                        <div class="payment-card bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden transition-all duration-200 hover:shadow-md {{ $isNew ? 'ring-2 ring-blue-500/20' : '' }}" data-payment-id="{{ $isPayment ? $item->id : '' }}" data-item-id="{{ $item->id }}">
                            <div class="p-5">
                                <div class="flex items-start gap-4">
                                    <!-- User Avatar -->
                                    <div class="flex-shrink-0">
                                        <div class="w-12 h-12 rounded-full bg-gradient-to-br from-blue-500 to-blue-600 flex items-center justify-center text-white font-medium text-lg shadow-md">
                                            @if($user)
                                                {{ strtoupper(substr($user->first_name ?? 'U', 0, 1)) }}{{ strtoupper(substr($user->last_name ?? 'S', 0, 1)) }}
                                            @else
                                                <i class="fas fa-user"></i>
                                            @endif
                                        </div>
                                    </div>

                                    <!-- Main Content -->
                                    <div class="flex-1 min-w-0">
                                        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 mb-2">
                                            <div class="flex-1 min-w-0">
                                                <h3 class="text-base font-semibold text-gray-900 dark:text-white truncate">
                                                    @if($user)
                                                        <a href="{{ route('admin.users.show', $user) }}" class="hover:text-blue-600 dark:hover:text-blue-400 transition-colors">
                                                            {{ $user->full_name ?? 'Unknown User' }}
                                                        </a>
                                                    @else
                                                        <span class="text-gray-500 dark:text-gray-400">User Not Found</span>
                                                    @endif
                                                </h3>
                                                
                                                @if($plan)
                                                    @php
                                                        $planName = is_string($plan->name) ? 
                                                            json_decode($plan->name, true) ?? $plan->name : 
                                                            $plan->name;
                                                        $localizedName = is_array($planName) ? 
                                                            ($planName[app()->getLocale()] ?? $planName['en'] ?? 'N/A') : 
                                                            $planName;
                                                    @endphp
                                                    <div class="mt-1">
                                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900/50 dark:text-blue-300">
                                                            {{ $localizedName }}
                                                            @if($isPayment)
                                                                <span class="ml-1">• Payment</span>
                                                            @endif
                                                        </span>
                                                    </div>
                                                @endif
                                            </div>
                                            
                                            @if($plan && isset($item->amount))
                                                <div class="text-lg font-bold text-gray-900 dark:text-white">
                                                    {{ number_format($item->amount, 2) }} <span class="text-sm font-normal text-gray-500 dark:text-gray-400">{{ $plan->currency ?? 'RWF' }}</span>
                                                </div>
                                            @endif
                                        </div>
                                        
                                        <div class="flex flex-wrap items-center gap-x-4 gap-y-2 text-sm text-gray-500 dark:text-gray-400 mt-2">
                                            <div class="flex items-center">
                                                <i class="far fa-clock mr-1.5"></i>
                                                <span>{{ $createdAt->diffForHumans() }}</span>
                                            </div>
                                            <div class="hidden sm:block w-px h-4 bg-gray-200 dark:bg-gray-700"></div>
                                            <div class="flex items-center">
                                                <i class="far fa-calendar mr-1.5"></i>
                                                <span>{{ $createdAt->format('M j, Y \a\t H:i') }}</span>
                                            </div>
                                        </div>
                                        
                                        @if($isPayment && $item->proof_url)
                                            <div class="mt-3">
                                                <a href="{{ $item->proof_url }}" 
                                                   target="_blank" 
                                                   class="inline-flex items-center px-3 py-1.5 border border-gray-300 dark:border-gray-600 rounded-lg text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 transition-colors">
                                                    <i class="fas fa-receipt mr-1.5 text-blue-500"></i>
                                                    View Payment Proof
                                                </a>
                                            </div>
                                        @endif
                                        
                                        <!-- Actions -->
                                        <div class="mt-4 pt-3 border-t border-gray-100 dark:border-gray-700 flex flex-wrap gap-2">
                                            @if($isPayment)
                                                <button type="button" 
                                                        class="approve-payment-btn w-full sm:w-auto flex-1 sm:flex-none flex items-center justify-center px-4 py-2 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
                                                        data-payment-id="{{ $item->id }}"
                                                        data-item-id="{{ $item->id }}">
                                                    <i class="fas fa-check mr-2"></i> Approve Payment
                                                </button>
                                                
                                                <button type="button" 
                                                        class="reject-btn w-full sm:w-auto flex-1 sm:flex-none flex items-center justify-center px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg shadow-sm text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
                                                        data-bs-toggle="modal" 
                                                        data-bs-target="#rejectModal"
                                                        data-id="{{ $item->id }}"
                                                        data-is-payment="true">
                                                    <i class="fas fa-times mr-2"></i> Reject
                                                </button>
                                            @else
                                                <button type="button"
                                                        class="approve-subscription-btn w-full sm:w-auto flex-1 sm:flex-none flex items-center justify-center px-4 py-2 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
                                                        data-subscription-id="{{ $item->id }}"
                                                        data-item-id="{{ $item->id }}">
                                                    <i class="fas fa-check mr-2"></i> Approve
                                                </button>
                                                
                                                <button type="button" 
                                                        class="reject-btn w-full sm:w-auto flex-1 sm:flex-none flex items-center justify-center px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg shadow-sm text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
                                                        data-bs-toggle="modal" 
                                                        data-bs-target="#rejectModal"
                                                        data-id="{{ $item->id }}"
                                                        data-is-payment="false">
                                                    <i class="fas fa-times mr-2"></i> Reject
                                                </button>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
                
                <!-- Pagination -->
                <div class="mt-6 flex flex-col sm:flex-row items-center justify-between gap-4 px-4" id="pagination-container">
                    <p class="text-sm text-gray-600 dark:text-gray-400 pagination-info">
                        Showing {{ $items->firstItem() }} to {{ $items->lastItem() }} of {{ $items->total() }} entries
                    </p>
                    <div class="flex-1 sm:flex-none">
                        {{ $items->links() }}
                    </div>
                </div>
            @endif
        </div>
    </div>

    <!-- Reject Modal -->
    <div class="modal fade" id="rejectModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content bg-white dark:bg-gray-800 rounded-xl shadow-xl">
                <div class="modal-header flex items-center justify-between p-4 border-b border-gray-200 dark:border-gray-700">
                    <h5 class="modal-title text-lg font-semibold text-gray-900 dark:text-white">Reject Request</h5>
                    <button type="button" class="text-gray-400 hover:text-gray-500 dark:hover:text-gray-300" data-bs-dismiss="modal" aria-label="Close">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <div class="modal-body p-4">
                    <form id="rejectForm" method="POST" class="space-y-4">
                        @csrf
                        @method('DELETE')
                        
                        <div>
                            <label for="rejectReason" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                Reason for rejection <span class="text-gray-400">(optional)</span>
                            </label>
                            <textarea id="rejectReason" 
                                    name="reason" 
                                    rows="3" 
                                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white dark:placeholder-gray-400"
                                    placeholder="Please provide a reason for rejection"></textarea>
                        </div>
                        
                        <div class="flex justify-end gap-3 pt-2">
                            <button type="button" 
                                    class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg shadow-sm hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 transition-colors" 
                                    data-bs-dismiss="modal">
                                Cancel
                            </button>
                            <button type="submit" 
                                    class="px-4 py-2 text-sm font-medium text-white bg-red-600 border border-transparent rounded-lg shadow-sm hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition-colors">
                                Confirm Rejection
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        const locale = window.location.pathname.split('/')[1] || 'en';
        let isRefreshing = false;
        
        // Handle approve payment button click
        document.addEventListener('click', function(e) {
            const approveBtn = e.target.closest('.approve-payment-btn');
            if (approveBtn) {
                e.preventDefault();
                handleApproval(approveBtn, 'payment');
            }
            
            const approveSubBtn = e.target.closest('.approve-subscription-btn');
            if (approveSubBtn) {
                e.preventDefault();
                handleApproval(approveSubBtn, 'subscription');
            }
        });
        
        async function handleApproval(button, type) {
            if (button.disabled) return;
            
            const itemId = button.dataset.itemId;
            const card = button.closest('.payment-card');
            const allButtons = card.querySelectorAll('button');
            
            // Disable all buttons in the card
            allButtons.forEach(btn => btn.disabled = true);
            
            // Show loading state
            const originalContent = button.innerHTML;
            button.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i> Approving...';
            
            try {
                const endpoint = type === 'payment' 
                    ? `/${locale}/admin/payments/${itemId}/approve`
                    : `/${locale}/admin/subscriptions/${itemId}`;
                    
                const method = type === 'payment' ? 'PATCH' : 'POST';
                
                const response = await fetch(endpoint, {
                    method: method,
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    }
                });
                
                const data = await response.json();
                
                if (!response.ok) {
                    throw new Error(data.message || `Failed to approve ${type}`);
                }
                
                // Show success notification
                showNotification('success', data.message || `${type.charAt(0).toUpperCase() + type.slice(1)} approved successfully`);
                
                // Update pending count
                updatePendingCount(-1);
                
                // Fade out and remove the card
                await fadeOutAndRemove(card);
                
                // Refresh list in background
                refreshListInBackground();
                
            } catch (error) {
                console.error('Error:', error);
                showNotification('error', error.message || `An error occurred while approving the ${type}`);
                
                // Re-enable buttons and restore original content on error
                allButtons.forEach(btn => btn.disabled = false);
                button.innerHTML = originalContent;
            }
        }
        
        function fadeOutAndRemove(element) {
            return new Promise((resolve) => {
                element.style.opacity = '0';
                element.style.transform = 'scale(0.95)';
                element.style.transition = 'opacity 300ms ease-in-out, transform 300ms ease-in-out';
                
                setTimeout(() => {
                    const height = element.offsetHeight;
                    element.style.height = height + 'px';
                    element.style.overflow = 'hidden';
                    
                    requestAnimationFrame(() => {
                        element.style.height = '0';
                        element.style.marginBottom = '0';
                        element.style.padding = '0';
                        element.style.transition = 'height 300ms ease-in-out, margin 300ms ease-in-out, padding 300ms ease-in-out';
                        
                        setTimeout(() => {
                            element.remove();
                            checkForEmptyState();
                            resolve();
                        }, 300);
                    });
                }, 300);
            });
        }
        
        function checkForEmptyState() {
            const grid = document.getElementById('payment-cards-grid');
            const remainingCards = grid?.querySelectorAll('.payment-card').length || 0;
            
            if (remainingCards === 0) {
                const container = document.getElementById('items-container');
                if (container) {
                    container.innerHTML = `
                        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
                            <div class="text-center p-10">
                                <div class="mx-auto w-16 h-16 flex items-center justify-center rounded-full bg-blue-50 dark:bg-blue-900/30 text-blue-500 dark:text-blue-400 mb-4">
                                    <i class="fas fa-inbox text-2xl"></i>
                                </div>
                                <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-1">No Pending Requests</h3>
                                <p class="text-gray-500 dark:text-gray-400">There are no pending subscription or payment requests at the moment.</p>
                            </div>
                        </div>`;
                }
            }
        }
        
        async function refreshListInBackground() {
            if (isRefreshing) return;
            isRefreshing = true;
            
            try {
                const currentUrl = new URL(window.location.href);
                currentUrl.searchParams.set('_refresh', Date.now());
                
                const response = await fetch(currentUrl.toString(), {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'text/html',
                    }
                });
                
                if (!response.ok) throw new Error('Failed to refresh list');
                
                const html = await response.text();
                const parser = new DOMParser();
                const doc = parser.parseFromString(html, 'text/html');
                
                // Update pagination silently
                const newPagination = doc.querySelector('#pagination-container');
                const currentPagination = document.getElementById('pagination-container');
                if (newPagination && currentPagination) {
                    currentPagination.innerHTML = newPagination.innerHTML;
                }
                
                // Pre-load next items (they'll appear after current items are removed)
                const newGrid = doc.querySelector('#payment-cards-grid');
                const currentGrid = document.getElementById('payment-cards-grid');
                if (newGrid && currentGrid) {
                    // Store new items for later
                    const newCards = Array.from(newGrid.querySelectorAll('.payment-card'));
                    const currentCards = Array.from(currentGrid.querySelectorAll('.payment-card'));
                    
                    // Find cards that are in new list but not in current list
                    const newCardIds = newCards.map(card => card.dataset.itemId);
                    const currentCardIds = currentCards.map(card => card.dataset.itemId);
                    
                    newCards.forEach(newCard => {
                        const cardId = newCard.dataset.itemId;
                        if (!currentCardIds.includes(cardId)) {
                            // Add new card with fade-in animation
                            newCard.style.opacity = '0';
                            newCard.style.transform = 'translateY(-10px)';
                            currentGrid.appendChild(newCard);
                            
                            requestAnimationFrame(() => {
                                newCard.style.transition = 'opacity 300ms ease-in-out, transform 300ms ease-in-out';
                                newCard.style.opacity = '1';
                                newCard.style.transform = 'translateY(0)';
                            });
                        }
                    });
                }
                
            } catch (error) {
                console.error('Error refreshing list:', error);
            } finally {
                isRefreshing = false;
            }
        }
        
        function updatePendingCount(change) {
            const countElement = document.querySelector('.pending-count');
            const textElement = document.querySelector('.pending-text');
            
            if (countElement) {
                const currentCount = parseInt(countElement.textContent) || 0;
                const newCount = Math.max(0, currentCount + change);
                countElement.textContent = newCount;
                
                if (textElement) {
                    textElement.textContent = `${newCount === 1 ? 'Request' : 'Requests'} Pending`;
                }
            }
        }
        
        function showNotification(type, message) {
            const event = new CustomEvent('notify', {
                detail: {
                    type: type,
                    message: message,
                    timeout: 5000
                }
            });
            window.dispatchEvent(event);
        }
        
        // Initialize reject modal
        const rejectModalElement = document.getElementById('rejectModal');
        let rejectModal = null;
        
        if (rejectModalElement) {
            rejectModal = new bootstrap.Modal(rejectModalElement, {
                backdrop: true,
                keyboard: true,
                focus: true
            });
            
            rejectModalElement.addEventListener('show.bs.modal', function(event) {
                const button = event.relatedTarget;
                const id = button.getAttribute('data-id');
                const isPayment = button.getAttribute('data-is-payment') === 'true';
                const form = document.getElementById('rejectForm');
                
                if (form) {
                    form.action = isPayment 
                        ? `/${locale}/admin/payments/${id}/reject`
                        : `/${locale}/admin/subscriptions/${id}/reject`;
                    form.reset();
                }
            });
        }
    });
</script>
@endpush