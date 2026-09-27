@extends('layouts.app')

@section('title', __('Cash Operations'))

@section('breadcrumb')
    <li class="breadcrumb-item active">{{ __('Cash Operations') }}</li>
@endsection

@section('actions')
    <a href="{{ route('cash-management.create') }}" class="btn btn-primary">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
        </svg>
        <span>{{ __('New Operation') }}</span>
    </a>
@endsection

@section('content')
<div class="space-y-6">
    <div class="card overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200 dark:divide-slate-700">
                <thead class="bg-slate-50 dark:bg-slate-800/60">
                    <tr>
                        <th class="px-6 py-3.5 text-start text-xs font-semibold text-slate-500 uppercase tracking-wider">{{ __('Reference') }}</th>
                        <th class="px-6 py-3.5 text-start text-xs font-semibold text-slate-500 uppercase tracking-wider">{{ __('Type') }}</th>
                        <th class="px-6 py-3.5 text-start text-xs font-semibold text-slate-500 uppercase tracking-wider">{{ __('Amount') }}</th>
                        <th class="px-6 py-3.5 text-start text-xs font-semibold text-slate-500 uppercase tracking-wider">{{ __('Status') }}</th>
                        <th class="px-6 py-3.5 text-start text-xs font-semibold text-slate-500 uppercase tracking-wider">{{ __('Branch') }}</th>
                        <th class="px-6 py-3.5 text-start text-xs font-semibold text-slate-500 uppercase tracking-wider">{{ __('Date') }}</th>
                        <th class="px-6 py-3.5 text-end text-xs font-semibold text-slate-500 uppercase tracking-wider">{{ __('Actions') }}</th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-slate-900 divide-y divide-slate-100 dark:divide-slate-800">
                    @forelse($operations as $operation)
                    <tr class="hover:bg-slate-50/80 dark:hover:bg-slate-800/40 transition-colors">
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-slate-900 dark:text-white" dir="ltr">
                            {{ $operation->operation_reference }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-600 dark:text-slate-300">
                            {{ ucfirst($operation->operation_type) }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-slate-900 dark:text-white" dir="ltr">
                            {{ $operation->currency }} {{ number_format($operation->amount, 2) }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                @if($operation->status === 'completed') bg-emerald-50 text-emerald-700 border border-emerald-200 dark:bg-emerald-950/40 dark:text-emerald-400
                                @elseif($operation->status === 'approved') bg-sky-50 text-sky-700 border border-sky-200 dark:bg-sky-950/40 dark:text-sky-400
                                @elseif($operation->status === 'pending') bg-amber-50 text-amber-700 border border-amber-200 dark:bg-amber-950/40 dark:text-amber-400
                                @elseif($operation->status === 'rejected') bg-rose-50 text-rose-700 border border-rose-200 dark:bg-rose-950/40 dark:text-rose-400
                                @else bg-slate-100 text-slate-700 border border-slate-200 dark:bg-slate-800 dark:text-slate-300 @endif">
                                {{ ucfirst($operation->status) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-600 dark:text-slate-300">
                            {{ $operation->branch?->name ?? '—' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-500 dark:text-slate-400" dir="ltr">
                            {{ $operation->operation_date?->format('Y-m-d') ?? '—' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-end text-sm">
                            <a href="{{ route('cash-management.show', $operation->id) }}" 
                               class="btn btn-sm btn-secondary">
                                {{ __('View') }}
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-6 py-12 text-center text-slate-500 dark:text-slate-400">
                            {{ __('No cash operations found') }}
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
