@extends('layouts.app')

@section('title', 'Generate Statement')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-2xl mx-auto">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-3xl font-bold text-slate-800">Generate Statement</h1>
            <a href="{{ route('statements.index') }}" 
               class="text-slate-600 hover:text-slate-900">Back</a>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <form action="{{ route('statements.store') }}" method="POST">
                @csrf
                
                <div class="grid grid-cols-1 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-2">Account</label>
                        <select name="account_id" required class="w-full border-slate-300 rounded-lg shadow-sm focus:ring-emerald-500 focus:border-emerald-500">
                            <option value="">Select Account</option>
                            @foreach(\App\Modules\Accounts\Models\Account::all() as $account)
                            <option value="{{ $account->id }}">{{ $account->account_number }} - {{ $account->customer->first_name }} {{ $account->customer->last_name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-2">Customer</label>
                        <select name="customer_id" required class="w-full border-slate-300 rounded-lg shadow-sm focus:ring-emerald-500 focus:border-emerald-500">
                            <option value="">Select Customer</option>
                            @foreach(\App\Modules\Customers\Models\Customer::all() as $customer)
                            <option value="{{ $customer->id }}">{{ $customer->first_name }} {{ $customer->last_name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-2">Period Start</label>
                        <input type="date" name="period_start" required 
                               class="w-full border-slate-300 rounded-lg shadow-sm focus:ring-emerald-500 focus:border-emerald-500">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-2">Period End</label>
                        <input type="date" name="period_end" required 
                               class="w-full border-slate-300 rounded-lg shadow-sm focus:ring-emerald-500 focus:border-emerald-500">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-2">Opening Balance</label>
                        <input type="number" name="opening_balance" step="0.01" value="0" required 
                               class="w-full border-slate-300 rounded-lg shadow-sm focus:ring-emerald-500 focus:border-emerald-500">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-2">Currency</label>
                        <input type="text" name="currency" value="SYP" required 
                               class="w-full border-slate-300 rounded-lg shadow-sm focus:ring-emerald-500 focus:border-emerald-500">
                    </div>

                    <div class="flex justify-end space-x-4">
                        <a href="{{ route('statements.index') }}" 
                           class="px-4 py-2 border border-slate-300 rounded-lg text-slate-700 hover:bg-slate-50">
                            Cancel
                        </a>
                        <button type="submit" 
                                class="px-4 py-2 bg-emerald-600 text-white rounded-lg hover:bg-emerald-700">
                            Generate Statement
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
