@extends('layouts.app')

@section('title', __('Create New Ticket'))

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('customerService.index') }}">{{ __('Customer Service Tickets') }}</a></li>
    <li class="breadcrumb-item active">{{ __('Create New Ticket') }}</li>
@endsection

@section('content')
<div class="mx-auto max-w-4xl space-y-6 px-4 py-8 sm:px-6 lg:px-8">
    <div class="bank-card">
        <div class="bank-card-header">
            <h2 class="text-base font-semibold text-slate-950">{{ __('Ticket Information') }}</h2>
            <p class="text-xs text-slate-500 mt-0.5">{{ __('Enter subject, category, description, and assign customer or account.') }}</p>
        </div>

        <form action="{{ route('customerService.store') }}" method="POST">
            @csrf
            
            <div class="bank-card-body space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-slate-700 mb-1.5">{{ __('Subject') }} <span class="text-rose-500">*</span></label>
                        <input type="text" name="subject" value="{{ old('subject') }}" required class="w-full rounded-lg border-slate-300 text-sm shadow-xs focus:border-emerald-500 focus:ring-emerald-500" placeholder="{{ __('Enter ticket subject') }}">
                        @error('subject')
                            <p class="text-rose-600 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1.5">{{ __('Category') }} <span class="text-rose-500">*</span></label>
                        <select name="category" required class="w-full rounded-lg border-slate-300 text-sm shadow-xs focus:border-emerald-500 focus:ring-emerald-500">
                            <option value="">{{ __('Select Category') }}</option>
                            @foreach(($categories ?? []) as $category)
                                <option value="{{ $category->value }}" @selected(old('category') === $category->value)>{{ $category->label() }}</option>
                            @endforeach
                        </select>
                        @error('category')
                            <p class="text-rose-600 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1.5">{{ __('Priority') }}</label>
                        <select name="priority" class="w-full rounded-lg border-slate-300 text-sm shadow-xs focus:border-emerald-500 focus:ring-emerald-500">
                            <option value="">{{ __('Auto (based on category)') }}</option>
                            @foreach(($priorities ?? []) as $priority)
                                <option value="{{ $priority->value }}" @selected(old('priority') === $priority->value)>{{ $priority->label() }}</option>
                            @endforeach
                        </select>
                        @error('priority')
                            <p class="text-rose-600 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1.5">{{ __('Customer') }}</label>
                        <select name="customer_id" class="w-full rounded-lg border-slate-300 text-sm shadow-xs focus:border-emerald-500 focus:ring-emerald-500">
                            <option value="">{{ __('Select Customer (optional)') }}</option>
                            @foreach(($customers ?? []) as $customer)
                                <option value="{{ $customer->id }}" @selected(old('customer_id') == $customer->id)>{{ $customer->full_name }} ({{ $customer->customer_number }})</option>
                            @endforeach
                        </select>
                        @error('customer_id')
                            <p class="text-rose-600 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1.5">{{ __('Account') }}</label>
                        <select name="account_id" class="w-full rounded-lg border-slate-300 text-sm shadow-xs focus:border-emerald-500 focus:ring-emerald-500">
                            <option value="">{{ __('Select Account (optional)') }}</option>
                            @foreach(($accounts ?? []) as $account)
                                <option value="{{ $account->id }}" @selected(old('account_id') == $account->id)>{{ $account->account_number }}</option>
                            @endforeach
                        </select>
                        @error('account_id')
                            <p class="text-rose-600 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-slate-700 mb-1.5">{{ __('Description') }} <span class="text-rose-500">*</span></label>
                        <textarea name="description" rows="5" required class="w-full rounded-lg border-slate-300 text-sm shadow-xs focus:border-emerald-500 focus:ring-emerald-500" placeholder="{{ __('Detailed description of the customer issue or request...') }}">{{ old('description') }}</textarea>
                        @error('description')
                            <p class="text-rose-600 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="bank-card-footer flex items-center justify-end gap-3">
                <a href="{{ route('customerService.index') }}" class="btn btn-secondary">
                    {{ __('Cancel') }}
                </a>
                <button type="submit" class="btn btn-primary">
                    {{ __('Submit Ticket') }}
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
