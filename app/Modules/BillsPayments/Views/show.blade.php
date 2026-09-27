@extends('layouts.app')

@section('title', __('Bill Details'))

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('bills-payments.index') }}">{{ __('Bills & Payments') }}</a></li>
    <li class="breadcrumb-item active font-mono" dir="ltr">{{ $bill->bill_reference }}</li>
@endsection

@section('actions')
    <a href="{{ route('bills-payments.edit', $bill->id) }}" class="btn btn-secondary">
        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
            <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10" />
        </svg>
        <span>{{ __('Edit') }}</span>
    </a>
    <a href="{{ route('bills-payments.index') }}" class="btn btn-secondary">
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
                        <span class="text-xs font-semibold uppercase tracking-wider text-slate-500">{{ $bill->bill_type->getLabel() }}</span>
                        <h2 class="text-xl font-bold text-slate-900 mt-0.5">{{ $bill->provider_name }}</h2>
                        <p class="text-xs text-slate-400 font-mono mt-0.5" dir="ltr">{{ $bill->bill_reference }}</p>
                    </div>
                    <span class="inline-flex items-center rounded-full px-3 py-1 text-xs font-semibold ring-1 ring-inset {{ $bill->status->getColor() === 'emerald' ? 'bg-emerald-50 text-emerald-700 ring-emerald-200' : ($bill->status->getColor() === 'red' ? 'bg-rose-50 text-rose-700 ring-rose-200' : 'bg-amber-50 text-amber-700 ring-amber-200') }}">
                        {{ $bill->status->getLabel() }}
                    </span>
                </div>

                <div class="bank-card-body">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pb-6 border-b border-slate-100">
                        <div>
                            <p class="text-xs font-semibold uppercase tracking-wider text-slate-500">{{ __('Amount Due') }}</p>
                            <p class="text-3xl font-bold mt-1 font-mono text-slate-900" dir="ltr">
                                {{ number_format($bill->amount, 2) }} {{ $bill->currency }}
                            </p>
                        </div>
                        <div>
                            <p class="text-xs font-semibold uppercase tracking-wider text-slate-500">{{ __('Due Date') }}</p>
                            <p class="text-lg font-semibold text-slate-900 mt-1 font-mono" dir="ltr">{{ $bill->due_date?->format('Y-m-d') ?? __('N/A') }}</p>
                        </div>
                    </div>

                    <div class="pt-6">
                        <h3 class="text-sm font-semibold text-slate-900 mb-4">{{ __('Invoice Specification') }}</h3>
                        <dl class="grid grid-cols-1 sm:grid-cols-2 gap-x-6 gap-y-4 text-sm">
                            <div class="flex justify-between border-b border-slate-50 pb-2">
                                <dt class="text-slate-500">{{ __('Customer') }}</dt>
                                <dd class="font-medium text-slate-900">{{ $bill->customer?->first_name }} {{ $bill->customer?->last_name ?? '' }}</dd>
                            </div>
                            <div class="flex justify-between border-b border-slate-50 pb-2">
                                <dt class="text-slate-500">{{ __('Account') }}</dt>
                                <dd class="font-medium text-slate-900 font-mono" dir="ltr">{{ $bill->account?->account_number ?? __('None linked') }}</dd>
                            </div>
                            <div class="flex justify-between border-b border-slate-50 pb-2">
                                <dt class="text-slate-500">{{ __('Provider Reference') }}</dt>
                                <dd class="font-medium text-slate-900 font-mono" dir="ltr">{{ $bill->bill_number ?? __('N/A') }}</dd>
                            </div>
                            <div class="flex justify-between border-b border-slate-50 pb-2">
                                <dt class="text-slate-500">{{ __('Paid At') }}</dt>
                                <dd class="font-medium text-slate-900 font-mono" dir="ltr">{{ $bill->paid_at?->format('Y-m-d H:i') ?? __('Unpaid') }}</dd>
                            </div>
                        </dl>
                    </div>

                    @if($bill->notes)
                        <div class="border-t border-slate-100 pt-4 mt-6">
                            <h3 class="text-xs font-semibold uppercase tracking-wider text-slate-500 mb-1.5">{{ __('Notes') }}</h3>
                            <p class="text-sm text-slate-800 bg-slate-50 p-3 rounded-lg">{{ $bill->notes }}</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="space-y-6">
            @if($bill->status->canPay())
                <div class="bank-card">
                    <div class="bank-card-header">
                        <h3 class="text-sm font-semibold text-slate-900">{{ __('Execute Payment') }}</h3>
                    </div>
                    <div class="bank-card-body">
                        <form action="{{ route('bills-payments.pay', $bill->id) }}" method="POST" class="space-y-4">
                            @csrf
                            <div>
                                <label class="block text-xs font-medium text-slate-700 mb-1">{{ __('Payment Method') }}</label>
                                <select name="payment_method" required class="w-full rounded-lg border-slate-300 text-xs shadow-xs focus:border-emerald-500 focus:ring-emerald-500">
                                    <option value="account">{{ __('Bank Account') }}</option>
                                    <option value="card">{{ __('Credit / Debit Card') }}</option>
                                    <option value="cash">{{ __('Cash at Branch') }}</option>
                                </select>
                            </div>
                            <button type="submit" class="w-full btn btn-primary">
                                {{ __('Pay Now') }} ({{ number_format($bill->amount, 2) }} {{ $bill->currency }})
                            </button>
                        </form>
                    </div>
                </div>
            @endif

            @if($bill->status->canCancel())
                <div class="bank-card">
                    <div class="bank-card-header">
                        <h3 class="text-sm font-semibold text-rose-700">{{ __('Cancel Bill') }}</h3>
                    </div>
                    <div class="bank-card-body">
                        <form action="{{ route('bills-payments.cancel', $bill->id) }}" method="POST">
                            @csrf
                            <p class="text-xs text-slate-500 mb-3">{{ __('Cancel this billing obligation.') }}</p>
                            <button type="submit" class="w-full btn btn-danger" onclick="return confirm('{{ __('Are you sure you want to cancel this bill?') }}')">
                                {{ __('Cancel Bill') }}
                            </button>
                        </form>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
