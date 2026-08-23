<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <p class="text-xs font-semibold uppercase text-emerald-700">{{ __('Customer Profile') }}</p>
                <h2 class="mt-1 text-2xl font-semibold text-slate-950 leading-tight">
                    {{ $customer->full_name }}
                </h2>
            </div>
            <a href="{{ route('customers.edit', $customer) }}" class="inline-flex items-center justify-center gap-2 rounded-lg bg-slate-950 px-4 py-2.5 text-sm font-semibold text-white shadow-sm shadow-slate-900/20 transition hover:bg-slate-800 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2">
                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.651-1.651a2.121 2.121 0 1 1 3 3l-9.193 9.193a4.5 4.5 0 0 1-1.897 1.13L7.5 17.25l1.091-2.923a4.5 4.5 0 0 1 1.13-1.897l7.141-7.143Z" />
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 7.125 16.875 4.5" />
                </svg>
                {{ __('Edit') }}
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-6">
            @if (session('status'))
                <div class="rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-800">
                    {{ session('status') }}
                </div>
            @endif

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
        </div>
    </div>
</x-app-layout>
