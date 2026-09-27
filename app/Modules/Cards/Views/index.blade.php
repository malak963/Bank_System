@extends('layouts.app')

@section('title', __('Cards'))

@section('breadcrumb')
    <li class="breadcrumb-item active">{{ __('Cards') }}</li>
@endsection

@section('actions')
    <a href="{{ route('cards.create') }}" class="btn btn-primary">
        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"></path>
        </svg>
        <span>{{ __('New Card') }}</span>
    </a>
@endsection

@section('content')
<div class="mx-auto max-w-7xl space-y-6 px-4 py-8 sm:px-6 lg:px-8">
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($cards as $card)
            <div class="bank-card">
                <!-- Card Gradient Banner -->
                <div class="bg-gradient-to-r from-slate-900 to-slate-800 p-5 text-white">
                    <div class="flex justify-between items-start">
                        <div>
                            <p class="text-xs font-semibold uppercase tracking-wider text-emerald-400">{{ $card->card_type->label() }}</p>
                            <p class="text-lg font-mono font-bold mt-1 tracking-widest" dir="ltr">{{ $card->maskCardNumber() }}</p>
                        </div>
                        <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-semibold ring-1 ring-inset {{ $card->isActive() ? 'bg-emerald-500/20 text-emerald-300 ring-emerald-400/30' : 'bg-rose-500/20 text-rose-300 ring-rose-400/30' }}">
                            {{ $card->status->label() }}
                        </span>
                    </div>
                    <div class="mt-5 flex justify-between text-xs text-slate-300">
                        <div>
                            <p class="text-[10px] uppercase text-slate-400 font-semibold">{{ __('Valid Thru') }}</p>
                            <p class="font-mono font-medium mt-0.5" dir="ltr">{{ str_pad($card->expiry_month, 2, '0', STR_PAD_LEFT) }}/{{ $card->expiry_year }}</p>
                        </div>
                        <div class="text-end">
                            <p class="text-[10px] uppercase text-slate-400 font-semibold">{{ __('Card Holder') }}</p>
                            <p class="font-medium mt-0.5 truncate max-w-[120px]">{{ $card->card_holder_name }}</p>
                        </div>
                    </div>
                </div>

                <!-- Card Body Metadata -->
                <div class="bank-card-body space-y-2.5 text-xs">
                    <div class="flex justify-between items-center py-1 border-b border-slate-50">
                        <span class="text-slate-500">{{ __('Account') }}</span>
                        <span class="font-semibold text-slate-900 font-mono" dir="ltr">{{ $card->account?->account_number ?? __('N/A') }}</span>
                    </div>
                    <div class="flex justify-between items-center py-1 border-b border-slate-50">
                        <span class="text-slate-500">{{ __('Brand') }}</span>
                        <span class="font-semibold text-slate-900">{{ $card->card_brand->label() }}</span>
                    </div>
                    <div class="flex justify-between items-center py-1 border-b border-slate-50">
                        <span class="text-slate-500">{{ __('Daily Limit') }}</span>
                        <span class="font-semibold text-slate-900 font-mono" dir="ltr">${{ number_format($card->daily_limit, 2) }}</span>
                    </div>
                </div>

                <div class="bank-card-footer flex justify-end">
                    <a href="{{ route('cards.show', $card) }}" class="btn btn-sm btn-secondary">
                        {{ __('View Details') }} &rarr;
                    </a>
                </div>
            </div>
        @empty
            <div class="col-span-full bank-card p-12 text-center text-slate-500">
                <svg class="w-12 h-12 mx-auto mb-3 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path>
                </svg>
                <p class="text-sm font-semibold text-slate-700">{{ __('No cards found') }}</p>
                <p class="text-xs text-slate-400 mt-1">{{ __('Create your first card to get started') }}</p>
            </div>
        @endforelse
    </div>

    @if (method_exists($cards, 'hasPages') && $cards->hasPages())
        <div class="rounded-xl border border-slate-200 bg-white px-4 py-3 shadow-sm">
            {{ $cards->links() }}
        </div>
    @endif
</div>
@endsection
