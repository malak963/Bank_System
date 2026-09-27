@extends('layouts.app')

@section('title', __('Transfers'))

@section('breadcrumb')
    <li class="breadcrumb-item active">{{ __('Transfers') }}</li>
@endsection

@section('actions')
    <form action="{{ route('transfers.process-scheduled') }}" method="POST" class="inline">
        @csrf
        <button type="submit" class="btn btn-secondary">
            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            <span>{{ __('Process Scheduled') }}</span>
        </button>
    </form>
    <a href="{{ route('transfers.create') }}" class="btn btn-primary">
        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"></path>
        </svg>
        <span>{{ __('New Transfer') }}</span>
    </a>
@endsection

@section('content')
<div class="mx-auto max-w-7xl space-y-6 px-4 py-8 sm:px-6 lg:px-8">
    <!-- Statistics Cards -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <div class="bank-card p-4">
            <p class="text-xs font-semibold uppercase text-slate-500">{{ __('Total Transfers') }}</p>
            <p class="text-2xl font-bold text-slate-950 mt-1 font-mono" dir="ltr">{{ $statistics['total'] }}</p>
        </div>
        <div class="bank-card p-4">
            <p class="text-xs font-semibold uppercase text-amber-700">{{ __('Pending') }}</p>
            <p class="text-2xl font-bold text-amber-800 mt-1 font-mono" dir="ltr">{{ $statistics['pending'] }}</p>
        </div>
        <div class="bank-card p-4">
            <p class="text-xs font-semibold uppercase text-emerald-700">{{ __('Completed') }}</p>
            <p class="text-2xl font-bold text-emerald-800 mt-1 font-mono" dir="ltr">{{ $statistics['completed'] }}</p>
        </div>
        <div class="bank-card p-4">
            <p class="text-xs font-semibold uppercase text-slate-500">{{ __('Total Volume') }}</p>
            <p class="text-2xl font-bold text-slate-900 mt-1 font-mono" dir="ltr">${{ number_format($statistics['total_amount'], 2) }}</p>
        </div>
    </div>

    <!-- Transfers Table -->
    <div class="bank-card">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200">
                <thead class="bg-slate-50">
                    <tr>
                        <th class="px-5 py-3.5 text-start text-xs font-semibold uppercase tracking-wider text-slate-500">{{ __('Reference') }}</th>
                        <th class="px-5 py-3.5 text-start text-xs font-semibold uppercase tracking-wider text-slate-500">{{ __('Type') }}</th>
                        <th class="px-5 py-3.5 text-start text-xs font-semibold uppercase tracking-wider text-slate-500">{{ __('From Account') }}</th>
                        <th class="px-5 py-3.5 text-start text-xs font-semibold uppercase tracking-wider text-slate-500">{{ __('To Account') }}</th>
                        <th class="px-5 py-3.5 text-start text-xs font-semibold uppercase tracking-wider text-slate-500">{{ __('Amount') }}</th>
                        <th class="px-5 py-3.5 text-start text-xs font-semibold uppercase tracking-wider text-slate-500">{{ __('Status') }}</th>
                        <th class="px-5 py-3.5 text-start text-xs font-semibold uppercase tracking-wider text-slate-500">{{ __('Customer') }}</th>
                        <th class="px-5 py-3.5 text-end text-xs font-semibold uppercase tracking-wider text-slate-500">{{ __('Actions') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 bg-white">
                    @forelse($transfers as $transfer)
                        <tr class="transition hover:bg-slate-50">
                            <td class="px-5 py-4 whitespace-nowrap text-sm font-semibold text-slate-900 font-mono" dir="ltr">
                                {{ $transfer->transfer_reference }}
                            </td>
                            <td class="px-5 py-4 whitespace-nowrap text-sm text-slate-600">
                                {{ $transfer->transfer_type->label() }}
                            </td>
                            <td class="px-5 py-4 whitespace-nowrap text-sm text-slate-600 font-mono" dir="ltr">
                                {{ $transfer->fromAccount?->account_number ?? __('N/A') }}
                            </td>
                            <td class="px-5 py-4 whitespace-nowrap text-sm text-slate-600 font-mono" dir="ltr">
                                {{ $transfer->toAccount?->account_number ?? $transfer->beneficiary_account_number ?? __('N/A') }}
                            </td>
                            <td class="px-5 py-4 whitespace-nowrap text-sm font-semibold text-slate-900 font-mono" dir="ltr">
                                {{ number_format($transfer->amount, 2) }} {{ $transfer->currency }}
                            </td>
                            <td class="px-5 py-4 whitespace-nowrap text-sm">
                                @switch($transfer->status->value)
                                    @case('pending')
                                        <span class="inline-flex items-center rounded-full px-2.5 py-1 text-xs font-semibold ring-1 ring-inset bg-amber-50 text-amber-700 ring-amber-200">{{ __('Pending') }}</span>
                                        @break
                                    @case('processing')
                                        <span class="inline-flex items-center rounded-full px-2.5 py-1 text-xs font-semibold ring-1 ring-inset bg-blue-50 text-blue-700 ring-blue-200">{{ __('Processing') }}</span>
                                        @break
                                    @case('completed')
                                        <span class="inline-flex items-center rounded-full px-2.5 py-1 text-xs font-semibold ring-1 ring-inset bg-emerald-50 text-emerald-700 ring-emerald-200">{{ __('Completed') }}</span>
                                        @break
                                    @case('failed')
                                        <span class="inline-flex items-center rounded-full px-2.5 py-1 text-xs font-semibold ring-1 ring-inset bg-rose-50 text-rose-700 ring-rose-200">{{ __('Failed') }}</span>
                                        @break
                                    @case('cancelled')
                                        <span class="inline-flex items-center rounded-full px-2.5 py-1 text-xs font-semibold ring-1 ring-inset bg-slate-100 text-slate-700 ring-slate-200">{{ __('Cancelled') }}</span>
                                        @break
                                    @default
                                        <span class="inline-flex items-center rounded-full px-2.5 py-1 text-xs font-semibold ring-1 ring-inset bg-slate-100 text-slate-700 ring-slate-200">{{ $transfer->status->label() }}</span>
                                @endswitch
                            </td>
                            <td class="px-5 py-4 whitespace-nowrap text-sm text-slate-600">
                                {{ $transfer->customer?->full_name ?? __('N/A') }}
                            </td>
                            <td class="px-5 py-4 whitespace-nowrap text-end text-sm">
                                <a href="{{ route('transfers.show', $transfer) }}" class="btn btn-sm btn-secondary">
                                    {{ __('View') }}
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-6 py-12 text-center text-slate-500">
                                <p class="text-sm font-semibold text-slate-700">{{ __('No transfers found.') }}</p>
                                <p class="mt-1 text-xs text-slate-400">{{ __('Create a new internal, external, or international transfer.') }}</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @if (method_exists($transfers, 'hasPages') && $transfers->hasPages())
        <div class="rounded-xl border border-slate-200 bg-white px-4 py-3 shadow-sm">
            {{ $transfers->links() }}
        </div>
    @endif
</div>
@endsection
