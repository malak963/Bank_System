<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <p class="text-xs font-semibold uppercase text-emerald-700">{{ __('Credit Operations') }}</p>
                <h2 class="mt-1 text-2xl font-semibold text-slate-950">{{ __('Loans') }}</h2>
            </div>
            <a href="{{ route('loans.create') }}" class="inline-flex items-center gap-2 rounded-lg bg-slate-950 px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-slate-800 transition">
                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M12 5v14M5 12h14" /></svg>
                <span>{{ __('New Loan Application') }}</span>
            </a>
        </div>
    </x-slot>

    @php $filterValue = fn (string $key): string => (string) ($filters[$key] ?? ''); @endphp

    <div class="mx-auto max-w-7xl space-y-6 px-4 py-8 sm:px-6 lg:px-8">
        @if (session('status'))
            <div class="rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-800">{{ session('status') }}</div>
        @endif
        @if ($errors->any())
            <div class="rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">{{ $errors->first() }}</div>
        @endif

        <div class="grid grid-cols-2 gap-4 lg:grid-cols-5">
            <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
                <p class="text-xs font-semibold uppercase text-slate-500">{{ __('Applications') }}</p>
                <p class="mt-2 text-3xl font-semibold text-slate-950 font-mono" dir="ltr">{{ number_format($summary['total']) }}</p>
            </div>
            <div class="rounded-xl border border-amber-200 bg-amber-50 p-5 shadow-sm">
                <p class="text-xs font-semibold uppercase text-amber-700">{{ __('Pending') }}</p>
                <p class="mt-2 text-3xl font-semibold text-amber-950 font-mono" dir="ltr">{{ number_format($summary['pending']) }}</p>
            </div>
            <div class="rounded-xl border border-sky-200 bg-sky-50 p-5 shadow-sm">
                <p class="text-xs font-semibold uppercase text-sky-700">{{ __('Active') }}</p>
                <p class="mt-2 text-3xl font-semibold text-sky-950 font-mono" dir="ltr">{{ number_format($summary['active']) }}</p>
            </div>
            <div class="rounded-xl border border-emerald-200 bg-emerald-50 p-5 shadow-sm">
                <p class="text-xs font-semibold uppercase text-emerald-700">{{ __('Paid Off') }}</p>
                <p class="mt-2 text-3xl font-semibold text-emerald-950 font-mono" dir="ltr">{{ number_format($summary['paid_off']) }}</p>
            </div>
            <div class="rounded-xl border border-red-200 bg-red-50 p-5 shadow-sm">
                <p class="text-xs font-semibold uppercase text-red-700">{{ __('Outstanding Principal') }}</p>
                <p class="mt-2 text-xl font-semibold text-red-950 font-mono" dir="ltr">{{ number_format($summary['outstanding'], 2) }}</p>
            </div>
        </div>

        <form method="GET" action="{{ route('loans.index') }}" class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
            <div class="grid grid-cols-1 gap-4 lg:grid-cols-12">
                <div class="lg:col-span-5">
                    <x-input-label for="search" :value="__('Search')" />
                    <x-text-input id="search" name="search" type="search" class="mt-1 block w-full" :value="$filterValue('search')" placeholder="{{ __('Loan reference or customer') }}" />
                </div>
                <div class="lg:col-span-3">
                    <x-input-label for="loan_type_id" :value="__('Loan Type')" />
                    <select id="loan_type_id" name="loan_type_id" class="mt-1 block w-full rounded-lg border-slate-300 bg-white text-sm shadow-sm focus:border-emerald-500 focus:ring-emerald-500">
                        <option value="">{{ __('Any') }}</option>
                        @foreach ($loanTypes as $loanType)
                            <option value="{{ $loanType->id }}" @selected($filterValue('loan_type_id') === (string) $loanType->id)>{{ $loanType->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="lg:col-span-2">
                    <x-input-label for="status" :value="__('Status')" />
                    <select id="status" name="status" class="mt-1 block w-full rounded-lg border-slate-300 bg-white text-sm shadow-sm focus:border-emerald-500 focus:ring-emerald-500">
                        <option value="">{{ __('Any') }}</option>
                        @foreach ($statuses as $status)
                            <option value="{{ $status->value }}" @selected($filterValue('status') === $status->value)>{{ $status->label() }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="flex items-end gap-2 lg:col-span-2">
                    <button type="submit" class="inline-flex min-h-10 flex-1 items-center justify-center rounded-lg bg-slate-950 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-slate-800 transition">{{ __('Filter') }}</button>
                    <a href="{{ route('loans.index') }}" class="inline-flex min-h-10 items-center justify-center rounded-lg border border-slate-200 px-3 py-2 text-sm font-semibold text-slate-700 hover:border-emerald-300 transition">{{ __('Reset') }}</a>
                </div>
            </div>
        </form>

        <div class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm">
            <div class="flex items-center justify-between border-b border-slate-200 px-5 py-4">
                <div>
                    <h3 class="font-semibold text-slate-950">{{ __('Loan Portfolio') }}</h3>
                    <p class="text-sm text-slate-500">{{ __('Applications, disbursement status and outstanding exposure') }}</p>
                </div>
                <span class="text-sm text-slate-500 font-mono" dir="ltr">{{ $loans->firstItem() ?? 0 }}-{{ $loans->lastItem() ?? 0 }} / {{ $loans->total() }}</span>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-200">
                    <thead class="bg-slate-50">
                        <tr>
                            <th class="px-5 py-3 text-start text-xs font-semibold uppercase text-slate-500">{{ __('Reference') }}</th>
                            <th class="px-5 py-3 text-start text-xs font-semibold uppercase text-slate-500">{{ __('Customer') }}</th>
                            <th class="px-5 py-3 text-start text-xs font-semibold uppercase text-slate-500">{{ __('Product') }}</th>
                            <th class="px-5 py-3 text-start text-xs font-semibold uppercase text-slate-500">{{ __('Amount') }}</th>
                            <th class="px-5 py-3 text-start text-xs font-semibold uppercase text-slate-500">{{ __('Next Due') }}</th>
                            <th class="px-5 py-3 text-start text-xs font-semibold uppercase text-slate-500">{{ __('Status') }}</th>
                            <th class="px-5 py-3 text-end text-xs font-semibold uppercase text-slate-500">{{ __('Actions') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($loans as $loan)
                            @php
                                $statusClass = match ($loan->status->value) {
                                    'active', 'disbursed' => 'bg-sky-50 text-sky-700 ring-sky-200',
                                    'approved' => 'bg-emerald-50 text-emerald-700 ring-emerald-200',
                                    'rejected', 'defaulted' => 'bg-red-50 text-red-700 ring-red-200',
                                    'paid_off' => 'bg-green-50 text-green-700 ring-green-200',
                                    default => 'bg-amber-50 text-amber-700 ring-amber-200'
                                };
                            @endphp
                            <tr class="hover:bg-slate-50/80 transition">
                                <td class="px-5 py-4 text-sm">
                                    <a href="{{ route('loans.show', $loan) }}" class="font-mono font-semibold text-slate-950 hover:text-emerald-700" dir="ltr">
                                        {{ $loan->loan_reference }}
                                    </a>
                                    <p class="text-xs text-slate-500 font-mono" dir="ltr">{{ $loan->application_date?->toDateString() }}</p>
                                </td>
                                <td class="px-5 py-4 text-sm">
                                    <p class="font-semibold text-slate-900">{{ $loan->customer?->full_name }}</p>
                                    <p class="text-xs text-slate-500 font-mono" dir="ltr">{{ $loan->customer?->customer_number }}</p>
                                </td>
                                <td class="px-5 py-4 text-sm text-slate-700">
                                    <p class="font-medium">{{ $loan->loanType?->name }}</p>
                                    <p class="text-xs text-slate-500"><span dir="ltr" class="font-mono">{{ $loan->annual_interest_rate }}%</span> / {{ $loan->interest_method?->label() }}</p>
                                </td>
                                <td class="px-5 py-4 text-sm font-semibold text-slate-900">
                                    <p dir="ltr" class="font-mono">{{ number_format((float) ($loan->approved_amount ?? $loan->requested_amount), 2) }}</p>
                                    <p class="text-xs font-normal text-slate-500">{{ __('Outstanding') }}: <span dir="ltr" class="font-mono">{{ number_format((float) $loan->outstanding_principal, 2) }}</span></p>
                                </td>
                                <td class="px-5 py-4 text-sm text-slate-700 font-mono" dir="ltr">
                                    {{ $loan->next_payment_date?->toDateString() ?? __('Not scheduled') }}
                                </td>
                                <td class="px-5 py-4 text-sm">
                                    <span class="rounded-full px-2.5 py-1 text-xs font-semibold ring-1 ring-inset {{ $statusClass }}">
                                        {{ $loan->status->label() }}
                                    </span>
                                </td>
                                <td class="px-5 py-4 text-end text-sm">
                                    <a href="{{ route('loans.show', $loan) }}" class="font-semibold text-emerald-700 hover:text-emerald-900">{{ __('Manage') }}</a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-14 text-center text-sm text-slate-500">
                                    <p class="font-medium">{{ __('No loan applications found.') }}</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        {{ $loans->links() }}
    </div>
</x-app-layout>
