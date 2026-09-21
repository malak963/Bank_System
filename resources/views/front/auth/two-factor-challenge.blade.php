<x-guest-layout>
    <div class="mb-8">
        <p class="text-xs font-semibold uppercase tracking-[0.2em] text-emerald-700">{{ __('Two-Factor Authentication') }}</p>
        <h1 class="mt-3 text-3xl font-semibold tracking-tight text-slate-950">{{ __('Security Verification') }}</h1>
        <p class="mt-2 text-sm leading-6 text-slate-500">
            {{ __('Please confirm access to your account to complete your authentication.') }}
        </p>
    </div>

    <div x-data="{ recovery: false }">
        <div class="mb-6 rounded-lg bg-emerald-50 border border-emerald-200 p-4 text-sm text-emerald-800" x-show="! recovery">
            {{ __('Please enter the 6-digit authentication code provided by your authenticator application.') }}
        </div>

        <div class="mb-6 rounded-lg bg-amber-50 border border-amber-200 p-4 text-sm text-amber-800" x-cloak x-show="recovery">
            {{ __('Please enter one of your emergency recovery codes to regain account access.') }}
        </div>

        <x-auth-session-status class="mb-4" :status="session('status')" />

        <form method="POST" action="{{ route('two-factor.login') }}" class="space-y-4">
            @csrf

            <div x-show="! recovery">
                <x-input-label for="code" :value="__('Authentication Code')" />
                <x-text-input id="code" class="block mt-1 w-full tracking-widest text-center text-lg font-mono" type="text" inputmode="numeric" name="code" autofocus x-ref="code" autocomplete="one-time-code" placeholder="000000" />
                <x-input-error :messages="$errors->get('code')" class="mt-2" />
            </div>

            <div x-cloak x-show="recovery">
                <x-input-label for="recovery_code" :value="__('Recovery Code')" />
                <x-text-input id="recovery_code" class="block mt-1 w-full font-mono text-center" type="text" name="recovery_code" x-ref="recovery_code" autocomplete="one-time-code" placeholder="xxxxx-xxxxx" />
                <x-input-error :messages="$errors->get('recovery_code')" class="mt-2" />
            </div>

            <div class="flex items-center justify-between pt-2">
                <button type="button" class="text-xs font-semibold text-emerald-700 hover:text-emerald-900 underline cursor-pointer"
                        x-show="! recovery"
                        x-on:click="
                            recovery = true;
                            $nextTick(() => { $refs.recovery_code.focus() })
                        ">
                    {{ __('Use an emergency recovery code') }}
                </button>

                <button type="button" class="text-xs font-semibold text-emerald-700 hover:text-emerald-900 underline cursor-pointer"
                        x-cloak
                        x-show="recovery"
                        x-on:click="
                            recovery = false;
                            $nextTick(() => { $refs.code.focus() })
                        ">
                    {{ __('Use an authenticator code') }}
                </button>

                <x-primary-button class="py-2.5 px-6 bg-slate-950 hover:bg-slate-800 text-white font-semibold">
                    {{ __('Verify & Log In') }}
                </x-primary-button>
            </div>
        </form>
    </div>
</x-guest-layout>
