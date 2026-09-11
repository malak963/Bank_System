<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <p class="text-xs font-semibold uppercase text-emerald-700">{{ __('Account Operations') }}</p>
                <h2 class="mt-1 text-2xl font-semibold text-slate-950">{{ __('Accounts') }}</h2>
            </div>
            <a href="{{ route('accounts.create') }}" class="inline-flex items-center gap-2 rounded-lg bg-slate-950 px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-slate-800">+ {{ __('Open New Account') }}</a>
        </div>
    </x-slot>

    @php $filterValue = fn (string $key): string => (string) ($filters[$key] ?? ''); @endphp

    <div class="mx-auto max-w-7xl space-y-6 px-4 py-8 sm:px-6 lg:px-8">
        @if (session('status'))
            <div class="rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-800">{{ session('status') }}</div>
        @endif
        @if ($errors->any())
            <div class="rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">{{ $errors->first() }}</div>
        @endif

        <div class="grid grid-cols-2 gap-4 lg:grid-cols-4">
            <div class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm"><p class="text-xs font-semibold uppercase text-slate-500">{{ __('Total Accounts') }}</p><p class="mt-2 text-3xl font-semibold text-slate-950">{{ number_format($summary['total']) }}</p></div>
            <div class="rounded-lg border border-emerald-200 bg-emerald-50 p-5 shadow-sm"><p class="text-xs font-semibold uppercase text-emerald-700">{{ __('Open') }}</p><p class="mt-2 text-3xl font-semibold text-emerald-950">{{ number_format($summary['open']) }}</p></div>
            <div class="rounded-lg border border-amber-200 bg-amber-50 p-5 shadow-sm"><p class="text-xs font-semibold uppercase text-amber-700">{{ __('Frozen') }}</p><p class="mt-2 text-3xl font-semibold text-amber-950">{{ number_format($summary['frozen']) }}</p></div>
            <div class="rounded-lg border border-slate-200 bg-slate-50 p-5 shadow-sm"><p class="text-xs font-semibold uppercase text-slate-600">{{ __('Closed') }}</p><p class="mt-2 text-3xl font-semibold text-slate-950">{{ number_format($summary['closed']) }}</p></div>
        </div>

        <form method="GET" action="{{ route('accounts.index') }}" class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
            <div class="grid grid-cols-1 gap-4 lg:grid-cols-12">
                <div class="lg:col-span-5"><x-input-label for="search" :value="__('Search')" /><x-text-input id="search" name="search" type="search" class="mt-1 block w-full" :value="$filterValue('search')" placeholder="{{ __('Account number, IBAN or customer') }}" /></div>
                <div class="lg:col-span-2"><x-input-label for="status" :value="__('Status')" /><select id="status" name="status" class="mt-1 block w-full rounded-lg border-slate-300 bg-white text-sm shadow-sm focus:border-emerald-500 focus:ring-emerald-500"><option value="">{{ __('Any') }}</option>@foreach ($statuses as $status)<option value="{{ $status->value }}" @selected($filterValue('status') === $status->value)>{{ $status->label() }}</option>@endforeach</select></div>
                <div class="lg:col-span-3"><x-input-label for="account_type_id" :value="__('Account Type')" /><select id="account_type_id" name="account_type_id" class="mt-1 block w-full rounded-lg border-slate-300 bg-white text-sm shadow-sm focus:border-emerald-500 focus:ring-emerald-500"><option value="">{{ __('Any') }}</option>@foreach ($accountTypes as $accountType)<option value="{{ $accountType->id }}" @selected($filterValue('account_type_id') === (string) $accountType->id)>{{ $accountType->name }} ({{ $accountType->currency }})</option>@endforeach</select></div>
                <div class="flex items-end gap-2 lg:col-span-2"><button type="submit" class="inline-flex min-h-10 flex-1 items-center justify-center rounded-lg bg-slate-950 px-4 py-2 text-sm font-semibold text-white hover:bg-slate-800">{{ __('Filter') }}</button><a href="{{ route('accounts.index') }}" class="inline-flex min-h-10 items-center justify-center rounded-lg border border-slate-200 px-3 py-2 text-sm font-semibold text-slate-700 hover:border-emerald-300">{{ __('Reset') }}</a></div>
            </div>
        </form>

        <div class="overflow-hidden rounded-lg border border-slate-200 bg-white shadow-sm">
            <div class="flex items-center justify-between border-b border-slate-200 px-5 py-4"><div><h3 class="font-semibold text-slate-950">{{ __('Account Ledger') }}</h3><p class="text-sm text-slate-500">{{ __('Account identity, owner and operational state') }}</p></div><span class="text-sm text-slate-500">{{ $accounts->firstItem() ?? 0 }}-{{ $accounts->lastItem() ?? 0 }} / {{ $accounts->total() }}</span></div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-200">
                    <thead class="bg-slate-50"><tr><th class="px-5 py-3 text-left text-xs font-semibold uppercase text-slate-500">{{ __('Account') }}</th><th class="px-5 py-3 text-left text-xs font-semibold uppercase text-slate-500">{{ __('Customer') }}</th><th class="px-5 py-3 text-left text-xs font-semibold uppercase text-slate-500">{{ __('Type') }}</th><th class="px-5 py-3 text-left text-xs font-semibold uppercase text-slate-500">{{ __('Balance') }}</th><th class="px-5 py-3 text-left text-xs font-semibold uppercase text-slate-500">{{ __('Status') }}</th><th class="px-5 py-3 text-right text-xs font-semibold uppercase text-slate-500">{{ __('Actions') }}</th></tr></thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($accounts as $account)
                            @php $statusClass = match ($account->status->value) { 'open' => 'bg-emerald-50 text-emerald-700 ring-emerald-200', 'frozen' => 'bg-amber-50 text-amber-700 ring-amber-200', default => 'bg-slate-100 text-slate-700 ring-slate-200' }; @endphp
                            <tr class="hover:bg-slate-50">
                                <td class="px-5 py-4 text-sm"><a href="{{ route('accounts.show', $account) }}" class="font-bold text-slate-950 hover:text-emerald-700">{{ $account->account_number }}</a><p class="mt-1 font-mono text-xs text-slate-500">{{ $account->iban }}</p></td>
                                <td class="px-5 py-4 text-sm"><p class="font-semibold text-slate-900">{{ $account->customer?->full_name }}</p><p class="text-xs text-slate-500">{{ $account->customer?->customer_number }}</p></td>
                                <td class="px-5 py-4 text-sm text-slate-700">{{ $account->accountType?->name }}<p class="text-xs text-slate-500">{{ $account->accountType?->currency }}</p></td>
                                <td class="px-5 py-4 whitespace-nowrap text-sm font-semibold text-slate-900">{{ number_format((float) $account->balance, 2) }} {{ $account->accountType?->currency }}</td>
                                <td class="px-5 py-4 text-sm"><span class="inline-flex rounded-full px-2.5 py-1 text-xs font-semibold ring-1 ring-inset {{ $statusClass }}">{{ $account->status->label() }}</span></td>
                                <td class="px-5 py-4 text-right text-sm"><a href="{{ route('accounts.show', $account) }}" class="font-semibold text-emerald-700 hover:text-emerald-900">{{ __('Manage') }}</a></td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="px-6 py-14 text-center text-sm text-slate-500">{{ __('No accounts found.') }}</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        {{ $accounts->links() }}
    </div>
</x-app-layout>
