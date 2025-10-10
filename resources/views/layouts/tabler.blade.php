<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Yanni's Meat Shop</title>

    <!-- Local Tabler CSS -->
    <link href="{{ asset('assets/css/tabler.min.css') }}" rel="stylesheet"/>

    <!-- Tailwind / App CSS -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>

<body class="font-sans antialiased theme-dark">
    <div class="min-h-screen bg-gray-100">
        @include('layouts.body.navigation')

        @if (isset($header))
            <header class="bg-white shadow">
                <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                    {{ $header }}
                </div>
            </header>
        @endif

        <main>
            @yield('content')
        </main>
    </div>

    @livewireScripts
    <script src="{{ asset('assets/js/sweetalert2.all.min.js') }}"></script>
</body>
</html>
