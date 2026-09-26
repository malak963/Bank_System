<x-user.layout :title="__('Transaction Receipt')">
    <x-slot:subtitle>
        {{ __('Official transaction execution voucher and digital audit proof.') }}
    </x-slot:subtitle>

    <div class="max-w-xl mx-auto space-y-4">
        <!-- Actions Strip -->
        <div class="flex items-center justify-between no-print text-xs">
            <a href="{{ route('portal.transactions') }}"
               class="font-semibold text-slate-600 hover:text-slate-900 transition">
                &larr; {{ __('Back to Transactions') }}
            </a>

            <button type="button" onclick="window.print()"
                    class="px-3.5 py-1.5 rounded-lg font-semibold bg-slate-900 hover:bg-slate-800 text-white transition">
                {{ __('Print Voucher') }}
            </button>
        </div>

        <!-- The Printable Receipt Card -->
        <div class="bg-white rounded-xl border border-slate-200 p-6 sm:p-8 space-y-6 print:border-none print:shadow-none print:p-0">
            <!-- Bank Top Banner -->
            <div class="flex items-start justify-between pb-4 border-b border-slate-100">
                <div class="flex items-center gap-2.5">
                    <span class="w-8 h-8 rounded-lg bg-emerald-600 text-white font-bold text-sm flex items-center justify-center">
                        M
                    </span>
                    <div>
                        <h2 class="text-base font-bold text-slate-900 tracking-tight">{{ config('user-nav.brand.name', 'MDAD Bank') }}</h2>
                        <p class="text-[11px] text-slate-400 font-medium">{{ __('Electronic Transaction Voucher') }}</p>
                    </div>
                </div>

                <div class="text-end">
                    <x-user.badge :status="$transaction->status" />
                    <span class="text-[10px] text-slate-400 block mt-1 font-mono uppercase">{{ __('Authorized') }}</span>
                </div>
            </div>

            <!-- Amount Section -->
            <div class="py-5 text-center border-b border-slate-100 bg-slate-50 rounded-lg">
                <span class="text-xs uppercase font-semibold text-slate-400 tracking-wider block">{{ __('Total Transaction Amount') }}</span>
                <div class="text-3xl sm:text-4xl font-bold font-mono text-slate-900 mt-1">
                    {{ $transaction->isCredit() ? '+' : '-' }}{{ number_format((float)$transaction->amount, 2) }}
                    <span class="text-sm font-semibold text-slate-500">{{ $transaction->currency }}</span>
                </div>
                <p class="text-xs text-slate-500 mt-1">{{ $transaction->description }}</p>
            </div>

            <!-- Breakdown Key Values -->
            <div class="space-y-2.5 text-xs text-slate-600">
                <div class="flex items-center justify-between py-1.5 border-b border-slate-100">
                    <span class="text-slate-400">{{ __('Transaction Reference') }}</span>
                    <span class="font-mono font-semibold text-slate-900">{{ $transaction->transaction_reference }}</span>
                </div>

                <div class="flex items-center justify-between py-1.5 border-b border-slate-100">
                    <span class="text-slate-400">{{ __('Execution Date & Time') }}</span>
                    <span class="font-mono text-slate-800">{{ $transaction->created_at->format('Y-m-d H:i:s') }}</span>
                </div>

                <div class="flex items-center justify-between py-1.5 border-b border-slate-100">
                    <span class="text-slate-400">{{ __('Operation') }}</span>
                    <span class="font-semibold text-slate-900 capitalize">{{ __(str_replace('_', ' ', $transaction->transaction_type?->value ?? '')) }}</span>
                </div>

                <div class="flex items-center justify-between py-1.5 border-b border-slate-100">
                    <span class="text-slate-400">{{ __('Account') }}</span>
                    <span class="font-mono font-semibold text-slate-900">{{ $transaction->account?->account_number }}</span>
                </div>

                @if($transaction->account?->iban)
                    <div class="flex items-center justify-between py-1.5 border-b border-slate-100">
                        <span class="text-slate-400">{{ __('IBAN') }}</span>
                        <span class="font-mono text-slate-700">{{ $transaction->account->iban }}</span>
                    </div>
                @endif

                <div class="flex items-center justify-between py-1.5 border-b border-slate-100">
                    <span class="text-slate-400">{{ __('Customer') }}</span>
                    <span class="font-semibold text-slate-800">{{ $transaction->account?->customer?->full_name ?? Auth::user()->name }}</span>
                </div>

                <div class="flex items-center justify-between py-1.5 border-b border-slate-100">
                    <span class="text-slate-400">{{ __('Balance After') }}</span>
                    <span class="font-mono font-semibold text-slate-900">{{ number_format((float)$transaction->balance_after, 2) }} {{ $transaction->currency }}</span>
                </div>

                <div class="flex items-center justify-between py-1.5">
                    <span class="text-slate-400">{{ __('Fee') }}</span>
                    <span class="font-semibold text-emerald-700">0.00 {{ $transaction->currency }} ({{ __('Free') }})</span>
                </div>
            </div>

            <!-- Digital Security Stamp Footer -->
            <div class="pt-4 border-t border-slate-100 flex items-center justify-between text-[10px]">
                <div>
                    <span class="font-semibold text-slate-400 uppercase tracking-wider block">{{ __('Digital Stamp') }}</span>
                    <span class="font-mono text-slate-400">
                        SHA256: {{ substr(hash('sha256', $transaction->transaction_reference . $transaction->amount), 0, 24) }}...
                    </span>
                </div>
                <div class="text-end">
                    <span class="text-slate-400 block">{{ config('user-nav.brand.name', 'MDAD Bank') }}</span>
                    <span class="text-emerald-700 font-semibold">&check; {{ __('Certified Authentic') }}</span>
                </div>
            </div>
        </div>
    </div>
</x-user.layout>
