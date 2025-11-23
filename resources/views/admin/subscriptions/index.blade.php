@extends('admin.layouts.app')

@section('content')
    <div class="mb-6 flex justify-between items-center">
        <h2 class="text-xl font-semibold">Subscription Requests</h2>
    </div>

    @if($subscriptions->isEmpty())
        <div class="bg-white p-6 rounded-lg shadow">
            <p class="text-gray-500">No pending subscription requests.</p>
        </div>
    @else
        <div class="bg-white rounded-lg shadow overflow-hidden">
            <table class="table">
                <thead>
                    <tr>
                        <th>User</th>
                        <th>Plan</th>
                        <th>Requested On</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($subscriptions as $subscription)
                        <tr>
                            <td>{{ $subscription->user->name }}<br><small class="text-gray-500">{{ $subscription->user->email }}</small></td>
                            <td>{{ $subscription->plan->name[app()->getLocale()] ?? $subscription->plan->name['en'] }}</td>
                            <td>{{ $subscription->created_at->format('M d, Y H:i') }}</td>
                            <td>
                                <span class="px-2 py-1 text-xs rounded-full 
                                    {{ $subscription->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                    {{ $subscription->status === 'active' ? 'bg-green-100 text-green-800' : '' }}
                                    {{ $subscription->status === 'rejected' ? 'bg-red-100 text-red-800' : '' }}
                                ">
                                    {{ ucfirst($subscription->status) }}
                                </span>
                            </td>
                            <td class="space-x-2">
                                @if($subscription->status === 'pending')
                                    <form action="{{ route('admin.subscriptions.approve', $subscription) }}" method="POST" class="inline">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="btn btn-success">Approve</button>
                                    </form>
                                    <form action="{{ route('admin.subscriptions.reject', $subscription) }}" method="POST" class="inline">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="btn btn-danger">Reject</button>
                                    </form>
                                @else
                                    <span class="text-gray-500">No actions available</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            <div class="p-4">
                {{ $subscriptions->links() }}
            </div>
        </div>
    @endif
@endsection
