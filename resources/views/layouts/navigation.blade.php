
@php
    $navigationItems = config('nav.primary', []);

    $profileRoute = config('nav.profile_route', 'profile.edit');
    $logoutRoute = config('nav.logout_route', 'logout');

    /*
    |--------------------------------------------------------------------------
    | Navigation Icons
    |--------------------------------------------------------------------------
    | The icon names come from config/nav.php.
    | Example:
    | 'icon' => 'users-round'
    |
    | We map them to registered Lucide Blade components here.
    |--------------------------------------------------------------------------
    */
    $iconComponents = [
        'layout-dashboard' => 'lucide-layout-dashboard',
        'users-round'      => 'lucide-users-round',
        'wallet-cards'     => 'lucide-wallet-cards',
        'layers-3'         => 'lucide-layers-3',
        'hand-coins'       => 'lucide-hand-coins',
        'calendar-check-2' => 'lucide-calendar-check-2',
        'shield-user'      => 'lucide-shield-user',
    ];
@endphp

<nav
    x-data="{ open: false }"
    class="border-b border-slate-800 bg-slate-950 shadow-lg shadow-slate-950/10"
>
    {{-- =========================================================
         PRIMARY NAVIGATION
    ========================================================== --}}
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">

        <div class="flex h-16 items-center justify-between">

            {{-- =================================================
                 LEFT SIDE
            ================================================== --}}
            <div class="flex h-full items-center">

                {{-- =======================
                     LOGO
                ======================== --}}
                <div class="flex shrink-0 items-center">

                    <a
                        href="{{ route('dashboard') }}"
                        class="group flex items-center gap-3"
                    >

                        {{-- Logo Mark --}}
                        <span
                            class="flex h-10 w-10 items-center justify-center rounded-lg
                                   border border-emerald-300/30
                                   bg-emerald-400/10
                                   text-emerald-200
                                   transition-all duration-200
                                   group-hover:border-emerald-300/50
                                   group-hover:bg-emerald-400/15"
                        >
                            <span class="text-sm font-bold tracking-widest">
                                {{ config('bank.short_name') }}
                            </span>
                        </span>

                        {{-- Bank Name --}}
                        <span class="hidden sm:block">

                            <span
                                class="block text-sm font-semibold leading-5 text-white"
                            >
                                {{ config('bank.name') }}
                            </span>

                            <span
                                class="block text-[11px] font-medium leading-4 text-slate-400"
                            >
                                {{ config('bank.tagline') }}
                            </span>

                        </span>

                    </a>

                </div>


                {{-- =======================
                     DESKTOP NAVIGATION
                ======================== --}}
                <div
                    class="hidden h-full items-center gap-1 sm:ms-8 sm:flex"
                >

                    @foreach ($navigationItems as $item)

                        @if (
                            empty($item['ability']) ||
                            (Auth::check() &&
                            call_user_func([Auth::user(), $item['ability']]))
                        )

                            @php
                                $isActive = request()->routeIs($item['active']);

                                $iconComponent =
                                    $iconComponents[$item['icon'] ?? '']
                                    ?? null;
                            @endphp

                            <a
                                href="{{ route($item['route']) }}"
                                class="group relative inline-flex h-full items-center gap-2
                                       px-3 text-sm font-medium
                                       transition-all duration-200
                                       {{ $isActive
                                            ? 'text-emerald-300'
                                            : 'text-slate-400 hover:text-white'
                                       }}"
                            >

                                {{-- Icon --}}
                                @if ($iconComponent)

                                    <x-dynamic-component
                                        :component="$iconComponent"
                                        class="h-4 w-4 shrink-0 transition-all duration-200
                                               {{ $isActive
                                                    ? 'text-emerald-400'
                                                    : 'text-slate-500 group-hover:text-emerald-400'
                                               }}"
                                    />

                                @endif


                                {{-- Label --}}
                                <span>
                                    {{ __($item['label']) }}
                                </span>


                                {{-- Active Indicator --}}
                                <span
                                    class="absolute bottom-0 left-2 right-2 h-0.5 rounded-full
                                           bg-emerald-400 transition-all duration-200
                                           {{ $isActive
                                                ? 'scale-x-100 opacity-100'
                                                : 'scale-x-0 opacity-0 group-hover:scale-x-75 group-hover:opacity-60'
                                           }}"
                                ></span>

                            </a>

                        @endif

                    @endforeach

                </div>

            </div>


            {{-- =================================================
                 DESKTOP USER MENU
            ================================================== --}}
            <div class="hidden items-center sm:flex">

                <x-dropdown
                    align="right"
                    width="56"
                >

                    {{-- =======================
                         DROPDOWN TRIGGER
                    ======================== --}}
                    <x-slot name="trigger">

                        <button
                            type="button"
                            class="group inline-flex items-center gap-3 rounded-xl
                                   border border-slate-700
                                   bg-slate-900
                                   px-3 py-2
                                   text-sm font-medium text-slate-200
                                   transition-all duration-200
                                   hover:border-emerald-300/40
                                   hover:bg-slate-800
                                   focus:outline-none
                                   focus:ring-2
                                   focus:ring-emerald-400/40"
                        >

                            {{-- Avatar --}}
                            <span
                                class="flex h-8 w-8 shrink-0 items-center justify-center
                                       rounded-full
                                       bg-emerald-400
                                       text-xs font-bold
                                       text-slate-950
                                       transition-transform duration-200
                                       group-hover:scale-105"
                            >
                                {{ Str::upper(Str::substr(Auth::user()->name, 0, 1)) }}
                            </span>


                            {{-- User Information --}}
                            <span class="hidden text-left lg:block">

                                <span
                                    class="block max-w-32 truncate text-sm leading-4 text-white"
                                >
                                    {{ Auth::user()->name }}
                                </span>

                                <span
                                    class="block max-w-32 truncate text-xs leading-4 text-slate-400"
                                >
                                    {{ Auth::user()->role?->label() ?? __('User') }}
                                </span>

                            </span>


                            {{-- Chevron --}}
                            <svg
                                class="h-4 w-4 text-slate-400 transition-transform duration-200
                                       group-hover:text-emerald-400"
                                xmlns="http://www.w3.org/2000/svg"
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke="currentColor"
                                stroke-width="1.8"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    d="m6 9 6 6 6-6"
                                />
                            </svg>

                        </button>

                    </x-slot>


                    {{-- =======================
                         DROPDOWN CONTENT
                    ======================== --}}
                    <x-slot name="content">

                        {{-- Profile --}}
                        <x-dropdown-link
                            :href="route($profileRoute)"
                        >

                            <div class="flex items-center gap-3">

                                <x-lucide-user-round
                                    class="h-4 w-4 text-slate-500"
                                />

                                <span>
                                    {{ __('Profile') }}
                                </span>

                            </div>

                        </x-dropdown-link>


                        {{-- Logout --}}
                        <form
                            method="POST"
                            action="{{ route($logoutRoute) }}"
                        >

                            @csrf

                            <x-dropdown-link
                                :href="route($logoutRoute)"
                                onclick="event.preventDefault(); this.closest('form').submit();"
                            >

                                <div class="flex items-center gap-3">

                                    <x-lucide-log-out
                                        class="h-4 w-4 text-slate-500"
                                    />

                                    <span>
                                        {{ __('Log Out') }}
                                    </span>

                                </div>

                            </x-dropdown-link>

                        </form>

                    </x-slot>

                </x-dropdown>

            </div>


            {{-- =================================================
                 MOBILE HAMBURGER
            ================================================== --}}
            <div class="-me-2 flex items-center sm:hidden">

                <button
                    type="button"
                    @click="open = !open"
                    class="inline-flex items-center justify-center rounded-lg
                           p-2 text-slate-300
                           transition duration-200
                           hover:bg-slate-800
                           hover:text-white
                           focus:bg-slate-800
                           focus:text-white
                           focus:outline-none"
                >

                    {{-- Menu Icon --}}
                    <svg
                        :class="{ 'hidden': open, 'block': !open }"
                        class="h-6 w-6"
                        stroke="currentColor"
                        fill="none"
                        viewBox="0 0 24 24"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M4 6h16M4 12h16M4 18h16"
                        />
                    </svg>


                    {{-- Close Icon --}}
                    <svg
                        :class="{ 'block': open, 'hidden': !open }"
                        class="hidden h-6 w-6"
                        stroke="currentColor"
                        fill="none"
                        viewBox="0 0 24 24"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M6 18L18 6M6 6l12 12"
                        />
                    </svg>

                </button>

            </div>

        </div>

    </div>


    {{-- =========================================================
         MOBILE NAVIGATION
    ========================================================== --}}
    <div
        x-cloak
        x-show="open"
        x-transition
        class="border-t border-slate-800 bg-slate-950 sm:hidden"
    >

        <div class="space-y-1 px-3 pb-3 pt-3">

            @foreach ($navigationItems as $item)

                @if (
                    empty($item['ability']) ||
                    (Auth::check() &&
                    call_user_func([Auth::user(), $item['ability']]))
                )

                    @php
                        $isActive = request()->routeIs($item['active']);

                        $iconComponent =
                            $iconComponents[$item['icon'] ?? '']
                            ?? null;
                    @endphp

                    <a
                        href="{{ route($item['route']) }}"
                        @click="open = false"
                        class="group flex items-center gap-3 rounded-xl
                               px-3 py-2.5
                               text-sm font-medium
                               transition-all duration-200
                               {{ $isActive
                                    ? 'bg-emerald-400/10 text-emerald-300'
                                    : 'text-slate-400 hover:bg-slate-900 hover:text-white'
                               }}"
                    >

                        {{-- Icon --}}
                        @if ($iconComponent)

                            <span
                                class="flex h-8 w-8 shrink-0 items-center justify-center
                                       rounded-lg
                                       {{ $isActive
                                            ? 'bg-emerald-400/10'
                                            : 'bg-slate-900 group-hover:bg-slate-800'
                                       }}"
                            >

                                <x-dynamic-component
                                    :component="$iconComponent"
                                    class="h-4 w-4
                                           {{ $isActive
                                                ? 'text-emerald-400'
                                                : 'text-slate-500 group-hover:text-emerald-400'
                                           }}"
                                />

                            </span>

                        @endif


                        {{-- Label --}}
                        <span>
                            {{ __($item['label']) }}
                        </span>

                    </a>

                @endif

            @endforeach

        </div>


        {{-- =====================================================
             MOBILE USER SECTION
        ====================================================== --}}
        <div class="border-t border-slate-800 px-4 pb-4 pt-4">

            {{-- User Header --}}
            <div class="flex items-center gap-3">

                {{-- Avatar --}}
                <span
                    class="flex h-10 w-10 shrink-0 items-center justify-center
                           rounded-full
                           bg-emerald-400
                           text-sm font-bold
                           text-slate-950"
                >
                    {{ Str::upper(Str::substr(Auth::user()->name, 0, 1)) }}
                </span>


                {{-- User Info --}}
                <div class="min-w-0">

                    <div class="truncate text-sm font-medium text-white">
                        {{ Auth::user()->name }}
                    </div>

                    <div class="truncate text-xs text-slate-400">
                        {{ Auth::user()->email }}
                    </div>

                </div>

            </div>


            {{-- Mobile User Actions --}}
            <div class="mt-3 space-y-1">

                {{-- Profile --}}
                <a
                    href="{{ route($profileRoute) }}"
                    class="group flex items-center gap-3 rounded-xl
                           px-3 py-2.5
                           text-sm font-medium
                           text-slate-400
                           transition-all duration-200
                           hover:bg-slate-900
                           hover:text-white"
                >

                    <x-lucide-user-round
                        class="h-5 w-5 text-slate-500
                               transition-colors
                               group-hover:text-emerald-400"
                    />

                    <span>
                        {{ __('Profile') }}
                    </span>

                </a>


                {{-- Logout --}}
                <form
                    method="POST"
                    action="{{ route($logoutRoute) }}"
                >

                    @csrf

                    <a
                        href="{{ route($logoutRoute) }}"
                        onclick="event.preventDefault(); this.closest('form').submit();"
                        class="group flex items-center gap-3 rounded-xl
                               px-3 py-2.5
                               text-sm font-medium
                               text-slate-400
                               transition-all duration-200
                               hover:bg-red-500/10
                               hover:text-red-400"
                    >

                        <x-lucide-log-out
                            class="h-5 w-5 text-slate-500
                                   transition-colors
                                   group-hover:text-red-400"
                        />

                        <span>
                            {{ __('Log Out') }}
                        </span>

                    </a>

                </form>

            </div>

        </div>

    </div>

</nav>