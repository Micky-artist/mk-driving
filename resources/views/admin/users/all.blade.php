@extends('admin.layouts.app')

@section('title', 'All Users')

@push('styles')
@keyframes fadeIn {
    from { opacity: 0; transform: translateY(20px); }
    to { opacity: 1; transform: translateY(0); }
}

.fade-in {
    animation: fadeIn 0.6s ease-out forwards;
}
@endpush

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-2">
                All Users
            </h1>
            <p class="text-gray-600 dark:text-gray-400">
                Complete list of all registered users with advanced filtering options.
            </p>
        </div>
        <div class="flex items-center gap-3">
            <!-- Clear Filters Button -->
            <button id="clear-filters" class="group inline-flex items-center px-6 py-3 bg-gradient-to-r from-gray-600 to-gray-700 hover:from-gray-700 hover:to-gray-800 text-white font-semibold rounded-full shadow-lg hover:shadow-xl transform hover:scale-105 transition-all duration-200 border border-gray-500/20">
                <svg class="w-5 h-5 mr-2 group-hover:rotate-180 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                </svg>
                <span class="relative">
                    Clear Filters
                    <span class="absolute -bottom-1 left-0 right-0 h-0.5 bg-white/30 transform scale-x-0 group-hover:scale-x-100 transition-transform duration-200"></span>
                </span>
            </button>
        </div>
    </div>

    <!-- Users Table -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden fade-in">
        <div class="overflow-x-auto" id="table-container">
            <table class="min-w-full">
                <thead class="bg-gray-50 dark:bg-gray-700 border-b border-gray-200 dark:border-gray-600">
                    <!-- Filter Row -->
                    <tr class="border-b border-gray-200 dark:border-gray-600">
                        <th colspan="7" class="py-4">
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                                <!-- Search Filter -->
                                <div>
                                    <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-2">Search</label>
                                    <div class="relative">
                                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                            <svg class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                            </svg>
                                        </div>
                                        <input type="text" 
                                               id="search-input"
                                               placeholder="Search users..." 
                                               class="bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200 pl-10 w-full">
                                    </div>
                                </div>

                                <!-- Role Filter -->
                                <div>
                                    <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-2">Role</label>
                                    <select id="role-filter" class="bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 text-gray-900 dark:text-white rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200 w-full">
                                        <option value="">All Roles</option>
                                        <option value="USER">User</option>
                                        <option value="INSTRUCTOR">Instructor</option>
                                        <option value="ADMIN">Admin</option>
                                    </select>
                                </div>

                                <!-- Status Filter -->
                                <div>
                                    <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-2">Status</label>
                                    <select id="status-filter" class="bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 text-gray-900 dark:text-white rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200 w-full">
                                        <option value="">All Status</option>
                                        <option value="1">Active</option>
                                        <option value="0">Inactive</option>
                                    </select>
                                </div>

                                <!-- Subscription Filter -->
                                <div>
                                    <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-2">Subscription</label>
                                    <select id="subscription-filter" class="bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 text-gray-900 dark:text-white rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200 w-full">
                                        <option value="">All Subscriptions</option>
                                        <option value="active">Active</option>
                                        <option value="pending">Pending</option>
                                        <option value="expired">Expired</option>
                                        <option value="none">No Subscription</option>
                                    </select>
                                </div>

                            </div>
                        </th>
                    </tr>
                    
                    <!-- Header Row -->
                    <tr>
                        <th scope="col" class="py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider cursor-pointer hover:bg-gray-100 dark:hover:bg-gray-600" onclick="sortTable('name')">
                            <div class="flex items-center">
                                Name
                                <svg class="ml-1 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 11l5-5m0 0l5 5m-5-5v12"></path>
                                </svg>
                            </div>
                        </th>
                        <th scope="col" class="py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider cursor-pointer hover:bg-gray-100 dark:hover:bg-gray-600" onclick="sortTable('email')">
                            <div class="flex items-center">
                                Email
                                <svg class="ml-1 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 11l5-5m0 0l5 5m-5-5v12"></path>
                                </svg>
                            </div>
                        </th>
                        <th scope="col" class="py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                            Phone
                        </th>
                        <th scope="col" class="py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                            Role
                        </th>
                        <th scope="col" class="py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                            Status
                        </th>
                        <th scope="col" class="py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                            Subscription
                        </th>
                        <th scope="col" class="py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider cursor-pointer hover:bg-gray-100 dark:hover:bg-gray-600" onclick="sortTable('created_at')">
                            <div class="flex items-center">
                                Joined
                                <svg class="ml-1 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 11l5-5m0 0l5 5m-5-5v12"></path>
                                </svg>
                            </div>
                        </th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse($users as $user)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors cursor-pointer" onclick="window.location.href='{{ route('admin.users.show', $user) }}'">
                        <td class="py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 h-10 w-10">
                                    <div class="h-10 w-10 rounded-full bg-gradient-to-br from-blue-500 to-blue-600 flex items-center justify-center text-white font-semibold text-sm shadow-md">
                                        {{ strtoupper(substr($user->first_name, 0, 1)) }}{{ strtoupper(substr($user->last_name, 0, 1)) }}
                                    </div>
                                </div>
                                <div class="ml-3">
                                    <div class="text-sm font-medium text-gray-900 dark:text-white">
                                        {{ $user->full_name }}
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td class="py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900 dark:text-white">
                                {{ $user->email }}
                                @if($user->hasVerifiedEmail())
                                    <span class="ml-2 inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900/20 dark:text-green-400">
                                        ✓
                                    </span>
                                @endif
                            </div>
                        </td>
                        <td class="py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                            {{ $user->phone_number ?: '-' }}
                        </td>
                        <td class="py-4 whitespace-nowrap">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                {{ $user->role === 'ADMIN' ? 'bg-purple-100 text-purple-800 dark:bg-purple-900/20 dark:text-purple-400' : 
                                   ($user->role === 'INSTRUCTOR' ? 'bg-blue-100 text-blue-800 dark:bg-blue-900/20 dark:text-blue-400' : 
                                   'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300') }}">
                                {{ $user->role === 'USER' ? 'User' : ($user->role === 'INSTRUCTOR' ? 'Instructor' : ($user->role === 'ADMIN' ? 'Admin' : $user->role)) }}
                            </span>
                        </td>
                        <td class="py-4 whitespace-nowrap">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                {{ $user->is_active ? 'bg-green-100 text-green-800 dark:bg-green-900/20 dark:text-green-400' : 'bg-red-100 text-red-800 dark:bg-red-900/20 dark:text-red-400' }}">
                                {{ $user->is_active ? 'Active' : 'Inactive' }}
                            </span>
                        </td>
                        <td class="py-4 whitespace-nowrap">
                            @if($user->subscriptions->isNotEmpty())
                                @php
                                    $subscription = $user->subscriptions->first();
                                    $isActive = $subscription->status === 'active' && 
                                               ($subscription->ends_at === null || $subscription->ends_at->isFuture());
                                @endphp
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                    {{ $isActive ? 'bg-green-100 text-green-800 dark:bg-green-900/20 dark:text-green-400' : 
                                       ($subscription->status === 'pending' ? 'bg-orange-100 text-orange-800 dark:bg-orange-900/20 dark:text-orange-400' : 'bg-red-100 text-red-800 dark:bg-red-900/20 dark:text-red-400') }}">
                                    {{ ucfirst($subscription->status) }}
                                </span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300">
                                    None
                                </span>
                            @endif
                        </td>
                        <td class="py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                            {{ $user->created_at->format('M j, Y') }}
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="py-12 text-center">
                            <div class="flex flex-col items-center">
                                <svg class="w-12 h-12 text-gray-400 dark:text-gray-500 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                                </svg>
                                <p class="text-sm text-gray-500 dark:text-gray-400 font-medium">
                                    No users found
                                </p>
                                <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">
                                    Try adjusting your search or filter criteria
                                </p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <!-- Pagination -->
        @if($users->hasPages())
        <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800">
            <div class="flex items-center justify-between">
                <div class="text-sm text-gray-700 dark:text-gray-300">
                    Showing <span class='font-medium'>{{ $users->firstItem() }}</span> to <span class='font-medium'>{{ $users->lastItem() }}</span> of <span class='font-medium'>{{ $users->total() }}</span> users
                </div>
                {{ $users->links() }}
            </div>
        </div>
        @endif
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('search-input');
    const roleFilter = document.getElementById('role-filter');
    const statusFilter = document.getElementById('status-filter');
    const subscriptionFilter = document.getElementById('subscription-filter');
    const clearFiltersBtn = document.getElementById('clear-filters');
    const scrollLeftBtn = document.getElementById('scroll-left');
    const scrollRightBtn = document.getElementById('scroll-right');
    const tableContainer = document.getElementById('table-container');
    
    let filterTimeout;
    
    // Scroll functionality
    if (scrollLeftBtn && scrollRightBtn && tableContainer) {
        scrollLeftBtn.addEventListener('click', function() {
            tableContainer.scrollBy({ left: -200, behavior: 'smooth' });
        });
        
        scrollRightBtn.addEventListener('click', function() {
            tableContainer.scrollBy({ left: 200, behavior: 'smooth' });
        });
        
        // Hide/show scroll buttons based on scroll position
        tableContainer.addEventListener('scroll', function() {
            const maxScroll = tableContainer.scrollWidth - tableContainer.clientWidth;
            
            if (tableContainer.scrollLeft <= 0) {
                scrollLeftBtn.style.opacity = '0.5';
                scrollLeftBtn.style.cursor = 'not-allowed';
            } else {
                scrollLeftBtn.style.opacity = '1';
                scrollLeftBtn.style.cursor = 'pointer';
            }
            
            if (tableContainer.scrollLeft >= maxScroll) {
                scrollRightBtn.style.opacity = '0.5';
                scrollRightBtn.style.cursor = 'not-allowed';
            } else {
                scrollRightBtn.style.opacity = '1';
                scrollRightBtn.style.cursor = 'pointer';
            }
        });
        
        // Initial state
        const maxScroll = tableContainer.scrollWidth - tableContainer.clientWidth;
        if (maxScroll <= 0) {
            scrollLeftBtn.style.display = 'none';
            scrollRightBtn.style.display = 'none';
        } else {
            scrollLeftBtn.style.opacity = '0.5';
            scrollLeftBtn.style.cursor = 'not-allowed';
        }
    }
    
    function applyFilters() {
        clearTimeout(filterTimeout);
        filterTimeout = setTimeout(() => {
            const url = new URL(window.location);
            
            // Search filter
            if (searchInput.value.trim()) {
                url.searchParams.set('search', searchInput.value.trim());
            } else {
                url.searchParams.delete('search');
            }
            
            // Role filter
            if (roleFilter.value) {
                url.searchParams.set('role', roleFilter.value);
            } else {
                url.searchParams.delete('role');
            }
            
            // Status filter
            if (statusFilter.value) {
                url.searchParams.set('status', statusFilter.value);
            } else {
                url.searchParams.delete('status');
            }
            
            // Subscription filter
            if (subscriptionFilter.value) {
                url.searchParams.set('subscription', subscriptionFilter.value);
            } else {
                url.searchParams.delete('subscription');
            }
            
            url.searchParams.set('page', '1'); // Reset to first page when filtering
            window.location.href = url.toString();
        }, 300); // Debounce filters
    }
    
    // Event listeners
    if (searchInput) searchInput.addEventListener('input', applyFilters);
    if (roleFilter) roleFilter.addEventListener('change', applyFilters);
    if (statusFilter) statusFilter.addEventListener('change', applyFilters);
    if (subscriptionFilter) subscriptionFilter.addEventListener('change', applyFilters);
    
    if (clearFiltersBtn) {
        clearFiltersBtn.addEventListener('click', function() {
            searchInput.value = '';
            roleFilter.value = '';
            statusFilter.value = '';
            subscriptionFilter.value = '';
            
            // Clear all URL parameters
            const url = new URL(window.location);
            url.searchParams.delete('search');
            url.searchParams.delete('role');
            url.searchParams.delete('status');
            url.searchParams.delete('subscription');
            url.searchParams.delete('date_filter');
            url.searchParams.delete('date_range');
            url.searchParams.delete('filter');
            url.searchParams.delete('period');
            url.searchParams.delete('sort');
            url.searchParams.delete('order');
            url.searchParams.set('page', '1');
            
            window.location.href = url.toString();
        });
    }
    
    // Maintain filter values on page load
    const urlParams = new URLSearchParams(window.location.search);
    if (urlParams.get('search')) searchInput.value = urlParams.get('search');
    if (urlParams.get('role')) roleFilter.value = urlParams.get('role');
    if (urlParams.get('status')) statusFilter.value = urlParams.get('status');
    if (urlParams.get('subscription')) subscriptionFilter.value = urlParams.get('subscription');
});

function sortTable(column) {
    const url = new URL(window.location);
    const currentSort = url.searchParams.get('sort');
    const currentOrder = url.searchParams.get('order') || 'asc';
    
    if (currentSort === column) {
        // Toggle order if same column
        url.searchParams.set('order', currentOrder === 'asc' ? 'desc' : 'asc');
    } else {
        // Set new column and default to asc
        url.searchParams.set('sort', column);
        url.searchParams.set('order', 'asc');
    }
    
    url.searchParams.set('page', '1');
    window.location.href = url.toString();
}
</script>
@endpush
@endsection
