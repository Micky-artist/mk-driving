<div class="bg-white rounded-lg shadow-md overflow-hidden border border-gray-200 hover:shadow-lg transition-shadow duration-300">
    <div class="p-6">
        <div class="flex justify-between items-start mb-4">
            <h3 class="text-xl font-bold text-gray-900">{{ $plan->name }}</h3>
            @if($plan->is_featured)
                <span class="bg-yellow-100 text-yellow-800 text-xs font-semibold px-2.5 py-0.5 rounded">Popular</span>
            @endif
        </div>
        
        <div class="mb-6">
            <span class="text-3xl font-bold text-gray-900">${{ number_format($plan->price, 2) }}</span>
            <span class="text-gray-600">/{{ $plan->billing_cycle }}</span>
        </div>
        
        <p class="text-gray-600 mb-6">{{ $plan->description }}</p>
        
        <ul class="space-y-3 mb-8">
            @foreach($plan->features as $feature)
                <li class="flex items-center">
                    <svg class="w-5 h-5 text-green-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                    {{ $feature }}
                </li>
            @endforeach
        </ul>
        
        @auth
            <form action="{{ route('subscriptions.store', $plan) }}" method="POST">
                @csrf
                <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-4 rounded-lg transition duration-200">
                    Subscribe Now
                </button>
            </form>
        @else
            <a href="{{ route('login') }}" class="block w-full text-center bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-4 rounded-lg transition duration-200">
                Sign In to Subscribe
            </a>
        @endauth
    </div>
</div>
