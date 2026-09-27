@extends('role-permession::layouts.ui')

@section('title', __('Edit Role') . ': ' . $role->name)

@section('content')
    @php
        $namePrefix = config('role-permession.ui.route_name_prefix', 'role-permession.');
    @endphp

    <div class="space-y-6">
        <div class="flex items-center justify-between">
            <div>
                <h3 class="text-base font-semibold text-slate-950">{{ __('Edit Role') }}: <span class="text-emerald-700">{{ $role->name }}</span></h3>
                <p class="text-xs text-slate-500 mt-0.5">{{ __('Update role permissions and security ability grants') }}</p>
            </div>
            <a href="{{ route($namePrefix.'roles.index') }}"
               class="inline-flex items-center gap-1.5 rounded-lg border border-slate-300 bg-white px-3 py-1.5 text-xs font-semibold text-slate-700 hover:bg-slate-50 transition shadow-xs">
                &larr; {{ __('Back to Roles') }}
            </a>
        </div>

        <form method="POST" action="{{ route($namePrefix.'roles.update', $role) }}">
            @csrf
            @method('PUT')
            @include('role-permession::roles._form')

            <div class="flex items-center justify-end gap-3 pt-2">
                <a href="{{ route($namePrefix.'roles.index') }}"
                   class="rounded-lg border border-slate-300 bg-white px-4 py-2.5 text-xs font-semibold text-slate-700 hover:bg-slate-50 transition">
                    {{ __('Cancel') }}
                </a>
                <button type="submit"
                        class="inline-flex items-center gap-2 rounded-lg bg-emerald-600 px-5 py-2.5 text-xs font-semibold text-white shadow-sm hover:bg-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2 transition">
                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" />
                    </svg>
                    <span>{{ __('Save Changes') }}</span>
                </button>
            </div>
        </form>
    </div>
@endsection
