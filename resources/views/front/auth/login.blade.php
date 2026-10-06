<x-guest-layout>
    <div class="mb-6">
        <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
            <svg class="h-3.5 w-3.5 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
            </svg>
            <span>{{ __('Secure Access') }}</span>
        </div>
        <h1 class="mt-3 text-2xl font-bold tracking-tight text-slate-900">{{ __('Sign In') }}</h1>
        <p class="mt-1 text-sm leading-6 text-slate-500">{{ __('Enter your email address and password to access your account.') }}</p>
    </div>

    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}" class="space-y-4">
        @csrf

        <!-- Email Address -->
        <x-form.input
            :label="__('Email Address')"
            name="email"
            id="email"
            type="email"
            :value="old('email')"
            placeholder="user@bank.com"
            class="no-auto-translate"
            dir="ltr"
            required
            autofocus
            autocomplete="email"
        />

        <!-- Password -->
        <x-form.input
            :label="__('Password')"
            name="password"
            id="password"
            type="password"
            placeholder="••••••••"
            class="no-auto-translate"
            dir="ltr"
            required
            autocomplete="current-password"
        />

        <!-- Remember Me & Forgot Password -->
        <div class="flex items-center justify-between pt-1">
            <label for="remember_me" class="inline-flex items-center cursor-pointer">
                <input id="remember_me" type="checkbox" class="rounded border-slate-300 text-emerald-600 shadow-sm focus:ring-emerald-500" name="remember">
                <span class="ms-2 text-xs font-medium text-slate-600 select-none">{{ __('Remember me') }}</span>
            </label>

            @if (Route::has('password.request'))
                <a class="text-xs font-medium text-emerald-700 hover:text-emerald-800 transition" href="{{ route('password.request') }}">
                    {{ __('Forgot password?') }}
                </a>
            @endif
        </div>

        <div class="pt-2">
            <x-primary-button class="w-full justify-center py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white font-semibold shadow-sm transition">
                {{ __('Sign In') }}
            </x-primary-button>
        </div>

        @if (Route::has('register'))
            <div class="pt-4 border-t border-slate-100 text-center text-xs text-slate-500">
                <span>{{ __("Don't have an account?") }}</span>
                <a href="{{ route('register') }}" class="font-semibold text-emerald-700 hover:text-emerald-800 ms-1">
                    {{ __('Register here') }}
                </a>
            </div>
        @endif
    </form>
</x-guest-layout>
