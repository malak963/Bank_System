<x-user.layout :title="__('Invoices')">
    <x-slot:actions>
        <button type="button"
                @click="$dispatch('open-custom-modal')"
                class="px-3.5 py-2 rounded-lg text-xs font-semibold bg-emerald-600 hover:bg-emerald-500 text-white transition">
            + {{ __('Pay New Bill') }}
        </button>
    </x-slot:actions>

    <div x-data="{
        payModalOpen: false,
        customModalOpen: false,
        selectedBillId: null,
        selectedBillRef: '',
        selectedBillProvider: '',
        selectedBillAmount: '0.00',
        selectedBillCurrency: '',
        openPay(id, ref, provider, amount, currency) {
            this.selectedBillId = id;
            this.selectedBillRef = ref;
            this.selectedBillProvider = provider;
            this.selectedBillAmount = amount;
            this.selectedBillCurrency = currency;
            this.payModalOpen = true;
        }
    }"
    @open-custom-modal.window="customModalOpen = true"
    class="space-y-4">

        <!-- Filter Tabs -->
        <div class="flex items-center gap-1 border-b border-slate-200 pb-2 text-xs">
            <a href="{{ route('portal.invoices', ['status' => 'all']) }}"
               class="px-3 py-1.5 rounded-md font-medium transition {{ $currentFilter === 'all' ? 'bg-slate-200 text-slate-900 font-semibold' : 'text-slate-600 hover:text-slate-900' }}">
                {{ __('All') }} ({{ $bills->total() }})
            </a>
            <a href="{{ route('portal.invoices', ['status' => 'pending']) }}"
               class="px-3 py-1.5 rounded-md font-medium transition {{ $currentFilter === 'pending' ? 'bg-slate-200 text-slate-900 font-semibold' : 'text-slate-600 hover:text-slate-900' }}">
                {{ __('Pending') }} ({{ $stats['pending_count'] }})
            </a>
            <a href="{{ route('portal.invoices', ['status' => 'paid']) }}"
               class="px-3 py-1.5 rounded-md font-medium transition {{ $currentFilter === 'paid' ? 'bg-slate-200 text-slate-900 font-semibold' : 'text-slate-600 hover:text-slate-900' }}">
                {{ __('Paid') }} ({{ $stats['paid_count'] }})
            </a>
            <a href="{{ route('portal.invoices', ['status' => 'overdue']) }}"
               class="px-3 py-1.5 rounded-md font-medium transition {{ $currentFilter === 'overdue' ? 'bg-slate-200 text-slate-900 font-semibold' : 'text-slate-600 hover:text-slate-900' }}">
                {{ __('Overdue') }}
            </a>
        </div>

        <!-- Invoices Table -->
        <div class="bg-white rounded-xl border border-slate-200 overflow-hidden">
            @if($bills->isEmpty())
                <div class="p-8 text-center text-xs text-slate-500">
                    {{ __('No invoices found.') }}
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full text-xs text-start">
                        <thead class="bg-slate-50 text-slate-500 font-semibold border-b border-slate-100">
                            <tr>
                                <th class="py-2.5 px-4 text-start">{{ __('Biller / Description') }}</th>
                                <th class="py-2.5 px-4 text-start">{{ __('Reference') }}</th>
                                <th class="py-2.5 px-4 text-end">{{ __('Amount') }}</th>
                                <th class="py-2.5 px-4 text-start">{{ __('Due Date') }}</th>
                                <th class="py-2.5 px-4 text-center">{{ __('Status') }}</th>
                                <th class="py-2.5 px-4 text-end">{{ __('Action') }}</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @foreach($bills as $bill)
                                @php
                                    $isPaid = $bill->status === \App\Modules\BillsPayments\Enums\BillStatus::COMPLETED;
                                @endphp
                                <tr class="hover:bg-slate-50/50">
                                    <td class="py-3 px-4">
                                        <div class="font-medium text-slate-900">{{ $bill->provider_name }}</div>
                                        @if($bill->description)
                                            <div class="text-[11px] text-slate-400">{{ $bill->description }}</div>
                                        @endif
                                    </td>
                                    <td class="py-3 px-4 font-mono text-slate-600">
                                        {{ $bill->bill_reference }}
                                    </td>
                                    <td class="py-3 px-4 text-end font-mono font-semibold text-slate-900">
                                        {{ number_format((float) $bill->amount, 2) }} {{ $bill->currency }}
                                    </td>
                                    <td class="py-3 px-4 text-slate-600 font-mono">
                                        {{ $bill->due_date ? $bill->due_date->format('Y-m-d') : '-' }}
                                    </td>
                                    <td class="py-3 px-4 text-center">
                                        <x-user.badge :status="$bill->status" />
                                    </td>
                                    <td class="py-3 px-4 text-end">
                                        @if($isPaid)
                                            @if($bill->transaction_id)
                                                <a href="{{ route('portal.transactions.receipt', $bill->transaction_id) }}"
                                                   class="text-emerald-700 hover:underline font-semibold">
                                                    {{ __('Receipt') }}
                                                </a>
                                            @else
                                                <span class="text-slate-400">{{ __('Settled') }}</span>
                                            @endif
                                        @else
                                            <button type="button"
                                                    @click="openPay('{{ $bill->id }}', '{{ $bill->bill_reference }}', '{{ addslashes($bill->provider_name) }}', '{{ number_format((float)$bill->amount, 2) }}', '{{ $bill->currency }}')"
                                                    class="px-3 py-1 rounded bg-emerald-600 hover:bg-emerald-500 text-white font-medium transition">
                                                {{ __('Pay') }}
                                            </button>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="p-3 border-t border-slate-100">
                    {{ $bills->links() }}
                </div>
            @endif
        </div>

        <!-- Pay Existing Modal -->
        <div x-show="payModalOpen" style="display: none;" class="fixed inset-0 z-50 bg-slate-900/40 flex items-center justify-center p-4">
            <div @click.outside="payModalOpen = false" class="bg-white rounded-xl max-w-sm w-full p-5 text-start shadow-xl border border-slate-200 space-y-4">
                <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                    <h3 class="text-sm font-bold text-slate-900">{{ __('Pay Invoice') }}</h3>
                    <button type="button" @click="payModalOpen = false" class="text-slate-400 hover:text-slate-700">&times;</button>
                </div>

                <div class="text-xs text-slate-600 space-y-1">
                    <div><strong>{{ __('Biller') }}:</strong> <span x-text="selectedBillProvider"></span></div>
                    <div><strong>{{ __('Reference') }}:</strong> <span class="font-mono" x-text="selectedBillRef"></span></div>
                    <div><strong>{{ __('Amount') }}:</strong> <span class="font-mono font-bold text-slate-900" x-text="selectedBillAmount"></span> <span x-text="selectedBillCurrency"></span></div>
                </div>

                <form :action="'{{ route('portal.invoices') }}/' + selectedBillId + '/pay'" method="POST" class="space-y-3">
                    @csrf
                    <div>
                        <label class="block text-xs text-slate-600 mb-1">{{ __('Payment Account') }}</label>
                        <select name="account_id" required class="w-full rounded-lg border-slate-300 text-xs">
                            @foreach($accounts as $acc)
                                <option value="{{ $acc->id }}">
                                    {{ $acc->accountType->name ?? __('Account') }} - {{ $acc->account_number }} ({{ number_format((float)$acc->balance, 2) }} {{ $acc->currency }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="pt-2 flex justify-end gap-2">
                        <button type="button" @click="payModalOpen = false" class="px-3 py-1.5 text-xs text-slate-600 hover:bg-slate-100 rounded-lg">
                            {{ __('Cancel') }}
                        </button>
                        <button type="submit" class="px-4 py-1.5 text-xs bg-emerald-600 text-white font-semibold rounded-lg hover:bg-emerald-500">
                            {{ __('Confirm Payment') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Pay Custom Modal -->
        <div x-show="customModalOpen" style="display: none;" class="fixed inset-0 z-50 bg-slate-900/40 flex items-center justify-center p-4">
            <div @click.outside="customModalOpen = false" class="bg-white rounded-xl max-w-md w-full p-5 text-start shadow-xl border border-slate-200 space-y-4">
                <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                    <h3 class="text-sm font-bold text-slate-900">{{ __('Pay New Bill') }}</h3>
                    <button type="button" @click="customModalOpen = false" class="text-slate-400 hover:text-slate-700">&times;</button>
                </div>

                <form action="{{ route('portal.invoices.pay-custom') }}" method="POST" class="space-y-3 text-xs">
                    @csrf
                    <div>
                        <label class="block text-slate-600 mb-1">{{ __('Account') }}</label>
                        <select name="account_id" required class="w-full rounded-lg border-slate-300 text-xs">
                            @foreach($accounts as $acc)
                                <option value="{{ $acc->id }}">
                                    {{ $acc->accountType->name ?? __('Account') }} - {{ $acc->account_number }} ({{ number_format((float)$acc->balance, 2) }} {{ $acc->currency }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="grid grid-cols-2 gap-2">
                        <div>
                            <label class="block text-slate-600 mb-1">{{ __('Biller Type') }}</label>
                            <select name="bill_type" required class="w-full rounded-lg border-slate-300 text-xs">
                                @foreach($billTypes as $bt)
                                    <option value="{{ $bt->value }}">{{ $bt->getLabel() }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-slate-600 mb-1">{{ __('Provider Name') }}</label>
                            <input type="text" name="provider_name" required placeholder="{{ __('e.g. Electric Company') }}" class="w-full rounded-lg border-slate-300 text-xs">
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-2">
                        <div>
                            <label class="block text-slate-600 mb-1">{{ __('Invoice Number') }}</label>
                            <input type="text" name="bill_reference" required placeholder="INV-0000" class="w-full rounded-lg border-slate-300 text-xs font-mono">
                        </div>
                        <div>
                            <label class="block text-slate-600 mb-1">{{ __('Amount') }}</label>
                            <input type="number" step="0.01" min="0.5" name="amount" required placeholder="0.00" class="w-full rounded-lg border-slate-300 text-xs font-mono">
                        </div>
                    </div>

                    <div class="pt-2 flex justify-end gap-2">
                        <button type="button" @click="customModalOpen = false" class="px-3 py-1.5 text-slate-600 hover:bg-slate-100 rounded-lg">
                            {{ __('Cancel') }}
                        </button>
                        <button type="submit" class="px-4 py-1.5 bg-emerald-600 text-white font-semibold rounded-lg hover:bg-emerald-500">
                            {{ __('Pay Now') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>

    </div>
</x-user.layout>
