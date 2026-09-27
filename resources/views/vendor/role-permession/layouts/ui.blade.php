<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <p class="text-xs font-semibold uppercase text-emerald-700">{{ __('Access Control & Security') }}</p>
                <h2 class="mt-1 text-2xl font-semibold text-slate-950 leading-tight">
                    @yield('title', __('Roles & Permissions'))
                </h2>
            </div>
            <div class="flex items-center gap-2">
                @canAbility('roles.view')
                    <a href="{{ route(config('role-permession.ui.route_name_prefix', 'role-permession.').'roles.index') }}"
                       class="inline-flex items-center gap-2 rounded-lg px-3.5 py-2 text-xs font-semibold transition {{ request()->routeIs(config('role-permession.ui.route_name_prefix', 'role-permession.').'roles.*') ? 'bg-emerald-600 text-white shadow-sm' : 'bg-white text-slate-700 border border-slate-300 hover:bg-slate-50' }}">
                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 1 0-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 0 0 2.25-2.25v-6.75a2.25 2.25 0 0 0-2.25-2.25H6.75a2.25 2.25 0 0 0-2.25 2.25v6.75a2.25 2.25 0 0 0 2.25 2.25Z" />
                        </svg>
                        {{ __('Roles') }}
                    </a>
                @endcanAbility

                @canAbility('roles.update')
                    <a href="{{ route(config('role-permession.ui.route_name_prefix', 'role-permession.').'users.index') }}"
                       class="inline-flex items-center gap-2 rounded-lg px-3.5 py-2 text-xs font-semibold transition {{ request()->routeIs(config('role-permession.ui.route_name_prefix', 'role-permession.').'users.*') ? 'bg-emerald-600 text-white shadow-sm' : 'bg-white text-slate-700 border border-slate-300 hover:bg-slate-50' }}">
                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M18 18.72a9.094 9.094 0 0 0 3.741-.479 3 3 0 0 0-4.682-2.72m.94 3.198.001.031c0 .225-.012.447-.037.666A11.944 11.944 0 0 1 12 21c-2.17 0-4.207-.576-5.963-1.584A6.062 6.062 0 0 1 6 18.719m12 0a5.971 5.971 0 0 0-.941-3.197m0 0A5.995 5.995 0 0 0 12 12.75a5.995 5.995 0 0 0-5.058 2.772m0 0a3 3 0 0 0-4.681 2.72 8.986 8.986 0 0 0 3.74.477m.999-3.199a5.971 5.971 0 0 0-.94 3.197M15 6.75a3 3 0 1 1-6 0 3 3 0 0 1 6 0Zm6 3a2.25 2.25 0 1 1-4.5 0 2.25 2.25 0 0 1 4.5 0Zm-13.5 0a2.25 2.25 0 1 1-4.5 0 2.25 2.25 0 0 1 4.5 0Z" />
                        </svg>
                        {{ __('Assign Roles') }}
                    </a>
                @endcanAbility
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
            @if (session('role_permession_success') || session('status'))
                <div class="rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-800 flex items-center gap-2">
                    <svg class="h-5 w-5 text-emerald-600 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    <span>{{ __(session('role_permession_success') ?? session('status')) }}</span>
                </div>
            @endif

            @if ($errors->any())
                <div class="rounded-xl border border-rose-200 bg-rose-50 p-4 text-sm text-rose-800 space-y-1">
                    <p class="font-semibold">{{ __('Please correct the following errors:') }}</p>
                    <ul class="list-disc list-inside text-xs space-y-0.5">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @yield('content')
        </div>
    </div>
</x-app-layout>
