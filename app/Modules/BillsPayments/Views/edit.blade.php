@extends('layouts.app')

@section('title', 'Edit Bill')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-2xl mx-auto">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-3xl font-bold text-slate-800">Edit Bill</h1>
            <a href="{{ route('bills-payments.show', $bill->id) }}" 
               class="text-slate-600 hover:text-slate-900">Back</a>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <form action="{{ route('bills-payments.update', $bill->id) }}" method="POST">
                @csrf
                @method('PUT')
                
                <div class="grid grid-cols-1 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-2">Customer</label>
                        <select name="customer_id" required class="w-full border-slate-300 rounded-lg shadow-sm focus:ring-emerald-500 focus:border-emerald-500">
                            <option value="">Select Customer</option>
                            @foreach(\App\Modules\Customers\Models\Customer::all() as $customer)
                            <option value="{{ $customer->id }}" {{ $bill->customer_id == $customer->id ? 'selected' : '' }}>
                                {{ $customer->first_name }} {{ $customer->last_name }}
                            </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-2">Account (Optional)</label>
                        <select name="account_id" class="w-full border-slate-300 rounded-lg shadow-sm focus:ring-emerald-500 focus:border-emerald-500">
                            <option value="">Select Account</option>
                            @foreach(\App\Modules\Accounts\Models\Account::all() as $account)
                            <option value="{{ $account->id }}" {{ $bill->account_id == $account->id ? 'selected' : '' }}>
                                {{ $account->account_number }}
                            </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-2">Bill Type</label>
                        <select name="bill_type" required class="w-full border-slate-300 rounded-lg shadow-sm focus:ring-emerald-500 focus:border-emerald-500">
                            <option value="">Select Type</option>
                            <option value="electricity" {{ $bill->bill_type->value === 'electricity' ? 'selected' : '' }}>Electricity</option>
                            <option value="water" {{ $bill->bill_type->value === 'water' ? 'selected' : '' }}>Water</option>
                            <option value="gas" {{ $bill->bill_type->value === 'gas' ? 'selected' : '' }}>Gas</option>
                            <option value="internet" {{ $bill->bill_type->value === 'internet' ? 'selected' : '' }}>Internet</option>
                            <option value="phone" {{ $bill->bill_type->value === 'phone' ? 'selected' : '' }}>Phone</option>
                            <option value="television" {{ $bill->bill_type->value === 'television' ? 'selected' : '' }}>Television</option>
                            <option value="insurance" {{ $bill->bill_type->value === 'insurance' ? 'selected' : '' }}>Insurance</option>
                            <option value="tax" {{ $bill->bill_type->value === 'tax' ? 'selected' : '' }}>Tax</option>
                            <option value="loan_installment" {{ $bill->bill_type->value === 'loan_installment' ? 'selected' : '' }}>Loan Installment</option>
                            <option value="subscription" {{ $bill->bill_type->value === 'subscription' ? 'selected' : '' }}>Subscription</option>
                            <option value="other" {{ $bill->bill_type->value === 'other' ? 'selected' : '' }}>Other</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-2">Provider Name</label>
                        <input type="text" name="provider_name" value="{{ $bill->provider_name }}" required 
                               class="w-full border-slate-300 rounded-lg shadow-sm focus:ring-emerald-500 focus:border-emerald-500">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-2">Provider Account Number (Optional)</label>
                        <input type="text" name="provider_account_number" value="{{ $bill->provider_account_number }}" 
                               class="w-full border-slate-300 rounded-lg shadow-sm focus:ring-emerald-500 focus:border-emerald-500">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-2">Amount</label>
                        <input type="number" name="amount" step="0.01" value="{{ $bill->amount }}" required 
                               class="w-full border-slate-300 rounded-lg shadow-sm focus:ring-emerald-500 focus:border-emerald-500">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-2">Currency</label>
                        <input type="text" name="currency" value="{{ $bill->currency }}" required 
                               class="w-full border-slate-300 rounded-lg shadow-sm focus:ring-emerald-500 focus:border-emerald-500">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-2">Due Date</label>
                        <input type="date" name="due_date" value="{{ $bill->due_date?->format('Y-m-d') }}" required 
                               class="w-full border-slate-300 rounded-lg shadow-sm focus:ring-emerald-500 focus:border-emerald-500">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-2">Description</label>
                        <textarea name="description" rows="3" 
                                  class="w-full border-slate-300 rounded-lg shadow-sm focus:ring-emerald-500 focus:border-emerald-500">{{ $bill->description }}</textarea>
                    </div>

                    <div class="flex justify-end space-x-4">
                        <a href="{{ route('bills-payments.show', $bill->id) }}" 
                           class="px-4 py-2 border border-slate-300 rounded-lg text-slate-700 hover:bg-slate-50">
                            Cancel
                        </a>
                        <button type="submit" 
                                class="px-4 py-2 bg-emerald-600 text-white rounded-lg hover:bg-emerald-700">
                            Update Bill
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
