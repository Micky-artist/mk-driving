@php
    $isEdit = isset($subscriptionPlan);
    $route = $isEdit 
        ? route('admin.subscription-plans.update', $subscriptionPlan)
        : route('admin.subscription-plans.store');
    $method = $isEdit ? 'PUT' : 'POST';
    $title = $isEdit ? 'Edit Subscription Plan' : 'Create New Subscription Plan';
    $buttonText = $isEdit ? 'Update Plan' : 'Create Plan';
    $plan = $subscriptionPlan ?? new \App\Models\SubscriptionPlan();
    
    // Set default values for features if not set
    $features = old('features', $plan->features ?? ['']);
    if (empty($features) || (is_array($features) && count($features) === 0)) {
        $features = [''];
    }
    
    // Get current locale
    $locale = app()->getLocale();
    $otherLocale = $locale === 'en' ? 'rw' : 'en';
@endphp

<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">{{ $title }}</h6>
    </div>
    <div class="card-body">
        <form action="{{ $route }}" method="POST">
            @csrf
            @method($method)
            
            <div class="row mb-3">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="name_en">Name (English) *</label>
                        <input type="text" class="form-control @error('name.en') is-invalid @enderror" 
                               id="name_en" name="name[en]" 
                               value="{{ old('name.en', $plan->getTranslation('name', 'en', false)) }}" required>
                        @error('name.en')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="name_rw">Name (Kinyarwanda) *</label>
                        <input type="text" class="form-control @error('name.rw') is-invalid @enderror" 
                               id="name_rw" name="name[rw]" 
                               value="{{ old('name.rw', $plan->getTranslation('name', 'rw', false)) }}" required>
                        @error('name.rw')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>
            </div>
            
            <div class="row mb-3">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="description_en">Description (English) *</label>
                        <textarea class="form-control @error('description.en') is-invalid @enderror" 
                                 id="description_en" name="description[en]" rows="3" required>{{ old('description.en', $plan->getTranslation('description', 'en', false)) }}</textarea>
                        @error('description.en')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="description_rw">Description (Kinyarwanda) *</label>
                        <textarea class="form-control @error('description.rw') is-invalid @enderror" 
                                 id="description_rw" name="description[rw]" rows="3" required>{{ old('description.rw', $plan->getTranslation('description', 'rw', false)) }}</textarea>
                        @error('description.rw')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>
            </div>
            
            <div class="row mb-3">
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="price">Price ($) *</label>
                        <input type="number" step="0.01" min="0" class="form-control @error('price') is-invalid @enderror" 
                               id="price" name="price" value="{{ old('price', $plan->price) }}" required>
                        @error('price')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="duration">Duration (days) *</label>
                        <input type="number" min="1" class="form-control @error('duration') is-invalid @enderror" 
                               id="duration" name="duration" value="{{ old('duration', $plan->duration ?? 30) }}" required>
                        @error('duration')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="max_quizzes">Max Quizzes *</label>
                        <input type="number" min="0" class="form-control @error('max_quizzes') is-invalid @enderror" 
                               id="max_quizzes" name="max_quizzes" value="{{ old('max_quizzes', $plan->max_quizzes ?? 0) }}" required>
                        <small class="form-text text-muted">Set to 0 for unlimited quizzes</small>
                        @error('max_quizzes')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>
            </div>
            
            <div class="row mb-3">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="color">Color *</label>
                        <input type="color" class="form-control form-control-color @error('color') is-invalid @enderror" 
                               id="color" name="color" value="{{ old('color', $plan->color ?? '#4e73df') }}" required>
                        @error('color')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <div class="form-check mt-4 pt-2">
                            <input class="form-check-input @error('is_active') is-invalid @enderror" 
                                   type="checkbox" id="is_active" name="is_active" 
                                   value="1" {{ old('is_active', $plan->is_active) ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_active">
                                Active Plan
                            </label>
                            @error('is_active')
                                <span class="invalid-feedback d-block" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="mb-3">
                <label class="form-label">Features</label>
                <div id="features-container">
                    @foreach($features as $index => $feature)
                        <div class="input-group mb-2 feature-input">
                            <input type="text" class="form-control" name="features[]" 
                                   value="{{ is_array($feature) ? ($feature[$locale] ?? '') : $feature }}" 
                                   placeholder="Enter feature">
                            <button type="button" class="btn btn-outline-danger remove-feature">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                    @endforeach
                </div>
                <button type="button" id="add-feature" class="btn btn-sm btn-outline-secondary mt-2">
                    <i class="fas fa-plus"></i> Add Feature
                </button>
            </div>
            
            <div class="d-flex justify-content-between">
                <a href="{{ route('admin.subscription-plans.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Back to List
                </a>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> {{ $buttonText }}
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Add feature input
        document.getElementById('add-feature').addEventListener('click', function() {
            const container = document.getElementById('features-container');
            const newInput = document.createElement('div');
            newInput.className = 'input-group mb-2 feature-input';
            newInput.innerHTML = `
                <input type="text" class="form-control" name="features[]" placeholder="Enter feature">
                <button type="button" class="btn btn-outline-danger remove-feature">
                    <i class="fas fa-times"></i>
                </button>
            `;
            container.appendChild(newInput);
            
            // Add event listener to the new remove button
            newInput.querySelector('.remove-feature').addEventListener('click', function() {
                container.removeChild(newInput);
            });
        });
        
        // Remove feature input
        document.querySelectorAll('.remove-feature').forEach(button => {
            button.addEventListener('click', function() {
                this.closest('.feature-input').remove();
            });
        });
    });
</script>
@endpush
