@extends('admin.layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="mb-6">
        <div class="flex items-center">
            <a href="{{ route('admin.msi.news.index') }}" class="text-blue-600 hover:text-blue-800 mr-2">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
            </a>
            <h1 class="text-2xl font-bold text-gray-900">Create News Article</h1>
        </div>
        <p class="text-gray-600 mt-1">Write and publish a new article for your users.</p>
    </div>

    <div class="bg-white shadow overflow-hidden sm:rounded-lg">
        <form action="{{ route('admin.msi.news.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="px-4 py-5 sm:p-6">
                @if ($errors->any())
                    <div class="bg-red-50 border-l-4 border-red-400 p-4 mb-6">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-red-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                                </svg>
                            </div>
                            <div class="ml-3">
                                <h3 class="text-sm font-medium text-red-800">There {{ $errors->count() == 1 ? 'was' : 'were' }} {{ $errors->count() }} {{ Str::plural('error', $errors->count()) }} with your submission</h3>
                                <div class="mt-2 text-sm text-red-700">
                                    <ul class="list-disc pl-5 space-y-1">
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif

                <div class="space-y-6">
                    <div>
                        <label for="title" class="block text-sm font-medium text-gray-700">Title <span class="text-red-500">*</span></label>
                        <div class="mt-1">
                            <input type="text" name="title" id="title" value="{{ old('title') }}" required class="shadow-sm focus:ring-blue-500 focus:border-blue-500 block w-full sm:text-sm border-gray-300 rounded-md">
                        </div>
                    </div>

                    <div>
                        <label for="content" class="block text-sm font-medium text-gray-700">Content <span class="text-red-500">*</span></label>
                        <div class="mt-1">
                            <textarea id="content" name="content" rows="10" class="shadow-sm focus:ring-blue-500 focus:border-blue-500 block w-full sm:text-sm border border-gray-300 rounded-md">{{ old('content') }}</textarea>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Images</label>
                        <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-md">
                            <div class="space-y-1 text-center">
                                <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48" aria-hidden="true">
                                    <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                                <div class="flex text-sm text-gray-600">
                                    <label for="images" class="relative cursor-pointer bg-white rounded-md font-medium text-blue-600 hover:text-blue-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-blue-500">
                                        <span>Upload files</span>
                                        <input id="images" name="images[]" type="file" multiple class="sr-only">
                                    </label>
                                    <p class="pl-1">or drag and drop</p>
                                </div>
                                <p class="text-xs text-gray-500">PNG, JPG, GIF up to 2MB</p>
                                <div id="image-preview" class="mt-2 flex flex-wrap gap-2"></div>
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                        <div>
                            <label for="category" class="block text-sm font-medium text-gray-700">Category</label>
                            <input type="text" name="category" id="category" value="{{ old('category') }}" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                        </div>
                        <div class="flex items-start">
                            <div class="flex items-center h-5">
                                <input id="is_published" name="is_published" type="checkbox" class="focus:ring-blue-500 h-4 w-4 text-blue-600 border-gray-300 rounded" {{ old('is_published') ? 'checked' : '' }}>
                            </div>
                            <div class="ml-3 text-sm">
                                <label for="is_published" class="font-medium text-gray-700">Publish immediately</label>
                                <p class="text-gray-500">Make this article visible to all users</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="px-4 py-3 bg-gray-50 text-right sm:px-6">
                <a href="{{ route('admin.msi.news.index') }}" class="inline-flex justify-center py-2 px-4 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    Cancel
                </a>
                <button type="submit" class="ml-3 inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    Publish Article
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    // Image preview functionality
    document.addEventListener('DOMContentLoaded', function() {
        const fileInput = document.getElementById('images');
        const previewContainer = document.getElementById('image-preview');

        fileInput.addEventListener('change', function() {
            previewContainer.innerHTML = ''; // Clear previous previews
            
            if (this.files) {
                Array.from(this.files).forEach(file => {
                    const reader = new FileReader();
                    
                    reader.onload = function(e) {
                        const preview = document.createElement('div');
                        preview.className = 'relative inline-block';
                        
                        const img = document.createElement('img');
                        img.src = e.target.result;
                        img.className = 'h-20 w-20 object-cover rounded';
                        
                        preview.appendChild(img);
                        previewContainer.appendChild(preview);
                    }
                    
                    reader.readAsDataURL(file);
                });
            }
        });
    });
</script>
@endpush
@endsection
