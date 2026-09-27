@extends('layouts.app')

@section('title', __('Banking Products'))

@section('breadcrumb')
    <li class="breadcrumb-item active">{{ __('Banking Products') }}</li>
@endsection

@section('actions')
    <a href="{{ route('products.create') }}" class="btn btn-primary">
        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"></path>
        </svg>
        <span>{{ __('New Product') }}</span>
    </a>
@endsection

@section('content')
<div class="mx-auto max-w-7xl space-y-6 px-4 py-8 sm:px-6 lg:px-8">
    <!-- Statistics Cards -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <div class="bank-card p-4">
            <p class="text-xs font-semibold uppercase text-slate-500">{{ __('Total Products') }}</p>
            <p class="text-2xl font-bold text-slate-900 mt-1 font-mono" dir="ltr">{{ $statistics['total'] ?? $statistics['total_products'] ?? 0 }}</p>
        </div>
        <div class="bank-card p-4">
            <p class="text-xs font-semibold uppercase text-emerald-700">{{ __('Active') }}</p>
            <p class="text-2xl font-bold text-emerald-800 mt-1 font-mono" dir="ltr">{{ $statistics['active'] ?? $statistics['active_products'] ?? 0 }}</p>
        </div>
        <div class="bank-card p-4">
            <p class="text-xs font-semibold uppercase text-slate-500">{{ __('Total Balance') }}</p>
            <p class="text-2xl font-bold text-slate-900 mt-1 font-mono" dir="ltr">${{ number_format($statistics['total_balance'] ?? 0, 2) }}</p>
        </div>
        <div class="bank-card p-4">
            <p class="text-xs font-semibold uppercase text-slate-500">{{ __('Interest Earned') }}</p>
            <p class="text-2xl font-bold text-slate-900 mt-1 font-mono" dir="ltr">${{ number_format($statistics['total_interest_earned'] ?? 0, 2) }}</p>
        </div>
    </div>

    <!-- Products Table -->
    <div class="bank-card">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200">
                <thead class="bg-slate-50">
                    <tr>
                        <th class="px-5 py-3.5 text-start text-xs font-semibold uppercase tracking-wider text-slate-500">{{ __('Product #') }}</th>
                        <th class="px-5 py-3.5 text-start text-xs font-semibold uppercase tracking-wider text-slate-500">{{ __('Name') }}</th>
                        <th class="px-5 py-3.5 text-start text-xs font-semibold uppercase tracking-wider text-slate-500">{{ __('Type') }}</th>
                        <th class="px-5 py-3.5 text-start text-xs font-semibold uppercase tracking-wider text-slate-500">{{ __('Balance') }}</th>
                        <th class="px-5 py-3.5 text-start text-xs font-semibold uppercase tracking-wider text-slate-500">{{ __('Interest Rate') }}</th>
                        <th class="px-5 py-3.5 text-start text-xs font-semibold uppercase tracking-wider text-slate-500">{{ __('Status') }}</th>
                        <th class="px-5 py-3.5 text-start text-xs font-semibold uppercase tracking-wider text-slate-500">{{ __('Customer') }}</th>
                        <th class="px-5 py-3.5 text-end text-xs font-semibold uppercase tracking-wider text-slate-500">{{ __('Actions') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 bg-white">
                    @forelse($products as $product)
                        <tr class="transition hover:bg-slate-50">
                            <td class="px-5 py-4 whitespace-nowrap text-sm font-semibold text-slate-900 font-mono" dir="ltr">
                                {{ $product->product_number }}
                            </td>
                            <td class="px-5 py-4 whitespace-nowrap text-sm font-medium text-slate-900">
                                <a href="{{ route('products.show', $product) }}" class="hover:text-emerald-700">
                                    {{ $product->name }}
                                </a>
                            </td>
                            <td class="px-5 py-4 whitespace-nowrap text-sm text-slate-600">
                                {{ $product->product_type->label() }}
                            </td>
                            <td class="px-5 py-4 whitespace-nowrap text-sm font-semibold text-slate-900 font-mono" dir="ltr">
                                ${{ number_format($product->balance, 2) }}
                            </td>
                            <td class="px-5 py-4 whitespace-nowrap text-sm text-slate-600 font-mono" dir="ltr">
                                {{ $product->interest_rate }}%
                            </td>
                            <td class="px-5 py-4 whitespace-nowrap text-sm">
                                <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-semibold ring-1 ring-inset {{ is_object($product->status) && method_exists($product->status, 'badgeColor') ? $product->status->badgeColor() : 'bg-slate-50 text-slate-700 ring-slate-200' }}">
                                    {{ is_object($product->status) && method_exists($product->status, 'label') ? $product->status->label() : ($product->status ?? '-') }}
                                </span>
                            </td>
                            <td class="px-5 py-4 whitespace-nowrap text-sm text-slate-600">
                                {{ $product->customer?->full_name ?? __('N/A') }}
                            </td>
                            <td class="px-5 py-4 whitespace-nowrap text-end text-sm">
                                <a href="{{ route('products.show', $product) }}" class="btn btn-sm btn-secondary">
                                    {{ __('View') }}
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-6 py-12 text-center text-slate-500">
                                <p class="text-sm font-semibold text-slate-700">{{ __('No products found.') }}</p>
                                <p class="mt-1 text-xs text-slate-400">{{ __('Financial products will appear here.') }}</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @if (method_exists($products, 'hasPages') && $products->hasPages())
        <div class="rounded-xl border border-slate-200 bg-white px-4 py-3 shadow-sm">
            {{ $products->links() }}
        </div>
    @endif
</div>
@endsection
