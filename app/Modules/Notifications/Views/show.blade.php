@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold text-gray-900">Notification Details</h1>
        <a href="{{ route('notifications.index') }}" class="text-gray-600 hover:text-gray-900">Back to Notifications</a>
    </div>

    @if(session('success'))
        <div class="bg-emerald-100 border border-emerald-400 text-emerald-700 px-4 py-3 rounded mb-4">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
            {{ session('error') }}
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2">
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex justify-between items-start mb-6">
                    <div>
                        <h2 class="text-xl font-semibold text-gray-900">{{ $notification->subject ?? 'Notification' }}</h2>
                        <p class="text-sm text-gray-500">{{ $notification->notification_type->label() }} via {{ $notification->channel->label() }}</p>
                    </div>
                    @switch($notification->status->value)
                        @case('pending')
                            <span class="px-3 py-1 inline-flex text-sm leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">Pending</span>
                            @break
                        @case('sent')
                            <span class="px-3 py-1 inline-flex text-sm leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">Sent</span>
                            @break
                        @case('delivered')
                            <span class="px-3 py-1 inline-flex text-sm leading-5 font-semibold rounded-full bg-emerald-100 text-emerald-800">Delivered</span>
                            @break
                        @case('failed')
                            <span class="px-3 py-1 inline-flex text-sm leading-5 font-semibold rounded-full bg-red-100 text-red-800">Failed</span>
                            @break
                        @case('read')
                            <span class="px-3 py-1 inline-flex text-sm leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">Read</span>
                            @break
                        @default
                            <span class="px-3 py-1 inline-flex text-sm leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">{{ $notification->status->label() }}</span>
                    @endswitch
                </div>

                <div class="prose max-w-none mb-6">
                    <p class="text-gray-700 text-lg">{{ $notification->message }}</p>
                </div>

                <div class="border-t pt-4">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Notification Details</h3>
                    <dl class="grid grid-cols-1 gap-4">
                        <div class="flex justify-between">
                            <dt class="text-sm text-gray-500">Customer</dt>
                            <dd class="text-sm font-medium text-gray-900">{{ $notification->customer?->full_name ?? 'N/A' }}</dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-sm text-gray-500">Type</dt>
                            <dd class="text-sm font-medium text-gray-900">{{ $notification->notification_type->label() }}</dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-sm text-gray-500">Channel</dt>
                            <dd class="text-sm font-medium text-gray-900">{{ $notification->channel->label() }}</dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-sm text-gray-500">Priority</dt>
                            <dd class="text-sm font-medium text-gray-900">{{ $notification->priority }}/10</dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-sm text-gray-500">Created</dt>
                            <dd class="text-sm font-medium text-gray-900">{{ $notification->created_at->format('M d, Y H:i:s') }}</dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-sm text-gray-500">Scheduled</dt>
                            <dd class="text-sm font-medium text-gray-900">{{ $notification->scheduled_at?->format('M d, Y H:i:s') ?? 'Not scheduled' }}</dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-sm text-gray-500">Sent</dt>
                            <dd class="text-sm font-medium text-gray-900">{{ $notification->sent_at?->format('M d, Y H:i:s') ?? 'Not sent' }}</dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-sm text-gray-500">Delivered</dt>
                            <dd class="text-sm font-medium text-gray-900">{{ $notification->delivered_at?->format('M d, Y H:i:s') ?? 'Not delivered' }}</dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-sm text-gray-500">Read</dt>
                            <dd class="text-sm font-medium text-gray-900">{{ $notification->read_at?->format('M d, Y H:i:s') ?? 'Not read' }}</dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-sm text-gray-500">Retry Count</dt>
                            <dd class="text-sm font-medium text-gray-900">{{ $notification->retry_count }}</dd>
                        </div>
                    </dl>
                </div>

                @if($notification->failure_reason)
                    <div class="mt-4 bg-red-50 border border-red-200 rounded-lg p-4">
                        <h3 class="text-sm font-semibold text-red-800">Failure Information</h3>
                        <p class="text-sm text-red-700 mt-1">{{ $notification->failure_reason }}</p>
                        <p class="text-xs text-red-600 mt-1">Failed at: {{ $notification->failed_at->format('M d, Y H:i:s') }}</p>
                    </div>
                @endif
            </div>
        </div>

        <div>
            <div class="bg-white rounded-lg shadow p-6 mb-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Actions</h3>
                <div class="space-y-3">
                    @if(!$notification->isRead())
                        <form action="{{ route('notifications.mark-read', $notification) }}" method="POST">
                            @csrf
                            <button type="submit" class="w-full bg-emerald-600 text-white px-4 py-2 rounded-lg hover:bg-emerald-700 text-sm">Mark as Read</button>
                        </form>
                    @endif

                    @if($notification->isFailed() && $notification->canBeRetried())
                        <form action="{{ route('notifications.retry-failed') }}" method="POST">
                            @csrf
                            <button type="submit" class="w-full bg-yellow-600 text-white px-4 py-2 rounded-lg hover:bg-yellow-700 text-sm">Retry Notification</button>
                        </form>
                    @endif
                </div>
            </div>

            @if($notification->data)
                <div class="bg-white rounded-lg shadow p-6 mb-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Data</h3>
                    <pre class="text-xs text-gray-600 bg-gray-50 p-3 rounded overflow-auto">{{ json_encode($notification->data, JSON_PRETTY_PRINT) }}</pre>
                </div>
            @endif

            @if($notification->metadata)
                <div class="bg-white rounded-lg shadow p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Metadata</h3>
                    <pre class="text-xs text-gray-600 bg-gray-50 p-3 rounded overflow-auto">{{ json_encode($notification->metadata, JSON_PRETTY_PRINT) }}</pre>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
