<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <p class="text-xs font-semibold uppercase text-emerald-700">{{ __('KYC Operations') }}</p>
                <h2 class="mt-1 text-2xl font-semibold text-slate-950 leading-tight">
                    {{ __('Customer Identity Verification') }}
                </h2>
            </div>
            <a href="{{ route('customers.create') }}" class="inline-flex items-center justify-center gap-2 rounded-lg bg-slate-950 px-4 py-2.5 text-sm font-semibold text-white shadow-sm shadow-slate-900/20 transition hover:bg-slate-800 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2">
                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 5v14M5 12h14" />
                </svg>
                {{ __('New Customer') }}
            </a>
        </div>
    </x-slot>

    @php
        $filterValue = fn (string $key): string => (string) ($filters[$key] ?? '');
    @endphp

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            @if (session('status'))
                <div class="rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-800">
                    {{ session('status') }}
                </div>
            @endif

            <div class="grid grid-cols-1 gap-4 md:grid-cols-5">
                <div class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
                    <p class="text-xs font-semibold uppercase text-slate-500">{{ __('Matched Records') }}</p>
                    <p class="mt-2 text-3xl font-semibold text-slate-950">{{ number_format($kycSummary['total']) }}</p>
                </div>
                <div class="rounded-lg border border-emerald-200 bg-emerald-50 p-5 shadow-sm">
                    <p class="text-xs font-semibold uppercase text-emerald-700">{{ __('Approved KYC') }}</p>
                    <p class="mt-2 text-3xl font-semibold text-emerald-950">{{ number_format($kycSummary['approved']) }}</p>
                </div>
                <div class="rounded-lg border border-amber-200 bg-amber-50 p-5 shadow-sm">
                    <p class="text-xs font-semibold uppercase text-amber-700">{{ __('Pending Review') }}</p>
                    <p class="mt-2 text-3xl font-semibold text-amber-950">{{ number_format($kycSummary['pending']) }}</p>
                </div>
                <div class="rounded-lg border border-red-200 bg-red-50 p-5 shadow-sm">
                    <p class="text-xs font-semibold uppercase text-red-700">{{ __('High Risk') }}</p>
                    <p class="mt-2 text-3xl font-semibold text-red-950">{{ number_format($kycSummary['high_risk']) }}</p>
                </div>
                <div class="rounded-lg border border-sky-200 bg-sky-50 p-5 shadow-sm">
                    <p class="text-xs font-semibold uppercase text-sky-700">{{ __('Docs Expiring') }}</p>
                    <p class="mt-2 text-3xl font-semibold text-sky-950">{{ number_format($kycSummary['expiring_documents']) }}</p>
                </div>
            </div>

            <form method="GET" action="{{ route('customers.index') }}" class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
                <div class="grid grid-cols-1 gap-4 lg:grid-cols-12">
                    <div class="lg:col-span-4">
                        <x-input-label for="search" :value="__('Search')" />
                        <x-text-input id="search" name="search" type="search" class="mt-1 block w-full rounded-lg border-slate-300 text-sm shadow-sm focus:border-emerald-500 focus:ring-emerald-500" :value="$filterValue('search')" placeholder="{{ __('Name, number, ID, phone, email or KYC reference') }}" />
                    </div>

                    <div class="lg:col-span-2">
                        <x-input-label for="kyc_status" :value="__('KYC')" />
                        <select id="kyc_status" name="kyc_status" class="mt-1 block w-full rounded-lg border-slate-300 bg-white text-sm shadow-sm focus:border-emerald-500 focus:ring-emerald-500">
                            <option value="">{{ __('Any') }}</option>
                            @foreach ($kycStatuses as $status)
                                <option value="{{ $status->value }}" @selected($filterValue('kyc_status') === $status->value)>{{ $status->label() }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="lg:col-span-2">
                        <x-input-label for="risk_level" :value="__('Risk')" />
                        <select id="risk_level" name="risk_level" class="mt-1 block w-full rounded-lg border-slate-300 bg-white text-sm shadow-sm focus:border-emerald-500 focus:ring-emerald-500">
                            <option value="">{{ __('Any') }}</option>
                            @foreach ($riskLevels as $level)
                                <option value="{{ $level->value }}" @selected($filterValue('risk_level') === $level->value)>{{ $level->label() }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="lg:col-span-2">
                        <x-input-label for="status" :value="__('Customer Status')" />
                        <select id="status" name="status" class="mt-1 block w-full rounded-lg border-slate-300 bg-white text-sm shadow-sm focus:border-emerald-500 focus:ring-emerald-500">
                            <option value="">{{ __('Any') }}</option>
                            @foreach ($customerStatuses as $status)
                                <option value="{{ $status->value }}" @selected($filterValue('status') === $status->value)>{{ $status->label() }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="lg:col-span-2">
                        <x-input-label for="sort" :value="__('Sort')" />
                        <select id="sort" name="sort" class="mt-1 block w-full rounded-lg border-slate-300 bg-white text-sm shadow-sm focus:border-emerald-500 focus:ring-emerald-500">
                            <option value="latest" @selected($filterValue('sort') === '' || $filterValue('sort') === 'latest')>{{ __('Newest') }}</option>
                            <option value="name" @selected($filterValue('sort') === 'name')>{{ __('Name') }}</option>
                            <option value="risk" @selected($filterValue('sort') === 'risk')>{{ __('Risk Priority') }}</option>
                            <option value="kyc_oldest" @selected($filterValue('sort') === 'kyc_oldest')>{{ __('Oldest Review') }}</option>
                            <option value="verification_newest" @selected($filterValue('sort') === 'verification_newest')>{{ __('Newest Review') }}</option>
                            <option value="document_expiry" @selected($filterValue('sort') === 'document_expiry')>{{ __('Document Expiry') }}</option>
                        </select>
                    </div>

                    <div class="lg:col-span-3">
                        <x-input-label for="identity_document_type" :value="__('Document Type')" />
                        <select id="identity_document_type" name="identity_document_type" class="mt-1 block w-full rounded-lg border-slate-300 bg-white text-sm shadow-sm focus:border-emerald-500 focus:ring-emerald-500">
                            <option value="">{{ __('Any') }}</option>
                            @foreach ($identityDocumentTypes as $type)
                                <option value="{{ $type->value }}" @selected($filterValue('identity_document_type') === $type->value)>{{ $type->label() }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="lg:col-span-2">
                        <x-input-label for="identity_document_country" :value="__('Country')" />
                        <x-text-input id="identity_document_country" name="identity_document_country" type="text" maxlength="2" class="mt-1 block w-full rounded-lg border-slate-300 text-sm uppercase shadow-sm focus:border-emerald-500 focus:ring-emerald-500" :value="$filterValue('identity_document_country')" placeholder="SY" />
                    </div>

                    <div class="lg:col-span-2">
                        <x-input-label for="verified_from" :value="__('Verified From')" />
                        <x-text-input id="verified_from" name="verified_from" type="date" class="mt-1 block w-full rounded-lg border-slate-300 text-sm shadow-sm focus:border-emerald-500 focus:ring-emerald-500" :value="$filterValue('verified_from')" />
                    </div>

                    <div class="lg:col-span-2">
                        <x-input-label for="verified_to" :value="__('Verified To')" />
                        <x-text-input id="verified_to" name="verified_to" type="date" class="mt-1 block w-full rounded-lg border-slate-300 text-sm shadow-sm focus:border-emerald-500 focus:ring-emerald-500" :value="$filterValue('verified_to')" />
                    </div>

                    <div class="flex items-end gap-2 lg:col-span-3">
                        <button type="submit" class="inline-flex min-h-10 flex-1 items-center justify-center rounded-lg bg-slate-950 px-4 py-2 text-sm font-semibold text-white shadow-sm shadow-slate-900/20 transition hover:bg-slate-800 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2">
                            {{ __('Apply Filters') }}
                        </button>
                        <a href="{{ route('customers.index') }}" class="inline-flex min-h-10 items-center justify-center rounded-lg border border-slate-200 px-4 py-2 text-sm font-semibold text-slate-700 transition hover:border-emerald-300 hover:text-emerald-700">
                            {{ __('Reset') }}
                        </a>
                    </div>
                </div>
            </form>

            <div class="overflow-hidden rounded-lg border border-slate-200 bg-white shadow-sm">
                <div class="flex flex-col gap-2 border-b border-slate-200 px-5 py-4 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <h3 class="text-base font-semibold text-slate-950">{{ __('KYC Customer Ledger') }}</h3>
                        <p class="text-sm text-slate-500">{{ __('Identity documents, review decisions and risk profile overview') }}</p>
                    </div>
                    <span class="text-sm font-medium text-slate-500">
                        {{ $customers->firstItem() ?? 0 }}-{{ $customers->lastItem() ?? 0 }} / {{ $customers->total() }}
                    </span>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-200">
                        <thead class="bg-slate-50">
                            <tr>
                                <th class="px-5 py-3 text-left text-xs font-semibold uppercase text-slate-500">{{ __('Number') }}</th>
                                <th class="px-5 py-3 text-left text-xs font-semibold uppercase text-slate-500">{{ __('Name') }}</th>
                                <th class="px-5 py-3 text-left text-xs font-semibold uppercase text-slate-500">{{ __('User') }}</th>
                                <th class="px-5 py-3 text-left text-xs font-semibold uppercase text-slate-500">{{ __('Document') }}</th>
                                <th class="px-5 py-3 text-left text-xs font-semibold uppercase text-slate-500">{{ __('Status') }}</th>
                                <th class="px-5 py-3 text-left text-xs font-semibold uppercase text-slate-500">{{ __('KYC') }}</th>
                                <th class="px-5 py-3 text-left text-xs font-semibold uppercase text-slate-500">{{ __('Risk') }}</th>
                                <th class="px-5 py-3 text-left text-xs font-semibold uppercase text-slate-500">{{ __('Reviewed') }}</th>
                                <th class="px-5 py-3 text-right text-xs font-semibold uppercase text-slate-500">{{ __('Actions') }}</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 bg-white">
                            @forelse ($customers as $customer)
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
                                <tr class="transition hover:bg-slate-50">
                                    <td class="px-5 py-4 whitespace-nowrap text-sm font-semibold text-slate-950">{{ $customer->customer_number }}</td>
                                    <td class="px-5 py-4 whitespace-nowrap text-sm">
                                        <a href="{{ route('customers.show', $customer) }}" class="font-semibold text-slate-900 hover:text-emerald-700">
                                            {{ $customer->full_name }}
                                        </a>
                                        @if ($customer->kyc_reference)
                                            <p class="mt-0.5 text-xs text-slate-500">{{ $customer->kyc_reference }}</p>
                                        @endif
                                    </td>
                                    <td class="px-5 py-4 whitespace-nowrap text-sm text-slate-600">{{ $customer->user?->email }}</td>
                                    <td class="px-5 py-4 whitespace-nowrap text-sm text-slate-600">
                                        <p class="font-medium text-slate-900">{{ $customer->identity_document_type?->label() ?? __('Not set') }}</p>
                                        <p class="mt-0.5 text-xs text-slate-500">{{ $customer->identity_document_number ?? __('No document number') }}</p>
                                        @if ($customer->identity_document_country || $customer->identity_document_expires_at)
                                            <p class="mt-0.5 text-xs text-slate-500">
                                                {{ $customer->identity_document_country ?? __('N/A') }}
                                                @if ($customer->identity_document_expires_at)
                                                    - {{ $customer->identity_document_expires_at->toDateString() }}
                                                @endif
                                            </p>
                                        @endif
                                    </td>
                                    <td class="px-5 py-4 whitespace-nowrap text-sm">
                                        <span class="inline-flex items-center rounded-full px-2.5 py-1 text-xs font-semibold ring-1 ring-inset {{ $statusClass }}">{{ $customer->status->label() }}</span>
                                    </td>
                                    <td class="px-5 py-4 whitespace-nowrap text-sm">
                                        <span class="inline-flex items-center rounded-full px-2.5 py-1 text-xs font-semibold ring-1 ring-inset {{ $kycClass }}">{{ $customer->kyc_status->label() }}</span>
                                    </td>
                                    <td class="px-5 py-4 whitespace-nowrap text-sm">
                                        <span class="inline-flex items-center rounded-full px-2.5 py-1 text-xs font-semibold ring-1 ring-inset {{ $riskClass }}">{{ $customer->risk_level->label() }}</span>
                                    </td>
                                    <td class="px-5 py-4 whitespace-nowrap text-sm text-slate-600">
                                        <p class="font-medium text-slate-900">{{ $customer->kyc_reviewed_at?->toDateString() ?? __('Pending') }}</p>
                                        <p class="mt-0.5 text-xs text-slate-500">{{ $customer->kycReviewer?->name ?? __('No reviewer') }}</p>
                                    </td>
                                    <td class="px-5 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <a href="{{ route('customers.edit', $customer) }}" class="inline-flex items-center rounded-md border border-slate-200 px-3 py-1.5 text-xs font-semibold text-slate-700 transition hover:border-emerald-300 hover:text-emerald-700">{{ __('Edit') }}</a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="9" class="px-6 py-14 text-center">
                                        <p class="text-sm font-semibold text-slate-700">{{ __('No customers found.') }}</p>
                                        <p class="mt-1 text-sm text-slate-500">{{ __('Adjust the KYC filters or create the first customer identity file.') }}</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="rounded-lg border border-slate-200 bg-white px-4 py-3 shadow-sm">
                {{ $customers->links() }}
            </div>
        </div>
    </div>
</x-app-layout>
