<x-user.layout :title="__('My Profile')">
    <x-slot:subtitle>
        {{ __('View your banking credentials, account numbers, active payment cards, and manage your personal security.') }}
    </x-slot:subtitle>

    <div class="max-w-7xl mx-auto space-y-8">
        {{-- Flash message --}}
        @if (session('status'))
            <div class="rounded-xl bg-emerald-50 border border-emerald-200 p-4 text-xs font-semibold text-emerald-800 flex items-center gap-2">
                <svg class="w-4 h-4 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
                <span>{{ session('status') }}</span>
            </div>
        @endif

        {{-- 1. Banking Credentials Section (Cards & Accounts) --}}
        <div class="space-y-4">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-base font-bold text-slate-900">{{ __('Banking Credentials & Cards') }}</h2>
                    <p class="text-xs text-slate-500">{{ __('Your official bank account and card details for digital transactions and transfers.') }}</p>
                </div>
                <span class="px-2.5 py-1 rounded-full text-[11px] font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                    {{ __('Verified Customer') }} &bull; {{ $customer->customer_number }}
                </span>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                {{-- Payment Cards Showcase --}}
                <div class="space-y-4">
                    <div class="flex items-center justify-between">
                        <h3 class="text-xs font-bold uppercase tracking-wider text-slate-500 flex items-center gap-2">
                            <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M2.25 8.25h19.5M2.25 9h19.5m-16.5 5.25h6m-6 2.25h3m-3.75 3h15a2.25 2.25 0 002.25-2.25V6.75A2.25 2.25 0 0019.5 4.5h-15A2.25 2.25 0 002.25 6.75v10.5A2.25 2.25 0 004.5 21z" />
                            </svg>
                            <span>{{ __('Payment Cards') }} ({{ count($cards) }})</span>
                        </h3>
                    </div>

                    @forelse($cards as $card)
                        <div class="relative overflow-hidden rounded-2xl bg-gradient-to-tr from-slate-900 via-slate-800 to-emerald-900 p-6 text-white shadow-lg border border-slate-700">
                            {{-- Background Accent --}}
                            <div class="absolute -end-10 -bottom-10 w-40 h-40 rounded-full bg-emerald-500/10 blur-2xl pointer-events-none"></div>

                            <div class="flex items-center justify-between relative z-10">
                                <div class="flex items-center gap-2">
                                    <span class="text-xs font-bold tracking-widest text-emerald-400 uppercase">MDAD BANK</span>
                                    <span class="text-[10px] px-2 py-0.5 rounded bg-emerald-950/80 text-emerald-300 border border-emerald-700">
                                        {{ $card->card_type instanceof \App\Modules\Cards\Enums\CardType ? $card->card_type->name : ucfirst((string)$card->card_type) }}
                                    </span>
                                </div>
                                <span class="text-sm font-bold tracking-wider text-slate-200">
                                    {{ $card->card_brand instanceof \App\Modules\Cards\Enums\CardBrand ? $card->card_brand->name : strtoupper((string)$card->card_brand) }}
                                </span>
                            </div>

                            {{-- Card Chip & Contactless --}}
                            <div class="my-5 flex items-center justify-between relative z-10">
                                <div class="w-10 h-7 rounded bg-amber-200/90 border border-amber-300/60 shadow-xs flex items-center justify-center">
                                    <div class="w-6 h-4 border border-amber-500/50 rounded-xs"></div>
                                </div>
                                <svg class="w-5 h-5 text-slate-400 rotate-90" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.111 16.404a5.5 5.5 0 017.778 0M12 20h.01m-7.08-7.071c3.904-3.905 10.236-3.905 14.14 0M1.393 9.393c5.857-5.857 15.355-5.857 21.213 0" />
                                </svg>
                            </div>

                            {{-- Card Number (رقم البطاقة) --}}
                            <div class="relative z-10 mb-4">
                                <div class="text-[10px] uppercase font-semibold text-slate-400 mb-0.5">{{ __('Card Number') }}</div>
                                <div class="text-lg sm:text-xl font-mono tracking-widest font-bold text-white selection:bg-emerald-500" dir="ltr">
                                    {{ chunk_split($card->card_number, 4, ' ') }}
                                </div>
                            </div>

                            {{-- Card Holder & Expiry --}}
                            <div class="flex items-end justify-between relative z-10 text-xs">
                                <div>
                                    <div class="text-[10px] uppercase text-slate-400">{{ __('Card Holder') }}</div>
                                    <div class="font-semibold tracking-wide uppercase text-slate-100 truncate max-w-[180px]">
                                        {{ $card->card_holder_name ?: $user->name }}
                                    </div>
                                </div>
                                <div class="text-end">
                                    <div class="text-[10px] uppercase text-slate-400">{{ __('Expires') }}</div>
                                    <div class="font-mono font-semibold text-slate-100" dir="ltr">
                                        {{ str_pad((string)$card->expiry_month, 2, '0', STR_PAD_LEFT) }}/{{ substr((string)$card->expiry_year, -2) }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="rounded-xl border border-dashed border-slate-300 p-8 text-center text-slate-400">
                            <p class="text-xs">{{ __('No active cards found.') }}</p>
                        </div>
                    @endforelse
                </div>

                {{-- Bank Accounts Showcase --}}
                <div class="space-y-4">
                    <h3 class="text-xs font-bold uppercase tracking-wider text-slate-500 flex items-center gap-2">
                        <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 21v-8.25M15.75 21v-8.25M8.25 21v-8.25M3 9l9-6 9 6m-1.5 12V10.5M4.5 21V10.5M21 21H3" />
                        </svg>
                        <span>{{ __('Bank Accounts') }} ({{ count($accounts) }})</span>
                    </h3>

                    <div class="space-y-3">
                        @forelse($accounts as $acc)
                            <div class="bg-white rounded-xl border border-slate-200 p-5 shadow-xs space-y-4 hover:border-emerald-300 transition">
                                <div class="flex items-start justify-between">
                                    <div>
                                        <div class="flex items-center gap-2">
                                            <span class="text-sm font-bold text-slate-900">
                                                {{ $acc->accountType->name ?? __('Current Account') }}
                                            </span>
                                            <span class="px-2 py-0.5 rounded text-[10px] font-semibold bg-emerald-50 text-emerald-700 border border-emerald-100">
                                                {{ $acc->currency }}
                                            </span>
                                        </div>
                                        <span class="text-[11px] text-slate-400">
                                            {{ $customer->branch->name ?? __('Main Branch') }}
                                        </span>
                                    </div>
                                    <div class="text-end">
                                        <span class="text-xs text-slate-400 block">{{ __('Available Balance') }}</span>
                                        <span class="text-base font-bold text-emerald-700 font-mono" dir="ltr">
                                            {{ number_format($acc->balance, 2) }} {{ $acc->currency }}
                                        </span>
                                    </div>
                                </div>

                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 pt-3 border-t border-slate-100 text-xs">
                                    {{-- Account Number (رقم الحساب) --}}
                                    <div class="p-2.5 rounded-lg bg-slate-50 border border-slate-100">
                                        <span class="text-[10px] uppercase font-semibold text-slate-400 block">{{ __('Account Number') }}</span>
                                        <span class="font-mono font-bold text-slate-900 selection:bg-emerald-200 text-sm" dir="ltr">
                                            {{ $acc->account_number }}
                                        </span>
                                    </div>

                                    {{-- IBAN --}}
                                    <div class="p-2.5 rounded-lg bg-slate-50 border border-slate-100">
                                        <span class="text-[10px] uppercase font-semibold text-slate-400 block">{{ __('IBAN') }}</span>
                                        <span class="font-mono font-semibold text-slate-800 text-[11px] truncate block" dir="ltr">
                                            {{ $acc->iban }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="rounded-xl border border-dashed border-slate-300 p-8 text-center text-slate-400">
                                <p class="text-xs">{{ __('No accounts found.') }}</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>

        {{-- 2. Edit Profile & Password Forms --}}
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            {{-- Personal Information Form --}}
            <div class="bg-white rounded-xl border border-slate-200 p-6 space-y-5">
                <div>
                    <h3 class="text-base font-bold text-slate-900">{{ __('Personal Information') }}</h3>
                    <p class="text-xs text-slate-500">{{ __('Update your customer profile information and contact details.') }}</p>
                </div>

                <form method="POST" action="{{ route('portal.profile.update') }}" class="space-y-4">
                    @csrf
                    @method('PUT')

                    <div>
                        <label for="name" class="block text-xs font-semibold text-slate-700 mb-1">{{ __('Full Name') }}</label>
                        <input type="text" id="name" name="name" value="{{ old('name', $user->name) }}" required
                               class="w-full text-xs rounded-lg border-slate-200 focus:border-emerald-500 focus:ring-emerald-500 shadow-xs">
                        @error('name')
                            <p class="text-[11px] text-rose-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="email" class="block text-xs font-semibold text-slate-700 mb-1">{{ __('Email Address') }}</label>
                        <input type="email" id="email" name="email" value="{{ old('email', $user->email) }}" required
                               class="w-full text-xs rounded-lg border-slate-200 focus:border-emerald-500 focus:ring-emerald-500 shadow-xs" dir="ltr">
                        @error('email')
                            <p class="text-[11px] text-rose-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="phone" class="block text-xs font-semibold text-slate-700 mb-1">{{ __('Phone Number') }}</label>
                        <input type="text" id="phone" name="phone" value="{{ old('phone', $user->phone ?? $customer->phone_number) }}"
                               class="w-full text-xs rounded-lg border-slate-200 focus:border-emerald-500 focus:ring-emerald-500 shadow-xs" dir="ltr">
                        @error('phone')
                            <p class="text-[11px] text-rose-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="address" class="block text-xs font-semibold text-slate-700 mb-1">{{ __('Residential Address') }}</label>
                        <input type="text" id="address" name="address" value="{{ old('address', $customer->address) }}"
                               class="w-full text-xs rounded-lg border-slate-200 focus:border-emerald-500 focus:ring-emerald-500 shadow-xs">
                        @error('address')
                            <p class="text-[11px] text-rose-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="pt-2">
                        <button type="submit"
                                class="px-5 py-2.5 rounded-lg text-xs font-semibold bg-emerald-600 hover:bg-emerald-500 text-white transition shadow-xs">
                            {{ __('Save Profile Changes') }}
                        </button>
                    </div>
                </form>
            </div>

            {{-- Password & Security --}}
            <div class="bg-white rounded-xl border border-slate-200 p-6 space-y-5">
                <div>
                    <h3 class="text-base font-bold text-slate-900">{{ __('Update Password') }}</h3>
                    <p class="text-xs text-slate-500">{{ __('Ensure your account is using a long, random password to stay secure.') }}</p>
                </div>

                <form method="POST" action="{{ route('portal.profile.password') }}" class="space-y-4">
                    @csrf
                    @method('PUT')

                    <div>
                        <label for="current_password" class="block text-xs font-semibold text-slate-700 mb-1">{{ __('Current Password') }}</label>
                        <input type="password" id="current_password" name="current_password" required
                               class="w-full text-xs rounded-lg border-slate-200 focus:border-emerald-500 focus:ring-emerald-500 shadow-xs" dir="ltr">
                        @error('current_password', 'updatePassword')
                            <p class="text-[11px] text-rose-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="password" class="block text-xs font-semibold text-slate-700 mb-1">{{ __('New Password') }}</label>
                        <input type="password" id="password" name="password" required
                               class="w-full text-xs rounded-lg border-slate-200 focus:border-emerald-500 focus:ring-emerald-500 shadow-xs" dir="ltr">
                        @error('password', 'updatePassword')
                            <p class="text-[11px] text-rose-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="password_confirmation" class="block text-xs font-semibold text-slate-700 mb-1">{{ __('Confirm Password') }}</label>
                        <input type="password" id="password_confirmation" name="password_confirmation" required
                               class="w-full text-xs rounded-lg border-slate-200 focus:border-emerald-500 focus:ring-emerald-500 shadow-xs" dir="ltr">
                    </div>

                    <div class="pt-2 flex items-center justify-between">
                        <button type="submit"
                                class="px-5 py-2.5 rounded-lg text-xs font-semibold bg-slate-900 hover:bg-slate-800 text-white transition shadow-xs">
                            {{ __('Update Password') }}
                        </button>

                        <a href="{{ route('portal.2fa') }}" class="text-xs font-semibold text-emerald-600 hover:text-emerald-700">
                            {{ __('Configure 2FA') }} &rarr;
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-user.layout>
