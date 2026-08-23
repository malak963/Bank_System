@props([
    'user',
    'roles',
    'submitLabel',
])

@php
    $selectedRole = old('role', $user?->role?->value ?? 'customer');
    $selectedActive = old('is_active', $user?->is_active ?? true);
@endphp

<div class="space-y-8">
    <section>
        <div class="mb-4 border-b border-slate-200 pb-3">
            <h4 class="text-sm font-semibold text-slate-950">{{ __('Identity') }}</h4>
            <p class="mt-1 text-sm text-slate-500">{{ __('Personal and contact identity for authentication.') }}</p>
        </div>

        <div class="grid grid-cols-1 gap-5 md:grid-cols-2">
    <div>
        <x-input-label for="name" :value="__('Name')" />
        <x-text-input id="name" name="name" type="text" class="mt-1 block w-full rounded-lg border-slate-300 text-sm shadow-sm focus:border-emerald-500 focus:ring-emerald-500" :value="old('name', $user?->name)" required />
        <x-input-error :messages="$errors->get('name')" class="mt-2" />
    </div>

    <div>
        <x-input-label for="email" :value="__('Email')" />
        <x-text-input id="email" name="email" type="email" class="mt-1 block w-full rounded-lg border-slate-300 text-sm shadow-sm focus:border-emerald-500 focus:ring-emerald-500" :value="old('email', $user?->email)" required />
        <x-input-error :messages="$errors->get('email')" class="mt-2" />
    </div>

    <div>
        <x-input-label for="phone" :value="__('Phone')" />
        <x-text-input id="phone" name="phone" type="tel" class="mt-1 block w-full rounded-lg border-slate-300 text-sm shadow-sm focus:border-emerald-500 focus:ring-emerald-500" :value="old('phone', $user?->phone)" required />
        <x-input-error :messages="$errors->get('phone')" class="mt-2" />
    </div>
        </div>
    </section>

    <section>
        <div class="mb-4 border-b border-slate-200 pb-3">
            <h4 class="text-sm font-semibold text-slate-950">{{ __('Access') }}</h4>
            <p class="mt-1 text-sm text-slate-500">{{ __('Role assignment and account availability.') }}</p>
        </div>

        <div class="grid grid-cols-1 gap-5 md:grid-cols-2">

    <div>
        <x-input-label for="role" :value="__('Role')" />
        <select id="role" name="role" class="mt-1 block w-full rounded-lg border-slate-300 bg-white text-sm shadow-sm focus:border-emerald-500 focus:ring-emerald-500" required>
            @foreach ($roles as $role)
                <option value="{{ $role->value }}" @selected($selectedRole === $role->value)>{{ $role->label() }}</option>
            @endforeach
        </select>
        <x-input-error :messages="$errors->get('role')" class="mt-2" />
    </div>

    <div>
        <x-input-label for="is_active" :value="__('Account Status')" />
        <input type="hidden" name="is_active" value="0">
        <label for="is_active" class="mt-1 flex min-h-[42px] items-center rounded-lg border border-slate-300 bg-white px-3 shadow-sm">
            <input id="is_active" type="checkbox" name="is_active" value="1" class="rounded border-slate-300 text-emerald-600 shadow-sm focus:ring-emerald-500" @checked((bool) $selectedActive)>
            <span class="ms-2 text-sm font-medium text-slate-700">{{ __('Active') }}</span>
        </label>
        <x-input-error :messages="$errors->get('is_active')" class="mt-2" />
    </div>
        </div>
    </section>

    <section>
        <div class="mb-4 border-b border-slate-200 pb-3">
            <h4 class="text-sm font-semibold text-slate-950">{{ __('Credentials') }}</h4>
            <p class="mt-1 text-sm text-slate-500">{{ $user === null ? __('Set the initial password for this account.') : __('Leave password fields empty to keep the current password.') }}</p>
        </div>

        <div class="grid grid-cols-1 gap-5 md:grid-cols-2">

    <div>
        <x-input-label for="password" :value="__('Password')" />
        <x-text-input id="password" name="password" type="password" class="mt-1 block w-full rounded-lg border-slate-300 text-sm shadow-sm focus:border-emerald-500 focus:ring-emerald-500" @if ($user === null) required @endif autocomplete="new-password" />
        <x-input-error :messages="$errors->get('password')" class="mt-2" />
    </div>

    <div>
        <x-input-label for="password_confirmation" :value="__('Confirm Password')" />
        <x-text-input id="password_confirmation" name="password_confirmation" type="password" class="mt-1 block w-full rounded-lg border-slate-300 text-sm shadow-sm focus:border-emerald-500 focus:ring-emerald-500" @if ($user === null) required @endif autocomplete="new-password" />
        <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
    </div>
        </div>
    </section>
</div>

<div class="flex items-center justify-end gap-3 border-t border-slate-200 pt-6">
    <a href="{{ route('users.index') }}" class="rounded-lg px-4 py-2 text-sm font-semibold text-slate-600 transition hover:bg-slate-100 hover:text-slate-900">{{ __('Cancel') }}</a>
    <button type="submit" class="inline-flex items-center justify-center rounded-lg bg-slate-950 px-4 py-2.5 text-sm font-semibold text-white shadow-sm shadow-slate-900/20 transition hover:bg-slate-800 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2">
        {{ $submitLabel }}
    </button>
</div>
