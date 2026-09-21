<x-app-layout>
    <x-slot name="header">
        <div>
            <p class="text-xs font-semibold uppercase tracking-[0.2em] text-emerald-700">{{ __('Security Settings') }}</p>
            <h2 class="mt-1 text-2xl font-semibold leading-tight text-slate-950">{{ __('Two-Factor Authentication (Admin)') }}</h2>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white border border-slate-200 rounded-xl shadow-sm overflow-hidden">
                <div class="p-6 sm:p-8">
                    @if (session('status') == 'two-factor-authentication-enabled')
                        <div class="mb-6 rounded-lg bg-emerald-50 border border-emerald-200 p-4 text-sm font-medium text-emerald-800">
                            {{ __('Two-factor authentication has been enabled. Please scan the QR code below using your authenticator application.') }}
                        </div>
                    @elseif (session('status') == 'two-factor-authentication-disabled')
                        <div class="mb-6 rounded-lg bg-slate-50 border border-slate-200 p-4 text-sm font-medium text-slate-700">
                            {{ __('Two-factor authentication has been disabled.') }}
                        </div>
                    @elseif (session('status') == 'recovery-codes-generated')
                        <div class="mb-6 rounded-lg bg-emerald-50 border border-emerald-200 p-4 text-sm font-medium text-emerald-800">
                            {{ __('New recovery codes have been generated.') }}
                        </div>
                    @endif

                    @if (!$user->two_factor_secret)
                        <div class="flex items-start gap-4 p-4 rounded-xl bg-amber-50 border border-amber-200 text-amber-900 mb-6">
                            <svg class="h-6 w-6 shrink-0 text-amber-600 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                            </svg>
                            <div>
                                <h3 class="font-semibold text-sm">{{ __('Two-Factor Authentication is currently disabled') }}</h3>
                                <p class="text-xs text-amber-800 mt-1 leading-5">
                                    {{ __('When two-factor authentication is enabled, you will be prompted for a secure 6-digit token from Google Authenticator or another TOTP app during authentication.') }}
                                </p>
                            </div>
                        </div>

                        <form method="POST" action="{{ route('two-factor.enable') }}">
                            @csrf
                            <x-primary-button class="py-2.5 px-5 bg-emerald-600 hover:bg-emerald-700 text-white font-semibold">
                                {{ __('Enable Two-Factor Authentication') }}
                            </x-primary-button>
                        </form>
                    @else
                        <div class="flex items-start gap-4 p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-900 mb-8">
                            <svg class="h-6 w-6 shrink-0 text-emerald-600 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                            </svg>
                            <div>
                                <h3 class="font-semibold text-sm">{{ __('Two-Factor Authentication is active') }}</h3>
                                <p class="text-xs text-emerald-800 mt-1 leading-5">
                                    {{ __('Your account is secured with two-factor authentication. You will be prompted for your authenticator code upon signing in.') }}
                                </p>
                            </div>
                        </div>

                        <div class="space-y-6">
                            <div>
                                <h4 class="text-sm font-bold uppercase tracking-wider text-slate-700 mb-2">{{ __('Authenticator QR Code') }}</h4>
                                <p class="text-xs text-slate-500 mb-4">
                                    {{ __('Scan the following QR code using your phone’s authenticator application (such as Google Authenticator or Microsoft Authenticator).') }}
                                </p>
                                <div class="inline-block p-4 bg-white border border-slate-200 rounded-xl shadow-sm">
                                    {!! $user->twoFactorQrCodeSvg() !!}
                                </div>
                            </div>

                            @if ($user->two_factor_recovery_codes)
                                <div class="pt-6 border-t border-slate-200">
                                    <h4 class="text-sm font-bold uppercase tracking-wider text-slate-700 mb-2">{{ __('Emergency Recovery Codes') }}</h4>
                                    <p class="text-xs text-slate-500 mb-4">
                                        {{ __('Store these recovery codes in a secure password manager. They can be used to recover access to your account if your authenticator device is lost.') }}
                                    </p>
                                    <div class="grid grid-cols-2 gap-2 max-w-md p-4 rounded-xl bg-slate-900 text-emerald-400 font-mono text-xs">
                                        @foreach ($user->recoveryCodes() as $code)
                                            <div class="py-1 px-2">{{ $code }}</div>
                                        @endforeach
                                    </div>

                                    <form method="POST" action="{{ route('two-factor.regenerate-recovery-codes') }}" class="mt-4">
                                        @csrf
                                        <button type="submit" class="text-xs font-medium text-emerald-700 hover:text-emerald-900 underline">
                                            {{ __('Regenerate Recovery Codes') }}
                                        </button>
                                    </form>
                                </div>
                            @endif

                            <div class="pt-6 border-t border-slate-200 flex items-center justify-between">
                                <form method="POST" action="{{ route('two-factor.disable') }}">
                                    @csrf
                                    @method('DELETE')
                                    <x-danger-button onclick="return confirm('{{ __('Are you sure you want to disable two-factor authentication?') }}')">
                                        {{ __('Disable Two-Factor Authentication') }}
                                    </x-danger-button>
                                </form>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
