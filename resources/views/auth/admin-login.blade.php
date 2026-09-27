<x-guest-layout>
    <div class="mb-6">
        <div class="flex items-center justify-between">
            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold bg-slate-900 text-white shadow-sm">
                <svg class="h-3.5 w-3.5 text-emerald-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                </svg>
                {{ __('Staff & Admin Console') }}
            </span>
            <a href="{{ route('login') }}" class="text-xs font-semibold text-emerald-700 hover:text-emerald-900 transition flex items-center gap-1">
                &larr; {{ __('Customer Portal') }}
            </a>
        </div>
        <h1 class="mt-4 text-2xl font-bold tracking-tight text-slate-950">{{ __('Staff & Admin Sign In') }}</h1>
        <p class="mt-1 text-sm leading-6 text-slate-500">{{ __('Authorized access for bank tellers, managers, and system administrators.') }}</p>
    </div>

    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('admin.login.store') }}" class="space-y-4">
        @csrf

        <!-- Email Address -->
        <x-form.input
            :label="__('Work Email Address')"
            name="email"
            type="email"
            :value="old('email')"
            :placeholder="__('staff@bank.com')"
            required
            autofocus
        />

        <!-- Password -->
        <x-form.input
            :label="__('Master Password')"
            name="password"
            type="password"
            :placeholder="__('••••••••••••')"
            required
        />

        <!-- Remember Me -->
        <div class="flex items-center justify-between pt-1">
            <label for="remember_me" class="inline-flex items-center cursor-pointer">
                <input id="remember_me" type="checkbox" class="rounded border-slate-300 text-emerald-600 shadow-sm focus:ring-emerald-500" name="remember">
                <span class="ms-2 text-sm text-slate-600 select-none">{{ __('Remember me') }}</span>
            </label>
        </div>

        <div class="pt-2">
            <button type="submit" class="w-full justify-center py-2.5 px-4 bg-slate-900 hover:bg-slate-800 text-white font-semibold rounded-lg shadow-sm transition flex items-center gap-2">
                <span>{{ __('Sign In to Admin Console') }}</span>
                <svg class="h-4 w-4 rtl:rotate-180" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                </svg>
            </button>
        </div>

        <div class="pt-4 border-t border-slate-100 text-center text-xs text-slate-500">
            {{ __('Are you a bank customer?') }}
            <a href="{{ route('login') }}" class="font-semibold text-emerald-700 hover:text-emerald-900 ms-1 underline">
                {{ __('Go to Digital Banking Login') }}
            </a>
        </div>
    </form>
</x-guest-layout>
