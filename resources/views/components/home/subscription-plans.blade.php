@php
    // Debug: Log the plans data
    \Log::info('Subscription plans data received:', [
        'plans_count' => count($plans),
        'plans_sample' => count($plans) > 0 ? [
            'id' => $plans[0]['id'] ?? null,
            'slug' => $plans[0]['slug'] ?? null,
            'name' => $plans[0]['name'] ?? null,
            'display_name' => $plans[0]['display_name'] ?? null
        ] : 'No plans found'
    ]);
    
    // Helper functions to determine plan styling
    $getPlanType = function($plan) {
        // Debug the incoming plan data
        \Log::debug('Processing plan:', [
            'plan_data' => $plan,
            'is_array' => is_array($plan),
            'is_object' => is_object($plan)
        ]);

        // First try to get the slug directly
        $slug = null;
        
        if (is_array($plan)) {
            $slug = $plan['slug'] ?? $plan['id'] ?? null;
        } elseif (is_object($plan)) {
            $slug = $plan->slug ?? $plan->id ?? null;
        }
        
        // If we have a slug, use it directly
        if (!empty($slug)) {
            $slug = strtolower(trim($slug));
            
            // Debug information
            \Log::info('Plan theming - using slug:', [
                'plan_id' => is_array($plan) ? ($plan['id'] ?? null) : (is_object($plan) ? $plan->id : null),
                'slug' => $slug,
                'plan_data_keys' => is_array($plan) ? array_keys($plan) : []
            ]);
            
            // Return the slug as the plan type if it matches our expected values
            if (in_array($slug, ['gold-unlimited', 'premium', 'standard', 'basic'])) {
                return $slug;
            }
        }
        
        // Fallback to old behavior if no valid slug found
        $planName = is_array($plan)
            ? ($plan['display_name'] ?? (is_array($plan['name'] ?? null) ? ($plan['name']['en'] ?? '') : ($plan['name'] ?? '')))
            : (is_object($plan) ? ($plan->display_name ?? (is_object($plan->name ?? null) ? ($plan->name->en ?? '') : ($plan->name ?? ''))) : '');
        
        $planName = strtolower($planName);
        
        // Debug fallback
        \Log::info('Plan theming - falling back to name matching:', [
            'plan_id' => is_array($plan) ? ($plan['id'] ?? null) : (is_object($plan) ? $plan->id : null),
            'plan_name' => $planName,
            'original_slug' => $slug ?? 'not_found'
        ]);
        
        if (str_contains($planName, 'gold-unlimited')) return 'gold-unlimited';
        if (str_contains($planName, 'premium')) return 'premium';
        if (str_contains($planName, 'standard') || str_contains($planName, 'std')) return 'standard';
        return 'basic';
    };

    $getGradientClass = function($planType) {
        return match($planType) {
            'gold-unlimited' => 'from-amber-500 to-amber-600',
            'premium' => 'from-blue-800 to-blue-900',
            'standard' => 'from-blue-600 to-blue-700',
            default => 'from-blue-100 to-blue-200',
        };
    };

    $getTextClass = function($planType) {
        return match($planType) {
            'gold-unlimited' => 'text-amber-700',
            'premium' => 'text-blue-900',
            'standard' => 'text-blue-800',
            default => 'text-blue-700',
        };
    };

    $getBadgeClass = function($planType) {
        return match($planType) {
            'gold-unlimited' => 'from-amber-500 to-amber-600',
            'premium' => 'from-blue-800 to-blue-900',
            'standard' => 'from-blue-600 to-blue-700',
            default => 'from-blue-400 to-blue-500',
        };
    };

    $getCardBgClass = function($planType) {
        return match($planType) {
            'gold-unlimited' => 'bg-amber-50',
            'premium' => 'bg-blue-50',
            'standard' => 'bg-blue-50',
            default => 'bg-blue-50',
        };
    };

    // Process features array
    $processFeatures = function($features) {
        // If features is already an array, return it directly
        if (is_array($features)) {
            // If it's an associative array with language codes, get the current locale or fallback
            if (isset($features[app()->getLocale()])) {
                $features = (array)$features[app()->getLocale()];
            } elseif (isset($features[config('app.fallback_locale', 'en')])) {
                $features = (array)$features[config('app.fallback_locale', 'en')];
            }
            
            // Ensure all features are strings
            return array_map('strval', array_filter($features, function($item) {
                return is_string($item) || is_numeric($item);
            }));
        }
        
        // If features is a JSON string, decode it
        if (is_string($features)) {
            $decoded = json_decode($features, true);
            if (json_last_error() === JSON_ERROR_NONE) {
                return $this->processFeatures($decoded);
            }
            // If it's not JSON, treat as a single feature
            return [$features];
        }
        
        // If we can't determine the format, return empty array
        return [];
        
        // If features is a string, try to decode it as JSON
        if (is_string($features)) {
            $features = json_decode($features, true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                return [];
            }
        }
        
        // If features is an array with language keys (en, rw), get the current locale
        if (isset($features[app()->getLocale()])) {
            return (array)$features[app()->getLocale()];
        }
        // Fallback to English if current locale not found
        if (isset($features['en'])) {
            return (array)$features['en'];
        }
        
        // If it's a simple array, return as is
        if (is_array($features)) {
            // Handle case where features might be a sequential array of strings
            if (array_values($features) === $features) {
                return array_values(array_filter($features, function($item) {
                    return !empty(trim((string)$item));
                }));
            }
            // If it's an associative array but not with locale keys, return values
            return array_values(array_filter($features));
        }
        
        return [];
    };
@endphp

<div class="my-16">
    <div class="text-center mb-10">
        <h2 class="text-2xl sm:text-3xl font-bold text-gray-900 mb-4">{{ __('home.subscriptionPlans.title') }}</h2>
        <p class="text-gray-600 max-w-2xl mx-auto">
            {{ __('home.subscriptionPlans.subtitle') }}
        </p>
    </div>

    <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-6 max-w-6xl mx-auto px-4">
        @foreach($plans ?? [] as $plan)
            @php
                $planType = $getPlanType($plan);
                $isPopular = $planType === 'premium';
                $features = $processFeatures($plan['features'] ?? []);
                $isCurrentPlan = $plan['is_current'] ?? false;
                
                // Debug the plan type determination
                \Log::info('Plan theming result:', [
                    'plan_id' => $plan['id'] ?? null,
                    'slug' => $plan['slug'] ?? null,
                    'plan_type' => $planType,
                    'is_popular' => $isPopular
                ]);
            @endphp

            <div 
                class="relative pt-8 h-full rounded-xl overflow-visible shadow-md hover:shadow-lg transition-all duration-300 transform hover:-translate-y-1 {{ $getCardBgClass($planType) }}"
            >
                @if($isPopular)
                    <div class="absolute -top-3 left-1/2 -translate-x-1/2 bg-gradient-to-r {{ $getBadgeClass($planType) }} text-white text-xs font-bold px-4 py-1 rounded-full shadow-lg z-50 whitespace-nowrap">
                        {{ __('home.subscriptionPlans.mostPopular') }}
                    </div>
                @endif

                <div class="h-full flex flex-col">
                    <div class="p-6 pb-0">
                        <h3 class="text-xl font-bold {{ $getTextClass($planType) }} mb-2">{{ $plan['display_name'] ?? $plan['name']['en'] ?? 'Unnamed Plan' }}</h3>
                        <p class="text-3xl font-bold {{ $getTextClass($planType) }}">
                            {{ __('home.subscriptionPlans.billing.currency', ['amount' => number_format($plan['price'])]) }}
                        </p>
                        <p class="text-base font-normal text-gray-600 mb-4">
                            @php
                                $duration = $plan['duration'] ?? 0;
                                $durationType = $plan['duration_type'] ?? 'day';
                            @endphp
                            
                            @if($duration <= 0)
                                {{ __('home.subscriptionPlans.billing.duration.no_limit') }}
                            @else
                                @if($durationType === 'hour')
                                    {{ trans_choice('home.subscriptionPlans.billing.duration.hour', $duration, ['count' => $duration]) }}
                                @elseif($durationType === 'day')
                                    {{ trans_choice('home.subscriptionPlans.billing.duration.day', $duration, ['count' => $duration]) }}
                                @elseif($durationType === 'month')
                                    {{ trans_choice('home.subscriptionPlans.billing.duration.month', $duration, ['count' => $duration]) }}
                                @elseif($durationType === 'year')
                                    {{ trans_choice('home.subscriptionPlans.billing.duration.year', $duration, ['count' => $duration]) }}
                                @endif
                                
                                @if(($plan['billing_cycle'] ?? '') === 'recurring')
                                    <span class="block text-sm mt-1">{{ __('home.subscriptionPlans.billing.monthly', ['period' => trans_choice('home.subscriptionPlans.billing.duration.month', 1, ['count' => 1])]) }}</span>
                                @endif
                            @endif
                        </p>
                        <p class="text-gray-600 mb-6">{{ $plan['display_description'] ?? $plan['description']['en'] ?? '' }}</p>
                    </div>

                    <div class="p-6 pt-0 flex-grow">
                        <ul class="space-y-3 mb-6">
                            @forelse($features as $feature)
                                <li class="flex items-start">
                                    <svg class="h-5 w-5 {{ $getTextClass($planType) }} mt-0.5 mr-2 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                    </svg>
                                    <span class="break-words text-gray-700">{{ $feature }}</span>
                                </li>
                            @empty
                                <li class="text-gray-500 text-sm">{{ __('home.subscriptionPlans.noFeatures') }}</li>
                            @endforelse
                        </ul>
                    </div>

                    <div class="p-6 pt-0 mt-auto">
                        @if($isCurrentPlan)
                            <button 
                                class="w-full text-center bg-gray-400 text-white font-medium py-2 px-4 rounded-lg cursor-not-allowed"
                                disabled
                            >
                                {{ __('home.subscriptionPlans.currentPlan') }}
                            </button>
                        @else
                            <div class="w-full">
                                <button 
                                    x-data="{}"
                                    @click="
                                        $dispatch('open-payment-modal', { 
                                            planId: '{{ $plan['id'] }}',
                                            planName: '{{ addslashes($plan['display_name'] ?? $plan['name']['en'] ?? 'Unnamed Plan') }}',
                                            amount: {{ $plan['price'] }},
                                            currency: 'RWF',
                                            duration: {{ $plan['duration'] ?? 1 }}
                                        });
                                    "
                                    class="w-full text-center {{ $planType === 'gold' ? 'bg-yellow-500 hover:bg-yellow-600' : 'bg-teal-600 hover:bg-teal-700' }} text-white font-medium py-2 px-4 rounded-lg transition-colors"
                                >
                                    <span>{{ __('home.subscriptionPlans.subscribe') }}</span>
                                </button>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <div class="mt-10 text-center">
        <p class="text-gray-600 mb-4">{{ __('home.subscriptionPlans.needHelp') }}</p>
        <a 
            href="{{ route('home', ['#subscription-plans']) }}" 
            class="text-teal-600 font-medium hover:underline"
        >
            {{ __('home.subscriptionPlans.comparePlans') }}
        </a>
    </div>
</div>

<!-- Payment Modal -->
<div x-data="{
    showModal: false,
    init() {
        this.$watch('showModal', value => {
            if (value) {
                document.body.classList.add('overflow-hidden');
            } else {
                document.body.classList.remove('overflow-hidden');
            }
        });
    },
    planDuration: 1,
    isLoading: false,
    error: null,
    success: false,
    paymentStatus: 'idle',
    planId: null,
    planName: '',
    planDescription: '',
    planFeatures: [],
    amount: 0,
    currency: 'RWF',
    phoneNumber: '',
    paymentReference: null,
    
    init() {
        // Handle body scroll when modal is open
        this.$watch('showModal', value => {
            if (value) {
                document.body.style.overflow = 'hidden';
            } else {
                document.body.style.overflow = '';
            }
        });
        
        // Listen for the open-payment-modal event
        this.$on('open-payment-modal', (event) => {
            this.planId = event.detail.planId;
            this.planName = event.detail.planName;
            this.planDescription = event.detail.planDescription || '';
            this.planFeatures = event.detail.planFeatures || [];
            this.amount = event.detail.amount;
            this.currency = event.detail.currency || 'RWF';
            this.planDuration = event.detail.duration || 1;
            this.showModal = true;
            this.resetForm();
        });
    },
    
    
    resetForm() {
        this.isLoading = false;
        this.error = null;
        this.success = false;
        this.paymentStatus = 'idle';
        this.phoneNumber = '';
        this.paymentReference = null;
    },
    
    closeModal() {
        if (!this.isLoading) {
            this.showModal = false;
            this.resetForm();
        }
    },
    
    formatPhoneNumber() {
        // Remove all non-digit characters
        let numbers = this.phoneNumber.replace(/\D/g, '');
        
        // Format as 078 123 4567 for Rwandan numbers
        if (numbers.length > 0) {
            numbers = numbers.match(/(\d{0,3})(\d{0,3})(\d{0,4})/);
            this.phoneNumber = !numbers[2] ? numbers[1] : numbers[1] + ' ' + numbers[2] + (numbers[3] ? ' ' + numbers[3] : '');
        }
    },
    
    async processPayment() {
        if (!this.phoneNumber) {
            this.error = 'Please enter your phone number';
            return;
        }
        
        // Remove any formatting from the phone number
        const rawPhoneNumber = '250' + this.phoneNumber.replace(/\D/g, '').substring(1);
        
        this.isLoading = true;
        this.error = null;
        
        try {
            // Call the payment initiation endpoint
            const response = await fetch('{{ route("payment.initiate") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json',
                },
                body: JSON.stringify({
                    plan_id: this.planId,
                    phone_number: rawPhoneNumber,
                    amount: this.amount,
                    currency: this.currency
                })
            });
            
            const data = await response.json();
            
            if (!response.ok) {
                throw new Error(data.message || 'Payment failed to initiate');
            }
            
            // Show success state
            this.paymentStatus = 'processing';
            this.paymentReference = data.payment_reference;
            
            // Start polling for payment status
            this.checkPaymentStatus();
            
        } catch (err) {
            console.error('Payment error:', err);
            this.error = err.message || 'An error occurred. Please try again.';
            this.isLoading = false;
        }
    },
    
    async checkPaymentStatus() {
        if (!this.paymentReference) return;
        
        try {
            const response = await fetch(`/api/payments/status/${this.paymentReference}`);
            const data = await response.json();
            
            if (data.status === 'success') {
                this.paymentStatus = 'success';
                this.success = true;
                
                // Redirect after a short delay
                setTimeout(() => {
                    window.location.href = '{{ route("dashboard", app()->getLocale()) }}';
                }, 3000);
                
            } else if (data.status === 'failed') {
                this.paymentStatus = 'failed';
                this.error = 'Payment failed. Please try again.';
                this.isLoading = false;
                
            } else {
                // Continue polling
                setTimeout(() => this.checkPaymentStatus(), 3000);
            }
            
        } catch (err) {
            console.error('Status check error:', err);
            // Continue polling even if there's an error
            setTimeout(() => this.checkPaymentStatus(), 3000);
        }
    }
}" 
     x-show="showModal" 
     x-transition:enter="ease-out duration-300"
     x-transition:enter-start="opacity-0"
     x-transition:enter-end="opacity-100"
     x-transition:leave="ease-in duration-200"
     x-transition:leave-start="opacity-100"
     x-transition:leave-end="opacity-0"
     class="fixed inset-0 z-50 overflow-y-auto" 
     aria-labelledby="modal-title" 
     role="dialog" 
     aria-modal="true"
     style="display: none;"
     x-cloak
     @keydown.escape.window="closeModal()"
     @open-payment-modal.window="
        planId = $event.detail.planId;
        planName = $event.detail.planName;
        planDescription = $event.detail.planDescription || '';
        planFeatures = $event.detail.planFeatures || [];
        amount = $event.detail.amount;
        currency = $event.detail.currency || 'RWF';
        showModal = true;
        resetForm();
     ">
    <!-- Background overlay with click to close -->
    <div x-show="showModal" x-cloak 
         x-transition:enter="ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-50 flex items-center justify-center p-0 sm:p-6 overflow-x-hidden" 
         style="overscroll-behavior: contain;"
         aria-hidden="true"
         @click.self="closeModal()">
        
        <!-- Modal panel -->
        <div class="w-full max-w-2xl bg-white rounded-none sm:rounded-lg shadow-xl overflow-y-auto overflow-x-hidden flex flex-col max-h-screen sm:max-h-[90vh] transform transition-all sm:w-full"
             @click.stop
             x-show="showModal"
             x-transition:enter="ease-out duration-300"
             x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
             x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
             x-transition:leave="ease-in duration-200"
             x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
             x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95">
            <!-- Header with theme gradient background -->
            <div class="bg-gradient-to-r from-blue-900 to-blue-800 px-6 py-5 sm:px-8 sm:py-6">
                <div class="flex items-center justify-between">
                    <div>
                        <h2 class="text-2xl font-bold text-white" id="modal-title">
                            <span x-text="planName"></span>
                        </h2>
                        <p class="mt-1 text-blue-100" x-text="planDescription"></p>
                    </div>
                    <button type="button" 
                            class="text-blue-200 hover:text-white focus:outline-none"
                            @click="closeModal()"
                            :disabled="isLoading">
                        <span class="sr-only">Close</span>
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
                
                <!-- Plan Price -->
                <div class="mt-6">
                    <div class="flex items-baseline">
                        <span class="text-4xl font-extrabold text-white" x-text="currency + ' ' + amount.toLocaleString()"></span>
                    </div>
                </div>
            </div>
            
            <!-- Main Content -->
            <div class="px-6 py-6 sm:px-8 sm:py-8 overflow-y-auto flex-1">
                <!-- Plan Features - Only show if we have features -->
                <template x-if="planFeatures && ((Array.isArray(planFeatures) && planFeatures.length > 0) || (!Array.isArray(planFeatures) && planFeatures))">
                    <div class="mb-8">
                        @php
                            // Get current locale
                            $currentLocale = app()->getLocale();
                            $fallbackLocale = config('app.fallback_locale', 'en');
                            
                            // Check if the translation exists in the current locale
                            $translations = [
                                'en' => 'What\'s Included',
                                'rw' => 'Ibirimo', // Kinyarwanda translation for 'What's Included'
                            ];
                            
                            $featuresTitle = $translations[$currentLocale] ?? $translations[$fallbackLocale] ?? 'Features';
                        @endphp
                        <h3 class="text-lg font-medium text-gray-900 mb-4">
                            {{ $featuresTitle }}
                        </h3>
                        <ul class="space-y-3">
                            <template x-if="Array.isArray(planFeatures) && planFeatures.length > 0">
                                <template x-for="(feature, index) in planFeatures" :key="index">
                                    <li class="flex items-start">
                                        <svg class="h-5 w-5 text-green-500 mr-2 mt-0.5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                        </svg>
                                        <span class="text-gray-700" x-text="feature"></span>
                                    </li>
                                </template>
                            </template>
                            <template x-if="!Array.isArray(planFeatures) && planFeatures">
                                <li class="flex items-start">
                                    <svg class="h-5 w-5 text-green-500 mr-2 mt-0.5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                    </svg>
                                    <span class="text-gray-700" x-text="planFeatures"></span>
                                </li>
                            </template>
                        </ul>
                    </div>
                </template>
                
                <!-- Payment Form -->
                <div x-show="paymentStatus === 'idle' || paymentStatus === 'failed'" x-transition>
                    <div class="border-t border-gray-200 pt-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">{{ __('home.subscriptionPlans.paymentModal.title') }}</h3>
                        
                        <!-- Error message -->
                        <div x-show="error" 
                             x-text="error" 
                             class="mb-4 p-3 bg-red-50 text-red-700 rounded-md text-sm">
                        </div>
                        
                        <!-- Phone Number Input -->
                        <div class="mb-6">
                            <label for="phone-number" class="block text-sm font-medium text-gray-700 mb-2">
                                MTN Mobile Money Number
                            </label>
                            <div class="mt-1 relative rounded-md shadow-sm">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <span class="text-gray-500 sm:text-sm">+250</span>
                                </div>
                                <input type="tel" 
                                       id="phone-number"
                                       x-model="phoneNumber"
                                       @input="formatPhoneNumber()"
                                       class="focus:ring-blue-500 focus:border-blue-500 block w-full pl-14 pr-12 sm:text-sm border-gray-300 rounded-md p-3 border"
                                       placeholder="78 123 4567"
                                       :disabled="isLoading"
                                       :class="{'bg-gray-50': isLoading}">
                            </div>
                            <p class="mt-2 text-sm text-gray-500">
                                We'll send a payment request to this number. Standard carrier charges may apply.
                            </p>
                        </div>
                        
                        <!-- Price Display -->
                        <div class="text-center mb-6">
                            <p class="text-2xl font-bold text-gray-900" x-text="'RWF ' + amount.toLocaleString()"></p>
                        </div>
                        
                        <!-- Payment Button -->
                        <button type="button"
                                @click="processPayment()"
                                :disabled="isLoading"
                                class="w-full flex justify-center items-center py-3 px-6 border border-transparent rounded-md shadow-sm text-base font-medium text-white bg-orange-500 hover:bg-orange-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-orange-500 disabled:opacity-50 disabled:cursor-not-allowed transition-colors duration-200">
                            <span x-text="isLoading ? '{{ __('home.subscriptionPlans.processing') }}' : '{{ __('home.subscriptionPlans.purchase') }}'"></span>
                            <svg x-show="isLoading" class="animate-spin ml-2 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                        </button>
                    </div>
                </div>
                
                <!-- Processing State -->
                <div x-show="paymentStatus === 'processing'" x-transition class="py-12 text-center">
                    <div class="animate-spin rounded-full h-16 w-16 border-4 border-orange-500 border-t-transparent mx-auto mb-6"></div>
                    <h3 class="text-xl font-medium text-gray-900 mb-2">{{ __('home.subscriptionPlans.paymentModal.processing.title') }}</h3>
                    <p class="text-gray-600 max-w-md mx-auto">
                        {{ __('home.subscriptionPlans.paymentModal.processing.message') }}
                    </p>
                    <p class="mt-4 text-sm text-gray-500">
                        This may take a few moments. Please don't close this window.
                    </p>
                    <button type="button"
                            @click="closeModal()"
                            class="mt-6 inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-orange-500">
                        Cancel Payment
                    </button>
                </div>
                
                <!-- Success State -->
                <div x-show="paymentStatus === 'success'" x-transition class="py-12 text-center">
                    <div class="mx-auto flex items-center justify-center h-20 w-20 rounded-full bg-green-50 border-2 border-green-100 mb-6">
                        <svg class="h-12 w-12 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-900 mb-3">Payment Successful!</h3>
                    <p class="text-gray-600 text-lg mb-1">
                        Welcome to <span class="font-medium" x-text="planName"></span>!
                    </p>
                    <p class="text-gray-600 mb-8">
                        Your subscription is now active.
                    </p>
                    <div class="bg-green-50 border border-green-200 rounded-lg p-4 max-w-md mx-auto mb-8">
                        <p class="text-sm text-green-700">
                            We've sent a confirmation to your email. You can now access all premium features.
                        </p>
                    </div>
                    <p class="text-sm text-gray-500">
                        Redirecting you to your dashboard...
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    [x-cloak] { display: none !important; }
</style>
