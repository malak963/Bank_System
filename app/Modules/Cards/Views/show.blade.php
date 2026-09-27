@extends('layouts.app')

@section('title', __('Card Details'))

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('cards.index') }}">{{ __('Cards') }}</a></li>
    <li class="breadcrumb-item active font-mono" dir="ltr">{{ $card->maskCardNumber() }}</li>
@endsection

@section('actions')
    <a href="{{ route('cards.index') }}" class="btn btn-secondary">
        {{ __('Back to Cards') }}
    </a>
@endsection

@section('content')
<div class="mx-auto max-w-7xl space-y-6 px-4 py-8 sm:px-6 lg:px-8">
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 space-y-6">
            <!-- Card Visual Banner -->
            <div class="bg-gradient-to-r from-slate-900 to-slate-800 rounded-xl p-6 text-white shadow-lg">
                <div class="flex justify-between items-start mb-8">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-wider text-emerald-400">{{ $card->card_type->label() }}</p>
                        <p class="text-2xl font-bold mt-2 font-mono tracking-widest" dir="ltr">{{ $card->maskCardNumber() }}</p>
                    </div>
                    <span class="inline-flex items-center rounded-full px-3 py-1 text-xs font-semibold ring-1 ring-inset {{ $card->isActive() ? 'bg-emerald-500/20 text-emerald-300 ring-emerald-400/30' : 'bg-rose-500/20 text-rose-300 ring-rose-400/30' }}">
                        {{ $card->status->label() }}
                    </span>
                </div>
                <div class="flex justify-between items-end text-xs text-slate-300">
                    <div>
                        <p class="text-[10px] uppercase text-slate-400 font-semibold">{{ __('CARD HOLDER') }}</p>
                        <p class="font-medium mt-0.5 text-sm">{{ $card->card_holder_name }}</p>
                    </div>
                    <div>
                        <p class="text-[10px] uppercase text-slate-400 font-semibold">{{ __('EXPIRES') }}</p>
                        <p class="font-mono font-medium mt-0.5" dir="ltr">{{ str_pad($card->expiry_month, 2, '0', STR_PAD_LEFT) }}/{{ $card->expiry_year }}</p>
                    </div>
                    <div>
                        <p class="text-[10px] uppercase text-slate-400 font-semibold">{{ __('CVV') }}</p>
                        <p class="font-mono font-medium mt-0.5" dir="ltr">***</p>
                    </div>
                </div>
            </div>

            <!-- Card Information Table -->
            <div class="bank-card">
                <div class="bank-card-header">
                    <h2 class="text-base font-semibold text-slate-950">{{ __('Card Information') }}</h2>
                </div>
                <div class="bank-card-body">
                    <dl class="grid grid-cols-1 sm:grid-cols-2 gap-x-6 gap-y-4 text-sm">
                        <div class="flex justify-between border-b border-slate-50 pb-2">
                            <dt class="text-slate-500">{{ __('Card Number') }}</dt>
                            <dd class="font-medium text-slate-900 font-mono" dir="ltr">{{ $card->maskCardNumber() }}</dd>
                        </div>
                        <div class="flex justify-between border-b border-slate-50 pb-2">
                            <dt class="text-slate-500">{{ __('Brand') }}</dt>
                            <dd class="font-medium text-slate-900">{{ $card->card_brand->label() }}</dd>
                        </div>
                        <div class="flex justify-between border-b border-slate-50 pb-2">
                            <dt class="text-slate-500">{{ __('Type') }}</dt>
                            <dd class="font-medium text-slate-900">{{ $card->card_type->label() }}</dd>
                        </div>
                        <div class="flex justify-between border-b border-slate-50 pb-2">
                            <dt class="text-slate-500">{{ __('Status') }}</dt>
                            <dd class="font-medium text-slate-900">{{ $card->status->label() }}</dd>
                        </div>
                        <div class="flex justify-between border-b border-slate-50 pb-2">
                            <dt class="text-slate-500">{{ __('Account') }}</dt>
                            <dd class="font-medium text-slate-900 font-mono" dir="ltr">{{ $card->account?->account_number ?? __('N/A') }}</dd>
                        </div>
                        <div class="flex justify-between border-b border-slate-50 pb-2">
                            <dt class="text-slate-500">{{ __('Customer') }}</dt>
                            <dd class="font-medium text-slate-900">{{ $card->customer?->full_name ?? __('N/A') }}</dd>
                        </div>
                        <div class="flex justify-between border-b border-slate-50 pb-2">
                            <dt class="text-slate-500">{{ __('Issued') }}</dt>
                            <dd class="font-medium text-slate-900 font-mono" dir="ltr">{{ $card->issued_at?->format('Y-m-d') ?? __('N/A') }}</dd>
                        </div>
                        <div class="flex justify-between border-b border-slate-50 pb-2">
                            <dt class="text-slate-500">{{ __('Expires') }}</dt>
                            <dd class="font-medium text-slate-900 font-mono" dir="ltr">{{ $card->expires_at?->format('Y-m-d') ?? __('N/A') }}</dd>
                        </div>
                    </dl>

                    @if($card->blocked_at)
                        <div class="mt-6 rounded-lg border border-rose-200 bg-rose-50 p-4">
                            <h3 class="text-xs font-semibold text-rose-800">{{ __('Block Information') }}</h3>
                            <p class="text-xs text-rose-700 mt-1">{{ $card->block_reason }}</p>
                            <p class="text-[11px] text-rose-600 mt-1 font-mono" dir="ltr">{{ __('Blocked on:') }} {{ $card->blocked_at->format('Y-m-d H:i') }}</p>
                        </div>
                    @endif

                    @if($card->replacement_reason)
                        <div class="mt-6 rounded-lg border border-amber-200 bg-amber-50 p-4">
                            <h3 class="text-xs font-semibold text-amber-800">{{ __('Replacement Information') }}</h3>
                            <p class="text-xs text-amber-700 mt-1">{{ $card->replacement_reason }}</p>
                            @if($card->replacementCard)
                                <p class="text-[11px] text-amber-600 mt-1 font-mono" dir="ltr">{{ __('Replaced by:') }} {{ $card->replacementCard->maskCardNumber() }}</p>
                            @endif
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="space-y-6">
            <!-- Quick Actions -->
            <div class="bank-card">
                <div class="bank-card-header">
                    <h3 class="text-sm font-semibold text-slate-900">{{ __('Card Management Actions') }}</h3>
                </div>
                <div class="bank-card-body space-y-4">
                    @if($card->status === \App\Modules\Cards\Enums\CardStatus::Pending)
                        <form action="{{ route('cards.activate', $card) }}" method="POST">
                            @csrf
                            <div class="mb-3">
                                <label class="block text-xs font-medium text-slate-700 mb-1">{{ __('Set PIN (4 digits)') }}</label>
                                <input type="password" name="pin" required maxlength="4" pattern="[0-9]{4}" class="w-full rounded-lg border-slate-300 text-sm shadow-xs focus:border-emerald-500 focus:ring-emerald-500" placeholder="****">
                                <p class="text-[11px] text-slate-500 mt-1">{{ __('Enter a 4-digit PIN to activate this card.') }}</p>
                            </div>
                            <button type="submit" class="w-full btn btn-primary">
                                {{ __('Activate Card') }}
                            </button>
                        </form>
                    @endif

                    @if($card->isActive())
                        <form action="{{ route('cards.block', $card) }}" method="POST">
                            @csrf
                            <div class="mb-3">
                                <label class="block text-xs font-medium text-slate-700 mb-1">{{ __('Block Reason') }}</label>
                                <textarea name="reason" required rows="2" class="w-full rounded-lg border-slate-300 text-xs shadow-xs focus:border-rose-500 focus:ring-rose-500" placeholder="{{ __('Enter reason...') }}"></textarea>
                            </div>
                            <button type="submit" class="w-full btn btn-danger" onclick="return confirm('{{ __('Are you sure you want to block this card?') }}')">
                                {{ __('Block Card') }}
                            </button>
                        </form>
                    @endif

                    @if($card->status === \App\Modules\Cards\Enums\CardStatus::Blocked)
                        <form action="{{ route('cards.unblock', $card) }}" method="POST">
                            @csrf
                            <button type="submit" class="w-full btn btn-success">
                                {{ __('Unblock Card') }}
                            </button>
                        </form>
                    @endif

                    @if($card->status->requiresReplacement())
                        <form action="{{ route('cards.replace', $card) }}" method="POST">
                            @csrf
                            <div class="mb-3">
                                <label class="block text-xs font-medium text-slate-700 mb-1">{{ __('Replacement Reason') }}</label>
                                <textarea name="reason" required rows="2" class="w-full rounded-lg border-slate-300 text-xs shadow-xs focus:border-amber-500 focus:ring-amber-500" placeholder="{{ __('Enter reason...') }}"></textarea>
                            </div>
                            <button type="submit" class="w-full btn btn-primary">
                                {{ __('Replace Card') }}
                            </button>
                        </form>
                    @endif
                </div>
            </div>

            <!-- Limits & Features -->
            <div class="bank-card">
                <div class="bank-card-header">
                    <h3 class="text-sm font-semibold text-slate-900">{{ __('Limits & Features') }}</h3>
                </div>
                <div class="bank-card-body space-y-3 text-xs">
                    <div class="flex justify-between items-center py-1 border-b border-slate-50">
                        <span class="text-slate-500">{{ __('Daily Limit') }}</span>
                        <span class="font-semibold text-slate-900 font-mono" dir="ltr">${{ number_format($card->daily_limit, 2) }}</span>
                    </div>
                    <div class="flex justify-between items-center py-1 border-b border-slate-50">
                        <span class="text-slate-500">{{ __('Monthly Limit') }}</span>
                        <span class="font-semibold text-slate-900 font-mono" dir="ltr">${{ number_format($card->monthly_limit, 2) }}</span>
                    </div>
                    <div class="pt-2 space-y-2">
                        <div class="flex justify-between items-center">
                            <span class="text-slate-600">{{ __('International Transactions') }}</span>
                            <span class="font-semibold {{ $card->international_enabled ? 'text-emerald-700' : 'text-slate-400' }}">
                                {{ $card->international_enabled ? __('Enabled') : __('Disabled') }}
                            </span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-slate-600">{{ __('Online Payments') }}</span>
                            <span class="font-semibold {{ $card->online_enabled ? 'text-emerald-700' : 'text-slate-400' }}">
                                {{ $card->online_enabled ? __('Enabled') : __('Disabled') }}
                            </span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-slate-600">{{ __('Contactless Payments') }}</span>
                            <span class="font-semibold {{ $card->contactless_enabled ? 'text-emerald-700' : 'text-slate-400' }}">
                                {{ $card->contactless_enabled ? __('Enabled') : __('Disabled') }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- PIN Management -->
            @if($card->isActive())
                <div class="bank-card">
                    <div class="bank-card-header">
                        <h3 class="text-sm font-semibold text-slate-900">{{ __('PIN Management') }}</h3>
                    </div>
                    <div class="bank-card-body">
                        <form action="{{ route('cards.update-pin', $card) }}" method="POST" class="space-y-3">
                            @csrf
                            <div>
                                <label class="block text-xs font-medium text-slate-700 mb-1">{{ __('Current PIN') }}</label>
                                <input type="password" name="current_pin" required maxlength="4" pattern="[0-9]{4}" class="w-full rounded-lg border-slate-300 text-xs shadow-xs focus:border-emerald-500 focus:ring-emerald-500" placeholder="****">
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-slate-700 mb-1">{{ __('New PIN') }}</label>
                                <input type="password" name="new_pin" required maxlength="4" pattern="[0-9]{4}" class="w-full rounded-lg border-slate-300 text-xs shadow-xs focus:border-emerald-500 focus:ring-emerald-500" placeholder="****">
                            </div>
                            <button type="submit" class="w-full btn btn-secondary text-xs">
                                {{ __('Update PIN') }}
                            </button>
                        </form>
                    </div>
                </div>
            @endif

            @if($card->metadata)
                <div class="bank-card">
                    <div class="bank-card-header">
                        <h3 class="text-sm font-semibold text-slate-900">{{ __('Metadata') }}</h3>
                    </div>
                    <div class="bank-card-body">
                        <pre class="text-xs text-slate-600 bg-slate-50 p-3 rounded-lg overflow-auto font-mono" dir="ltr">{{ json_encode($card->metadata, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
