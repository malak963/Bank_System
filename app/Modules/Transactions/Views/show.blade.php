@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold text-gray-900">Transaction Details</h1>
        <a href="{{ route('transactions.index') }}" class="text-gray-600 hover:text-gray-900">Back to Transactions</a>
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
                        <h2 class="text-xl font-semibold text-gray-900">{{ $transaction->transaction_reference }}</h2>
                        <p class="text-sm text-gray-500">{{ $transaction->transaction_type->label() }}</p>
                    </div>
                    @switch($transaction->status->value)
                        @case('completed')
                            <span class="px-3 py-1 inline-flex text-sm leading-5 font-semibold rounded-full bg-emerald-100 text-emerald-800">Completed</span>
                            @break
                        @case('pending')
                            <span class="px-3 py-1 inline-flex text-sm leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">Pending</span>
                            @break
                        @case('failed')
                            <span class="px-3 py-1 inline-flex text-sm leading-5 font-semibold rounded-full bg-red-100 text-red-800">Failed</span>
                            @break
                        @case('reversed')
                            <span class="px-3 py-1 inline-flex text-sm leading-5 font-semibold rounded-full bg-purple-100 text-purple-800">Reversed</span>
                            @break
                        @default
                            <span class="px-3 py-1 inline-flex text-sm leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">{{ $transaction->status->label() }}</span>
                    @endswitch
                </div>

                <div class="grid grid-cols-2 gap-4 mb-6">
                    <div>
                        <p class="text-sm text-gray-500">Amount</p>
                        <p class="text-2xl font-bold {{ $transaction->isCredit() ? 'text-emerald-600' : 'text-red-600' }}">
                            {{ $transaction->isCredit() ? '+' : '-' }}{{ number_format($transaction->amount, 2) }} {{ $transaction->currency }}
                        </p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Date</p>
                        <p class="text-lg font-semibold text-gray-900">{{ $transaction->created_at->format('M d, Y H:i') }}</p>
                    </div>
                </div>

                <div class="border-t pt-4">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Transaction Details</h3>
                    <dl class="grid grid-cols-1 gap-4">
                        <div class="flex justify-between">
                            <dt class="text-sm text-gray-500">Account</dt>
                            <dd class="text-sm font-medium text-gray-900">{{ $transaction->account?->account_number ?? 'N/A' }}</dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-sm text-gray-500">Customer</dt>
                            <dd class="text-sm font-medium text-gray-900">{{ $transaction->customer?->full_name ?? 'N/A' }}</dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-sm text-gray-500">Branch</dt>
                            <dd class="text-sm font-medium text-gray-900">{{ $transaction->branch?->name ?? 'N/A' }}</dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-sm text-gray-500">Balance Before</dt>
                            <dd class="text-sm font-medium text-gray-900">{{ number_format($transaction->balance_before, 2) }} {{ $transaction->currency }}</dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-sm text-gray-500">Balance After</dt>
                            <dd class="text-sm font-medium text-gray-900">{{ number_format($transaction->balance_after, 2) }} {{ $transaction->currency }}</dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-sm text-gray-500">Reference Number</dt>
                            <dd class="text-sm font-medium text-gray-900">{{ $transaction->reference_number ?? 'N/A' }}</dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-sm text-gray-500">Fees</dt>
                            <dd class="text-sm font-medium text-gray-900">{{ number_format($transaction->fees, 2) }} {{ $transaction->currency }}</dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-sm text-gray-500">Tax</dt>
                            <dd class="text-sm font-medium text-gray-900">{{ number_format($transaction->tax, 2) }} {{ $transaction->currency }}</dd>
                        </div>
                    </dl>
                </div>

                @if($transaction->description)
                    <div class="border-t pt-4 mt-4">
                        <h3 class="text-lg font-semibold text-gray-900 mb-2">Description</h3>
                        <p class="text-gray-700">{{ $transaction->description }}</p>
                    </div>
                @endif

                @if($transaction->relatedTransaction)
                    <div class="border-t pt-4 mt-4">
                        <h3 class="text-lg font-semibold text-gray-900 mb-2">Related Transaction</h3>
                        <a href="{{ route('transactions.show', $transaction->relatedTransaction) }}" class="text-emerald-600 hover:text-emerald-900">
                            {{ $transaction->relatedTransaction->transaction_reference }}
                        </a>
                    </div>
                @endif
            </div>
        </div>

        <div>
            <div class="bg-white rounded-lg shadow p-6 mb-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Actions</h3>
                <div class="space-y-3">
                    @if($transaction->canBeReversed())
                        <form action="{{ route('transactions.reverse', $transaction) }}" method="POST">
                            @csrf
                            <div class="mb-3">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Reversal Reason</label>
                                <textarea name="reason" required rows="2" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500" placeholder="Enter reason for reversal..."></textarea>
                            </div>
                            <button type="submit" class="w-full bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700 text-sm">Reverse Transaction</button>
                        </form>
                    @endif

                    <a href="{{ route('transactions.edit', $transaction) }}" class="block w-full text-center bg-gray-100 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-200 text-sm">Edit Details</a>
                </div>
            </div>

            @if($transaction->reversals->count() > 0)
                <div class="bg-white rounded-lg shadow p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Reversals</h3>
                    <div class="space-y-3">
                        @foreach($transaction->reversals as $reversal)
                            <a href="{{ route('transactions.show', $reversal) }}" class="block text-sm text-emerald-600 hover:text-emerald-900">
                                {{ $reversal->transaction_reference }} - {{ $reversal->reversal_reason }}
                            </a>
                        @endforeach
                    </div>
                </div>
            @endif

            @if($transaction->metadata)
                <div class="bg-white rounded-lg shadow p-6 mt-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Metadata</h3>
                    <pre class="text-xs text-gray-600 bg-gray-50 p-3 rounded overflow-auto">{{ json_encode($transaction->metadata, JSON_PRETTY_PRINT) }}</pre>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
