@props([
    'customer',
    'users',
    'customerStatuses',
    'kycStatuses',
    'riskLevels',
    'identityDocumentTypes',
    'kycReviewers',
    'submitLabel',
])

@php
    $selectedUser = old('user_id', $customer?->user_id);
    $selectedStatus = old('status', $customer?->status?->value ?? 'prospect');
    $selectedKycStatus = old('kyc_status', $customer?->kyc_status?->value ?? 'pending');
    $selectedRiskLevel = old('risk_level', $customer?->risk_level?->value ?? 'low');
    $selectedIdentityDocumentType = old('identity_document_type', $customer?->identity_document_type?->value);
    $selectedKycReviewer = old('kyc_reviewed_by', $customer?->kyc_reviewed_by);
    $selectedKycReviewedAt = old('kyc_reviewed_at', $customer?->kyc_reviewed_at?->format('Y-m-d\TH:i'));
@endphp

<div class="space-y-8">
    <section>
        <div class="mb-4 border-b border-slate-200 pb-3">
            <h4 class="text-sm font-semibold text-slate-950">{{ __('Identity') }}</h4>
            <p class="mt-1 text-sm text-slate-500">{{ __('Core customer identity and linked system user.') }}</p>
        </div>

        <div class="grid grid-cols-1 gap-5 md:grid-cols-2">
    <div>
        <x-input-label for="user_id" :value="__('Linked User')" />
        <select id="user_id" name="user_id" class="mt-1 block w-full rounded-lg border-slate-300 bg-white text-sm shadow-sm focus:border-emerald-500 focus:ring-emerald-500">
            <option value="">{{ __('Select user') }}</option>
            @foreach ($users as $user)
                <option value="{{ $user->id }}" @selected((string) $selectedUser === (string) $user->id)>
                    {{ $user->name }} - {{ $user->email }}
                </option>
            @endforeach
        </select>
        <x-input-error :messages="$errors->get('user_id')" class="mt-2" />
    </div>

    <div>
        <x-input-label for="customer_number" :value="__('Customer Number')" />
        <x-text-input id="customer_number" name="customer_number" type="text" class="mt-1 block w-full rounded-lg border-slate-300 text-sm shadow-sm focus:border-emerald-500 focus:ring-emerald-500" :value="old('customer_number', $customer?->customer_number)" required />
        <x-input-error :messages="$errors->get('customer_number')" class="mt-2" />
    </div>

    <div>
        <x-input-label for="first_name" :value="__('First Name')" />
        <x-text-input id="first_name" name="first_name" type="text" class="mt-1 block w-full rounded-lg border-slate-300 text-sm shadow-sm focus:border-emerald-500 focus:ring-emerald-500" :value="old('first_name', $customer?->first_name)" required />
        <x-input-error :messages="$errors->get('first_name')" class="mt-2" />
    </div>

    <div>
        <x-input-label for="last_name" :value="__('Last Name')" />
        <x-text-input id="last_name" name="last_name" type="text" class="mt-1 block w-full rounded-lg border-slate-300 text-sm shadow-sm focus:border-emerald-500 focus:ring-emerald-500" :value="old('last_name', $customer?->last_name)" required />
        <x-input-error :messages="$errors->get('last_name')" class="mt-2" />
    </div>

    <div>
        <x-input-label for="date_of_birth" :value="__('Date of Birth')" />
        <x-text-input id="date_of_birth" name="date_of_birth" type="date" class="mt-1 block w-full rounded-lg border-slate-300 text-sm shadow-sm focus:border-emerald-500 focus:ring-emerald-500" :value="old('date_of_birth', $customer?->date_of_birth?->toDateString())" />
        <x-input-error :messages="$errors->get('date_of_birth')" class="mt-2" />
    </div>

    <div>
        <x-input-label for="national_id" :value="__('National ID')" />
        <x-text-input id="national_id" name="national_id" type="text" class="mt-1 block w-full rounded-lg border-slate-300 text-sm shadow-sm focus:border-emerald-500 focus:ring-emerald-500" :value="old('national_id', $customer?->national_id)" required />
        <x-input-error :messages="$errors->get('national_id')" class="mt-2" />
    </div>

    <div>
        <x-input-label for="identity_document_type" :value="__('Identity Document Type')" />
        <select id="identity_document_type" name="identity_document_type" class="mt-1 block w-full rounded-lg border-slate-300 bg-white text-sm shadow-sm focus:border-emerald-500 focus:ring-emerald-500">
            <option value="">{{ __('Select document type') }}</option>
            @foreach ($identityDocumentTypes as $type)
                <option value="{{ $type->value }}" @selected($selectedIdentityDocumentType === $type->value)>{{ $type->label() }}</option>
            @endforeach
        </select>
        <x-input-error :messages="$errors->get('identity_document_type')" class="mt-2" />
    </div>

    <div>
        <x-input-label for="identity_document_number" :value="__('Identity Document Number')" />
        <x-text-input id="identity_document_number" name="identity_document_number" type="text" class="mt-1 block w-full rounded-lg border-slate-300 text-sm shadow-sm focus:border-emerald-500 focus:ring-emerald-500" :value="old('identity_document_number', $customer?->identity_document_number)" />
        <x-input-error :messages="$errors->get('identity_document_number')" class="mt-2" />
    </div>

    <div>
        <x-input-label for="identity_document_country" :value="__('Issuing Country')" />
        <x-text-input id="identity_document_country" name="identity_document_country" type="text" maxlength="2" class="mt-1 block w-full rounded-lg border-slate-300 text-sm uppercase shadow-sm focus:border-emerald-500 focus:ring-emerald-500" :value="old('identity_document_country', $customer?->identity_document_country)" placeholder="SY" />
        <x-input-error :messages="$errors->get('identity_document_country')" class="mt-2" />
    </div>

    <div>
        <x-input-label for="identity_document_expires_at" :value="__('Document Expiry Date')" />
        <x-text-input id="identity_document_expires_at" name="identity_document_expires_at" type="date" class="mt-1 block w-full rounded-lg border-slate-300 text-sm shadow-sm focus:border-emerald-500 focus:ring-emerald-500" :value="old('identity_document_expires_at', $customer?->identity_document_expires_at?->toDateString())" />
        <x-input-error :messages="$errors->get('identity_document_expires_at')" class="mt-2" />
    </div>
        </div>
    </section>

    <section>
        <div class="mb-4 border-b border-slate-200 pb-3">
            <h4 class="text-sm font-semibold text-slate-950">{{ __('Contact') }}</h4>
            <p class="mt-1 text-sm text-slate-500">{{ __('Reachability and residence information.') }}</p>
        </div>

        <div class="grid grid-cols-1 gap-5 md:grid-cols-2">

    <div>
        <x-input-label for="phone_number" :value="__('Phone')" />
        <x-text-input id="phone_number" name="phone_number" type="text" class="mt-1 block w-full rounded-lg border-slate-300 text-sm shadow-sm focus:border-emerald-500 focus:ring-emerald-500" :value="old('phone_number', $customer?->phone_number)" />
        <x-input-error :messages="$errors->get('phone_number')" class="mt-2" />
    </div>

    <div>
        <x-input-label for="address" :value="__('Address')" />
        <x-text-input id="address" name="address" type="text" class="mt-1 block w-full rounded-lg border-slate-300 text-sm shadow-sm focus:border-emerald-500 focus:ring-emerald-500" :value="old('address', $customer?->address)" />
        <x-input-error :messages="$errors->get('address')" class="mt-2" />
    </div>
        </div>
    </section>

    <section>
        <div class="mb-4 border-b border-slate-200 pb-3">
            <h4 class="text-sm font-semibold text-slate-950">{{ __('Compliance') }}</h4>
            <p class="mt-1 text-sm text-slate-500">{{ __('Operational status, KYC result and risk class.') }}</p>
        </div>

        <div class="grid grid-cols-1 gap-5 md:grid-cols-3">

    <div>
        <x-input-label for="status" :value="__('Status')" />
        <select id="status" name="status" class="mt-1 block w-full rounded-lg border-slate-300 bg-white text-sm shadow-sm focus:border-emerald-500 focus:ring-emerald-500" required>
            @foreach ($customerStatuses as $status)
                <option value="{{ $status->value }}" @selected($selectedStatus === $status->value)>{{ $status->label() }}</option>
            @endforeach
        </select>
        <x-input-error :messages="$errors->get('status')" class="mt-2" />
    </div>

    <div>
        <x-input-label for="kyc_status" :value="__('KYC Status')" />
        <select id="kyc_status" name="kyc_status" class="mt-1 block w-full rounded-lg border-slate-300 bg-white text-sm shadow-sm focus:border-emerald-500 focus:ring-emerald-500" required>
            @foreach ($kycStatuses as $status)
                <option value="{{ $status->value }}" @selected($selectedKycStatus === $status->value)>{{ $status->label() }}</option>
            @endforeach
        </select>
        <x-input-error :messages="$errors->get('kyc_status')" class="mt-2" />
    </div>

    <div>
        <x-input-label for="risk_level" :value="__('Risk Level')" />
        <select id="risk_level" name="risk_level" class="mt-1 block w-full rounded-lg border-slate-300 bg-white text-sm shadow-sm focus:border-emerald-500 focus:ring-emerald-500" required>
            @foreach ($riskLevels as $level)
                <option value="{{ $level->value }}" @selected($selectedRiskLevel === $level->value)>{{ $level->label() }}</option>
            @endforeach
        </select>
        <x-input-error :messages="$errors->get('risk_level')" class="mt-2" />
    </div>

    <div>
        <x-input-label for="kyc_reference" :value="__('KYC Reference')" />
        <x-text-input id="kyc_reference" name="kyc_reference" type="text" class="mt-1 block w-full rounded-lg border-slate-300 text-sm shadow-sm focus:border-emerald-500 focus:ring-emerald-500" :value="old('kyc_reference', $customer?->kyc_reference)" placeholder="KYC-2026-0001" />
        <x-input-error :messages="$errors->get('kyc_reference')" class="mt-2" />
    </div>

    <div>
        <x-input-label for="kyc_reviewed_by" :value="__('Reviewed By')" />
        <select id="kyc_reviewed_by" name="kyc_reviewed_by" class="mt-1 block w-full rounded-lg border-slate-300 bg-white text-sm shadow-sm focus:border-emerald-500 focus:ring-emerald-500">
            <option value="">{{ __('Auto assign current user') }}</option>
            @foreach ($kycReviewers as $reviewer)
                <option value="{{ $reviewer->id }}" @selected((string) $selectedKycReviewer === (string) $reviewer->id)>
                    {{ $reviewer->name }} - {{ $reviewer->email }}
                </option>
            @endforeach
        </select>
        <x-input-error :messages="$errors->get('kyc_reviewed_by')" class="mt-2" />
    </div>

    <div>
        <x-input-label for="kyc_reviewed_at" :value="__('Reviewed At')" />
        <x-text-input id="kyc_reviewed_at" name="kyc_reviewed_at" type="datetime-local" class="mt-1 block w-full rounded-lg border-slate-300 text-sm shadow-sm focus:border-emerald-500 focus:ring-emerald-500" :value="$selectedKycReviewedAt" />
        <x-input-error :messages="$errors->get('kyc_reviewed_at')" class="mt-2" />
    </div>

    <div class="md:col-span-3">
        <x-input-label for="kyc_rejection_reason" :value="__('Rejection Reason')" />
        <textarea id="kyc_rejection_reason" name="kyc_rejection_reason" rows="4" class="mt-1 block w-full rounded-lg border-slate-300 text-sm shadow-sm focus:border-emerald-500 focus:ring-emerald-500" placeholder="{{ __('Required when KYC is rejected') }}">{{ old('kyc_rejection_reason', $customer?->kyc_rejection_reason) }}</textarea>
        <x-input-error :messages="$errors->get('kyc_rejection_reason')" class="mt-2" />
    </div>
        </div>
    </section>
</div>

<div class="flex items-center justify-end gap-3 border-t border-slate-200 pt-6">
    <a href="{{ route('customers.index') }}" class="rounded-lg px-4 py-2 text-sm font-semibold text-slate-600 transition hover:bg-slate-100 hover:text-slate-900">{{ __('Cancel') }}</a>
    <button type="submit" class="inline-flex items-center justify-center rounded-lg bg-slate-950 px-4 py-2.5 text-sm font-semibold text-white shadow-sm shadow-slate-900/20 transition hover:bg-slate-800 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2">
        {{ $submitLabel }}
    </button>
</div>
