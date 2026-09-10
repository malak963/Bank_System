@php
    $navigationItems = config('nav.primary', []);
    $profileRoute = config('nav.profile_route', 'profile.edit');
    $logoutRoute = config('nav.logout_route', 'logout');
@endphp

<nav x-data="{ open: false }" class="bg-slate-950 border-b border-slate-800 shadow-lg shadow-slate-950/10">
    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <!-- Logo -->
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('dashboard') }}" class="flex items-center gap-3">
                        <span class="flex h-10 w-10 items-center justify-center rounded-lg border border-emerald-300/30 bg-emerald-400/10 text-emerald-200">
                            <span class="text-sm font-bold tracking-widest">{{ config('bank.short_name') }}</span>
                        </span>
                        <span class="hidden sm:block">
                            <span class="block text-sm font-semibold text-white leading-5">{{ config('bank.name') }}</span>
                            <span class="block text-[11px] font-medium text-slate-400 leading-4">{{ config('bank.tagline') }}</span>
                        </span>
                    </a>
                </div>

                <!-- Navigation Links -->
                <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                    @foreach ($navigationItems as $item)
                        @if (empty($item['ability']) || (Auth::check() && call_user_func([Auth::user(), $item['ability']])))
                            <x-nav-link :href="route($item['route'])" :active="request()->routeIs($item['active'])" @click.stop>
                                {{ __($item['label']) }}
                            </x-nav-link>
                        @endif
                    @endforeach
                </div>
            </div>

            <!-- Settings Dropdown -->
            <div class="hidden sm:flex sm:items-center sm:ms-6">
                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button class="inline-flex items-center gap-3 rounded-lg border border-slate-700 bg-slate-900 px-3 py-2 text-sm font-medium text-slate-200 hover:border-emerald-300/50 hover:bg-slate-800 focus:outline-none focus:ring-2 focus:ring-emerald-400/40 transition ease-in-out duration-150">
                            <span class="flex h-8 w-8 items-center justify-center rounded-full bg-emerald-400 text-xs font-bold text-slate-950">
                                {{ Str::upper(Str::substr(Auth::user()->name, 0, 1)) }}
                            </span>
                            <span class="text-left">
                                <span class="block text-sm text-white leading-4">{{ Auth::user()->name }}</span>
                                <span class="block text-xs text-slate-400 leading-4">{{ Auth::user()->role?->label() ?? __('User') }}</span>
                            </span>

                            <div>
                                <svg class="fill-current h-4 w-4 text-slate-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </div>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <x-dropdown-link :href="route($profileRoute)">
                            {{ __('Profile') }}
                        </x-dropdown-link>

                        <!-- Authentication -->
                        <form method="POST" action="{{ route($logoutRoute) }}">
                            @csrf

                            <x-dropdown-link :href="route($logoutRoute)"
                                    onclick="event.preventDefault(); this.closest('form').submit();">
                                {{ __('Log Out') }}
                            </x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>
            </div>

            <!-- Hamburger -->
            <div class="-me-2 flex items-center sm:hidden">
                <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-md text-slate-300 hover:text-white hover:bg-slate-800 focus:outline-none focus:bg-slate-800 focus:text-white transition duration-150 ease-in-out">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Responsive Navigation Menu -->
    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden border-t border-slate-800 bg-slate-950">
        <div class="pt-2 pb-3 space-y-1">
            @foreach ($navigationItems as $item)
                @if (empty($item['ability']) || (Auth::check() && call_user_func([Auth::user(), $item['ability']])))
                    <x-responsive-nav-link :href="route($item['route'])" :active="request()->routeIs($item['active'])" @click.stop="open = false">
                        {{ __($item['label']) }}
                    </x-responsive-nav-link>
                @endif
            @endforeach
        </div>

        <!-- Responsive Settings Options -->
        <div class="pt-4 pb-1 border-t border-slate-800">
            <div class="px-4">
                <div class="font-medium text-base text-white">{{ Auth::user()->name }}</div>
                <div class="font-medium text-sm text-slate-400">{{ Auth::user()->email }}</div>
            </div>

            <div class="mt-3 space-y-1">
                <x-responsive-nav-link :href="route($profileRoute)">
                    {{ __('Profile') }}
                </x-responsive-nav-link>

                <!-- Authentication -->
                <form method="POST" action="{{ route($logoutRoute) }}">
                    @csrf

                    <x-responsive-nav-link :href="route($logoutRoute)"
                            onclick="event.preventDefault(); this.closest('form').submit();">
                        {{ __('Log Out') }}
                    </x-responsive-nav-link>
                </form>
            </div>
        </div>
    </div>
</nav>
