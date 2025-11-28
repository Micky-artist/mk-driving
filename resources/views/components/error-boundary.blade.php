<div class="bg-blue-900 text-white min-h-screen flex items-center justify-center p-6">
    <div class="max-w-md w-full bg-blue-800 rounded-lg shadow-xl overflow-hidden">
        <div class="p-8">
            <div class="flex justify-center mb-6">
                <div class="bg-orange-500 rounded-full p-3">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                </div>
            </div>
            <h2 class="text-2xl font-bold text-center mb-4">Oops! Something went wrong</h2>
            <p class="text-blue-200 text-center mb-6">
                {{ $message }}
            </p>
            
            @if(app()->environment('local') && isset($exception))
                <div class="mt-6 bg-blue-900 rounded-lg p-4 overflow-x-auto">
                    <div class="text-sm text-orange-400 font-mono">
                        {{ $exception->getMessage() }} in {{ $exception->getFile() }}:{{ $exception->getLine() }}
                    </div>
                    <div class="mt-2 text-xs text-blue-300 font-mono overflow-x-auto">
                        @foreach(explode("\n", $exception->getTraceAsString()) as $trace)
                            <div class="py-1 border-b border-blue-700">{{ $trace }}</div>
                        @endforeach
                    </div>
                </div>
            @endif
            
            <div class="mt-8 text-center">
                <a href="{{ url()->previous() }}" class="inline-flex items-center px-4 py-2 bg-orange-500 hover:bg-orange-600 text-white font-medium rounded-md transition duration-150 ease-in-out">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    Go Back
                </a>
            </div>
        </div>
    </div>
</div>
