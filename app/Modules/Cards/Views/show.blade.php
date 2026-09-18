@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold text-gray-900">Card Details</h1>
        <a href="{{ route('cards.index') }}" class="text-gray-600 hover:text-gray-900">Back to Cards</a>
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

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2">
            <!-- Card Visual -->
            <div class="bg-gradient-to-r from-emerald-500 to-blue-500 rounded-xl p-6 mb-6 text-white shadow-lg">
                <div class="flex justify-between items-start mb-8">
                    <div>
                        <p class="text-sm font-medium opacity-90">{{ $card->card_type->label() }}</p>
                        <p class="text-2xl font-bold mt-2 tracking-wider">{{ $card->maskCardNumber() }}</p>
                    </div>
                    <span class="px-3 py-1 rounded-full text-sm font-semibold {{ $card->isActive() ? 'bg-white text-emerald-600' : 'bg-red-100 text-red-800' }}">
                        {{ $card->status->label() }}
                    </span>
                </div>
                <div class="flex justify-between items-end">
                    <div>
                        <p class="text-xs opacity-75">CARD HOLDER</p>
                        <p class="font-semibold">{{ $card->card_holder_name }}</p>
                    </div>
                    <div>
                        <p class="text-xs opacity-75">EXPIRES</p>
                        <p class="font-semibold">{{ str_pad($card->expiry_month, 2, '0', STR_PAD_LEFT) }}/{{ $card->expiry_year }}</p>
                    </div>
                    <div>
                        <p class="text-xs opacity-75">CVV</p>
                        <p class="font-semibold">***</p>
                    </div>
                </div>
            </div>

            <!-- Card Details -->
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-xl font-semibold text-gray-900 mb-4">Card Information</h2>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <p class="text-sm text-gray-500">Card Number</p>
                        <p class="text-lg font-medium text-gray-900">{{ $card->maskCardNumber() }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Brand</p>
                        <p class="text-lg font-medium text-gray-900">{{ $card->card_brand->label() }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Type</p>
                        <p class="text-lg font-medium text-gray-900">{{ $card->card_type->label() }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Status</p>
                        <p class="text-lg font-medium text-gray-900">{{ $card->status->label() }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Account</p>
                        <p class="text-lg font-medium text-gray-900">{{ $card->account?->account_number ?? 'N/A' }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Customer</p>
                        <p class="text-lg font-medium text-gray-900">{{ $card->customer?->full_name ?? 'N/A' }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Issued</p>
                        <p class="text-lg font-medium text-gray-900">{{ $card->issued_at?->format('M d, Y') ?? 'N/A' }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Expires</p>
                        <p class="text-lg font-medium text-gray-900">{{ $card->expires_at?->format('M d, Y') ?? 'N/A' }}</p>
                    </div>
                </div>

                @if($card->blocked_at)
                    <div class="mt-4 bg-red-50 border border-red-200 rounded-lg p-4">
                        <h3 class="text-sm font-semibold text-red-800">Block Information</h3>
                        <p class="text-sm text-red-700 mt-1">{{ $card->block_reason }}</p>
                        <p class="text-xs text-red-600 mt-1">Blocked on: {{ $card->blocked_at->format('M d, Y H:i') }}</p>
                    </div>
                @endif

                @if($card->replacement_reason)
                    <div class="mt-4 bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                        <h3 class="text-sm font-semibold text-yellow-800">Replacement Information</h3>
                        <p class="text-sm text-yellow-700 mt-1">{{ $card->replacement_reason }}</p>
                        @if($card->replacementCard)
                            <p class="text-xs text-yellow-600 mt-1">Replaced by: {{ $card->replacementCard->maskCardNumber() }}</p>
                        @endif
                    </div>
                @endif
            </div>
        </div>

        <div>
            <!-- Quick Actions -->
            <div class="bg-white rounded-lg shadow p-6 mb-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Quick Actions</h3>
                <div class="space-y-3">
                    @if($card->status === \App\Modules\Cards\Enums\CardStatus::Pending)
                        <form action="{{ route('cards.activate', $card) }}" method="POST">
                            @csrf
                            <div class="mb-3">
                                <label class="block text-sm font-medium text-gray-700 mb-1">PIN (4 digits)</label>
                                <input type="password" name="pin" required maxlength="4" pattern="[0-9]{4}" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500" placeholder="****">
                            </div>
                            <button type="submit" class="w-full bg-emerald-600 text-white px-4 py-2 rounded-lg hover:bg-emerald-700 text-sm">Activate Card</button>
                        </form>
                    @endif

                    @if($card->isActive())
                        <form action="{{ route('cards.block', $card) }}" method="POST">
                            @csrf
                            <div class="mb-3">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Block Reason</label>
                                <textarea name="reason" required rows="2" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500" placeholder="Enter reason..."></textarea>
                            </div>
                            <button type="submit" class="w-full bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700 text-sm">Block Card</button>
                        </form>
                    @endif

                    @if($card->status === \App\Modules\Cards\Enums\CardStatus::Blocked)
                        <form action="{{ route('cards.unblock', $card) }}" method="POST">
                            @csrf
                            @method('POST')
                            <button type="submit" class="w-full bg-emerald-600 text-white px-4 py-2 rounded-lg hover:bg-emerald-700 text-sm">Unblock Card</button>
                        </form>
                    @endif

                    @if($card->status->requiresReplacement())
                        <form action="{{ route('cards.replace', $card) }}" method="POST">
                            @csrf
                            <div class="mb-3">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Replacement Reason</label>
                                <textarea name="reason" required rows="2" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500" placeholder="Enter reason..."></textarea>
                            </div>
                            <button type="submit" class="w-full bg-yellow-600 text-white px-4 py-2 rounded-lg hover:bg-yellow-700 text-sm">Replace Card</button>
                        </form>
                    @endif
                </div>
            </div>

            <!-- Limits & Features -->
            <div class="bg-white rounded-lg shadow p-6 mb-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Limits & Features</h3>
                <div class="space-y-4">
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-600">Daily Limit</span>
                        <span class="text-sm font-medium">${{ number_format($card->daily_limit, 2) }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-600">Monthly Limit</span>
                        <span class="text-sm font-medium">${{ number_format($card->monthly_limit, 2) }}</span>
                    </div>
                    <div class="border-t pt-4">
                        <div class="flex justify-between items-center mb-2">
                            <span class="text-sm text-gray-600">International</span>
                            <span class="text-sm font-medium {{ $card->international_enabled ? 'text-emerald-600' : 'text-gray-400' }}">
                                {{ $card->international_enabled ? 'Enabled' : 'Disabled' }}
                            </span>
                        </div>
                        <div class="flex justify-between items-center mb-2">
                            <span class="text-sm text-gray-600">Online</span>
                            <span class="text-sm font-medium {{ $card->online_enabled ? 'text-emerald-600' : 'text-gray-400' }}">
                                {{ $card->online_enabled ? 'Enabled' : 'Disabled' }}
                            </span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-600">Contactless</span>
                            <span class="text-sm font-medium {{ $card->contactless_enabled ? 'text-emerald-600' : 'text-gray-400' }}">
                                {{ $card->contactless_enabled ? 'Enabled' : 'Disabled' }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- PIN Management -->
            @if($card->isActive())
                <div class="bg-white rounded-lg shadow p-6 mb-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">PIN Management</h3>
                    <form action="{{ route('cards.update-pin', $card) }}" method="POST">
                        @csrf
                        <div class="space-y-3">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Current PIN</label>
                                <input type="password" name="current_pin" required maxlength="4" pattern="[0-9]{4}" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500" placeholder="****">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">New PIN</label>
                                <input type="password" name="new_pin" required maxlength="4" pattern="[0-9]{4}" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500" placeholder="****">
                            </div>
                            <button type="submit" class="w-full bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-700 text-sm">Update PIN</button>
                        </div>
                    </form>
                </div>
            @endif

            @if($card->metadata)
                <div class="bg-white rounded-lg shadow p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Metadata</h3>
                    <pre class="text-xs text-gray-600 bg-gray-50 p-3 rounded overflow-auto">{{ json_encode($card->metadata, JSON_PRETTY_PRINT) }}</pre>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
