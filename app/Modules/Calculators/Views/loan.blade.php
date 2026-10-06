@extends('layouts.app')

@section('title', __('Loan Calculator'))

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('calculators.index') }}">{{ __('Banking Calculators') }}</a></li>
    <li class="breadcrumb-item active">{{ __('Loan Calculator') }}</li>
@endsection

@section('actions')
    <a href="{{ route('calculators.index') }}" class="btn btn-secondary">
        <svg class="w-4 h-4 rtl:rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
        </svg>
        <span>{{ __('Back') }}</span>
    </a>
@endsection

@section('content')
<div class="max-w-6xl mx-auto space-y-6">
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        {{-- Input Form --}}
        <div class="bank-card p-6">
            <h2 class="text-lg font-bold text-slate-900 dark:text-white mb-4">{{ __('Loan Details') }}</h2>
            <form id="loanCalculator" class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1.5">{{ __('Loan Amount') }}</label>
                    <input type="number" id="amount" name="amount" min="1000" max="10000000" step="100" required
                           class="w-full border-slate-300 dark:border-slate-700 dark:bg-slate-900 dark:text-white rounded-lg shadow-sm focus:ring-emerald-500 focus:border-emerald-500"
                           placeholder="{{ __('Enter loan amount') }}"
                           value="{{ old('amount', request('amount', '')) }}">
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1.5">{{ __('Annual Interest Rate (%)') }}</label>
                    <input type="number" id="interest_rate" name="interest_rate" min="0.1" max="30" step="0.1" required
                           class="w-full border-slate-300 dark:border-slate-700 dark:bg-slate-900 dark:text-white rounded-lg shadow-sm focus:ring-emerald-500 focus:border-emerald-500"
                           placeholder="{{ __('Enter annual interest rate') }}"
                           value="{{ old('interest_rate', request('interest_rate', '')) }}">
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1.5">{{ __('Term (months)') }}</label>
                    <input type="number" id="term_months" name="term_months" min="1" max="360" required
                           class="w-full border-slate-300 dark:border-slate-700 dark:bg-slate-900 dark:text-white rounded-lg shadow-sm focus:ring-emerald-500 focus:border-emerald-500"
                           placeholder="{{ __('Enter loan term in months') }}"
                           value="{{ old('term_months', request('term_months', '')) }}">
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1.5">{{ __('Interest Method') }}</label>
                    <select id="method" name="method"
                            class="w-full border-slate-300 dark:border-slate-700 dark:bg-slate-900 dark:text-white rounded-lg shadow-sm focus:ring-emerald-500 focus:border-emerald-500">
                        <option value="reducing_balance" {{ request('method', 'reducing_balance') === 'reducing_balance' ? 'selected' : '' }}>{{ __('Reducing Balance') }}</option>
                        <option value="flat_rate" {{ request('method') === 'flat_rate' ? 'selected' : '' }}>{{ __('Flat Rate') }}</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1.5">{{ __('Payment Frequency') }}</label>
                    <select id="frequency" name="frequency"
                            class="w-full border-slate-300 dark:border-slate-700 dark:bg-slate-900 dark:text-white rounded-lg shadow-sm focus:ring-emerald-500 focus:border-emerald-500">
                        <option value="monthly" {{ request('frequency', 'monthly') === 'monthly' ? 'selected' : '' }}>{{ __('Monthly') }}</option>
                        <option value="quarterly" {{ request('frequency') === 'quarterly' ? 'selected' : '' }}>{{ __('Quarterly') }}</option>
                    </select>
                </div>

                <button type="submit" id="calculateBtn" class="btn btn-primary w-full justify-center mt-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                    </svg>
                    <span>{{ __('Calculate') }}</span>
                </button>
            </form>
        </div>

        {{-- Results --}}
        <div class="bank-card p-6">
            <h2 class="text-lg font-bold text-slate-900 dark:text-white mb-4">{{ __('Calculation Results') }}</h2>
            <div id="results">
                <div class="text-center text-slate-400 py-12 {{ isset($result) ? 'hidden' : '' }}" id="results-placeholder">
                    <svg class="w-12 h-12 mx-auto mb-3 text-slate-300 dark:text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                    </svg>
                    <p class="text-sm">{{ __('Enter loan details and click calculate to see results') }}</p>
                </div>

                <div id="results-content" class="{{ isset($result) ? '' : 'hidden' }} space-y-4">
                    @if(isset($result))
                    <div class="space-y-3">
                        <div class="rounded-xl bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-800 p-4">
                            <div class="text-xs font-semibold uppercase tracking-wider text-emerald-700 dark:text-emerald-400">
                                {{ ($result['frequency'] ?? '') === 'quarterly' ? __('Quarterly') : __('Monthly') }} {{ __('Payment') }}
                            </div>
                            <div class="text-2xl font-bold text-emerald-800 dark:text-emerald-300 mt-1 font-mono" dir="ltr">
                                {{ number_format($result['monthlyPayment'] ?? 0, 2) }}
                            </div>
                        </div>
                        <div class="grid grid-cols-2 gap-3">
                            <div class="rounded-xl bg-slate-50 dark:bg-slate-800/60 border border-slate-200 dark:border-slate-700 p-4">
                                <div class="text-xs font-medium text-slate-500 dark:text-slate-400">{{ __('Total Repayment') }}</div>
                                <div class="text-base font-bold text-slate-900 dark:text-white mt-1 font-mono" dir="ltr">
                                    {{ number_format($result['totalAmount'] ?? 0, 2) }}
                                </div>
                            </div>
                            <div class="rounded-xl bg-slate-50 dark:bg-slate-800/60 border border-slate-200 dark:border-slate-700 p-4">
                                <div class="text-xs font-medium text-slate-500 dark:text-slate-400">{{ __('Total Interest') }}</div>
                                <div class="text-base font-bold text-red-600 dark:text-red-400 mt-1 font-mono" dir="ltr">
                                    {{ number_format($result['totalInterest'] ?? 0, 2) }}
                                </div>
                            </div>
                            <div class="rounded-xl bg-slate-50 dark:bg-slate-800/60 border border-slate-200 dark:border-slate-700 p-4">
                                <div class="text-xs font-medium text-slate-500 dark:text-slate-400">{{ __('Principal') }}</div>
                                <div class="text-base font-bold text-slate-900 dark:text-white mt-1 font-mono" dir="ltr">
                                    {{ number_format($result['principal'] ?? 0, 2) }}
                                </div>
                            </div>
                            <div class="rounded-xl bg-slate-50 dark:bg-slate-800/60 border border-slate-200 dark:border-slate-700 p-4">
                                <div class="text-xs font-medium text-slate-500 dark:text-slate-400">{{ __('Effective Rate') }}</div>
                                <div class="text-base font-bold text-slate-900 dark:text-white mt-1 font-mono" dir="ltr">
                                    {{ number_format($result['effectiveRate'] ?? 0, 2) }}%
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif
                </div>

                <div id="results-error" class="hidden rounded-lg bg-red-50 dark:bg-red-950/40 border border-red-200 dark:border-red-800 p-4 text-sm text-red-700 dark:text-red-300"></div>
            </div>
        </div>
    </div>

    {{-- Amortization Schedule --}}
    <div id="schedule-section" class="{{ (isset($result) && !empty($result['schedule'])) ? '' : 'hidden' }} bank-card">
        <div class="bank-card-header flex items-center justify-between p-4 border-b border-slate-200 dark:border-slate-700">
            <h3 class="text-sm font-semibold text-slate-900 dark:text-white">{{ __('Amortization Schedule') }}</h3>
            <span id="schedule-count" class="text-xs text-slate-500 dark:text-slate-400">
                @if(isset($result['schedule']))
                    {{ count($result['schedule']) }} {{ __('payments') }}
                @endif
            </span>
        </div>
        <div class="overflow-x-auto max-h-96">
            <table class="w-full text-xs">
                <thead class="bg-slate-50 dark:bg-slate-800 sticky top-0 border-b border-slate-200 dark:border-slate-700">
                    <tr>
                        <th class="px-4 py-3 text-start font-semibold text-slate-600 dark:text-slate-300">#</th>
                        <th class="px-4 py-3 text-end font-semibold text-slate-600 dark:text-slate-300">{{ __('Payment') }}</th>
                        <th class="px-4 py-3 text-end font-semibold text-slate-600 dark:text-slate-300">{{ __('Principal') }}</th>
                        <th class="px-4 py-3 text-end font-semibold text-slate-600 dark:text-slate-300">{{ __('Interest') }}</th>
                        <th class="px-4 py-3 text-end font-semibold text-slate-600 dark:text-slate-300">{{ __('Balance') }}</th>
                    </tr>
                </thead>
                <tbody id="schedule-body" class="divide-y divide-slate-100 dark:divide-slate-800">
                    @if(isset($result['schedule']))
                        @foreach($result['schedule'] as $row)
                            <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/50">
                                <td class="px-4 py-2 font-mono text-slate-600 dark:text-slate-400">{{ $row['paymentNumber'] }}</td>
                                <td class="px-4 py-2 text-end font-mono text-slate-900 dark:text-white" dir="ltr">{{ number_format($row['paymentAmount'], 2) }}</td>
                                <td class="px-4 py-2 text-end font-mono text-emerald-600 dark:text-emerald-400" dir="ltr">{{ number_format($row['principalComponent'], 2) }}</td>
                                <td class="px-4 py-2 text-end font-mono text-red-600 dark:text-red-400" dir="ltr">{{ number_format($row['interestComponent'], 2) }}</td>
                                <td class="px-4 py-2 text-end font-mono text-slate-700 dark:text-slate-300" dir="ltr">{{ number_format($row['remainingBalance'], 2) }}</td>
                            </tr>
                        @endforeach
                    @endif
                </tbody>
            </table>
        </div>
    </div>
</div>

@push('scripts')
<script>
(function () {
    const fmt = (n) => Number(n).toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2});

    const form = document.getElementById('loanCalculator');

    form.addEventListener('submit', function(e) {
        e.preventDefault();
        doCalculate();
    });

    function doCalculate() {
        const formData = new FormData(form);

        // Show loading state
        const placeholder = document.getElementById('results-placeholder');
        const errEl = document.getElementById('results-error');
        const content = document.getElementById('results-content');

        if (placeholder) placeholder.classList.add('hidden');
        if (errEl) errEl.classList.add('hidden');
        if (content) {
            content.innerHTML = '<div class="text-center py-8 text-slate-400 text-sm">{{ __("Calculating...") }}</div>';
            content.classList.remove('hidden');
        }

        fetch('{{ route('calculators.loan.calculate') }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json',
            },
            body: formData
        })
        .then(response => {
            if (!response.ok) {
                return response.json().then(err => { throw err; });
            }
            return response.json();
        })
        .then(data => {
            if (data.success && data.data) {
                displayResults(data.data);
            } else {
                showError(data.message || '{{ __("Calculation failed. Please check your inputs.") }}');
            }
        })
        .catch(error => {
            console.error('Calculation error:', error);
            let message = '{{ __("An unexpected error occurred. Please try again.") }}';
            if (error && error.errors) {
                message = Object.values(error.errors).flat().join('<br>');
            } else if (error && error.message) {
                message = error.message;
            }
            showError(message);
        });
    }

    function showError(msg) {
        const content = document.getElementById('results-content');
        const placeholder = document.getElementById('results-placeholder');
        const errEl = document.getElementById('results-error');
        const scheduleSec = document.getElementById('schedule-section');

        if (content) content.classList.add('hidden');
        if (placeholder) placeholder.classList.add('hidden');
        if (errEl) {
            errEl.innerHTML = msg;
            errEl.classList.remove('hidden');
        }
        if (scheduleSec) scheduleSec.classList.add('hidden');
    }

    function displayResults(data) {
        const content = document.getElementById('results-content');
        const payment = data.monthlyPayment ?? data.monthly_payment ?? 0;
        const totalAmt = data.totalAmount ?? data.total_payment ?? 0;
        const totalInt = data.totalInterest ?? data.total_interest ?? 0;
        const principal = data.principal ?? data.total_principal ?? 0;
        const effRate   = data.effectiveRate ?? data.effective_rate ?? 0;
        const frequencyLabel = data.frequency === 'quarterly' ? '{{ __("Quarterly") }}' : '{{ __("Monthly") }}';

        content.innerHTML = `
            <div class="space-y-3">
                <div class="rounded-xl bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-800 p-4">
                    <div class="text-xs font-semibold uppercase tracking-wider text-emerald-700 dark:text-emerald-400">${frequencyLabel} {{ __('Payment') }}</div>
                    <div class="text-2xl font-bold text-emerald-800 dark:text-emerald-300 mt-1 font-mono" dir="ltr">${fmt(payment)}</div>
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div class="rounded-xl bg-slate-50 dark:bg-slate-800/60 border border-slate-200 dark:border-slate-700 p-4">
                        <div class="text-xs font-medium text-slate-500 dark:text-slate-400">{{ __('Total Repayment') }}</div>
                        <div class="text-base font-bold text-slate-900 dark:text-white mt-1 font-mono" dir="ltr">${fmt(totalAmt)}</div>
                    </div>
                    <div class="rounded-xl bg-slate-50 dark:bg-slate-800/60 border border-slate-200 dark:border-slate-700 p-4">
                        <div class="text-xs font-medium text-slate-500 dark:text-slate-400">{{ __('Total Interest') }}</div>
                        <div class="text-base font-bold text-red-600 dark:text-red-400 mt-1 font-mono" dir="ltr">${fmt(totalInt)}</div>
                    </div>
                    <div class="rounded-xl bg-slate-50 dark:bg-slate-800/60 border border-slate-200 dark:border-slate-700 p-4">
                        <div class="text-xs font-medium text-slate-500 dark:text-slate-400">{{ __('Principal') }}</div>
                        <div class="text-base font-bold text-slate-900 dark:text-white mt-1 font-mono" dir="ltr">${fmt(principal)}</div>
                    </div>
                    <div class="rounded-xl bg-slate-50 dark:bg-slate-800/60 border border-slate-200 dark:border-slate-700 p-4">
                        <div class="text-xs font-medium text-slate-500 dark:text-slate-400">{{ __('Effective Rate') }}</div>
                        <div class="text-base font-bold text-slate-900 dark:text-white mt-1 font-mono" dir="ltr">${fmt(effRate)}%</div>
                    </div>
                </div>
            </div>
        `;
        content.classList.remove('hidden');
        document.getElementById('results-placeholder').classList.add('hidden');
        document.getElementById('results-error').classList.add('hidden');

        // Amortization schedule
        const scheduleSec = document.getElementById('schedule-section');
        if (data.schedule && data.schedule.length > 0) {
            const tbody = document.getElementById('schedule-body');
            tbody.innerHTML = data.schedule.map(row => `
                <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/50">
                    <td class="px-4 py-2 font-mono text-slate-600 dark:text-slate-400">${row.paymentNumber}</td>
                    <td class="px-4 py-2 text-end font-mono text-slate-900 dark:text-white" dir="ltr">${fmt(row.paymentAmount)}</td>
                    <td class="px-4 py-2 text-end font-mono text-emerald-600 dark:text-emerald-400" dir="ltr">${fmt(row.principalComponent)}</td>
                    <td class="px-4 py-2 text-end font-mono text-red-600 dark:text-red-400" dir="ltr">${fmt(row.interestComponent)}</td>
                    <td class="px-4 py-2 text-end font-mono text-slate-700 dark:text-slate-300" dir="ltr">${fmt(row.remainingBalance)}</td>
                </tr>
            `).join('');
            document.getElementById('schedule-count').textContent = `${data.schedule.length} {{ __('payments') }}`;
            scheduleSec.classList.remove('hidden');
        } else {
            scheduleSec.classList.add('hidden');
        }
    }
})();
</script>
@endpush
@endsection
