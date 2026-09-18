@php
    $selectedStatus = old('status', $accountType?->status?->value ?? 'active');
@endphp

<div class="grid grid-cols-1 gap-5 md:grid-cols-2">
    <div>
        <x-input-label for="code" :value="__('Code')" />
        <x-text-input id="code" name="code" type="text" maxlength="20" class="mt-1 block w-full uppercase" :value="old('code', $accountType?->code)" required />
        <x-input-error :messages="$errors->get('code')" class="mt-2" />
    </div>
    <div>
        <x-input-label for="name" :value="__('Name')" />
        <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" :value="old('name', $accountType?->name)" required />
        <x-input-error :messages="$errors->get('name')" class="mt-2" />
    </div>
    <div>
        <x-input-label for="currency" :value="__('Currency')" />
        <x-text-input id="currency" name="currency" type="text" maxlength="3" class="mt-1 block w-full uppercase" :value="old('currency', $accountType?->currency ?? 'SYP')" required />
        <x-input-error :messages="$errors->get('currency')" class="mt-2" />
    </div>
    <div>
        <x-input-label for="status" :value="__('Status')" />
        <select id="status" name="status" class="mt-1 block w-full rounded-lg border-slate-300 bg-white shadow-sm focus:border-emerald-500 focus:ring-emerald-500" required>
            @foreach ($statuses as $status)
                <option value="{{ $status->value }}" @selected($selectedStatus === $status->value)>{{ $status->label() }}</option>
            @endforeach
        </select>
        <x-input-error :messages="$errors->get('status')" class="mt-2" />
    </div>
    <div class="md:col-span-2">
        <x-input-label for="description" :value="__('Description')" />
        <textarea id="description" name="description" rows="4" class="mt-1 block w-full rounded-lg border-slate-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500">{{ old('description', $accountType?->description) }}</textarea>
        <x-input-error :messages="$errors->get('description')" class="mt-2" />
    </div>
</div>

<div class="mt-6 flex items-center justify-end gap-3">
    <a href="{{ route('account-types.index') }}" class="rounded-lg px-4 py-2 text-sm font-semibold text-slate-600 hover:bg-slate-100">{{ __('Cancel') }}</a>
    <button type="submit" class="inline-flex items-center rounded-lg bg-slate-950 px-4 py-2.5 text-sm font-semibold text-white hover:bg-slate-800">{{ $submitLabel }}</button>
</div>
