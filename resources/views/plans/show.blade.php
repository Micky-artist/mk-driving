@extends('layouts.app')

@push('styles')
<style>
    .plan-card {
        transition: all 0.3s ease;
    }
    .plan-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
    }
    .feature-icon {
        background-color: rgba(255, 152, 0, 0.1);
        color: #FF9800;
    }
</style>
@endpush

@section('content')
<div x-data="{
    showPaymentModal: false,
    phoneNumber: '',
    isLoading: false,
    error: '',
    success: false,
    paymentStatus: 'idle', // idle, processing, success, failed
    
    init() {
        // Listen for payment status updates from Livewire or other events
        window.addEventListener('payment-status-updated', (e) => {
            this.paymentStatus = e.detail.status;
            this.isLoading = false;
            
            if (e.detail.status === 'success') {
                this.success = true;
                // Redirect to dashboard after 3 seconds
                setTimeout(() => {
                    window.location.href = '{{ route('dashboard', app()->getLocale()) }}';
                }, 3000);
            }
        });
    },
    
    async processPayment() {
        if (!this.phoneNumber) {
            this.error = 'Please enter your phone number';
            return;
        }
        
        this.isLoading = true;
        this.error = '';
        
        try {
            // Call your payment processing endpoint
            const response = await fetch('{{ route('payment.initiate') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    plan_id: '{{ $formattedPlan['id'] }}',
                    phone_number: this.phoneNumber,
                    amount: {{ $formattedPlan['price'] }},
                    currency: 'RWF'
                })
            });
            
            const data = await response.json();
            
            if (!response.ok) {
                throw new Error(data.message || 'Payment failed');
            }
            
            // Show success state
            this.paymentStatus = 'processing';
            
            // Poll for payment status
            this.checkPaymentStatus(data.payment_reference);
            
        } catch (err) {
            console.error('Payment error:', err);
            this.error = err.message || 'An error occurred. Please try again.';
            this.isLoading = false;
        }
    },
    
    async checkPaymentStatus(reference) {
        try {
            const response = await fetch(`/api/payment/status/${reference}`);
            const data = await response.json();
            
            if (data.status === 'success') {
                this.paymentStatus = 'success';
                this.success = true;
                // Redirect after a short delay
                setTimeout(() => {
                    window.location.href = '{{ route('dashboard', app()->getLocale()) }}';
                }, 3000);
            } else if (data.status === 'failed') {
                this.paymentStatus = 'failed';
                this.error = 'Payment failed. Please try again.';
            } else {
                // Continue polling
                setTimeout(() => this.checkPaymentStatus(reference), 3000);
            }
        } catch (err) {
            console.error('Status check error:', err);
            setTimeout(() => this.checkPaymentStatus(reference), 3000);
        }
    }
}" class="min-h-screen bg-gray-50 py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-5xl mx-auto">
        <!-- Back button -->
        <div class="mb-8">
            <a href="{{ route('plans.index') }}" class="inline-flex items-center text-orange-600 hover:text-orange-700 font-medium">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                {{ __('Back to plans') }}
            </a>
        </div>

        <!-- Plan Card -->
        <div class="bg-white rounded-xl shadow-md overflow-hidden plan-card">
            <div class="md:flex">
                <div class="md:flex-shrink-0 bg-gradient-to-br from-blue-900 to-blue-800 p-8 flex items-center justify-center md:w-1/3">
                    <div class="text-center">
                        <span class="inline-flex items-center px-4 py-2 rounded-full text-sm font-semibold bg-orange-100 text-orange-800 mb-4">
                            {{ $formattedPlan['maxQuizzes'] ? 'Up to ' . $formattedPlan['maxQuizzes'] . ' quizzes' : 'Unlimited Quizzes' }}
                        </span>
                        <h2 class="text-3xl font-bold text-white mb-2">{{ $formattedPlan['name'] }}</h2>
                        <div class="text-4xl font-extrabold text-white">
                            RWF {{ number_format($formattedPlan['price'], 0) }}
                            <span class="text-xl text-blue-200">/month</span>
                        </div>
                        <p class="mt-4 text-blue-100">{{ $formattedPlan['description'] }}</p>
                        
                        <button
                            @click="showPaymentModal = true"
                            class="mt-8 w-full bg-orange-500 hover:bg-orange-600 text-white font-bold py-3 px-6 rounded-lg transition duration-300 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:ring-offset-2"
                        >
                            Subscribe Now
                        </button>
                    </div>
                </div>
                
                <div class="p-8 md:w-2/3">
                    <h3 class="text-xl font-semibold text-gray-800 mb-6">Plan Features</h3>
                    <ul class="space-y-4">
                        @forelse($formattedPlan['features'] as $feature)
                            <li class="flex items-start">
                                <span class="feature-icon rounded-full p-1.5 mr-3 mt-0.5">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                    </svg>
                                </span>
                                <span class="text-gray-700">{{ $feature }}</span>
                            </li>
                        @empty
                            <li class="text-gray-500">{{ __('No features available') }}</li>
                        @endforelse
                    </ul>
                    
                    <div class="mt-10 pt-6 border-t border-gray-200">
                        <h4 class="text-sm font-medium text-gray-500 uppercase tracking-wider mb-4">Need help?</h4>
                        <p class="text-gray-600 text-sm">
                            Have questions about this plan? 
                            <a href="#" class="text-orange-600 hover:text-orange-700 font-medium">Contact our support team</a> 
                            for assistance.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Payment Modal -->
    <div 
        x-show="showPaymentModal" 
        x-transition:enter="ease-out duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="ease-in duration-200"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="fixed z-50 inset-0 overflow-y-auto" 
        aria-labelledby="modal-title" 
        role="dialog" 
        aria-modal="true"
    >
        <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <!-- Background overlay -->
            <div 
                x-show="showPaymentModal" 
                x-transition:enter="ease-out duration-300"
                x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100"
                x-transition:leave="ease-in duration-200"
                x-transition:leave-start="opacity-100"
                x-transition:leave-end="opacity-0"
                class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" 
                @click="showPaymentModal = false"
                aria-hidden="true"
            ></div>

            <!-- Modal content -->
            <div 
                x-show="showPaymentModal"
                x-transition:enter="ease-out duration-300"
                x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                x-transition:leave="ease-in duration-200"
                x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                class="inline-block align-bottom bg-white rounded-lg px-4 pt-5 pb-4 text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full sm:p-6"
            >
                <!-- Payment form -->
                <div x-show="paymentStatus === 'idle' || paymentStatus === 'failed'">
                    <div class="sm:flex sm:items-start">
                        <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-orange-100 sm:mx-0 sm:h-10 sm:w-10">
                            <svg class="h-6 w-6 text-orange-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                            <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">
                                Complete Your Subscription
                            </h3>
                            <div class="mt-2">
                                <p class="text-sm text-gray-500">
                                    You're about to subscribe to the <span class="font-semibold">{{ $formattedPlan['name'] }}</span> plan for 
                                    <span class="font-semibold">RWF {{ number_format($formattedPlan['price'], 0) }}/month</span>.
                                </p>
                                <p class="mt-2 text-sm text-gray-500">
                                    Enter your MTN Mobile Money number below to complete the payment.
                                </p>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Error message -->
                    <div x-show="error" class="mt-4 bg-red-50 border-l-4 border-red-400 p-4">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                                </svg>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm text-red-700" x-text="error"></p>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Phone number input -->
                    <div class="mt-5 sm:mt-6">
                        <label for="phone" class="block text-sm font-medium text-gray-700">MTN Mobile Money Number</label>
                        <div class="mt-1 relative rounded-md shadow-sm">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <span class="text-gray-500 sm:text-sm">+250</span>
                            </div>
                            <input 
                                type="tel" 
                                name="phone" 
                                id="phone" 
                                x-model="phoneNumber"
                                class="focus:ring-orange-500 focus:border-orange-500 block w-full pl-16 sm:pl-14 sm:text-sm border-gray-300 rounded-md py-3 px-4" 
                                placeholder="78 123 4567"
                                :disabled="isLoading"
                            >
                        </div>
                        <p class="mt-2 text-sm text-gray-500">We'll send a payment request to this number</p>
                    </div>
                </div>
                
                <!-- Processing state -->
                <div x-show="paymentStatus === 'processing'" class="text-center py-8">
                    <div class="animate-spin rounded-full h-16 w-16 border-b-2 border-orange-500 mx-auto"></div>
                    <h3 class="mt-4 text-lg font-medium text-gray-900">Processing Payment</h3>
                    <p class="mt-2 text-sm text-gray-500">
                        Please check your phone and complete the payment on your MTN Mobile Money app.
                    </p>
                    <p class="mt-2 text-sm text-gray-400">
                        This may take a moment...
                    </p>
                </div>
                
                <!-- Success state -->
                <div x-show="paymentStatus === 'success'" class="text-center py-8">
                    <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-green-100">
                        <svg class="h-10 w-10 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                    </div>
                    <h3 class="mt-4 text-lg font-medium text-gray-900">Payment Successful!</h3>
                    <p class="mt-2 text-sm text-gray-500">
                        Your subscription to {{ $formattedPlan['name'] }} is now active.
                    </p>
                    <p class="mt-2 text-sm text-gray-400">
                        Redirecting to your dashboard...
                    </p>
                </div>
                
                <!-- Footer buttons -->
                <div class="mt-5 sm:mt-6 sm:grid sm:grid-cols-2 sm:gap-3 sm:grid-flow-row-dense">
                    <button 
                        x-show="paymentStatus === 'idle' || paymentStatus === 'failed'"
                        type="button" 
                        @click="processPayment()"
                        :disabled="isLoading"
                        class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-orange-600 text-base font-medium text-white hover:bg-orange-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-orange-500 sm:col-start-2 sm:text-sm disabled:opacity-75 disabled:cursor-not-allowed"
                    >
                        <span x-show="!isLoading">Pay RWF {{ number_format($formattedPlan['price'], 0) }}</span>
                        <span x-show="isLoading">Processing...</span>
                    </button>
                    <button 
                        x-show="paymentStatus === 'idle' || paymentStatus === 'failed'"
                        type="button" 
                        @click="showPaymentModal = false"
                        :disabled="isLoading"
                        class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-orange-500 sm:mt-0 sm:col-start-1 sm:text-sm"
                    >
                        Cancel
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Format phone number as user types
    document.addEventListener('alpine:init', () => {
        Alpine.data('formatPhone', () => ({
            phone: '',
            
            format() {
                // Remove all non-digit characters
                let numbers = this.phone.replace(/\D/g, '');
                
                // Format as 078 123 4567
                if (numbers.length > 0) {
                    numbers = numbers.match(/(\d{0,3})(\d{0,3})(\d{0,4})/);
                    this.phone = !numbers[2] ? numbers[1] : numbers[1] + ' ' + numbers[2] + (numbers[3] ? ' ' + numbers[3] : '');
                }
            }
        }));
    });
</script>
@endpush

@endsection
