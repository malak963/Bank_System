@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold text-gray-900">{{ __('Transfer Details') }}</h1>
        <a href="{{ route('transfers.index') }}" class="text-gray-600 hover:text-gray-900">{{ __('Back to Transfers') }}</a>
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
                        <h2 class="text-xl font-semibold text-gray-900">{{ $transfer->transfer_reference }}</h2>
                        <p class="text-sm text-gray-500">{{ $transfer->transfer_type->label() }}</p>
                    </div>
                    @switch($transfer->status->value)
                        @case('pending')
                            <span class="px-3 py-1 inline-flex text-sm leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">{{ __('Pending') }}</span>
                            @break
                        @case('processing')
                            <span class="px-3 py-1 inline-flex text-sm leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">{{ __('Processing') }}</span>
                            @break
                        @case('completed')
                            <span class="px-3 py-1 inline-flex text-sm leading-5 font-semibold rounded-full bg-emerald-100 text-emerald-800">{{ __('Completed') }}</span>
                            @break
                        @case('failed')
                            <span class="px-3 py-1 inline-flex text-sm leading-5 font-semibold rounded-full bg-red-100 text-red-800">{{ __('Failed') }}</span>
                            @break
                        @case('cancelled')
                            <span class="px-3 py-1 inline-flex text-sm leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">{{ __('Cancelled') }}</span>
                            @break
                        @case('on_hold')
                            <span class="px-3 py-1 inline-flex text-sm leading-5 font-semibold rounded-full bg-orange-100 text-orange-800">{{ __('On Hold') }}</span>
                            @break
                    @endswitch
                </div>

                <div class="border-t pt-4">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">{{ __('Transfer Information') }}</h3>
                    <dl class="grid grid-cols-1 gap-4">
                        <div class="flex justify-between">
                            <dt class="text-sm text-gray-500">Customer</dt>
                            <dd class="text-sm font-medium text-gray-900">{{ $transfer->customer?->full_name ?? 'N/A' }}</dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-sm text-gray-500">From Account</dt>
                            <dd class="text-sm font-medium text-gray-900">{{ $transfer->fromAccount?->account_number ?? 'N/A' }}</dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-sm text-gray-500">To Account</dt>
                            <dd class="text-sm font-medium text-gray-900">{{ $transfer->toAccount?->account_number ?? $transfer->recipient_account ?? 'N/A' }}</dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-sm text-gray-500">Amount</dt>
                            <dd class="text-sm font-medium text-gray-900">${{ number_format($transfer->amount, 2) }}</dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-sm text-gray-500">Currency</dt>
                            <dd class="text-sm font-medium text-gray-900">{{ $transfer->currency }}</dd>
                        </div>
                        @if($transfer->exchange_rate)
                            <div class="flex justify-between">
                                <dt class="text-sm text-gray-500">Exchange Rate</dt>
                                <dd class="text-sm font-medium text-gray-900">{{ number_format($transfer->exchange_rate, 6) }}</dd>
                            </div>
                        @endif
                        @if($transfer->converted_amount)
                            <div class="flex justify-between">
                                <dt class="text-sm text-gray-500">Converted Amount</dt>
                                <dd class="text-sm font-medium text-gray-900">${{ number_format($transfer->converted_amount, 2) }}</dd>
                            </div>
                        @endif
                        <div class="flex justify-between">
                            <dt class="text-sm text-gray-500">Fees</dt>
                            <dd class="text-sm font-medium text-gray-900">${{ number_format($transfer->fees, 2) }}</dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-sm text-gray-500">Total Deducted</dt>
                            <dd class="text-sm font-medium text-gray-900">${{ number_format($transfer->total_deducted, 2) }}</dd>
                        </div>
                        @if($transfer->recipient_name)
                            <div class="flex justify-between">
                                <dt class="text-sm text-gray-500">Recipient Name</dt>
                                <dd class="text-sm font-medium text-gray-900">{{ $transfer->recipient_name }}</dd>
                            </div>
                        @endif
                        @if($transfer->recipient_bank)
                            <div class="flex justify-between">
                                <dt class="text-sm text-gray-500">Recipient Bank</dt>
                                <dd class="text-sm font-medium text-gray-900">{{ $transfer->recipient_bank }}</dd>
                            </div>
                        @endif
                        @if($transfer->reference)
                            <div class="flex justify-between">
                                <dt class="text-sm text-gray-500">Reference</dt>
                                <dd class="text-sm font-medium text-gray-900">{{ $transfer->reference }}</dd>
                            </div>
                        @endif
                        <div class="flex justify-between">
                            <dt class="text-sm text-gray-500">Created</dt>
                            <dd class="text-sm font-medium text-gray-900">{{ $transfer->created_at->format('M d, Y H:i:s') }}</dd>
                        </div>
                        @if($transfer->scheduled_for)
                            <div class="flex justify-between">
                                <dt class="text-sm text-gray-500">Scheduled For</dt>
                                <dd class="text-sm font-medium text-gray-900">{{ $transfer->scheduled_for->format('M d, Y H:i:s') }}</dd>
                            </div>
                        @endif
                        @if($transfer->processed_at)
                            <div class="flex justify-between">
                                <dt class="text-sm text-gray-500">Processed</dt>
                                <dd class="text-sm font-medium text-gray-900">{{ $transfer->processed_at->format('M d, Y H:i:s') }}</dd>
                            </div>
                        @endif
                        @if($transfer->completed_at)
                            <div class="flex justify-between">
                                <dt class="text-sm text-gray-500">Completed</dt>
                                <dd class="text-sm font-medium text-gray-900">{{ $transfer->completed_at->format('M d, Y H:i:s') }}</dd>
                            </div>
                        @endif
                    </dl>
                </div>

                @if($transfer->description)
                    <div class="mt-4 bg-gray-50 border border-gray-200 rounded-lg p-4">
                        <h3 class="text-sm font-semibold text-gray-800 mb-2">{{ __('Description') }}</h3>
                        <p class="text-sm text-gray-700">{{ $transfer->description }}</p>
                    </div>
                @endif

                @if($transfer->failure_reason)
                    <div class="mt-4 bg-red-50 border border-red-200 rounded-lg p-4">
                        <h3 class="text-sm font-semibold text-red-800 mb-2">{{ __('Failure Reason') }}</h3>
                        <p class="text-sm text-red-700">{{ $transfer->failure_reason }}</p>
                    </div>
                @endif

                @if($transfer->cancellation_reason)
                    <div class="mt-4 bg-gray-50 border border-gray-200 rounded-lg p-4">
                        <h3 class="text-sm font-semibold text-gray-800 mb-2">Cancellation Reason</h3>
                        <p class="text-sm text-gray-700">{{ $transfer->cancellation_reason }}</p>
                    </div>
                @endif
            </div>

            <!-- Transactions Section -->
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">{{ __('Related Transactions') }}</h3>
                @if($transfer->transactions->count() > 0)
                    <div class="space-y-2">
                        @foreach($transfer->transactions as $transaction)
                            <div class="border rounded-lg p-3 bg-gray-50">
                                <div class="flex justify-between items-center">
                                    <span class="text-sm font-medium text-gray-900">{{ $transaction->transaction_reference }}</span>
                                    <span class="text-xs text-gray-500">{{ $transaction->created_at->format('M d, Y H:i') }}</span>
                                </div>
                                <p class="text-sm text-gray-600 mt-1">{{ $transaction->transaction_type->label() }} - ${{ number_format($transaction->amount, 2) }}</p>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-sm text-gray-500">{{ __('No transactions yet') }}</p>
                @endif
            </div>
        </div>

        <div>
            <div class="bg-white rounded-lg shadow p-6 mb-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">{{ __('Actions') }}</h3>
                <div class="space-y-3">
                    @if($transfer->status->canBeCancelled())
                        <form action="{{ route('transfers.cancel', $transfer) }}" method="POST">
                            @csrf
                            <div class="mb-3">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Cancellation Reason</label>
                                <textarea name="reason" required rows="2" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500" placeholder="{{ __('Enter reason...') }}"></textarea>
                            </div>
                            <button type="submit" class="w-full bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700 text-sm">Cancel Transfer</button>
                        </form>
                    @endif

                    @if($transfer->status->canBeRetried())
                        <form action="{{ route('transfers.retry', $transfer) }}" method="POST">
                            @csrf
                            <button type="submit" class="w-full bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 text-sm">{{ __('Retry Transfer') }}</button>
                        </form>
                    @endif

                    @if($transfer->isScheduled())
                        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-3">
                            <p class="text-sm text-yellow-800">
                                <strong>Scheduled:</strong> This transfer is scheduled for {{ $transfer->scheduled_for->format('M d, Y H:i') }}
                            </p>
                        </div>
                    @endif
                </div>
            </div>

            @if($transfer->metadata)
                <div class="bg-white rounded-lg shadow p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Metadata</h3>
                    <pre class="text-xs text-gray-600 bg-gray-50 p-3 rounded overflow-auto">{{ json_encode($transfer->metadata, JSON_PRETTY_PRINT) }}</pre>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
