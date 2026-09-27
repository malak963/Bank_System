@extends('layouts.app')

@section('title', __('Transfer Details'))

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('transfers.index') }}">{{ __('Transfers') }}</a></li>
    <li class="breadcrumb-item active font-mono" dir="ltr">{{ $transfer->transfer_reference }}</li>
@endsection

@section('actions')
    <a href="{{ route('transfers.index') }}" class="btn btn-secondary">
        {{ __('Back to Transfers') }}
    </a>
@endsection

@section('content')
<div class="mx-auto max-w-7xl space-y-6 px-4 py-8 sm:px-6 lg:px-8">
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 space-y-6">
            <div class="bank-card">
                <div class="bank-card-header flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                    <div>
                        <span class="text-xs font-semibold uppercase tracking-wider text-slate-500">{{ $transfer->transfer_type->label() }}</span>
                        <h2 class="text-xl font-bold text-slate-900 font-mono mt-0.5" dir="ltr">{{ $transfer->transfer_reference }}</h2>
                    </div>
                    @switch($transfer->status->value)
                        @case('pending')
                            <span class="inline-flex items-center rounded-full px-3 py-1 text-xs font-semibold ring-1 ring-inset bg-amber-50 text-amber-700 ring-amber-200">{{ __('Pending') }}</span>
                            @break
                        @case('processing')
                            <span class="inline-flex items-center rounded-full px-3 py-1 text-xs font-semibold ring-1 ring-inset bg-blue-50 text-blue-700 ring-blue-200">{{ __('Processing') }}</span>
                            @break
                        @case('completed')
                            <span class="inline-flex items-center rounded-full px-3 py-1 text-xs font-semibold ring-1 ring-inset bg-emerald-50 text-emerald-700 ring-emerald-200">{{ __('Completed') }}</span>
                            @break
                        @case('failed')
                            <span class="inline-flex items-center rounded-full px-3 py-1 text-xs font-semibold ring-1 ring-inset bg-rose-50 text-rose-700 ring-rose-200">{{ __('Failed') }}</span>
                            @break
                        @case('cancelled')
                            <span class="inline-flex items-center rounded-full px-3 py-1 text-xs font-semibold ring-1 ring-inset bg-slate-100 text-slate-700 ring-slate-200">{{ __('Cancelled') }}</span>
                            @break
                        @default
                            <span class="inline-flex items-center rounded-full px-3 py-1 text-xs font-semibold ring-1 ring-inset bg-slate-100 text-slate-700 ring-slate-200">{{ $transfer->status->label() }}</span>
                    @endswitch
                </div>

                <div class="bank-card-body">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pb-6 border-b border-slate-100">
                        <div>
                            <p class="text-xs font-semibold uppercase tracking-wider text-slate-500">{{ __('Amount') }}</p>
                            <p class="text-3xl font-bold mt-1 font-mono text-slate-900" dir="ltr">
                                {{ number_format($transfer->amount, 2) }} {{ $transfer->currency }}
                            </p>
                        </div>
                        <div>
                            <p class="text-xs font-semibold uppercase tracking-wider text-slate-500">{{ __('Initiated At') }}</p>
                            <p class="text-sm font-semibold text-slate-900 mt-1 font-mono" dir="ltr">{{ $transfer->created_at->format('Y-m-d H:i') }}</p>
                        </div>
                    </div>

                    <div class="pt-6">
                        <h3 class="text-sm font-semibold text-slate-900 mb-4">{{ __('Transfer Details') }}</h3>
                        <dl class="grid grid-cols-1 sm:grid-cols-2 gap-x-6 gap-y-4 text-sm">
                            <div class="flex justify-between border-b border-slate-50 pb-2">
                                <dt class="text-slate-500">{{ __('Customer') }}</dt>
                                <dd class="font-medium text-slate-900">{{ $transfer->customer?->full_name ?? __('N/A') }}</dd>
                            </div>
                            <div class="flex justify-between border-b border-slate-50 pb-2">
                                <dt class="text-slate-500">{{ __('From Account') }}</dt>
                                <dd class="font-medium text-slate-900 font-mono" dir="ltr">{{ $transfer->fromAccount?->account_number ?? __('N/A') }}</dd>
                            </div>
                            <div class="flex justify-between border-b border-slate-50 pb-2">
                                <dt class="text-slate-500">{{ __('To Account') }}</dt>
                                <dd class="font-medium text-slate-900 font-mono" dir="ltr">{{ $transfer->toAccount?->account_number ?? $transfer->beneficiary_account_number ?? __('N/A') }}</dd>
                            </div>
                            <div class="flex justify-between border-b border-slate-50 pb-2">
                                <dt class="text-slate-500">{{ __('Beneficiary Name') }}</dt>
                                <dd class="font-medium text-slate-900">{{ $transfer->beneficiary_name ?? __('N/A') }}</dd>
                            </div>
                            <div class="flex justify-between border-b border-slate-50 pb-2">
                                <dt class="text-slate-500">{{ __('Transfer Fee') }}</dt>
                                <dd class="font-medium text-slate-900 font-mono" dir="ltr">{{ number_format($transfer->fee_amount, 2) }} {{ $transfer->currency }}</dd>
                            </div>
                            <div class="flex justify-between border-b border-slate-50 pb-2">
                                <dt class="text-slate-500">{{ __('Exchange Rate') }}</dt>
                                <dd class="font-medium text-slate-900 font-mono" dir="ltr">{{ $transfer->exchange_rate ?? '1.0000' }}</dd>
                            </div>
                        </dl>
                    </div>

                    @if($transfer->description)
                        <div class="border-t border-slate-100 pt-4 mt-6">
                            <h3 class="text-xs font-semibold uppercase tracking-wider text-slate-500 mb-1.5">{{ __('Description / Memo') }}</h3>
                            <p class="text-sm text-slate-800 bg-slate-50 p-3 rounded-lg">{{ $transfer->description }}</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="space-y-6">
            @if($transfer->status === \App\Modules\Transfers\Enums\TransferStatus::Pending)
                <div class="bank-card">
                    <div class="bank-card-header">
                        <h3 class="text-sm font-semibold text-rose-700">{{ __('Cancel Transfer') }}</h3>
                    </div>
                    <div class="bank-card-body">
                        <form action="{{ route('transfers.cancel', $transfer) }}" method="POST">
                            @csrf
                            <p class="text-xs text-slate-500 mb-3">{{ __('You can cancel this transfer while it is still pending execution.') }}</p>
                            <button type="submit" class="w-full btn btn-danger" onclick="return confirm('{{ __('Are you sure you want to cancel this transfer?') }}')">
                                {{ __('Cancel Transfer') }}
                            </button>
                        </form>
                    </div>
                </div>
            @endif

            @if($transfer->metadata)
                <div class="bank-card">
                    <div class="bank-card-header">
                        <h3 class="text-sm font-semibold text-slate-900">{{ __('Technical Metadata') }}</h3>
                    </div>
                    <div class="bank-card-body">
                        <pre class="text-xs text-slate-600 bg-slate-50 p-3 rounded-lg overflow-auto font-mono" dir="ltr">{{ json_encode($transfer->metadata, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
