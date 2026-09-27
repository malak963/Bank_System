@extends('layouts.app')

@section('title', __('Bills & Payments'))

@section('breadcrumb')
    <li class="breadcrumb-item active">{{ __('Bills & Payments') }}</li>
@endsection

@section('actions')
    <a href="{{ route('bills-payments.overdue') }}" class="btn btn-secondary text-rose-700 hover:text-rose-800">
        <span>{{ __('Overdue Bills') }}</span>
    </a>
    <a href="{{ route('bills-payments.upcoming') }}" class="btn btn-secondary text-blue-700 hover:text-blue-800">
        <span>{{ __('Upcoming Bills') }}</span>
    </a>
    <a href="{{ route('bills-payments.create') }}" class="btn btn-primary">
        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"></path>
        </svg>
        <span>{{ __('New Bill') }}</span>
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
                        <th class="px-5 py-3.5 text-start text-xs font-semibold uppercase tracking-wider text-slate-500">{{ __('Provider') }}</th>
                        <th class="px-5 py-3.5 text-start text-xs font-semibold uppercase tracking-wider text-slate-500">{{ __('Type') }}</th>
                        <th class="px-5 py-3.5 text-start text-xs font-semibold uppercase tracking-wider text-slate-500">{{ __('Amount') }}</th>
                        <th class="px-5 py-3.5 text-start text-xs font-semibold uppercase tracking-wider text-slate-500">{{ __('Due Date') }}</th>
                        <th class="px-5 py-3.5 text-start text-xs font-semibold uppercase tracking-wider text-slate-500">{{ __('Status') }}</th>
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
                            <td class="px-5 py-4 whitespace-nowrap text-sm">
                                <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-semibold ring-1 ring-inset {{ $bill->status->getColor() === 'emerald' ? 'bg-emerald-50 text-emerald-700 ring-emerald-200' : ($bill->status->getColor() === 'red' ? 'bg-rose-50 text-rose-700 ring-rose-200' : 'bg-amber-50 text-amber-700 ring-amber-200') }}">
                                    {{ $bill->status->getLabel() }}
                                </span>
                            </td>
                            <td class="px-5 py-4 whitespace-nowrap text-end text-sm">
                                <a href="{{ route('bills-payments.show', $bill->id) }}" class="btn btn-sm btn-secondary">
                                    {{ __('View') }}
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center text-slate-500">
                                <p class="text-sm font-semibold text-slate-700">{{ __('No bills found.') }}</p>
                                <p class="mt-1 text-xs text-slate-400">{{ __('Utility, telecommunication, and invoice payments will appear here.') }}</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @if (method_exists($bills, 'hasPages') && $bills->hasPages())
        <div class="rounded-xl border border-slate-200 bg-white px-4 py-3 shadow-sm">
            {{ $bills->links() }}
        </div>
    @endif
</div>
@endsection
