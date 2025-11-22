@props(['plan', 'show' => false])

<div x-data="{
    show: @js($show),
    paymentProof: null,
    paymentProofPreview: null,
    notes: '',
    isLoading: false,
    error: null,
    success: false,
    
    init() {
        this.$watch('show', value => {
            if (value) {
                document.body.classList.add('overflow-hidden');
            } else {
                document.body.classList.remove('overflow-hidden');
            }
        });
    },
    
    handleFileChange(event) {
        const file = event.target.files[0];
        if (!file) return;
        
        // Validate file type
        if (!file.type.startsWith('image/')) {
            this.error = 'Please upload an image file';
            return;
        }
        
        // Validate file size (max 5MB)
        if (file.size > 5 * 1024 * 1024) {
            this.error = 'File size must be less than 5MB';
            return;
        }
        
        this.paymentProof = file;
        this.error = null;
        
        // Create preview
        const reader = new FileReader();
        reader.onload = (e) => {
            this.paymentProofPreview = e.target.result;
        };
        reader.readAsDataURL(file);
    },
    
    async submit() {
        if (!this.paymentProof) {
            this.error = 'Please upload payment proof';
            return;
        }
        
        this.isLoading = true;
        this.error = null;
        
        try {
            const formData = new FormData();
            formData.append('payment_proof', this.paymentProof);
            formData.append('notes', this.notes);
            formData.append('plan_id', this.plan.id);
            
            const response = await fetch('{{ route("subscription.submit-proof") }}', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json',
                },
                body: formData,
            });
            
            const data = await response.json();
            
            if (!response.ok) {
                throw new Error(data.message || 'Failed to submit payment proof');
            }
            
            this.success = true;
            
            // Close modal after 2 seconds
            setTimeout(() => {
                this.show = false;
                window.location.reload();
            }, 2000);
            
        } catch (error) {
            this.error = error.message || 'An error occurred';
        } finally {
            this.isLoading = false;
        }
    },
    
    close() {
        this.show = false;
        this.$dispatch('close');
    }
}" 
x-show="show" 
x-transition:enter="ease-out duration-300"
x-transition:enter-start="opacity-0"
x-transition:enter-end="opacity-100"
x-transition:leave="ease-in duration-200"
x-transition:leave-start="opacity-100"
x-transition:leave-end="opacity-0"
class="fixed inset-0 z-50 overflow-y-auto" 
aria-labelledby="modal-title" 
role="dialog" 
aria-modal="true">
    <div class="flex min-h-screen items-end justify-center px-4 pt-4 pb-20 text-center sm:block sm:p-0">
        <div x-show="show" 
            x-transition:enter="ease-out duration-300"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="ease-in duration-200"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" 
            x-on:click="close"
            aria-hidden="true"></div>

        <!-- This element is to trick the browser into centering the modal contents. -->
        <span class="hidden sm:inline-block sm:h-screen sm:align-middle" aria-hidden="true">&#8203;</span>

        <div x-show="show"
            x-transition:enter="ease-out duration-300"
            x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
            x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
            x-transition:leave="ease-in duration-200"
            x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
            x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
            class="inline-block transform overflow-hidden rounded-lg bg-white text-left align-bottom shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg sm:align-middle">
            
            <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                <div class="sm:flex sm:items-start">
                    <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                        <div class="flex justify-between items-center">
                            <h3 class="text-lg font-medium leading-6 text-gray-900" id="modal-title">
                                {{ __('plans.subscribe_to') }}: {{ $plan['name'][app()->getLocale()] ?? $plan['name']['en'] }}
                            </h3>
                            <button type="button" 
                                @click="close"
                                class="rounded-md bg-white text-gray-400 hover:text-gray-500 focus:outline-none">
                                <span class="sr-only">Close</span>
                                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>
                        
                        <div class="mt-4 space-y-4">
                            <div>
                                <p class="text-sm text-gray-500">{{ __('plans.upload_payment_proof') }}</p>
                                <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-md">
                                    <div class="space-y-1 text-center">
                                        <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48" aria-hidden="true">
                                            <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                        </svg>
                                        <div class="flex text-sm text-gray-600">
                                            <label for="payment-proof" class="relative cursor-pointer bg-white rounded-md font-medium text-indigo-600 hover:text-indigo-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-indigo-500">
                                                <span>Upload a file</span>
                                                <input id="payment-proof" name="payment-proof" type="file" class="sr-only" @change="handleFileChange">
                                            </label>
                                            <p class="pl-1">or drag and drop</p>
                                        </div>
                                        <p class="text-xs text-gray-500">PNG, JPG, GIF up to 5MB</p>
                                    </div>
                                </div>
                                
                                <template x-if="paymentProofPreview">
                                    <div class="mt-2">
                                        <img :src="paymentProofPreview" alt="Payment proof preview" class="h-32 object-cover rounded">
                                    </div>
                                </template>
                            </div>
                            
                            <div>
                                <label for="notes" class="block text-sm font-medium text-gray-700">{{ __('plans.notes') }}</label>
                                <div class="mt-1">
                                    <textarea id="notes" name="notes" rows="3" 
                                        x-model="notes"
                                        class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border border-gray-300 rounded-md"></textarea>
                                </div>
                            </div>
                            
                            <template x-if="error">
                                <div class="rounded-md bg-red-50 p-4">
                                    <div class="flex">
                                        <div class="flex-shrink-0">
                                            <svg class="h-5 w-5 text-red-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.28 7.22a.75.75 0 00-1.06 1.06L8.94 10l-1.72 1.72a.75.75 0 101.06 1.06L10 11.06l1.72 1.72a.75.75 0 101.06-1.06L11.06 10l1.72-1.72a.75.75 0 00-1.06-1.06L10 8.94 8.28 7.22z" clip-rule="evenodd" />
                                            </svg>
                                        </div>
                                        <div class="ml-3">
                                            <h3 class="text-sm font-medium text-red-800" x-text="error"></h3>
                                        </div>
                                    </div>
                                </div>
                            </template>
                            
                            <template x-if="success">
                                <div class="rounded-md bg-green-50 p-4">
                                    <div class="flex">
                                        <div class="flex-shrink-0">
                                            <svg class="h-5 w-5 text-green-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.857-9.809a.75.75 0 00-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 10-1.06 1.061l2.5 2.5a.75.75 0 001.137-.089l4-5.5z" clip-rule="evenodd" />
                                            </svg>
                                        </div>
                                        <div class="ml-3">
                                            <p class="text-sm font-medium text-green-800">{{ __('plans.submission_success') }}</p>
                                        </div>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="bg-gray-50 px-4 py-3 sm:flex sm:flex-row-reverse sm:px-6">
                <button type="button"
                    @click="submit"
                    :disabled="isLoading || success"
                    :class="{
                        'opacity-50 cursor-not-allowed': isLoading || success,
                        'bg-indigo-600 hover:bg-indigo-700': !isLoading && !success,
                        'bg-green-600 hover:bg-green-700': success,
                    }"
                    class="inline-flex w-full justify-center rounded-md border border-transparent px-4 py-2 text-base font-medium text-white shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 sm:ml-3 sm:w-auto sm:text-sm">
                    <template x-if="isLoading">
                        <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                    </template>
                    <template x-if="!isLoading && !success">
                        <span>{{ __('plans.submit_payment') }}</span>
                    </template>
                    <template x-if="success">
                        <span>{{ __('plans.success') }}!</span>
                    </template>
                </button>
                <button type="button"
                    @click="close"
                    :disabled="isLoading"
                    class="mt-3 inline-flex w-full justify-center rounded-md border border-gray-300 bg-white px-4 py-2 text-base font-medium text-gray-700 shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                    {{ __('plans.cancel') }}
                </button>
            </div>
        </div>
    </div>
</div>
