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

                <!-- Page Heading & Breadcrumbs -->
                @if (isset($header) || View::hasSection('header'))
                    <header class="bank-page-header">
                        <div class="bank-page-header__inner mx-auto max-w-7xl px-4 py-4 sm:px-6 lg:px-8">
                            <div class="flex items-center justify-between gap-4">
                                <div class="min-w-0">
                                    {{ $header ?? '' }}
                                    @yield('header')
                                </div>
                                @if (View::hasSection('breadcrumb'))
                                    <nav aria-label="{{ __('Breadcrumb') }}" class="shrink-0 overflow-x-auto">
                                        <ol class="breadcrumb">
                                            <li class="breadcrumb-item">
                                                <a href="{{ route('dashboard') }}" class="hover:text-emerald-700">
                                                    {{ __('Dashboard') }}
                                                </a>
                                            </li>
                                            @yield('breadcrumb')
                                        </ol>
                                    </nav>
                                @endif
                            </div>
                        </div>
                    </header>
                @elseif (View::hasSection('title') || View::hasSection('breadcrumb'))
                    <header class="bank-page-header">
                        <div class="bank-page-header__inner mx-auto max-w-7xl px-4 py-4 sm:px-6 lg:px-8">
                            <div class="flex items-center justify-between gap-4">
                                <div class="min-w-0">
                                    @hasSection('subtitle')
                                        <p class="text-xs font-semibold uppercase text-emerald-700">@yield('subtitle')</p>
                                    @endif
                                    <h1 class="text-xl sm:text-2xl font-bold text-slate-950 tracking-tight">@yield('title')</h1>
                                </div>
                                <div class="flex items-center gap-3 sm:gap-4 shrink-0">
                                    @if (View::hasSection('breadcrumb'))
                                        <nav aria-label="{{ __('Breadcrumb') }}" class="shrink-0 overflow-x-auto">
                                            <ol class="breadcrumb">
                                                <li class="breadcrumb-item">
                                                    <a href="{{ route('dashboard') }}" class="hover:text-emerald-700">
                                                        {{ __('Dashboard') }}
                                                    </a>
                                                </li>
                                                @yield('breadcrumb')
                                            </ol>
                                        </nav>
                                    @endif
                                    @hasSection('actions')
                                        <div class="flex items-center gap-2 shrink-0">
                                            @yield('actions')
                                        </div>
                                    @endif
                                </div>
                            </div>
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
