@extends('layouts.app')

@section('title', __('Cash Operation Details'))

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></li>
    <li class="breadcrumb-item"><a href="{{ route('cash-management.index') }}">{{ __('Cash Operations') }}</a></li>
    <li class="breadcrumb-item active">{{ $operation->operation_reference }}</li>
@endsection

@section('actions')
    <div class="flex items-center gap-2">
        <a href="{{ route('cash-management.index') }}" class="btn btn-secondary">
            <svg class="w-4 h-4 rtl:rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
            <span>{{ __('Back') }}</span>
        </a>
        <a href="{{ route('cash-management.edit', $operation->id) }}" class="btn btn-primary">
            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.651-1.651a2.121 2.121 0 1 1 3 3l-9.193 9.193a4.5 4.5 0 0 1-1.897 1.13L7.5 17.25l1.091-2.923a4.5 4.5 0 0 1 1.13-1.897l7.141-7.143Z" />
                <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 7.125 16.875 4.5" />
            </svg>
            <span>{{ __('Edit') }}</span>
        </a>
        @if($operation->status === 'pending')
        <form action="{{ route('cash-management.destroy', $operation->id) }}" method="POST" class="inline" onsubmit="return confirm('{{ __('Are you sure you want to delete this operation?') }}')">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn btn-danger">
                {{ __('Delete') }}
            </button>
        </form>
        @endif
    </div>
@endsection

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <div class="card p-6">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-6 border-b border-slate-100 dark:border-slate-800">
            <div>
                <p class="text-xs font-semibold uppercase tracking-wider text-emerald-700 dark:text-emerald-400">{{ __('Reference') }}</p>
                <h2 class="mt-1 font-mono text-2xl font-bold text-slate-900 dark:text-white" dir="ltr">{{ $operation->operation_reference }}</h2>
            </div>
            <div>
                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold 
                    @if($operation->status === 'completed') bg-emerald-50 text-emerald-700 border border-emerald-200 dark:bg-emerald-950/40 dark:text-emerald-400
                    @elseif($operation->status === 'approved') bg-sky-50 text-sky-700 border border-sky-200 dark:bg-sky-950/40 dark:text-sky-400
                    @elseif($operation->status === 'pending') bg-amber-50 text-amber-700 border border-amber-200 dark:bg-amber-950/40 dark:text-amber-400
                    @elseif($operation->status === 'rejected') bg-rose-50 text-rose-700 border border-rose-200 dark:bg-rose-950/40 dark:text-rose-400
                    @else bg-slate-100 text-slate-700 border border-slate-200 dark:bg-slate-800 dark:text-slate-300 @endif">
                    {{ ucfirst($operation->status) }}
                </span>
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-6 pt-6">
            <div class="p-3.5 rounded-xl bg-slate-50/70 dark:bg-slate-800/40 border border-slate-100 dark:border-slate-800">
                <label class="block text-xs font-medium text-slate-500 dark:text-slate-400">{{ __('Operation Type') }}</label>
                <p class="mt-1 text-sm font-semibold text-slate-900 dark:text-white">{{ ucfirst($operation->operation_type) }}</p>
            </div>
            <div class="p-3.5 rounded-xl bg-slate-50/70 dark:bg-slate-800/40 border border-slate-100 dark:border-slate-800">
                <label class="block text-xs font-medium text-slate-500 dark:text-slate-400">{{ __('Amount') }}</label>
                <p class="mt-1 text-sm font-bold text-slate-900 dark:text-white" dir="ltr">
                    {{ $operation->currency }} {{ number_format($operation->amount, 2) }}
                </p>
            </div>
            <div class="p-3.5 rounded-xl bg-slate-50/70 dark:bg-slate-800/40 border border-slate-100 dark:border-slate-800">
                <label class="block text-xs font-medium text-slate-500 dark:text-slate-400">{{ __('Branch') }}</label>
                <p class="mt-1 text-sm font-semibold text-slate-900 dark:text-white">{{ $operation->branch?->name ?? '—' }}</p>
            </div>
            <div class="p-3.5 rounded-xl bg-slate-50/70 dark:bg-slate-800/40 border border-slate-100 dark:border-slate-800">
                <label class="block text-xs font-medium text-slate-500 dark:text-slate-400">{{ __('Teller') }}</label>
                <p class="mt-1 text-sm font-semibold text-slate-900 dark:text-white">{{ $operation->teller?->name ?? '—' }}</p>
            </div>
            <div class="p-3.5 rounded-xl bg-slate-50/70 dark:bg-slate-800/40 border border-slate-100 dark:border-slate-800">
                <label class="block text-xs font-medium text-slate-500 dark:text-slate-400">{{ __('Account') }}</label>
                <p class="mt-1 text-sm font-semibold text-slate-900 dark:text-white" dir="ltr">{{ $operation->account?->account_number ?? '—' }}</p>
            </div>
            <div class="p-3.5 rounded-xl bg-slate-50/70 dark:bg-slate-800/40 border border-slate-100 dark:border-slate-800">
                <label class="block text-xs font-medium text-slate-500 dark:text-slate-400">{{ __('Operation Date') }}</label>
                <p class="mt-1 text-sm font-semibold text-slate-900 dark:text-white" dir="ltr">{{ $operation->operation_date?->format('Y-m-d H:i') ?? '—' }}</p>
            </div>
            <div class="p-3.5 rounded-xl bg-slate-50/70 dark:bg-slate-800/40 border border-slate-100 dark:border-slate-800">
                <label class="block text-xs font-medium text-slate-500 dark:text-slate-400">{{ __('Approved By') }}</label>
                <p class="mt-1 text-sm font-semibold text-slate-900 dark:text-white">{{ $operation->approvedBy?->name ?? '—' }}</p>
            </div>
            <div class="p-3.5 rounded-xl bg-slate-50/70 dark:bg-slate-800/40 border border-slate-100 dark:border-slate-800">
                <label class="block text-xs font-medium text-slate-500 dark:text-slate-400">{{ __('Approved At') }}</label>
                <p class="mt-1 text-sm font-semibold text-slate-900 dark:text-white" dir="ltr">{{ $operation->approved_at?->format('Y-m-d H:i') ?? '—' }}</p>
            </div>
            <div class="p-3.5 rounded-xl bg-slate-50/70 dark:bg-slate-800/40 border border-slate-100 dark:border-slate-800">
                <label class="block text-xs font-medium text-slate-500 dark:text-slate-400">{{ __('Completed At') }}</label>
                <p class="mt-1 text-sm font-semibold text-slate-900 dark:text-white" dir="ltr">{{ $operation->completed_at?->format('Y-m-d H:i') ?? '—' }}</p>
            </div>
        </div>

        @if($operation->description)
        <div class="mt-6 pt-6 border-t border-slate-100 dark:border-slate-800">
            <label class="block text-xs font-medium text-slate-500 dark:text-slate-400 mb-1">{{ __('Description') }}</label>
            <p class="text-sm text-slate-800 dark:text-slate-200">{{ $operation->description }}</p>
        </div>
        @endif

        @if($operation->notes)
        <div class="mt-4">
            <label class="block text-xs font-medium text-slate-500 dark:text-slate-400 mb-1">{{ __('Notes') }}</label>
            <p class="text-sm text-slate-800 dark:text-slate-200">{{ $operation->notes }}</p>
        </div>
        @endif
    </div>
</div>
@endsection
