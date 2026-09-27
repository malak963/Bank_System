@extends('layouts.app')

@section('title', __('Security Events'))

@section('breadcrumb')
    <li class="breadcrumb-item active">{{ __('Security Events') }}</li>
@endsection

@section('content')
<div class="mx-auto max-w-7xl space-y-6 px-4 py-8 sm:px-6 lg:px-8">
    <!-- Alert Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        @if($criticalEvents->count() > 0)
            <div class="rounded-lg border border-rose-200 bg-rose-50 p-4">
                <div class="flex items-center gap-2 mb-1">
                    <svg class="h-5 w-5 text-rose-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                    </svg>
                    <h3 class="font-semibold text-rose-800 text-sm">{{ __('Critical Security Events') }}</h3>
                </div>
                <p class="text-xs text-rose-700">{{ __(':count unresolved critical events require attention', ['count' => $criticalEvents->count()]) }}</p>
            </div>
        @endif

        @if($fraudAlerts->count() > 0)
            <div class="rounded-lg border border-amber-200 bg-amber-50 p-4">
                <div class="flex items-center gap-2 mb-1">
                    <svg class="h-5 w-5 text-amber-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <h3 class="font-semibold text-amber-800 text-sm">{{ __('Fraud Alerts') }}</h3>
                </div>
                <p class="text-xs text-amber-700">{{ __(':count fraud alerts flagged for review', ['count' => $fraudAlerts->count()]) }}</p>
            </div>
        @endif
    </div>

    <!-- Security Events Table -->
    <div class="bank-card">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200">
                <thead class="bg-slate-50">
                    <tr>
                        <th class="px-5 py-3.5 text-start text-xs font-semibold uppercase tracking-wider text-slate-500">{{ __('Type') }}</th>
                        <th class="px-5 py-3.5 text-start text-xs font-semibold uppercase tracking-wider text-slate-500">{{ __('Severity Level') }}</th>
                        <th class="px-5 py-3.5 text-start text-xs font-semibold uppercase tracking-wider text-slate-500">{{ __('User / Customer') }}</th>
                        <th class="px-5 py-3.5 text-start text-xs font-semibold uppercase tracking-wider text-slate-500">{{ __('Description') }}</th>
                        <th class="px-5 py-3.5 text-start text-xs font-semibold uppercase tracking-wider text-slate-500">{{ __('Status') }}</th>
                        <th class="px-5 py-3.5 text-start text-xs font-semibold uppercase tracking-wider text-slate-500">{{ __('Recorded At') }}</th>
                        <th class="px-5 py-3.5 text-end text-xs font-semibold uppercase tracking-wider text-slate-500">{{ __('Actions') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 bg-white">
                    @forelse($events as $event)
                        <tr class="transition hover:bg-slate-50">
                            <td class="px-5 py-4 whitespace-nowrap text-sm font-semibold text-slate-900">
                                {{ $event->event_type->label() }}
                            </td>
                            <td class="px-5 py-4 whitespace-nowrap text-sm">
                                <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-semibold ring-1 ring-inset {{ is_object($event->security_level) && method_exists($event->security_level, 'badgeColor') ? $event->security_level->badgeColor() : 'bg-slate-50 text-slate-700 ring-slate-200' }}">
                                    {{ is_object($event->security_level) && method_exists($event->security_level, 'label') ? $event->security_level->label() : ($event->security_level ?? '-') }}
                                </span>
                            </td>
                            <td class="px-5 py-4 whitespace-nowrap text-sm text-slate-600">
                                {{ $event->user?->name ?? $event->customer?->full_name ?? __('System') }}
                            </td>
                            <td class="px-5 py-4 text-sm text-slate-600 max-w-xs truncate">
                                {{ $event->description }}
                            </td>
                            <td class="px-5 py-4 whitespace-nowrap text-sm">
                                @if($event->isResolved())
                                    <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-semibold ring-1 ring-inset bg-emerald-50 text-emerald-700 ring-emerald-200">{{ __('Resolved') }}</span>
                                @else
                                    <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-semibold ring-1 ring-inset bg-amber-50 text-amber-700 ring-amber-200">{{ __('Unresolved') }}</span>
                                @endif
                            </td>
                            <td class="px-5 py-4 whitespace-nowrap text-sm text-slate-500 font-mono" dir="ltr">
                                {{ $event->created_at->format('Y-m-d H:i') }}
                            </td>
                            <td class="px-5 py-4 whitespace-nowrap text-end text-sm">
                                <a href="{{ route('security.show', $event) }}" class="btn btn-sm btn-secondary">
                                    {{ __('View') }}
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center text-slate-500">
                                <p class="text-sm font-semibold text-slate-700">{{ __('No security events found.') }}</p>
                                <p class="mt-1 text-xs text-slate-400">{{ __('Audit log and fraud monitoring events will be recorded here.') }}</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @if (method_exists($events, 'hasPages') && $events->hasPages())
        <div class="rounded-xl border border-slate-200 bg-white px-4 py-3 shadow-sm">
            {{ $events->links() }}
        </div>
    @endif
</div>
@endsection
