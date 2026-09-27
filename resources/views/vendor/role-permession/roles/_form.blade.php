@php
    $namePrefix = config('role-permession.ui.route_name_prefix', 'role-permession.');
@endphp

<!-- Role Details Card -->
<div class="rounded-xl border border-slate-200 bg-white p-6 shadow-xs mb-6">
    <div class="mb-4 border-b border-slate-200 pb-3">
        <h4 class="text-sm font-semibold text-slate-950">{{ __('Role Name') }}</h4>
        <p class="mt-1 text-xs text-slate-500">{{ __('Role identifier and display name') }}</p>
    </div>

    <div>
        <label for="name" class="block text-xs font-semibold uppercase text-slate-700 mb-1.5">
            {{ __('Role Name') }} <span class="text-rose-500">*</span>
        </label>
        <input id="name" type="text" name="name" value="{{ old('name', $role->name ?? '') }}" required maxlength="255"
               placeholder="{{ __('e.g. Compliance Officer, Teller Supervisor') }}"
               class="w-full max-w-md rounded-lg border-slate-300 text-sm focus:ring-emerald-500 focus:border-emerald-500 shadow-xs">
        @error('name')
            <p class="text-xs text-rose-600 mt-1.5">{{ $message }}</p>
        @enderror
    </div>
</div>

<!-- Abilities Configuration Card -->
<div class="rounded-xl border border-slate-200 bg-white p-6 shadow-xs mb-6">
    <div class="mb-5 pb-3 border-b border-slate-200 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
        <div>
            <h4 class="text-sm font-semibold text-slate-950">{{ __('System Abilities') }}</h4>
            <p class="mt-1 text-xs text-slate-500">{{ __('Choose Allow, Deny, or leave as Inherit (not stored).') }}</p>
        </div>
        <div class="flex items-center gap-3 text-xs">
            <span class="inline-flex items-center gap-1.5 text-emerald-700 font-medium">
                <span class="h-2 w-2 rounded-full bg-emerald-500"></span> {{ __('Allow') }}
            </span>
            <span class="inline-flex items-center gap-1.5 text-rose-700 font-medium">
                <span class="h-2 w-2 rounded-full bg-rose-500"></span> {{ __('Deny') }}
            </span>
            <span class="inline-flex items-center gap-1.5 text-slate-500 font-medium">
                <span class="h-2 w-2 rounded-full bg-slate-300"></span> {{ __('Inherit') }}
            </span>
        </div>
    </div>

    <div class="space-y-5">
        @forelse ($grouped as $group => $abilities)
            <div class="rounded-xl border border-slate-200 overflow-hidden shadow-xs">
                <!-- Group Header -->
                <div class="bg-slate-50 px-4 py-3 border-b border-slate-200 flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <span class="h-2 w-2 rounded-full bg-emerald-500"></span>
                        <span class="font-bold text-xs uppercase tracking-wider text-slate-900">{{ __($group) }}</span>
                    </div>
                    <span class="text-[11px] font-medium text-slate-500 font-mono">{{ count($abilities) }} {{ __('Abilities') }}</span>
                </div>

                <!-- Group Abilities List -->
                <div class="divide-y divide-slate-100 bg-white">
                    @foreach ($abilities as $code => $label)
                        @php
                            $current = old("abilities.$code", $selected[$code] ?? 'inherit');
                        @endphp
                        <div class="p-3.5 sm:px-5 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 hover:bg-slate-50/50 transition">
                            <div class="min-w-0 flex-1">
                                <p class="text-sm font-semibold text-slate-900">{{ __($label) }}</p>
                                <span class="text-[11px] font-mono text-slate-400 block mt-0.5" dir="ltr">{{ $code }}</span>
                            </div>

                            <div class="flex items-center gap-2 shrink-0">
                                <!-- Allow -->
                                <label class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg border text-xs font-semibold cursor-pointer transition select-none {{ $current === 'allow' ? 'bg-emerald-50 border-emerald-300 text-emerald-700 ring-1 ring-emerald-300' : 'bg-white border-slate-200 text-slate-600 hover:bg-slate-50' }}">
                                    <input type="radio" name="abilities[{{ $code }}]" value="allow" @checked($current === 'allow') class="text-emerald-600 focus:ring-emerald-500">
                                    <span>{{ __('Allow') }}</span>
                                </label>

                                <!-- Deny -->
                                <label class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg border text-xs font-semibold cursor-pointer transition select-none {{ $current === 'deny' ? 'bg-rose-50 border-rose-300 text-rose-700 ring-1 ring-rose-300' : 'bg-white border-slate-200 text-slate-600 hover:bg-slate-50' }}">
                                    <input type="radio" name="abilities[{{ $code }}]" value="deny" @checked($current === 'deny') class="text-rose-600 focus:ring-rose-500">
                                    <span>{{ __('Deny') }}</span>
                                </label>

                                <!-- Inherit -->
                                <label class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg border text-xs font-medium cursor-pointer transition select-none {{ $current === 'inherit' ? 'bg-slate-100 border-slate-300 text-slate-800' : 'bg-white border-slate-200 text-slate-500 hover:bg-slate-50' }}">
                                    <input type="radio" name="abilities[{{ $code }}]" value="inherit" @checked($current === 'inherit') class="text-slate-500 focus:ring-slate-400">
                                    <span>{{ __('Inherit') }}</span>
                                </label>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @empty
            <div class="p-8 text-center text-xs text-slate-500">
                {{ __('No abilities catalog found in system configuration.') }}
            </div>
        @endforelse
    </div>
</div>
