@php
    // Helper functions to determine plan styling
    $getPlanType = function ($plan) {
        // First try to get the slug directly
        $slug = null;

        if (is_array($plan)) {
            $slug = $plan['slug'] ?? ($plan['id'] ?? null);
        } elseif (is_object($plan)) {
            $slug = $plan->slug ?? ($plan->id ?? null);
        }

        // If we have a slug, use it directly
        if (!empty($slug)) {
            $slug = strtolower(trim($slug));

            // Return the slug as the plan type if it matches our expected values
            if (in_array($slug, ['gold-unlimited', 'premium', 'standard', 'basic'])) {
                return $slug;
            }
        }

        // Fallback to old behavior if no valid slug found
        $planName = is_array($plan)
            ? $plan['display_name'] ??
                (is_array($plan['name'] ?? null) ? $plan['name']['en'] ?? '' : $plan['name'] ?? '')
            : (is_object($plan)
                ? $plan->display_name ?? (is_object($plan->name ?? null) ? $plan->name->en ?? '' : $plan->name ?? '')
                : '');

        $planName = strtolower($planName);

        if (str_contains($planName, 'gold-unlimited')) {
            return 'gold-unlimited';
        }
        if (str_contains($planName, 'premium')) {
            return 'premium';
        }
        if (str_contains($planName, 'standard') || str_contains($planName, 'std')) {
            return 'standard';
        }
        return 'basic';
    };

    $getGradientClass = function ($planType) {
        return match ($planType) {
            'gold-unlimited' => 'from-amber-500 to-amber-600',
            'premium' => 'from-blue-800 to-blue-900',
            'standard' => 'from-blue-600 to-blue-700',
            default => 'from-blue-100 to-blue-200',
        };
    };

    $getTextClass = function ($planType) {
        return match ($planType) {
            'gold-unlimited' => 'text-amber-700 dark:text-amber-300',
            'premium' => 'text-blue-900 dark:text-blue-200',
            'standard' => 'text-blue-800 dark:text-blue-200',
            default => 'text-blue-700 dark:text-blue-300',
        };
    };

    $getBadgeClass = function ($planType) {
        return match ($planType) {
            'gold-unlimited' => 'from-amber-500 to-amber-600',
            'premium' => 'from-blue-800 to-blue-900',
            'standard' => 'from-blue-600 to-blue-700',
            default => 'from-blue-400 to-blue-500',
        };
    };

    $getCardBgClass = function ($planType) {
        return match ($planType) {
            'gold-unlimited' => 'bg-amber-50 dark:bg-amber-900/20',
            'premium' => 'bg-blue-50 dark:bg-blue-900/20',
            'standard' => 'bg-blue-50 dark:bg-blue-800/20',
            default => 'bg-blue-50 dark:bg-blue-700/20',
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

<div class="my-16">
    <div class="text-center mb-10 fade-in">
        <h2 class="text-2xl sm:text-3xl font-bold text-gray-900 dark:text-white mb-4">{{ __('home.subscriptionPlans.title') }}</h2>
        <p class="text-gray-600 dark:text-gray-300 max-w-2xl mx-auto fade-in delay-100">
            {{ __('home.subscriptionPlans.subtitle') }}
        </p>
    </div>

    <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-6 max-w-6xl mx-auto px-4">
        @foreach ($plans ?? [] as $plan)
            @php
                $planType = $getPlanType($plan);
                $isPopular = $planType === 'premium';
                $features = $processFeatures($plan['features'] ?? []);
                $isCurrentPlan = $plan['is_current'] ?? false
            @endphp

            <div
                class="relative pt-8 h-full rounded-xl overflow-visible shadow-md hover:shadow-lg transition-all duration-300 transform hover:-translate-y-1 {{ $getCardBgClass($planType) }} fade-in"
                style="animation-delay: {{ $loop->index * 0.1 }}s;">
                @if ($isPopular)
                    <div
                        class="absolute -top-3 left-1/2 -translate-x-1/2 bg-gradient-to-r {{ $getBadgeClass($planType) }} text-white text-xs font-bold px-4 py-1 rounded-full shadow-lg z-50 whitespace-nowrap">
                        {{ __('home.subscriptionPlans.mostPopular') }}
                    </div>
                @endif

                <div class="h-full flex flex-col">
                    <div class="p-6 pb-0 fade-in delay-100">
                        <h3 class="text-xl font-bold {{ $getTextClass($planType) }} mb-2">
                            {{ $plan['display_name'] ?? ($plan['name']['en'] ?? 'Unnamed Plan') }}</h3>
                        <p class="text-3xl font-bold {{ $getTextClass($planType) }}">
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
                            <button
                                class="w-full text-center bg-gray-400 text-white font-medium py-2 px-4 rounded-lg cursor-not-allowed"
                                disabled>
                                {{ __('home.subscriptionPlans.currentPlan') }}
                            </button>
                        @else
                            <div class="w-full">
                                @auth
                                    <button x-data="{}"
                                        @click="
                                            $dispatch('open-payment-modal', { 
                                                planId: '{{ $plan['id'] }}',
                                                planName: '{{ addslashes($plan['display_name'] ?? ($plan['name']['en'] ?? 'Unnamed Plan')) }}',
                                                amount: {{ $plan['price'] }},
                                                currency: 'RWF',
                                                duration: {{ $plan['duration'] ?? 1 }}
                                            });
                                        "
                                        class="w-full text-center {{ $planType === 'gold' ? 'bg-yellow-500 hover:bg-yellow-600' : 'bg-teal-600 hover:bg-teal-700' }} text-white font-medium py-2 px-4 rounded-lg transition-colors">
                                        <span>{{ __('home.subscriptionPlans.subscribe') }}</span>
                                    </button>
                                @else
                                    <a href="{{ route('login') }}" 
                                       onclick="localStorage.setItem('intended_url', window.location.href);"
                                       class="block w-full text-center {{ $planType === 'gold' ? 'bg-yellow-500 hover:bg-yellow-600' : 'bg-teal-600 hover:bg-teal-700' }} text-white font-medium py-2 px-4 rounded-lg transition-colors">
                                        {{ __('home.subscriptionPlans.subscribe') }}
                                    </a>
                                @endauth
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <div class="mt-10 text-center fade-in delay-400">
        <p class="text-gray-600 dark:text-gray-300 mb-4">{{ __('home.subscriptionPlans.needHelp') }}</p>
        <a href="{{ route('home', ['#subscription-plans']) }}" class="text-teal-600 dark:text-teal-400 font-medium hover:underline transition-colors duration-200 hover:text-teal-700 dark:hover:text-teal-300">
            {{ __('home.subscriptionPlans.contact_us') }}
        </a>
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
    momoPhoneNumber: '{{ env('MOMO_PHONE_NUMBER', '2507XXXXXXXX') }}',

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
        // Format: *182*1*1*{phone}*{amount}#
        const amount = Math.floor(parseFloat(this.amount));
        return `*182*1*1*${this.momoPhoneNumber}*${amount}#`;
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
        this.resetForm();
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
        class="fixed mt-2 inset-0 z-50 flex items-center justify-center p-4 overflow-x-hidden"
    aria-labelledby="modal-title" role="dialog" aria-modal="true" style="display: none;" x-cloak
    @keydown.escape.window="closeModal()"
    @open-payment-modal.window="
    planId = $event.detail.planId;
    planName = $event.detail.planName;
    amount = $event.detail.amount;
    currency = $event.detail.currency || 'RWF';
    showModal = true;
"
    @click.self="closeModal()">

        <!-- Modal panel -->
        <div class="w-full max-w-2xl bg-white dark:bg-gray-800 rounded-none sm:rounded-lg shadow-xl overflow-y-auto overflow-x-hidden flex flex-col max-h-screen sm:max-h-[90vh] transform transition-all sm:w-full mx-auto my-auto"
            @click.stop x-show="showModal" x-transition:enter="ease-out duration-300"
            x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
            x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" x-transition:leave="ease-in duration-200"
            x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
            x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95">

            <!-- Header -->
            <div class="bg-gradient-to-r from-blue-600 to-blue-700 px-6 py-5 sm:px-8 sm:py-6">
                <div class="flex items-center justify-between">
                    <div>
                        <h2 class="text-2xl font-bold text-white" id="modal-title">
                            <span x-text="planName"></span>
                        </h2>
                        <p class="mt-1 text-blue-100"
                            x-text="'Pay ' + currency + ' ' + amount.toLocaleString() + ' to subscribe'"></p>
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
            <div class="px-6 py-6 sm:px-8 sm:py-8 overflow-y-auto flex-1">
                <!-- Payment Instructions -->
                <div x-show="paymentStatus !== 'requested'" class="mb-8">
                    <div class="bg-blue-50 dark:bg-blue-900/30 border-l-4 border-blue-400 dark:border-blue-500 p-4 mb-6">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-blue-400" xmlns="http://www.w3.org/2000/svg"
                                    viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd"
                                        d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z"
                                        clip-rule="evenodd" />
                                </svg>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm text-blue-700">
                                    Please dial the following USSD code to complete your payment:
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- MoMo Code Box -->
                    <div class="bg-gray-50 p-4 rounded-lg border border-gray-200 mb-6">
                        <div class="flex justify-between items-center">
                            <div class="font-mono text-lg" x-text="momoCode"></div>
                            <button type="button" @click="copyToClipboard(momoCode)" id="copy-momo-code"
                                class="ml-4 text-sm font-medium text-blue-600 hover:text-blue-500">
                                Copy
                            </button>
                        </div>
                    </div>

                    <div class="text-sm text-gray-600 mb-6">
                        <p class="mb-2">1. Dial the USSD code above on your phone</p>
                        <p class="mb-2">2. Follow the prompts to complete the payment to <span
                                x-text="momoPhoneNumber"></span></p>
                        <p>3. Enter your phone number below and click "I've Paid"</p>
                    </div>
                </div>

                <!-- Success Message -->
                <div x-show="success" class="mb-6">
                    <div class="rounded-md bg-green-50 p-4">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-green-400" xmlns="http://www.w3.org/2000/svg"
                                    viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd"
                                        d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                        clip-rule="evenodd" />
                                </svg>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm font-medium text-green-800">
                                    Your payment request has been received. We'll verify your payment and activate your
                                    subscription shortly.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Error Message -->
                <div x-show="error" class="mb-6">
                    <div class="rounded-md bg-red-50 p-4">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-red-400" xmlns="http://www.w3.org/2000/svg"
                                    viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd"
                                        d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                                        clip-rule="evenodd" />
                                </svg>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm font-medium text-red-800" x-text="error"></p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Phone Number Input (only show if not submitted) -->
                <div x-show="!success" class="mt-8">
                    <label for="phone" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        {{ __('Your Phone Number') }}
                        <span class="text-red-500">*</span>
                    </label>
                    <div class="mt-1 flex rounded-md shadow-sm">
                        <span
                            class="inline-flex items-center px-3 rounded-l-md border border-r-0 border-gray-300 bg-gray-50 text-gray-500 text-sm">
                            +250
                        </span>
                        <input 
                            type="tel" 
                            id="phone" 
                            x-model="phoneNumber" 
                            @input="error = null" 
                            :disabled="isLoading"
                            class="focus:ring-blue-500 focus:border-blue-500 flex-1 block w-full rounded-none rounded-r-md sm:text-sm border-gray-300 p-2 border"
                            placeholder="72xxxxxxx"
                            :class="{'border-red-300': error, 'border-gray-300': !error}">
                    </div>
                    <p class="mt-1 text-sm text-gray-500">
                        {{ __('Enter your MTN Mobile Money number (e.g., 72xxxxxxx, 73xxxxxxx, 78xxxxxxx, or 79xxxxxxx)') }}
                    </p>
                    <p x-show="error" class="mt-2 text-sm text-red-600" x-text="error"></p>
                </div>
                <p class="mt-1 text-sm text-gray-500">Enter the phone number you used to make the payment</p>
            </div>

            <!-- Footer -->
            <div class="bg-gray-50 dark:bg-gray-800 px-6 py-4 sm:px-8 sm:py-5 border-t border-gray-200 dark:border-gray-700">
                <div class="flex justify-end space-x-3">
                    <button type="button" @click="closeModal()" :disabled="isLoading"
                        class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 disabled:opacity-50">
                        Cancel
                    </button>
                    <button type="button" @click="submitPayment()" :disabled="isLoading || !phoneNumber"
                        class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 disabled:opacity-50">
                        <span x-show="!isLoading">I've Paid</span>
                        <span x-show="isLoading">Processing...</span>
                    </button>
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
