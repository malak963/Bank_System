@extends('layouts.app')

@section('title', __('Create New Card'))

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('cards.index') }}">{{ __('Cards') }}</a></li>
    <li class="breadcrumb-item active">{{ __('Create New Card') }}</li>
@endsection

@section('content')
<div class="mx-auto max-w-4xl space-y-6 px-4 py-8 sm:px-6 lg:px-8">
    <div class="bank-card">
        <div class="bank-card-header">
            <h2 class="text-base font-semibold text-slate-950">{{ __('Card Configuration') }}</h2>
            <p class="text-xs text-slate-500 mt-0.5">{{ __('Assign an account, choose brand and type, and set limits.') }}</p>
        </div>

        <form action="{{ route('cards.store') }}" method="POST">
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
                        <label class="block text-sm font-medium text-slate-700 mb-1.5">{{ __('Card Type') }} <span class="text-rose-500">*</span></label>
                        <select name="card_type" required class="w-full rounded-lg border-slate-300 text-sm shadow-xs focus:border-emerald-500 focus:ring-emerald-500">
                            <option value="">{{ __('Select Type') }}</option>
                            <option value="debit" @selected(old('card_type') === 'debit')>{{ __('Debit Card') }}</option>
                            <option value="credit" @selected(old('card_type') === 'credit')>{{ __('Credit Card') }}</option>
                            <option value="prepaid" @selected(old('card_type') === 'prepaid')>{{ __('Prepaid Card') }}</option>
                            <option value="virtual" @selected(old('card_type') === 'virtual')>{{ __('Virtual Card') }}</option>
                        </select>
                        @error('card_type')
                            <p class="text-rose-600 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1.5">{{ __('Card Brand') }} <span class="text-rose-500">*</span></label>
                        <select name="card_brand" required class="w-full rounded-lg border-slate-300 text-sm shadow-xs focus:border-emerald-500 focus:ring-emerald-500">
                            <option value="">{{ __('Select Brand') }}</option>
                            <option value="visa" @selected(old('card_brand') === 'visa')>{{ __('Visa') }}</option>
                            <option value="mastercard" @selected(old('card_brand') === 'mastercard')>{{ __('Mastercard') }}</option>
                            <option value="american_express" @selected(old('card_brand') === 'american_express')>{{ __('American Express') }}</option>
                            <option value="discover" @selected(old('card_brand') === 'discover')>{{ __('Discover') }}</option>
                            <option value="maestro" @selected(old('card_brand') === 'maestro')>{{ __('Maestro') }}</option>
                            <option value="union_pay" @selected(old('card_brand') === 'union_pay')>{{ __('UnionPay') }}</option>
                        </select>
                        @error('card_brand')
                            <p class="text-rose-600 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-slate-700 mb-1.5">{{ __('Card Holder Name') }} <span class="text-rose-500">*</span></label>
                        <input type="text" name="card_holder_name" value="{{ old('card_holder_name') }}" required class="w-full rounded-lg border-slate-300 text-sm shadow-xs focus:border-emerald-500 focus:ring-emerald-500" placeholder="{{ __('Full name as it appears on ID') }}">
                        @error('card_holder_name')
                            <p class="text-rose-600 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1.5">{{ __('Daily Limit ($)') }}</label>
                        <input type="number" name="daily_limit" step="0.01" min="0" max="100000" value="{{ old('daily_limit', 5000) }}" class="w-full rounded-lg border-slate-300 text-sm shadow-xs focus:border-emerald-500 focus:ring-emerald-500" placeholder="5000">
                        @error('daily_limit')
                            <p class="text-rose-600 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1.5">{{ __('Monthly Limit ($)') }}</label>
                        <input type="number" name="monthly_limit" step="0.01" min="0" max="1000000" value="{{ old('monthly_limit', 20000) }}" class="w-full rounded-lg border-slate-300 text-sm shadow-xs focus:border-emerald-500 focus:ring-emerald-500" placeholder="20000">
                        @error('monthly_limit')
                            <p class="text-rose-600 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="md:col-span-2">
                        <h3 class="text-sm font-semibold text-slate-900 mb-3">{{ __('Card Features') }}</h3>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <label class="flex items-center gap-2 text-sm text-slate-700">
                                <input type="checkbox" name="international_enabled" value="1" @checked(old('international_enabled')) class="rounded border-slate-300 text-emerald-600 focus:ring-emerald-500">
                                <span>{{ __('International Transactions') }}</span>
                            </label>
                            <label class="flex items-center gap-2 text-sm text-slate-700">
                                <input type="checkbox" name="online_enabled" value="1" @checked(old('online_enabled', true)) class="rounded border-slate-300 text-emerald-600 focus:ring-emerald-500">
                                <span>{{ __('Online Payments') }}</span>
                            </label>
                            <label class="flex items-center gap-2 text-sm text-slate-700">
                                <input type="checkbox" name="contactless_enabled" value="1" @checked(old('contactless_enabled', true)) class="rounded border-slate-300 text-emerald-600 focus:ring-emerald-500">
                                <span>{{ __('Contactless Payments') }}</span>
                            </label>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1.5">{{ __('Delivery Method') }}</label>
                        <select name="delivery_method" class="w-full rounded-lg border-slate-300 text-sm shadow-xs focus:border-emerald-500 focus:ring-emerald-500">
                            <option value="branch">{{ __('Pick up at Branch') }}</option>
                            <option value="mail">{{ __('Regular Mail') }}</option>
                            <option value="courier">{{ __('Express Courier') }}</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1.5">{{ __('Priority') }}</label>
                        <select name="priority" class="w-full rounded-lg border-slate-300 text-sm shadow-xs focus:border-emerald-500 focus:ring-emerald-500">
                            <option value="standard">{{ __('Standard') }}</option>
                            <option value="express">{{ __('Express') }}</option>
                            <option value="urgent">{{ __('Urgent') }}</option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="bank-card-footer flex items-center justify-end gap-3">
                <a href="{{ route('cards.index') }}" class="btn btn-secondary">
                    {{ __('Cancel') }}
                </a>
                <button type="submit" class="btn btn-primary">
                    {{ __('Create Card') }}
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
