@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold text-gray-900">Create New Card</h1>
        <a href="{{ route('cards.index') }}" class="text-gray-600 hover:text-gray-900">Back to Cards</a>
    </div>

    @if(session('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
            {{ session('error') }}
        </div>
    @endif

    <div class="bg-white rounded-lg shadow p-6">
        <form action="{{ route('cards.store') }}" method="POST">
            @csrf
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Account</label>
                    <select name="account_id" required class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
                        <option value="">Select Account</option>
                        @foreach($accounts as $account)
                            <option value="{{ $account->id }}">{{ $account->account_number }} - {{ $account->customer->full_name ?? 'Unknown' }} ({{ number_format($account->balance, 2) }})</option>
                        @endforeach
                    </select>
                    @error('account_id')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Card Type</label>
                    <select name="card_type" required class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
                        <option value="">Select Type</option>
                        <option value="debit">Debit Card</option>
                        <option value="credit">Credit Card</option>
                        <option value="prepaid">Prepaid Card</option>
                        <option value="virtual">Virtual Card</option>
                    </select>
                    @error('card_type')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Card Brand</label>
                    <select name="card_brand" required class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
                        <option value="">Select Brand</option>
                        <option value="visa">Visa</option>
                        <option value="mastercard">Mastercard</option>
                        <option value="american_express">American Express</option>
                        <option value="discover">Discover</option>
                        <option value="maestro">Maestro</option>
                        <option value="union_pay">UnionPay</option>
                    </select>
                    @error('card_brand')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Card Holder Name</label>
                    <input type="text" name="card_holder_name" required class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500" placeholder="Full name as it appears on ID">
                    @error('card_holder_name')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Daily Limit ($)</label>
                    <input type="number" name="daily_limit" step="0.01" min="0" max="100000" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500" placeholder="5000">
                    @error('daily_limit')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Monthly Limit ($)</label>
                    <input type="number" name="monthly_limit" step="0.01" min="0" max="1000000" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500" placeholder="20000">
                    @error('monthly_limit')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="col-span-2">
                    <h3 class="text-lg font-semibold text-gray-900 mb-3">Card Features</h3>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <label class="flex items-center">
                            <input type="checkbox" name="international_enabled" value="1" class="rounded border-gray-300 text-emerald-600 focus:ring-emerald-500">
                            <span class="ml-2 text-sm text-gray-700">International Transactions</span>
                        </label>
                        <label class="flex items-center">
                            <input type="checkbox" name="online_enabled" value="1" checked class="rounded border-gray-300 text-emerald-600 focus:ring-emerald-500">
                            <span class="ml-2 text-sm text-gray-700">Online Payments</span>
                        </label>
                        <label class="flex items-center">
                            <input type="checkbox" name="contactless_enabled" value="1" checked class="rounded border-gray-300 text-emerald-600 focus:ring-emerald-500">
                            <span class="ml-2 text-sm text-gray-700">Contactless Payments</span>
                        </label>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Delivery Method</label>
                    <select name="delivery_method" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
                        <option value="branch">Pick up at Branch</option>
                        <option value="mail">Regular Mail</option>
                        <option value="courier">Express Courier</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Priority</label>
                    <select name="priority" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
                        <option value="standard">Standard</option>
                        <option value="express">Express</option>
                        <option value="urgent">Urgent</option>
                    </select>
                </div>
            </div>

            <div class="mt-6 flex justify-end gap-4">
                <a href="{{ route('cards.index') }}" class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">Cancel</a>
                <button type="submit" class="px-4 py-2 bg-emerald-600 text-white rounded-lg hover:bg-emerald-700">Create Card</button>
            </div>
        </form>
    </div>
</div>
@endsection
