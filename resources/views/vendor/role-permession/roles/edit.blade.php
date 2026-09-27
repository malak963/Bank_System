@extends('role-permession::layouts.ui')

@section('title', __('Edit Role') . ': ' . $role->name)

@section('content')
    @php
        $namePrefix = config('role-permession.ui.route_name_prefix', 'role-permession.');
    @endphp

    <div class="space-y-6">
        <div class="flex items-center justify-between pb-2">
            <p class="text-xs text-slate-500">{{ __('Update role permissions and security ability grants') }}</p>
            <a href="{{ route($namePrefix.'roles.index') }}"
               class="btn btn-sm btn-secondary">
                &larr; {{ __('Back to Roles') }}
            </a>
        </div>

        <form method="POST" action="{{ route($namePrefix.'roles.update', $role) }}">
            @csrf
            @method('PUT')
            @include('role-permession::roles._form')

            <div class="flex items-center justify-end gap-3 pt-2">
                <a href="{{ route($namePrefix.'roles.index') }}"
                   class="btn btn-secondary">
                    {{ __('Cancel') }}
                </a>
                <button type="submit"
                        class="btn btn-primary">
                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" />
                    </svg>
                    <span>{{ __('Save Changes') }}</span>
                </button>
            </div>
        </form>
    </div>
@endsection
