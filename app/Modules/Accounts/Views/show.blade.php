<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <p class="text-xs font-semibold uppercase text-emerald-700">{{ __('Account Operations') }}</p>
                <h2 class="mt-1 font-mono text-2xl font-semibold text-slate-950" dir="ltr">{{ $account->account_number }}</h2>
            </div>
            <a href="{{ route('accounts.index') }}" class="text-sm font-semibold text-slate-600 hover:text-emerald-700 inline-flex items-center gap-1">
                <span>&larr;</span>
                <span>{{ __('Back to Accounts') }}</span>
            </a>
        </div>
    </x-slot>

    @php
        $statusClass = match ($account->status->value) {
            'open' => 'bg-emerald-50 text-emerald-700 ring-emerald-200',
            'frozen' => 'bg-amber-50 text-amber-700 ring-amber-200',
            default => 'bg-slate-100 text-slate-700 ring-slate-200'
        };
    @endphp

    <div class="mx-auto max-w-5xl space-y-6 px-4 py-8 sm:px-6 lg:px-8">
        @if (session('status'))
            <div class="rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-800">{{ session('status') }}</div>
        @endif
        @if ($errors->any())
            <div class="rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">{{ $errors->first() }}</div>
        @endif

        <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
            <section class="rounded-xl bg-slate-950 p-6 text-white shadow-sm lg:col-span-2">
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-wider text-emerald-300">{{ __('Available Balance') }}</p>
                        <p class="mt-3 text-4xl font-semibold">
                            <span dir="ltr" class="font-mono">{{ number_format((float) $account->balance, 2) }}</span>
                            <span class="text-lg font-normal text-slate-400">{{ $account->accountType?->currency }}</span>
                        </p>
                    </div>
                    <span class="inline-flex rounded-full px-3 py-1 text-xs font-semibold ring-1 ring-inset {{ $statusClass }}">
                        {{ $account->status->label() }}
                    </span>
                </div>
                <div class="mt-8 grid grid-cols-1 gap-4 border-t border-slate-800 pt-5 sm:grid-cols-2">
                    <div>
                        <p class="text-xs uppercase text-slate-400">{{ __('Account Number') }}</p>
                        <p class="mt-1 font-mono text-lg font-semibold text-white" dir="ltr">{{ $account->account_number }}</p>
                    </div>
                    <div>
                        <p class="text-xs uppercase text-slate-400">{{ __('IBAN') }}</p>
                        <p class="mt-1 break-all font-mono text-sm font-semibold text-emerald-300" dir="ltr">{{ $account->iban }}</p>
                    </div>
                </div>
            </section>

            <section class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm flex flex-col justify-between">
                <div>
                    <p class="text-xs font-semibold uppercase text-slate-500">{{ __('Customer') }}</p>
                    <p class="mt-2 text-xl font-semibold text-slate-950">{{ $account->customer?->full_name }}</p>
                    <p class="mt-1 text-xs text-slate-500 font-mono" dir="ltr">{{ $account->customer?->customer_number }}</p>
                    <p class="mt-3 text-sm text-slate-600">{{ $account->customer?->user?->email }}</p>
                </div>
                <a href="{{ route('customers.show', $account->customer) }}" class="mt-5 inline-flex items-center gap-1 text-sm font-semibold text-emerald-700 hover:text-emerald-900">
                    <span>{{ __('View customer profile') }}</span>
                    <span>&rarr;</span>
                </a>
            </section>
        </div>

        <section class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
            <h3 class="font-semibold text-slate-950">{{ __('Account Details') }}</h3>
            <dl class="mt-5 grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4">
                <div>
                    <dt class="text-xs uppercase text-slate-500">{{ __('Account Type') }}</dt>
                    <dd class="mt-1 font-medium text-slate-900">{{ $account->accountType?->name }}</dd>
                </div>
                <div>
                    <dt class="text-xs uppercase text-slate-500">{{ __('Currency') }}</dt>
                    <dd class="mt-1 font-medium text-slate-900">{{ $account->accountType?->currency }}</dd>
                </div>
                <div>
                    <dt class="text-xs uppercase text-slate-500">{{ __('Opened At') }}</dt>
                    <dd class="mt-1 font-medium text-slate-900 font-mono" dir="ltr">{{ $account->opened_at?->format('Y-m-d H:i') ?? __('Not set') }}</dd>
                </div>
                <div>
                    <dt class="text-xs uppercase text-slate-500">{{ __('Closed At') }}</dt>
                    <dd class="mt-1 font-medium text-slate-900 font-mono" dir="ltr">{{ $account->closed_at?->format('Y-m-d H:i') ?? __('Not set') }}</dd>
                </div>
            </dl>
            @if ($account->freeze_reason)
                <div class="mt-5 rounded-lg border border-amber-200 bg-amber-50 p-4 text-sm text-amber-900">
                    <span class="font-semibold">{{ __('Freeze reason') }}:</span> {{ $account->freeze_reason }}
                </div>
            @endif
        </section>

        <section class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h3 class="font-semibold text-slate-950">{{ __('Account Actions') }}</h3>
                    <p class="text-sm text-slate-500">{{ __('Operational state changes are recorded on this account.') }}</p>
                </div>
                <div class="flex flex-wrap items-center gap-3">
                    @if ($account->status->value === 'open')
                        <form method="POST" action="{{ route('accounts.freeze', $account) }}" class="flex items-center gap-2">
                            @csrf
                            <x-text-input name="freeze_reason" type="text" class="w-48 text-sm" placeholder="{{ __('Freeze reason') }}" />
                            <button class="rounded-lg bg-amber-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-amber-700 transition">{{ __('Freeze') }}</button>
                        </form>
                        <form method="POST" action="{{ route('accounts.close', $account) }}">
                            @csrf
                            <button class="rounded-lg border border-red-200 px-4 py-2 text-sm font-semibold text-red-700 hover:bg-red-50 transition">{{ __('Close Account') }}</button>
                        </form>
                    @elseif ($account->status->value === 'frozen')
                        <form method="POST" action="{{ route('accounts.reactivate', $account) }}">
                            @csrf
                            <button class="rounded-lg bg-emerald-700 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-emerald-800 transition">{{ __('Reactivate') }}</button>
                        </form>
                    @else
                        <form method="POST" action="{{ route('accounts.open', $account) }}">
                            @csrf
                            <button class="rounded-lg bg-emerald-700 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-emerald-800 transition">{{ __('Open Account') }}</button>
                        </form>
                    @endif
                </div>
            </div>
        </section>
    </div>
</x-app-layout>
