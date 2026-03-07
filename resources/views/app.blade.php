<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>MK Driving Academy</title>

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <!-- Global API Configuration -->
    <script>
        // Auto-configure fetch requests to include credentials
        const originalFetch = window.fetch;
        window.fetch = function(url, options = {}) {
            // Always include credentials for same-origin requests
            if (!options.credentials && url.startsWith('/')) {
                options.credentials = 'same-origin';
            }
            
            // Auto-add CSRF token for web routes (not API routes)
            if (['POST', 'PUT', 'DELETE', 'PATCH'].includes(options.method?.toUpperCase()) && 
                !options.headers?.['X-CSRF-TOKEN'] && 
                !url.startsWith('/api/')) {
                const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
                if (token) {
                    options.headers = {
                        ...options.headers,
                        'X-CSRF-TOKEN': token,
                        'X-Requested-With': 'XMLHttpRequest'
                    };
                }
            }
            
            return originalFetch.call(this, url, options);
        };
    </script>
</head>
<body>
    <div id="app">
        @yield('content')
    </div>
</body>
</html>
