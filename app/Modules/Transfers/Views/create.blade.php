@extends('layouts.app')

@section('title', __('Create New Transfer'))

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('transfers.index') }}">{{ __('Transfers') }}</a></li>
    <li class="breadcrumb-item active">{{ __('Create New Transfer') }}</li>
@endsection

@section('content')
<div class="mx-auto max-w-4xl space-y-6 px-4 py-8 sm:px-6 lg:px-8">
    <div class="bank-card">
        <div class="bank-card-header">
            <h2 class="text-base font-semibold text-slate-950">{{ __('Transfer Setup') }}</h2>
            <p class="text-xs text-slate-500 mt-0.5">{{ __('Select transfer type, debit account, beneficiary details, and amount.') }}</p>
        </div>

        <form action="{{ route('transfers.store') }}" method="POST">
            @csrf
            
            <div class="bank-card-body space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1.5">{{ __('Transfer Type') }} <span class="text-rose-500">*</span></label>
                        <select name="transfer_type" required class="w-full rounded-lg border-slate-300 text-sm shadow-xs focus:border-emerald-500 focus:ring-emerald-500" onchange="toggleTransferFields(this.value)">
                            <option value="">{{ __('Select Type') }}</option>
                            @foreach($transferTypes as $type)
                                <option value="{{ $type->value }}" @selected(old('transfer_type') === $type->value)>{{ $type->label() }}</option>
                            @endforeach
                        </select>
                        @error('transfer_type')
                            <p class="text-rose-600 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1.5">{{ __('Customer') }} <span class="text-rose-500">*</span></label>
                        <select name="customer_id" required class="w-full rounded-lg border-slate-300 text-sm shadow-xs focus:border-emerald-500 focus:ring-emerald-500">
                            <option value="">{{ __('Select Customer') }}</option>
                            @foreach($customers as $customer)
                                <option value="{{ $customer->id }}" @selected(old('customer_id') == $customer->id)>
                                    {{ $customer->full_name }} ({{ $customer->customer_number }})
                                </option>
                            @endforeach
                        </select>
                        @error('customer_id')
                            <p class="text-rose-600 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1.5">{{ __('From Account') }} <span class="text-rose-500">*</span></label>
                        <select name="from_account_id" required class="w-full rounded-lg border-slate-300 text-sm shadow-xs focus:border-emerald-500 focus:ring-emerald-500">
                            <option value="">{{ __('Select Source Account') }}</option>
                            @foreach($accounts as $account)
                                <option value="{{ $account->id }}" @selected(old('from_account_id') == $account->id)>
                                    {{ $account->account_number }} ({{ __('Balance:') }} ${{ number_format($account->balance, 2) }})
                                </option>
                            @endforeach
                        </select>
                        @error('from_account_id')
                            <p class="text-rose-600 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div id="to-account-field">
                        <label class="block text-sm font-medium text-slate-700 mb-1.5">{{ __('To Account (Internal)') }}</label>
                        <select name="to_account_id" class="w-full rounded-lg border-slate-300 text-sm shadow-xs focus:border-emerald-500 focus:ring-emerald-500">
                            <option value="">{{ __('Select Destination Account') }}</option>
                            @foreach($accounts as $account)
                                <option value="{{ $account->id }}" @selected(old('to_account_id') == $account->id)>
                                    {{ $account->account_number }} - {{ $account->customer->full_name ?? __('Unknown') }}
                                </option>
                            @endforeach
                        </select>
                        @error('to_account_id')
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

                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1.5">{{ __('Currency') }} <span class="text-rose-500">*</span></label>
                        <select name="currency" required class="w-full rounded-lg border-slate-300 text-sm shadow-xs focus:border-emerald-500 focus:ring-emerald-500">
                            <option value="USD">USD</option>
                            <option value="EUR">EUR</option>
                            <option value="GBP">GBP</option>
                            <option value="SAR">SAR</option>
                            <option value="AED">AED</option>
                        </select>
                        @error('currency')
                            <p class="text-rose-600 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- External Transfer Fields -->
                    <div id="external-fields" class="md:col-span-2 hidden space-y-6 pt-4 border-t border-slate-100">
                        <h3 class="text-sm font-semibold text-slate-900">{{ __('Beneficiary Bank Details') }}</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-1.5">{{ __('Beneficiary Name') }}</label>
                                <input type="text" name="beneficiary_name" value="{{ old('beneficiary_name') }}" class="w-full rounded-lg border-slate-300 text-sm shadow-xs focus:border-emerald-500 focus:ring-emerald-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-1.5">{{ __('Beneficiary Account / IBAN') }}</label>
                                <input type="text" name="beneficiary_account_number" value="{{ old('beneficiary_account_number') }}" class="w-full rounded-lg border-slate-300 text-sm shadow-xs focus:border-emerald-500 focus:ring-emerald-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-1.5">{{ __('Bank Name') }}</label>
                                <input type="text" name="beneficiary_bank_name" value="{{ old('beneficiary_bank_name') }}" class="w-full rounded-lg border-slate-300 text-sm shadow-xs focus:border-emerald-500 focus:ring-emerald-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-1.5">{{ __('Routing / SWIFT Code') }}</label>
                                <input type="text" name="beneficiary_routing_number" value="{{ old('beneficiary_routing_number') }}" class="w-full rounded-lg border-slate-300 text-sm shadow-xs focus:border-emerald-500 focus:ring-emerald-500">
                            </div>
                        </div>
                    </div>

                    <!-- Scheduled Transfer Fields -->
                    <div id="scheduled-fields" class="md:col-span-2 hidden space-y-4 pt-4 border-t border-slate-100">
                        <h3 class="text-sm font-semibold text-slate-900">{{ __('Scheduling') }}</h3>
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1.5">{{ __('Execution Date & Time') }}</label>
                            <input type="datetime-local" name="scheduled_at" value="{{ old('scheduled_at') }}" class="w-full rounded-lg border-slate-300 text-sm shadow-xs focus:border-emerald-500 focus:ring-emerald-500">
                        </div>
                    </div>

                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-slate-700 mb-1.5">{{ __('Description / Reference') }}</label>
                        <textarea name="description" rows="2" class="w-full rounded-lg border-slate-300 text-sm shadow-xs focus:border-emerald-500 focus:ring-emerald-500" placeholder="{{ __('Payment memo or note...') }}">{{ old('description') }}</textarea>
                    </div>
                </div>
            </div>

            <div class="bank-card-footer flex items-center justify-end gap-3">
                <a href="{{ route('transfers.index') }}" class="btn btn-secondary">
                    {{ __('Cancel') }}
                </a>
                <button type="submit" class="btn btn-primary">
                    {{ __('Create Transfer') }}
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function toggleTransferFields(type) {
    const toAccountField = document.getElementById('to-account-field');
    const externalFields = document.getElementById('external-fields');
    const scheduledFields = document.getElementById('scheduled-fields');
    
    if (type === 'internal') {
        toAccountField.classList.remove('hidden');
        externalFields.classList.add('hidden');
    } else if (type === 'external' || type === 'international') {
        toAccountField.classList.add('hidden');
        externalFields.classList.remove('hidden');
    } else {
        toAccountField.classList.remove('hidden');
        externalFields.classList.add('hidden');
    }
    
    if (type === 'scheduled') {
        scheduledFields.classList.remove('hidden');
    } else {
        scheduledFields.classList.add('hidden');
    }
}
</script>
@endsection
