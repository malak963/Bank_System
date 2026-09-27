@extends('layouts.app')

@section('title', __('Create Cash Operation'))

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('cash-management.index') }}">{{ __('Cash Operations') }}</a></li>
    <li class="breadcrumb-item active">{{ __('Create') }}</li>
@endsection

@section('actions')
    <a href="{{ route('cash-management.index') }}" class="btn btn-secondary">
        <svg class="w-4 h-4 rtl:rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
        </svg>
        <span>{{ __('Back') }}</span>
    </a>
@endsection

@section('content')
<div class="max-w-3xl mx-auto">
    <div class="card p-6">
        <form action="{{ route('cash-management.store') }}" method="POST">
            @csrf
            
            <div class="space-y-6">
                <div>
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">{{ __('Branch') }}</label>
                    <select name="branch_id" required class="w-full border-slate-300 dark:border-slate-700 dark:bg-slate-800 dark:text-white rounded-lg shadow-sm focus:ring-emerald-500 focus:border-emerald-500">
                        <option value="">{{ __('Select Branch') }}</option>
                        @foreach(\App\Modules\Branches\Models\Branch::all() as $branch)
                        <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">{{ __('Operation Type') }}</label>
                    <select name="operation_type" required class="w-full border-slate-300 dark:border-slate-700 dark:bg-slate-800 dark:text-white rounded-lg shadow-sm focus:ring-emerald-500 focus:border-emerald-500">
                        <option value="">{{ __('Select Type') }}</option>
                        <option value="deposit">{{ __('Deposit') }}</option>
                        <option value="withdrawal">{{ __('Withdrawal') }}</option>
                        <option value="transfer">{{ __('Transfer') }}</option>
                        <option value="replenishment">{{ __('Replenishment') }}</option>
                        <option value="withdrawal_to_vault">{{ __('Withdrawal to Vault') }}</option>
                        <option value="deposit_from_vault">{{ __('Deposit from Vault') }}</option>
                    </select>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">{{ __('Amount') }}</label>
                        <input type="number" name="amount" step="0.01" required 
                               class="w-full border-slate-300 dark:border-slate-700 dark:bg-slate-800 dark:text-white rounded-lg shadow-sm focus:ring-emerald-500 focus:border-emerald-500">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">{{ __('Currency') }}</label>
                        <input type="text" name="currency" value="USD" required 
                               class="w-full border-slate-300 dark:border-slate-700 dark:bg-slate-800 dark:text-white rounded-lg shadow-sm focus:ring-emerald-500 focus:border-emerald-500">
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">{{ __('Operation Date') }}</label>
                    <input type="date" name="operation_date" 
                           class="w-full border-slate-300 dark:border-slate-700 dark:bg-slate-800 dark:text-white rounded-lg shadow-sm focus:ring-emerald-500 focus:border-emerald-500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">{{ __('Account') }} ({{ __('Optional') }})</label>
                    <select name="account_id" class="w-full border-slate-300 dark:border-slate-700 dark:bg-slate-800 dark:text-white rounded-lg shadow-sm focus:ring-emerald-500 focus:border-emerald-500">
                        <option value="">{{ __('Select Account') }}</option>
                        @foreach(\App\Modules\Accounts\Models\Account::all() as $account)
                        <option value="{{ $account->id }}">{{ $account->account_number }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">{{ __('Description') }}</label>
                    <textarea name="description" rows="3" 
                              class="w-full border-slate-300 dark:border-slate-700 dark:bg-slate-800 dark:text-white rounded-lg shadow-sm focus:ring-emerald-500 focus:border-emerald-500"></textarea>
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">{{ __('Notes') }}</label>
                    <textarea name="notes" rows="2" 
                              class="w-full border-slate-300 dark:border-slate-700 dark:bg-slate-800 dark:text-white rounded-lg shadow-sm focus:ring-emerald-500 focus:border-emerald-500"></textarea>
                </div>

                <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-100 dark:border-slate-800">
                    <a href="{{ route('cash-management.index') }}" class="btn btn-secondary">
                        {{ __('Cancel') }}
                    </a>
                    <button type="submit" class="btn btn-primary">
                        {{ __('Create Operation') }}
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
