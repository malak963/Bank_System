@extends('role-permession::layouts.ui')

@section('title', __('Roles & Permissions'))

@section('content')
    @php
        $namePrefix = config('role-permession.ui.route_name_prefix', 'role-permession.');
    @endphp

    <!-- Metric Overview Cards -->
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
        <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-xs">
            <p class="text-xs font-semibold uppercase text-slate-500">{{ __('Total Roles') }}</p>
            <p class="mt-2 text-3xl font-semibold text-slate-950 font-mono" dir="ltr">{{ $roles->count() }}</p>
        </div>
        <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-xs">
            <p class="text-xs font-semibold uppercase text-slate-500">{{ __('Total Abilities') }}</p>
            <p class="mt-2 text-3xl font-semibold text-slate-950 font-mono" dir="ltr">{{ count($catalog) }}</p>
        </div>
        <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-xs">
            <p class="text-xs font-semibold uppercase text-slate-500">{{ __('Policy Status') }}</p>
            <div class="mt-2 flex items-center gap-2">
                <span class="h-2.5 w-2.5 rounded-full bg-emerald-500 animate-pulse"></span>
                <span class="text-sm font-semibold text-emerald-800">{{ __('Active & Enforced') }}</span>
            </div>
        </div>
    </div>

    <!-- Main Roles Card -->
    <div class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-xs">
        <div class="flex flex-col gap-4 border-b border-slate-200 px-5 py-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h3 class="text-base font-semibold text-slate-950">{{ __('Roles Directory') }}</h3>
                <p class="text-xs text-slate-500 mt-0.5">{{ __('Manage system roles, assign granular abilities and define user access levels') }}</p>
            </div>

            @canAbility('roles.create')
                <a href="{{ route($namePrefix.'roles.create') }}"
                   class="btn btn-primary">
                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                    </svg>
                    <span>{{ __('New Role') }}</span>
                </a>
            @endcanAbility
        </div>

        @if ($roles->isEmpty())
            <div class="p-12 text-center">
                <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-xl bg-slate-100 text-slate-400 mb-3">
                    <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 1 0-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 0 0 2.25-2.25v-6.75a2.25 2.25 0 0 0-2.25-2.25H6.75a2.25 2.25 0 0 0-2.25 2.25v6.75a2.25 2.25 0 0 0 2.25 2.25Z" />
                    </svg>
                </div>
                <h4 class="text-sm font-semibold text-slate-900">{{ __('No roles yet') }}</h4>
                <p class="mt-1 text-xs text-slate-500 max-w-sm mx-auto">
                    {{ __('No roles created yet. Get started by adding your first system role.') }}
                </p>
                @canAbility('roles.create')
                    <div class="mt-4">
                        <a href="{{ route($namePrefix.'roles.create') }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold bg-emerald-600 text-white hover:bg-emerald-500 transition">
                            + {{ __('Create Role') }}
                        </a>
                    </div>
                @endcanAbility
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-200 text-start text-xs">
                    <thead class="bg-slate-50 text-slate-600 font-semibold">
                        <tr>
                            <th class="px-5 py-3 text-start">{{ __('Role Name') }}</th>
                            <th class="px-5 py-3 text-start">{{ __('Abilities') }}</th>
                            <th class="px-5 py-3 text-end">{{ __('Action') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 bg-white">
                        @foreach ($roles as $role)
                            <tr class="hover:bg-slate-50/60 transition">
                                <td class="px-5 py-4 whitespace-nowrap">
                                    <div class="flex items-center gap-3">
                                        <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-emerald-50 text-emerald-700 ring-1 ring-emerald-200 font-bold text-xs">
                                            {{ Str::upper(Str::substr($role->name, 0, 1)) }}
                                        </div>
                                        <div>
                                            <p class="font-semibold text-slate-900">{{ $role->name }}</p>
                                            <span class="text-[11px] text-slate-400 font-mono">#{{ $role->id }}</span>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-5 py-4 whitespace-nowrap">
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 ring-1 ring-emerald-200">
                                        <svg class="h-3.5 w-3.5" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 1 0 0-16 8 8 0 0 0 0 16Zm3.857-9.809a.75.75 0 0 0-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 1 0-1.06 1.061l2.5 2.5a.75.75 0 0 0 1.137-.089l4-5.5Z" clip-rule="evenodd" />
                                        </svg>
                                        {{ $role->abilities_count }} {{ __('Abilities Set') }}
                                    </span>
                                </td>
                                <td class="px-5 py-4 whitespace-nowrap text-end">
                                    <div class="flex items-center justify-end gap-2">
                                        @canAbility('roles.update')
                                            <a href="{{ route($namePrefix.'roles.edit', $role) }}"
                                               class="inline-flex items-center gap-1 rounded-lg border border-slate-200 bg-white px-2.5 py-1.5 text-xs font-semibold text-slate-700 hover:bg-slate-50 hover:text-emerald-700 transition">
                                                <svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125" />
                                                </svg>
                                                <span>{{ __('Edit') }}</span>
                                            </a>
                                        @endcanAbility

                                        @canAbility('roles.delete')
                                            <form method="POST"
                                                  action="{{ route($namePrefix.'roles.destroy', $role) }}"
                                                  onsubmit="return confirm('{{ __('Are you sure you want to delete this role?') }}')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                        class="inline-flex items-center gap-1 rounded-lg border border-rose-200 bg-white px-2.5 py-1.5 text-xs font-semibold text-rose-600 hover:bg-rose-50 transition">
                                                    <svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                                                    </svg>
                                                    <span>{{ __('Delete') }}</span>
                                                </button>
                                            </form>
                                        @endcanAbility
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
@endsection
