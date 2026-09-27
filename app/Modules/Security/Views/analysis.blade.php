@extends('layouts.app')

@section('title', __('Security Analysis'))

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('security.index') }}">{{ __('Security Events') }}</a></li>
    <li class="breadcrumb-item active">{{ __('Security Analysis') }}</li>
@endsection

@section('actions')
    <a href="{{ route('security.index') }}" class="btn btn-secondary">
        {{ __('Back to Security Events') }}
    </a>
@endsection

@section('content')
<div class="mx-auto max-w-4xl space-y-6 px-4 py-8 sm:px-6 lg:px-8">
    <div class="bank-card">
        <div class="bank-card-header">
            <h2 class="text-base font-semibold text-slate-950">{{ __('Anomalous Pattern Detection') }}</h2>
            <p class="text-xs text-slate-500 mt-0.5">{{ __('Customer ID:') }} <span class="font-mono font-semibold text-slate-800">{{ $customerId ?? __('All Customers') }}</span></p>
        </div>

        <div class="bank-card-body">
            @if(empty($anomalies))
                <div class="rounded-lg border border-emerald-200 bg-emerald-50 p-4">
                    <p class="text-xs font-medium text-emerald-800">{{ __('No anomalous patterns detected for this scope.') }}</p>
                </div>
            @else
                <div class="space-y-4">
                    @foreach($anomalies as $anomaly)
                        <div class="rounded-lg p-4 {{ $anomaly['severity'] === 'high' ? 'border border-rose-300 bg-rose-50' : 'border border-amber-300 bg-amber-50' }}">
                            <div class="flex justify-between items-start mb-2">
                                <h3 class="font-semibold text-sm {{ $anomaly['severity'] === 'high' ? 'text-rose-900' : 'text-amber-900' }}">
                                    {{ ucfirst(str_replace('_', ' ', $anomaly['type'])) }}
                                </h3>
                                <span class="px-2 py-0.5 text-xs font-semibold rounded-full {{ $anomaly['severity'] === 'high' ? 'bg-rose-100 text-rose-800' : 'bg-amber-100 text-amber-800' }}">
                                    {{ ucfirst($anomaly['severity']) }}
                                </span>
                            </div>
                            <p class="text-xs {{ $anomaly['severity'] === 'high' ? 'text-rose-700' : 'text-amber-700' }}">
                                {{ $anomaly['description'] }}
                            </p>
                            @if(isset($anomaly['count']))
                                <p class="text-xs text-slate-600 mt-1 font-mono">{{ __('Count:') }} {{ $anomaly['count'] }}</p>
                            @endif
                            @if(isset($anomaly['locations']))
                                <p class="text-xs text-slate-600 mt-1 font-mono">{{ __('Locations:') }} {{ implode(', ', $anomaly['locations']) }}</p>
                            @endif
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
