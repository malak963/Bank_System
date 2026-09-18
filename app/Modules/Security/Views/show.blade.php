@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold text-gray-900">{{ __('Security Event Details') }}</h1>
        <a href="{{ route('security.index') }}" class="text-gray-600 hover:text-gray-900">{{ __('Back to Security Events') }}</a>
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
                        <h2 class="text-xl font-semibold text-gray-900">{{ $event->event_type->label() }}</h2>
                        <p class="text-sm text-gray-500">{{ $event->description }}</p>
                    </div>
                    @switch($event->security_level->value)
                        @case('low')
                            <span class="px-3 py-1 inline-flex text-sm leading-5 font-semibold rounded-full bg-green-100 text-green-800">{{ __('Low') }}</span>
                            @break
                        @case('medium')
                            <span class="px-3 py-1 inline-flex text-sm leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">{{ __('Medium') }}</span>
                            @break
                        @case('high')
                            <span class="px-3 py-1 inline-flex text-sm leading-5 font-semibold rounded-full bg-orange-100 text-orange-800">{{ __('High') }}</span>
                            @break
                        @case('critical')
                            <span class="px-3 py-1 inline-flex text-sm leading-5 font-semibold rounded-full bg-red-100 text-red-800">{{ __('Critical') }}</span>
                            @break
                    @endswitch
                </div>

                <div class="border-t pt-4">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">{{ __('Event Information') }}</h3>
                    <dl class="grid grid-cols-1 gap-4">
                        <div class="flex justify-between">
                            <dt class="text-sm text-gray-500">User</dt>
                            <dd class="text-sm font-medium text-gray-900">{{ $event->user?->name ?? 'N/A' }}</dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-sm text-gray-500">Customer</dt>
                            <dd class="text-sm font-medium text-gray-900">{{ $event->customer?->full_name ?? 'N/A' }}</dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-sm text-gray-500">IP Address</dt>
                            <dd class="text-sm font-medium text-gray-900">{{ $event->ip_address ?? 'N/A' }}</dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-sm text-gray-500">Location</dt>
                            <dd class="text-sm font-medium text-gray-900">{{ $event->location ?? 'N/A' }}</dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-sm text-gray-500">Source</dt>
                            <dd class="text-sm font-medium text-gray-900">{{ $event->source ?? 'N/A' }}</dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-sm text-gray-500">Created</dt>
                            <dd class="text-sm font-medium text-gray-900">{{ $event->created_at->format('M d, Y H:i:s') }}</dd>
                        </div>
                        @if($event->is_resolved)
                            <div class="flex justify-between">
                                <dt class="text-sm text-gray-500">Resolved</dt>
                                <dd class="text-sm font-medium text-gray-900">{{ $event->resolved_at->format('M d, Y H:i:s') }}</dd>
                            </div>
                            <div class="flex justify-between">
                                <dt class="text-sm text-gray-500">Resolved By</dt>
                                <dd class="text-sm font-medium text-gray-900">{{ $event->resolver?->name ?? 'N/A' }}</dd>
                            </div>
                        @endif
                        @if($event->blocked)
                            <div class="flex justify-between">
                                <dt class="text-sm text-gray-500">Blocked</dt>
                                <dd class="text-sm font-medium text-red-600">Yes {{ $event->blocked_until ? 'until ' . $event->blocked_until->format('M d, Y H:i') : '' }}</dd>
                            </div>
                        @endif
                    </dl>
                </div>

                @if($event->resolution_notes)
                    <div class="mt-4 bg-emerald-50 border border-emerald-200 rounded-lg p-4">
                        <h3 class="text-sm font-semibold text-emerald-800">{{ __('Resolution Notes') }}</h3>
                        <p class="text-sm text-emerald-700 mt-1">{{ $event->resolution_notes }}</p>
                    </div>
                @endif
            </div>
        </div>

        <div>
            <div class="bg-white rounded-lg shadow p-6 mb-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">{{ __('Actions') }}</h3>
                <div class="space-y-3">
                    @if(!$event->is_resolved)
                        <form action="{{ route('security.resolve', $event) }}" method="POST">
                            @csrf
                            <div class="mb-3">
                                <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('Resolution Notes') }}</label>
                                <textarea name="resolution_notes" required rows="2" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500" placeholder="{{ __('Enter resolution notes...') }}"></textarea>
                            </div>
                            <button type="submit" class="w-full bg-emerald-600 text-white px-4 py-2 rounded-lg hover:bg-emerald-700 text-sm">{{ __('Resolve Event') }}</button>
                        </form>
                    @endif

                    @if(!$event->blocked)
                        <form action="{{ route('security.block', $event) }}" method="POST">
                            @csrf
                            <div class="mb-3">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Block Duration (hours)</label>
                                <input type="number" name="block_duration_hours" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500" placeholder="{{ __('Leave empty for permanent') }}">
                            </div>
                            <button type="submit" class="w-full bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700 text-sm">Block User</button>
                        </form>
                    @else
                        <form action="{{ route('security.unblock', $event) }}" method="POST">
                            @csrf
                            <button type="submit" class="w-full bg-yellow-600 text-white px-4 py-2 rounded-lg hover:bg-yellow-700 text-sm">{{ __('Unblock User') }}</button>
                        </form>
                    @endif
                </div>
            </div>

            @if($event->details)
                <div class="bg-white rounded-lg shadow p-6 mb-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">{{ __('Event Details') }}</h3>
                    <pre class="text-xs text-gray-600 bg-gray-50 p-3 rounded overflow-auto">{{ json_encode($event->details, JSON_PRETTY_PRINT) }}</pre>
                </div>
            @endif

            @if($event->metadata)
                <div class="bg-white rounded-lg shadow p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Metadata</h3>
                    <pre class="text-xs text-gray-600 bg-gray-50 p-3 rounded overflow-auto">{{ json_encode($event->metadata, JSON_PRETTY_PRINT) }}</pre>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
