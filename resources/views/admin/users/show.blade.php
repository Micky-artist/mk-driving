@extends('admin.layouts.app')

@section('title', 'User Details: ' . $user->full_name)

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">User Details</h1>
            <p class="mt-1 text-sm text-gray-500">Manage user account and permissions</p>
        </div>
        <div class="mt-4 md:mt-0">
            <a href="{{ route('admin.users.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                Back to Users
            </a>
        </div>
    </div>

    <div class="bg-white shadow overflow-hidden sm:rounded-lg">
        <div class="px-4 py-5 sm:px-6 border-b border-gray-200">
            <div class="flex items-center">
                <div class="flex-shrink-0 h-12 w-12 rounded-full bg-blue-100 flex items-center justify-center text-blue-600 text-xl font-semibold">
                    {{ strtoupper(substr($user->first_name, 0, 1)) }}{{ strtoupper(substr($user->last_name, 0, 1)) }}
                </div>
                <div class="ml-4">
                    <h3 class="text-lg leading-6 font-medium text-gray-900">
                        {{ $user->full_name }}
                        <span class="ml-2 px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                            {{ $user->isAdmin() ? 'bg-purple-100 text-purple-800' : 'bg-blue-100 text-blue-800' }}">
                            {{ ucfirst($user->role) }}
                        </span>
                    </h3>
                    <p class="mt-1 max-w-2xl text-sm text-gray-500">
                        Member since {{ $user->created_at->format('F j, Y') }}
                    </p>
                </div>
            </div>
        </div>

        <div class="border-t border-gray-200">
            <dl>
                <!-- Personal Information -->
                <div class="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                    <dt class="text-sm font-medium text-gray-500">Personal Information</dt>
                    <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <p class="text-xs text-gray-500">First Name</p>
                                <p class="font-medium">{{ $user->first_name }}</p>
                            </div>
                            <div>
                                <p class="text-xs text-gray-500">Last Name</p>
                                <p class="font-medium">{{ $user->last_name }}</p>
                            </div>
                            <div>
                                <p class="text-xs text-gray-500">Email Address</p>
                                <p class="font-medium flex items-center">
                                    {{ $user->email }}
                                    @if($user->hasVerifiedEmail())
                                        <span class="ml-1 text-green-500" title="Verified">
                                            <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.643.304 1.254.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                            </svg>
                                        </span>
                                    @endif
                                </p>
                            </div>
                            <div>
                                <p class="text-xs text-gray-500">Phone Number</p>
                                <p class="font-medium">{{ $user->phone ?? 'Not provided' }}</p>
                            </div>
                        </div>
                    </dd>
                </div>

                <!-- Account Status -->
                <div class="bg-white px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                    <dt class="text-sm font-medium text-gray-500">Account Status</dt>
                    <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                        <div class="flex items-center">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                {{ $user->status === 'active' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                                {{ ucfirst($user->status) }}
                            </span>
                            @if($user->status_reason)
                                <span class="ml-2 text-sm text-gray-500">({{ $user->status_reason }})</span>
                            @endif
                        </div>
                    </dd>
                </div>

                <!-- Subscription Information -->
                <div class="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                    <dt class="text-sm font-medium text-gray-500">Subscription</dt>
                    <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                        @if($user->subscriptions->isNotEmpty())
                            @php 
                                $subscription = $user->subscriptions->first();
                                $isActive = $subscription->status === 'active' && 
                                           ($subscription->ends_at === null || $subscription->ends_at->isFuture());
                                $isTrial = $subscription->trial_ends_at && $subscription->trial_ends_at->isFuture();
                            @endphp
                            
                            <div class="space-y-4">
                                <!-- Subscription Status -->
                                <div class="flex items-center">
                                    <div class="flex-shrink-0">
                                        <span class="h-5 w-5 rounded-full flex items-center justify-center 
                                            {{ $isActive ? 'bg-green-100 text-green-600' : 'bg-red-100 text-red-600' }}">
                                            @if($isActive)
                                                <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                                </svg>
                                            @else
                                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                                </svg>
                                            @endif
                                        </span>
                                    </div>
                                    <div class="ml-3">
                                        <p class="text-sm font-medium text-gray-900">
                                            {{ $subscription->plan->name ?? 'Unknown Plan' }}
                                            <span class="ml-2 px-2 py-0.5 text-xs font-medium rounded-full 
                                                {{ $isActive ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                                {{ $isTrial ? 'Trial' : ucfirst($subscription->status) }}
                                            </span>
                                            @if($isTrial)
                                                <span class="ml-1 text-xs text-yellow-600">({{ $subscription->trial_ends_at->diffForHumans() }} left)</span>
                                            @endif
                                        </p>
                                    </div>
                                </div>

                                <!-- Subscription Details -->
                                <div class="ml-8 pl-4 border-l-2 border-gray-200 space-y-2">
                                    @if($subscription->plan)
                                        <div class="flex justify-between text-sm">
                                            <span class="text-gray-500">Plan Price:</span>
                                            <span class="font-medium">{{ number_format($subscription->plan->price, 2) }} {{ $subscription->plan->currency }}/{{ $subscription->plan->billing_interval }}</span>
                                        </div>
                                    @endif
                                    
                                    <div class="flex justify-between text-sm">
                                        <span class="text-gray-500">Started:</span>
                                        <span class="font-medium">{{ $subscription->created_at->format('M j, Y') }}</span>
                                    </div>

                                    @if($subscription->trial_ends_at)
                                        <div class="flex justify-between text-sm">
                                            <span class="text-gray-500">Trial Ends:</span>
                                            <span class="font-medium">{{ $subscription->trial_ends_at->format('M j, Y') }}</span>
                                        </div>
                                    @endif

                                    @if($subscription->ends_at)
                                        <div class="flex justify-between text-sm">
                                            <span class="text-gray-500">{{ $subscription->ends_at->isFuture() ? 'Renews' : 'Ended' }}:</span>
                                            <span class="font-medium">
                                                {{ $subscription->ends_at->format('M j, Y') }}
                                                <span class="text-gray-500">({{ $subscription->ends_at->diffForHumans() }})</span>
                                            </span>
                                        </div>
                                    @endif

                                    @if($subscription->payment_method)
                                        <div class="flex justify-between text-sm">
                                            <span class="text-gray-500">Payment Method:</span>
                                            <span class="font-medium">{{ ucfirst($subscription->payment_method) }} •••• {{ $subscription->card_last_four ?? '' }}</span>
                                        </div>
                                    @endif

                                    @if($subscription->status === 'cancelled' && $subscription->ends_at)
                                        <div class="mt-2 p-2 bg-yellow-50 text-yellow-700 text-sm rounded-md">
                                            <p>This subscription is cancelled but remains active until {{ $subscription->ends_at->format('M j, Y') }}.</p>
                                        </div>
                                    @endif
                                </div>

                                <!-- Subscription Actions -->
                                <div class="ml-8 pt-2 flex space-x-3">
                                    @if($isActive && !$subscription->cancelled())
                                        <form action="{{ route('admin.subscriptions.cancel', $subscription) }}" method="POST" class="inline-block">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-xs text-red-600 hover:text-red-800" 
                                                onclick="return confirm('Are you sure you want to cancel this subscription?')">
                                                Cancel Subscription
                                            </button>
                                        </form>
                                    @endif
                                    
                                    @if($subscription->cancelled() && !$subscription->ended())
                                        <form action="{{ route('admin.subscriptions.resume', $subscription) }}" method="POST" class="inline-block">
                                            @csrf
                                            <button type="submit" class="text-xs text-blue-600 hover:text-blue-800">
                                                Resume Subscription
                                            </button>
                                        </form>
                                    @endif

                                    @if($subscription->ended())
                                        <form action="{{ route('admin.subscriptions.renew', $subscription) }}" method="POST" class="inline-block">
                                            @csrf
                                            <button type="submit" class="text-xs text-green-600 hover:text-green-800">
                                                Renew Subscription
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </div>
                        @else
                            <div class="text-center py-4">
                                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <h3 class="mt-2 text-sm font-medium text-gray-900">No subscription</h3>
                                <p class="mt-1 text-sm text-gray-500">This user doesn't have an active subscription.</p>
                                <div class="mt-6">
                                    <a href="#" class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                        <svg class="-ml-1 mr-2 h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                        </svg>
                                        Assign Subscription
                                    </a>
                                </div>
                            </div>
                        @endif
                    </dd>
                </div>

                <!-- Recent Activity -->
                <div class="bg-white px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                    <dt class="text-sm font-medium text-gray-500">Recent Activity</dt>
                    <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                        @if($user->quizAttempts->isNotEmpty())
                            <ul class="border border-gray-200 rounded-md divide-y divide-gray-200">
                                @foreach($user->quizAttempts->take(3) as $attempt)
                                    <li class="pl-3 pr-4 py-3 flex items-center justify-between text-sm">
                                        <div class="w-0 flex-1 flex items-center">
                                            <svg class="flex-shrink-0 h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                            </svg>
                                            <span class="ml-2 flex-1 w-0 truncate">
                                                Scored {{ $attempt->score }}% on {{ $attempt->quiz->title }}
                                            </span>
                                        </div>
                                        <div class="ml-4 flex-shrink-0">
                                            <span class="text-gray-500">{{ $attempt->completed_at->diffForHumans() }}</span>
                                        </div>
                                    </li>
                                @endforeach
                            </ul>
                        @else
                            <p class="text-gray-500 italic">No recent activity</p>
                        @endif
                    </dd>
                </div>

                <!-- Account Actions -->
                <div class="bg-gray-50 px-4 py-4 sm:px-6 sm:py-5">
                    <div class="flex justify-end space-x-3">
                        <form action="{{ route('admin.users.update-status', $user) }}" method="POST" class="inline-block">
                            @csrf
                            @method('PATCH')
                            <input type="hidden" name="status" value="{{ $user->status === 'active' ? 'suspended' : 'active' }}">
                            <button type="submit" class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                {{ $user->status === 'active' ? 'Suspend Account' : 'Activate Account' }}
                            </button>
                        </form>
                        <a href="#" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            Edit User
                        </a>
                    </div>
                </div>
            </dl>
        </div>
    </div>
</div>
@endsection
