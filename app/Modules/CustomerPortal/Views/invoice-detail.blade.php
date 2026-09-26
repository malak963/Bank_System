<x-user.layout :title="__('Invoice Details')">
    <x-slot:subtitle>
        {{ __('Review invoice breakdown and process immediate electronic payment.') }}
    </x-slot:subtitle>

    @php
        $isPaid = $bill->status === \App\Modules\BillsPayments\Enums\BillStatus::COMPLETED;
        $isOverdue = !$isPaid && $bill->due_date && $bill->due_date < now();
    @endphp

    <div class="max-w-2xl mx-auto space-y-4">
        <!-- Top Navigation -->
        <div class="flex items-center justify-between no-print text-xs">
            <a href="{{ route('portal.invoices') }}"
               class="font-semibold text-slate-600 hover:text-slate-900 transition">
                &larr; {{ __('Back to Invoices') }}
            </a>

            @if($isPaid && $bill->transaction_id)
                <a href="{{ route('portal.transactions.receipt', $bill->transaction_id) }}"
                   class="font-semibold text-emerald-700 hover:underline">
                    {{ __('View Receipt') }}
                </a>
            @endif
        </div>

        <div class="bg-white rounded-xl border border-slate-200 p-6 space-y-6">
            <!-- Header -->
            <div class="flex items-start justify-between pb-4 border-b border-slate-100">
                <div>
                    <h2 class="text-base font-bold text-slate-900">{{ $bill->provider_name }}</h2>
                    <span class="text-xs font-mono text-slate-400 block mt-0.5">{{ $bill->bill_reference }}</span>
                </div>
                <x-user.badge :status="$bill->status" />
            </div>

            <!-- Total Amount Card -->
            <div class="p-4 rounded-lg bg-slate-50 border border-slate-100 flex items-center justify-between">
                <div>
                    <span class="text-xs text-slate-400 uppercase tracking-wider block">{{ __('Invoice Total Due') }}</span>
                    <div class="text-2xl sm:text-3xl font-bold font-mono text-slate-900 mt-0.5">
                        {{ number_format((float) $bill->amount, 2) }}
                        <span class="text-sm font-semibold text-slate-500">{{ $bill->currency }}</span>
                    </div>
                </div>
                <div class="text-end">
                    <span class="text-xs text-slate-400 uppercase tracking-wider block">{{ __('Due Date') }}</span>
                    <span class="text-xs font-semibold font-mono {{ $isOverdue ? 'text-rose-600 font-bold' : 'text-slate-700' }}">
                        {{ $bill->due_date ? $bill->due_date->format('Y-m-d') : '-' }}
                    </span>
                    @if($isOverdue)
                        <span class="text-[10px] text-rose-600 font-bold block">{{ __('Overdue') }}</span>
                    @endif
                </div>
            </div>

            <!-- Details List -->
            <div class="space-y-2.5 text-xs text-slate-700">
                <div class="flex items-center justify-between py-2 border-b border-slate-100">
                    <span class="text-slate-500">{{ __('Category') }}</span>
                    <span class="font-medium capitalize">{{ __(str_replace('_', ' ', $bill->bill_type?->value ?? ($bill->bill_type ?? ''))) }}</span>
                </div>
                @if($bill->provider_account_number)
                    <div class="flex items-center justify-between py-2 border-b border-slate-100">
                        <span class="text-slate-500">{{ __('Subscriber Account / Meter #') }}</span>
                        <span class="font-mono font-medium">{{ $bill->provider_account_number }}</span>
                    </div>
                @endif
                @if($bill->description)
                    <div class="py-2 border-b border-slate-100">
                        <span class="text-slate-500 block mb-1">{{ __('Description') }}</span>
                        <p class="text-slate-800">{{ $bill->description }}</p>
                    </div>
                @endif
                @if($isPaid)
                    <div class="flex items-center justify-between py-2 border-b border-slate-100">
                        <span class="text-slate-500">{{ __('Settlement Date') }}</span>
                        <span class="font-medium text-emerald-700">{{ $bill->paid_at ? $bill->paid_at->format('Y-m-d H:i') : '-' }}</span>
                    </div>
                    @if($bill->transaction)
                        <div class="flex items-center justify-between py-2 border-b border-slate-100">
                            <span class="text-slate-500">{{ __('Payment Reference') }}</span>
                            <span class="font-mono text-slate-900">{{ $bill->transaction->transaction_reference }}</span>
                        </div>
                    @endif
                @endif
            </div>

            <!-- Pay Action Form if Not Paid -->
            @if(!$isPaid)
                <div class="pt-4 border-t border-slate-100">
                    <h3 class="text-xs font-bold text-slate-900 uppercase tracking-wider mb-3">{{ __('Settle This Invoice') }}</h3>
                    <form action="{{ route('portal.invoices.pay', $bill->id) }}" method="POST" class="space-y-4">
                        @csrf
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">
                                {{ __('Select Source Bank Account') }} <span class="text-rose-500">*</span>
                            </label>
                            <select name="account_id" required class="w-full rounded-lg border-slate-300 text-xs">
                                @foreach($accounts as $acc)
                                    <option value="{{ $acc->id }}">
                                        {{ $acc->accountType->name ?? __('Account') }} - {{ $acc->account_number }} ({{ __('Available') }}: {{ number_format((float)$acc->balance, 2) }} {{ $acc->currency }})
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="flex items-center justify-between pt-2">
                            <span class="text-xs text-slate-400">{{ __('Fee') }}: 0.00 ({{ __('Free') }})</span>
                            <button type="submit"
                                    class="px-5 py-2 rounded-lg text-xs font-semibold bg-emerald-600 hover:bg-emerald-500 text-white transition">
                                {{ __('Pay Now') }} ({{ number_format((float)$bill->amount, 2) }} {{ $bill->currency }})
                            </button>
                        </div>
                    </form>
                </div>
            @endif
        </div>
    </div>
</x-user.layout>
