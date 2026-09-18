<!DOCTYPE html>
<html lang="{{ LaravelLocalization::getCurrentLocale() }}" dir="{{ LaravelLocalization::getCurrentLocaleDirection() }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('bank.name', config('app.name', 'Bank System')) }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600|alexandria:400,500,600,700&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased text-slate-900 bg-[#f4f7f8]">
        <div x-data="{ sidebarOpen: false }" class="min-h-screen">
            <!-- Sidebar Navigation (Desktop Fixed + Mobile Off-Canvas Drawer) -->
            @include('layouts.navigation')

            <!-- Main Workspace Container (Starts after fixed sidebar on desktop) -->
            <div class="lg:ps-72 flex flex-col min-h-screen">
                <!-- Top Header Bar -->
                @include('layouts.topbar')

                <!-- Page Heading -->
                @if (isset($header) || View::hasSection('header'))
                    <header class="bank-page-header">
                        <div class="bank-page-header__inner mx-auto max-w-7xl px-4 py-5 sm:px-6 lg:px-8">
                            {{ $header ?? '' }}
                            @yield('header')
                        </div>
                    </header>
                @endif

                <!-- Page Content -->
                <main class="bank-main flex-1">
                    {{ $slot ?? '' }}
                    @yield('content')
                </main>
            </div>
        </div>
    </body>
</html>
