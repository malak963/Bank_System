@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold text-gray-900">Create New Transfer</h1>
        <a href="{{ route('transfers.index') }}" class="text-gray-600 hover:text-gray-900">Back to Transfers</a>
    </div>

    @if(session('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
            {{ session('error') }}
        </div>
    @endif

    <div class="bg-white rounded-lg shadow p-6">
        <form action="{{ route('transfers.store') }}" method="POST">
            @csrf
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Transfer Type</label>
                    <select name="transfer_type" required class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500" onchange="toggleTransferFields(this.value)">
                        <option value="">Select Type</option>
                        @foreach($transferTypes as $type)
                            <option value="{{ $type->value }}">{{ $type->label() }}</option>
                        @endforeach
                    </select>
                    @error('transfer_type')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Customer</label>
                    <select name="customer_id" required class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
                        <option value="">Select Customer</option>
                        @foreach($customers as $customer)
                            <option value="{{ $customer->id }}">{{ $customer->full_name }} ({{ $customer->customer_number }})</option>
                        @endforeach
                    </select>
                    @error('customer_id')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">From Account</label>
                    <select name="from_account_id" required class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
                        <option value="">Select Source Account</option>
                        @foreach($accounts as $account)
                            <option value="{{ $account->id }}">{{ $account->account_number }} (Balance: ${{ number_format($account->balance, 2) }})</option>
                        @endforeach
                    </select>
                    @error('from_account_id')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div id="to-account-field">
                    <label class="block text-sm font-medium text-gray-700 mb-2">To Account (Internal)</label>
                    <select name="to_account_id" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
                        <option value="">Select Destination Account</option>
                        @foreach($accounts as $account)
                            <option value="{{ $account->id }}">{{ $account->account_number }}</option>
                        @endforeach
                    </select>
                    @error('to_account_id')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Amount ($)</label>
                    <input type="number" name="amount" step="0.01" min="0.01" max="1000000" required class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500" placeholder="0.00">
                    @error('amount')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div id="currency-field" class="hidden">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Currency</label>
                    <input type="text" name="currency" maxlength="3" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500" placeholder="USD">
                    @error('currency')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div id="exchange-rate-field" class="hidden">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Exchange Rate</label>
                    <input type="number" name="exchange_rate" step="0.000001" min="0" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500" placeholder="1.000000">
                    @error('exchange_rate')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div id="beneficiary-fields" class="hidden col-span-2">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Beneficiary Information</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Recipient Name</label>
                            <input type="text" name="recipient_name" maxlength="200" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500" placeholder="Full name">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Recipient Account</label>
                            <input type="text" name="recipient_account" maxlength="50" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500" placeholder="Account number">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Recipient Bank</label>
                            <input type="text" name="recipient_bank" maxlength="200" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500" placeholder="Bank name">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Routing Number</label>
                            <input type="text" name="routing_number" maxlength="9" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500" placeholder="9-digit routing number">
                        </div>
                        <div id="swift-field" class="hidden">
                            <label class="block text-sm font-medium text-gray-700 mb-2">SWIFT Code</label>
                            <input type="text" name="swift_code" maxlength="11" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500" placeholder="SWIFT/BIC code">
                        </div>
                        <div id="iban-field" class="hidden">
                            <label class="block text-sm font-medium text-gray-700 mb-2">IBAN</label>
                            <input type="text" name="iban" maxlength="34" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500" placeholder="International Bank Account Number">
                        </div>
                        <div class="col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Bank Address</label>
                            <textarea name="recipient_bank_address" rows="2" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500" placeholder="Bank address"></textarea>
                        </div>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Reference</label>
                    <input type="text" name="reference" maxlength="50" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500" placeholder="Payment reference">
                    @error('reference')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Schedule For</label>
                    <input type="datetime-local" name="scheduled_for" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
                    @error('scheduled_for')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                    <textarea name="description" rows="3" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500" placeholder="Transfer description..."></textarea>
                    @error('description')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="mt-6 flex justify-end gap-4">
                <a href="{{ route('transfers.index') }}" class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">Cancel</a>
                <button type="submit" class="px-4 py-2 bg-emerald-600 text-white rounded-lg hover:bg-emerald-700">Create Transfer</button>
            </div>
        </form>
    </div>
</div>

<script>
function toggleTransferFields(type) {
    const isInternal = type === 'internal';
    const isExternal = type === 'external';
    const isInternational = type === 'international';
    
    document.getElementById('to-account-field').classList.toggle('hidden', !isInternal);
    document.getElementById('beneficiary-fields').classList.toggle('hidden', isInternal);
    document.getElementById('currency-field').classList.toggle('hidden', !isInternational);
    document.getElementById('exchange-rate-field').classList.toggle('hidden', !isInternational);
    document.getElementById('swift-field').classList.toggle('hidden', !isInternational);
    document.getElementById('iban-field').classList.toggle('hidden', !isInternational);
}
</script>
@endsection
