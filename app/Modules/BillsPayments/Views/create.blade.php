@extends('layouts.app')

@section('title', __('Create Bill'))

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('bills-payments.index') }}">{{ __('Bills & Payments') }}</a></li>
    <li class="breadcrumb-item active">{{ __('Create Bill') }}</li>
@endsection

@section('content')
<div class="mx-auto max-w-4xl space-y-6 px-4 py-8 sm:px-6 lg:px-8">
    <div class="bank-card">
        <div class="bank-card-header">
            <h2 class="text-base font-semibold text-slate-950">{{ __('Bill Details') }}</h2>
            <p class="text-xs text-slate-500 mt-0.5">{{ __('Enter provider, account, amount, and due date information.') }}</p>
        </div>

        <form action="{{ route('bills-payments.store') }}" method="POST">
            @csrf
            
            <div class="bank-card-body space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1.5">{{ __('Customer') }} <span class="text-rose-500">*</span></label>
                        <select name="customer_id" required class="w-full rounded-lg border-slate-300 text-sm shadow-xs focus:border-emerald-500 focus:ring-emerald-500">
                            <option value="">{{ __('Select Customer') }}</option>
                            @foreach(\App\Modules\Customers\Models\Customer::all() as $customer)
                                <option value="{{ $customer->id }}" @selected(old('customer_id') == $customer->id)>{{ $customer->first_name }} {{ $customer->last_name }}</option>
                            @endforeach
                        </select>
                        @error('customer_id')
                            <p class="text-rose-600 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1.5">{{ __('Account (Optional)') }}</label>
                        <select name="account_id" class="w-full rounded-lg border-slate-300 text-sm shadow-xs focus:border-emerald-500 focus:ring-emerald-500">
                            <option value="">{{ __('Select Account') }}</option>
                            @foreach(\App\Modules\Accounts\Models\Account::all() as $account)
                                <option value="{{ $account->id }}" @selected(old('account_id') == $account->id)>{{ $account->account_number }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1.5">{{ __('Bill Type') }} <span class="text-rose-500">*</span></label>
                        <select name="bill_type" required class="w-full rounded-lg border-slate-300 text-sm shadow-xs focus:border-emerald-500 focus:ring-emerald-500">
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
                        @error('bill_type')
                            <p class="text-rose-600 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1.5">{{ __('Provider Name') }} <span class="text-rose-500">*</span></label>
                        <input type="text" name="provider_name" value="{{ old('provider_name') }}" required class="w-full rounded-lg border-slate-300 text-sm shadow-xs focus:border-emerald-500 focus:ring-emerald-500" placeholder="{{ __('e.g., Telecom, Electricity Corp') }}">
                        @error('provider_name')
                            <p class="text-rose-600 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1.5">{{ __('Bill Number / Account #') }}</label>
                        <input type="text" name="bill_number" value="{{ old('bill_number') }}" class="w-full rounded-lg border-slate-300 text-sm shadow-xs focus:border-emerald-500 focus:ring-emerald-500" placeholder="{{ __('Provider reference number') }}">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1.5">{{ __('Amount') }} <span class="text-rose-500">*</span></label>
                        <input type="number" step="0.01" min="0.01" name="amount" value="{{ old('amount') }}" required class="w-full rounded-lg border-slate-300 text-sm shadow-xs focus:border-emerald-500 focus:ring-emerald-500" placeholder="0.00">
                        @error('amount')
                            <p class="text-rose-600 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1.5">{{ __('Currency') }}</label>
                        <select name="currency" class="w-full rounded-lg border-slate-300 text-sm shadow-xs focus:border-emerald-500 focus:ring-emerald-500">
                            <option value="USD">USD</option>
                            <option value="EUR">EUR</option>
                            <option value="GBP">GBP</option>
                            <option value="SAR">SAR</option>
                            <option value="AED">AED</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1.5">{{ __('Due Date') }} <span class="text-rose-500">*</span></label>
                        <input type="date" name="due_date" value="{{ old('due_date') }}" required class="w-full rounded-lg border-slate-300 text-sm shadow-xs focus:border-emerald-500 focus:ring-emerald-500">
                        @error('due_date')
                            <p class="text-rose-600 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-slate-700 mb-1.5">{{ __('Notes') }}</label>
                        <textarea name="notes" rows="2" class="w-full rounded-lg border-slate-300 text-sm shadow-xs focus:border-emerald-500 focus:ring-emerald-500" placeholder="{{ __('Optional payment notes...') }}">{{ old('notes') }}</textarea>
                    </div>
                </div>
            </div>

            <div class="bank-card-footer flex items-center justify-end gap-3">
                <a href="{{ route('bills-payments.index') }}" class="btn btn-secondary">
                    {{ __('Cancel') }}
                </a>
                <button type="submit" class="btn btn-primary">
                    {{ __('Create Bill') }}
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
