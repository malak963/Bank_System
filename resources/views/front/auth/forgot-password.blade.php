<x-guest-layout>
    <div class="mb-8">
        <p class="text-xs font-semibold uppercase tracking-[0.2em] text-emerald-700">{{ config('bank.name', 'Bank System') }}</p>
        <h1 class="mt-3 text-3xl font-semibold tracking-tight text-slate-950">{{ __('Reset password') }}</h1>
    </div>

    <div class="mb-4 text-sm text-slate-600">
        {{ __('Forgot your password? No problem. Just enter your registered email address and we will email you a password reset link.') }}
    </div>

    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('password.email') }}" class="space-y-4">
        @csrf

        <!-- Email Address -->
        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autofocus />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div class="flex items-center justify-between pt-2">
            <a class="text-sm text-slate-600 hover:text-emerald-700 underline" href="{{ route('login') }}">
                {{ __('Back to login') }}
            </a>

            <x-primary-button class="py-2.5 px-6 bg-slate-950 hover:bg-slate-800 text-white font-semibold">
                {{ __('Email Password Reset Link') }}
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>
