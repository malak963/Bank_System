@props([
    'title' => null,
    'subtitle' => null,
    'actions' => null,
])

<!DOCTYPE html>
<html lang="{{ LaravelLocalization::getCurrentLocale() }}" dir="{{ LaravelLocalization::getCurrentLocaleDirection() }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title ? $title . ' - ' : '' }}{{ config('user-nav.brand.name', 'MDAD Bank') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600|alexandria:400,500,600,700&display=swap" rel="stylesheet" />

    <!-- Scripts and Styles -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <style>
        [x-cloak] { display: none !important; }
        html[dir="rtl"] body {
            font-family: 'Alexandria', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
        }
    </style>
</head>
<body class="font-sans antialiased bg-[#f8fafc] text-slate-800 min-h-screen flex flex-col">

    <!-- Minimal Navbar -->
    <x-user.navbar />

    <!-- Main Container -->
    <main class="flex-1 pb-16">
        @if($title || $actions)
            <div class="bg-white border-b border-slate-200">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-5 sm:py-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                    <div>
                        <h1 class="text-xl sm:text-2xl font-bold text-slate-900 tracking-tight">
                            {{ $title }}
                        </h1>
                        @if($subtitle)
                            <p class="mt-0.5 text-xs text-slate-500">
                                {{ $subtitle }}
                            </p>
                        @endif
                    </div>
                    @if($actions)
                        <div class="flex items-center gap-2">
                            {{ $actions }}
                        </div>
                    @endif
                </div>
            </div>
        @endif

        <!-- Flash Alert -->
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-6">
            <x-user.alert />
        </div>

        <!-- Page Content -->
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-2">
            {{ $slot }}
        </div>
    </main>

    <!-- Clean Minimal Footer -->
    <footer class="bg-white border-t border-slate-200 py-6 text-xs text-slate-500">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-col sm:flex-row items-center justify-between gap-3">
            <div>
                <span class="font-semibold text-slate-700">{{ config('user-nav.brand.name', 'MDAD Bank') }}</span>
                <span>&copy; {{ date('Y') }} {{ __('All rights reserved.') }}</span>
            </div>
            <div class="flex items-center gap-4 text-slate-500">
                <a href="{{ route('portal.dashboard') }}" class="hover:text-slate-900 transition">{{ __('Dashboard') }}</a>
                <a href="{{ route('portal.deposit') }}" class="hover:text-slate-900 transition">{{ __('Deposit') }}</a>
                <a href="{{ route('portal.withdraw') }}" class="hover:text-slate-900 transition">{{ __('Withdraw') }}</a>
                <a href="{{ route('portal.transfer') }}" class="hover:text-slate-900 transition">{{ __('Transfer') }}</a>
                <a href="{{ route('portal.invoices') }}" class="hover:text-slate-900 transition">{{ __('Invoices') }}</a>
                <a href="{{ route('portal.transactions') }}" class="hover:text-slate-900 transition">{{ __('Transactions') }}</a>
            </div>
        </div>
    </footer>

</body>
</html>
