@extends('role-permession::layouts.ui')

@section('title', __('Create New Role'))

@section('content')
    @php
        $namePrefix = config('role-permession.ui.route_name_prefix', 'role-permession.');
    @endphp

    <div class="space-y-6">
        <div class="flex items-center justify-between pb-2">
            <p class="text-xs text-slate-500">{{ __('Define a new security role and configure its specific abilities') }}</p>
            <a href="{{ route($namePrefix.'roles.index') }}"
               class="btn btn-sm btn-secondary">
                &larr; {{ __('Back to Roles') }}
            </a>
        </div>

        <form method="POST" action="{{ route($namePrefix.'roles.store') }}">
            @csrf
            @include('role-permession::roles._form')

            <div class="flex items-center justify-end gap-3 pt-2">
                <a href="{{ route($namePrefix.'roles.index') }}"
                   class="btn btn-secondary">
                    {{ __('Cancel') }}
                </a>
                <button type="submit"
                        class="btn btn-primary">
                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                    </svg>
                    <span>{{ __('Create Role') }}</span>
                </button>
            </div>
        </form>
    </div>
@endsection
