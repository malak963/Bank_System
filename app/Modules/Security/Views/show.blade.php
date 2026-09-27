@extends('layouts.app')

@section('title', __('Security Event Details'))

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('security.index') }}">{{ __('Security Events') }}</a></li>
    <li class="breadcrumb-item active font-mono">#{{ $event->id }}</li>
@endsection

@section('actions')
    <a href="{{ route('security.index') }}" class="btn btn-secondary">
        {{ __('Back to Security Events') }}
    </a>
@endsection

@section('content')
<div class="mx-auto max-w-7xl space-y-6 px-4 py-8 sm:px-6 lg:px-8">
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 space-y-6">
            <div class="bank-card">
                <div class="bank-card-header flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                    <div>
                        <span class="text-xs font-semibold uppercase tracking-wider text-slate-500">{{ __('Event') }} #{{ $event->id }}</span>
                        <h2 class="text-xl font-bold text-slate-900 mt-0.5">{{ $event->event_type->label() }}</h2>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-semibold ring-1 ring-inset {{ $event->security_level->badgeColor() }}">
                            {{ $event->security_level->label() }}
                        </span>
                        @if($event->isResolved())
                            <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-semibold ring-1 ring-inset bg-emerald-50 text-emerald-700 ring-emerald-200">{{ __('Resolved') }}</span>
                        @else
                            <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-semibold ring-1 ring-inset bg-amber-50 text-amber-700 ring-amber-200">{{ __('Unresolved') }}</span>
                        @endif
                    </div>
                </div>

                <div class="bank-card-body">
                    <p class="text-sm text-slate-800 bg-slate-50 p-4 rounded-lg border border-slate-100 mb-6">
                        {{ $event->description }}
                    </p>

                    <h3 class="text-sm font-semibold text-slate-900 mb-4">{{ __('Event Attributes & Context') }}</h3>
                    <dl class="grid grid-cols-1 sm:grid-cols-2 gap-x-6 gap-y-3 text-xs">
                        <div class="flex justify-between py-1 border-b border-slate-50">
                            <dt class="text-slate-500">{{ __('User') }}</dt>
                            <dd class="font-medium text-slate-900">{{ $event->user?->name ?? __('N/A') }}</dd>
                        </div>
                        <div class="flex justify-between py-1 border-b border-slate-50">
                            <dt class="text-slate-500">{{ __('Customer') }}</dt>
                            <dd class="font-medium text-slate-900">{{ $event->customer?->full_name ?? __('N/A') }}</dd>
                        </div>
                        <div class="flex justify-between py-1 border-b border-slate-50">
                            <dt class="text-slate-500">{{ __('IP Address') }}</dt>
                            <dd class="font-medium text-slate-900 font-mono" dir="ltr">{{ $event->ip_address ?? __('N/A') }}</dd>
                        </div>
                        <div class="flex justify-between py-1 border-b border-slate-50">
                            <dt class="text-slate-500">{{ __('User Agent') }}</dt>
                            <dd class="font-medium text-slate-900 max-w-[200px] truncate" title="{{ $event->user_agent }}">{{ $event->user_agent ?? __('N/A') }}</dd>
                        </div>
                        <div class="flex justify-between py-1 border-b border-slate-50">
                            <dt class="text-slate-500">{{ __('Device Fingerprint') }}</dt>
                            <dd class="font-medium text-slate-900 font-mono" dir="ltr">{{ $event->device_id ? Str::limit($event->device_id, 16) : __('N/A') }}</dd>
                        </div>
                        <div class="flex justify-between py-1 border-b border-slate-50">
                            <dt class="text-slate-500">{{ __('Timestamp') }}</dt>
                            <dd class="font-medium text-slate-900 font-mono" dir="ltr">{{ $event->created_at->format('Y-m-d H:i:s') }}</dd>
                        </div>
                    </dl>

                    @if($event->isResolved())
                        <div class="mt-6 rounded-lg border border-emerald-200 bg-emerald-50 p-4">
                            <h4 class="text-xs font-semibold text-emerald-900">{{ __('Resolution Notes') }}</h4>
                            <p class="text-xs text-emerald-800 mt-1">{{ $event->resolution_notes }}</p>
                            <p class="text-[11px] text-emerald-600 mt-1 font-mono" dir="ltr">{{ __('Resolved at:') }} {{ $event->resolved_at?->format('Y-m-d H:i') }}</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="space-y-6">
            @if(!$event->isResolved())
                <div class="bank-card">
                    <div class="bank-card-header">
                        <h3 class="text-sm font-semibold text-slate-900">{{ __('Resolve Security Incident') }}</h3>
                    </div>
                    <div class="bank-card-body">
                        <form action="{{ route('security.resolve', $event) }}" method="POST" class="space-y-3">
                            @csrf
                            <div>
                                <label class="block text-xs font-medium text-slate-700 mb-1">{{ __('Resolution Details / Notes') }} <span class="text-rose-500">*</span></label>
                                <textarea name="notes" required rows="3" class="w-full rounded-lg border-slate-300 text-xs shadow-xs focus:border-emerald-500 focus:ring-emerald-500" placeholder="{{ __('Document remediation steps taken...') }}"></textarea>
                            </div>
                            <button type="submit" class="w-full btn btn-primary text-xs">
                                {{ __('Mark as Resolved') }}
                            </button>
                        </form>
                    </div>
                </div>
            @endif

            @if($event->metadata)
                <div class="bank-card">
                    <div class="bank-card-header">
                        <h3 class="text-sm font-semibold text-slate-900">{{ __('Security Event Metadata') }}</h3>
                    </div>
                    <div class="bank-card-body">
                        <pre class="text-xs text-slate-600 bg-slate-50 p-3 rounded-lg overflow-auto font-mono" dir="ltr">{{ json_encode($event->metadata, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
