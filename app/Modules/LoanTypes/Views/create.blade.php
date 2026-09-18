<x-app-layout>
    <x-slot name="header"><h2 class="text-2xl font-semibold text-slate-950">{{ __('New Loan Type') }}</h2></x-slot>
    <div class="mx-auto max-w-4xl px-4 py-8 sm:px-6 lg:px-8"><form method="POST" action="{{ route('loan-types.store') }}" class="rounded-lg border border-slate-200 bg-white p-6 shadow-sm">@csrf@include('loan_types::partials.form', ['loanType' => null, 'submitLabel' => __('Create Loan Type')])</form></div>
</x-app-layout>
