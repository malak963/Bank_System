<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <p class="text-xs font-semibold uppercase text-emerald-700">{{ __('Access Management') }}</p>
                <h2 class="mt-1 text-2xl font-semibold text-slate-950 leading-tight">
                    {{ __('Users') }}
                </h2>
            </div>
            <a href="{{ route('users.create') }}" class="inline-flex items-center justify-center gap-2 rounded-lg bg-slate-950 px-4 py-2.5 text-sm font-semibold text-white shadow-sm shadow-slate-900/20 transition hover:bg-slate-800 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2">
                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 5v14M5 12h14" />
                </svg>
                {{ __('New User') }}
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            @if (session('status'))
                <div class="rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-800">
                    {{ session('status') }}
                </div>
            @endif

            <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
                <div class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
                    <p class="text-xs font-semibold uppercase text-slate-500">{{ __('Total Users') }}</p>
                    <p class="mt-2 text-3xl font-semibold text-slate-950">{{ number_format($users->total()) }}</p>
                </div>
                <div class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
                    <p class="text-xs font-semibold uppercase text-slate-500">{{ __('Visible Records') }}</p>
                    <p class="mt-2 text-3xl font-semibold text-slate-950">{{ number_format($users->count()) }}</p>
                </div>
                <div class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
                    <p class="text-xs font-semibold uppercase text-slate-500">{{ __('Directory Page') }}</p>
                    <p class="mt-2 text-3xl font-semibold text-slate-950">{{ number_format($users->currentPage()) }}</p>
                </div>
            </div>

            <div class="overflow-hidden rounded-lg border border-slate-200 bg-white shadow-sm">
                <div class="flex flex-col gap-2 border-b border-slate-200 px-5 py-4 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <h3 class="text-base font-semibold text-slate-950">{{ __('User Directory') }}</h3>
                        <p class="text-sm text-slate-500">{{ __('Access role, account state and authentication activity') }}</p>
                    </div>
                    <span class="text-sm font-medium text-slate-500">
                        {{ $users->firstItem() ?? 0 }}-{{ $users->lastItem() ?? 0 }} / {{ $users->total() }}
                    </span>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-200">
                        <thead class="bg-slate-50">
                            <tr>
                                <th class="px-5 py-3 text-left text-xs font-semibold uppercase text-slate-500">{{ __('Name') }}</th>
                                <th class="px-5 py-3 text-left text-xs font-semibold uppercase text-slate-500">{{ __('Email') }}</th>
                                <th class="px-5 py-3 text-left text-xs font-semibold uppercase text-slate-500">{{ __('Phone') }}</th>
                                <th class="px-5 py-3 text-left text-xs font-semibold uppercase text-slate-500">{{ __('Role') }}</th>
                                <th class="px-5 py-3 text-left text-xs font-semibold uppercase text-slate-500">{{ __('Active') }}</th>
                                <th class="px-5 py-3 text-left text-xs font-semibold uppercase text-slate-500">{{ __('Last Login') }}</th>
                                <th class="px-5 py-3 text-right text-xs font-semibold uppercase text-slate-500">{{ __('Actions') }}</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 bg-white">
                            @forelse ($users as $user)
                                @php
                                    $roleClass = match ($user->role?->value) {
                                        'admin' => 'bg-slate-950 text-white ring-slate-950',
                                        'manager' => 'bg-blue-50 text-blue-700 ring-blue-200',
                                        'employee' => 'bg-amber-50 text-amber-700 ring-amber-200',
                                        default => 'bg-emerald-50 text-emerald-700 ring-emerald-200',
                                    };
                                @endphp
                                <tr class="transition hover:bg-slate-50">
                                    <td class="px-5 py-4 whitespace-nowrap text-sm">
                                        <a href="{{ route('users.show', $user) }}" class="font-semibold text-slate-900 hover:text-emerald-700">
                                            {{ $user->name }}
                                        </a>
                                    </td>
                                    <td class="px-5 py-4 whitespace-nowrap text-sm text-slate-600">{{ $user->email }}</td>
                                    <td class="px-5 py-4 whitespace-nowrap text-sm text-slate-600">{{ $user->phone ?? __('Not set') }}</td>
                                    <td class="px-5 py-4 whitespace-nowrap text-sm">
                                        <span class="inline-flex items-center rounded-full px-2.5 py-1 text-xs font-semibold ring-1 ring-inset {{ $roleClass }}">{{ $user->role?->label() ?? __('Not set') }}</span>
                                    </td>
                                    <td class="px-5 py-4 whitespace-nowrap text-sm">
                                        <span class="inline-flex items-center rounded-full px-2.5 py-1 text-xs font-semibold ring-1 ring-inset {{ $user->is_active ? 'bg-emerald-50 text-emerald-700 ring-emerald-200' : 'bg-red-50 text-red-700 ring-red-200' }}">{{ $user->is_active ? __('Yes') : __('No') }}</span>
                                    </td>
                                    <td class="px-5 py-4 whitespace-nowrap text-sm text-slate-600">{{ $user->last_login_at?->diffForHumans() ?? __('Never') }}</td>
                                    <td class="px-5 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <a href="{{ route('users.edit', $user) }}" class="inline-flex items-center rounded-md border border-slate-200 px-3 py-1.5 text-xs font-semibold text-slate-700 transition hover:border-emerald-300 hover:text-emerald-700">{{ __('Edit') }}</a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-6 py-14 text-center">
                                        <p class="text-sm font-semibold text-slate-700">{{ __('No users found.') }}</p>
                                        <p class="mt-1 text-sm text-slate-500">{{ __('Create the first operator account for this console.') }}</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="rounded-lg border border-slate-200 bg-white px-4 py-3 shadow-sm">
                {{ $users->links() }}
            </div>
        </div>
    </div>
</x-app-layout>
