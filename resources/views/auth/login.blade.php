<x-guest-layout>
    <div class="mb-6">
        <div class="flex items-center justify-between">
            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-800">
                <svg class="h-3.5 w-3.5 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                </svg>
                {{ __('Admin Portal') }}
            </span>
            <a href="{{ route('login') }}" class="text-xs font-medium text-slate-500 hover:text-emerald-700 transition">
                {{ __('Customer Portal') }} &rarr;
            </a>
        </div>
        <h1 class="mt-3 text-2xl font-bold tracking-tight text-slate-950">{{ __('Administrator Sign In') }}</h1>
        <p class="mt-1 text-sm leading-6 text-slate-500">{{ __('Access the management console for bank administration.') }}</p>
    </div>

    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('admin.login.store') }}" class="space-y-4">
        @csrf

        <!-- Email Address -->
        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div>
            <x-input-label for="password" :value="__('Password')" />
            <x-text-input id="password" class="block mt-1 w-full"
                            type="password"
                            name="password"
                            required autocomplete="current-password" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Remember Me -->
        <div class="flex items-center justify-between pt-1">
            <label for="remember_me" class="inline-flex items-center cursor-pointer">
                <input id="remember_me" type="checkbox" class="rounded border-slate-300 text-emerald-600 shadow-sm focus:ring-emerald-500" name="remember">
                <span class="ms-2 text-sm text-slate-600 select-none">{{ __('Remember me') }}</span>
            </label>
        </div>

        <div class="pt-2">
            <x-primary-button class="w-full justify-center py-2.5 bg-slate-950 hover:bg-slate-800 text-white font-semibold">
                {{ __('Sign In as Administrator') }}
            </x-primary-button>
        </div>

        <div class="pt-4 border-t border-slate-100 text-center text-xs text-slate-500">
            {{ __('Looking for customer digital banking?') }}
            <a href="{{ route('login') }}" class="font-semibold text-emerald-700 hover:text-emerald-900 ms-1">
                {{ __('Switch to Customer Portal') }}
            </a>
        </div>
    </form>
</x-guest-layout>
