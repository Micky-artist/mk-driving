<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'MK Driving') }} - Admin</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <style>
        [x-cloak] { display: none !important; }
        body { 
            font-family: 'Figtree', Arial, sans-serif; 
            background-color: #f5f5f5;
            min-height: 100vh;
        }
        .admin-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 2rem;
        }
        .alert {
            padding: 1rem;
            margin-bottom: 1rem;
            border-radius: 0.25rem;
        }
        .alert-success {
            background-color: #d1fae5;
            color: #065f46;
        }
        .btn {
            padding: 0.5rem 1rem;
            border-radius: 0.25rem;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.2s;
        }
        .btn-success {
            background-color: #10b981;
            color: white;
        }
        .btn-danger {
            background-color: #ef4444;
            color: white;
        }
        .btn:hover {
            opacity: 0.9;
        }
        .table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 1rem;
        }
        .table th, .table td {
            padding: 0.75rem;
            text-align: left;
            border-bottom: 1px solid #e5e7eb;
        }
        .table th {
            background-color: #f9fafb;
            font-weight: 600;
        }
        .table tr:hover {
            background-color: #f9fafb;
        }
    </style>
</head>
<body class="antialiased">
    <div class="admin-container">
        <header class="mb-8">
            <h1 class="text-2xl font-bold">MK Driving Admin</h1>
            <nav class="mt-4">
                <a href="{{ route('admin.subscriptions.index') }}" class="text-blue-600 hover:text-blue-800">Subscription Requests</a>
            </nav>
        </header>

        <main>
            @if(session('success'))
                <div class="alert alert-success mb-4">
                    {{ session('success') }}
                </div>
            @endif

            @yield('content')
        </main>
    </div>
</body>
</html>
