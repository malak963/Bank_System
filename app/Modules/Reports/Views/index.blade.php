@extends('layouts.app')

@section('title', __('Reports'))

@section('breadcrumb')
    <li class="breadcrumb-item active">{{ __('Reports') }}</li>
@endsection

@section('actions')
    <form action="{{ route('reports.process-scheduled') }}" method="POST" class="inline">
        @csrf
        <button type="submit" class="btn btn-secondary">
            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            <span>{{ __('Process Scheduled') }}</span>
        </button>
    </form>
    <form action="{{ route('reports.cleanup-expired') }}" method="POST" class="inline">
        @csrf
        <button type="submit" class="btn btn-secondary text-rose-700 hover:text-rose-800">
            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
            </svg>
            <span>{{ __('Cleanup Expired') }}</span>
        </button>
    </form>
    <a href="{{ route('reports.create') }}" class="btn btn-primary">
        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"></path>
        </svg>
        <span>{{ __('New Report') }}</span>
    </a>
@endsection

@section('content')
<div class="mx-auto max-w-7xl space-y-6 px-4 py-8 sm:px-6 lg:px-8">
    <div class="bank-card">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200">
                <thead class="bg-slate-50">
                    <tr>
                        <th class="px-5 py-3.5 text-start text-xs font-semibold uppercase tracking-wider text-slate-500">{{ __('Title') }}</th>
                        <th class="px-5 py-3.5 text-start text-xs font-semibold uppercase tracking-wider text-slate-500">{{ __('Type') }}</th>
                        <th class="px-5 py-3.5 text-start text-xs font-semibold uppercase tracking-wider text-slate-500">{{ __('Format') }}</th>
                        <th class="px-5 py-3.5 text-start text-xs font-semibold uppercase tracking-wider text-slate-500">{{ __('Status') }}</th>
                        <th class="px-5 py-3.5 text-start text-xs font-semibold uppercase tracking-wider text-slate-500">{{ __('Branch') }}</th>
                        <th class="px-5 py-3.5 text-start text-xs font-semibold uppercase tracking-wider text-slate-500">{{ __('Generated') }}</th>
                        <th class="px-5 py-3.5 text-end text-xs font-semibold uppercase tracking-wider text-slate-500">{{ __('Actions') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 bg-white">
                    @forelse($reports as $report)
                        <tr class="transition hover:bg-slate-50">
                            <td class="px-5 py-4 whitespace-nowrap text-sm font-medium text-slate-900">
                                <a href="{{ route('reports.show', $report) }}" class="hover:text-emerald-700">
                                    {{ $report->title }}
                                </a>
                            </td>
                            <td class="px-5 py-4 whitespace-nowrap text-sm text-slate-600">
                                {{ $report->report_type->label() }}
                            </td>
                            <td class="px-5 py-4 whitespace-nowrap text-sm font-mono text-slate-600 uppercase" dir="ltr">
                                {{ $report->format->value }}
                            </td>
                            <td class="px-5 py-4 whitespace-nowrap text-sm">
                                <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-semibold ring-1 ring-inset {{ $report->status->badgeColor() }}">
                                    {{ $report->status->label() }}
                                </span>
                            </td>
                            <td class="px-5 py-4 whitespace-nowrap text-sm text-slate-600">
                                {{ $report->branch?->name ?? __('All Branches') }}
                            </td>
                            <td class="px-5 py-4 whitespace-nowrap text-sm text-slate-500 font-mono" dir="ltr">
                                {{ $report->generated_at?->format('Y-m-d H:i') ?? __('Pending') }}
                            </td>
                            <td class="px-5 py-4 whitespace-nowrap text-end text-sm space-x-1 rtl:space-x-reverse">
                                <a href="{{ route('reports.show', $report) }}" class="btn btn-sm btn-secondary">
                                    {{ __('View') }}
                                </a>
                                @if($report->isCompleted() && $report->file_path)
                                    <a href="{{ route('reports.download', $report) }}" class="btn btn-sm btn-secondary text-emerald-700">
                                        {{ __('Download') }}
                                    </a>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center text-slate-500">
                                <p class="text-sm font-semibold text-slate-700">{{ __('No reports found.') }}</p>
                                <p class="mt-1 text-xs text-slate-400">{{ __('Generate financial, customer, or audit reports.') }}</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @if (method_exists($reports, 'hasPages') && $reports->hasPages())
        <div class="rounded-xl border border-slate-200 bg-white px-4 py-3 shadow-sm">
            {{ $reports->links() }}
        </div>
    @endif
</div>
@endsection
