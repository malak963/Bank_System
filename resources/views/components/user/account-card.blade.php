@props([
    'account',
])

@php
    $balance = number_format((float) ($account->balance ?? 0), 2);
    $currency = $account->currency ?? 'USD';
    $accNumber = $account->account_number ?? '0000000000';
    $iban = $account->iban ?? '';
    $accountTypeName = $account->accountType->name ?? __('Current Account');
@endphp

<div x-data="{ copied: false }" class="rounded-xl bg-white p-5 border border-slate-200 flex flex-col justify-between">
    <div>
        <div class="flex items-center justify-between">
            <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider">
                {{ $accountTypeName }}
            </span>
            <span class="text-xs px-2 py-0.5 rounded-full bg-emerald-50 text-emerald-700 font-medium border border-emerald-100">
                {{ __('Active') }}
            </span>
        </div>

        <div class="mt-4">
            <span class="text-xs text-slate-400 block">{{ __('Available Balance') }}</span>
            <div class="text-2xl font-bold font-mono text-slate-900 mt-0.5">
                {{ $balance }} <span class="text-xs font-semibold text-slate-500">{{ $currency }}</span>
            </div>
        </div>
    </div>

    <div class="mt-6 pt-4 border-t border-slate-100 flex items-center justify-between text-xs">
        <div>
            <span class="text-slate-400 font-mono">{{ $accNumber }}</span>
            @if($iban)
                <button type="button"
                        @click="navigator.clipboard.writeText('{{ $iban }}'); copied = true; setTimeout(() => copied = false, 2000)"
                        class="ms-2 text-emerald-700 hover:underline">
                    <span x-show="!copied">{{ __('Copy IBAN') }}</span>
                    <span x-show="copied" style="display: none;" class="font-bold">{{ __('Copied!') }}</span>
                </button>
            @endif
        </div>

        <div class="flex items-center gap-1.5">
            <a href="{{ route('portal.deposit', ['account_id' => $account->id]) }}"
               class="px-2.5 py-1 rounded bg-slate-100 hover:bg-slate-200 text-slate-700 font-medium transition">
                {{ __('Deposit') }}
            </a>
            <a href="{{ route('portal.transfer', ['from_account_id' => $account->id]) }}"
               class="px-2.5 py-1 rounded bg-slate-100 hover:bg-slate-200 text-slate-700 font-medium transition">
                {{ __('Transfer') }}
            </a>
        </div>
    </div>
</div>
