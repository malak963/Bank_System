<x-user.layout :title="__('Withdraw Money')">
    <div class="max-w-xl mx-auto">
        @php
            $accountsJson = $accounts->mapWithKeys(fn($a) => [(string)$a->id => (float)$a->balance])->toJson();
        @endphp

        <form action="{{ route('portal.withdraw.store') }}" method="POST"
              x-data="{
                  selectedMethod: 'atm',
                  selectedAccount: '{{ old('account_id', $selectedAccountId) }}',
                  amount: '{{ old('amount', '') }}',
                  balances: {{ $accountsJson }},
                  get currentBalance() {
                      return this.balances[this.selectedAccount] || 0;
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

            <!-- Source Account -->
            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1.5">
                    {{ __('Source Account') }} <span class="text-rose-500">*</span>
                </label>
                <select name="account_id" x-model="selectedAccount" required class="w-full rounded-lg border-slate-300 text-sm">
                    @foreach($accounts as $acc)
                        <option value="{{ $acc->id }}">
                            {{ $acc->accountType->name ?? __('Account') }} - {{ $acc->account_number }} ({{ number_format((float) $acc->balance, 2) }} {{ $acc->currency }})
                        </option>
                    @endforeach
                </select>
                @error('account_id')
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
                    @foreach([50, 100, 200, 500, 1000] as $preset)
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

            <!-- Method -->
            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1.5">
                    {{ __('Withdrawal Method') }} <span class="text-rose-500">*</span>
                </label>
                <select name="method" x-model="selectedMethod" class="w-full rounded-lg border-slate-300 text-sm">
                    <option value="atm">{{ __('ATM Cashout') }}</option>
                    <option value="branch_pickup">{{ __('Branch Counter Pickup') }}</option>
                    <option value="wire_transfer">{{ __('Bank Wire') }}</option>
                </select>
            </div>

            <!-- Note -->
            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1.5">
                    {{ __('Note (Optional)') }}
                </label>
                <input type="text" name="description" placeholder="{{ __('e.g. Cash withdrawal') }}"
                       class="w-full rounded-lg border-slate-300 text-sm">
            </div>

            <!-- Submit -->
            <div class="pt-3 border-t border-slate-100 flex items-center justify-between">
                <span class="text-xs text-slate-400">{{ __('Fee') }}: 0.00 ({{ __('Free') }})</span>
                <button type="submit"
                        :disabled="isOverBalance"
                        class="px-5 py-2.5 rounded-lg text-xs font-semibold bg-emerald-600 hover:bg-emerald-500 disabled:bg-slate-300 text-white transition">
                    {{ __('Confirm Withdrawal') }}
                </button>
            </div>
        </form>
    </div>
</x-user.layout>
