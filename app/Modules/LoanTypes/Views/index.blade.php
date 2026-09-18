<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <p class="text-xs font-semibold uppercase text-emerald-700">{{ __('Loan Configuration') }}</p>
                <h2 class="mt-1 text-2xl font-semibold text-slate-950">{{ __('Loan Types') }}</h2>
            </div>
            <a href="{{ route('loan-types.create') }}" class="inline-flex items-center gap-2 rounded-lg bg-slate-950 px-4 py-2.5 text-sm font-semibold text-white hover:bg-slate-800 transition">
                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M12 5v14M5 12h14" /></svg>
                <span>{{ __('New Loan Type') }}</span>
            </a>
        </div>
    </x-slot>

    <div class="mx-auto max-w-7xl space-y-6 px-4 py-8 sm:px-6 lg:px-8">
        @if (session('status'))
            <div class="rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-800">{{ session('status') }}</div>
        @endif
        @if ($errors->any())
            <div class="rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">{{ $errors->first() }}</div>
        @endif

        <div class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-200">
                    <thead class="bg-slate-50">
                        <tr>
                            <th class="px-5 py-3 text-start text-xs font-semibold uppercase text-slate-500">{{ __('Code') }}</th>
                            <th class="px-5 py-3 text-start text-xs font-semibold uppercase text-slate-500">{{ __('Product') }}</th>
                            <th class="px-5 py-3 text-start text-xs font-semibold uppercase text-slate-500">{{ __('Limits') }}</th>
                            <th class="px-5 py-3 text-start text-xs font-semibold uppercase text-slate-500">{{ __('Pricing') }}</th>
                            <th class="px-5 py-3 text-start text-xs font-semibold uppercase text-slate-500">{{ __('Loans') }}</th>
                            <th class="px-5 py-3 text-start text-xs font-semibold uppercase text-slate-500">{{ __('Status') }}</th>
                            <th class="px-5 py-3 text-end text-xs font-semibold uppercase text-slate-500">{{ __('Actions') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($loanTypes as $loanType)
                            <tr class="hover:bg-slate-50/80 transition">
                                <td class="px-5 py-4 text-sm font-mono font-bold text-slate-950" dir="ltr">{{ $loanType->code }}</td>
                                <td class="px-5 py-4 text-sm">
                                    <p class="font-semibold text-slate-900">{{ $loanType->name }}</p>
                                    <p class="text-xs text-slate-500">{{ $loanType->description }}</p>
                                </td>
                                <td class="px-5 py-4 text-sm text-slate-700">
                                    <p><span dir="ltr" class="font-mono">{{ number_format((float) $loanType->minimum_amount, 2) }}</span> - <span dir="ltr" class="font-mono">{{ number_format((float) $loanType->maximum_amount, 2) }}</span> {{ $loanType->currency }}</p>
                                    <p class="text-xs text-slate-500"><span dir="ltr" class="font-mono">{{ $loanType->minimum_term_months }}-{{ $loanType->maximum_term_months }}</span> {{ __('months') }}</p>
                                </td>
                                <td class="px-5 py-4 text-sm text-slate-700">
                                    <p><span dir="ltr" class="font-mono">{{ $loanType->annual_interest_rate }}%</span> / {{ $loanType->interest_method->label() }}</p>
                                    <p class="text-xs text-slate-500">{{ $loanType->repayment_frequency->label() }}</p>
                                </td>
                                <td class="px-5 py-4 text-sm text-slate-600 font-mono" dir="ltr">{{ $loanType->loans_count }}</td>
                                <td class="px-5 py-4 text-sm">
                                    <span class="rounded-full px-2.5 py-1 text-xs font-semibold ring-1 ring-inset {{ $loanType->status->value === 'active' ? 'bg-emerald-50 text-emerald-700 ring-emerald-200' : 'bg-slate-100 text-slate-600 ring-slate-200' }}">
                                        {{ $loanType->status->label() }}
                                    </span>
                                </td>
                                <td class="px-5 py-4 text-end text-sm">
                                    <a href="{{ route('loan-types.edit', $loanType) }}" class="font-semibold text-emerald-700 hover:text-emerald-900">{{ __('Edit') }}</a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-12 text-center text-sm text-slate-500">
                                    <p class="font-medium">{{ __('No loan types found.') }}</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        {{ $loanTypes->links() }}
    </div>
</x-app-layout>
