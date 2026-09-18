@php
    $profileRoute = config('nav.profile_route', 'profile.edit');
    $logoutRoute = config('nav.logout_route', 'logout');
    $isRtl = LaravelLocalization::getCurrentLocaleDirection() === 'rtl';

    $navSections = [
        [
            'title' => 'Overview',
            'items' => [
                [
                    'route' => 'dashboard',
                    'label' => 'Dashboard',
                    'active' => 'dashboard',
                    'icon' => '<svg class="h-5 w-5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6A2.25 2.25 0 0 1 6 3.75h2.25A2.25 2.25 0 0 1 10.5 6v2.25a2.25 2.25 0 0 1-2.25 2.25H6a2.25 2.25 0 0 1-2.25-2.25V6ZM3.75 15.75A2.25 2.25 0 0 1 6 13.5h2.25a2.25 2.25 0 0 1 2.25 2.25V18a2.25 2.25 0 0 1-2.25 2.25H6A2.25 2.25 0 0 1 3.75 18v-2.25ZM13.5 6a2.25 2.25 0 0 1 2.25-2.25H18A2.25 2.25 0 0 1 20.25 6v2.25A2.25 2.25 0 0 1 18 10.5h-2.25a2.25 2.25 0 0 1-2.25-2.25V6ZM13.5 15.75a2.25 2.25 0 0 1 2.25-2.25H18a2.25 2.25 0 0 1 2.25 2.25V18A2.25 2.25 0 0 1 18 20.25h-2.25A2.25 2.25 0 0 1 13.5 18v-2.25Z" /></svg>',
                ],
                [
                    'route' => 'premium.dashboard',
                    'label' => 'Premium Experience',
                    'active' => 'premium.*',
                    'icon' => '<svg class="h-5 w-5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M9.813 15.904 9 18.75l-.813-2.846a4.5 4.5 0 0 0-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 0 0 3.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 0 0 3.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 0 0-3.09 3.09ZM18.259 8.715 18 9.75l-.259-1.035a3.375 3.375 0 0 0-2.455-2.456L14.25 6l1.036-.259a3.375 3.375 0 0 0 2.455-2.456L18 2.25l.259 1.035a3.375 3.375 0 0 0 2.456 2.456L21.75 6l-1.035.259a3.375 3.375 0 0 0-2.456 2.456ZM16.894 20.567 16.5 21.75l-.394-1.183a2.25 2.25 0 0 0-1.423-1.423L13.5 18.75l1.183-.394a2.25 2.25 0 0 0 1.423-1.423l.394-1.183.394 1.183a2.25 2.25 0 0 0 1.423 1.423l1.183.394-1.183.394a2.25 2.25 0 0 0-1.423 1.423Z" /></svg>',
                ],
            ],
        ],
        [
            'title' => 'Core Banking',
            'items' => [
                [
                    'route' => 'customers.index',
                    'label' => 'Customers',
                    'active' => 'customers.*',
                    'icon' => '<svg class="h-5 w-5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 0 0 2.625.372 9.337 9.337 0 0 0 4.121-.952 4.125 4.125 0 0 0-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 0 1 8.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0 1 11.964-3.07M12 6.375a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0Zm8.25 2.25a2.625 2.625 0 1 1-5.25 0 2.625 2.625 0 0 1 5.25 0Z" /></svg>',
                ],
                [
                    'route' => 'accounts.index',
                    'label' => 'Accounts',
                    'active' => 'accounts.*',
                    'icon' => '<svg class="h-5 w-5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M12 21v-8.25M15.75 21v-8.25M8.25 21v-8.25M3 9l9-6 9 6m-1.5 12V10.5m-15 10.5V10.5M3 21h18M3 10.5h18" /></svg>',
                ],
                [
                    'route' => 'branches.index',
                    'label' => 'Branches',
                    'active' => 'branches.*',
                    'icon' => '<svg class="h-5 w-5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 21h19.5m-18-18v18m10.5-18v18m6-13.5V21M6.75 6.75h.75m-.75 3h.75m-.75 3h.75m3-6h.75m-.75 3h.75m-.75 3h.75M6.75 21v-3.75a1.5 1.5 0 0 1 1.5-1.5h1.5a1.5 1.5 0 0 1 1.5 1.5V21" /></svg>',
                ],
                [
                    'route' => 'transactions.index',
                    'label' => 'Transactions',
                    'active' => 'transactions.*',
                    'icon' => '<svg class="h-5 w-5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M7.5 21 3 16.5m0 0L7.5 12M3 16.5h13.5m0-13.5L21 7.5m0 0L16.5 12M21 7.5H7.5" /></svg>',
                ],
                [
                    'route' => 'transfers.index',
                    'label' => 'Transfers',
                    'active' => 'transfers.*',
                    'icon' => '<svg class="h-5 w-5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M6 12 3.269 3.125A59.769 59.769 0 0 1 21.485 12 59.768 59.768 0 0 1 3.27 20.875L5.999 12Zm0 0h7.5" /></svg>',
                ],
                [
                    'route' => 'cards.index',
                    'label' => 'Cards',
                    'active' => 'cards.*',
                    'icon' => '<svg class="h-5 w-5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 8.25h19.5M2.25 9h19.5m-16.5 5.25h6m-6 2.25h3m-6.75 3h18a2.25 2.25 0 0 0 2.25-2.25V6.75A2.25 2.25 0 0 0 19.5 4.5h-18A2.25 2.25 0 0 0 .75 6.75v10.5A2.25 2.25 0 0 0 3 19.5Z" /></svg>',
                ],
                [
                    'route' => 'loans.index',
                    'label' => 'Loans',
                    'active' => 'loans.*',
                    'icon' => '<svg class="h-5 w-5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18.75a60.07 60.07 0 0 1 15.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 0 1 3 6H2.25m0 0v8.25m0-8.25c0-.414.336-.75.75-.75h14.25c.414 0 .75.336.75.75v8.25m-15.75 0h15.75m-15.75 0c0 .414.336.75.75.75h14.25c.414 0 .75-.336.75-.75v-8.25M9 12a3 3 0 1 0 6 0 3 3 0 0 0-6 0Z" /></svg>',
                ],
                [
                    'route' => 'installments.index',
                    'label' => 'Installments',
                    'active' => 'installments.*',
                    'icon' => '<svg class="h-5 w-5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 11.25v7.5m-9-6h.008v.008H12v-.008ZM12 15h.008v.008H12V15Zm0 2.25h.008v.008H12v-.008ZM9.75 15h.008v.008H9.75V15Zm0 2.25h.008v.008H9.75v-.008ZM7.5 15h.008v.008H7.5V15Zm0 2.25h.008v.008H7.5v-.008Zm6.75-4.5h.008v.008h-.008v-.008Zm0 2.25h.008v.008h-.008V15Zm0 2.25h.008v.008h-.008v-.008Zm2.25-4.5h.008v.008H16.5v-.008Zm0 2.25h.008v.008H16.5V15Z" /></svg>',
                ],
                [
                    'route' => 'bills-payments.index',
                    'label' => 'Bill Payments',
                    'active' => 'bills-payments.*',
                    'icon' => '<svg class="h-5 w-5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18.75a60.07 60.07 0 0 1 15.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 0 1 3 6H2.25m0 0v8.25m0-8.25c0-.414.336-.75.75-.75h14.25c.414 0 .75.336.75.75v8.25m-15.75 0h15.75" /></svg>',
                ],
                [
                    'route' => 'statements.index',
                    'label' => 'Statements',
                    'active' => 'statements.*',
                    'icon' => '<svg class="h-5 w-5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" /></svg>',
                ],
            ],
        ],
        [
            'title' => 'Operations & Support',
            'items' => [
                [
                    'route' => 'cash-management.index',
                    'label' => 'Cash Operations',
                    'active' => 'cash-management.*',
                    'icon' => '<svg class="h-5 w-5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m-3-2.818.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" /></svg>',
                ],
                [
                    'route' => 'customerService.index',
                    'label' => 'Customer Service',
                    'active' => 'customerService.*',
                    'icon' => '<svg class="h-5 w-5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M20.25 8.511c.884.284 1.5 1.128 1.5 2.097v4.286c0 1.136-.847 2.1-1.98 2.193-.34.027-.68.052-1.02.072v3.091l-3.6-3.091c-.482-.016-.96-.046-1.435-.091a9.23 9.23 0 0 1-5.715-2.615m7.25-5.85c-.328-.01-.659-.015-.99-.015-4.97 0-9 3.582-9 8 0 1.28.344 2.482.956 3.513l-1.956 4.487 4.743-1.423A8.93 8.93 0 0 0 8 19c.331 0 .662-.005.99-.015" /></svg>',
                ],
                [
                    'route' => 'calculators.index',
                    'label' => 'Banking Calculators',
                    'active' => 'calculators.*',
                    'icon' => '<svg class="h-5 w-5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 15.75V18m-7.5-6.75h.008v.008H8.25v-.008Zm0 2.25h.008v.008H8.25V15Zm0 2.25h.008v.008H8.25v-.008Zm2.25-4.5h.008v.008H10.5v-.008Zm0 2.25h.008v.008H10.5V15Zm0 2.25h.008v.008H10.5v-.008Zm2.25-4.5h.008v.008H12.75v-.008Zm0 2.25h.008v.008H12.75V15Zm0 2.25h.008v.008H12.75v-.008Zm2.25-4.5h.008v.008H15v-.008Zm0 2.25h.008v.008H15V15ZM6 18.75a2.25 2.25 0 0 0 2.25 2.25h7.5A2.25 2.25 0 0 0 18 18.75V5.25A2.25 2.25 0 0 0 15.75 3h-7.5A2.25 2.25 0 0 0 6 5.25v13.5Z" /></svg>',
                ],
            ],
        ],
        [
            'title' => 'Products & Admin',
            'items' => [
                [
                    'route' => 'products.index',
                    'label' => 'Products',
                    'active' => 'products.*',
                    'icon' => '<svg class="h-5 w-5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="m20.25 7.5-.625 10.632a2.25 2.25 0 0 1-2.247 2.118H6.622a2.25 2.25 0 0 1-2.247-2.118L3.75 7.5M10 11.25h4M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125Z" /></svg>',
                ],
                [
                    'route' => 'account-types.index',
                    'label' => 'Account Types',
                    'active' => 'account-types.*',
                    'icon' => '<svg class="h-5 w-5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M6 6.878V6a2.25 2.25 0 0 1 2.25-2.25h7.5A2.25 2.25 0 0 1 18 6v.878m-12 0c.235-.083.487-.128.75-.128h10.5c.263 0 .515.045.75.128m-12 0A2.25 2.25 0 0 0 4.5 9v.878m13.5-3A2.25 2.25 0 0 1 19.5 9v.878m-15 0a2.246 2.246 0 0 0-.75.128m15.75 0c-.235-.083-.487-.128-.75-.128m-14.25 0A2.25 2.25 0 0 0 3 12v6.75A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V12a2.25 2.25 0 0 0-1.5-2.122" /></svg>',
                ],
                [
                    'route' => 'loan-types.index',
                    'label' => 'Loan Types',
                    'active' => 'loan-types.*',
                    'icon' => '<svg class="h-5 w-5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 6h9.75M10.5 6a1.5 1.5 0 1 1-3 0m3 0a1.5 1.5 0 1 0-3 0M3.75 6H7.5m3 12h9.75m-9.75 0a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m-3.75 0H7.5m9-6h3.75m-3.75 0a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m-9.75 0h9.75" /></svg>',
                ],
                [
                    'route' => 'users.index',
                    'label' => 'Users',
                    'active' => 'users.*',
                    'ability' => 'canManageUsers',
                    'icon' => '<svg class="h-5 w-5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75m-3-7.036A11.959 11.959 0 0 1 3.598 6 11.99 11.99 0 0 0 3 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285Z" /></svg>',
                ],
            ],
        ],
        [
            'title' => 'System & Analytics',
            'items' => [
                [
                    'route' => 'notifications.index',
                    'label' => 'Notifications',
                    'active' => 'notifications.*',
                    'icon' => '<svg class="h-5 w-5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 0 0 5.454-1.31A8.967 8.967 0 0 1 18 9.75V9A6 6 0 0 0 6 9v.75a8.967 8.967 0 0 1-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 0 1-5.714 0m5.714 0a3 3 0 1 1-5.714 0" /></svg>',
                ],
                [
                    'route' => 'reports.index',
                    'label' => 'Reports',
                    'active' => 'reports.*',
                    'icon' => '<svg class="h-5 w-5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 0 1 3 19.875v-6.75ZM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 0 1-1.125-1.125V8.625ZM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 0 1-1.125-1.125V4.125Z" /></svg>',
                ],
                [
                    'route' => 'security.index',
                    'label' => 'Security',
                    'active' => 'security.*',
                    'icon' => '<svg class="h-5 w-5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 1 0-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 0 0 2.25-2.25v-6.75a2.25 2.25 0 0 0-2.25-2.25H6.75a2.25 2.25 0 0 0-2.25 2.25v6.75a2.25 2.25 0 0 0 2.25 2.25Z" /></svg>',
                ],
            ],
        ],
    ];
@endphp

<!-- ========================================================================= -->
<!-- DESKTOP FIXED SIDEBAR                                                     -->
<!-- ========================================================================= -->
<aside class="hidden lg:fixed lg:inset-y-0 lg:start-0 lg:z-50 lg:flex lg:w-72 lg:flex-col bg-slate-950 border-e border-slate-800/80 shadow-2xl">
    <!-- Brand Header -->
    <div class="flex h-20 shrink-0 items-center justify-between px-6 border-b border-slate-800/80 bg-slate-950">
        <a href="{{ route('dashboard') }}" class="flex items-center gap-3.5 group">
            <span class="flex h-11 w-11 items-center justify-center rounded-xl bg-gradient-to-br from-emerald-400 to-teal-600 text-slate-950 font-bold text-base shadow-lg shadow-emerald-500/20 ring-2 ring-emerald-400/20 group-hover:scale-105 transition duration-200">
                {{ config('bank.short_name', 'BS') }}
            </span>
            <div class="min-w-0">
                <span class="block text-base font-bold text-white tracking-tight leading-5 truncate">{{ config('bank.name') }}</span>
                <span class="block text-[11px] font-medium text-emerald-400 leading-4 truncate tracking-wide">{{ __(config('bank.tagline')) }}</span>
            </div>
        </a>
    </div>

    <!-- Navigation Menu (Scrollable) -->
    <nav class="flex-1 overflow-y-auto px-4 py-5 space-y-6 scrollbar-thin scrollbar-thumb-slate-800 scrollbar-track-transparent">
        @foreach ($navSections as $section)
            @php
                $visibleItems = array_filter($section['items'], function ($item) {
                    return Route::has($item['route']) && (empty($item['ability']) || (Auth::check() && call_user_func([Auth::user(), $item['ability']])));
                });
            @endphp

            @if (count($visibleItems) > 0)
                <div>
                    <p class="px-3 text-[11px] font-semibold uppercase tracking-widest text-slate-500">
                        {{ __($section['title']) }}
                    </p>

                    <div class="mt-2 space-y-1">
                        @foreach ($visibleItems as $item)
                            @php $isActive = request()->routeIs($item['active']); @endphp
                            <a href="{{ route($item['route']) }}"
                               class="group flex items-center gap-3 rounded-xl px-3.5 py-2.5 text-sm font-medium transition duration-150 {{ $isActive ? 'bg-emerald-500/10 text-emerald-400 shadow-sm ring-1 ring-emerald-400/20 font-semibold' : 'text-slate-400 hover:bg-slate-900 hover:text-slate-200' }}">
                                <span class="{{ $isActive ? 'text-emerald-400' : 'text-slate-500 group-hover:text-slate-300' }}">
                                    {!! $item['icon'] !!}
                                </span>
                                <span class="truncate">{{ __($item['label']) }}</span>
                                @if ($isActive)
                                    <span class="ms-auto h-1.5 w-1.5 rounded-full bg-emerald-400 shadow-sm shadow-emerald-400"></span>
                                @endif
                            </a>
                        @endforeach
                    </div>
                </div>
            @endif
        @endforeach
    </nav>

    <!-- Bottom Sidebar Section: User Info & Fast Language Switcher -->
    <div class="shrink-0 border-t border-slate-800/80 p-4 bg-slate-950/60">
        <!-- Language Switcher Pills -->
        <div class="mb-3 flex items-center justify-between rounded-xl bg-slate-900/90 p-1.5 ring-1 ring-slate-800">
            @foreach(LaravelLocalization::getSupportedLocales() as $localeCode => $properties)
                @php $isCurrent = LaravelLocalization::getCurrentLocale() === $localeCode; @endphp
                <a href="{{ LaravelLocalization::getLocalizedURL($localeCode, null, [], true) }}"
                   class="flex-1 text-center py-1.5 px-2 rounded-lg text-xs font-semibold transition duration-150 {{ $isCurrent ? 'bg-emerald-500 text-slate-950 shadow-sm shadow-emerald-500/20' : 'text-slate-400 hover:text-white' }}">
                    {{ $properties['native'] }}
                </a>
            @endforeach
        </div>

        <!-- User Profile Card -->
        <div class="flex items-center justify-between rounded-xl bg-slate-900/50 p-2.5 ring-1 ring-slate-800/60">
            <div class="flex items-center gap-3 min-w-0">
                <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-emerald-500/20 text-emerald-300 font-bold text-sm ring-1 ring-emerald-500/30">
                    {{ Str::upper(Str::substr(Auth::user()->name, 0, 1)) }}
                </span>
                <div class="min-w-0">
                    <p class="text-xs font-semibold text-white truncate leading-4">{{ Auth::user()->name }}</p>
                    <p class="text-[11px] font-medium text-slate-400 truncate leading-3 mt-1">{{ Auth::user()->role?->label() ?? __('User') }}</p>
                </div>
            </div>

            <div class="flex items-center gap-1">
                <a href="{{ route($profileRoute) }}" class="rounded-lg p-1.5 text-slate-400 hover:bg-slate-800 hover:text-emerald-400 transition" title="{{ __('Profile') }}">
                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z" /></svg>
                </a>
                <form method="POST" action="{{ route($logoutRoute) }}">
                    @csrf
                    <button type="submit" class="rounded-lg p-1.5 text-slate-400 hover:bg-slate-800 hover:text-red-400 transition" title="{{ __('Log Out') }}">
                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0 0 13.5 3h-6a2.25 2.25 0 0 0-2.25 2.25v13.5A2.25 2.25 0 0 0 7.5 21h6a2.25 2.25 0 0 0 2.25-2.25V15m3 0 3-3m0 0-3-3m3 3H9" /></svg>
                    </button>
                </form>
            </div>
        </div>
    </div>
</aside>

<!-- ========================================================================= -->
<!-- MOBILE RESPONSIVE DRAWER (Off-Canvas)                                     -->
<!-- ========================================================================= -->
<div x-show="sidebarOpen"
     x-cloak
     class="relative z-50 lg:hidden"
     role="dialog"
     aria-modal="true">

    <!-- Backdrop Overlay -->
    <div x-show="sidebarOpen"
         x-transition:enter="transition-opacity ease-linear duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition-opacity ease-linear duration-300"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         @click="sidebarOpen = false"
         class="fixed inset-0 bg-slate-950/80 backdrop-blur-sm"></div>

    <!-- Drawer Panel -->
    <div class="fixed inset-0 flex">
        <div x-show="sidebarOpen"
             x-transition:enter="transition ease-in-out duration-300 transform"
             x-transition:enter-start="{{ $isRtl ? 'translate-x-full' : '-translate-x-full' }}"
             x-transition:enter-end="translate-x-0"
             x-transition:leave="transition ease-in-out duration-300 transform"
             x-transition:leave-start="translate-x-0"
             x-transition:leave-end="{{ $isRtl ? 'translate-x-full' : '-translate-x-full' }}"
             class="relative flex w-full max-w-xs flex-1 flex-col bg-slate-950 pt-5 pb-4 border-e border-slate-800 shadow-2xl">

            <!-- Close Button -->
            <div class="absolute top-4 end-4">
                <button @click="sidebarOpen = false" type="button" class="flex h-9 w-9 items-center justify-center rounded-lg bg-slate-900 text-slate-400 hover:text-white ring-1 ring-slate-800">
                    <span class="sr-only">{{ __('Close navigation') }}</span>
                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
                </button>
            </div>

            <!-- Mobile Brand Header -->
            <div class="flex shrink-0 items-center px-6">
                <a href="{{ route('dashboard') }}" class="flex items-center gap-3">
                    <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-gradient-to-br from-emerald-400 to-teal-600 text-slate-950 font-bold text-sm shadow-md">
                        {{ config('bank.short_name', 'BS') }}
                    </span>
                    <div>
                        <span class="block text-base font-bold text-white">{{ config('bank.name') }}</span>
                        <span class="block text-xs font-medium text-emerald-400">{{ __(config('bank.tagline')) }}</span>
                    </div>
                </a>
            </div>

            <!-- Mobile Nav Links -->
            <nav class="mt-6 flex-1 overflow-y-auto px-4 space-y-6">
                @foreach ($navSections as $section)
                    @php
                        $visibleItems = array_filter($section['items'], function ($item) {
                            return Route::has($item['route']) && (empty($item['ability']) || (Auth::check() && call_user_func([Auth::user(), $item['ability']])));
                        });
                    @endphp

                    @if (count($visibleItems) > 0)
                        <div>
                            <p class="px-3 text-[11px] font-semibold uppercase tracking-widest text-slate-500">
                                {{ __($section['title']) }}
                            </p>
                            <div class="mt-2 space-y-1">
                                @foreach ($visibleItems as $item)
                                    @php $isActive = request()->routeIs($item['active']); @endphp
                                    <a href="{{ route($item['route']) }}"
                                       @click="sidebarOpen = false"
                                       class="group flex items-center gap-3 rounded-xl px-3.5 py-2.5 text-sm font-medium transition duration-150 {{ $isActive ? 'bg-emerald-500/10 text-emerald-400 font-semibold ring-1 ring-emerald-400/20' : 'text-slate-400 hover:bg-slate-900 hover:text-slate-200' }}">
                                        <span class="{{ $isActive ? 'text-emerald-400' : 'text-slate-500 group-hover:text-slate-300' }}">
                                            {!! $item['icon'] !!}
                                        </span>
                                        <span>{{ __($item['label']) }}</span>
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    @endif
                @endforeach
            </nav>

            <!-- Mobile Language & User Card -->
            <div class="shrink-0 border-t border-slate-800 p-4 space-y-3">
                <div class="flex gap-2">
                    @foreach(LaravelLocalization::getSupportedLocales() as $localeCode => $properties)
                        @php $isCurrent = LaravelLocalization::getCurrentLocale() === $localeCode; @endphp
                        <a href="{{ LaravelLocalization::getLocalizedURL($localeCode, null, [], true) }}"
                           class="flex-1 text-center py-2 px-2 rounded-lg text-xs font-semibold {{ $isCurrent ? 'bg-emerald-500 text-slate-950 font-bold' : 'bg-slate-900 text-slate-400 ring-1 ring-slate-800' }}">
                            {{ $properties['native'] }}
                        </a>
                    @endforeach
                </div>

                <div class="flex items-center justify-between pt-2">
                    <div class="flex items-center gap-2.5 min-w-0">
                        <span class="flex h-8 w-8 items-center justify-center rounded-full bg-emerald-500/20 text-emerald-300 font-bold text-xs">
                            {{ Str::upper(Str::substr(Auth::user()->name, 0, 1)) }}
                        </span>
                        <div class="min-w-0">
                            <p class="text-xs font-semibold text-white truncate">{{ Auth::user()->name }}</p>
                            <p class="text-[10px] text-slate-400 truncate">{{ Auth::user()->role?->label() ?? __('User') }}</p>
                        </div>
                    </div>
                    <form method="POST" action="{{ route($logoutRoute) }}">
                        @csrf
                        <button type="submit" class="text-xs text-red-400 hover:text-red-300 font-medium px-2 py-1">
                            {{ __('Log Out') }}
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
