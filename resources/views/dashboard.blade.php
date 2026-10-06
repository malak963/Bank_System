<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                @php
                    $hour = now()->hour;
                    $greeting = $hour < 12 ? __('Good morning') : ($hour < 17 ? __('Good afternoon') : __('Good evening'));
                @endphp
                <h1 class="text-2xl font-bold tracking-tight text-slate-900 dark:text-white">{{ $greeting }}، {{ Auth::user()->name }}</h1>
                <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">{{ __('Executive overview of bank liquidity, clients, and loan operations.') }}</p>
            </div>
            <div class="flex items-center gap-3">
                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs font-medium text-slate-600 dark:text-slate-300 shadow-xs">
                    <svg class="h-3.5 w-3.5 text-emerald-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                    <span>{{ now()->locale(app()->getLocale())->translatedFormat('l، j F Y') }}</span>
                </span>
                <a href="{{ route('loans.create') }}" class="btn btn-primary text-xs shadow-xs font-semibold">
                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                    </svg>
                    <span>{{ __('New Loan') }}</span>
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-6 space-y-6 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        @if (! $databaseReady)
            <div class="flex items-center gap-3 rounded-xl border border-amber-200 bg-amber-50 dark:bg-amber-950/40 dark:border-amber-900 p-4 text-xs text-amber-900 dark:text-amber-200">
                <svg class="h-5 w-5 text-amber-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
                <div>
                    <span class="font-bold">{{ __('Banking data is initializing:') }}</span>
                    <span>{{ __('Run database migrations to view live portfolio metrics.') }}</span>
                </div>
            </div>
        @endif

        {{-- 1. بطاقات المؤشرات الرئيسية الأربعة --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            {{-- قاعدة العملاء --}}
            <div class="bank-card p-5 hover:border-emerald-300 transition">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-semibold text-slate-500 dark:text-slate-400">{{ __('Customer Base') }}</span>
                    <span class="p-2 rounded-lg bg-emerald-50 dark:bg-emerald-950/50 text-emerald-600">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                    </span>
                </div>
                <div class="mt-3">
                    <div class="text-2xl font-bold text-slate-900 dark:text-white font-mono" dir="ltr">
                        {{ number_format($metrics['customers']) }}
                    </div>
                    <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">{{ __('Registered active customers') }}</p>
                </div>
            </div>

            {{-- السيولة النقدية المتاحة --}}
            <div class="bank-card p-5 hover:border-emerald-300 transition">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-semibold text-slate-500 dark:text-slate-400">{{ __('Total Liquidity') }}</span>
                    <span class="p-2 rounded-lg bg-sky-50 dark:bg-sky-950/50 text-sky-600">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                        </svg>
                    </span>
                </div>
                <div class="mt-3">
                    <div class="text-2xl font-bold text-slate-900 dark:text-white font-mono" dir="ltr">
                        {{ number_format($metrics['available_balance'], 2) }}
                    </div>
                    <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">
                        {{ __(config('bank.currency', 'USD')) }} &bull; {{ number_format($metrics['open_accounts']) }} {{ __('open accounts') }}
                    </p>
                </div>
            </div>

            {{-- محفظة القروض والائتمان --}}
            <div class="bank-card p-5 hover:border-emerald-300 transition">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-semibold text-slate-500 dark:text-slate-400">{{ __('Loan Exposure') }}</span>
                    <span class="p-2 rounded-lg bg-violet-50 dark:bg-violet-950/50 text-violet-600">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                    </span>
                </div>
                <div class="mt-3">
                    <div class="text-2xl font-bold text-slate-900 dark:text-white font-mono" dir="ltr">
                        {{ number_format($metrics['outstanding_principal'], 2) }}
                    </div>
                    <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">
                        {{ number_format($metrics['active_loans']) }} {{ __('active loans') }} &bull; {{ number_format($metrics['pending_loans']) }} {{ __('pending') }}
                    </p>
                </div>
            </div>

            {{-- التحصيلات الشهرية --}}
            <div class="bank-card p-5 hover:border-emerald-300 transition">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-semibold text-slate-500 dark:text-slate-400">{{ __('Monthly Collections') }}</span>
                    <span class="p-2 rounded-lg bg-emerald-50 dark:bg-emerald-950/50 text-emerald-600">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </span>
                </div>
                <div class="mt-3">
                    <div class="text-2xl font-bold text-emerald-700 dark:text-emerald-400 font-mono" dir="ltr">
                        {{ number_format($metrics['collected_this_month'], 2) }}
                    </div>
                    <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">
                        {{ __('Due:') }} <span dir="ltr" class="font-mono font-medium text-slate-700 dark:text-slate-300">{{ number_format($metrics['due_this_month'], 2) }}</span> {{ __(config('bank.currency', 'USD')) }}
                    </p>
                </div>
            </div>
        </div>

        {{-- 2. القسم الوسطي: جدول القروض ومتابعة التحصيلات --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            {{-- العمود الأيسر: أحدث طلبات القروض --}}
            <div class="lg:col-span-2 bank-card">
                <div class="bank-card-header flex items-center justify-between p-4 border-b border-slate-200 dark:border-slate-700">
                    <div>
                        <h2 class="text-sm font-bold text-slate-900 dark:text-white">{{ __('Recent Loan Applications') }}</h2>
                        <p class="text-xs text-slate-500">{{ __('Latest credit requests and status movement') }}</p>
                    </div>
                    <a href="{{ route('loans.index') }}" class="text-xs font-semibold text-emerald-700 dark:text-emerald-400 hover:underline">
                        {{ __('View all') }} &rarr;
                    </a>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-xs">
                        <thead class="bg-slate-50 dark:bg-slate-800 border-b border-slate-200 dark:border-slate-700">
                            <tr>
                                <th class="px-4 py-3 text-start font-semibold text-slate-600 dark:text-slate-300">{{ __('Reference') }}</th>
                                <th class="px-4 py-3 text-start font-semibold text-slate-600 dark:text-slate-300">{{ __('Customer') }}</th>
                                <th class="px-4 py-3 text-end font-semibold text-slate-600 dark:text-slate-300">{{ __('Amount') }}</th>
                                <th class="px-4 py-3 text-end font-semibold text-slate-600 dark:text-slate-300">{{ __('Status') }}</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                            @forelse ($recentLoans as $loan)
                                @php
                                    $loanStatusClass = match ($loan->status->value) {
                                        'active', 'disbursed' => 'bg-sky-50 text-sky-700 border-sky-200',
                                        'approved' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                                        'rejected', 'defaulted' => 'bg-rose-50 text-rose-700 border-rose-200',
                                        'paid_off' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                                        default => 'bg-amber-50 text-amber-700 border-amber-200'
                                    };
                                @endphp
                                <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/50 transition">
                                    <td class="px-4 py-3 font-mono font-medium">
                                        <a href="{{ route('loans.show', $loan) }}" class="text-emerald-700 dark:text-emerald-400 hover:underline" dir="ltr">
                                            {{ $loan->loan_reference }}
                                        </a>
                                        <span class="block text-[11px] text-slate-400">
                                            {{ $loan->created_at?->locale(app()->getLocale())->diffForHumans() }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3">
                                        <span class="font-medium text-slate-900 dark:text-white block">{{ $loan->customer?->full_name }}</span>
                                        <span class="text-[11px] text-slate-400">{{ $loan->loanType?->name }}</span>
                                    </td>
                                    <td class="px-4 py-3 text-end font-mono font-bold text-slate-900 dark:text-white" dir="ltr">
                                        {{ number_format((float) ($loan->approved_amount ?? $loan->requested_amount), 2) }} {{ __(config('bank.currency', 'USD')) }}
                                    </td>
                                    <td class="px-4 py-3 text-end">
                                        <span class="px-2 py-0.5 rounded text-[11px] font-semibold border {{ $loanStatusClass }}">
                                            {{ $loan->status->label() }}
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-4 py-10 text-center text-slate-400">
                                        <p>{{ __('No recent loans found.') }}</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- العمود الأيمن: متابعة الأقساط القادمة --}}
            <div class="bank-card">
                <div class="bank-card-header flex items-center justify-between p-4 border-b border-slate-200 dark:border-slate-700">
                    <div>
                        <h2 class="text-sm font-bold text-slate-900 dark:text-white">{{ __('Collection Watch') }}</h2>
                        <p class="text-xs text-slate-500">{{ __('Next installments due') }}</p>
                    </div>
                    <a href="{{ route('installments.index') }}" class="text-xs font-semibold text-emerald-700 dark:text-emerald-400 hover:underline">
                        {{ __('Open') }} &rarr;
                    </a>
                </div>

                <div class="divide-y divide-slate-100 dark:divide-slate-800">
                    @forelse ($upcomingInstallments as $installment)
                        @php $isOverdue = $installment->due_date?->isBefore(today()); @endphp
                        <div class="p-3.5 flex items-center justify-between gap-3 hover:bg-slate-50 dark:hover:bg-slate-800/50 transition text-xs">
                            <div class="min-w-0">
                                <a href="{{ route('loans.show', $installment->loan) }}" class="font-semibold text-slate-900 dark:text-white truncate block hover:text-emerald-700">
                                    {{ $installment->loan?->customer?->full_name }}
                                </a>
                                <span class="text-[11px] text-slate-400 font-mono" dir="ltr">
                                    {{ $installment->loan?->loan_reference }} · {{ __('Installment #') }}{{ $installment->installment_number }}
                                </span>
                            </div>
                            <div class="text-end shrink-0">
                                <span class="font-mono font-bold text-slate-900 dark:text-white block" dir="ltr">
                                    {{ number_format((float) $installment->remainingAmount(), 2) }} {{ __(config('bank.currency', 'USD')) }}
                                </span>
                                <span class="text-[11px] {{ $isOverdue ? 'text-rose-600 font-semibold' : 'text-slate-400' }}">
                                    {{ $isOverdue ? __('Overdue') : $installment->due_date?->format('Y-m-d') }}
                                </span>
                            </div>
                        </div>
                    @empty
                        <div class="p-8 text-center text-slate-400 text-xs">
                            <p>{{ __('No urgent installments.') }}</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        {{-- 3. الصف السفلي: توزيع المحفظة والعمليات المباشرة --}}
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            {{-- توزيع محفظة القروض --}}
            <div class="bank-card p-5">
                <h3 class="text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400 mb-4">
                    {{ __('Loan Portfolio Breakdown') }}
                </h3>
                @php $portfolioTotal = max(1, array_sum($portfolio)); @endphp
                <div class="space-y-3">
                    @foreach ([
                        ['active', __('Active'), 'bg-sky-500', $portfolio['active']],
                        ['pending', __('Pending Review'), 'bg-amber-500', $portfolio['pending']],
                        ['paid_off', __('Paid Off'), 'bg-emerald-500', $portfolio['paid_off']],
                        ['rejected', __('Rejected'), 'bg-rose-500', $portfolio['rejected']]
                    ] as [$key, $label, $color, $count])
                        <div>
                            <div class="flex items-center justify-between text-xs mb-1">
                                <span class="font-medium text-slate-700 dark:text-slate-300 flex items-center gap-2">
                                    <span class="w-2 h-2 rounded-full {{ $color }}"></span>
                                    {{ $label }}
                                </span>
                                <span class="font-mono font-bold text-slate-900 dark:text-white">{{ $count }}</span>
                            </div>
                            <div class="h-1.5 w-full bg-slate-100 dark:bg-slate-800 rounded-full overflow-hidden">
                                <div class="h-full rounded-full {{ $color }}" style="width: {{ min(100, ($count / $portfolioTotal) * 100) }}%"></div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- العمليات المصرفية المباشرة --}}
            <div class="bank-card p-5">
                <h3 class="text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400 mb-4">
                    {{ __('Direct Operations') }}
                </h3>
                <div class="grid grid-cols-2 gap-3">
                    <a href="{{ route('customers.create') }}" class="p-3 rounded-lg border border-slate-200 dark:border-slate-700 hover:border-emerald-300 hover:bg-emerald-50/50 dark:hover:bg-emerald-950/20 transition flex items-center gap-3">
                        <span class="p-2 rounded bg-emerald-50 dark:bg-emerald-950/50 text-emerald-600">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
                            </svg>
                        </span>
                        <div>
                            <span class="text-xs font-bold text-slate-900 dark:text-white block">{{ __('New Customer') }}</span>
                            <span class="text-[11px] text-slate-400">{{ __('Register customer file') }}</span>
                        </div>
                    </a>

                    <a href="{{ route('accounts.create') }}" class="p-3 rounded-lg border border-slate-200 dark:border-slate-700 hover:border-sky-300 hover:bg-sky-50/50 dark:hover:bg-sky-950/20 transition flex items-center gap-3">
                        <span class="p-2 rounded bg-sky-50 dark:bg-sky-950/50 text-sky-600">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                            </svg>
                        </span>
                        <div>
                            <span class="text-xs font-bold text-slate-900 dark:text-white block">{{ __('Open Account') }}</span>
                            <span class="text-[11px] text-slate-400">{{ __('Issue new bank account') }}</span>
                        </div>
                    </a>

                    <a href="{{ route('loans.index') }}" class="p-3 rounded-lg border border-slate-200 dark:border-slate-700 hover:border-violet-300 hover:bg-violet-50/50 dark:hover:bg-violet-950/20 transition flex items-center gap-3">
                        <span class="p-2 rounded bg-violet-50 dark:bg-violet-950/50 text-violet-600">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                            </svg>
                        </span>
                        <div>
                            <span class="text-xs font-bold text-slate-900 dark:text-white block">{{ __('Loan Pipeline') }}</span>
                            <span class="text-[11px] text-slate-400">{{ __('Manage all applications') }}</span>
                        </div>
                    </a>

                    <a href="{{ route('installments.index') }}" class="p-3 rounded-lg border border-slate-200 dark:border-slate-700 hover:border-amber-300 hover:bg-amber-50/50 dark:hover:bg-amber-950/20 transition flex items-center gap-3">
                        <span class="p-2 rounded bg-amber-50 dark:bg-amber-950/50 text-amber-600">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </span>
                        <div>
                            <span class="text-xs font-bold text-slate-900 dark:text-white block">{{ __('Collections') }}</span>
                            <span class="text-[11px] text-slate-400">{{ __('Track payments & overdue') }}</span>
                        </div>
                    </a>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
