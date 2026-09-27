@extends('role-permession::layouts.ui')

@section('title', __('Create New Role'))

@section('content')
    @php
        $namePrefix = config('role-permession.ui.route_name_prefix', 'role-permession.');
    @endphp

    <div class="space-y-6">
        <div class="flex items-center justify-between">
            <div>
                <h3 class="text-base font-semibold text-slate-950">{{ __('Create New Role') }}</h3>
                <p class="text-xs text-slate-500 mt-0.5">{{ __('Define a new security role and configure its specific abilities') }}</p>
            </div>
            <a href="{{ route($namePrefix.'roles.index') }}"
               class="inline-flex items-center gap-1.5 rounded-lg border border-slate-300 bg-white px-3 py-1.5 text-xs font-semibold text-slate-700 hover:bg-slate-50 transition shadow-xs">
                &larr; {{ __('Back to Roles') }}
            </a>
        </div>

        <form method="POST" action="{{ route($namePrefix.'roles.store') }}">
            @csrf
            @include('role-permession::roles._form')

            <div class="flex items-center justify-end gap-3 pt-2">
                <a href="{{ route($namePrefix.'roles.index') }}"
                   class="rounded-lg border border-slate-300 bg-white px-4 py-2.5 text-xs font-semibold text-slate-700 hover:bg-slate-50 transition">
                    {{ __('Cancel') }}
                </a>
                <button type="submit"
                        class="inline-flex items-center gap-2 rounded-lg bg-emerald-600 px-5 py-2.5 text-xs font-semibold text-white shadow-sm hover:bg-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2 transition">
                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                    </svg>
                    <span>{{ __('Create Role') }}</span>
                </button>
            </div>
        </form>
    </div>
@endsection
