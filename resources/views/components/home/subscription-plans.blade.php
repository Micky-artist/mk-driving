@php
    // Helper functions to determine plan styling based on slug
    $getPlanType = function ($plan) {
        // Get the slug from array or object
        $slug = is_array($plan) 
            ? ($plan['slug'] ?? null)
            : (is_object($plan) ? $plan->slug : null);

        if (empty($slug)) {
            return 'basic';
        }

        $slug = strtolower(trim($slug));

        // Match the plan type based on slug
        if (str_contains($slug, 'gold-unlimited') || $slug === 'gold-unlimited-plan') {
            return 'gold-unlimited';
        }
        if (str_contains($slug, 'premium') || $slug === 'premium-plan') {
            return 'premium';
        }
        if (str_contains($slug, 'standard') || $slug === 'standard-plan') {
            return 'standard';
        }
        if (str_contains($slug, 'basic') || $slug === 'basic-plan') {
            return 'basic';
        }
        
        // Default fallback
        return 'basic';
    };

    // Enhanced styling functions for different plan types with more vibrant colors
    $getGradientClass = function ($planType) {
        return match ($planType) {
            'gold-unlimited' => 'from-amber-400 to-yellow-600',  // More vibrant gold gradient
            'premium' => 'from-blue-500 to-indigo-700',
            'standard' => 'from-blue-400 to-blue-600',
            default => 'from-blue-300 to-blue-500',
        };
    };

    $getTextClass = function ($planType) {
        return match ($planType) {
            'gold-unlimited' => 'text-yellow-800 dark:text-yellow-100',
            'premium' => 'text-blue-900 dark:text-blue-100',
            'standard' => 'text-blue-800 dark:text-blue-100',
            default => 'text-blue-700 dark:text-blue-200',
        };
    };

    $getBadgeClass = function ($planType) {
        return match ($planType) {
            'gold-unlimited' => 'from-amber-400 to-yellow-600 shadow-lg shadow-yellow-500/20',
            'premium' => 'from-blue-500 to-indigo-700 shadow-lg shadow-blue-500/20',
            'standard' => 'from-blue-400 to-blue-600 shadow-lg shadow-blue-400/20',
            default => 'from-blue-300 to-blue-500 shadow-lg shadow-blue-300/20',
        };
    };

    $getCardBgClass = function ($planType) {
        return match ($planType) {
            'gold-unlimited' => 'bg-gradient-to-br from-yellow-50 to-amber-100 dark:from-yellow-900/40 dark:to-amber-900/30 border-2 border-yellow-100 dark:border-yellow-700/60 shadow-lg shadow-yellow-100/30 dark:shadow-yellow-900/10',
            'premium' => 'bg-gradient-to-br from-blue-50 to-indigo-100 dark:from-blue-900/40 dark:to-indigo-900/30 border-2 border-blue-100 dark:border-blue-700/60 shadow-lg shadow-blue-100/30 dark:shadow-blue-900/10',
            'standard' => 'bg-gradient-to-br from-blue-50 to-blue-100 dark:from-blue-900/40 dark:to-blue-800/30 border-2 border-blue-100 dark:border-blue-700/50 shadow-lg shadow-blue-100/20 dark:shadow-blue-900/10',
            default => 'bg-gradient-to-br from-gray-50 to-gray-100 dark:from-gray-800/40 dark:to-gray-900/30 border-2 border-gray-100 dark:border-gray-600/50 shadow-lg shadow-gray-100/20 dark:shadow-gray-900/10',
        };
    };

    // Button styling functions
    $getButtonGlow = function($planType) {
        return match($planType) {
            'gold-unlimited' => 'from-yellow-400 to-amber-500',
            'premium' => 'from-blue-500 to-indigo-600',
            'standard' => 'from-blue-400 to-blue-600',
            default => 'from-blue-300 to-blue-500',
        };
    };

    $getButtonGradient = function($planType) {
        return match($planType) {
            'gold-unlimited' => 'from-yellow-600 to-amber-600 hover:from-amber-500 hover:to-yellow-600',
            'premium' => 'from-blue-600 to-indigo-700 hover:from-blue-700 hover:to-indigo-800',
            'standard' => 'from-blue-500 to-blue-700 hover:from-blue-600 hover:to-blue-800',
            default => 'from-blue-400 to-blue-600 hover:from-blue-500 hover:to-blue-700',
        };
    };

    // Process features array
    $processFeatures = function ($features) {
        // If features is already an array, return it directly
        if (is_array($features)) {
            // If it's an associative array with language codes, get the current locale or fallback
        if (isset($features[app()->getLocale()])) {
            $features = (array) $features[app()->getLocale()];
        } elseif (isset($features[config('app.fallback_locale', 'en')])) {
            $features = (array) $features[config('app.fallback_locale', 'en')];
        }

        // Ensure all features are strings
        return array_map(
            'strval',
            array_filter($features, function ($item) {
                return is_string($item) || is_numeric($item);
            }),
        );
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
        return (array) $features[app()->getLocale()];
    }
    // Fallback to English if current locale not found
    if (isset($features['en'])) {
        return (array) $features['en'];
    }

    // If it's a simple array, return as is
        if (is_array($features)) {
            // Handle case where features might be a sequential array of strings
            if (array_values($features) === $features) {
                return array_values(
                    array_filter($features, function ($item) {
                        return !empty(trim((string) $item));
                    }),
                );
            }
            // If it's an associative array but not with locale keys, return values
            return array_values(array_filter($features));
        }

        return [];
    };
@endphp

<div class="relative py-4 bg-gradient-to-br from-blue-50 to-indigo-50 dark:from-gray-900 dark:to-gray-800">
    <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Premium billboard header -->
<div class="text-center mb-8 fade-in">
    <div class="inline-block bg-gradient-to-r from-blue-600 to-blue-700 dark:from-blue-800 dark:to-blue-900 rounded-xl px-8 py-6 shadow-lg">
        <h2 class="text-3xl sm:text-4xl font-bold text-white mb-2">
            {{ __('home.subscriptionPlans.title') }}
        </h2>
        <div class="w-16 h-1 bg-blue-400/50 mx-auto my-3 rounded-full"></div>
        <p class="text-blue-100 dark:text-blue-200 max-w-2xl mx-auto text-sm sm:text-base">
            {{ __('home.subscriptionPlans.subtitle') }}
        </p>
    </div>
</div>
        <div class="mt-4">

        <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-6">
        @foreach ($plans ?? [] as $plan)
            @php
                $planType = $getPlanType($plan);
                $isPopular = $planType === 'premium';
                // Use display_features which is already processed for the current locale
                $features = is_array($plan['display_features'] ?? null) ? $plan['display_features'] : [];
                // Check if current plan (call the closure if it's a closure)
                $isCurrentPlan = is_callable($plan['is_current'] ?? null) ? $plan['is_current']() : ($plan['is_current'] ?? false);
            @endphp

            <div
                class="group relative pt-6 h-full rounded-2xl overflow-visible shadow-lg hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-2 {{ $getCardBgClass($planType) }} fade-in"
                style="animation-delay: {{ $loop->index * 0.1 }}s;">
                @if ($isPopular)
                    <div
                        class="absolute -top-4 left-1/2 -translate-x-1/2 bg-gradient-to-r {{ $getBadgeClass($planType) }} text-white text-sm font-extrabold px-6 py-1.5 rounded-full shadow-lg z-50 whitespace-nowrap transform group-hover:scale-105 transition-transform duration-300">
                        {{ __('home.subscriptionPlans.mostPopular') }}
                    </div>
                @endif

                <div class="h-full flex flex-col">
                    <div class="p-4 pb-2 fade-in delay-100">
                        <div class="mb-2">
                            <h3 class="text-2xl font-extrabold {{ $getTextClass($planType) }} mb-1">
                                {{ $plan['display_name'] }}
                            </h3>
                            <div class="h-1 w-12 rounded-full bg-gradient-to-r {{ $getGradientClass($planType) }} mb-2"></div>
                        </div>
                        <p class="text-4xl font-black {{ $getTextClass($planType) }} mb-1">
                            {{ __('home.subscriptionPlans.billing.currency', ['amount' => number_format($plan['price'])]) }}
                        </p>
                        <p class="text-base font-normal text-gray-600 dark:text-gray-300 mb-4">
                            @php
                                $duration = $plan['duration'] ?? 0;
                                $durationType = $plan['duration_type'] ?? 'day';
                            @endphp

                            @if ($duration <= 0)
                                {{ __('home.subscriptionPlans.billing.duration.no_limit') }}
                            @else
                                @if ($durationType === 'hour')
                                    {{ trans_choice('home.subscriptionPlans.billing.duration.hour', $duration, ['count' => $duration]) }}
                                @elseif($durationType === 'day')
                                    {{ trans_choice('home.subscriptionPlans.billing.duration.day', $duration, ['count' => $duration]) }}
                                @elseif($durationType === 'month')
                                    {{ trans_choice('home.subscriptionPlans.billing.duration.month', $duration, ['count' => $duration]) }}
                                @elseif($durationType === 'year')
                                    {{ trans_choice('home.subscriptionPlans.billing.duration.year', $duration, ['count' => $duration]) }}
                                @endif

                                @if (($plan['billing_cycle'] ?? '') === 'recurring')
                                    <span
                                        class="block text-sm mt-1">{{ __('home.subscriptionPlans.billing.monthly', ['period' => trans_choice('home.subscriptionPlans.billing.duration.month', 1, ['count' => 1])]) }}</span>
                                @endif
                            @endif
                        </p>
                        <p class="text-gray-600 mb-6">
                            {{ $plan['display_description'] ?? ($plan['description']['en'] ?? '') }}</p>
                    </div>

                    <div class="p-6 pt-0 flex-grow">
                        <ul class="space-y-3 mb-6">
                            @forelse($features as $feature)
                                <li class="flex items-start">
                                    <svg class="h-5 w-5 {{ $getTextClass($planType) }} mt-0.5 mr-2 flex-shrink-0"
                                        fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M5 13l4 4L19 7" />
                                    </svg>
                                    <span class="break-words text-gray-700 dark:text-gray-300">{{ $feature }}</span>
                                </li>
                            @empty
                                <li class="text-gray-500 dark:text-gray-400 text-sm">{{ __('home.subscriptionPlans.noFeatures') }}</li>
                            @endforelse
                        </ul>
                    </div>

                    <div class="p-6 pt-0 mt-auto fade-in delay-300">
                        @if ($isCurrentPlan)
                            <div class="relative group">
                                <div class="absolute -inset-0.5 bg-gradient-to-r from-gray-400 to-gray-300 dark:from-gray-600 dark:to-gray-700 rounded-lg opacity-70 group-hover:opacity-100 blur transition duration-1000 group-hover:duration-300"></div>
                                <button
                                    class="relative w-full text-center bg-gray-300 dark:bg-gray-700 text-gray-800 dark:text-gray-200 font-semibold py-3 px-6 rounded-lg border-2 border-white/20 shadow-md transition-all duration-300 transform hover:scale-[1.02]"
                                    disabled>
                                    <span class="flex items-center justify-center">
                                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                        </svg>
                                        {{ __('home.subscriptionPlans.currentPlan') }}
                                    </span>
                                </button>
                            </div>
                        @else
                            <div class="w-full">
                                @auth
                                    @php
                                        $planName = addslashes($plan['display_name'] ?? ($plan['name']['en'] ?? 'Unnamed Plan'));
                                        $price = number_format($plan['price']);
                                        $duration = $plan['duration'] ?? 1;
                                        $durationText = '';
                                        
                                        if (isset($plan['duration']) && $plan['duration'] > 1) {
                                            $durationText = "/ {$plan['duration']} " . __('home.subscriptionPlans.months');
                                        } elseif (isset($plan['duration_in_days']) && $plan['duration_in_days'] > 0) {
                                            $durationText = "/ {$plan['duration_in_days']} " . __('home.subscriptionPlans.days');
                                        }
                                    @endphp
                                    <div class="relative group">
                                        <div class="absolute -inset-0.5 bg-gradient-to-r {{ $getButtonGlow($planType) }} rounded-lg opacity-70 group-hover:opacity-100 blur transition duration-1000 group-hover:duration-300"></div>
                                        <button 
                                            x-data="{}"
                                            @click="$dispatch('open-payment-modal', { 
                                                planId: '{{ $plan['id'] }}',
                                                planName: '{{ $planName }}',
                                                price: '{{ $price }} RWF{{ $durationText }}',
                                                amount: {{ $plan['price'] }},
                                                currency: 'RWF',
                                                duration: {{ $duration }}
                                            })"
                                            class="relative w-full text-center bg-gradient-to-r {{ $getButtonGradient($planType) }} text-white font-semibold py-3 px-6 rounded-lg border-2 border-white/20 shadow-lg transform transition-all duration-300 hover:scale-[1.02]"
                                        >
                                            <span class="flex items-center justify-center">
                                                <svg class="w-5 h-5 mr-2 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                                                </svg>
                                                {{ __('home.subscriptionPlans.subscribe') }}
                                                <svg class="w-4 h-4 ml-2 -mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                                                </svg>
                                            </span>
                                        </button>
                                    </div>
                                @else
                                    <div class="relative group">
                                        <div class="absolute -inset-0.5 bg-gradient-to-r {{ $getButtonGlow($planType) }} rounded-lg opacity-70 group-hover:opacity-100 blur transition duration-1000 group-hover:duration-300"></div>
                                        <a href="{{ route('login') }}" 
                                           onclick="localStorage.setItem('intended_url', window.location.href);"
                                           class="relative block w-full text-center bg-gradient-to-r {{ $getButtonGradient($planType) }} text-white font-semibold py-3 px-6 rounded-lg border-2 border-white/20 shadow-lg transform transition-all duration-300 hover:scale-[1.02]">
                                            <span class="flex items-center justify-center">
                                                <svg class="w-5 h-5 mr-2 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                                                </svg>
                                                {{ __('home.subscriptionPlans.subscribe') }}
                                                <svg class="w-4 h-4 ml-2 -mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                                                </svg>
                                            </span>
                                        </a>
                                    </div>
                                @endauth
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        @endforeach
            <!-- Closing div for cards grid -->
        </div>
        
        <!-- Help section -->
        <div class="mt-12 text-center fade-in delay-400">
            <p class="text-gray-600 dark:text-gray-300 mb-3">{{ __('home.subscriptionPlans.needHelp') }}</p>
            <div class="flex items-center justify-center space-x-6">
                <a href="mailto:mkscholars250@gmail.com" class="inline-flex items-center text-blue-600 dark:text-blue-400 font-medium hover:text-blue-800 dark:hover:text-blue-300 transition-colors duration-200 group">
                    <span>{{ __('home.subscriptionPlans.contact_us') }}</span>
                    <svg class="w-4 h-4 ml-1 transform group-hover:translate-x-1 transition-transform duration-200" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                    </svg>
                </a>
                <span class="text-gray-400">|</span>
                <a href="https://wa.me/250798611161" target="_blank" rel="noopener noreferrer" class="inline-flex items-center text-green-600 dark:text-green-400 font-medium hover:text-green-700 dark:hover:text-green-300 transition-colors duration-200 group">
                    <i class="fab fa-whatsapp text-xl mr-2"></i>
                    <span>WhatsApp</span>
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Payment Modal -->
<div x-data="{
    showModal: false,
    planId: null,
    planName: '',
    amount: 0,
    currency: 'RWF',
    isLoading: false,
    error: null,
    success: false,
    phoneNumber: '',
    paymentStatus: 'idle',
    momoPhoneNumber: '{{ config('payment.momo_phone_number') }}',

    init() {
        this.$watch('showModal', value => {
            if (value) {
                document.body.classList.add('overflow-hidden');
            } else {
                document.body.classList.remove('overflow-hidden');
            }
        });
    },

    get momoCode() {
        // Format: *182*8*1*{phone}*{amount}#
        const amount = Math.floor(parseFloat(this.amount));
        return `*182*8*1*${this.momoPhoneNumber}*${amount}#`;
    },

    async submitPayment() {
        // First, validate the phone number
        const formattedPhone = this.formatPhoneNumber(this.phoneNumber);
        
        if (!formattedPhone) {
            this.error = 'Please enter a valid MTN Mobile Money number (e.g., 72xxxxxxx, 73xxxxxxx, 78xxxxxxx, or 79xxxxxxx)';
            return;
        }
        
        // Clear any previous errors
        this.error = null;
        this.isLoading = true;
        
        try {
            const response = await fetch('{{ route('payments.request') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify({
                    plan_id: this.planId,
                    phone_number: formattedPhone,
                    amount: this.amount,
                    currency: this.currency
                })
            });
            
            const data = await response.json();
            
            if (!response.ok) {
                // Handle validation errors from the server
                if (data.errors) {
                    // Join all error messages
                    this.error = Object.values(data.errors).flat().join(' ');
                } else {
                    this.error = data.message || 'Failed to process payment request';
                }
                return;
            }
            
            // If we get here, the request was successful
            this.paymentStatus = 'requested';
            this.success = true;
            
            // Close the modal after 3 seconds
            setTimeout(() => {
                this.closeModal();
            }, 3000);
            
        } catch (error) {
            console.error('Payment error:', error);
            this.error = 'An error occurred. Please try again.';
        } finally {
            this.isLoading = false;
        }
    },

    formatPhoneNumber(phone) {
        // Remove all non-digit characters
        let cleaned = ('' + phone).replace(/\D/g, '');
        
        // Check if it's a 9-digit number starting with 72, 73, 78, or 79
        if (cleaned.length === 9 && /^7[2389]\d{7}$/.test(cleaned)) {
            return '250' + cleaned; // Convert to 12-digit format
        }
        
        // Check if it's a 12-digit number starting with 25072, 25073, 25078, or 25079
        if (cleaned.length === 12 && /^2507[2389]\d{7}$/.test(cleaned)) {
            return cleaned; // Already in correct format
        }
        
        // If we get here, the number format is not recognized
        return null;

        // If it's 12 digits and starts with 250, it's already in the right format
        if (cleaned.length === 12 && cleaned.startsWith('250')) {
            return cleaned;
        }

        return null;
    },

    copyToClipboard(text) {
        navigator.clipboard.writeText(text).then(() => {
            // Show copied message
            const copyBtn = document.getElementById('copy-momo-code');
            if (copyBtn) {
                const originalText = copyBtn.innerHTML;
                copyBtn.innerHTML = '{{ __('Copied!') }}';
                copyBtn.classList.remove('text-blue-600', 'hover:text-blue-500');
                copyBtn.classList.add('text-green-600');

                setTimeout(() => {
                    copyBtn.innerHTML = originalText;
                    copyBtn.classList.remove('text-green-600');
                    copyBtn.classList.add('text-blue-600', 'hover:text-blue-500');
                }, 2000);
            }
        }).catch(err => {
            console.error('Failed to copy text: ', err);
        });
    },

    closeModal() {
        this.showModal = false;
        document.body.classList.remove('overflow-hidden');
        this.resetForm();
    },
    
    openModal() {
        document.body.classList.add('overflow-hidden');
        this.showModal = true;
    },

    resetForm() {
        this.planId = null;
        this.planName = '';
        this.amount = 0;
        this.currency = 'RWF';
        this.isLoading = false;
        this.error = null;
        this.success = false;
        this.phoneNumber = '';
        this.paymentStatus = 'idle';
    }
}" x-show="showModal" x-cloak x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200"
        x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
        class="fixed inset-0 z-[99999] flex items-start md:items-center justify-center p-4 overflow-y-auto bg-black/50 backdrop-blur-sm" style="position: fixed !important;"
        aria-labelledby="modal-title" role="dialog" aria-modal="true" style="display: none;" x-cloak
    @keydown.escape.window="closeModal()"
    @open-payment-modal.window="
    planId = $event.detail.planId;
    planName = $event.detail.planName;
    amount = $event.detail.amount;
    currency = $event.detail.currency || 'RWF';
    openModal()
"
    @click.self="closeModal()">

        <!-- Modal panel -->
        <div class="w-full max-w-2xl bg-white dark:bg-gray-800 rounded-xl shadow-xl overflow-y-auto overflow-x-hidden flex flex-col max-h-screen sm:max-h-[90vh] transform transition-all sm:w-full mx-auto my-auto relative z-[99999]" style="position: relative !important;"
            @click.stop x-show="showModal" x-transition:enter="ease-out duration-300"
            x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
            x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" x-transition:leave="ease-in duration-200"
            x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
            x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95">

            <!-- Header -->
            <div class="bg-gradient-to-r from-blue-600 to-blue-700 px-4 py-2 sm:px-6 sm:py-3">
                <div class="flex items-start justify-between space-x-2">
                    <div class="flex-1 min-w-0">
                        <h2 class="text-lg sm:text-2xl font-bold text-white truncate" id="modal-title" x-text="planName">
                        </h2>
                        <p class="mt-0.5 text-sm sm:text-base text-blue-100 whitespace-normal break-words"
                            x-text="'{{ __('payment.pay_amount') }}: ' + currency + ' ' + amount.toLocaleString() + ' {{ __('payment.to_subscribe') }}'"></p>
                    </div>
                    <button type="button" class="text-blue-200 hover:text-white focus:outline-none"
                        @click="closeModal()" :disabled="isLoading">
                        <span class="sr-only">Close</span>
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            </div>

           <!-- Main Content -->
<div class="px-4 py-3 sm:px-6 sm:py-4 overflow-y-auto flex-1">
    <!-- Payment Instructions -->
    <div x-show="paymentStatus !== 'requested'" class="space-y-4 sm:space-y-6">
        <!-- Payment Code with Payee -->
        <div class="bg-gradient-to-r from-gray-50 to-gray-100 dark:from-gray-700 dark:to-gray-800 p-5 rounded-xl border border-gray-200 dark:border-gray-700 shadow-md transition-all duration-300 hover:shadow-lg">
            <div class="flex flex-col space-y-3">
                <div class="flex flex-col sm:flex-row justify-between items-start gap-2">
                    <div class="flex flex-col sm:flex-row sm:items-center gap-2">
                        <span class="text-xs sm:text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('payment.amount') }}</span>
                        <span class="text-md sm:text-lg font-bold text-gray-900 dark:text-white" x-text="'RWF ' + new Intl.NumberFormat().format(amount)"></span>
                    </div>
                    <span class="text-base sm:text-lg font-bold text-blue-600 dark:text-blue-400">MS Innovation Lab Ltd</span>
                </div>
                <div class="pt-3 border-t border-gray-200 dark:border-gray-700" x-data="{ copied: false }">
    <p class="text-xs sm:text-sm font-medium text-gray-500 dark:text-gray-400 mb-2">{{ __('payment.instructions') }}:</p>
    <div class="bg-white dark:bg-gray-800 p-2 sm:p-3 rounded-lg border border-gray-200 dark:border-gray-700 overflow-x-auto">
        <p class="font-mono text-sm sm:text-base font-bold text-center text-gray-900 dark:text-white" x-text="momoCode"></p>
    </div>
    <button type="button" 
        @click="
            copyToClipboard(momoCode);
            copied = true;
            setTimeout(() => { copied = false; }, 2000);
        " 
        class="mt-3 w-full flex items-center justify-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors duration-200 relative overflow-hidden">
        <svg x-show="!copied" class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z" />
        </svg>
        <span x-show="!copied">{{ __('payment.copy_code') }}</span>
        <span x-show="copied" class="flex items-center">
            <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
            </svg>
            {{ __('payment.copied') }}
        </span>
    </button>

    <!-- Toast notification -->
    <div x-show="copied" 
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 translate-y-2"
         x-transition:enter-end="opacity-100 translate-y-0"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100 translate-y-0"
         x-transition:leave-end="opacity-0 translate-y-2"
         class="fixed bottom-4 right-4 bg-green-500 text-white px-4 py-2 rounded-lg shadow-lg flex items-center space-x-2">
        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
        </svg>
        <span>{{ __('payment.copied_to_clipboard') }}</span>
    </div>
</div>
            </div>
        </div>

        <!-- Simple Instructions -->
        <div class="bg-white dark:bg-gray-800 rounded-xl p-4 sm:p-5 shadow-sm border border-gray-200 dark:border-gray-700">
            <div class="space-y-2 sm:space-y-3">
                <div class="flex flex-nowrap items-start gap-2 sm:gap-3">
                    <div class="flex-shrink-0 mt-0.5">
                        <div class="flex items-center justify-center h-5 w-5 sm:h-6 sm:w-6 rounded-full bg-blue-100 dark:bg-blue-900/50 text-blue-600 dark:text-blue-400">
                            <svg class="h-3 w-3 sm:h-4 sm:w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                    </div>
                    <div class="min-w-0">
                        <p class="text-xs sm:text-sm text-gray-600 dark:text-gray-300 leading-relaxed break-words">
                            <span>{{ __('payment.press_or_dial') }}</span>
                            <span class="font-mono font-bold break-all" x-text="momoCode"></span>
                            <span>{{ __('payment.and_follow_instructions') }}</span>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Footer -->
<div class="bg-white dark:bg-gray-800 px-4 py-3 sm:px-6 border-t border-gray-200 dark:border-gray-700">
    <div class="bg-gradient-to-r from-green-500 to-green-600 rounded-xl p-3 sm:p-4 shadow-lg">
        <div class="flex flex-col sm:flex-row items-center justify-between space-y-2 sm:space-y-0 sm:space-x-4">
            <div class="flex-1 text-center sm:text-left mb-2 sm:mb-0">
                <h3 class="text-sm sm:text-base font-bold text-white">
                    {{ __('payment.whatsapp_contact') }}
                </h3>
            </div>
            <a href="https://wa.me/250798611161" target="_blank" class="w-full sm:w-auto bg-white hover:bg-gray-100 text-green-600 font-bold rounded-full px-4 py-2 text-sm sm:text-base flex items-center justify-center space-x-2 transition-all duration-200 transform hover:scale-[1.02] hover:shadow">
                <i class="fab fa-whatsapp text-xl"></i>
                <span>+250 798 611 161</span>
            </a>
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
</div>

<style>
    [x-cloak] {
        display: none !important;
    }
</style>
