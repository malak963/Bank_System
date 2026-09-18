@extends('layouts.app')

@section('title', 'Bills & Payments')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold text-slate-800">{{ __('Bills & Payments') }}</h1>
        <div class="flex space-x-4">
            <a href="{{ route('bills-payments.overdue') }}" 
               class="bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700 transition">
                Overdue
            </a>
            <a href="{{ route('bills-payments.upcoming') }}" 
               class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition">
                Upcoming
            </a>
            <a href="{{ route('bills-payments.create') }}" 
               class="bg-emerald-600 text-white px-4 py-2 rounded-lg hover:bg-emerald-700 transition">
                New Bill
            </a>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow overflow-hidden">
        <table class="min-w-full divide-y divide-slate-200">
            <thead class="bg-slate-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">{{ __('Reference') }}</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">{{ __('Provider') }}</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">{{ __('Type') }}</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">{{ __('Amount') }}</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">{{ __('Due Date') }}</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">{{ __('Status') }}</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">{{ __('Actions') }}</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-slate-200">
                @forelse($bills as $bill)
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-slate-900">
                        {{ $bill->bill_reference }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-500">
                        {{ $bill->provider_name }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-500">
                        {{ $bill->bill_type->getLabel() }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-500">
                        {{ $bill->currency }} {{ number_format($bill->amount, 2) }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-500">
                        {{ $bill->due_date?->format('Y-m-d') ?? 'N/A' }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                            @if($bill->status->getColor() === 'emerald') bg-emerald-100 text-emerald-800
                            @elseif($bill->status->getColor() === 'red') bg-red-100 text-red-800
                            @elseif($bill->status->getColor() === 'yellow') bg-yellow-100 text-yellow-800
                            @elseif($bill->status->getColor() === 'blue') bg-blue-100 text-blue-800
                            @elseif($bill->status->getColor() === 'purple') bg-purple-100 text-purple-800
                            @elseif($bill->status->getColor() === 'gray') bg-gray-100 text-gray-800
                            @else bg-orange-100 text-orange-800 @endif">
                            {{ $bill->status->getLabel() }}
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                        <a href="{{ route('bills-payments.show', $bill->id) }}" 
                           class="text-emerald-600 hover:text-emerald-900">View</a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="px-6 py-4 text-center text-sm text-slate-500">
                        No bills found
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
