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
<div class="max-w-5xl mx-auto">
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="card p-6">
            <h2 class="text-lg font-bold text-slate-900 dark:text-white mb-4">{{ __('Loan Details') }}</h2>
            <form id="loanCalculator">
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1.5">{{ __('Loan Amount') }}</label>
                        <input type="number" id="amount" name="amount" min="1000" max="10000000" step="100" required
                               class="w-full border-slate-300 dark:border-slate-700 dark:bg-slate-800 dark:text-white rounded-lg shadow-sm focus:ring-emerald-500 focus:border-emerald-500"
                               placeholder="{{ __('Enter loan amount') }}">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1.5">{{ __('Annual Interest Rate (%)') }}</label>
                        <input type="number" id="interest_rate" name="interest_rate" min="0.1" max="30" step="0.1" required
                               class="w-full border-slate-300 dark:border-slate-700 dark:bg-slate-800 dark:text-white rounded-lg shadow-sm focus:ring-emerald-500 focus:border-emerald-500"
                               placeholder="{{ __('Enter annual interest rate') }}">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1.5">{{ __('Term (months)') }}</label>
                        <input type="number" id="term_months" name="term_months" min="1" max="360" required
                               class="w-full border-slate-300 dark:border-slate-700 dark:bg-slate-800 dark:text-white rounded-lg shadow-sm focus:ring-emerald-500 focus:border-emerald-500"
                               placeholder="{{ __('Enter loan term in months') }}">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1.5">{{ __('Interest Method') }}</label>
                        <select id="method" name="method" 
                                class="w-full border-slate-300 dark:border-slate-700 dark:bg-slate-800 dark:text-white rounded-lg shadow-sm focus:ring-emerald-500 focus:border-emerald-500">
                            <option value="reducing_balance">{{ __('Reducing Balance') }}</option>
                            <option value="flat_rate">{{ __('Flat Rate') }}</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1.5">{{ __('Payment Frequency') }}</label>
                        <select id="frequency" name="frequency" 
                                class="w-full border-slate-300 dark:border-slate-700 dark:bg-slate-800 dark:text-white rounded-lg shadow-sm focus:ring-emerald-500 focus:border-emerald-500">
                            <option value="monthly">{{ __('Monthly') }}</option>
                            <option value="quarterly">{{ __('Quarterly') }}</option>
                        </select>
                    </div>

                    <button type="submit" class="btn btn-primary w-full justify-center mt-2">
                        {{ __('Calculate') }}
                    </button>
                </div>
            </form>
        </div>

        <div class="card p-6">
            <h2 class="text-lg font-bold text-slate-900 dark:text-white mb-4">{{ __('Calculation Results') }}</h2>
            <div id="results" class="space-y-4">
                <div class="text-center text-slate-400 py-12">
                    <svg class="w-12 h-12 mx-auto mb-3 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                    </svg>
                    <p class="text-sm">{{ __('Enter loan details and click calculate to see results') }}</p>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.getElementById('loanCalculator').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    
    fetch('{{ route('calculators.loan.calculate') }}', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json',
        },
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            displayResults(data.data);
        }
    })
    .catch(error => {
        console.error('Error:', error);
    });
});

function displayResults(data) {
    const resultsDiv = document.getElementById('results');
    resultsDiv.innerHTML = `
        <div class="space-y-4">
            <div class="bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-800 rounded-xl p-4">
                <div class="text-xs font-semibold uppercase tracking-wider text-emerald-700 dark:text-emerald-400">{{ __('Monthly Payment') }}</div>
                <div class="text-2xl font-bold text-emerald-800 dark:text-emerald-300 mt-1 font-mono" dir="ltr">
                    ${data.monthly_payment.toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2})}
                </div>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div class="bg-slate-50 dark:bg-slate-800/50 border border-slate-100 dark:border-slate-800 rounded-xl p-4">
                    <div class="text-xs font-medium text-slate-500 dark:text-slate-400">{{ __('Total Payment') }}</div>
                    <div class="text-base font-bold text-slate-900 dark:text-white mt-1 font-mono" dir="ltr">
                        ${data.total_payment.toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2})}
                    </div>
                </div>
                <div class="bg-slate-50 dark:bg-slate-800/50 border border-slate-100 dark:border-slate-800 rounded-xl p-4">
                    <div class="text-xs font-medium text-slate-500 dark:text-slate-400">{{ __('Total Interest') }}</div>
                    <div class="text-base font-bold text-slate-900 dark:text-white mt-1 font-mono" dir="ltr">
                        ${data.total_interest.toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2})}
                    </div>
                </div>
            </div>
            <div class="bg-slate-50 dark:bg-slate-800/50 border border-slate-100 dark:border-slate-800 rounded-xl p-4">
                <div class="text-xs font-medium text-slate-500 dark:text-slate-400">{{ __('Total Principal') }}</div>
                <div class="text-base font-bold text-slate-900 dark:text-white mt-1 font-mono" dir="ltr">
                    ${data.total_principal.toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2})}
                </div>
            </div>
        </div>
    `;
}
</script>
@endpush
@endsection
