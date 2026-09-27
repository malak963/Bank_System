@extends('layouts.app')

@section('title', __('Create New Product'))

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('products.index') }}">{{ __('Banking Products') }}</a></li>
    <li class="breadcrumb-item active">{{ __('Create New Product') }}</li>
@endsection

@section('content')
<div class="mx-auto max-w-4xl space-y-6 px-4 py-8 sm:px-6 lg:px-8">
    <div class="bank-card">
        <div class="bank-card-header">
            <h2 class="text-base font-semibold text-slate-950">{{ __('Product Specifications') }}</h2>
            <p class="text-xs text-slate-500 mt-0.5">{{ __('Define product type, customer, linked account, and financial terms.') }}</p>
        </div>

        <form action="{{ route('products.store') }}" method="POST">
            @csrf
            
            <div class="bank-card-body space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-slate-700 mb-1.5">{{ __('Product Name') }} <span class="text-rose-500">*</span></label>
                        <input type="text" name="name" value="{{ old('name') }}" required class="w-full rounded-lg border-slate-300 text-sm shadow-xs focus:border-emerald-500 focus:ring-emerald-500" placeholder="{{ __('Enter product name') }}">
                        @error('name')
                            <p class="text-rose-600 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1.5">{{ __('Product Type') }} <span class="text-rose-500">*</span></label>
                        <select name="product_type" required class="w-full rounded-lg border-slate-300 text-sm shadow-xs focus:border-emerald-500 focus:ring-emerald-500">
                            <option value="">{{ __('Select Type') }}</option>
                            @foreach(($productTypes ?? []) as $type)
                                <option value="{{ $type->value }}" @selected(old('product_type') === $type->value)>{{ $type->label() }}</option>
                            @endforeach
                        </select>
                        @error('product_type')
                            <p class="text-rose-600 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1.5">{{ __('Customer') }} <span class="text-rose-500">*</span></label>
                        <select name="customer_id" required class="w-full rounded-lg border-slate-300 text-sm shadow-xs focus:border-emerald-500 focus:ring-emerald-500">
                            <option value="">{{ __('Select Customer') }}</option>
                            @foreach(($customers ?? []) as $customer)
                                <option value="{{ $customer->id }}" @selected(old('customer_id') == $customer->id)>{{ $customer->full_name }} ({{ $customer->customer_number }})</option>
                            @endforeach
                        </select>
                        @error('customer_id')
                            <p class="text-rose-600 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1.5">{{ __('Linked Account') }}</label>
                        <select name="account_id" class="w-full rounded-lg border-slate-300 text-sm shadow-xs focus:border-emerald-500 focus:ring-emerald-500">
                            <option value="">{{ __('Select Account (optional)') }}</option>
                            @foreach(($accounts ?? []) as $account)
                                <option value="{{ $account->id }}" @selected(old('account_id') == $account->id)>{{ $account->account_number }}</option>
                            @endforeach
                        </select>
                        @error('account_id')
                            <p class="text-rose-600 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1.5">{{ __('Interest Rate (%)') }} <span class="text-rose-500">*</span></label>
                        <input type="number" name="interest_rate" step="0.01" min="0" max="100" value="{{ old('interest_rate') }}" required class="w-full rounded-lg border-slate-300 text-sm shadow-xs focus:border-emerald-500 focus:ring-emerald-500" placeholder="5.25">
                        @error('interest_rate')
                            <p class="text-rose-600 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1.5">{{ __('Term (Months)') }}</label>
                        <input type="number" name="term_months" min="1" max="360" value="{{ old('term_months') }}" class="w-full rounded-lg border-slate-300 text-sm shadow-xs focus:border-emerald-500 focus:ring-emerald-500" placeholder="12">
                        @error('term_months')
                            <p class="text-rose-600 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1.5">{{ __('Initial Balance ($)') }}</label>
                        <input type="number" name="initial_balance" step="0.01" min="0" value="{{ old('initial_balance', '0.00') }}" class="w-full rounded-lg border-slate-300 text-sm shadow-xs focus:border-emerald-500 focus:ring-emerald-500" placeholder="1000.00">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1.5">{{ __('Minimum Balance ($)') }}</label>
                        <input type="number" name="minimum_balance" step="0.01" min="0" value="{{ old('minimum_balance', '100.00') }}" class="w-full rounded-lg border-slate-300 text-sm shadow-xs focus:border-emerald-500 focus:ring-emerald-500" placeholder="100.00">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1.5">{{ __('Interest Calculation Frequency') }}</label>
                        <select name="interest_calculation_frequency" class="w-full rounded-lg border-slate-300 text-sm shadow-xs focus:border-emerald-500 focus:ring-emerald-500">
                            <option value="daily">{{ __('Daily') }}</option>
                            <option value="monthly" selected>{{ __('Monthly') }}</option>
                            <option value="quarterly">{{ __('Quarterly') }}</option>
                            <option value="annually">{{ __('Annually') }}</option>
                        </select>
                    </div>

                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-slate-700 mb-1.5">{{ __('Terms and Conditions') }}</label>
                        <textarea name="terms_and_conditions" rows="3" class="w-full rounded-lg border-slate-300 text-sm shadow-xs focus:border-emerald-500 focus:ring-emerald-500" placeholder="{{ __('Product terms, fees, and rules...') }}">{{ old('terms_and_conditions') }}</textarea>
                    </div>
                </div>
            </div>

            <div class="bank-card-footer flex items-center justify-end gap-3">
                <a href="{{ route('products.index') }}" class="btn btn-secondary">
                    {{ __('Cancel') }}
                </a>
                <button type="submit" class="btn btn-primary">
                    {{ __('Create Product') }}
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
