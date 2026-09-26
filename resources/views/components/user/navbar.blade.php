@php
    $navLinks = config('user-nav.links', []);
    $brand = config('user-nav.brand', [
        'name' => 'MDAD Bank',
        'logo_route' => 'portal.dashboard',
    ]);
@endphp

<header x-data="{ mobileMenuOpen: false, profileOpen: false }" class="sticky top-0 z-40 bg-white border-b border-slate-200">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-16">
            <!-- Brand & Desktop Navigation -->
            <div class="flex items-center gap-8">
                <a href="{{ route($brand['logo_route'] ?? 'portal.dashboard') }}" class="flex items-center gap-2.5">
                    <span class="w-8 h-8 rounded-lg bg-emerald-600 text-white font-bold text-sm flex items-center justify-center">
                        M
                    </span>
                    <span class="text-base font-bold text-slate-900 tracking-tight">
                        {{ __($brand['name']) }}
                    </span>
                </a>

                <!-- Desktop Nav Links (Iterated from config/user-nav.php) -->
                <nav class="hidden md:flex items-center gap-1">
                    @foreach($navLinks as $navItem)
                        @php
                            $isActive = request()->routeIs($navItem['active'] ?? '');
                        @endphp
                        <a href="{{ route($navItem['route']) }}"
                           class="px-3 py-1.5 rounded-md text-sm font-medium transition {{ $isActive ? 'text-emerald-700 bg-emerald-50 font-semibold' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-50' }}">
                            {{ __($navItem['title']) }}
                        </a>
                    @endforeach
                </nav>
            </div>

            <!-- Right: Locale Switcher & User Menu -->
            <div class="flex items-center gap-3">
                <!-- Mcamara Language Switcher -->
                <div class="flex items-center text-xs rounded-lg border border-slate-200 bg-slate-50 p-0.5">
                    @foreach(LaravelLocalization::getSupportedLocales() as $localeCode => $properties)
                        <a href="{{ LaravelLocalization::getLocalizedURL($localeCode, null, [], true) }}"
                           class="px-2.5 py-1 rounded-md font-medium transition {{ LaravelLocalization::getCurrentLocale() === $localeCode ? 'bg-white text-slate-900 font-semibold shadow-xs' : 'text-slate-500 hover:text-slate-900' }}">
                            {{ $properties['native'] }}
                        </a>
                    @endforeach
                </div>



                <!-- User Dropdown -->
                <div class="relative" @click.outside="profileOpen = false">
                    <button type="button"
                            @click="profileOpen = !profileOpen"
                            class="flex items-center gap-2 p-1.5 rounded-lg hover:bg-slate-100 transition text-start text-xs font-semibold text-slate-800">
                        <span class="w-7 h-7 rounded-full bg-slate-100 text-slate-700 flex items-center justify-center font-bold text-xs border border-slate-200">
                            {{ Str::upper(Str::substr(Auth::user()->name ?? 'U', 0, 1)) }}
                        </span>
                        <span class="hidden sm:inline">{{ Auth::user()->name ?? '' }}</span>
                        <svg class="w-3.5 h-3.5 text-slate-400" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                        </svg>
                    </button>

                    <!-- Dropdown -->
                    <div x-show="profileOpen"
                         x-transition
                         style="display: none;"
                         class="absolute end-0 mt-1 w-48 rounded-xl bg-white p-1.5 shadow-lg border border-slate-200 z-50 text-xs">
                        <div class="px-3 py-2 border-b border-slate-100 mb-1">
                            <p class="font-semibold text-slate-900 truncate">{{ Auth::user()->name ?? '' }}</p>
                            <p class="text-[11px] text-slate-400 truncate">{{ Auth::user()->email ?? '' }}</p>
                        </div>

                        @foreach(config('user-nav.profile_menu', []) as $pItem)
                            <a href="{{ route($pItem['route']) }}"
                               class="block px-3 py-1.5 rounded-md text-slate-700 hover:bg-slate-50 hover:text-emerald-700">
                                {{ __($pItem['title']) }}
                            </a>
                        @endforeach

                        <div class="my-1 border-t border-slate-100"></div>

                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit"
                                    class="w-full text-start px-3 py-1.5 rounded-md text-rose-600 hover:bg-rose-50 font-medium">
                                {{ __('Log Out') }}
                            </button>
                        </form>
                    </div>
                </div>

                <!-- Mobile Hamburger Toggle -->
                <button type="button"
                        @click="mobileMenuOpen = !mobileMenuOpen"
                        class="p-2 rounded-lg text-slate-600 hover:bg-slate-100 md:hidden">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Mobile Menu -->
    <div x-show="mobileMenuOpen"
         x-transition
         style="display: none;"
         class="md:hidden border-t border-slate-200 bg-white px-4 py-3 space-y-1">
        @foreach($navLinks as $navItem)
            @php
                $isActive = request()->routeIs($navItem['active'] ?? '');
            @endphp
            <a href="{{ route($navItem['route']) }}"
               @click="mobileMenuOpen = false"
               class="block px-3 py-2 rounded-md text-sm font-medium {{ $isActive ? 'text-emerald-700 bg-emerald-50 font-semibold' : 'text-slate-700 hover:bg-slate-50' }}">
                {{ __($navItem['title']) }}
            </a>
        @endforeach
    </div>
</header>
