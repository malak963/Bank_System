@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('customers.index') }}">{{ __('Customers') }}</a></li>
    <li class="breadcrumb-item active">{{ $customer->full_name }}</li>
@endsection

<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <p class="text-xs font-semibold uppercase text-emerald-700">{{ __('Customer Profile') }}</p>
                <h2 class="mt-1 text-2xl font-semibold text-slate-950 leading-tight">
                    {{ $customer->full_name }}
                </h2>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('customers.index') }}" class="btn btn-secondary">
                    <svg class="w-4 h-4 rtl:rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    <span>{{ __('Back') }}</span>
                </a>
                <a href="{{ route('customers.edit', $customer) }}" class="btn btn-primary">
                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.651-1.651a2.121 2.121 0 1 1 3 3l-9.193 9.193a4.5 4.5 0 0 1-1.897 1.13L7.5 17.25l1.091-2.923a4.5 4.5 0 0 1 1.13-1.897l7.141-7.143Z" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 7.125 16.875 4.5" />
                    </svg>
                    <span>{{ __('Edit') }}</span>
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-6">
            @php
                $statusClass = match ($customer->status->value) {
                    'active' => 'bg-emerald-50 text-emerald-700 ring-emerald-200',
                    'suspended' => 'bg-amber-50 text-amber-700 ring-amber-200',
                    'closed' => 'bg-slate-100 text-slate-700 ring-slate-200',
                    default => 'bg-blue-50 text-blue-700 ring-blue-200',
                };
                $kycClass = match ($customer->kyc_status->value) {
                    'approved' => 'bg-emerald-50 text-emerald-700 ring-emerald-200',
                    'rejected' => 'bg-red-50 text-red-700 ring-red-200',
                    default => 'bg-amber-50 text-amber-700 ring-amber-200',
                };
                $riskClass = match ($customer->risk_level->value) {
                    'high' => 'bg-red-50 text-red-700 ring-red-200',
                    'medium' => 'bg-amber-50 text-amber-700 ring-amber-200',
                    default => 'bg-emerald-50 text-emerald-700 ring-emerald-200',
                };
            @endphp

            <div class="overflow-hidden rounded-lg border border-slate-200 bg-white shadow-sm">
                <div class="border-b border-slate-200 bg-slate-950 px-6 py-6">
                    <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
                        <div>
                            <p class="text-sm font-medium text-slate-400">{{ __('Customer Number') }}</p>
                            <p class="mt-1 text-2xl font-semibold text-white">{{ $customer->customer_number }}</p>
                        </div>
                        <div class="flex flex-wrap gap-2">
                            <span class="inline-flex items-center rounded-full px-3 py-1 text-xs font-semibold ring-1 ring-inset {{ $statusClass }}">{{ $customer->status->label() }}</span>
                            <span class="inline-flex items-center rounded-full px-3 py-1 text-xs font-semibold ring-1 ring-inset {{ $kycClass }}">{{ $customer->kyc_status->label() }}</span>
                            <span class="inline-flex items-center rounded-full px-3 py-1 text-xs font-semibold ring-1 ring-inset {{ $riskClass }}">{{ $customer->risk_level->label() }}</span>
                        </div>
                    </div>
                </div>

                <dl class="grid grid-cols-1 divide-y divide-slate-100 sm:grid-cols-2 sm:divide-x sm:divide-y-0">
                    <div class="space-y-5 p-6">
                        <div>
                            <dt class="text-xs font-semibold uppercase text-slate-500">{{ __('Linked User') }}</dt>
                            <dd class="mt-1 text-sm font-medium text-slate-900">{{ $customer->user?->email }}</dd>
                        </div>
                        <div>
                            <dt class="text-xs font-semibold uppercase text-slate-500">{{ __('Linked Branch') }}</dt>
                            <dd class="mt-1 text-sm font-medium text-slate-900">
                                @if ($customer->branch)
                                    <a href="{{ route('branches.show', $customer->branch) }}" class="inline-flex items-center gap-1.5 font-semibold text-emerald-700 hover:text-emerald-800 hover:underline">
                                        <svg class="h-4 w-4 text-emerald-600 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 21h19.5m-18-18v18m10.5-18v18m6-13.5V21M6.75 6.75h.75m-.75 3h.75m-.75 3h.75m3-6h.75m-.75 3h.75m-.75 3h.75M6.75 21v-3.75a1.5 1.5 0 0 1 1.5-1.5h1.5a1.5 1.5 0 0 1 1.5 1.5V21" />
                                        </svg>
                                        {{ $customer->branch->name }}
                                        <span class="font-mono text-xs text-slate-500">({{ $customer->branch->code }})</span>
                                    </a>
                                @else
                                    <span class="text-slate-400 italic">{{ __('Not assigned') }}</span>
                                @endif
                            </dd>
                        </div>
                        <div>
                            <dt class="text-xs font-semibold uppercase text-slate-500">{{ __('National ID') }}</dt>
                            <dd class="mt-1 text-sm font-medium text-slate-900">{{ $customer->national_id }}</dd>
                        </div>
                        <div>
                            <dt class="text-xs font-semibold uppercase text-slate-500">{{ __('Date of Birth') }}</dt>
                            <dd class="mt-1 text-sm font-medium text-slate-900">{{ $customer->date_of_birth?->toDateString() ?? __('Not set') }}</dd>
                        </div>
                    </div>
                    <div class="space-y-5 p-6">
                        <div>
                            <dt class="text-xs font-semibold uppercase text-slate-500">{{ __('Phone') }}</dt>
                            <dd class="mt-1 text-sm font-medium text-slate-900">{{ $customer->phone_number ?? __('Not set') }}</dd>
                        </div>
                        <div>
                            <dt class="text-xs font-semibold uppercase text-slate-500">{{ __('Address') }}</dt>
                            <dd class="mt-1 text-sm font-medium text-slate-900">{{ $customer->address ?? __('Not set') }}</dd>
                        </div>
                        <div>
                            <dt class="text-xs font-semibold uppercase text-slate-500">{{ __('Updated At') }}</dt>
                            <dd class="mt-1 text-sm font-medium text-slate-900">{{ $customer->updated_at?->toDateTimeString() }}</dd>
                        </div>
                    </div>
                </dl>
            </div>

            <div class="overflow-hidden rounded-lg border border-slate-200 bg-white shadow-sm">
                <div class="border-b border-slate-200 px-6 py-5">
                    <h3 class="text-base font-semibold text-slate-950">{{ __('KYC Verification') }}</h3>
                    <p class="mt-1 text-sm text-slate-500">{{ __('Identity document, review reference and final decision trail.') }}</p>
                </div>

                <dl class="grid grid-cols-1 divide-y divide-slate-100 md:grid-cols-3 md:divide-x md:divide-y-0">
                    <div class="space-y-5 p-6">
                        <div>
                            <dt class="text-xs font-semibold uppercase text-slate-500">{{ __('Document Type') }}</dt>
                            <dd class="mt-1 text-sm font-medium text-slate-900">{{ $customer->identity_document_type?->label() ?? __('Not set') }}</dd>
                        </div>
                        <div>
                            <dt class="text-xs font-semibold uppercase text-slate-500">{{ __('Document Number') }}</dt>
                            <dd class="mt-1 text-sm font-medium text-slate-900">{{ $customer->identity_document_number ?? __('Not set') }}</dd>
                        </div>
                    </div>
                    <div class="space-y-5 p-6">
                        <div>
                            <dt class="text-xs font-semibold uppercase text-slate-500">{{ __('Issuing Country') }}</dt>
                            <dd class="mt-1 text-sm font-medium text-slate-900">{{ $customer->identity_document_country ?? __('Not set') }}</dd>
                        </div>
                        <div>
                            <dt class="text-xs font-semibold uppercase text-slate-500">{{ __('Document Expiry') }}</dt>
                            <dd class="mt-1 text-sm font-medium text-slate-900">{{ $customer->identity_document_expires_at?->toDateString() ?? __('Not set') }}</dd>
                        </div>
                    </div>
                    <div class="space-y-5 p-6">
                        <div>
                            <dt class="text-xs font-semibold uppercase text-slate-500">{{ __('KYC Reference') }}</dt>
                            <dd class="mt-1 text-sm font-medium text-slate-900">{{ $customer->kyc_reference ?? __('Not set') }}</dd>
                        </div>
                        <div>
                            <dt class="text-xs font-semibold uppercase text-slate-500">{{ __('Reviewed By') }}</dt>
                            <dd class="mt-1 text-sm font-medium text-slate-900">{{ $customer->kycReviewer?->name ?? __('Not reviewed') }}</dd>
                        </div>
                        <div>
                            <dt class="text-xs font-semibold uppercase text-slate-500">{{ __('Reviewed At') }}</dt>
                            <dd class="mt-1 text-sm font-medium text-slate-900">{{ $customer->kyc_reviewed_at?->toDateTimeString() ?? __('Not reviewed') }}</dd>
                        </div>
                    </div>
                </dl>

                @if ($customer->kyc_rejection_reason)
                    <div class="border-t border-red-100 bg-red-50 px-6 py-5">
                        <p class="text-xs font-semibold uppercase text-red-700">{{ __('Rejection Reason') }}</p>
                        <p class="mt-2 text-sm font-medium text-red-950">{{ $customer->kyc_rejection_reason }}</p>
                    </div>
                @endif
            </div>

            {{-- Accounts & Cards Section --}}
            @if ($customer->accounts->isNotEmpty())
                <div class="overflow-hidden rounded-lg border border-slate-200 bg-white shadow-sm">
                    <div class="border-b border-slate-200 px-6 py-5 flex items-center justify-between">
                        <div>
                            <h3 class="text-base font-semibold text-slate-950">{{ __('Accounts & Cards') }}</h3>
                            <p class="mt-1 text-sm text-slate-500">{{ __('All accounts and associated cards for this customer.') }}</p>
                        </div>
                        <span class="inline-flex items-center rounded-full bg-emerald-50 px-3 py-1 text-xs font-semibold text-emerald-700 ring-1 ring-inset ring-emerald-200">
                            {{ $customer->accounts->count() }} {{ __('account(s)') }}
                        </span>
                    </div>

                    <div class="divide-y divide-slate-100">
                        @foreach ($customer->accounts as $account)
                            <div class="p-6">
                                {{-- Account Header --}}
                                <div class="flex flex-wrap items-center justify-between gap-3 mb-4">
                                    <div class="flex items-center gap-3">
                                        <div class="flex h-9 w-9 items-center justify-center rounded-lg bg-emerald-100">
                                            <svg class="h-5 w-5 text-emerald-700" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 8.25h19.5M2.25 9h19.5m-16.5 5.25h6m-6 2.25h3m-3.75 3h15a2.25 2.25 0 0 0 2.25-2.25V6.75A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25v10.5A2.25 2.25 0 0 0 4.5 19.5Z" />
                                            </svg>
                                        </div>
                                        <div>
                                            <p class="text-xs font-semibold uppercase text-slate-500">{{ __('Account Number') }}</p>
                                            <p class="font-mono text-base font-semibold text-slate-900 tracking-wider" dir="ltr">{{ $account->account_number }}</p>
                                        </div>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        @php
                                            $acctStatusClass = match ($account->status->value) {
                                                'open'   => 'bg-emerald-50 text-emerald-700 ring-emerald-200',
                                                'closed' => 'bg-slate-100 text-slate-700 ring-slate-200',
                                                'frozen' => 'bg-blue-50 text-blue-700 ring-blue-200',
                                                default  => 'bg-amber-50 text-amber-700 ring-amber-200',
                                            };
                                        @endphp
                                        <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-semibold ring-1 ring-inset {{ $acctStatusClass }}">
                                            {{ $account->status->label() }}
                                        </span>
                                        <a href="{{ route('accounts.show', $account) }}"
                                           class="inline-flex items-center gap-1 rounded-md border border-slate-200 bg-white px-3 py-1.5 text-xs font-medium text-slate-700 shadow-sm hover:bg-slate-50 transition">
                                            {{ __('View') }}
                                            <svg class="h-3.5 w-3.5 rtl:rotate-180" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3" />
                                            </svg>
                                        </a>
                                    </div>
                                </div>

                                {{-- Cards under this account --}}
                                @if ($account->cards->isNotEmpty())
                                    <div class="mt-3 rounded-lg border border-slate-100 bg-slate-50 p-4">
                                        <p class="mb-3 text-xs font-semibold uppercase text-slate-500">
                                            {{ __('Cards') }} ({{ $account->cards->count() }})
                                        </p>
                                        <div class="grid grid-cols-1 gap-3 sm:grid-cols-2 lg:grid-cols-3">
                                            @foreach ($account->cards as $card)
                                                @php
                                                    $cardStatusClass = match ($card->status->value) {
                                                        'active'   => 'bg-emerald-50 text-emerald-700 ring-emerald-200',
                                                        'blocked'  => 'bg-red-50 text-red-700 ring-red-200',
                                                        'expired'  => 'bg-slate-100 text-slate-600 ring-slate-200',
                                                        default    => 'bg-amber-50 text-amber-700 ring-amber-200',
                                                    };
                                                @endphp
                                                <div class="flex items-start gap-3 rounded-lg border border-slate-200 bg-white px-4 py-3 shadow-sm">
                                                    <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-md bg-slate-900">
                                                        <svg class="h-4 w-4 text-white" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 8.25h19.5M2.25 9h19.5m-16.5 5.25h6m-6 2.25h3m-3.75 3h15a2.25 2.25 0 0 0 2.25-2.25V6.75A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25v10.5A2.25 2.25 0 0 0 4.5 19.5Z" />
                                                        </svg>
                                                    </div>
                                                    <div class="min-w-0 flex-1">
                                                        <p class="font-mono text-sm font-semibold text-slate-900 tracking-widest" dir="ltr">
                                                            **** **** **** {{ substr($card->card_number, -4) }}
                                                        </p>
                                                        <p class="mt-0.5 text-xs text-slate-500">
                                                            {{ $card->card_brand->label() }} &middot; {{ $card->card_type->label() }}
                                                        </p>
                                                        <div class="mt-1.5">
                                                            <span class="inline-flex items-center rounded-full px-2 py-0.5 text-xs font-semibold ring-1 ring-inset {{ $cardStatusClass }}">
                                                                {{ $card->status->label() }}
                                                            </span>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                @else
                                    <p class="mt-2 text-sm italic text-slate-400">{{ __('No cards linked to this account.') }}</p>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

        </div>
    </div>
</x-app-layout>
