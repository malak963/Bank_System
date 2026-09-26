<x-user.layout :title="__('Transactions')">
    <x-slot:subtitle>
        {{ __('View your transaction history and download receipts.') }}
    </x-slot:subtitle>

    <div class="space-y-4">
        <!-- Filter Bar -->
        <form method="GET" action="{{ route('portal.transactions') }}"
              class="bg-white rounded-xl border border-slate-200 p-4 space-y-3">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3">
                <!-- Search -->
                <div class="lg:col-span-2">
                    <label class="block text-xs font-semibold text-slate-600 mb-1">{{ __('Search') }}</label>
                    <input type="text" name="search" value="{{ request('search') }}"
                           placeholder="{{ __('Search reference or description...') }}"
                           class="w-full rounded-lg border-slate-300 text-xs py-2">
                </div>

                <!-- Account -->
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1">{{ __('Account') }}</label>
                    <select name="account_id" class="w-full rounded-lg border-slate-300 text-xs py-2">
                        <option value="">{{ __('All Accounts') }}</option>
                        @foreach($accounts as $acc)
                            <option value="{{ $acc->id }}" {{ request('account_id') == $acc->id ? 'selected' : '' }}>
                                {{ $acc->account_number }} ({{ $acc->currency }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Type -->
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1">{{ __('Type') }}</label>
                    <select name="type" class="w-full rounded-lg border-slate-300 text-xs py-2">
                        <option value="">{{ __('All Types') }}</option>
                        @foreach($types as $t)
                            <option value="{{ $t->value }}" {{ request('type') == $t->value ? 'selected' : '' }}>
                                {{ method_exists($t, 'label') ? $t->label() : __($t->value) }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Status -->
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1">{{ __('Status') }}</label>
                    <select name="status" class="w-full rounded-lg border-slate-300 text-xs py-2">
                        <option value="">{{ __('All Statuses') }}</option>
                        @foreach($statuses as $s)
                            <option value="{{ $s->value }}" {{ request('status') == $s->value ? 'selected' : '' }}>
                                {{ __($s->value) }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <!-- Date Range & Action Buttons -->
            <div class="flex flex-col sm:flex-row items-center justify-between gap-3 pt-3 border-t border-slate-100 text-xs">
                <div class="flex items-center gap-2 w-full sm:w-auto">
                    <span class="text-slate-500 font-medium">{{ __('From') }}</span>
                    <input type="date" name="date_from" value="{{ request('date_from') }}" class="rounded-lg border-slate-300 text-xs py-1.5 px-2">
                    <span class="text-slate-500 font-medium">{{ __('To') }}</span>
                    <input type="date" name="date_to" value="{{ request('date_to') }}" class="rounded-lg border-slate-300 text-xs py-1.5 px-2">
                </div>

                <div class="flex items-center gap-2 w-full sm:w-auto justify-end">
                    <a href="{{ route('portal.transactions') }}"
                       class="px-3.5 py-1.5 rounded-lg text-xs font-medium text-slate-600 hover:bg-slate-100 border border-slate-200 transition">
                        {{ __('Reset') }}
                    </a>
                    <button type="submit"
                            class="px-4 py-1.5 rounded-lg text-xs font-semibold bg-emerald-600 hover:bg-emerald-500 text-white transition">
                        {{ __('Filter') }}
                    </button>
                </div>
            </div>
        </form>

        <!-- Transactions Ledger Table -->
        <div class="bg-white rounded-xl border border-slate-200 overflow-hidden">
            @if($transactions->isEmpty())
                <div class="p-10 text-center">
                    <h4 class="text-sm font-semibold text-slate-800">{{ __('No transactions match your search filters') }}</h4>
                    <p class="text-xs text-slate-500 mt-1 max-w-sm mx-auto">
                        {{ __('Try adjusting your date range or filter criteria.') }}
                    </p>
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full text-start text-xs">
                        <thead class="bg-slate-50 border-b border-slate-100 text-slate-500 font-semibold">
                            <tr>
                                <th class="py-2.5 px-4 text-start">{{ __('Description') }}</th>
                                <th class="py-2.5 px-4 text-start">{{ __('Type') }}</th>
                                <th class="py-2.5 px-4 text-start">{{ __('Account') }}</th>
                                <th class="py-2.5 px-4 text-end">{{ __('Amount') }}</th>
                                <th class="py-2.5 px-4 text-end">{{ __('Balance After') }}</th>
                                <th class="py-2.5 px-4 text-center">{{ __('Status') }}</th>
                                <th class="py-2.5 px-4 text-end">{{ __('Date') }}</th>
                                <th class="py-2.5 px-4 text-end">{{ __('Action') }}</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @foreach($transactions as $txn)
                                @php
                                    $isCredit = $txn->isCredit();
                                    $typeVal = $txn->transaction_type?->value ?? ($txn->transaction_type ?? '');
                                @endphp
                                <tr class="hover:bg-slate-50/50 transition-colors">
                                    <td class="py-3 px-4">
                                        <div class="font-medium text-slate-900">{{ $txn->description }}</div>
                                        <div class="text-[10px] font-mono text-slate-400">{{ $txn->transaction_reference }}</div>
                                    </td>
                                    <td class="py-3 px-4 text-slate-700 capitalize">
                                        {{ __(str_replace('_', ' ', $typeVal)) }}
                                    </td>
                                    <td class="py-3 px-4 font-mono text-slate-600">
                                        {{ $txn->account?->account_number ?? '-' }}
                                    </td>
                                    <td class="py-3 px-4 text-end font-mono font-semibold {{ $isCredit ? 'text-emerald-700' : 'text-slate-900' }}">
                                        {{ $isCredit ? '+' : '-' }}{{ number_format((float) $txn->amount, 2) }} {{ $txn->currency }}
                                    </td>
                                    <td class="py-3 px-4 text-end font-mono text-slate-600">
                                        {{ number_format((float) ($txn->balance_after ?? 0), 2) }} {{ $txn->currency }}
                                    </td>
                                    <td class="py-3 px-4 text-center">
                                        <x-user.badge :status="$txn->status" />
                                    </td>
                                    <td class="py-3 px-4 text-end text-slate-500 whitespace-nowrap">
                                        {{ $txn->created_at->format('Y-m-d') }}
                                    </td>
                                    <td class="py-3 px-4 text-end">
                                        <a href="{{ route('portal.transactions.receipt', $txn->id) }}"
                                           class="text-xs font-semibold text-emerald-700 hover:underline">
                                            {{ __('Receipt') }}
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="p-3 border-t border-slate-100">
                    {{ $transactions->links() }}
                </div>
            @endif
        </div>
    </div>
</x-user.layout>
