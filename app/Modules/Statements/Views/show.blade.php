@extends('layouts.app')

@section('title', __('Statement Details'))

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('statements.index') }}">{{ __('Account Statements') }}</a></li>
    <li class="breadcrumb-item active font-mono" dir="ltr">{{ $statement->statement_reference }}</li>
@endsection

@section('actions')
    @if($statement->file_path)
        <a href="{{ route('statements.download', $statement->id) }}" class="btn btn-primary">
            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
            </svg>
            <span>{{ __('Download PDF') }}</span>
        </a>
    @endif
    <a href="{{ route('statements.index') }}" class="btn btn-secondary">
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
                        <span class="text-xs font-semibold uppercase tracking-wider text-slate-500">{{ __('Period:') }} {{ $statement->period_start }} &rarr; {{ $statement->period_end }}</span>
                        <h2 class="text-xl font-bold text-slate-900 font-mono mt-0.5" dir="ltr">{{ $statement->statement_reference }}</h2>
                    </div>
                    <span class="inline-flex items-center rounded-full px-3 py-1 text-xs font-semibold ring-1 ring-inset 
                        @if($statement->status === 'completed') bg-emerald-50 text-emerald-700 ring-emerald-200
                        @elseif($statement->status === 'generating') bg-blue-50 text-blue-700 ring-blue-200
                        @elseif($statement->status === 'pending') bg-amber-50 text-amber-700 ring-amber-200
                        @else bg-rose-50 text-rose-700 ring-rose-200 @endif">
                        {{ ucfirst($statement->status) }}
                    </span>
                </div>

                <div class="bank-card-body">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pb-6 border-b border-slate-100">
                        <div>
                            <p class="text-xs font-semibold uppercase tracking-wider text-slate-500">{{ __('Opening Balance') }}</p>
                            <p class="text-2xl font-bold mt-1 font-mono text-slate-900" dir="ltr">
                                {{ number_format($statement->opening_balance, 2) }} {{ $statement->currency }}
                            </p>
                        </div>
                        <div>
                            <p class="text-xs font-semibold uppercase tracking-wider text-slate-500">{{ __('Closing Balance') }}</p>
                            <p class="text-2xl font-bold mt-1 font-mono text-emerald-700" dir="ltr">
                                {{ number_format($statement->closing_balance, 2) }} {{ $statement->currency }}
                            </p>
                        </div>
                    </div>

                    <div class="pt-6">
                        <h3 class="text-sm font-semibold text-slate-900 mb-4">{{ __('Statement Summary') }}</h3>
                        <dl class="grid grid-cols-1 sm:grid-cols-2 gap-x-6 gap-y-4 text-sm">
                            <div class="flex justify-between border-b border-slate-50 pb-2">
                                <dt class="text-slate-500">{{ __('Account') }}</dt>
                                <dd class="font-medium text-slate-900 font-mono" dir="ltr">{{ $statement->account?->account_number ?? __('N/A') }}</dd>
                            </div>
                            <div class="flex justify-between border-b border-slate-50 pb-2">
                                <dt class="text-slate-500">{{ __('Customer') }}</dt>
                                <dd class="font-medium text-slate-900">{{ $statement->customer?->first_name }} {{ $statement->customer?->last_name ?? '' }}</dd>
                            </div>
                            <div class="flex justify-between border-b border-slate-50 pb-2">
                                <dt class="text-slate-500">{{ __('Total Credits') }}</dt>
                                <dd class="font-medium text-emerald-700 font-mono" dir="ltr">+{{ number_format($statement->total_credits, 2) }} {{ $statement->currency }}</dd>
                            </div>
                            <div class="flex justify-between border-b border-slate-50 pb-2">
                                <dt class="text-slate-500">{{ __('Total Debits') }}</dt>
                                <dd class="font-medium text-rose-700 font-mono" dir="ltr">-{{ number_format($statement->total_debits, 2) }} {{ $statement->currency }}</dd>
                            </div>
                            <div class="flex justify-between border-b border-slate-50 pb-2">
                                <dt class="text-slate-500">{{ __('Total Transactions') }}</dt>
                                <dd class="font-medium text-slate-900 font-mono" dir="ltr">{{ $statement->transaction_count }}</dd>
                            </div>
                            <div class="flex justify-between border-b border-slate-50 pb-2">
                                <dt class="text-slate-500">{{ __('Generated At') }}</dt>
                                <dd class="font-medium text-slate-900 font-mono" dir="ltr">{{ $statement->generated_at?->format('Y-m-d H:i') ?? __('Pending') }}</dd>
                            </div>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        <div class="space-y-6">
            <div class="bank-card">
                <div class="bank-card-header">
                    <h3 class="text-sm font-semibold text-slate-900">{{ __('Export Options') }}</h3>
                </div>
                <div class="bank-card-body space-y-3">
                    @if($statement->file_path)
                        <a href="{{ route('statements.download', $statement->id) }}" class="w-full btn btn-primary text-xs">
                            {{ __('Download PDF Statement') }}
                        </a>
                    @else
                        <p class="text-xs text-slate-500">{{ __('Statement generation is currently pending or processing.') }}</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
