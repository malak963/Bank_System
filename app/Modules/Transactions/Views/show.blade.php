@extends('layouts.app')

@section('title', __('Transaction Details'))

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('transactions.index') }}">{{ __('Transactions') }}</a></li>
    <li class="breadcrumb-item active font-mono" dir="ltr">{{ $transaction->transaction_reference }}</li>
@endsection

@section('actions')
    <a href="{{ route('transactions.edit', $transaction) }}" class="btn btn-secondary">
        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10" />
        </svg>
        <span>{{ __('Edit Details') }}</span>
    </a>
    <a href="{{ route('transactions.index') }}" class="btn btn-secondary">
        {{ __('Back') }}
    </a>
@endsection

@section('content')
<div class="mx-auto max-w-7xl space-y-6 px-4 py-8 sm:px-6 lg:px-8">
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 space-y-6">
            <div class="bank-card">
                <div class="bank-card-header flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                    <div>
                        <span class="text-xs font-semibold uppercase tracking-wider text-slate-500">{{ $transaction->transaction_type->label() }}</span>
                        <h2 class="text-xl font-bold text-slate-900 font-mono mt-0.5" dir="ltr">{{ $transaction->transaction_reference }}</h2>
                    </div>
                    @switch($transaction->status->value)
                        @case('completed')
                            <span class="inline-flex items-center rounded-full px-3 py-1 text-xs font-semibold ring-1 ring-inset bg-emerald-50 text-emerald-700 ring-emerald-200">{{ __('Completed') }}</span>
                            @break
                        @case('pending')
                            <span class="inline-flex items-center rounded-full px-3 py-1 text-xs font-semibold ring-1 ring-inset bg-amber-50 text-amber-700 ring-amber-200">{{ __('Pending') }}</span>
                            @break
                        @case('failed')
                            <span class="inline-flex items-center rounded-full px-3 py-1 text-xs font-semibold ring-1 ring-inset bg-rose-50 text-rose-700 ring-rose-200">{{ __('Failed') }}</span>
                            @break
                        @case('reversed')
                            <span class="inline-flex items-center rounded-full px-3 py-1 text-xs font-semibold ring-1 ring-inset bg-purple-50 text-purple-700 ring-purple-200">{{ __('Reversed') }}</span>
                            @break
                        @default
                            <span class="inline-flex items-center rounded-full px-3 py-1 text-xs font-semibold ring-1 ring-inset bg-slate-100 text-slate-700 ring-slate-200">{{ $transaction->status->label() }}</span>
                    @endswitch
                </div>

                <div class="bank-card-body">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pb-6 border-b border-slate-100">
                        <div>
                            <p class="text-xs font-semibold uppercase tracking-wider text-slate-500">{{ __('Amount') }}</p>
                            <p class="text-3xl font-bold mt-1 font-mono {{ $transaction->isCredit() ? 'text-emerald-700' : 'text-rose-700' }}" dir="ltr">
                                {{ $transaction->isCredit() ? '+' : '-' }}{{ number_format($transaction->amount, 2) }} {{ $transaction->currency }}
                            </p>
                        </div>
                        <div>
                            <p class="text-xs font-semibold uppercase tracking-wider text-slate-500">{{ __('Date & Time') }}</p>
                            <p class="text-sm font-semibold text-slate-900 mt-1 font-mono" dir="ltr">{{ $transaction->created_at->format('Y-m-d H:i:s') }}</p>
                        </div>
                    </div>

                    <div class="pt-6">
                        <h3 class="text-sm font-semibold text-slate-900 mb-4">{{ __('Transaction Ledger Details') }}</h3>
                        <dl class="grid grid-cols-1 sm:grid-cols-2 gap-x-6 gap-y-4 text-sm">
                            <div class="flex justify-between border-b border-slate-50 pb-2">
                                <dt class="text-slate-500">{{ __('Account') }}</dt>
                                <dd class="font-medium text-slate-900 font-mono" dir="ltr">{{ $transaction->account?->account_number ?? __('N/A') }}</dd>
                            </div>
                            <div class="flex justify-between border-b border-slate-50 pb-2">
                                <dt class="text-slate-500">{{ __('Customer') }}</dt>
                                <dd class="font-medium text-slate-900">{{ $transaction->customer?->full_name ?? __('N/A') }}</dd>
                            </div>
                            <div class="flex justify-between border-b border-slate-50 pb-2">
                                <dt class="text-slate-500">{{ __('Branch') }}</dt>
                                <dd class="font-medium text-slate-900">{{ $transaction->branch?->name ?? __('N/A') }}</dd>
                            </div>
                            <div class="flex justify-between border-b border-slate-50 pb-2">
                                <dt class="text-slate-500">{{ __('Reference Number') }}</dt>
                                <dd class="font-medium text-slate-900 font-mono" dir="ltr">{{ $transaction->reference_number ?? __('N/A') }}</dd>
                            </div>
                            <div class="flex justify-between border-b border-slate-50 pb-2">
                                <dt class="text-slate-500">{{ __('Balance Before') }}</dt>
                                <dd class="font-medium text-slate-900 font-mono" dir="ltr">{{ number_format($transaction->balance_before, 2) }} {{ $transaction->currency }}</dd>
                            </div>
                            <div class="flex justify-between border-b border-slate-50 pb-2">
                                <dt class="text-slate-500">{{ __('Balance After') }}</dt>
                                <dd class="font-medium text-slate-900 font-mono" dir="ltr">{{ number_format($transaction->balance_after, 2) }} {{ $transaction->currency }}</dd>
                            </div>
                            <div class="flex justify-between border-b border-slate-50 pb-2">
                                <dt class="text-slate-500">{{ __('Fees') }}</dt>
                                <dd class="font-medium text-slate-900 font-mono" dir="ltr">{{ number_format($transaction->fees, 2) }} {{ $transaction->currency }}</dd>
                            </div>
                            <div class="flex justify-between border-b border-slate-50 pb-2">
                                <dt class="text-slate-500">{{ __('Tax') }}</dt>
                                <dd class="font-medium text-slate-900 font-mono" dir="ltr">{{ number_format($transaction->tax, 2) }} {{ $transaction->currency }}</dd>
                            </div>
                        </dl>
                    </div>

                    @if($transaction->description)
                        <div class="border-t border-slate-100 pt-4 mt-6">
                            <h3 class="text-xs font-semibold uppercase tracking-wider text-slate-500 mb-1.5">{{ __('Description') }}</h3>
                            <p class="text-sm text-slate-800 bg-slate-50 p-3 rounded-lg">{{ $transaction->description }}</p>
                        </div>
                    @endif

                    @if($transaction->relatedTransaction)
                        <div class="border-t border-slate-100 pt-4 mt-4">
                            <h3 class="text-xs font-semibold uppercase tracking-wider text-slate-500 mb-1.5">{{ __('Related Transaction') }}</h3>
                            <a href="{{ route('transactions.show', $transaction->relatedTransaction) }}" class="text-sm text-emerald-700 hover:text-emerald-900 font-mono font-semibold" dir="ltr">
                                {{ $transaction->relatedTransaction->transaction_reference }} &rarr;
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="space-y-6">
            @if($transaction->canBeReversed())
                <div class="bank-card">
                    <div class="bank-card-header">
                        <h3 class="text-sm font-semibold text-rose-700">{{ __('Reverse Transaction') }}</h3>
                    </div>
                    <div class="bank-card-body">
                        <form action="{{ route('transactions.reverse', $transaction) }}" method="POST">
                            @csrf
                            <div class="mb-4">
                                <label class="block text-xs font-medium text-slate-700 mb-1">{{ __('Reversal Reason') }} <span class="text-rose-500">*</span></label>
                                <textarea name="reason" required rows="3" class="w-full rounded-lg border-slate-300 text-xs shadow-xs focus:border-rose-500 focus:ring-rose-500" placeholder="{{ __('Enter reason for reversal...') }}"></textarea>
                            </div>
                            <button type="submit" class="w-full btn btn-danger" onclick="return confirm('{{ __('Are you sure you want to reverse this transaction?') }}')">
                                {{ __('Reverse Transaction') }}
                            </button>
                        </form>
                    </div>
                </div>
            @endif

            @if($transaction->reversals->count() > 0)
                <div class="bank-card">
                    <div class="bank-card-header">
                        <h3 class="text-sm font-semibold text-slate-900">{{ __('Reversals History') }}</h3>
                    </div>
                    <div class="bank-card-body space-y-2">
                        @foreach($transaction->reversals as $reversal)
                            <a href="{{ route('transactions.show', $reversal) }}" class="block p-2 rounded-lg hover:bg-slate-50 text-xs">
                                <span class="font-semibold text-emerald-700 font-mono" dir="ltr">{{ $reversal->transaction_reference }}</span>
                                <span class="text-slate-500 block mt-0.5">{{ $reversal->reversal_reason }}</span>
                            </a>
                        @endforeach
                    </div>
                </div>
            @endif

            @if($transaction->metadata)
                <div class="bank-card">
                    <div class="bank-card-header">
                        <h3 class="text-sm font-semibold text-slate-900">{{ __('Metadata') }}</h3>
                    </div>
                    <div class="bank-card-body">
                        <pre class="text-xs text-slate-600 bg-slate-50 p-3 rounded-lg overflow-auto font-mono" dir="ltr">{{ json_encode($transaction->metadata, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
