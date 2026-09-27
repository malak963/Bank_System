@extends('layouts.app')

@section('title', __('Create Transaction'))

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('transactions.index') }}">{{ __('Transactions') }}</a></li>
    <li class="breadcrumb-item active">{{ __('Create Transaction') }}</li>
@endsection

@section('content')
<div class="mx-auto max-w-4xl space-y-6 px-4 py-8 sm:px-6 lg:px-8">
    <div class="bank-card">
        <div class="bank-card-header">
            <h2 class="text-base font-semibold text-slate-950">{{ __('Transaction Information') }}</h2>
            <p class="text-xs text-slate-500 mt-0.5">{{ __('Enter account details, transaction type, and amounts.') }}</p>
        </div>

        <form action="{{ route('transactions.store') }}" method="POST">
            @csrf
            
            <div class="bank-card-body space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-slate-700 mb-1.5">{{ __('Account') }} <span class="text-rose-500">*</span></label>
                        <select name="account_id" required class="w-full rounded-lg border-slate-300 text-sm shadow-xs focus:border-emerald-500 focus:ring-emerald-500">
                            <option value="">{{ __('Select Account') }}</option>
                            @foreach($accounts as $account)
                                <option value="{{ $account->id }}" @selected(old('account_id') == $account->id)>
                                    {{ $account->account_number }} - {{ $account->customer->full_name ?? __('Unknown') }} ({{ number_format($account->balance, 2) }} {{ $account->currency }})
                                </option>
                            @endforeach
                        </select>
                        @error('account_id')
                            <p class="text-rose-600 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1.5">{{ __('Transaction Type') }} <span class="text-rose-500">*</span></label>
                        <select name="transaction_type" required class="w-full rounded-lg border-slate-300 text-sm shadow-xs focus:border-emerald-500 focus:ring-emerald-500">
                            <option value="">{{ __('Select Type') }}</option>
                            <option value="deposit" @selected(old('transaction_type') === 'deposit')>{{ __('Deposit') }}</option>
                            <option value="withdrawal" @selected(old('transaction_type') === 'withdrawal')>{{ __('Withdrawal') }}</option>
                            <option value="transfer" @selected(old('transaction_type') === 'transfer')>{{ __('Transfer') }}</option>
                            <option value="fee" @selected(old('transaction_type') === 'fee')>{{ __('Fee') }}</option>
                            <option value="interest" @selected(old('transaction_type') === 'interest')>{{ __('Interest') }}</option>
                            <option value="penalty" @selected(old('transaction_type') === 'penalty')>{{ __('Penalty') }}</option>
                        </select>
                        @error('transaction_type')
                            <p class="text-rose-600 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1.5">{{ __('Amount') }} <span class="text-rose-500">*</span></label>
                        <input type="number" name="amount" step="0.01" min="0.01" value="{{ old('amount') }}" required class="w-full rounded-lg border-slate-300 text-sm shadow-xs focus:border-emerald-500 focus:ring-emerald-500" placeholder="0.00">
                        @error('amount')
                            <p class="text-rose-600 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-slate-700 mb-1.5">{{ __('Description') }}</label>
                        <textarea name="description" rows="3" class="w-full rounded-lg border-slate-300 text-sm shadow-xs focus:border-emerald-500 focus:ring-emerald-500" placeholder="{{ __('Transaction description...') }}">{{ old('description') }}</textarea>
                        @error('description')
                            <p class="text-rose-600 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1.5">{{ __('Reference Number') }}</label>
                        <input type="text" name="reference_number" value="{{ old('reference_number') }}" class="w-full rounded-lg border-slate-300 text-sm shadow-xs focus:border-emerald-500 focus:ring-emerald-500" placeholder="{{ __('Optional reference number') }}">
                        @error('reference_number')
                            <p class="text-rose-600 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1.5">{{ __('Category') }}</label>
                        <input type="text" name="category" value="{{ old('category') }}" class="w-full rounded-lg border-slate-300 text-sm shadow-xs focus:border-emerald-500 focus:ring-emerald-500" placeholder="{{ __('Transaction category') }}">
                        @error('category')
                            <p class="text-rose-600 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1.5">{{ __('Fees') }}</label>
                        <input type="number" name="fees" step="0.01" min="0" value="{{ old('fees', '0.00') }}" class="w-full rounded-lg border-slate-300 text-sm shadow-xs focus:border-emerald-500 focus:ring-emerald-500" placeholder="0.00">
                        @error('fees')
                            <p class="text-rose-600 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1.5">{{ __('Tax') }}</label>
                        <input type="number" name="tax" step="0.01" min="0" value="{{ old('tax', '0.00') }}" class="w-full rounded-lg border-slate-300 text-sm shadow-xs focus:border-emerald-500 focus:ring-emerald-500" placeholder="0.00">
                        @error('tax')
                            <p class="text-rose-600 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="bank-card-footer flex items-center justify-end gap-3">
                <a href="{{ route('transactions.index') }}" class="btn btn-secondary">
                    {{ __('Cancel') }}
                </a>
                <button type="submit" class="btn btn-primary">
                    {{ __('Create Transaction') }}
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
