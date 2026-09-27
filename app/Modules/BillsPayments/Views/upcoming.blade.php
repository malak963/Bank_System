@extends('layouts.app')

@section('title', __('Upcoming Bills'))

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('bills-payments.index') }}">{{ __('Bills & Payments') }}</a></li>
    <li class="breadcrumb-item active">{{ __('Upcoming Bills') }}</li>
@endsection

@section('actions')
    <a href="{{ route('bills-payments.index') }}" class="btn btn-secondary">
        {{ __('All Bills') }}
    </a>
@endsection

@section('content')
<div class="mx-auto max-w-7xl space-y-6 px-4 py-8 sm:px-6 lg:px-8">
    <div class="rounded-lg border border-sky-200 bg-sky-50 p-4">
        <div class="flex items-center gap-2">
            <svg class="h-5 w-5 text-sky-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4l4 4m0-4H8m0 0H8m0 0v8m0-8h8M12 21v-8m0 0V13m0 0l-4-4m4 4l-4-4"></path>
            </svg>
            <p class="text-xs font-semibold text-sky-800">{{ __('Bills due in the next 7 days requiring upcoming processing.') }}</p>
        </div>
    </div>

    <div class="bank-card">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200">
                <thead class="bg-slate-50">
                    <tr>
                        <th class="px-5 py-3.5 text-start text-xs font-semibold uppercase tracking-wider text-slate-500">{{ __('Reference') }}</th>
                        <th class="px-5 py-3.5 text-start text-xs font-semibold uppercase tracking-wider text-slate-500">{{ __('Provider') }}</th>
                        <th class="px-5 py-3.5 text-start text-xs font-semibold uppercase tracking-wider text-slate-500">{{ __('Type') }}</th>
                        <th class="px-5 py-3.5 text-start text-xs font-semibold uppercase tracking-wider text-slate-500">{{ __('Amount') }}</th>
                        <th class="px-5 py-3.5 text-start text-xs font-semibold uppercase tracking-wider text-slate-500">{{ __('Due Date') }}</th>
                        <th class="px-5 py-3.5 text-start text-xs font-semibold uppercase tracking-wider text-slate-500">{{ __('Days Until Due') }}</th>
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
                            <td class="px-5 py-4 whitespace-nowrap text-sm text-slate-600">
                                {{ $bill->bill_type->getLabel() }}
                            </td>
                            <td class="px-5 py-4 whitespace-nowrap text-sm font-semibold text-slate-900 font-mono" dir="ltr">
                                {{ number_format($bill->amount, 2) }} {{ $bill->currency }}
                            </td>
                            <td class="px-5 py-4 whitespace-nowrap text-sm text-slate-500 font-mono" dir="ltr">
                                {{ $bill->due_date?->format('Y-m-d') ?? __('N/A') }}
                            </td>
                            <td class="px-5 py-4 whitespace-nowrap text-sm font-semibold font-mono {{ ($bill->due_date && $bill->due_date->diffInDays(now()) <= 2) ? 'text-amber-600' : 'text-slate-600' }}" dir="ltr">
                                {{ $bill->due_date ? $bill->due_date->diffInDays(now()) : 0 }} {{ __('days') }}
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
                            <td colspan="8" class="px-6 py-12 text-center text-slate-500">
                                <p class="text-sm font-semibold text-slate-700">{{ __('No upcoming bills found.') }}</p>
                                <p class="mt-1 text-xs text-slate-400">{{ __('No bills due in the upcoming week.') }}</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
