<x-app-layout>
    <x-slot name="header">
        <div>
            <p class="text-xs font-semibold uppercase text-emerald-700">{{ __('Access Maintenance') }}</p>
            <h2 class="mt-1 text-2xl font-semibold text-slate-950 leading-tight">
                {{ __('Edit User') }}
            </h2>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-6">
            @if ($errors->has('user'))
                <div class="rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm font-medium text-red-700">
                    {{ $errors->first('user') }}
                </div>
            @endif

            <div class="overflow-hidden rounded-lg border border-slate-200 bg-white shadow-sm">
                <div class="border-b border-slate-200 px-6 py-5">
                    <h3 class="text-base font-semibold text-slate-950">{{ $user->name }}</h3>
                    <p class="mt-1 text-sm text-slate-500">{{ __('Maintain account identity, role and sign-in eligibility.') }}</p>
                </div>
                <div class="p-6">
                <form method="POST" action="{{ route('users.update', $user) }}" class="space-y-6">
                    @csrf
                    @method('PUT')

                    @include('users::partials.form', [
                        'user' => $user,
                        'roles' => $roles,
                        'submitLabel' => __('Update User'),
                    ])
                </form>
                </div>
            </div>

            @if ($user->is_active)
                <div class="rounded-lg border border-red-200 bg-white p-6 shadow-sm">
                    <form method="POST" action="{{ route('users.destroy', $user) }}" class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                        @csrf
                        @method('DELETE')

                        <div>
                            <h3 class="text-sm font-semibold text-slate-950">{{ __('Deactivate Access') }}</h3>
                            <p class="mt-1 text-sm text-slate-500">{{ __('Disable sign-in while preserving account history.') }}</p>
                        </div>

                        <button type="submit" class="inline-flex items-center justify-center rounded-lg bg-red-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2">
                            {{ __('Deactivate User') }}
                        </button>
                    </form>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
