@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold text-gray-900">{{ __('Cards') }}</h1>
        <a href="{{ route('cards.create') }}" class="bg-emerald-600 hover:bg-emerald-700 text-white px-4 py-2 rounded-lg flex items-center gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
            </svg>
            New Card
        </a>
    </div>

    @if(session('success'))
        <div class="bg-emerald-100 border border-emerald-400 text-emerald-700 px-4 py-3 rounded mb-4">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
            {{ session('error') }}
        </div>
    @endif

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($cards as $card)
            <div class="bg-white rounded-lg shadow overflow-hidden">
                <div class="bg-gradient-to-r from-emerald-500 to-blue-500 p-4">
                    <div class="flex justify-between items-start">
                        <div>
                            <p class="text-white text-sm font-medium">{{ $card->card_type->label() }}</p>
                            <p class="text-white text-lg font-bold mt-1">{{ $card->maskCardNumber() }}</p>
                        </div>
                        <span class="px-2 py-1 rounded-full text-xs font-semibold {{ $card->isActive() ? 'bg-white text-emerald-600' : 'bg-red-100 text-red-800' }}">
                            {{ $card->status->label() }}
                        </span>
                    </div>
                    <div class="mt-4 flex justify-between text-white text-sm">
                        <div>
                            <p class="opacity-75">{{ __('Valid Thru') }}</p>
                            <p class="font-semibold">{{ str_pad($card->expiry_month, 2, '0', STR_PAD_LEFT) }}/{{ $card->expiry_year }}</p>
                        </div>
                        <div>
                            <p class="opacity-75">{{ __('Card Holder') }}</p>
                            <p class="font-semibold">{{ $card->card_holder_name }}</p>
                        </div>
                    </div>
                </div>
                <div class="p-4">
                    <div class="space-y-2 text-sm">
                        <div class="flex justify-between">
                            <span class="text-gray-500">{{ __('Account') }}:</span>
                            <span class="font-medium">{{ $card->account?->account_number ?? 'N/A' }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-500">{{ __('Brand') }}:</span>
                            <span class="font-medium">{{ $card->card_brand->label() }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-500">{{ __('Daily Limit') }}:</span>
                            <span class="font-medium">${{ number_format($card->daily_limit, 2) }}</span>
                        </div>
                    </div>
                    <div class="mt-4 pt-4 border-t">
                        <a href="{{ route('cards.show', $card) }}" class="text-emerald-600 hover:text-emerald-900 text-sm font-medium">View Details →</a>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-span-full text-center py-12 text-gray-500">
                <svg class="w-16 h-16 mx-auto mb-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path>
                </svg>
                <p class="text-lg">{{ __('No cards found') }}</p>
                <p class="text-sm mt-1">{{ __('Create your first card to get started') }}</p>
            </div>
        @endforelse
    </div>

    {{ $cards->links() }}
</div>
@endsection
