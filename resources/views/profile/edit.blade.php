<x-app-layout>
    <x-slot name="header">
        <div>
            <p class="text-xs font-semibold uppercase tracking-[0.2em] text-emerald-700">{{ __('Account Security') }}</p>
            <h2 class="mt-1 text-2xl font-semibold leading-tight text-slate-950">{{ __('Profile') }}</h2>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="rounded-lg border border-slate-200 bg-white p-4 shadow-sm sm:p-8">
                <div class="max-w-xl">
                    @include('profile.partials.update-profile-information-form')
                </div>
            </div>

            <div class="rounded-lg border border-slate-200 bg-white p-4 shadow-sm sm:p-8">
                <div class="max-w-xl">
                    @include('profile.partials.update-password-form')
                </div>
            </div>

            <div class="rounded-lg border border-slate-200 bg-white p-4 shadow-sm sm:p-8">
                <div class="max-w-xl">
                    @include('profile.partials.delete-user-form')
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
