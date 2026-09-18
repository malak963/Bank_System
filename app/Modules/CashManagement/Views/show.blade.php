@extends('layouts.app')

@section('title', 'Cash Operation Details')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-4xl mx-auto">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-3xl font-bold text-slate-800">Cash Operation Details</h1>
            <a href="{{ route('cash-management.index') }}" 
               class="text-slate-600 hover:text-slate-900">Back</a>
        </div>

        <div class="bg-white rounded-lg shadow p-6 mb-6">
            <div class="grid grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-slate-500 mb-1">{{ __('Reference') }}</label>
                    <p class="text-lg font-semibold text-slate-900">{{ $operation->operation_reference }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-500 mb-1">{{ __('Status') }}</label>
                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                        @if($operation->status === 'completed') bg-emerald-100 text-emerald-800
                        @elseif($operation->status === 'approved') bg-blue-100 text-blue-800
                        @elseif($operation->status === 'pending') bg-yellow-100 text-yellow-800
                        @elseif($operation->status === 'rejected') bg-red-100 text-red-800
                        @else bg-slate-100 text-slate-800 @endif">
                        {{ ucfirst($operation->status) }}
                    </span>
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-500 mb-1">{{ __('Operation Type') }}</label>
                    <p class="text-slate-900">{{ ucfirst($operation->operation_type) }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-500 mb-1">{{ __('Amount') }}</label>
                    <p class="text-lg font-semibold text-slate-900">
                        {{ $operation->currency }} {{ number_format($operation->amount, 2) }}
                    </p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-500 mb-1">{{ __('Branch') }}</label>
                    <p class="text-slate-900">{{ $operation->branch?->name ?? 'N/A' }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-500 mb-1">{{ __('Teller') }}</label>
                    <p class="text-slate-900">{{ $operation->teller?->name ?? 'N/A' }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-500 mb-1">{{ __('Account') }}</label>
                    <p class="text-slate-900">{{ $operation->account?->account_number ?? 'N/A' }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-500 mb-1">{{ __('Operation Date') }}</label>
                    <p class="text-slate-900">{{ $operation->operation_date?->format('Y-m-d H:i') ?? 'N/A' }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-500 mb-1">{{ __('Approved By') }}</label>
                    <p class="text-slate-900">{{ $operation->approvedBy?->name ?? 'N/A' }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-500 mb-1">{{ __('Approved At') }}</label>
                    <p class="text-slate-900">{{ $operation->approved_at?->format('Y-m-d H:i') ?? 'N/A' }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-500 mb-1">Completed At</label>
                    <p class="text-slate-900">{{ $operation->completed_at?->format('Y-m-d H:i') ?? 'N/A' }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-500 mb-1">Counterparty</label>
                    <p class="text-slate-900">{{ $operation->counterparty_name ?? 'N/A' }}</p>
                </div>
            </div>

            @if($operation->description)
            <div class="mt-6">
                <label class="block text-sm font-medium text-slate-500 mb-1">{{ __('Description') }}</label>
                <p class="text-slate-900">{{ $operation->description }}</p>
            </div>
            @endif

            @if($operation->notes)
            <div class="mt-6">
                <label class="block text-sm font-medium text-slate-500 mb-1">{{ __('Notes') }}</label>
                <p class="text-slate-900">{{ $operation->notes }}</p>
            </div>
            @endif
        </div>

        <div class="flex justify-end space-x-4">
            <a href="{{ route('cash-management.edit', $operation->id) }}" 
               class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                Edit
            </a>
            @if($operation->status === 'pending')
            <form action="{{ route('cash-management.destroy', $operation->id) }}" method="POST" class="inline">
                @csrf
                @method('DELETE')
                <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700">
                    Delete
                </button>
            </form>
            @endif
        </div>
    </div>
</div>
@endsection
