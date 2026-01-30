<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>@yield('title', 'Dashboard') - Supplier Portal</title>

        <!-- Scripts and CSS -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased">
        <div class="min-h-screen bg-gray-100">
            <nav class="bg-white border-b border-gray-100">
                <!-- Primary Navigation Menu -->
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div class="flex justify-between h-16">
                        <div class="flex">
                            <!-- Logo -->
                            <div class="shrink-0 flex items-center">
                                <a href="{{ route('supplier.dashboard') }}" class="font-bold">
                                    Supplier Portal
                                </a>
                            </div>
                        </div>

                        <!-- Right Side Of Navbar -->
                        <div class="flex items-center ml-6">
                            <span class="mr-4">Welcome, {{ Auth::guard('supplier')->user()->name }}</span>
                            <a href="{{ route('supplier.profile') }}" class="text-sm text-gray-700 underline mr-4">Profile</a>
                            <form method="POST" action="{{ route('supplier.logout') }}">
                                @csrf
                                <a href="{{ route('supplier.logout') }}" onclick="event.preventDefault(); this.closest('form').submit();" class="text-sm text-gray-700 underline">
                                    Log Out
                                </a>
                            </form>
                        </div>
                    </div>
                </div>
            </nav>

            <!-- Page Content -->
            <main class="py-12">
                <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                    @yield('content')
                </div>
            </main>
        </div>
    </body>
</html>