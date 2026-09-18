@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold text-gray-900">{{ __('Ticket Details') }}</h1>
        <a href="{{ route('customerService.index') }}" class="text-gray-600 hover:text-gray-900">{{ __('Back to Tickets') }}</a>
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
            <div class="bg-white rounded-lg shadow p-6 mb-6">
                <div class="flex justify-between items-start mb-6">
                    <div>
                        <h2 class="text-xl font-semibold text-gray-900">{{ $ticket->ticket_number }}</h2>
                        <p class="text-sm text-gray-500">{{ $ticket->subject }}</p>
                    </div>
                    @switch($ticket->status->value)
                        @case('open')
                            <span class="px-3 py-1 inline-flex text-sm leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">{{ __('Open') }}</span>
                            @break
                        @case('in_progress')
                            <span class="px-3 py-1 inline-flex text-sm leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">In Progress</span>
                            @break
                        @case('pending_customer')
                            <span class="px-3 py-1 inline-flex text-sm leading-5 font-semibold rounded-full bg-purple-100 text-purple-800">{{ __('Pending Customer') }}</span>
                            @break
                        @case('resolved')
                            <span class="px-3 py-1 inline-flex text-sm leading-5 font-semibold rounded-full bg-emerald-100 text-emerald-800">{{ __('Resolved') }}</span>
                            @break
                        @case('closed')
                            <span class="px-3 py-1 inline-flex text-sm leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">{{ __('Closed') }}</span>
                            @break
                        @case('escalated')
                            <span class="px-3 py-1 inline-flex text-sm leading-5 font-semibold rounded-full bg-red-100 text-red-800">{{ __('Escalated') }}</span>
                            @break
                    @endswitch
                </div>

                <div class="border-t pt-4">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">{{ __('Ticket Information') }}</h3>
                    <dl class="grid grid-cols-1 gap-4">
                        <div class="flex justify-between">
                            <dt class="text-sm text-gray-500">Category</dt>
                            <dd class="text-sm font-medium text-gray-900">{{ $ticket->category->label() }}</dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-sm text-gray-500">Priority</dt>
                            <dd class="text-sm font-medium text-gray-900">{{ $ticket->priority->label() }}</dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-sm text-gray-500">Customer</dt>
                            <dd class="text-sm font-medium text-gray-900">{{ $ticket->customer?->full_name ?? 'N/A' }}</dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-sm text-gray-500">Branch</dt>
                            <dd class="text-sm font-medium text-gray-900">{{ $ticket->branch?->name ?? 'N/A' }}</dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-sm text-gray-500">Assigned To</dt>
                            <dd class="text-sm font-medium text-gray-900">{{ $ticket->assignedTo?->name ?? 'Unassigned' }}</dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-sm text-gray-500">Created</dt>
                            <dd class="text-sm font-medium text-gray-900">{{ $ticket->created_at->format('M d, Y H:i:s') }}</dd>
                        </div>
                        @if($ticket->first_response_at)
                            <div class="flex justify-between">
                                <dt class="text-sm text-gray-500">First Response</dt>
                                <dd class="text-sm font-medium text-gray-900">{{ $ticket->first_response_at->format('M d, Y H:i:s') }}</dd>
                            </div>
                        @endif
                        @if($ticket->resolved_at)
                            <div class="flex justify-between">
                                <dt class="text-sm text-gray-500">Resolved</dt>
                                <dd class="text-sm font-medium text-gray-900">{{ $ticket->resolved_at->format('M d, Y H:i:s') }}</dd>
                            </div>
                        @endif
                        @if($ticket->customer_satisfaction)
                            <div class="flex justify-between">
                                <dt class="text-sm text-gray-500">Customer Satisfaction</dt>
                                <dd class="text-sm font-medium text-gray-900">{{ $ticket->customer_satisfaction }}/5</dd>
                            </div>
                        @endif
                    </dl>
                </div>

                @if($ticket->description)
                    <div class="mt-4 bg-gray-50 border border-gray-200 rounded-lg p-4">
                        <h3 class="text-sm font-semibold text-gray-800 mb-2">{{ __('Description') }}</h3>
                        <p class="text-sm text-gray-700">{{ $ticket->description }}</p>
                    </div>
                @endif

                @if($ticket->resolution)
                    <div class="mt-4 bg-emerald-50 border border-emerald-200 rounded-lg p-4">
                        <h3 class="text-sm font-semibold text-emerald-800 mb-2">{{ __('Resolution') }}</h3>
                        <p class="text-sm text-emerald-700">{{ $ticket->resolution }}</p>
                        <p class="text-xs text-emerald-600 mt-1">Resolved by: {{ $ticket->resolvedBy?->name ?? 'N/A' }}</p>
                    </div>
                @endif
            </div>

            <!-- Responses Section -->
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Responses ({{ $ticket->responses->count() }})</h3>
                @if($ticket->responses->count() > 0)
                    <div class="space-y-4">
                        @foreach($ticket->responses as $response)
                            <div class="border rounded-lg p-4 {{ $response->is_internal ? 'bg-yellow-50 border-yellow-200' : 'bg-gray-50 border-gray-200' }}">
                                <div class="flex justify-between items-start mb-2">
                                    <div>
                                        <span class="font-medium text-gray-900">
                                            {{ $response->user?->name ?? $response->customer?->full_name ?? 'System' }}
                                        </span>
                                        @if($response->is_internal)
                                            <span class="ml-2 px-2 py-1 text-xs bg-yellow-200 text-yellow-800 rounded">Internal</span>
                                        @endif
                                    </div>
                                    <span class="text-xs text-gray-500">{{ $response->created_at->format('M d, Y H:i') }}</span>
                                </div>
                                <p class="text-sm text-gray-700">{{ $response->message }}</p>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-sm text-gray-500">{{ __('No responses yet') }}</p>
                @endif
            </div>
        </div>

        <div>
            <div class="bg-white rounded-lg shadow p-6 mb-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">{{ __('Actions') }}</h3>
                <div class="space-y-3">
                    @if($ticket->isActive())
                        <form action="{{ route('customerService.assign', $ticket) }}" method="POST">
                            @csrf
                            <div class="mb-3">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Assign To</label>
                                <select name="assigned_to" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
                                    <option value="">{{ __('Select User') }}</option>
                                    @foreach(\App\Models\User::all() as $user)
                                        <option value="{{ $user->id }}" {{ $ticket->assigned_to === $user->id ? 'selected' : '' }}>{{ $user->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <button type="submit" class="w-full bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 text-sm">Assign Ticket</button>
                        </form>
                    @endif

                    @if($ticket->isActive() && !$ticket->resolution)
                        <form action="{{ route('customerService.resolve', $ticket) }}" method="POST">
                            @csrf
                            <div class="mb-3">
                                <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('Resolution') }}</label>
                                <textarea name="resolution" required rows="2" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500" placeholder="{{ __('Enter resolution...') }}"></textarea>
                            </div>
                            <button type="submit" class="w-full bg-emerald-600 text-white px-4 py-2 rounded-lg hover:bg-emerald-700 text-sm">{{ __('Resolve Ticket') }}</button>
                        </form>
                    @endif

                    @if($ticket->status === \App\Modules\CustomerService\Enums\TicketStatus::Resolved)
                        <form action="{{ route('customerService.close', $ticket) }}" method="POST">
                            @csrf
                            <button type="submit" class="w-full bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-700 text-sm">Close Ticket</button>
                        </form>
                    @endif

                    @if($ticket->status === \App\Modules\CustomerService\Enums\TicketStatus::Closed)
                        <form action="{{ route('customerService.reopen', $ticket) }}" method="POST">
                            @csrf
                            <button type="submit" class="w-full bg-yellow-600 text-white px-4 py-2 rounded-lg hover:bg-yellow-700 text-sm">{{ __('Reopen Ticket') }}</button>
                        </form>
                    @endif
                </div>
            </div>

            <div class="bg-white rounded-lg shadow p-6 mb-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Add Response</h3>
                <form action="{{ route('customerService.add-response', $ticket) }}" method="POST">
                    @csrf
                    <div class="space-y-3">
                        <div>
                            <label class="flex items-center">
                                <input type="checkbox" name="is_internal" value="1" class="rounded border-gray-300 text-emerald-600 focus:ring-emerald-500">
                                <span class="ml-2 text-sm text-gray-700">{{ __('Internal Note') }}</span>
                            </label>
                        </div>
                        <div>
                            <textarea name="message" required rows="3" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500" placeholder="{{ __('Enter your response...') }}"></textarea>
                        </div>
                        <button type="submit" class="w-full bg-emerald-600 text-white px-4 py-2 rounded-lg hover:bg-emerald-700 text-sm">Add Response</button>
                    </div>
                </form>
            </div>

            @if($ticket->tags)
                <div class="bg-white rounded-lg shadow p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">{{ __('Tags') }}</h3>
                    <div class="flex flex-wrap gap-2">
                        @foreach($ticket->tags as $tag)
                            <span class="px-2 py-1 bg-gray-100 text-gray-700 rounded text-sm">{{ $tag }}</span>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
