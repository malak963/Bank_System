<x-app-layout>
    <x-slot name="header"><div><p class="text-xs font-semibold uppercase text-emerald-700">{{ __('Branch Operations') }}</p><h2 class="mt-1 text-2xl font-semibold text-slate-950">{{ __('Create New Branch') }}</h2></div></x-slot>
    <div class="mx-auto max-w-3xl px-4 py-8 sm:px-6 lg:px-8">
        @if ($errors->any())<div class="mb-6 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">{{ $errors->first() }}</div>@endif
        <form method="POST" action="{{ route('branches.store') }}" class="space-y-6 rounded-lg border border-slate-200 bg-white p-6 shadow-sm">
            @csrf
            <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
                <div><x-input-label for="code" :value="__('Branch Code')" /><x-text-input id="code" name="code" type="text" class="mt-1 block w-full" :value="old('code')" required /><x-input-error :messages="$errors->get('code')" class="mt-2" /></div>
                <div><x-input-label for="name" :value="__('Branch Name')" /><x-text-input id="name" name="name" type="text" class="mt-1 block w-full" :value="old('name')" required /><x-input-error :messages="$errors->get('name')" class="mt-2" /></div>
            </div>
            <div><x-input-label for="address" :value="__('Address')" /><textarea id="address" name="address" class="mt-1 block w-full rounded-lg border-slate-300 bg-white shadow-sm focus:border-emerald-500 focus:ring-emerald-500" rows="3">{{ old('address') }}</textarea><x-input-error :messages="$errors->get('address')" class="mt-2" /></div>
            <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
                <div><x-input-label for="phone" :value="__('Phone')" /><x-text-input id="phone" name="phone" type="text" class="mt-1 block w-full" :value="old('phone')" /><x-input-error :messages="$errors->get('phone')" class="mt-2" /></div>
                <div><x-input-label for="email" :value="__('Email')" /><x-text-input id="email" name="email" type="email" class="mt-1 block w-full" :value="old('email')" /><x-input-error :messages="$errors->get('email')" class="mt-2" /></div>
            </div>
            <div><x-input-label for="manager_id" :value="__('Manager')" /><select id="manager_id" name="manager_id" class="mt-1 block w-full rounded-lg border-slate-300 bg-white shadow-sm focus:border-emerald-500 focus:ring-emerald-500"><option value="">{{ __('Select manager (optional)') }}</option>@foreach ($managers as $manager)<option value="{{ $manager->id }}" @selected(old('manager_id') == $manager->id)>{{ $manager->name }} ({{ $manager->email }})</option>@endforeach</select><x-input-error :messages="$errors->get('manager_id')" class="mt-2" /></div>
            <div class="flex items-center justify-end gap-3"><a href="{{ route('branches.index') }}" class="rounded-lg px-4 py-2 text-sm font-semibold text-slate-600 hover:bg-slate-100">{{ __('Cancel') }}</a><button type="submit" class="rounded-lg bg-slate-950 px-4 py-2.5 text-sm font-semibold text-white hover:bg-slate-800">{{ __('Create Branch') }}</button></div>
        </form>
    </div>
</x-app-layout>
