@extends('layouts.app')

@section('title', __('Edit Transaction'))

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('transactions.index') }}">{{ __('Transactions') }}</a></li>
    <li class="breadcrumb-item"><a href="{{ route('transactions.show', $transaction) }}">{{ $transaction->transaction_reference }}</a></li>
    <li class="breadcrumb-item active">{{ __('Edit') }}</li>
@endsection

@section('content')
<div class="mx-auto max-w-4xl space-y-6 px-4 py-8 sm:px-6 lg:px-8">
    <div class="bank-card">
        <div class="bank-card-header">
            <h2 class="text-base font-semibold text-slate-950">{{ __('Edit Transaction Metadata') }}</h2>
            <p class="text-xs text-slate-500 mt-0.5">{{ __('Reference:') }} <span class="font-mono" dir="ltr">{{ $transaction->transaction_reference }}</span></p>
        </div>

        <form action="{{ route('transactions.update', $transaction) }}" method="POST">
            @csrf
            @method('PUT')
            
            <div class="bank-card-body space-y-6">
                <div class="rounded-lg border border-amber-200 bg-amber-50 p-4">
                    <p class="text-xs text-amber-900 leading-relaxed">
                        <strong class="font-semibold">{{ __('Note:') }}</strong> {{ __('Only metadata fields can be edited. Transaction amount, type, and status cannot be modified after creation.') }}
                    </p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-slate-700 mb-1.5">{{ __('Description') }}</label>
                        <textarea name="description" rows="3" class="w-full rounded-lg border-slate-300 text-sm shadow-xs focus:border-emerald-500 focus:ring-emerald-500" placeholder="{{ __('Transaction description...') }}">{{ old('description', $transaction->description) }}</textarea>
                        @error('description')
                            <p class="text-rose-600 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1.5">{{ __('Category') }}</label>
                        <input type="text" name="category" value="{{ old('category', $transaction->category) }}" class="w-full rounded-lg border-slate-300 text-sm shadow-xs focus:border-emerald-500 focus:ring-emerald-500" placeholder="{{ __('Transaction category') }}">
                        @error('category')
                            <p class="text-rose-600 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1.5">{{ __('Sub Category') }}</label>
                        <input type="text" name="sub_category" value="{{ old('sub_category', $transaction->sub_category) }}" class="w-full rounded-lg border-slate-300 text-sm shadow-xs focus:border-emerald-500 focus:ring-emerald-500" placeholder="{{ __('Transaction sub-category') }}">
                        @error('sub_category')
                            <p class="text-rose-600 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-slate-700 mb-1.5">{{ __('Tags (comma separated)') }}</label>
                        <input type="text" name="tags" value="{{ old('tags', $transaction->tags ? implode(', ', $transaction->tags) : '') }}" class="w-full rounded-lg border-slate-300 text-sm shadow-xs focus:border-emerald-500 focus:ring-emerald-500" placeholder="{{ __('urgent, important, routine') }}">
                        @error('tags')
                            <p class="text-rose-600 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="bank-card-footer flex items-center justify-end gap-3">
                <a href="{{ route('transactions.show', $transaction) }}" class="btn btn-secondary">
                    {{ __('Cancel') }}
                </a>
                <button type="submit" class="btn btn-primary">
                    {{ __('Update Transaction') }}
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
