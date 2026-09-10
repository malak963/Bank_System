<x-app-layout>
    <x-slot name="header"><h2 class="text-2xl font-semibold text-slate-950">{{ __('New Account Type') }}</h2></x-slot>
    <div class="mx-auto max-w-3xl px-4 py-8 sm:px-6 lg:px-8">
        <form method="POST" action="{{ route('account-types.store') }}" class="rounded-lg border border-slate-200 bg-white p-6 shadow-sm">
            @csrf
            @include('account_types::partials.form', ['accountType' => null, 'submitLabel' => __('Create Account Type')])
        </form>
    </div>
</x-app-layout>
