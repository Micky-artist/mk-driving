@extends('layouts.app')

@section('content')
<div class="pt-16">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <div class="bg-white rounded-lg shadow-md overflow-hidden">
            <div class="px-6 py-12 sm:px-12 text-center">
                <div class="text-9xl font-bold text-indigo-600">404</div>
                <h1 class="mt-6 text-3xl font-bold text-gray-900 sm:text-4xl">
                    {{ __('errors.page_not_found') }}
                </h1>
                <p class="mt-4 text-lg text-gray-700">
                    {{ __('errors.page_not_found_message') }}
                </p>
                
                <div class="mt-8 flex flex-col sm:flex-row justify-center gap-4">
                    <a href="{{ url(app()->getLocale()) }}" class="inline-flex items-center justify-center px-6 py-3 border border-transparent text-base font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors duration-200">
                        <svg class="-ml-1 mr-2 h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z" clip-rule="evenodd" />
                        </svg>
                        {{ __('common.back_to_home') }}
                    </a>
                    
                    @if (Route::has('contact'))
                    <a href="{{ route('contact', ['locale' => app()->getLocale()]) }}" class="inline-flex items-center justify-center px-6 py-3 border border-gray-300 text-base font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors duration-200">
                        <svg class="-ml-1 mr-2 h-5 w-5 text-gray-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                            <path d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z" />
                            <path d="M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z" />
                        </svg>
                        {{ __('common.contact_support') }}
                    </a>
                    @endif
                </div>
                
                <div class="mt-10 pt-6 border-t border-gray-200">
                    <p class="text-sm text-gray-600">
                        {{ __('errors.need_help') }}
                        <a href="{{ route('contact', ['locale' => app()->getLocale()]) }}" class="font-medium text-indigo-600 hover:text-indigo-500">
                            {{ __('common.contact_support') }}
                        </a>
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
