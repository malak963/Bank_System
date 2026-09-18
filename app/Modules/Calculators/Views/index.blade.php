@extends('layouts.app')

@section('title', 'Calculators')

@section('content')
<div class="container mx-auto px-4 py-8">
    <h1 class="text-3xl font-bold text-slate-800 mb-6">{{ __('Banking Calculators') }}</h1>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <div class="bg-white rounded-lg shadow p-6 hover:shadow-lg transition">
            <div class="flex items-center mb-4">
                <svg class="w-8 h-8 text-emerald-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <h2 class="text-xl font-semibold text-slate-800">{{ __('Loan Calculator') }}</h2>
            </div>
            <p class="text-slate-600 mb-4">{{ __('Calculate monthly payments, total interest, and loan amortization schedule.') }}</p>
            <a href="{{ route('calculators.loan') }}" 
               class="inline-block w-full text-center bg-emerald-600 text-white px-4 py-2 rounded-lg hover:bg-emerald-700 transition">
                Calculate Loan
            </a>
        </div>

        <div class="bg-white rounded-lg shadow p-6 hover:shadow-lg transition">
            <div class="flex items-center mb-4">
                <svg class="w-8 h-8 text-emerald-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                </svg>
                <h2 class="text-xl font-semibold text-slate-800">{{ __('Affordability Calculator') }}</h2>
            </div>
            <p class="text-slate-600 mb-4">{{ __('Determine how much you can borrow based on your income and expenses.') }}</p>
            <button class="inline-block w-full text-center bg-slate-400 text-white px-4 py-2 rounded-lg cursor-not-allowed">
                Coming Soon
            </button>
        </div>

        <div class="bg-white rounded-lg shadow p-6 hover:shadow-lg transition">
            <div class="flex items-center mb-4">
                <svg class="w-8 h-8 text-emerald-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                </svg>
                <h2 class="text-xl font-semibold text-slate-800">{{ __('Savings Calculator') }}</h2>
            </div>
            <p class="text-slate-600 mb-4">Calculate how your savings will grow over time with compound interest.</p>
            <button class="inline-block w-full text-center bg-slate-400 text-white px-4 py-2 rounded-lg cursor-not-allowed">
                Coming Soon
            </button>
        </div>
    </div>
</div>
@endsection
