@extends('layouts.app')

@section('title', __('Customer Service Tickets'))

@section('breadcrumb')
    <li class="breadcrumb-item active">{{ __('Customer Service Tickets') }}</li>
@endsection

@section('actions')
    <a href="{{ route('customerService.create') }}" class="btn btn-primary">
        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"></path>
        </svg>
        <span>{{ __('New Ticket') }}</span>
    </a>
@endsection

@section('content')
<div class="mx-auto max-w-7xl space-y-6 px-4 py-8 sm:px-6 lg:px-8">
    <!-- Statistics Cards -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <div class="bank-card p-4">
            <p class="text-xs font-semibold uppercase text-slate-500">{{ __('Total Tickets') }}</p>
            <p class="text-2xl font-bold text-slate-900 mt-1 font-mono" dir="ltr">{{ $statistics['total'] }}</p>
        </div>
        <div class="bank-card p-4">
            <p class="text-xs font-semibold uppercase text-emerald-700">{{ __('Open Tickets') }}</p>
            <p class="text-2xl font-bold text-emerald-800 mt-1 font-mono" dir="ltr">{{ $statistics['open'] }}</p>
        </div>
        <div class="bank-card p-4">
            <p class="text-xs font-semibold uppercase text-rose-700">{{ __('Overdue') }}</p>
            <p class="text-2xl font-bold text-rose-800 mt-1 font-mono" dir="ltr">{{ $statistics['overdue'] ?? 0 }}</p>
        </div>
        <div class="bank-card p-4">
            <p class="text-xs font-semibold uppercase text-slate-500">{{ __('Avg Satisfaction') }}</p>
            <p class="text-2xl font-bold text-slate-900 mt-1 font-mono" dir="ltr">{{ $statistics['avg_satisfaction'] ?? __('N/A') }}/5</p>
        </div>
    </div>

    <!-- Alert Cards -->
    @if($overdueTickets->count() > 0 || $urgentTickets->count() > 0)
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            @if($overdueTickets->count() > 0)
                <div class="rounded-lg border border-rose-200 bg-rose-50 p-4">
                    <div class="flex items-center gap-2 mb-1">
                        <svg class="h-5 w-5 text-rose-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <h3 class="font-semibold text-rose-800 text-sm">{{ __('Overdue Tickets') }}</h3>
                    </div>
                    <p class="text-xs text-rose-700">{{ __(':count tickets require immediate attention', ['count' => $overdueTickets->count()]) }}</p>
                </div>
            @endif

            @if($urgentTickets->count() > 0)
                <div class="rounded-lg border border-amber-200 bg-amber-50 p-4">
                    <div class="flex items-center gap-2 mb-1">
                        <svg class="h-5 w-5 text-amber-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                        </svg>
                        <h3 class="font-semibold text-amber-800 text-sm">{{ __('Urgent Tickets') }}</h3>
                    </div>
                    <p class="text-xs text-amber-700">{{ __(':count urgent tickets pending', ['count' => $urgentTickets->count()]) }}</p>
                </div>
            @endif
        </div>
    @endif

    <!-- Tickets Table -->
    <div class="bank-card">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200">
                <thead class="bg-slate-50">
                    <tr>
                        <th class="px-5 py-3.5 text-start text-xs font-semibold uppercase tracking-wider text-slate-500">{{ __('Ticket #') }}</th>
                        <th class="px-5 py-3.5 text-start text-xs font-semibold uppercase tracking-wider text-slate-500">{{ __('Subject') }}</th>
                        <th class="px-5 py-3.5 text-start text-xs font-semibold uppercase tracking-wider text-slate-500">{{ __('Category') }}</th>
                        <th class="px-5 py-3.5 text-start text-xs font-semibold uppercase tracking-wider text-slate-500">{{ __('Priority') }}</th>
                        <th class="px-5 py-3.5 text-start text-xs font-semibold uppercase tracking-wider text-slate-500">{{ __('Status') }}</th>
                        <th class="px-5 py-3.5 text-start text-xs font-semibold uppercase tracking-wider text-slate-500">{{ __('Customer') }}</th>
                        <th class="px-5 py-3.5 text-start text-xs font-semibold uppercase tracking-wider text-slate-500">{{ __('Assigned To') }}</th>
                        <th class="px-5 py-3.5 text-end text-xs font-semibold uppercase tracking-wider text-slate-500">{{ __('Actions') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 bg-white">
                    @forelse($tickets as $ticket)
                        <tr class="transition hover:bg-slate-50">
                            <td class="px-5 py-4 whitespace-nowrap text-sm font-semibold text-slate-900 font-mono" dir="ltr">
                                {{ $ticket->ticket_number }}
                            </td>
                            <td class="px-5 py-4 whitespace-nowrap text-sm font-medium text-slate-900">
                                <a href="{{ route('customerService.show', $ticket) }}" class="hover:text-emerald-700">
                                    {{ Str::limit($ticket->subject, 40) }}
                                </a>
                            </td>
                            <td class="px-5 py-4 whitespace-nowrap text-sm text-slate-600">
                                {{ is_object($ticket->category) && method_exists($ticket->category, 'label') ? $ticket->category->label() : ($ticket->category ?? '-') }}
                            </td>
                            <td class="px-5 py-4 whitespace-nowrap text-sm">
                                <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-semibold ring-1 ring-inset {{ is_object($ticket->priority) && method_exists($ticket->priority, 'badgeColor') ? $ticket->priority->badgeColor() : 'bg-slate-50 text-slate-700 ring-slate-200' }}">
                                    {{ is_object($ticket->priority) && method_exists($ticket->priority, 'label') ? $ticket->priority->label() : ($ticket->priority ?? '-') }}
                                </span>
                            </td>
                            <td class="px-5 py-4 whitespace-nowrap text-sm">
                                <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-semibold ring-1 ring-inset {{ is_object($ticket->status) && method_exists($ticket->status, 'badgeColor') ? $ticket->status->badgeColor() : 'bg-slate-50 text-slate-700 ring-slate-200' }}">
                                    {{ is_object($ticket->status) && method_exists($ticket->status, 'label') ? $ticket->status->label() : ($ticket->status ?? '-') }}
                                </span>
                            </td>
                            <td class="px-5 py-4 whitespace-nowrap text-sm text-slate-600">
                                {{ $ticket->customer?->full_name ?? $ticket->customer_name ?? __('Guest') }}
                            </td>
                            <td class="px-5 py-4 whitespace-nowrap text-sm text-slate-600">
                                {{ $ticket->assignedTo?->name ?? __('Unassigned') }}
                            </td>
                            <td class="px-5 py-4 whitespace-nowrap text-end text-sm">
                                <a href="{{ route('customerService.show', $ticket) }}" class="btn btn-sm btn-secondary">
                                    {{ __('View') }}
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-6 py-12 text-center text-slate-500">
                                <p class="text-sm font-semibold text-slate-700">{{ __('No tickets found.') }}</p>
                                <p class="mt-1 text-xs text-slate-400">{{ __('Customer support tickets will appear here.') }}</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @if (method_exists($tickets, 'hasPages') && $tickets->hasPages())
        <div class="rounded-xl border border-slate-200 bg-white px-4 py-3 shadow-sm">
            {{ $tickets->links() }}
        </div>
    @endif
</div>
@endsection
