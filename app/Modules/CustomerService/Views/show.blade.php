@extends('layouts.app')

@section('title', __('Ticket Details'))

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('customerService.index') }}">{{ __('Customer Service Tickets') }}</a></li>
    <li class="breadcrumb-item active font-mono" dir="ltr">{{ $ticket->ticket_number }}</li>
@endsection

@section('actions')
    <a href="{{ route('customerService.index') }}" class="btn btn-secondary">
        {{ __('Back to Tickets') }}
    </a>
@endsection

@section('content')
<div class="mx-auto max-w-7xl space-y-6 px-4 py-8 sm:px-6 lg:px-8">
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 space-y-6">
            <!-- Ticket Info Card -->
            <div class="bank-card">
                <div class="bank-card-header flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                    <div>
                        <span class="text-xs font-semibold uppercase tracking-wider text-slate-500">{{ $ticket->category->label() }}</span>
                        <h2 class="text-xl font-bold text-slate-900 mt-0.5">{{ $ticket->subject }}</h2>
                        <p class="text-xs text-slate-400 font-mono mt-0.5" dir="ltr">{{ $ticket->ticket_number }}</p>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-semibold ring-1 ring-inset {{ $ticket->priority->badgeColor() }}">
                            {{ $ticket->priority->label() }}
                        </span>
                        <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-semibold ring-1 ring-inset {{ $ticket->status->badgeColor() }}">
                            {{ $ticket->status->label() }}
                        </span>
                    </div>
                </div>

                <div class="bank-card-body">
                    <div class="prose max-w-none text-sm text-slate-800 bg-slate-50 p-4 rounded-lg border border-slate-100 mb-6">
                        {{ $ticket->description }}
                    </div>

                    <dl class="grid grid-cols-1 sm:grid-cols-2 gap-x-6 gap-y-3 text-xs border-t border-slate-100 pt-4">
                        <div class="flex justify-between py-1 border-b border-slate-50">
                            <dt class="text-slate-500">{{ __('Customer') }}</dt>
                            <dd class="font-medium text-slate-900">{{ $ticket->customer?->full_name ?? $ticket->customer_name ?? __('Guest') }}</dd>
                        </div>
                        <div class="flex justify-between py-1 border-b border-slate-50">
                            <dt class="text-slate-500">{{ __('Account') }}</dt>
                            <dd class="font-medium text-slate-900 font-mono" dir="ltr">{{ $ticket->account?->account_number ?? __('N/A') }}</dd>
                        </div>
                        <div class="flex justify-between py-1 border-b border-slate-50">
                            <dt class="text-slate-500">{{ __('Created At') }}</dt>
                            <dd class="font-medium text-slate-900 font-mono" dir="ltr">{{ $ticket->created_at->format('Y-m-d H:i') }}</dd>
                        </div>
                        <div class="flex justify-between py-1 border-b border-slate-50">
                            <dt class="text-slate-500">{{ __('Assigned To') }}</dt>
                            <dd class="font-medium text-slate-900">{{ $ticket->assignedTo?->name ?? __('Unassigned') }}</dd>
                        </div>
                    </dl>
                </div>
            </div>

            <!-- Responses Card -->
            <div class="bank-card">
                <div class="bank-card-header">
                    <h3 class="text-sm font-semibold text-slate-900">{{ __('Conversation History') }}</h3>
                </div>
                <div class="bank-card-body space-y-4">
                    @forelse($ticket->responses as $response)
                        <div class="p-4 rounded-lg {{ $response->is_internal ? 'bg-amber-50 border border-amber-200' : 'bg-slate-50 border border-slate-100' }}">
                            <div class="flex justify-between items-center mb-2">
                                <div class="flex items-center gap-2">
                                    <span class="font-semibold text-xs text-slate-900">{{ $response->user?->name ?? __('System') }}</span>
                                    @if($response->is_internal)
                                        <span class="inline-flex rounded bg-amber-200 text-amber-900 px-1.5 py-0.5 text-[10px] font-bold">{{ __('Internal Note') }}</span>
                                    @endif
                                </div>
                                <span class="text-[11px] text-slate-400 font-mono" dir="ltr">{{ $response->created_at->format('Y-m-d H:i') }}</span>
                            </div>
                            <p class="text-xs text-slate-700 leading-relaxed">{{ $response->message }}</p>
                        </div>
                    @empty
                        <p class="text-xs text-slate-400 text-center py-4">{{ __('No responses yet.') }}</p>
                    @endforelse

                    <!-- Add Response Form -->
                    <form action="{{ route('customerService.add-response', $ticket) }}" method="POST" class="pt-4 border-t border-slate-100 space-y-3">
                        @csrf
                        <div>
                            <label class="block text-xs font-medium text-slate-700 mb-1">{{ __('Add Reply / Note') }}</label>
                            <textarea name="message" rows="3" required class="w-full rounded-lg border-slate-300 text-xs shadow-xs focus:border-emerald-500 focus:ring-emerald-500" placeholder="{{ __('Type your response or internal note...') }}"></textarea>
                        </div>
                        <div class="flex items-center justify-between">
                            <label class="flex items-center gap-2 text-xs text-slate-600">
                                <input type="checkbox" name="is_internal" value="1" class="rounded border-slate-300 text-emerald-600 focus:ring-emerald-500">
                                <span>{{ __('Internal note (hidden from customer)') }}</span>
                            </label>
                            <button type="submit" class="btn btn-sm btn-primary">
                                {{ __('Send Reply') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="space-y-6">
            <!-- Ticket Status Control Card -->
            <div class="bank-card">
                <div class="bank-card-header">
                    <h3 class="text-sm font-semibold text-slate-900">{{ __('Update Status') }}</h3>
                </div>
                <div class="bank-card-body">
                    <form action="{{ route('customerService.update-status', $ticket) }}" method="POST" class="space-y-3">
                        @csrf
                        <div>
                            <label class="block text-xs font-medium text-slate-700 mb-1">{{ __('Change Status') }}</label>
                            <select name="status" class="w-full rounded-lg border-slate-300 text-xs shadow-xs focus:border-emerald-500 focus:ring-emerald-500">
                                @foreach($statuses as $status)
                                    <option value="{{ $status->value }}" @selected($ticket->status === $status)>{{ $status->label() }}</option>
                                @endforeach
                            </select>
                        </div>
                        <button type="submit" class="w-full btn btn-primary text-xs">
                            {{ __('Update Status') }}
                        </button>
                    </form>
                </div>
            </div>

            <!-- Assignment Card -->
            <div class="bank-card">
                <div class="bank-card-header">
                    <h3 class="text-sm font-semibold text-slate-900">{{ __('Assign Ticket') }}</h3>
                </div>
                <div class="bank-card-body">
                    <form action="{{ route('customerService.assign', $ticket) }}" method="POST" class="space-y-3">
                        @csrf
                        <div>
                            <label class="block text-xs font-medium text-slate-700 mb-1">{{ __('Assign To Staff') }}</label>
                            <select name="assigned_to" class="w-full rounded-lg border-slate-300 text-xs shadow-xs focus:border-emerald-500 focus:ring-emerald-500">
                                <option value="">{{ __('Unassigned') }}</option>
                                @foreach($agents as $agent)
                                    <option value="{{ $agent->id }}" @selected($ticket->assigned_to == $agent->id)>{{ $agent->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <button type="submit" class="w-full btn btn-secondary text-xs">
                            {{ __('Save Assignment') }}
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
