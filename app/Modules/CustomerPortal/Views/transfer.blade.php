<x-user.layout :title="__('Transfer Money')">
    <div class="max-w-xl mx-auto">
        @php
            $accountsJson = $accounts->mapWithKeys(fn($a) => [(string)$a->id => (float)$a->balance])->toJson();
        @endphp

        <form action="{{ route('portal.transfer.store') }}" method="POST"
              x-data="{
                  transferMode: 'own_account',
                  fromAccount: '{{ old('from_account_id', $selectedFromAccountId) }}',
                  toAccount: '{{ old('to_account_id', ($accounts->count() > 1 ? $accounts[1]->id : '')) }}',
                  amount: '{{ old('amount', '') }}',
                  balances: {{ $accountsJson }},
                  get currentBalance() {
                      return this.balances[this.fromAccount] || 0;
                  },
                  get isOverBalance() {
                      let amt = parseFloat(this.amount) || 0;
                      return amt > this.currentBalance;
                  },
                  setAmount(val) { this.amount = val; },
                  setMax() { this.amount = this.currentBalance; }
              }"
              class="bg-white rounded-xl border border-slate-200 p-6 space-y-5">
            @csrf

            <!-- Mode Switcher -->
            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1.5">
                    {{ __('Transfer Type') }}
                </label>
                <div class="grid grid-cols-2 gap-2 p-1 rounded-lg bg-slate-100 text-xs font-medium">
                    <button type="button" @click="transferMode = 'own_account'"
                            class="py-1.5 rounded-md transition text-center"
                            :class="transferMode === 'own_account' ? 'bg-white text-slate-900 font-semibold shadow-xs' : 'text-slate-600 hover:text-slate-900'">
                        {{ __('Between My Accounts') }}
                    </button>
                    <button type="button" @click="transferMode = 'other_account'"
                            class="py-1.5 rounded-md transition text-center"
                            :class="transferMode === 'other_account' ? 'bg-white text-slate-900 font-semibold shadow-xs' : 'text-slate-600 hover:text-slate-900'">
                        {{ __('To Another Client') }}
                    </button>
                </div>
                <input type="hidden" name="transfer_mode" :value="transferMode">
            </div>

            <!-- Source Account -->
            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1.5">
                    {{ __('From Account') }} <span class="text-rose-500">*</span>
                </label>
                <select name="from_account_id" x-model="fromAccount" required class="w-full rounded-lg border-slate-300 text-sm">
                    @foreach($accounts as $acc)
                        <option value="{{ $acc->id }}">
                            {{ $acc->accountType->name ?? __('Account') }} - {{ $acc->account_number }} ({{ number_format((float) $acc->balance, 2) }} {{ $acc->currency }})
                        </option>
                    @endforeach
                </select>
                @error('from_account_id')
                    <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Destination Account (Own Account) -->
            <div x-show="transferMode === 'own_account'">
                <label class="block text-xs font-semibold text-slate-700 mb-1.5">
                    {{ __('To Account') }} <span class="text-rose-500">*</span>
                </label>
                @if($accounts->count() <= 1)
                    <p class="text-xs text-slate-500 p-3 rounded-lg bg-slate-50 border border-slate-200">
                        {{ __('You have only one account. Use "To Another Client" or open a new account from the dashboard.') }}
                    </p>
                @else
                    <select name="to_account_id" x-model="toAccount" class="w-full rounded-lg border-slate-300 text-sm">
                        @foreach($accounts as $acc)
                            <option value="{{ $acc->id }}" x-show="fromAccount != '{{ $acc->id }}'">
                                {{ $acc->accountType->name ?? __('Account') }} - {{ $acc->account_number }}
                            </option>
                        @endforeach
                    </select>
                @endif
                @error('to_account_id')
                    <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Destination Account (Other Account) -->
            <div x-show="transferMode === 'other_account'" style="display: none;" class="space-y-3">
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1.5">
                        {{ __('Recipient Account Number or IBAN') }} <span class="text-rose-500">*</span>
                    </label>
                    <input type="text" name="recipient_identifier" placeholder="{{ __('e.g. 202600010000 or IBAN') }}"
                           class="w-full rounded-lg border-slate-300 text-sm font-mono">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1.5">
                        {{ __('Recipient Name (Optional)') }}
                    </label>
                    <input type="text" name="recipient_name" placeholder="{{ __('Name') }}"
                           class="w-full rounded-lg border-slate-300 text-sm">
                </div>
                @error('recipient_identifier')
                    <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Amount -->
            <div>
                <div class="flex items-center justify-between mb-1.5">
                    <label class="block text-xs font-semibold text-slate-700">
                        {{ __('Amount') }} <span class="text-rose-500">*</span>
                    </label>
                    <button type="button" @click="setMax()" class="text-xs text-emerald-700 hover:underline font-medium">
                        {{ __('Max') }} (<span x-text="currentBalance.toFixed(2)"></span>)
                    </button>
                </div>

                <input type="number" step="0.01" min="1" max="500000" name="amount" x-model="amount" required
                       placeholder="0.00"
                       class="w-full rounded-lg border-slate-300 text-lg font-bold font-mono text-slate-900 py-2.5"
                       :class="{ 'border-rose-400': isOverBalance }">

                <div x-show="isOverBalance" style="display: none;" class="mt-1 text-xs text-rose-600 font-medium">
                    {{ __('Amount exceeds available balance') }}
                </div>

                <!-- Quick Pick -->
                <div class="flex flex-wrap items-center gap-1.5 mt-2">
                    @foreach([50, 100, 250, 500, 1000] as $preset)
                        <button type="button" @click="setAmount('{{ $preset }}')"
                                class="px-2.5 py-1 rounded text-xs bg-slate-100 hover:bg-slate-200 text-slate-700 font-medium transition">
                            {{ number_format($preset) }}
                        </button>
                    @endforeach
                </div>
                @error('amount')
                    <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Description -->
            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1.5">
                    {{ __('Note (Optional)') }}
                </label>
                <input type="text" name="description" placeholder="{{ __('e.g. Rent, payment') }}"
                       class="w-full rounded-lg border-slate-300 text-sm">
            </div>

            <!-- Submit -->
            <div class="pt-3 border-t border-slate-100 flex items-center justify-between">
                <span class="text-xs text-slate-400">{{ __('Fee') }}: 0.00 ({{ __('Free') }})</span>
                <button type="submit"
                        :disabled="isOverBalance"
                        class="px-5 py-2.5 rounded-lg text-xs font-semibold bg-emerald-600 hover:bg-emerald-500 disabled:bg-slate-300 text-white transition">
                    {{ __('Transfer Now') }}
                </button>
            </div>
        </form>
    </div>
</x-user.layout>
