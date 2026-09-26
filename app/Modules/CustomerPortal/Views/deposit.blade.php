<x-user.layout :title="__('Deposit Money')">
    <div class="max-w-xl mx-auto">
        <form action="{{ route('portal.deposit.store') }}" method="POST"
              x-data="{
                  selectedMethod: 'card',
                  amount: '{{ old('amount', '') }}',
                  setAmount(val) { this.amount = val; }
              }"
              class="bg-white rounded-xl border border-slate-200 p-6 space-y-5">
            @csrf

            <!-- Destination Account -->
            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1.5">
                    {{ __('Destination Account') }} <span class="text-rose-500">*</span>
                </label>
                <select name="account_id" required class="w-full rounded-lg border-slate-300 text-sm">
                    @foreach($accounts as $acc)
                        <option value="{{ $acc->id }}" {{ old('account_id', $selectedAccountId) == $acc->id ? 'selected' : '' }}>
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
                <label class="block text-xs font-semibold text-slate-700 mb-1.5">
                    {{ __('Amount') }} <span class="text-rose-500">*</span>
                </label>
                <div class="relative">
                    <input type="number" step="0.01" min="1" max="500000" name="amount" x-model="amount" required
                           placeholder="0.00"
                           class="w-full rounded-lg border-slate-300 text-lg font-bold font-mono text-slate-900 py-2.5">
                </div>

                <!-- Quick Pick -->
                <div class="flex flex-wrap items-center gap-1.5 mt-2">
                    @foreach([50, 100, 250, 500, 1000] as $preset)
                        <button type="button" @click="setAmount('{{ $preset }}')"
                                class="px-2.5 py-1 rounded text-xs bg-slate-100 hover:bg-slate-200 text-slate-700 font-medium transition">
                            +{{ number_format($preset) }}
                        </button>
                    @endforeach
                </div>
                @error('amount')
                    <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Payment Method -->
            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1.5">
                    {{ __('Payment Method') }} <span class="text-rose-500">*</span>
                </label>
                <select name="method" x-model="selectedMethod" class="w-full rounded-lg border-slate-300 text-sm">
                    <option value="card">{{ __('Credit / Debit Card') }}</option>
                    <option value="bank_wire">{{ __('Bank Wire Transfer') }}</option>
                    <option value="cash_pickup">{{ __('Cash / ATM Branch') }}</option>
                    <option value="online_gateway">{{ __('Online Gateway') }}</option>
                </select>
            </div>

            <!-- Card Inputs (only if card selected) -->
            <div x-show="selectedMethod === 'card'" class="space-y-3 pt-2 border-t border-slate-100">
                <div>
                    <label class="block text-xs text-slate-600 mb-1">{{ __('Card Number') }}</label>
                    <input type="text" name="card_number" maxlength="19" placeholder="4000 0000 0000 0000"
                           class="w-full rounded-lg border-slate-300 text-sm font-mono">
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs text-slate-600 mb-1">{{ __('Expiry') }}</label>
                        <input type="text" name="card_expiry" placeholder="MM/YY" maxlength="5"
                                class="w-full rounded-lg border-slate-300 text-sm font-mono">
                    </div>
                    <div>
                        <label class="block text-xs text-slate-600 mb-1">{{ __('CVV') }}</label>
                        <input type="password" maxlength="4" placeholder="123"
                               class="w-full rounded-lg border-slate-300 text-sm font-mono">
                    </div>
                </div>
            </div>

            <!-- Description / Note -->
            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1.5">
                    {{ __('Note (Optional)') }}
                </label>
                <input type="text" name="description" placeholder="{{ __('e.g. Deposit') }}"
                       class="w-full rounded-lg border-slate-300 text-sm">
            </div>

            <!-- Submit -->
            <div class="pt-3 border-t border-slate-100 flex items-center justify-between">
                <span class="text-xs text-slate-400">{{ __('Fee') }}: 0.00 ({{ __('Free') }})</span>
                <button type="submit"
                        class="px-5 py-2.5 rounded-lg text-xs font-semibold bg-emerald-600 hover:bg-emerald-500 text-white transition">
                    {{ __('Confirm Deposit') }}
                </button>
            </div>
        </form>
    </div>
</x-user.layout>
