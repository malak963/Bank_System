@props([
    'status' => 'pending',
    'label' => null,
])

@php
    $statusKey = strtolower((string) ($status?->value ?? $status));
    $badgeStyles = [
        'completed' => 'bg-emerald-50 text-emerald-700',
        'paid' => 'bg-emerald-50 text-emerald-700',
        'active' => 'bg-emerald-50 text-emerald-700',
        'open' => 'bg-emerald-50 text-emerald-700',
        'processing' => 'bg-blue-50 text-blue-700',
        'scheduled' => 'bg-slate-100 text-slate-700',
        'pending' => 'bg-amber-50 text-amber-800',
        'failed' => 'bg-rose-50 text-rose-700',
        'cancelled' => 'bg-slate-100 text-slate-600',
        'reversed' => 'bg-purple-50 text-purple-700',
        'refunded' => 'bg-orange-50 text-orange-700',
        'overdue' => 'bg-rose-50 text-rose-700',
    ];
    $style = $badgeStyles[$statusKey] ?? 'bg-slate-100 text-slate-700';

    $statusTranslations = [
        'completed' => __('Completed'),
        'paid' => __('Paid'),
        'active' => __('Active'),
        'open' => __('Open'),
        'processing' => __('Processing'),
        'scheduled' => __('Scheduled'),
        'pending' => __('Pending'),
        'failed' => __('Failed'),
        'cancelled' => __('Cancelled'),
        'reversed' => __('Reversed'),
        'refunded' => __('Refunded'),
        'overdue' => __('Overdue'),
    ];
    $displayText = $label ?? ($statusTranslations[$statusKey] ?? __(ucfirst(str_replace('_', ' ', $statusKey))));
@endphp

<span class="inline-block px-2.5 py-0.5 rounded text-xs font-medium {{ $style }}">
    {{ $displayText }}
</span>
