@extends('layouts.app')

@section('title', 'Bill Details')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-4xl mx-auto">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-3xl font-bold text-slate-800">Bill Details</h1>
            <a href="{{ route('bills-payments.index') }}" 
               class="text-slate-600 hover:text-slate-900">Back</a>
        </div>

        <div class="bg-white rounded-lg shadow p-6 mb-6">
            <div class="grid grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-slate-500 mb-1">Reference</label>
                    <p class="text-lg font-semibold text-slate-900">{{ $bill->bill_reference }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-500 mb-1">Status</label>
                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                        @if($bill->status->getColor() === 'emerald') bg-emerald-100 text-emerald-800
                        @elseif($bill->status->getColor() === 'red') bg-red-100 text-red-800
                        @elseif($bill->status->getColor() === 'yellow') bg-yellow-100 text-yellow-800
                        @elseif($bill->status->getColor() === 'blue') bg-blue-100 text-blue-800
                        @elseif($bill->status->getColor() === 'purple') bg-purple-100 text-purple-800
                        @elseif($bill->status->getColor() === 'gray') bg-gray-100 text-gray-800
                        @else bg-orange-100 text-orange-800 @endif">
                        {{ $bill->status->getLabel() }}
                    </span>
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-500 mb-1">Provider</label>
                    <p class="text-slate-900">{{ $bill->provider_name }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-500 mb-1">Bill Type</label>
                    <p class="text-slate-900">{{ $bill->bill_type->getLabel() }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-500 mb-1">Amount</label>
                    <p class="text-lg font-semibold text-slate-900">
                        {{ $bill->currency }} {{ number_format($bill->amount, 2) }}
                    </p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-500 mb-1">Due Date</label>
                    <p class="text-slate-900">{{ $bill->due_date?->format('Y-m-d') ?? 'N/A' }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-500 mb-1">Customer</label>
                    <p class="text-slate-900">{{ $bill->customer?->first_name }} {{ $bill->customer?->last_name ?? 'N/A' }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-500 mb-1">Account</label>
                    <p class="text-slate-900">{{ $bill->account?->account_number ?? 'N/A' }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-500 mb-1">Paid At</label>
                    <p class="text-slate-900">{{ $bill->paid_at?->format('Y-m-d H:i') ?? 'N/A' }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-500 mb-1">Transaction</label>
                    <p class="text-slate-900">{{ $bill->transaction?->transaction_reference ?? 'N/A' }}</p>
                </div>
            </div>

            @if($bill->description)
            <div class="mt-6">
                <label class="block text-sm font-medium text-slate-500 mb-1">Description</label>
                <p class="text-slate-900">{{ $bill->description }}</p>
            </div>
            @endif
        </div>

        <div class="flex justify-end space-x-4">
            @if($bill->status === \App\Modules\BillsPayments\Enums\BillStatus::PENDING)
            <form action="{{ route('bills-payments.pay', $bill->id) }}" method="POST" class="inline">
                @csrf
                <input type="hidden" name="account_id" value="{{ $bill->account_id ?? auth()->user()->customer->accounts->first()->id ?? '' }}">
                <button type="submit" class="px-4 py-2 bg-emerald-600 text-white rounded-lg hover:bg-emerald-700">
                    Pay Bill
                </button>
            </form>
            <form action="{{ route('bills-payments.schedule', $bill->id) }}" method="POST" class="inline">
                @csrf
                <input type="hidden" name="scheduled_date" value="{{ now()->addDays(7)->format('Y-m-d') }}">
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                    Schedule
                </button>
            </form>
            <form action="{{ route('bills-payments.cancel', $bill->id) }}" method="POST" class="inline">
                @csrf
                <button type="submit" class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700">
                    Cancel
                </button>
            </form>
            @elseif($bill->status === \App\Modules\BillsPayments\Enums\BillStatus::COMPLETED)
            <form action="{{ route('bills-payments.refund', $bill->id) }}" method="POST" class="inline">
                @csrf
                <input type="hidden" name="reason" value="Customer request">
                <button type="submit" class="px-4 py-2 bg-orange-600 text-white rounded-lg hover:bg-orange-700">
                    Refund
                </button>
            </form>
            @endif
            <a href="{{ route('bills-payments.edit', $bill->id) }}" 
               class="px-4 py-2 bg-slate-600 text-white rounded-lg hover:bg-slate-700">
                Edit
            </a>
        </div>
    </div>
</div>
@endsection
