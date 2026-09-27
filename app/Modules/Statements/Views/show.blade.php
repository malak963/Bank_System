@extends('layouts.app')

@section('title', 'Statement Details')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-4xl mx-auto">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-3xl font-bold text-slate-800">{{ __('Statement Details') }}</h1>
            <a href="{{ route('statements.index') }}" 
               class="text-slate-600 hover:text-slate-900">Back</a>
        </div>

        <div class="bg-white rounded-lg shadow p-6 mb-6">
            <div class="grid grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-slate-500 mb-1">{{ __('Reference') }}</label>
                    <p class="text-lg font-semibold text-slate-900">{{ $statement->statement_reference }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-500 mb-1">{{ __('Status') }}</label>
                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                        @if($statement->status === 'completed') bg-emerald-100 text-emerald-800
                        @elseif($statement->status === 'generating') bg-blue-100 text-blue-800
                        @elseif($statement->status === 'pending') bg-yellow-100 text-yellow-800
                        @else bg-red-100 text-red-800 @endif">
                        {{ ucfirst($statement->status) }}
                    </span>
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-500 mb-1">{{ __('Account') }}</label>
                    <p class="text-slate-900">{{ $statement->account?->account_number ?? 'N/A' }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-500 mb-1">{{ __('Customer') }}</label>
                    <p class="text-slate-900">{{ $statement->customer?->first_name }} {{ $statement->customer?->last_name ?? 'N/A' }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-500 mb-1">{{ __('Period') }}</label>
                    <p class="text-slate-900">{{ $statement->period_start }} to {{ $statement->period_end }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-500 mb-1">{{ __('Generated At') }}</label>
                    <p class="text-slate-900">{{ $statement->generated_at?->format('Y-m-d H:i') ?? 'N/A' }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-500 mb-1">{{ __('Opening Balance') }}</label>
                    <p class="text-lg font-semibold text-slate-900">
                        {{ $statement->currency }} {{ number_format($statement->opening_balance, 2) }}
                    </p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-500 mb-1">{{ __('Closing Balance') }}</label>
                    <p class="text-lg font-semibold text-slate-900">
                        {{ $statement->currency }} {{ number_format($statement->closing_balance, 2) }}
                    </p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-500 mb-1">{{ __('Total Debits') }}</label>
                    <p class="text-slate-900">{{ $statement->currency }} {{ number_format($statement->total_debits, 2) }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-500 mb-1">{{ __('Total Credits') }}</label>
                    <p class="text-slate-900">{{ $statement->currency }} {{ number_format($statement->total_credits, 2) }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-500 mb-1">{{ __('Transaction Count') }}</label>
                    <p class="text-slate-900">{{ $statement->transaction_count }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-500 mb-1">{{ __('File Size') }}</label>
                    <p class="text-slate-900">{{ $statement->file_size ? number_format($statement->file_size / 1024, 2) . ' KB' : 'N/A' }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow overflow-hidden mb-6">
            <div class="px-6 py-4 border-b border-slate-200">
                <h2 class="text-lg font-semibold text-slate-800">{{ __('Transactions in Period') }}</h2>
            </div>
            <table class="min-w-full divide-y divide-slate-200">
                <thead class="bg-slate-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">{{ __('Reference') }}</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">{{ __('Type') }}</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">{{ __('Amount') }}</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">{{ __('Date') }}</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">{{ __('Status') }}</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-slate-200">
                    @forelse($statement->transactions as $transaction)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-slate-900">
                                {{ $transaction->transaction_reference }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-500">
                                {{ method_exists($transaction->transaction_type, 'label') ? $transaction->transaction_type->label() : ($transaction->transaction_type->value ?? $transaction->transaction_type) }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium {{ method_exists($transaction, 'isCredit') && $transaction->isCredit() ? 'text-emerald-600' : 'text-red-600' }}">
                                {{ (method_exists($transaction, 'isCredit') && $transaction->isCredit()) ? '+' : '-' }}{{ number_format($transaction->amount, 2) }} {{ $statement->currency }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-500">
                                {{ $transaction->created_at->format('Y-m-d H:i') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-slate-100 text-slate-800">
                                    {{ $transaction->status->value ?? $transaction->status }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-4 text-center text-sm text-slate-500">
                                {{ __('No transactions found for this period.') }}
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="flex justify-end space-x-4">
            @if($statement->status === 'completed' && !$statement->file_path)
            <form action="{{ route('statements.generate', $statement->id) }}" method="POST" class="inline">
                @csrf
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                    Generate File
                </button>
            </form>
            @endif
            @if($statement->file_path)
            <a href="{{ route('statements.download', $statement->id) }}" 
               class="px-4 py-2 bg-emerald-600 text-white rounded-lg hover:bg-emerald-700">
                Download CSV
            </a>
            @endif
        </div>
    </div>
</div>
@endsection
