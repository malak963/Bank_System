@extends('layouts.app')

@section('title', __('Notification Details'))

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('notifications.index') }}">{{ __('Notifications') }}</a></li>
    <li class="breadcrumb-item active">{{ __('Notification Details') }}</li>
@endsection

@section('actions')
    <div class="flex items-center gap-2">
        <a href="{{ route('notifications.index') }}" class="btn btn-secondary">
            <svg class="w-4 h-4 rtl:rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
            <span>{{ __('Back') }}</span>
        </a>
        @if(!$notification->isRead())
            <form action="{{ route('notifications.mark-read', $notification) }}" method="POST" class="inline">
                @csrf
                <button type="submit" class="btn btn-primary">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                    <span>{{ __('Mark as Read') }}</span>
                </button>
            </form>
        @endif
        @if($notification->isFailed() && $notification->canBeRetried())
            <form action="{{ route('notifications.retry-failed') }}" method="POST" class="inline">
                @csrf
                <button type="submit" class="btn btn-secondary">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                    </svg>
                    <span>{{ __('Retry Notification') }}</span>
                </button>
            </form>
        @endif
    </div>
@endsection

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <div class="lg:col-span-2 space-y-6">
        <div class="card p-6">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-6 border-b border-slate-100 dark:border-slate-800">
                <div>
                    <h2 class="text-xl font-bold text-slate-900 dark:text-white">{{ $notification->subject ?? __('Notification') }}</h2>
                    <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">
                        {{ $notification->notification_type->label() }} &bull; {{ $notification->channel->label() }}
                    </p>
                </div>
                <div>
                    @switch($notification->status->value)
                        @case('pending')
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-amber-50 text-amber-700 border border-amber-200 dark:bg-amber-950/40 dark:text-amber-400 dark:border-amber-800">{{ __('Pending') }}</span>
                            @break
                        @case('sent')
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-sky-50 text-sky-700 border border-sky-200 dark:bg-sky-950/40 dark:text-sky-400 dark:border-sky-800">{{ __('Sent') }}</span>
                            @break
                        @case('delivered')
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200 dark:bg-emerald-950/40 dark:text-emerald-400 dark:border-emerald-800">{{ __('Delivered') }}</span>
                            @break
                        @case('failed')
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-rose-50 text-rose-700 border border-rose-200 dark:bg-rose-950/40 dark:text-rose-400 dark:border-rose-800">{{ __('Failed') }}</span>
                            @break
                        @case('read')
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-slate-100 text-slate-700 border border-slate-200 dark:bg-slate-800 dark:text-slate-300 dark:border-slate-700">{{ __('Read') }}</span>
                            @break
                        @default
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-slate-100 text-slate-700 border border-slate-200">{{ $notification->status->label() }}</span>
                    @endswitch
                </div>
            </div>

            <div class="py-6">
                <h3 class="text-xs font-semibold uppercase tracking-wider text-slate-400 mb-2">{{ __('Message') }}</h3>
                <div class="p-4 rounded-xl bg-slate-50 dark:bg-slate-800/50 border border-slate-100 dark:border-slate-800 text-slate-800 dark:text-slate-200 whitespace-pre-wrap leading-relaxed">
                    {{ $notification->message }}
                </div>
            </div>

            <div class="pt-6 border-t border-slate-100 dark:border-slate-800">
                <h3 class="text-base font-semibold text-slate-900 dark:text-white mb-4">{{ __('Notification Details') }}</h3>
                <dl class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div class="p-3 rounded-lg bg-slate-50/60 dark:bg-slate-800/40 border border-slate-100 dark:border-slate-800/80">
                        <dt class="text-xs font-medium text-slate-500 dark:text-slate-400">{{ __('Customer') }}</dt>
                        <dd class="text-sm font-semibold text-slate-900 dark:text-white mt-1">{{ $notification->customer?->full_name ?? '—' }}</dd>
                    </div>
                    <div class="p-3 rounded-lg bg-slate-50/60 dark:bg-slate-800/40 border border-slate-100 dark:border-slate-800/80">
                        <dt class="text-xs font-medium text-slate-500 dark:text-slate-400">{{ __('Type') }}</dt>
                        <dd class="text-sm font-semibold text-slate-900 dark:text-white mt-1">{{ $notification->notification_type->label() }}</dd>
                    </div>
                    <div class="p-3 rounded-lg bg-slate-50/60 dark:bg-slate-800/40 border border-slate-100 dark:border-slate-800/80">
                        <dt class="text-xs font-medium text-slate-500 dark:text-slate-400">{{ __('Channel') }}</dt>
                        <dd class="text-sm font-semibold text-slate-900 dark:text-white mt-1">{{ $notification->channel->label() }}</dd>
                    </div>
                    <div class="p-3 rounded-lg bg-slate-50/60 dark:bg-slate-800/40 border border-slate-100 dark:border-slate-800/80">
                        <dt class="text-xs font-medium text-slate-500 dark:text-slate-400">{{ __('Priority') }}</dt>
                        <dd class="text-sm font-semibold text-slate-900 dark:text-white mt-1">{{ $notification->priority }}/10</dd>
                    </div>
                    <div class="p-3 rounded-lg bg-slate-50/60 dark:bg-slate-800/40 border border-slate-100 dark:border-slate-800/80">
                        <dt class="text-xs font-medium text-slate-500 dark:text-slate-400">{{ __('Created At') }}</dt>
                        <dd class="text-sm font-semibold text-slate-900 dark:text-white mt-1" dir="ltr">{{ $notification->created_at->format('Y-m-d H:i:s') }}</dd>
                    </div>
                    <div class="p-3 rounded-lg bg-slate-50/60 dark:bg-slate-800/40 border border-slate-100 dark:border-slate-800/80">
                        <dt class="text-xs font-medium text-slate-500 dark:text-slate-400">{{ __('Scheduled At') }}</dt>
                        <dd class="text-sm font-semibold text-slate-900 dark:text-white mt-1" dir="ltr">{{ $notification->scheduled_at?->format('Y-m-d H:i:s') ?? '—' }}</dd>
                    </div>
                    <div class="p-3 rounded-lg bg-slate-50/60 dark:bg-slate-800/40 border border-slate-100 dark:border-slate-800/80">
                        <dt class="text-xs font-medium text-slate-500 dark:text-slate-400">{{ __('Sent At') }}</dt>
                        <dd class="text-sm font-semibold text-slate-900 dark:text-white mt-1" dir="ltr">{{ $notification->sent_at?->format('Y-m-d H:i:s') ?? '—' }}</dd>
                    </div>
                    <div class="p-3 rounded-lg bg-slate-50/60 dark:bg-slate-800/40 border border-slate-100 dark:border-slate-800/80">
                        <dt class="text-xs font-medium text-slate-500 dark:text-slate-400">{{ __('Delivered At') }}</dt>
                        <dd class="text-sm font-semibold text-slate-900 dark:text-white mt-1" dir="ltr">{{ $notification->delivered_at?->format('Y-m-d H:i:s') ?? '—' }}</dd>
                    </div>
                    <div class="p-3 rounded-lg bg-slate-50/60 dark:bg-slate-800/40 border border-slate-100 dark:border-slate-800/80">
                        <dt class="text-xs font-medium text-slate-500 dark:text-slate-400">{{ __('Read At') }}</dt>
                        <dd class="text-sm font-semibold text-slate-900 dark:text-white mt-1" dir="ltr">{{ $notification->read_at?->format('Y-m-d H:i:s') ?? '—' }}</dd>
                    </div>
                    <div class="p-3 rounded-lg bg-slate-50/60 dark:bg-slate-800/40 border border-slate-100 dark:border-slate-800/80">
                        <dt class="text-xs font-medium text-slate-500 dark:text-slate-400">{{ __('Retry Count') }}</dt>
                        <dd class="text-sm font-semibold text-slate-900 dark:text-white mt-1">{{ $notification->retry_count }}</dd>
                    </div>
                </dl>
            </div>

            @if($notification->failure_reason)
                <div class="mt-6 p-4 rounded-xl bg-rose-50 dark:bg-rose-950/30 border border-rose-200 dark:border-rose-900/40">
                    <h3 class="text-sm font-semibold text-rose-800 dark:text-rose-400 flex items-center gap-2">
                        <svg class="w-4 h-4 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                        </svg>
                        <span>{{ __('Failure Information') }}</span>
                    </h3>
                    <p class="text-sm text-rose-700 dark:text-rose-300 mt-2">{{ $notification->failure_reason }}</p>
                    @if($notification->failed_at)
                        <p class="text-xs text-rose-500 mt-1" dir="ltr">{{ __('Failed at') }}: {{ $notification->failed_at->format('Y-m-d H:i:s') }}</p>
                    @endif
                </div>
            @endif
        </div>
    </div>

    <div class="space-y-6">
        @if($notification->data)
            <div class="card p-6">
                <h3 class="text-base font-semibold text-slate-900 dark:text-white mb-3">{{ __('Payload Data') }}</h3>
                <pre class="text-xs text-slate-700 dark:text-slate-300 bg-slate-50 dark:bg-slate-800/80 p-3.5 rounded-lg overflow-auto border border-slate-100 dark:border-slate-800 font-mono" dir="ltr">{{ json_encode($notification->data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
            </div>
        @endif

        @if($notification->metadata)
            <div class="card p-6">
                <h3 class="text-base font-semibold text-slate-900 dark:text-white mb-3">{{ __('Metadata') }}</h3>
                <pre class="text-xs text-slate-700 dark:text-slate-300 bg-slate-50 dark:bg-slate-800/80 p-3.5 rounded-lg overflow-auto border border-slate-100 dark:border-slate-800 font-mono" dir="ltr">{{ json_encode($notification->metadata, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
            </div>
        @endif
    </div>
</div>
@endsection
