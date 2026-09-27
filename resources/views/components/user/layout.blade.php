@props([
    'title' => null,
    'subtitle' => null,
    'actions' => null,
    'breadcrumbs' => [],
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
        @if($title || $actions || !empty($breadcrumbs))
            <div class="bg-white border-b border-slate-200">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4 sm:py-5 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                    <div>
                        <!-- Consistent Breadcrumb like school-system -->
                        <div class="mb-1.5">
                            <ol class="inline-flex items-center gap-1.5 text-xs text-slate-500">
                                <li>
                                    <a href="{{ route('portal.dashboard') }}" class="hover:text-emerald-700 transition flex items-center gap-1 font-medium">
                                        <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="m2.25 12 8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25" />
                                        </svg>
                                        <span>{{ __('Digital Banking') }}</span>
                                    </a>
                                </li>
                                @if(!empty($breadcrumbs))
                                    @foreach($breadcrumbs as $bc)
                                        <li class="flex items-center gap-1.5">
                                            <svg class="h-3 w-3 text-slate-300 rtl:rotate-180" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                                            @if(!empty($bc['url']) && !$loop->last)
                                                <a href="{{ $bc['url'] }}" class="hover:text-emerald-700 transition">{{ $bc['label'] }}</a>
                                            @else
                                                <span class="text-slate-900 font-semibold">{{ is_array($bc) ? $bc['label'] : $bc }}</span>
                                            @endif
                                        </li>
                                    @endforeach
                                @elseif($title)
                                    <li class="flex items-center gap-1.5">
                                        <svg class="h-3 w-3 text-slate-300 rtl:rotate-180" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                                        <span class="text-slate-900 font-semibold">{{ $title }}</span>
                                    </li>
                                @endif
                            </ol>
                        </div>

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
