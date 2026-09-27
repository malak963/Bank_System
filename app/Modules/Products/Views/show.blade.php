@extends('layouts.app')

@section('title', __('Product Details'))

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('products.index') }}">{{ __('Banking Products') }}</a></li>
    <li class="breadcrumb-item active font-mono" dir="ltr">{{ $product->product_number }}</li>
@endsection

@section('actions')
    <a href="{{ route('products.index') }}" class="btn btn-secondary">
        {{ __('Back to Products') }}
    </a>
@endsection

@section('content')
<div class="mx-auto max-w-7xl space-y-6 px-4 py-8 sm:px-6 lg:px-8">
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 space-y-6">
            <div class="bank-card">
                <div class="bank-card-header flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                    <div>
                        <span class="text-xs font-semibold uppercase tracking-wider text-slate-500">{{ $product->product_type->label() }}</span>
                        <h2 class="text-xl font-bold text-slate-900 mt-0.5">{{ $product->name }}</h2>
                        <p class="text-xs text-slate-400 font-mono mt-0.5" dir="ltr">{{ $product->product_number }}</p>
                    </div>
                    <span class="inline-flex items-center rounded-full px-3 py-1 text-xs font-semibold ring-1 ring-inset {{ $product->status->badgeColor() }}">
                        {{ $product->status->label() }}
                    </span>
                </div>

                <div class="bank-card-body">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pb-6 border-b border-slate-100">
                        <div>
                            <p class="text-xs font-semibold uppercase tracking-wider text-slate-500">{{ __('Current Balance') }}</p>
                            <p class="text-3xl font-bold mt-1 font-mono text-slate-900" dir="ltr">
                                ${{ number_format($product->balance, 2) }}
                            </p>
                        </div>
                        <div>
                            <p class="text-xs font-semibold uppercase tracking-wider text-slate-500">{{ __('Interest Rate') }}</p>
                            <p class="text-3xl font-bold mt-1 font-mono text-emerald-700" dir="ltr">
                                {{ $product->interest_rate }}%
                            </p>
                        </div>
                    </div>

                    <div class="pt-6">
                        <h3 class="text-sm font-semibold text-slate-900 mb-4">{{ __('Product Terms & Specs') }}</h3>
                        <dl class="grid grid-cols-1 sm:grid-cols-2 gap-x-6 gap-y-4 text-sm">
                            <div class="flex justify-between border-b border-slate-50 pb-2">
                                <dt class="text-slate-500">{{ __('Customer') }}</dt>
                                <dd class="font-medium text-slate-900">{{ $product->customer?->full_name ?? __('N/A') }}</dd>
                            </div>
                            <div class="flex justify-between border-b border-slate-50 pb-2">
                                <dt class="text-slate-500">{{ __('Linked Account') }}</dt>
                                <dd class="font-medium text-slate-900 font-mono" dir="ltr">{{ $product->account?->account_number ?? __('None') }}</dd>
                            </div>
                            <div class="flex justify-between border-b border-slate-50 pb-2">
                                <dt class="text-slate-500">{{ __('Term (Months)') }}</dt>
                                <dd class="font-medium text-slate-900 font-mono" dir="ltr">{{ $product->term_months ?? __('Ongoing') }}</dd>
                            </div>
                            <div class="flex justify-between border-b border-slate-50 pb-2">
                                <dt class="text-slate-500">{{ __('Minimum Balance') }}</dt>
                                <dd class="font-medium text-slate-900 font-mono" dir="ltr">${{ number_format($product->minimum_balance, 2) }}</dd>
                            </div>
                            <div class="flex justify-between border-b border-slate-50 pb-2">
                                <dt class="text-slate-500">{{ __('Calculation Frequency') }}</dt>
                                <dd class="font-medium text-slate-900 capitalize">{{ __($product->interest_calculation_frequency) }}</dd>
                            </div>
                            <div class="flex justify-between border-b border-slate-50 pb-2">
                                <dt class="text-slate-500">{{ __('Interest Earned') }}</dt>
                                <dd class="font-medium text-slate-900 font-mono" dir="ltr">${{ number_format($product->interest_earned, 2) }}</dd>
                            </div>
                            <div class="flex justify-between border-b border-slate-50 pb-2">
                                <dt class="text-slate-500">{{ __('Opening Date') }}</dt>
                                <dd class="font-medium text-slate-900 font-mono" dir="ltr">{{ $product->opened_at?->format('Y-m-d') ?? __('N/A') }}</dd>
                            </div>
                            <div class="flex justify-between border-b border-slate-50 pb-2">
                                <dt class="text-slate-500">{{ __('Maturity Date') }}</dt>
                                <dd class="font-medium text-slate-900 font-mono" dir="ltr">{{ $product->matures_at?->format('Y-m-d') ?? __('None') }}</dd>
                            </div>
                        </dl>
                    </div>

                    @if($product->terms_and_conditions)
                        <div class="border-t border-slate-100 pt-4 mt-6">
                            <h3 class="text-xs font-semibold uppercase tracking-wider text-slate-500 mb-1.5">{{ __('Terms and Conditions') }}</h3>
                            <p class="text-xs text-slate-700 bg-slate-50 p-3 rounded-lg leading-relaxed">{{ $product->terms_and_conditions }}</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="space-y-6">
            <div class="bank-card">
                <div class="bank-card-header">
                    <h3 class="text-sm font-semibold text-slate-900">{{ __('Product Operations') }}</h3>
                </div>
                <div class="bank-card-body space-y-4">
                    @if($product->isActive())
                        <form action="{{ route('products.apply-interest', $product) }}" method="POST">
                            @csrf
                            <p class="text-xs text-slate-500 mb-2">{{ __('Accrue and post pending interest to balance.') }}</p>
                            <button type="submit" class="w-full btn btn-primary text-xs">
                                {{ __('Apply Interest Now') }}
                            </button>
                        </form>

                        <form action="{{ route('products.close', $product) }}" method="POST" class="pt-3 border-t border-slate-100">
                            @csrf
                            <p class="text-xs text-slate-500 mb-2">{{ __('Close account product and settle principal.') }}</p>
                            <button type="submit" class="w-full btn btn-danger text-xs" onclick="return confirm('{{ __('Are you sure you want to close this product?') }}')">
                                {{ __('Close Product') }}
                            </button>
                        </form>
                    @else
                        <p class="text-xs text-slate-400 text-center py-2">{{ __('No operational actions available for current product state.') }}</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
