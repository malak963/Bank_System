@php
    $profileRoute = config('nav.profile_route', 'profile.edit');
    $logoutRoute = config('nav.logout_route', 'logout');
@endphp

<header class="sticky top-0 z-40 flex h-16 shrink-0 items-center gap-x-4 border-b border-slate-200 bg-white/80 px-4 backdrop-blur-md sm:gap-x-6 sm:px-6 lg:px-8 shadow-xs">
    <!-- Mobile Hamburger Toggle -->
    <button type="button"
            @click="sidebarOpen = true"
            class="-m-2.5 p-2.5 text-slate-700 hover:text-slate-950 lg:hidden rounded-lg hover:bg-slate-100 transition">
        <span class="sr-only">{{ __('Open sidebar') }}</span>
        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
        </svg>
    </button>

    <!-- Separator for mobile -->
    <div class="h-6 w-px bg-slate-200 lg:hidden" aria-hidden="true"></div>

    <div class="flex flex-1 items-center justify-between gap-x-4 self-stretch lg:gap-x-6">
        <!-- Quick Title or Search placeholder -->
        <div class="flex items-center gap-3">
            <span class="hidden sm:inline-flex items-center gap-1.5 rounded-full bg-emerald-50 px-3 py-1 text-xs font-semibold text-emerald-700 ring-1 ring-inset ring-emerald-600/20">
                <span class="h-1.5 w-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                {{ config('bank.descriptor', 'Secure banking operations') }}
            </span>
        </div>

        <div class="flex items-center gap-x-3 sm:gap-x-4">
            <!-- Language Selector -->
            <div class="inline-flex items-center rounded-lg border border-slate-200 bg-slate-50 p-1 text-xs font-medium">
                @foreach(LaravelLocalization::getSupportedLocales() as $localeCode => $properties)
                    <a href="{{ LaravelLocalization::getLocalizedURL($localeCode, null, [], true) }}"
                       class="px-2.5 py-1 rounded transition-colors {{ LaravelLocalization::getCurrentLocale() === $localeCode ? 'bg-emerald-600 text-white font-semibold shadow-xs' : 'text-slate-600 hover:text-slate-950' }}">
                        {{ $properties['native'] }}
                    </a>
                @endforeach
            </div>

            <!-- Separator -->
            <div class="hidden sm:block sm:h-6 sm:w-px sm:bg-slate-200" aria-hidden="true"></div>

            <!-- Profile dropdown / actions -->
            <div class="flex items-center gap-2 sm:gap-3">
                <a href="{{ route($profileRoute) }}" class="flex items-center gap-2 p-1.5 rounded-lg hover:bg-slate-100 transition group">
                    <span class="flex h-8 w-8 items-center justify-center rounded-full bg-emerald-500/10 text-emerald-700 font-bold text-xs ring-1 ring-emerald-500/20 group-hover:bg-emerald-500/20">
                        {{ Str::upper(Str::substr(Auth::user()->name ?? 'U', 0, 1)) }}
                    </span>
                    <span class="hidden sm:block text-xs font-semibold text-slate-800">{{ Auth::user()->name ?? '' }}</span>
                </a>

                <form method="POST" action="{{ route($logoutRoute) }}">
                    @csrf
                    <button type="submit"
                            title="{{ __('Log Out') }}"
                            class="p-2 text-slate-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition">
                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0 0 13.5 3h-6a2.25 2.25 0 0 0-2.25 2.25v13.5A2.25 2.25 0 0 0 7.5 21h6a2.25 2.25 0 0 0 2.25-2.25V15m3 0 3-3m0 0-3-3m3 3H9" />
                        </svg>
                    </button>
                </form>
            </div>
        </div>
    </div>
</header>
