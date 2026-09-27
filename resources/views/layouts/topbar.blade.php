@php
    $profileRoute = config('nav.profile_route', 'profile.edit');
    $logoutRoute = config('nav.logout_route', 'logout');
    $allBranches = \App\Modules\Branches\Models\Branch::orderBy('name')->get(['id', 'code', 'name', 'status']);
    $activeBranchId = session('current_branch_id');
    $currentBranch = $activeBranchId ? $allBranches->firstWhere('id', $activeBranchId) : null;
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
        <!-- Branch Switcher Dropdown & Descriptor -->
        <div class="flex items-center gap-3">
            <!-- Branch Dropdown -->
            <div class="relative" x-data="{ open: false }" @click.outside="open = false" @close.stop="open = false">
                <button @click="open = !open"
                        type="button"
                        class="inline-flex items-center gap-2 rounded-lg border border-slate-200 bg-slate-50/90 px-3 py-1.5 text-xs font-semibold text-slate-800 hover:bg-slate-100 hover:border-emerald-300 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 transition-all shadow-xs">
                    <svg class="h-4 w-4 text-emerald-600 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 21h19.5m-18-18v18m10.5-18v18m6-13.5V21M6.75 6.75h.75m-.75 3h.75m-.75 3h.75m3-6h.75m-.75 3h.75m-.75 3h.75M6.75 21v-3.75a1.5 1.5 0 0 1 1.5-1.5h1.5a1.5 1.5 0 0 1 1.5 1.5V21" />
                    </svg>
                    @if ($currentBranch)
                        <span class="h-2 w-2 rounded-full bg-emerald-500 ring-2 ring-emerald-200 shrink-0"></span>
                        <span class="max-w-[120px] sm:max-w-[160px] truncate text-slate-900 font-bold">{{ $currentBranch->name }}</span>
                        <span class="font-mono text-[10px] text-emerald-700 bg-emerald-100/80 px-1 rounded">{{ $currentBranch->code }}</span>
                    @else
                        <span class="h-2 w-2 rounded-full bg-slate-400 shrink-0"></span>
                        <span class="text-slate-700">{{ __('All Branches') }}</span>
                    @endif
                    <svg class="h-3.5 w-3.5 text-slate-400 transition-transform duration-200 shrink-0" :class="{ 'rotate-180': open }" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5" />
                    </svg>
                </button>

                <!-- Dropdown Menu -->
                <div x-show="open"
                     x-cloak
                     x-transition:enter="transition ease-out duration-150"
                     x-transition:enter-start="opacity-0 scale-95"
                     x-transition:enter-end="opacity-100 scale-100"
                     x-transition:leave="transition ease-in duration-100"
                     x-transition:leave-start="opacity-100 scale-100"
                     x-transition:leave-end="opacity-0 scale-95"
                     class="absolute start-0 z-50 mt-2 w-72 sm:w-80 rounded-xl border border-slate-200 bg-white p-2 shadow-xl ring-1 ring-slate-950/5 divide-y divide-slate-100">
                    
                    <div class="px-3 py-2">
                        <div class="flex items-center justify-between">
                            <p class="text-xs font-bold text-slate-900">{{ __('System Branches') }}</p>
                            <span class="text-[11px] font-medium text-slate-500 bg-slate-100 px-2 py-0.5 rounded-full">{{ $allBranches->count() }} {{ __('branches') }}</span>
                        </div>
                        <p class="text-[11px] text-slate-500 mt-0.5">{{ __('Select a branch to switch context or view') }}</p>
                    </div>

                    <!-- Options list -->
                    <div class="py-1 max-h-64 overflow-y-auto space-y-1">
                        <!-- All Branches option -->
                        <a href="{{ route('branches.switch', ['redirect' => 'index']) }}"
                           class="flex items-center justify-between gap-2 px-3 py-2 rounded-lg text-xs font-medium transition {{ !$currentBranch ? 'bg-emerald-50 text-emerald-900 font-semibold ring-1 ring-emerald-200' : 'text-slate-700 hover:bg-slate-50 hover:text-slate-950' }}">
                            <div class="flex items-center gap-2.5">
                                <span class="flex h-7 w-7 items-center justify-center rounded-md {{ !$currentBranch ? 'bg-emerald-600 text-white' : 'bg-slate-100 text-slate-600' }}">
                                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6A2.25 2.25 0 0 1 6 3.75h2.25A2.25 2.25 0 0 1 10.5 6v2.25a2.25 2.25 0 0 1-2.25 2.25H6A2.25 2.25 0 0 1 3.75 18v-2.25ZM13.5 6a2.25 2.25 0 0 1 2.25-2.25H18A2.25 2.25 0 0 1 20.25 6v2.25A2.25 2.25 0 0 1 18 10.5h-2.25a2.25 2.25 0 0 1-2.25-2.25V6ZM13.5 15.75a2.25 2.25 0 0 1 2.25-2.25H18a2.25 2.25 0 0 1 2.25 2.25V18A2.25 2.25 0 0 1 18 20.25h-2.25A2.25 2.25 0 0 1 13.5 18v-2.25Z" />
                                    </svg>
                                </span>
                                <div>
                                    <p class="leading-tight">{{ __('All Branches') }}</p>
                                    <p class="text-[10px] text-slate-400 font-normal">{{ __('General system overview') }}</p>
                                </div>
                            </div>
                            @if (!$currentBranch)
                                <svg class="h-4 w-4 text-emerald-600" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M16.704 4.153a.75.75 0 0 1 .143 1.052l-8 10.5a.75.75 0 0 1-1.127.075l-4.5-4.5a.75.75 0 0 1 1.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 0 1 1.05-.143Z" clip-rule="evenodd" />
                                </svg>
                            @endif
                        </a>

                        <!-- Branch items -->
                        @foreach ($allBranches as $branchItem)
                            @php $isCurrent = $currentBranch && $currentBranch->id === $branchItem->id; @endphp
                            <div class="flex items-center justify-between gap-2 px-3 py-2 rounded-lg text-xs font-medium transition {{ $isCurrent ? 'bg-emerald-50 text-emerald-950 ring-1 ring-emerald-200' : 'text-slate-700 hover:bg-slate-50' }}">
                                <a href="{{ route('branches.switch', ['branch' => $branchItem->id, 'redirect' => 'show']) }}"
                                   class="flex-1 min-w-0 flex items-center gap-2.5 group">
                                    <span class="flex h-7 w-7 shrink-0 items-center justify-center rounded-md {{ $isCurrent ? 'bg-emerald-600 text-white' : 'bg-slate-100 text-slate-600 group-hover:bg-slate-200' }}">
                                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 21h19.5m-18-18v18m10.5-18v18m6-13.5V21M6.75 6.75h.75m-.75 3h.75m-.75 3h.75m3-6h.75m-.75 3h.75m-.75 3h.75M6.75 21v-3.75a1.5 1.5 0 0 1 1.5-1.5h1.5a1.5 1.5 0 0 1 1.5 1.5V21" />
                                        </svg>
                                    </span>
                                    <div class="min-w-0 flex-1">
                                        <div class="flex items-center gap-1.5">
                                            <p class="font-semibold text-slate-900 truncate">{{ $branchItem->name }}</p>
                                            <span class="font-mono text-[10px] text-slate-500 bg-slate-100 px-1 rounded">{{ $branchItem->code }}</span>
                                        </div>
                                        <div class="flex items-center gap-2 mt-0.5">
                                            <span class="inline-flex items-center gap-1 text-[10px] {{ $branchItem->status->value === 'open' ? 'text-emerald-700' : 'text-slate-400' }}">
                                                <span class="h-1.5 w-1.5 rounded-full {{ $branchItem->status->value === 'open' ? 'bg-emerald-500' : 'bg-slate-300' }}"></span>
                                                {{ $branchItem->status->label() }}
                                            </span>
                                        </div>
                                    </div>
                                </a>

                                <div class="flex items-center gap-1 shrink-0">
                                    @if ($isCurrent)
                                        <span class="text-[10px] font-semibold text-emerald-700 bg-emerald-100/80 px-2 py-0.5 rounded-full">
                                            {{ __('Active') }}
                                        </span>
                                    @else
                                        <a href="{{ route('branches.switch', ['branch' => $branchItem->id, 'redirect' => 'show']) }}"
                                           class="text-[11px] font-medium text-slate-500 hover:text-emerald-700 px-1.5 py-0.5 rounded hover:bg-slate-100"
                                           title="{{ __('Switch & View') }}">
                                            {{ __('Switch') }}
                                        </a>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <!-- Bottom Quick Links -->
                    <div class="pt-2 flex items-center justify-between text-xs px-2">
                        <a href="{{ route('branches.index') }}" class="font-semibold text-emerald-700 hover:text-emerald-800 hover:underline">
                            {{ __('View all branches') }} &rarr;
                        </a>
                        <a href="{{ route('branches.create') }}" class="font-semibold text-slate-700 hover:text-slate-950 inline-flex items-center gap-1">
                            <span>+</span> {{ __('New Branch') }}
                        </a>
                    </div>
                </div>
            </div>

            <!-- Descriptor Badge (Desktop) -->
            <span class="hidden md:inline-flex items-center gap-1.5 rounded-full bg-emerald-50 px-3 py-1 text-xs font-semibold text-emerald-700 ring-1 ring-inset ring-emerald-600/20">
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
