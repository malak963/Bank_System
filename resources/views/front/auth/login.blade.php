<x-guest-layout>
    <div class="mb-6">
        <div class="flex items-center justify-between">
            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-800">
                <svg class="h-3.5 w-3.5 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                </svg>
                {{ __('Customer Portal') }}
            </span>
            <a href="{{ route('admin.login') }}" class="text-xs font-semibold text-emerald-700 hover:text-emerald-900 transition flex items-center gap-1">
                {{ __('Admin Portal') }} &rarr;
            </a>
        </div>
        <h1 class="mt-3 text-2xl font-bold tracking-tight text-slate-950">{{ __('Customer Sign In') }}</h1>
        <p class="mt-1 text-sm leading-6 text-slate-500">{{ __('Sign in to access your digital banking account.') }}</p>
    </div>

    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}" class="space-y-4">
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

            @if (Route::has('password.request'))
                <a class="text-xs font-medium text-emerald-700 hover:text-emerald-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500 transition" href="{{ route('password.request') }}">
                    {{ __('Forgot your password?') }}
                </a>
            @endif
        </div>

        <div class="pt-2">
            <x-primary-button class="w-full justify-center py-2.5 bg-slate-950 hover:bg-slate-800 text-white font-semibold">
                {{ __('Log in') }}
            </x-primary-button>
        </div>

        <div class="pt-4 border-t border-slate-100 flex flex-col gap-2 text-center text-xs text-slate-500">
            <div>
                {{ __('Are you a Bank Administrator?') }}
                <a href="{{ route('admin.login') }}" class="font-semibold text-emerald-700 hover:text-emerald-900 ms-1">
                    {{ __('Sign in through Admin Portal') }} &rarr;
                </a>
            </div>

            @if (Route::has('register'))
                <div>
                    {{ __("Don't have an account?") }}
                    <a href="{{ route('register') }}" class="font-medium text-emerald-700 hover:text-emerald-900 ms-1">
                        {{ __('Register here') }}
                    </a>
                </div>
            @endif
        </div>
    </form>
</x-guest-layout>
