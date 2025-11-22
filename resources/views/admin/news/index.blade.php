@extends('admin.layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">News Management</h1>
            <p class="text-gray-600">Manage all news articles on the platform</p>
        </div>
        <div class="mt-4 md:mt-0">
            <a href="{{ route('admin.msi.news.create') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-800 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition ease-in-out duration-150">
                <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                </svg>
                Create New Article
            </a>
        </div>
    </div>

    @if (session('success'))
        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6" role="alert">
            <p>{{ session('success') }}</p>
        </div>
    @endif

    <div class="bg-white shadow overflow-hidden sm:rounded-lg">
        @if($news->count() > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 p-6">
                @foreach($news as $article)
                    <div class="border rounded-lg overflow-hidden hover:shadow-lg transition-shadow duration-300">
                        @if(!empty($article->images) && count($article->images) > 0)
                            <div class="h-48 overflow-hidden">
                                <img src="{{ asset('storage/' . $article->images[0]) }}" alt="{{ $article->title }}" class="w-full h-full object-cover">
                            </div>
                        @endif
                        
                        <div class="p-4">
                            <div class="flex justify-between items-start">
                                <h3 class="text-lg font-semibold text-gray-900 mb-2">{{ $article->title }}</h3>
                                <div class="relative">
                                    <button type="button" class="text-gray-400 hover:text-gray-600 focus:outline-none" id="menu-button-{{ $article->id }}" aria-expanded="false" aria-haspopup="true" x-data="{ open: false }" @click="open = !open">
                                        <span class="sr-only">Open options</span>
                                        <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                            <path d="M10 6a2 2 0 110-4 2 2 0 010 4zM10 12a2 2 0 110-4 2 2 0 010 4zM10 18a2 2 0 110-4 2 2 0 010 4z" />
                                        </svg>
                                    </button>

                                    <div x-show="open" @click.away="open = false" class="origin-top-right absolute right-0 mt-2 w-48 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 focus:outline-none z-10" role="menu" aria-orientation="vertical" aria-labelledby="menu-button-{{ $article->id }}" tabindex="-1">
                                        <div class="py-1" role="none">
                                            <a href="{{ route('admin.msi.news.edit', $article->id) }}" class="text-gray-700 block px-4 py-2 text-sm hover:bg-gray-100" role="menuitem" tabindex="-1" id="menu-item-0">
                                                <i class="fas fa-edit mr-2"></i> Edit
                                            </a>
                                            <form action="{{ route('admin.msi.news.destroy', $article->id) }}" method="POST" class="block w-full text-left">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-600 w-full text-left px-4 py-2 text-sm hover:bg-gray-100" role="menuitem" tabindex="-1" id="menu-item-1" onclick="return confirm('Are you sure you want to delete this article?')">
                                                    <i class="fas fa-trash mr-2"></i> Delete
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="flex items-center text-sm text-gray-500 mb-3">
                                <span class="flex items-center mr-4">
                                    <svg class="h-4 w-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                    </svg>
                                    {{ $article->created_at->format('M d, Y') }}
                                </span>
                                @if($article->author)
                                    <span class="flex items-center">
                                        <svg class="h-4 w-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                        </svg>
                                        {{ $article->author->name }}
                                    </span>
                                @endif
                            </div>
                            
                            <div class="flex flex-wrap gap-2 mb-3">
                                @if($article->category)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                        {{ $article->category }}
                                    </span>
                                @endif
                                @if(!empty($article->images) && count($article->images) > 0)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                        {{ count($article->images) }} {{ Str::plural('image', count($article->images)) }}
                                    </span>
                                @endif
                            </div>
                            
                            <p class="text-gray-600 text-sm mb-4 line-clamp-3">
                                {{ Str::limit(strip_tags($article->content), 150) }}
                            </p>
                            
                            <div class="flex justify-between items-center">
                                <span class="text-xs text-gray-500">
                                    /news/{{ $article->slug }}
                                </span>
                                <a href="{{ route('admin.msi.news.edit', $article->id) }}" class="text-sm text-blue-600 hover:text-blue-800">
                                    View/Edit
                                </a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
            
            <div class="px-6 py-4 border-t border-gray-200">
                {{ $news->links() }}
            </div>
        @else
            <div class="text-center py-12">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"></path>
                </svg>
                <h3 class="mt-2 text-sm font-medium text-gray-900">No articles yet</h3>
                <p class="mt-1 text-sm text-gray-500">Get started by creating a new article.</p>
                <div class="mt-6">
                    <a href="{{ route('admin.msi.news.create') }}" class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                        </svg>
                        New Article
                    </a>
                </div>
            </div>
        @endif
    </div>
</div>

@push('scripts')
<script>
    // Add Alpine.js for dropdown functionality
    document.addEventListener('alpine:init', () => {
        Alpine.data('dropdown', () => ({
            open: false,
            toggle() {
                this.open = !this.open;
            }
        }));
    });
</script>
@endpush
@endsection
