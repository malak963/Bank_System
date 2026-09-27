@extends('layouts.app')

@section('title', __('Banking Calculators'))

@section('breadcrumb')
    <li class="breadcrumb-item active">{{ __('Banking Calculators') }}</li>
@endsection

@section('content')
<div class="space-y-6">
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <div class="card p-6 flex flex-col justify-between hover:shadow-md transition">
            <div>
                <div class="flex items-center gap-3 mb-4">
                    <div class="p-2.5 rounded-xl bg-emerald-50 dark:bg-emerald-950/40 text-emerald-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <h2 class="text-lg font-bold text-slate-900 dark:text-white">{{ __('Loan Calculator') }}</h2>
                </div>
                <p class="text-sm text-slate-600 dark:text-slate-400 mb-6 leading-relaxed">{{ __('Calculate monthly payments, total interest, and loan amortization schedule.') }}</p>
            </div>
            <a href="{{ route('calculators.loan') }}" class="btn btn-primary w-full justify-center">
                {{ __('Calculate Loan') }}
            </a>
        </div>

        <div class="card p-6 flex flex-col justify-between hover:shadow-md transition">
            <div>
                <div class="flex items-center gap-3 mb-4">
                    <div class="p-2.5 rounded-xl bg-slate-100 dark:bg-slate-800 text-slate-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                        </svg>
                    </div>
                    <h2 class="text-lg font-bold text-slate-900 dark:text-white">{{ __('Affordability Calculator') }}</h2>
                </div>
                <p class="text-sm text-slate-600 dark:text-slate-400 mb-6 leading-relaxed">{{ __('Determine how much you can borrow based on your income and expenses.') }}</p>
            </div>
            <button class="btn btn-secondary w-full justify-center opacity-60 cursor-not-allowed" disabled>
                {{ __('Coming Soon') }}
            </button>
        </div>

        <div class="card p-6 flex flex-col justify-between hover:shadow-md transition">
            <div>
                <div class="flex items-center gap-3 mb-4">
                    <div class="p-2.5 rounded-xl bg-slate-100 dark:bg-slate-800 text-slate-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                        </svg>
                    </div>
                    <h2 class="text-lg font-bold text-slate-900 dark:text-white">{{ __('Savings Calculator') }}</h2>
                </div>
                <p class="text-sm text-slate-600 dark:text-slate-400 mb-6 leading-relaxed">{{ __('Calculate how your savings will grow over time with compound interest.') }}</p>
            </div>
            <button class="btn btn-secondary w-full justify-center opacity-60 cursor-not-allowed" disabled>
                {{ __('Coming Soon') }}
            </button>
        </div>
    </div>
</div>
@endsection
