@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('users.index') }}">{{ __('Users') }}</a></li>
    <li class="breadcrumb-item active">{{ $user->name }}</li>
@endsection

<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <p class="text-xs font-semibold uppercase text-emerald-700">{{ __('User Profile') }}</p>
                <h2 class="mt-1 text-2xl font-semibold text-slate-950 leading-tight">
                    {{ $user->name }}
                </h2>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('users.index') }}" class="btn btn-secondary">
                    <svg class="w-4 h-4 rtl:rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    <span>{{ __('Back') }}</span>
                </a>
                <a href="{{ route('users.edit', $user) }}" class="btn btn-primary">
                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.651-1.651a2.121 2.121 0 1 1 3 3l-9.193 9.193a4.5 4.5 0 0 1-1.897 1.13L7.5 17.25l1.091-2.923a4.5 4.5 0 0 1 1.13-1.897l7.141-7.143Z" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 7.125 16.875 4.5" />
                    </svg>
                    <span>{{ __('Edit') }}</span>
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-6">
            @php
                $roleClass = match ($user->role?->value) {
                    'admin' => 'bg-slate-950 text-white ring-slate-950',
                    'manager' => 'bg-blue-50 text-blue-700 ring-blue-200',
                    'employee' => 'bg-amber-50 text-amber-700 ring-amber-200',
                    default => 'bg-emerald-50 text-emerald-700 ring-emerald-200',
                };
            @endphp

            <div class="overflow-hidden rounded-lg border border-slate-200 bg-white shadow-sm">
                <div class="border-b border-slate-200 bg-slate-950 px-6 py-6">
                    <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
                        <div>
                            <p class="text-sm font-medium text-slate-400">{{ __('Primary Email') }}</p>
                            <p class="mt-1 text-2xl font-semibold text-white">{{ $user->email }}</p>
                        </div>
                        <div class="flex flex-wrap gap-2">
                            <span class="inline-flex items-center rounded-full px-3 py-1 text-xs font-semibold ring-1 ring-inset {{ $roleClass }}">{{ $user->role?->label() ?? __('Not set') }}</span>
                            <span class="inline-flex items-center rounded-full px-3 py-1 text-xs font-semibold ring-1 ring-inset {{ $user->is_active ? 'bg-emerald-50 text-emerald-700 ring-emerald-200' : 'bg-red-50 text-red-700 ring-red-200' }}">{{ $user->is_active ? __('Active') : __('Inactive') }}</span>
                        </div>
                    </div>
                </div>

                <dl class="grid grid-cols-1 divide-y divide-slate-100 sm:grid-cols-2 sm:divide-x sm:divide-y-0">
                    <div class="space-y-5 p-6">
                        <div>
                            <dt class="text-xs font-semibold uppercase text-slate-500">{{ __('Phone') }}</dt>
                            <dd class="mt-1 text-sm font-medium text-slate-900">{{ $user->phone ?? __('Not set') }}</dd>
                        </div>
                        <div>
                            <dt class="text-xs font-semibold uppercase text-slate-500">{{ __('Email Verified At') }}</dt>
                            <dd class="mt-1 text-sm font-medium text-slate-900">{{ $user->email_verified_at?->toDateTimeString() ?? __('Not verified') }}</dd>
                        </div>
                        <div>
                            <dt class="text-xs font-semibold uppercase text-slate-500">{{ __('Last Login At') }}</dt>
                            <dd class="mt-1 text-sm font-medium text-slate-900">{{ $user->last_login_at?->toDateTimeString() ?? __('Never') }}</dd>
                        </div>
                    </div>
                    <div class="space-y-5 p-6">
                        <div>
                            <dt class="text-xs font-semibold uppercase text-slate-500">{{ __('Role') }}</dt>
                            <dd class="mt-1 text-sm font-medium text-slate-900">{{ $user->role?->label() ?? __('Not set') }}</dd>
                        </div>
                        <div>
                            <dt class="text-xs font-semibold uppercase text-slate-500">{{ __('Created At') }}</dt>
                            <dd class="mt-1 text-sm font-medium text-slate-900">{{ $user->created_at?->toDateTimeString() }}</dd>
                        </div>
                        <div>
                            <dt class="text-xs font-semibold uppercase text-slate-500">{{ __('Updated At') }}</dt>
                            <dd class="mt-1 text-sm font-medium text-slate-900">{{ $user->updated_at?->toDateTimeString() }}</dd>
                        </div>
                    </div>
                </dl>
            </div>
        </div>
    </div>
</x-app-layout>
