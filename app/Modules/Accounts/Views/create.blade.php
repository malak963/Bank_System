<x-app-layout>
    <x-slot name="header"><div><p class="text-xs font-semibold uppercase text-emerald-700">{{ __('Account Operations') }}</p><h2 class="mt-1 text-2xl font-semibold text-slate-950">{{ __('Open New Account') }}</h2></div></x-slot>
    <div class="mx-auto max-w-3xl px-4 py-8 sm:px-6 lg:px-8">
        @if ($errors->any())<div class="mb-6 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">{{ $errors->first() }}</div>@endif
        @if ($accountTypes->isEmpty())
            <div class="rounded-lg border border-amber-200 bg-amber-50 p-5 text-sm text-amber-900">{{ __('Create an active account type before opening an account.') }} <a href="{{ route('account-types.create') }}" class="font-semibold underline">{{ __('Create account type') }}</a></div>
        @else
            <form method="POST" action="{{ route('accounts.store') }}" class="space-y-6 rounded-lg border border-slate-200 bg-white p-6 shadow-sm">
                @csrf
                <div><x-input-label for="customer_id" :value="__('Customer')" /><select id="customer_id" name="customer_id" class="mt-1 block w-full rounded-lg border-slate-300 bg-white shadow-sm focus:border-emerald-500 focus:ring-emerald-500" required><option value="">{{ __('Select customer') }}</option>@foreach ($customers as $customer)<option value="{{ $customer->id }}" @selected(old('customer_id') == $customer->id)>{{ $customer->full_name }} - {{ $customer->customer_number }} ({{ $customer->user?->email }})</option>@endforeach</select><x-input-error :messages="$errors->get('customer_id')" class="mt-2" /></div>
                <div><x-input-label for="account_type_id" :value="__('Account Type')" /><select id="account_type_id" name="account_type_id" class="mt-1 block w-full rounded-lg border-slate-300 bg-white shadow-sm focus:border-emerald-500 focus:ring-emerald-500" required><option value="">{{ __('Select account type') }}</option>@foreach ($accountTypes as $accountType)<option value="{{ $accountType->id }}" @selected(old('account_type_id') == $accountType->id)>{{ $accountType->name }} ({{ $accountType->code }} / {{ $accountType->currency }})</option>@endforeach</select><x-input-error :messages="$errors->get('account_type_id')" class="mt-2" /></div>
                <div><x-input-label for="initial_deposit" :value="__('Initial Deposit')" /><x-text-input id="initial_deposit" name="initial_deposit" type="number" min="0" step="0.01" class="mt-1 block w-full" :value="old('initial_deposit', '0.00')" /><p class="mt-1 text-xs text-slate-500">{{ __('The account number and IBAN will be generated automatically.') }}</p><x-input-error :messages="$errors->get('initial_deposit')" class="mt-2" /></div>
                <div class="flex items-center justify-end gap-3"><a href="{{ route('accounts.index') }}" class="rounded-lg px-4 py-2 text-sm font-semibold text-slate-600 hover:bg-slate-100">{{ __('Cancel') }}</a><button type="submit" class="rounded-lg bg-slate-950 px-4 py-2.5 text-sm font-semibold text-white hover:bg-slate-800">{{ __('Create and Open Account') }}</button></div>
            </form>
        @endif
    </div>
</x-app-layout>
