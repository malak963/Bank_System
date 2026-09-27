@extends('layouts.app')

@section('title', __('Overdue Bills'))

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('bills-payments.index') }}">{{ __('Bills & Payments') }}</a></li>
    <li class="breadcrumb-item active">{{ __('Overdue Bills') }}</li>
@endsection

@section('actions')
    <a href="{{ route('bills-payments.index') }}" class="btn btn-secondary">
        {{ __('All Bills') }}
    </a>
@endsection

@section('content')
<div class="mx-auto max-w-7xl space-y-6 px-4 py-8 sm:px-6 lg:px-8">
    <div class="rounded-lg border border-rose-200 bg-rose-50 p-4">
        <div class="flex items-center gap-2">
            <svg class="h-5 w-5 text-rose-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
            </svg>
            <p class="text-xs font-semibold text-rose-800">{{ __('These bills are past their due date and require immediate settlement.') }}</p>
        </div>
    </div>

    <div class="bank-card">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200">
                <thead class="bg-slate-50">
                    <tr>
                        <th class="px-5 py-3.5 text-start text-xs font-semibold uppercase tracking-wider text-slate-500">{{ __('Reference') }}</th>
                        <th class="px-5 py-3.5 text-start text-xs font-semibold uppercase tracking-wider text-slate-500">{{ __('Provider') }}</th>
                        <th class="px-5 py-3.5 text-start text-xs font-semibold uppercase tracking-wider text-slate-500">{{ __('Amount') }}</th>
                        <th class="px-5 py-3.5 text-start text-xs font-semibold uppercase tracking-wider text-slate-500">{{ __('Due Date') }}</th>
                        <th class="px-5 py-3.5 text-start text-xs font-semibold uppercase tracking-wider text-slate-500">{{ __('Days Overdue') }}</th>
                        <th class="px-5 py-3.5 text-start text-xs font-semibold uppercase tracking-wider text-slate-500">{{ __('Customer') }}</th>
                        <th class="px-5 py-3.5 text-end text-xs font-semibold uppercase tracking-wider text-slate-500">{{ __('Actions') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 bg-white">
                    @forelse($bills as $bill)
                        <tr class="transition hover:bg-slate-50">
                            <td class="px-5 py-4 whitespace-nowrap text-sm font-semibold text-slate-900 font-mono" dir="ltr">
                                {{ $bill->bill_reference }}
                            </td>
                            <td class="px-5 py-4 whitespace-nowrap text-sm text-slate-700 font-medium">
                                {{ $bill->provider_name }}
                            </td>
                            <td class="px-5 py-4 whitespace-nowrap text-sm font-semibold text-rose-700 font-mono" dir="ltr">
                                {{ number_format($bill->amount, 2) }} {{ $bill->currency }}
                            </td>
                            <td class="px-5 py-4 whitespace-nowrap text-sm text-slate-500 font-mono" dir="ltr">
                                {{ $bill->due_date?->format('Y-m-d') ?? __('N/A') }}
                            </td>
                            <td class="px-5 py-4 whitespace-nowrap text-sm font-semibold text-rose-600 font-mono" dir="ltr">
                                {{ $bill->due_date?->diffInDays(now()) }} {{ __('days') }}
                            </td>
                            <td class="px-5 py-4 whitespace-nowrap text-sm text-slate-600">
                                {{ $bill->customer?->first_name }} {{ $bill->customer?->last_name ?? '' }}
                            </td>
                            <td class="px-5 py-4 whitespace-nowrap text-end text-sm">
                                <a href="{{ route('bills-payments.show', $bill->id) }}" class="btn btn-sm btn-secondary">
                                    {{ __('View') }}
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center text-slate-500">
                                <p class="text-sm font-semibold text-slate-700">{{ __('No overdue bills.') }}</p>
                                <p class="mt-1 text-xs text-slate-400">{{ __('All billed obligations are currently in good standing.') }}</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
