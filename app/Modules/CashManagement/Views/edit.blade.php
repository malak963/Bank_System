@extends('layouts.app')

@section('title', 'Edit Cash Operation')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-2xl mx-auto">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-3xl font-bold text-slate-800">Edit Cash Operation</h1>
            <a href="{{ route('cash-management.show', $operation->id) }}" 
               class="text-slate-600 hover:text-slate-900">Back</a>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <form action="{{ route('cash-management.update', $operation->id) }}" method="POST">
                @csrf
                @method('PUT')
                
                <div class="grid grid-cols-1 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-2">Branch</label>
                        <select name="branch_id" required class="w-full border-slate-300 rounded-lg shadow-sm focus:ring-emerald-500 focus:border-emerald-500">
                            <option value="">Select Branch</option>
                            @foreach(\App\Modules\Branches\Models\Branch::all() as $branch)
                            <option value="{{ $branch->id }}" {{ $operation->branch_id == $branch->id ? 'selected' : '' }}>
                                {{ $branch->name }}
                            </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-2">Operation Type</label>
                        <select name="operation_type" required class="w-full border-slate-300 rounded-lg shadow-sm focus:ring-emerald-500 focus:border-emerald-500">
                            <option value="">Select Type</option>
                            <option value="deposit" {{ $operation->operation_type === 'deposit' ? 'selected' : '' }}>Deposit</option>
                            <option value="withdrawal" {{ $operation->operation_type === 'withdrawal' ? 'selected' : '' }}>Withdrawal</option>
                            <option value="transfer" {{ $operation->operation_type === 'transfer' ? 'selected' : '' }}>Transfer</option>
                            <option value="replenishment" {{ $operation->operation_type === 'replenishment' ? 'selected' : '' }}>Replenishment</option>
                            <option value="withdrawal_to_vault" {{ $operation->operation_type === 'withdrawal_to_vault' ? 'selected' : '' }}>Withdrawal to Vault</option>
                            <option value="deposit_from_vault" {{ $operation->operation_type === 'deposit_from_vault' ? 'selected' : '' }}>Deposit from Vault</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-2">Amount</label>
                        <input type="number" name="amount" step="0.01" value="{{ $operation->amount }}" required 
                               class="w-full border-slate-300 rounded-lg shadow-sm focus:ring-emerald-500 focus:border-emerald-500">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-2">Currency</label>
                        <input type="text" name="currency" value="{{ $operation->currency }}" required 
                               class="w-full border-slate-300 rounded-lg shadow-sm focus:ring-emerald-500 focus:border-emerald-500">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-2">Operation Date</label>
                        <input type="date" name="operation_date" value="{{ $operation->operation_date?->format('Y-m-d') }}" 
                               class="w-full border-slate-300 rounded-lg shadow-sm focus:ring-emerald-500 focus:border-emerald-500">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-2">Account (Optional)</label>
                        <select name="account_id" class="w-full border-slate-300 rounded-lg shadow-sm focus:ring-emerald-500 focus:border-emerald-500">
                            <option value="">Select Account</option>
                            @foreach(\App\Modules\Accounts\Models\Account::all() as $account)
                            <option value="{{ $account->id }}" {{ $operation->account_id == $account->id ? 'selected' : '' }}>
                                {{ $account->account_number }}
                            </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-2">Description</label>
                        <textarea name="description" rows="3" 
                                  class="w-full border-slate-300 rounded-lg shadow-sm focus:ring-emerald-500 focus:border-emerald-500">{{ $operation->description }}</textarea>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-2">Notes</label>
                        <textarea name="notes" rows="2" 
                                  class="w-full border-slate-300 rounded-lg shadow-sm focus:ring-emerald-500 focus:border-emerald-500">{{ $operation->notes }}</textarea>
                    </div>

                    <div class="flex justify-end space-x-4">
                        <a href="{{ route('cash-management.show', $operation->id) }}" 
                           class="px-4 py-2 border border-slate-300 rounded-lg text-slate-700 hover:bg-slate-50">
                            Cancel
                        </a>
                        <button type="submit" 
                                class="px-4 py-2 bg-emerald-600 text-white rounded-lg hover:bg-emerald-700">
                            Update Operation
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
