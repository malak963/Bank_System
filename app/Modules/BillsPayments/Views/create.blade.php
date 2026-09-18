@extends('layouts.app')

@section('title', 'Create Bill')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-2xl mx-auto">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-3xl font-bold text-slate-800">{{ __('Create Bill') }}</h1>
            <a href="{{ route('bills-payments.index') }}" 
               class="text-slate-600 hover:text-slate-900">Back</a>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <form action="{{ route('bills-payments.store') }}" method="POST">
                @csrf
                
                <div class="grid grid-cols-1 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-2">{{ __('Customer') }}</label>
                        <select name="customer_id" required class="w-full border-slate-300 rounded-lg shadow-sm focus:ring-emerald-500 focus:border-emerald-500">
                            <option value="">Select Customer</option>
                            @foreach(\App\Modules\Customers\Models\Customer::all() as $customer)
                            <option value="{{ $customer->id }}">{{ $customer->first_name }} {{ $customer->last_name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-2">Account (Optional)</label>
                        <select name="account_id" class="w-full border-slate-300 rounded-lg shadow-sm focus:ring-emerald-500 focus:border-emerald-500">
                            <option value="">Select Account</option>
                            @foreach(\App\Modules\Accounts\Models\Account::all() as $account)
                            <option value="{{ $account->id }}">{{ $account->account_number }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-2">{{ __('Bill Type') }}</label>
                        <select name="bill_type" required class="w-full border-slate-300 rounded-lg shadow-sm focus:ring-emerald-500 focus:border-emerald-500">
                            <option value="">{{ __('Select Type') }}</option>
                            <option value="electricity">{{ __('Electricity') }}</option>
                            <option value="water">{{ __('Water') }}</option>
                            <option value="gas">{{ __('Gas') }}</option>
                            <option value="internet">{{ __('Internet') }}</option>
                            <option value="phone">{{ __('Phone') }}</option>
                            <option value="television">{{ __('Television') }}</option>
                            <option value="insurance">{{ __('Insurance') }}</option>
                            <option value="tax">{{ __('Tax') }}</option>
                            <option value="loan_installment">{{ __('Loan Installment') }}</option>
                            <option value="subscription">{{ __('Subscription') }}</option>
                            <option value="other">{{ __('Other') }}</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-2">{{ __('Provider Name') }}</label>
                        <input type="text" name="provider_name" required 
                               class="w-full border-slate-300 rounded-lg shadow-sm focus:ring-emerald-500 focus:border-emerald-500">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-2">{{ __('Provider Account Number (Optional)') }}</label>
                        <input type="text" name="provider_account_number" 
                               class="w-full border-slate-300 rounded-lg shadow-sm focus:ring-emerald-500 focus:border-emerald-500">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-2">{{ __('Amount') }}</label>
                        <input type="number" name="amount" step="0.01" required 
                               class="w-full border-slate-300 rounded-lg shadow-sm focus:ring-emerald-500 focus:border-emerald-500">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-2">{{ __('Currency') }}</label>
                        <input type="text" name="currency" value="SYP" required 
                               class="w-full border-slate-300 rounded-lg shadow-sm focus:ring-emerald-500 focus:border-emerald-500">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-2">{{ __('Due Date') }}</label>
                        <input type="date" name="due_date" required 
                               class="w-full border-slate-300 rounded-lg shadow-sm focus:ring-emerald-500 focus:border-emerald-500">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-2">{{ __('Description') }}</label>
                        <textarea name="description" rows="3" 
                                  class="w-full border-slate-300 rounded-lg shadow-sm focus:ring-emerald-500 focus:border-emerald-500"></textarea>
                    </div>

                    <div class="flex justify-end space-x-4">
                        <a href="{{ route('bills-payments.index') }}" 
                           class="px-4 py-2 border border-slate-300 rounded-lg text-slate-700 hover:bg-slate-50">
                            Cancel
                        </a>
                        <button type="submit" 
                                class="px-4 py-2 bg-emerald-600 text-white rounded-lg hover:bg-emerald-700">
                            Create Bill
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
