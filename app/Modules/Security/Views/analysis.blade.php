@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold text-gray-900">{{ __('Security Analysis') }}</h1>
        <a href="{{ route('security.index') }}" class="text-gray-600 hover:text-gray-900">{{ __('Back to Security Events') }}</a>
    </div>

    <div class="bg-white rounded-lg shadow p-6">
        <h2 class="text-xl font-semibold text-gray-900 mb-4">{{ __('Anomalous Pattern Detection') }}</h2>
        <p class="text-gray-600 mb-6">Customer ID: {{ $customerId ?? 'N/A' }}</p>

        @if(empty($anomalies))
            <div class="bg-emerald-50 border border-emerald-200 rounded-lg p-4">
                <p class="text-emerald-800">{{ __('No anomalous patterns detected for this customer.') }}</p>
            </div>
        @else
            <div class="space-y-4">
                @foreach($anomalies as $anomaly)
                    <div class="border rounded-lg p-4 {{ $anomaly['severity'] === 'high' ? 'border-red-300 bg-red-50' : 'border-yellow-300 bg-yellow-50' }}">
                        <div class="flex justify-between items-start mb-2">
                            <h3 class="font-semibold {{ $anomaly['severity'] === 'high' ? 'text-red-800' : 'text-yellow-800' }}">
                                {{ ucfirst(str_replace('_', ' ', $anomaly['type'])) }}
                            </h3>
                            <span class="px-2 py-1 text-xs font-semibold rounded-full {{ $anomaly['severity'] === 'high' ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800' }}">
                                {{ ucfirst($anomaly['severity']) }}
                            </span>
                        </div>
                        <p class="text-sm {{ $anomaly['severity'] === 'high' ? 'text-red-700' : 'text-yellow-700' }}">
                            {{ $anomaly['description'] }}
                        </p>
                        @if(isset($anomaly['count']))
                            <p class="text-xs text-gray-600 mt-1">Count: {{ $anomaly['count'] }}</p>
                        @endif
                        @if(isset($anomaly['locations']))
                            <p class="text-xs text-gray-600 mt-1">Locations: {{ implode(', ', $anomaly['locations']) }}</p>
                        @endif
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</div>
@endsection
