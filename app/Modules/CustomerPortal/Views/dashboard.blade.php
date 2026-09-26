<x-user.layout :title="__('Dashboard')">
    <x-slot:actions>
        <a href="{{ route('portal.deposit') }}"
           class="inline-flex items-center px-3.5 py-2 rounded-lg text-xs font-semibold bg-emerald-600 hover:bg-emerald-500 text-white transition">
            {{ __('Deposit') }}
        </a>
        <a href="{{ route('portal.transfer') }}"
           class="inline-flex items-center px-3.5 py-2 rounded-lg text-xs font-semibold bg-white text-slate-700 hover:bg-slate-50 border border-slate-300 transition">
            {{ __('Transfer') }}
        </a>
    </x-slot:actions>

    <div class="space-y-6">
        <!-- Metric Summary -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <x-user.stat-card
                :title="__('Total Balance')"
                :value="number_format($total_balance, 2) . ' ' . $currency"
                :hint="__(':count Accounts', ['count' => $accounts_count])"
            />
            <x-user.stat-card
                :title="__('Total Deposits')"
                :value="number_format($total_deposits, 2) . ' ' . $currency"
            />
            <x-user.stat-card
                :title="__('Total Withdrawals')"
                :value="number_format($total_withdrawals, 2) . ' ' . $currency"
            />
            <x-user.stat-card
                :title="__('Pending Invoices')"
                :value="number_format($pending_bills_amount, 2) . ' ' . $currency"
                :hint="__(':count Pending', ['count' => $pending_bills_count])"
            />
        </div>

        <!-- My Accounts -->
        <div class="space-y-3">
            <div class="flex items-center justify-between">
                <h2 class="text-sm font-bold text-slate-900 uppercase tracking-wider">{{ __('Accounts') }}</h2>
                <div x-data="{ openModal: false }">
                    <button type="button" @click="openModal = true" class="text-xs text-emerald-700 hover:underline font-semibold">
                        + {{ __('Open New Account') }}
                    </button>

                    <!-- Simple Modal -->
                    <div x-show="openModal" style="display: none;" class="fixed inset-0 z-50 bg-slate-900/40 flex items-center justify-center p-4">
                        <div @click.outside="openModal = false" class="bg-white rounded-xl max-w-sm w-full p-5 text-start shadow-xl border border-slate-200">
                            <h3 class="text-sm font-bold text-slate-900 mb-3">{{ __('Open New Account') }}</h3>
                            <form action="{{ route('portal.accounts.create') }}" method="POST" class="space-y-3">
                                @csrf
                                <div>
                                    <label class="block text-xs text-slate-600 mb-1">{{ __('Account Type') }}</label>
                                    <select name="account_type_id" required class="w-full rounded-lg border-slate-300 text-xs">
                                        @foreach($availableAccountTypes as $at)
                                            <option value="{{ $at->id }}">{{ $at->name }} ({{ $at->code }})</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-xs text-slate-600 mb-1">{{ __('Currency') }}</label>
                                    <select name="currency" required class="w-full rounded-lg border-slate-300 text-xs">
                                        <option value="USD">USD</option>
                                        <option value="EUR">EUR</option>
                                        <option value="SYP">SYP</option>
                                        <option value="AED">AED</option>
                                        <option value="SAR">SAR</option>
                                    </select>
                                </div>
                                <div class="pt-2 flex justify-end gap-2">
                                    <button type="button" @click="openModal = false" class="px-3 py-1.5 text-xs text-slate-600 hover:bg-slate-100 rounded-lg">
                                        {{ __('Cancel') }}
                                    </button>
                                    <button type="submit" class="px-4 py-1.5 text-xs bg-emerald-600 text-white font-semibold rounded-lg hover:bg-emerald-500">
                                        {{ __('Create') }}
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach($accounts as $acc)
                    <x-user.account-card :account="$acc" />
                @endforeach
            </div>
        </div>

        <!-- Recent Transactions -->
        <div class="bg-white rounded-xl border border-slate-200 overflow-hidden">
            <div class="px-5 py-4 border-b border-slate-100 flex items-center justify-between">
                <h3 class="text-sm font-bold text-slate-900 uppercase tracking-wider">{{ __('Recent Activity') }}</h3>
                <a href="{{ route('portal.transactions') }}" class="text-xs text-emerald-700 hover:underline font-semibold">
                    {{ __('View All') }}
                </a>
            </div>

            @if($recent_transactions->isEmpty())
                <div class="p-8 text-center text-xs text-slate-500">
                    {{ __('No recent activity.') }}
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full text-xs text-start">
                        <thead class="bg-slate-50 text-slate-500 font-semibold border-b border-slate-100">
                            <tr>
                                <th class="py-2.5 px-4 text-start">{{ __('Description') }}</th>
                                <th class="py-2.5 px-4 text-start">{{ __('Account') }}</th>
                                <th class="py-2.5 px-4 text-end">{{ __('Amount') }}</th>
                                <th class="py-2.5 px-4 text-center">{{ __('Status') }}</th>
                                <th class="py-2.5 px-4 text-end">{{ __('Date') }}</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @foreach($recent_transactions as $txn)
                                @php
                                    $isCredit = $txn->isCredit();
                                @endphp
                                <tr class="hover:bg-slate-50/50">
                                    <td class="py-3 px-4">
                                        <div class="font-medium text-slate-900">{{ $txn->description }}</div>
                                        <div class="text-[10px] text-slate-400 font-mono">{{ $txn->transaction_reference }}</div>
                                    </td>
                                    <td class="py-3 px-4 text-slate-600 font-mono">
                                        {{ $txn->account?->account_number }}
                                    </td>
                                    <td class="py-3 px-4 text-end font-mono font-semibold {{ $isCredit ? 'text-emerald-700' : 'text-slate-900' }}">
                                        {{ $isCredit ? '+' : '-' }}{{ number_format((float) $txn->amount, 2) }} {{ $txn->currency }}
                                    </td>
                                    <td class="py-3 px-4 text-center">
                                        <x-user.badge :status="$txn->status" />
                                    </td>
                                    <td class="py-3 px-4 text-end text-slate-500 whitespace-nowrap">
                                        {{ $txn->created_at->format('Y-m-d') }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
</x-user.layout>
