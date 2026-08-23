<x-app-layout>
    <x-slot name="header">
        <div>
            <p class="text-xs font-semibold uppercase text-emerald-700">{{ __('Access Provisioning') }}</p>
            <h2 class="mt-1 text-2xl font-semibold text-slate-950 leading-tight">
                {{ __('New User') }}
            </h2>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="overflow-hidden rounded-lg border border-slate-200 bg-white shadow-sm">
                <div class="border-b border-slate-200 px-6 py-5">
                    <h3 class="text-base font-semibold text-slate-950">{{ __('User Access File') }}</h3>
                    <p class="mt-1 text-sm text-slate-500">{{ __('Create a controlled system identity with role and account state.') }}</p>
                </div>
                <div class="p-6">
                <form method="POST" action="{{ route('users.store') }}" class="space-y-6">
                    @csrf

                    @include('users::partials.form', [
                        'user' => null,
                        'roles' => $roles,
                        'submitLabel' => __('Create User'),
                    ])
                </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
