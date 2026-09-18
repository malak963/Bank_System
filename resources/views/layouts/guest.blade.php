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
    <body class="font-sans text-slate-900 antialiased">
        <div class="guest-shell flex min-h-screen flex-col justify-center px-4 py-8 sm:px-6 lg:px-8">
            <div class="mx-auto grid w-full max-w-5xl overflow-hidden rounded-lg border border-slate-200 bg-white shadow-2xl shadow-slate-900/10 lg:grid-cols-[0.9fr_1.1fr]">
                <div class="guest-brand-panel hidden flex-col justify-between p-10 lg:flex">
                    <div>
                        <div class="flex items-center gap-3">
                            <span class="flex h-11 w-11 items-center justify-center rounded-lg border border-emerald-300/30 bg-emerald-400/10 text-sm font-bold tracking-widest text-emerald-200">{{ config('bank.short_name') }}</span>
                            <div>
                                <p class="text-sm font-semibold text-white">{{ config('bank.name') }}</p>
                                <p class="text-xs text-slate-400">{{ __(config('bank.tagline')) }}</p>
                            </div>
                        </div>
                        <p class="mt-16 text-xs font-semibold uppercase tracking-[0.22em] text-emerald-300">{{ __(config('bank.dashboard.eyebrow')) }}</p>
                        <h1 class="mt-4 max-w-sm text-4xl font-semibold leading-tight tracking-tight text-white">{{ __(config('bank.dashboard.title')) }}</h1>
                        <p class="mt-5 max-w-sm text-sm leading-7 text-slate-300">{{ __(config('bank.dashboard.description')) }}</p>
                    </div>
                    <p class="text-xs text-slate-500">{{ config('bank.descriptor') }} · {{ config('bank.currency') }}</p>
                </div>

                <div class="guest-card px-6 py-8 sm:px-10 sm:py-10">
                    <div class="flex items-center justify-between mb-6">
                        <div class="lg:hidden">
                            <div class="flex items-center gap-3">
                                <span class="flex h-10 w-10 items-center justify-center rounded-lg bg-slate-950 text-xs font-bold tracking-widest text-emerald-300">{{ config('bank.short_name') }}</span>
                                <div>
                                    <p class="text-sm font-semibold text-slate-950">{{ config('bank.name') }}</p>
                                    <p class="text-xs text-slate-500">{{ __(config('bank.tagline')) }}</p>
                                </div>
                            </div>
                        </div>
                        <div class="ms-auto inline-flex items-center rounded-lg border border-slate-200 bg-slate-50 p-1 text-xs font-medium">
                            @foreach(LaravelLocalization::getSupportedLocales() as $localeCode => $properties)
                                <a href="{{ LaravelLocalization::getLocalizedURL($localeCode, null, [], true) }}"
                                   class="px-2.5 py-1 rounded transition-colors {{ LaravelLocalization::getCurrentLocale() === $localeCode ? 'bg-emerald-600 text-white font-semibold shadow-xs' : 'text-slate-600 hover:text-slate-950' }}">
                                    {{ $properties['native'] }}
                                </a>
                            @endforeach
                        </div>
                    </div>
                {{ $slot }}
                </div>
            </div>
        </div>
    </body>
</html>
