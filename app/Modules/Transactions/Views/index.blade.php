@extends('layouts.app')

@section('title', __('Transactions'))

@section('breadcrumb')
    <li class="breadcrumb-item active">{{ __('Transactions') }}</li>
@endsection

@section('actions')
    <a href="{{ route('transactions.create') }}" class="btn btn-primary">
        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"></path>
        </svg>
        <span>{{ __('New Transaction') }}</span>
    </a>
@endsection

@section('content')
<div class="mx-auto max-w-7xl space-y-6 px-4 py-8 sm:px-6 lg:px-8">
    <div class="bank-card">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200">
                <thead class="bg-slate-50">
                    <tr>
                        <th class="px-5 py-3.5 text-start text-xs font-semibold uppercase tracking-wider text-slate-500">{{ __('Reference') }}</th>
                        <th class="px-5 py-3.5 text-start text-xs font-semibold uppercase tracking-wider text-slate-500">{{ __('Type') }}</th>
                        <th class="px-5 py-3.5 text-start text-xs font-semibold uppercase tracking-wider text-slate-500">{{ __('Account') }}</th>
                        <th class="px-5 py-3.5 text-start text-xs font-semibold uppercase tracking-wider text-slate-500">{{ __('Amount') }}</th>
                        <th class="px-5 py-3.5 text-start text-xs font-semibold uppercase tracking-wider text-slate-500">{{ __('Status') }}</th>
                        <th class="px-5 py-3.5 text-start text-xs font-semibold uppercase tracking-wider text-slate-500">{{ __('Date') }}</th>
                        <th class="px-5 py-3.5 text-end text-xs font-semibold uppercase tracking-wider text-slate-500">{{ __('Actions') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 bg-white">
                    @forelse($transactions as $transaction)
                        <tr class="transition hover:bg-slate-50">
                            <td class="px-5 py-4 whitespace-nowrap text-sm font-semibold text-slate-900 font-mono" dir="ltr">
                                {{ $transaction->transaction_reference }}
                            </td>
                            <td class="px-5 py-4 whitespace-nowrap text-sm text-slate-600">
                                {{ $transaction->transaction_type->label() }}
                            </td>
                            <td class="px-5 py-4 whitespace-nowrap text-sm text-slate-600 font-mono" dir="ltr">
                                {{ $transaction->account?->account_number ?? __('N/A') }}
                            </td>
                            <td class="px-5 py-4 whitespace-nowrap text-sm font-semibold {{ $transaction->isCredit() ? 'text-emerald-700' : 'text-rose-700' }} font-mono" dir="ltr">
                                {{ $transaction->isCredit() ? '+' : '-' }}{{ number_format($transaction->amount, 2) }}
                            </td>
                            <td class="px-5 py-4 whitespace-nowrap text-sm">
                                @switch($transaction->status->value)
                                    @case('completed')
                                        <span class="inline-flex items-center rounded-full px-2.5 py-1 text-xs font-semibold ring-1 ring-inset bg-emerald-50 text-emerald-700 ring-emerald-200">{{ __('Completed') }}</span>
                                        @break
                                    @case('pending')
                                        <span class="inline-flex items-center rounded-full px-2.5 py-1 text-xs font-semibold ring-1 ring-inset bg-amber-50 text-amber-700 ring-amber-200">{{ __('Pending') }}</span>
                                        @break
                                    @case('failed')
                                        <span class="inline-flex items-center rounded-full px-2.5 py-1 text-xs font-semibold ring-1 ring-inset bg-rose-50 text-rose-700 ring-rose-200">{{ __('Failed') }}</span>
                                        @break
                                    @case('reversed')
                                        <span class="inline-flex items-center rounded-full px-2.5 py-1 text-xs font-semibold ring-1 ring-inset bg-purple-50 text-purple-700 ring-purple-200">{{ __('Reversed') }}</span>
                                        @break
                                    @default
                                        <span class="inline-flex items-center rounded-full px-2.5 py-1 text-xs font-semibold ring-1 ring-inset bg-slate-100 text-slate-700 ring-slate-200">{{ $transaction->status->label() }}</span>
                                @endswitch
                            </td>
                            <td class="px-5 py-4 whitespace-nowrap text-sm text-slate-500 font-mono" dir="ltr">
                                {{ $transaction->created_at->format('Y-m-d H:i') }}
                            </td>
                            <td class="px-5 py-4 whitespace-nowrap text-end text-sm">
                                <a href="{{ route('transactions.show', $transaction) }}" class="btn btn-sm btn-secondary">
                                    {{ __('View') }}
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center text-slate-500">
                                <p class="text-sm font-semibold text-slate-700">{{ __('No transactions found.') }}</p>
                                <p class="mt-1 text-xs text-slate-400">{{ __('No transactions have been recorded in the system yet.') }}</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @if (method_exists($transactions, 'hasPages') && $transactions->hasPages())
        <div class="rounded-xl border border-slate-200 bg-white px-4 py-3 shadow-sm">
            {{ $transactions->links() }}
        </div>
    @endif
</div>
@endsection
