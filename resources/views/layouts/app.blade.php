<!DOCTYPE html>
<html lang="{{ LaravelLocalization::getCurrentLocale() }}" dir="{{ LaravelLocalization::getCurrentLocaleDirection() }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>@hasSection('title')@yield('title') | @endif{{ config('bank.name', config('app.name', 'Bank System')) }}</title>

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

                <!-- Page Heading & Breadcrumbs (Standardized Architecture) -->
                @if (isset($header) || View::hasSection('header') || View::hasSection('title') || View::hasSection('breadcrumb'))
                    <header class="bank-page-header border-b border-slate-200 bg-white">
                        <div class="mx-auto max-w-7xl px-4 py-3 sm:px-6 lg:px-8">
                            <!-- Unified Discreet Breadcrumbs (Always top, consistent, doesn't distort header) -->
                            <nav aria-label="{{ __('Breadcrumb') }}" class="mb-1 text-xs">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item">
                                        <a href="{{ route('dashboard') }}" class="inline-flex items-center gap-1.5 hover:text-emerald-700">
                                            <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                                            </svg>
                                            <span>{{ __('Dashboard') }}</span>
                                        </a>
                                    </li>
                                    @yield('breadcrumb')
                                </ol>
                            </nav>

                            <!-- Main Heading & Actions Row (Always Full Width & Consistent) -->
                            @if (isset($header) || View::hasSection('header'))
                                <div class="w-full">
                                    {{ $header ?? '' }}
                                    @yield('header')
                                </div>
                            @elseif (View::hasSection('title'))
                                <div class="w-full flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                                    <div class="min-w-0">
                                        @hasSection('subtitle')
                                            <p class="text-xs font-semibold uppercase text-emerald-700">@yield('subtitle')</p>
                                        @endif
                                        <h1 class="text-xl sm:text-2xl font-bold text-slate-950 tracking-tight">@yield('title')</h1>
                                    </div>
                                    @hasSection('actions')
                                        <div class="flex items-center gap-2 shrink-0">
                                            @yield('actions')
                                        </div>
                                    @endif
                                </div>
                            @endif
                        </div>
                    </header>
                @endif

                <!-- Page Content -->
                <main class="bank-main flex-1">
                    <div class="mx-auto max-w-7xl px-4 pt-6 sm:px-6 lg:px-8">
                        <x-flash-message />
                    </div>
                    {{ $slot ?? '' }}
                    @yield('content')
                </main>
            </div>
        </div>
    </body>
</html>
