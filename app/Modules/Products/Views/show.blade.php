@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold text-gray-900">Product Details</h1>
        <a href="{{ route('products.index') }}" class="text-gray-600 hover:text-gray-900">Back to Products</a>
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
            <div class="bg-white rounded-lg shadow p-6 mb-6">
                <div class="flex justify-between items-start mb-6">
                    <div>
                        <h2 class="text-xl font-semibold text-gray-900">{{ $product->product_number }}</h2>
                        <p class="text-sm text-gray-500">{{ $product->name }}</p>
                    </div>
                    @switch($product->status->value)
                        @case('active')
                            <span class="px-3 py-1 inline-flex text-sm leading-5 font-semibold rounded-full bg-emerald-100 text-emerald-800">Active</span>
                            @break
                        @case('inactive')
                            <span class="px-3 py-1 inline-flex text-sm leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">Inactive</span>
                            @break
                        @case('matured')
                            <span class="px-3 py-1 inline-flex text-sm leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">Matured</span>
                            @break
                        @case('closed')
                            <span class="px-3 py-1 inline-flex text-sm leading-5 font-semibold rounded-full bg-red-100 text-red-800">Closed</span>
                            @break
                        @case('suspended')
                            <span class="px-3 py-1 inline-flex text-sm leading-5 font-semibold rounded-full bg-orange-100 text-orange-800">Suspended</span>
                            @break
                        @case('pending')
                            <span class="px-3 py-1 inline-flex text-sm leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">Pending</span>
                            @break
                    @endswitch
                </div>

                <div class="border-t pt-4">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Product Information</h3>
                    <dl class="grid grid-cols-1 gap-4">
                        <div class="flex justify-between">
                            <dt class="text-sm text-gray-500">Type</dt>
                            <dd class="text-sm font-medium text-gray-900">{{ $product->product_type->label() }}</dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-sm text-gray-500">Customer</dt>
                            <dd class="text-sm font-medium text-gray-900">{{ $product->customer?->full_name ?? 'N/A' }}</dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-sm text-gray-500">Account</dt>
                            <dd class="text-sm font-medium text-gray-900">{{ $product->account?->account_number ?? 'N/A' }}</dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-sm text-gray-500">Branch</dt>
                            <dd class="text-sm font-medium text-gray-900">{{ $product->branch?->name ?? 'N/A' }}</dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-sm text-gray-500">Balance</dt>
                            <dd class="text-sm font-medium text-gray-900">${{ number_format($product->balance, 2) }}</dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-sm text-gray-500">Interest Rate</dt>
                            <dd class="text-sm font-medium text-gray-900">{{ $product->interest_rate ? $product->interest_rate . '%' : 'N/A' }}</dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-sm text-gray-500">Interest Earned</dt>
                            <dd class="text-sm font-medium text-gray-900">${{ number_format($product->interest_earned, 2) }}</dd>
                        </div>
                        @if($product->term_months)
                            <div class="flex justify-between">
                                <dt class="text-sm text-gray-500">Term</dt>
                                <dd class="text-sm font-medium text-gray-900">{{ $product->term_months }} months</dd>
                            </div>
                        @endif
                        <div class="flex justify-between">
                            <dt class="text-sm text-gray-500">Maturity Date</dt>
                            <dd class="text-sm font-medium text-gray-900">{{ $product->maturity_date?->format('M d, Y') ?? 'N/A' }}</dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-sm text-gray-500">Auto Renew</dt>
                            <dd class="text-sm font-medium text-gray-900">{{ $product->auto_renew ? 'Yes' : 'No' }}</dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-sm text-gray-500">Min Balance</dt>
                            <dd class="text-sm font-medium text-gray-900">${{ number_format($product->min_balance, 2) }}</dd>
                        </div>
                        @if($product->max_balance)
                            <div class="flex justify-between">
                                <dt class="text-sm text-gray-500">Max Balance</dt>
                                <dd class="text-sm font-medium text-gray-900">${{ number_format($product->max_balance, 2) }}</dd>
                            </div>
                        @endif
                        <div class="flex justify-between">
                            <dt class="text-sm text-gray-500">Opened</dt>
                            <dd class="text-sm font-medium text-gray-900">{{ $product->opened_at?->format('M d, Y H:i') ?? 'N/A' }}</dd>
                        </div>
                        @if($product->closed_at)
                            <div class="flex justify-between">
                                <dt class="text-sm text-gray-500">Closed</dt>
                                <dd class="text-sm font-medium text-gray-900">{{ $product->closed_at->format('M d, Y H:i') }}</dd>
                            </div>
                        @endif
                    </dl>
                </div>

                @if($product->notes)
                    <div class="mt-4 bg-gray-50 border border-gray-200 rounded-lg p-4">
                        <h3 class="text-sm font-semibold text-gray-800 mb-2">Notes</h3>
                        <p class="text-sm text-gray-700">{{ $product->notes }}</p>
                    </div>
                @endif
            </div>

            <!-- Transactions Section -->
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Related Transactions</h3>
                @if($product->transactions->count() > 0)
                    <div class="space-y-2">
                        @foreach($product->transactions->take(5) as $transaction)
                            <div class="border rounded-lg p-3 bg-gray-50">
                                <div class="flex justify-between items-center">
                                    <span class="text-sm font-medium text-gray-900">{{ $transaction->transaction_reference }}</span>
                                    <span class="text-xs text-gray-500">{{ $transaction->created_at->format('M d, Y H:i') }}</span>
                                </div>
                                <p class="text-sm text-gray-600 mt-1">{{ $transaction->transaction_type->label() }} - ${{ number_format($transaction->amount, 2) }}</p>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-sm text-gray-500">No transactions yet</p>
                @endif
            </div>
        </div>

        <div>
            <div class="bg-white rounded-lg shadow p-6 mb-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Actions</h3>
                <div class="space-y-3">
                    @if($product->isActive())
                        <form action="{{ route('products.apply-interest', $product) }}" method="POST">
                            @csrf
                            <button type="submit" class="w-full bg-emerald-600 text-white px-4 py-2 rounded-lg hover:bg-emerald-700 text-sm">Apply Interest</button>
                        </form>
                    @endif

                    @if($product->isActive() || $product->isMatured())
                        <form action="{{ route('products.close', $product) }}" method="POST">
                            @csrf
                            <div class="mb-3">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Reason</label>
                                <textarea name="reason" rows="2" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500" placeholder="Enter close reason..."></textarea>
                            </div>
                            <button type="submit" class="w-full bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700 text-sm">Close Product</button>
                        </form>
                    @endif

                    @if($product->isMatured())
                        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-3">
                            <p class="text-sm text-yellow-800">
                                <strong>Matured:</strong> This product has reached maturity
                            </p>
                        </div>
                    @endif
                </div>
            </div>

            @if($product->metadata)
                <div class="bg-white rounded-lg shadow p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Metadata</h3>
                    <pre class="text-xs text-gray-600 bg-gray-50 p-3 rounded overflow-auto">{{ json_encode($product->metadata, JSON_PRETTY_PRINT) }}</pre>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
