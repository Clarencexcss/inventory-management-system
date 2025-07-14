<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Custom CSS -->
    <style>
        .bg-primary { background-color: #3b82f6; }
        .bg-primary-dark { background-color: #2563eb; }
        .bg-primary-light { background-color: #dbeafe; }
        .bg-primary-lighter { background-color: #eff6ff; }
        .text-primary { color: #3b82f6; }
        .text-primary-dark { color: #2563eb; }
        .border-primary { border-color: #3b82f6; }
        .btn-primary { background-color: #3b82f6; color: white; }
        .btn-primary:hover { background-color: #2563eb; }
        .hover\:text-primary-dark:hover { color: #2563eb; }
        .hover\:bg-primary-dark:hover { background-color: #2563eb; }
        .hover\:bg-primary-lighter:hover { background-color: #eff6ff; }
    </style>
</head>
<body class="font-sans antialiased">
    <div class="min-h-screen bg-gradient-to-br from-blue-50 to-indigo-100 flex flex-col justify-center py-12 sm:px-6 lg:px-8">
        <div class="sm:mx-auto sm:w-full sm:max-w-md">
            <div class="text-center">
                <h1 class="text-4xl font-bold text-gray-900 mb-2">
                    🥩 Yannis Meatshop
                </h1>
                <p class="text-lg text-gray-600">
                    Inventory Management System
                </p>
            </div>
        </div>

        <div class="mt-8 sm:mx-auto sm:w-full sm:max-w-md">
            <div class="bg-white py-8 px-4 shadow-xl rounded-lg sm:px-10">
                <div class="text-center mb-8">
                    <h2 class="text-2xl font-bold text-gray-900 mb-2">
                        Welcome Back!
                    </h2>
                    <p class="text-gray-600">
                        Choose your login portal
                    </p>
                </div>

                <div class="space-y-4">
                    <!-- Admin/Staff Login Button -->
                    <a href="{{ route('login') }}" 
                       class="w-full flex justify-center py-3 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition duration-150 ease-in-out">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                        </svg>
                        Admin/Staff Login
                    </a>

                    <!-- Customer Login Button -->
                    <a href="{{ route('customer.login') }}" 
                       class="w-full flex justify-center py-3 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition duration-150 ease-in-out">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                        Customer Login
                    </a>
                </div>

                <div class="mt-6 text-center">
                    <p class="text-sm text-gray-600">
                        New customer? 
                        <a href="{{ route('customer.register') }}" class="font-medium text-indigo-600 hover:text-indigo-500">
                            Create an account
                        </a>
                    </p>
                </div>
            </div>
        </div>

        <div class="mt-8 text-center">
            <p class="text-sm text-gray-500">
                © 2024 Yannis Meatshop. All rights reserved.
            </p>
        </div>
    </div>
</body>
</html> 