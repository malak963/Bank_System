@extends('layouts.app')

@section('title', __('Account Statements'))

@section('breadcrumb')
    <li class="breadcrumb-item active">{{ __('Account Statements') }}</li>
@endsection

@section('actions')
    <a href="{{ route('statements.create') }}" class="btn btn-primary">
        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"></path>
        </svg>
        <span>{{ __('Generate Statement') }}</span>
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
                        <th class="px-5 py-3.5 text-start text-xs font-semibold uppercase tracking-wider text-slate-500">{{ __('Account') }}</th>
                        <th class="px-5 py-3.5 text-start text-xs font-semibold uppercase tracking-wider text-slate-500">{{ __('Period') }}</th>
                        <th class="px-5 py-3.5 text-start text-xs font-semibold uppercase tracking-wider text-slate-500">{{ __('Opening Balance') }}</th>
                        <th class="px-5 py-3.5 text-start text-xs font-semibold uppercase tracking-wider text-slate-500">{{ __('Closing Balance') }}</th>
                        <th class="px-5 py-3.5 text-start text-xs font-semibold uppercase tracking-wider text-slate-500">{{ __('Transactions') }}</th>
                        <th class="px-5 py-3.5 text-start text-xs font-semibold uppercase tracking-wider text-slate-500">{{ __('Status') }}</th>
                        <th class="px-5 py-3.5 text-end text-xs font-semibold uppercase tracking-wider text-slate-500">{{ __('Actions') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 bg-white">
                    @forelse($statements as $statement)
                        <tr class="transition hover:bg-slate-50">
                            <td class="px-5 py-4 whitespace-nowrap text-sm font-semibold text-slate-900 font-mono" dir="ltr">
                                {{ $statement->statement_reference }}
                            </td>
                            <td class="px-5 py-4 whitespace-nowrap text-sm text-slate-600 font-mono" dir="ltr">
                                {{ $statement->account?->account_number ?? __('N/A') }}
                            </td>
                            <td class="px-5 py-4 whitespace-nowrap text-sm text-slate-500 font-mono" dir="ltr">
                                {{ $statement->period_start }} - {{ $statement->period_end }}
                            </td>
                            <td class="px-5 py-4 whitespace-nowrap text-sm font-medium text-slate-900 font-mono" dir="ltr">
                                {{ number_format($statement->opening_balance, 2) }} {{ $statement->currency }}
                            </td>
                            <td class="px-5 py-4 whitespace-nowrap text-sm font-semibold text-slate-900 font-mono" dir="ltr">
                                {{ number_format($statement->closing_balance, 2) }} {{ $statement->currency }}
                            </td>
                            <td class="px-5 py-4 whitespace-nowrap text-sm text-slate-600 font-mono" dir="ltr">
                                {{ $statement->transaction_count }}
                            </td>
                            <td class="px-5 py-4 whitespace-nowrap text-sm">
                                <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-semibold ring-1 ring-inset 
                                    @if($statement->status === 'completed') bg-emerald-50 text-emerald-700 ring-emerald-200
                                    @elseif($statement->status === 'generating') bg-blue-50 text-blue-700 ring-blue-200
                                    @elseif($statement->status === 'pending') bg-amber-50 text-amber-700 ring-amber-200
                                    @else bg-rose-50 text-rose-700 ring-rose-200 @endif">
                                    {{ ucfirst($statement->status) }}
                                </span>
                            </td>
                            <td class="px-5 py-4 whitespace-nowrap text-end text-sm">
                                <a href="{{ route('statements.show', $statement->id) }}" class="btn btn-sm btn-secondary">
                                    {{ __('View') }}
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-6 py-12 text-center text-slate-500">
                                <p class="text-sm font-semibold text-slate-700">{{ __('No statements found.') }}</p>
                                <p class="mt-1 text-xs text-slate-400">{{ __('Generated periodical account statements will appear here.') }}</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @if (method_exists($statements, 'hasPages') && $statements->hasPages())
        <div class="rounded-xl border border-slate-200 bg-white px-4 py-3 shadow-sm">
            {{ $statements->links() }}
        </div>
    @endif
</div>
@endsection
