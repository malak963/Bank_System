@extends('layouts.app')

@section('title', 'Loan Calculator')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-4xl mx-auto">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-3xl font-bold text-slate-800">{{ __('Loan Calculator') }}</h1>
            <a href="{{ route('calculators.index') }}" 
               class="text-slate-600 hover:text-slate-900">Back</a>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-xl font-semibold text-slate-800 mb-4">{{ __('Loan Details') }}</h2>
                <form id="loanCalculator">
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-2">Loan Amount</label>
                            <input type="number" id="amount" name="amount" min="1000" max="10000000" step="100" required
                                   class="w-full border-slate-300 rounded-lg shadow-sm focus:ring-emerald-500 focus:border-emerald-500"
                                   placeholder="{{ __('Enter loan amount') }}">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-2">{{ __('Interest Rate (%)') }}</label>
                            <input type="number" id="interest_rate" name="interest_rate" min="0.1" max="30" step="0.1" required
                                   class="w-full border-slate-300 rounded-lg shadow-sm focus:ring-emerald-500 focus:border-emerald-500"
                                   placeholder="{{ __('Enter annual interest rate') }}">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-2">{{ __('Loan Term (Months)') }}</label>
                            <input type="number" id="term_months" name="term_months" min="1" max="360" required
                                   class="w-full border-slate-300 rounded-lg shadow-sm focus:ring-emerald-500 focus:border-emerald-500"
                                   placeholder="{{ __('Enter loan term in months') }}">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-2">{{ __('Interest Method') }}</label>
                            <select id="method" name="method" 
                                    class="w-full border-slate-300 rounded-lg shadow-sm focus:ring-emerald-500 focus:border-emerald-500">
                                <option value="reducing_balance">{{ __('Reducing Balance') }}</option>
                                <option value="flat_rate">{{ __('Flat Rate') }}</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-2">Payment Frequency</label>
                            <select id="frequency" name="frequency" 
                                    class="w-full border-slate-300 rounded-lg shadow-sm focus:ring-emerald-500 focus:border-emerald-500">
                                <option value="monthly">Monthly</option>
                                <option value="quarterly">Quarterly</option>
                            </select>
                        </div>

                        <button type="submit" 
                                class="w-full bg-emerald-600 text-white px-4 py-2 rounded-lg hover:bg-emerald-700 transition">
                            Calculate
                        </button>
                    </div>
                </form>
            </div>

            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-xl font-semibold text-slate-800 mb-4">Calculation Results</h2>
                <div id="results" class="space-y-4">
                    <div class="text-center text-slate-500 py-8">
                        Enter loan details and click calculate to see results
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@section('scripts')
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
            <div class="bg-slate-50 rounded-lg p-4">
                <div class="text-sm text-slate-500">Monthly Payment</div>
                <div class="text-2xl font-bold text-emerald-600">
                    SYP ${data.monthly_payment.toLocaleString()}
                </div>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div class="bg-slate-50 rounded-lg p-4">
                    <div class="text-sm text-slate-500">Total Payment</div>
                    <div class="text-lg font-semibold text-slate-900">
                        SYP ${data.total_payment.toLocaleString()}
                    </div>
                </div>
                <div class="bg-slate-50 rounded-lg p-4">
                    <div class="text-sm text-slate-500">Total Interest</div>
                    <div class="text-lg font-semibold text-slate-900">
                        SYP ${data.total_interest.toLocaleString()}
                    </div>
                </div>
            </div>
            <div class="bg-slate-50 rounded-lg p-4">
                <div class="text-sm text-slate-500">Total Principal</div>
                <div class="text-lg font-semibold text-slate-900">
                    SYP ${data.total_principal.toLocaleString()}
                </div>
            </div>
        </div>
    `;
}
</script>
@endsection
