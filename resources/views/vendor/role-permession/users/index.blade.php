@extends('role-permession::layouts.ui')

@section('title', __('Assign Roles to Users'))

@section('content')
    @php
        $namePrefix = config('role-permession.ui.route_name_prefix', 'role-permession.');
    @endphp

    <!-- Metrics -->
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
        <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-xs">
            <p class="text-xs font-semibold uppercase text-slate-500">{{ __('Total Users') }}</p>
            <p class="mt-2 text-3xl font-semibold text-slate-950 font-mono" dir="ltr">{{ number_format($users->total()) }}</p>
        </div>
        <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-xs">
            <p class="text-xs font-semibold uppercase text-slate-500">{{ __('Roles') }}</p>
            <p class="mt-2 text-3xl font-semibold text-slate-950 font-mono" dir="ltr">{{ $roles->count() }}</p>
        </div>
        <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-xs">
            <p class="text-xs font-semibold uppercase text-slate-500">{{ __('Page') }}</p>
            <p class="mt-2 text-3xl font-semibold text-slate-950 font-mono" dir="ltr">
                {{ $users->currentPage() }} / {{ max(1, $users->lastPage()) }}
            </p>
        </div>
    </div>

    <!-- Main Card -->
    <div class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-xs">
        <!-- Header & Search -->
        <div class="flex flex-col gap-4 border-b border-slate-200 px-5 py-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h3 class="text-base font-semibold text-slate-950">{{ __('User Roles Assignment') }}</h3>
                <p class="text-xs text-slate-500 mt-0.5">{{ __('Assign system roles to users to grant corresponding module access') }}</p>
            </div>

            <form method="GET" action="{{ route($namePrefix.'users.index') }}" class="flex items-center gap-2">
                <div class="relative">
                    <input type="search" name="q" value="{{ $q }}"
                           placeholder="{{ __('Search user by name or email...') }}"
                           class="w-56 sm:w-64 rounded-lg border-slate-300 text-xs py-1.5 px-3 focus:ring-emerald-500 focus:border-emerald-500 shadow-xs">
                </div>
                <button type="submit"
                        class="inline-flex items-center gap-1.5 rounded-lg bg-emerald-600 px-3.5 py-1.5 text-xs font-semibold text-white hover:bg-emerald-500 transition shadow-xs">
                    <svg class="h-3.5 w-3.5" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M9 3.5a5.5 5.5 0 1 0 0 11 5.5 5.5 0 0 0 0-11ZM2 9a7 7 0 1 1 12.452 4.391l3.328 3.329a.75.75 0 1 1-1.06 1.06l-3.329-3.328A7 7 0 0 1 2 9Z" clip-rule="evenodd" />
                    </svg>
                    <span>{{ __('Search') }}</span>
                </button>
                @if ($q)
                    <a href="{{ route($namePrefix.'users.index') }}"
                       class="text-xs font-semibold text-slate-500 hover:text-slate-800 transition">
                        {{ __('Reset') }}
                    </a>
                @endif
            </form>
        </div>

        @if ($users->isEmpty())
            <div class="p-12 text-center">
                <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-xl bg-slate-100 text-slate-400 mb-3">
                    <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z" />
                    </svg>
                </div>
                <h4 class="text-sm font-semibold text-slate-900">{{ __('No users found.') }}</h4>
                <p class="mt-1 text-xs text-slate-500 max-w-sm mx-auto">
                    {{ __('No users found matching your search.') }}
                </p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-200 text-start text-xs">
                    <thead class="bg-slate-50 text-slate-600 font-semibold">
                        <tr>
                            <th class="px-5 py-3 text-start w-1/4">{{ __('User Details') }}</th>
                            <th class="px-5 py-3 text-start">{{ __('Assigned Roles') }}</th>
                            <th class="px-5 py-3 text-end w-28">{{ __('Action') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 bg-white">
                        @foreach ($users as $user)
                            <tr class="hover:bg-slate-50/60 transition">
                                <!-- User Info -->
                                <td class="px-5 py-4 whitespace-nowrap">
                                    <div class="flex items-center gap-3">
                                        <div class="flex h-9 w-9 items-center justify-center rounded-full bg-emerald-50 text-emerald-700 ring-1 ring-emerald-200 font-bold text-xs shrink-0">
                                            {{ Str::upper(Str::substr($user->name ?? 'U', 0, 1)) }}
                                        </div>
                                        <div class="min-w-0">
                                            <p class="font-semibold text-slate-900 truncate">{{ $user->name ?? ('#'.$user->getKey()) }}</p>
                                            <p class="text-[11px] text-slate-500 font-mono truncate" dir="ltr">{{ $user->email }}</p>
                                        </div>
                                    </div>
                                </td>

                                <!-- Roles Selection Form -->
                                <td class="px-5 py-4">
                                    <form method="POST"
                                          action="{{ route($namePrefix.'users.update', $user->getKey()) }}"
                                          id="user-roles-{{ $user->getKey() }}">
                                        @csrf
                                        @method('PUT')

                                        <div class="flex flex-wrap gap-2">
                                            @forelse ($roles as $role)
                                                @php
                                                    $isAssigned = $user->roles->contains('id', $role->id);
                                                @endphp
                                                <label class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg border text-xs font-semibold cursor-pointer transition select-none {{ $isAssigned ? 'bg-emerald-50 border-emerald-300 text-emerald-800 ring-1 ring-emerald-300' : 'bg-slate-50 border-slate-200 text-slate-600 hover:bg-white' }}">
                                                    <input type="checkbox"
                                                           name="roles[]"
                                                           value="{{ $role->id }}"
                                                           @checked($isAssigned)
                                                           class="rounded border-slate-300 text-emerald-600 focus:ring-emerald-500">
                                                    <span>{{ $role->name }}</span>
                                                </label>
                                            @empty
                                                <span class="text-xs text-slate-400 italic">{{ __('No roles yet') }}</span>
                                            @endforelse
                                        </div>
                                    </form>
                                </td>

                                <!-- Save Button -->
                                <td class="px-5 py-4 whitespace-nowrap text-end">
                                    @if ($roles->isNotEmpty())
                                        <button type="submit"
                                                form="user-roles-{{ $user->getKey() }}"
                                                class="inline-flex items-center gap-1.5 rounded-lg bg-emerald-600 px-3.5 py-1.5 text-xs font-semibold text-white shadow-xs hover:bg-emerald-500 transition">
                                            <svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" />
                                            </svg>
                                            <span>{{ __('Save') }}</span>
                                        </button>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="border-t border-slate-200 bg-white px-5 py-3">
                {{ $users->links() }}
            </div>
        @endif
    </div>
@endsection
