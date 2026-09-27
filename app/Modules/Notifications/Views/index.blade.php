@extends('layouts.app')

@section('title', __('Notifications'))

@section('breadcrumb')
    <li class="breadcrumb-item active">{{ __('Notifications') }}</li>
@endsection

@section('actions')
    <div class="flex items-center gap-2">
        <form action="{{ route('notifications.retry-failed') }}" method="POST" class="inline">
            @csrf
            <button type="submit" class="btn btn-secondary">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                </svg>
                <span>{{ __('Retry Failed') }}</span>
            </button>
        </form>
        <form action="{{ route('notifications.process-scheduled') }}" method="POST" class="inline">
            @csrf
            <button type="submit" class="btn btn-primary">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <span>{{ __('Process Scheduled') }}</span>
            </button>
        </form>
    </div>
@endsection

@section('content')
<div class="space-y-6">
    <div class="card overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200 dark:divide-slate-700">
                <thead class="bg-slate-50 dark:bg-slate-800/60">
                    <tr>
                        <th class="px-6 py-3.5 text-start text-xs font-semibold text-slate-500 uppercase tracking-wider">{{ __('Type') }}</th>
                        <th class="px-6 py-3.5 text-start text-xs font-semibold text-slate-500 uppercase tracking-wider">{{ __('Channel') }}</th>
                        <th class="px-6 py-3.5 text-start text-xs font-semibold text-slate-500 uppercase tracking-wider">{{ __('Subject') }}</th>
                        <th class="px-6 py-3.5 text-start text-xs font-semibold text-slate-500 uppercase tracking-wider">{{ __('Customer') }}</th>
                        <th class="px-6 py-3.5 text-start text-xs font-semibold text-slate-500 uppercase tracking-wider">{{ __('Status') }}</th>
                        <th class="px-6 py-3.5 text-start text-xs font-semibold text-slate-500 uppercase tracking-wider">{{ __('Priority') }}</th>
                        <th class="px-6 py-3.5 text-start text-xs font-semibold text-slate-500 uppercase tracking-wider">{{ __('Date') }}</th>
                        <th class="px-6 py-3.5 text-end text-xs font-semibold text-slate-500 uppercase tracking-wider">{{ __('Actions') }}</th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-slate-900 divide-y divide-slate-100 dark:divide-slate-800">
                    @forelse($notifications as $notification)
                        <tr class="hover:bg-slate-50/80 dark:hover:bg-slate-800/40 transition-colors {{ $notification->isRead() ? 'opacity-60' : '' }}">
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-slate-900 dark:text-white">
                                {{ $notification->notification_type->label() }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-600 dark:text-slate-300">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-slate-100 dark:bg-slate-800 text-slate-800 dark:text-slate-200">
                                    {{ $notification->channel->label() }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-sm text-slate-700 dark:text-slate-300 max-w-xs truncate">
                                {{ $notification->subject ?? '—' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-600 dark:text-slate-300">
                                {{ $notification->customer?->full_name ?? '—' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                @switch($notification->status->value)
                                    @case('pending')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-amber-50 text-amber-700 border border-amber-200 dark:bg-amber-950/40 dark:text-amber-400 dark:border-amber-800">{{ __('Pending') }}</span>
                                        @break
                                    @case('sent')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-sky-50 text-sky-700 border border-sky-200 dark:bg-sky-950/40 dark:text-sky-400 dark:border-sky-800">{{ __('Sent') }}</span>
                                        @break
                                    @case('delivered')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-emerald-50 text-emerald-700 border border-emerald-200 dark:bg-emerald-950/40 dark:text-emerald-400 dark:border-emerald-800">{{ __('Delivered') }}</span>
                                        @break
                                    @case('failed')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-rose-50 text-rose-700 border border-rose-200 dark:bg-rose-950/40 dark:text-rose-400 dark:border-rose-800">{{ __('Failed') }}</span>
                                        @break
                                    @case('read')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-slate-100 text-slate-700 border border-slate-200 dark:bg-slate-800 dark:text-slate-300 dark:border-slate-700">{{ __('Read') }}</span>
                                        @break
                                    @default
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-slate-100 text-slate-700 border border-slate-200">{{ $notification->status->label() }}</span>
                                @endswitch
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center gap-0.5">
                                    @for($i = 0; $i < min($notification->priority, 5); $i++)
                                        <svg class="w-3.5 h-3.5 {{ $notification->priority >= 8 ? 'text-rose-500' : ($notification->priority >= 5 ? 'text-amber-500' : 'text-slate-300') }}" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                        </svg>
                                    @endfor
                                    <span class="text-xs text-slate-400 ms-1">({{ $notification->priority }}/10)</span>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-500 dark:text-slate-400" dir="ltr">
                                {{ $notification->created_at->format('Y-m-d H:i') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-end text-sm">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('notifications.show', $notification) }}" class="btn btn-sm btn-secondary">
                                        {{ __('View') }}
                                    </a>
                                    @if(!$notification->isRead())
                                        <form action="{{ route('notifications.mark-read', $notification) }}" method="POST" class="inline">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-primary">
                                                {{ __('Mark Read') }}
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-6 py-12 text-center text-slate-500 dark:text-slate-400">
                                {{ __('No notifications found') }}
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if(method_exists($notifications, 'hasPages') && $notifications->hasPages())
            <div class="px-6 py-4 border-t border-slate-200 dark:border-slate-700">
                {{ $notifications->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
