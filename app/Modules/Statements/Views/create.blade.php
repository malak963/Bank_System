@extends('layouts.app')

@section('title', __('Generate Statement'))

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('statements.index') }}">{{ __('Account Statements') }}</a></li>
    <li class="breadcrumb-item active">{{ __('Generate Statement') }}</li>
@endsection

@section('content')
<div class="mx-auto max-w-4xl space-y-6 px-4 py-8 sm:px-6 lg:px-8">
    <div class="bank-card">
        <div class="bank-card-header">
            <h2 class="text-base font-semibold text-slate-950">{{ __('Statement Period & Parameters') }}</h2>
            <p class="text-xs text-slate-500 mt-0.5">{{ __('Select target account, statement period, and opening balance.') }}</p>
        </div>

        <form action="{{ route('statements.store') }}" method="POST">
            @csrf
            
            <div class="bank-card-body space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1.5">{{ __('Account') }} <span class="text-rose-500">*</span></label>
                        <select name="account_id" required class="w-full rounded-lg border-slate-300 text-sm shadow-xs focus:border-emerald-500 focus:ring-emerald-500">
                            <option value="">{{ __('Select Account') }}</option>
                            @foreach(\App\Modules\Accounts\Models\Account::all() as $account)
                                <option value="{{ $account->id }}" @selected(old('account_id') == $account->id)>
                                    {{ $account->account_number }} - {{ $account->customer->full_name ?? '' }}
                                </option>
                            @endforeach
                        </select>
                        @error('account_id')
                            <p class="text-rose-600 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1.5">{{ __('Customer') }} <span class="text-rose-500">*</span></label>
                        <select name="customer_id" required class="w-full rounded-lg border-slate-300 text-sm shadow-xs focus:border-emerald-500 focus:ring-emerald-500">
                            <option value="">{{ __('Select Customer') }}</option>
                            @foreach(\App\Modules\Customers\Models\Customer::all() as $customer)
                                <option value="{{ $customer->id }}" @selected(old('customer_id') == $customer->id)>
                                    {{ $customer->first_name }} {{ $customer->last_name }}
                                </option>
                            @endforeach
                        </select>
                        @error('customer_id')
                            <p class="text-rose-600 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1.5">{{ __('Period Start') }} <span class="text-rose-500">*</span></label>
                        <input type="date" name="period_start" value="{{ old('period_start', now()->startOfMonth()->format('Y-m-d')) }}" required class="w-full rounded-lg border-slate-300 text-sm shadow-xs focus:border-emerald-500 focus:ring-emerald-500">
                        @error('period_start')
                            <p class="text-rose-600 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1.5">{{ __('Period End') }} <span class="text-rose-500">*</span></label>
                        <input type="date" name="period_end" value="{{ old('period_end', now()->format('Y-m-d')) }}" required class="w-full rounded-lg border-slate-300 text-sm shadow-xs focus:border-emerald-500 focus:ring-emerald-500">
                        @error('period_end')
                            <p class="text-rose-600 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1.5">{{ __('Opening Balance') }} <span class="text-rose-500">*</span></label>
                        <input type="number" name="opening_balance" step="0.01" value="{{ old('opening_balance', '0.00') }}" required class="w-full rounded-lg border-slate-300 text-sm shadow-xs focus:border-emerald-500 focus:ring-emerald-500">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1.5">{{ __('Currency') }} <span class="text-rose-500">*</span></label>
                        <select name="currency" class="w-full rounded-lg border-slate-300 text-sm shadow-xs focus:border-emerald-500 focus:ring-emerald-500">
                            <option value="USD">USD</option>
                            <option value="EUR">EUR</option>
                            <option value="GBP">GBP</option>
                            <option value="SAR">SAR</option>
                            <option value="AED">AED</option>
                            <option value="SYP">SYP</option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="bank-card-footer flex items-center justify-end gap-3">
                <a href="{{ route('statements.index') }}" class="btn btn-secondary">
                    {{ __('Cancel') }}
                </a>
                <button type="submit" class="btn btn-primary">
                    {{ __('Generate Statement') }}
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
