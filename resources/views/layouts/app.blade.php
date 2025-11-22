<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'MK Driving') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Figtree', 'sans-serif'],
                    },
                    spacing: {
                        '128': '32rem',
                        '144': '36rem',
                    },
                },
            },
            plugins: [
                require('@tailwindcss/forms'),
                require('@tailwindcss/typography'),
            ],
        }
    </script>

    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    
    @stack('styles')
</head>
<body class="font-sans antialiased bg-gray-50 flex flex-col min-h-screen">
    <!-- Header -->
    @if (!request()->is('dashboard*'))
        @include('components.navbar')
    @endif

    <!-- Page Content -->
    <main class="flex-grow pt-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
            @yield('content')
        </div>
    </main>

    <!-- Footer -->
    @include('components.footer')

    @stack('scripts')
</body>
</html>
