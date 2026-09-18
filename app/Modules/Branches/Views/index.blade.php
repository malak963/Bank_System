<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <p class="text-xs font-semibold uppercase text-emerald-700">{{ __('Branch Operations') }}</p>
                <h2 class="mt-1 text-2xl font-semibold text-slate-950">{{ __('Branches') }}</h2>
            </div>
            <a href="{{ route('branches.create') }}" class="inline-flex items-center gap-2 rounded-lg bg-slate-950 px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-slate-800">+ {{ __('Create New Branch') }}</a>
        </div>
    </x-slot>

    @php $filterValue = fn (string $key): string => (string) ($filters[$key] ?? ''); @endphp

    <div class="mx-auto max-w-7xl space-y-6 px-4 py-8 sm:px-6 lg:px-8">
        @if (session('status'))
            <div class="rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-800">{{ session('status') }}</div>
        @endif
        @if ($errors->any())
            <div class="rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">{{ $errors->first() }}</div>
        @endif

        <div class="grid grid-cols-2 gap-4 lg:grid-cols-3">
            <div class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm"><p class="text-xs font-semibold uppercase text-slate-500">{{ __('Total Branches') }}</p><p class="mt-2 text-3xl font-semibold text-slate-950">{{ number_format($summary['total']) }}</p></div>
            <div class="rounded-lg border border-emerald-200 bg-emerald-50 p-5 shadow-sm"><p class="text-xs font-semibold uppercase text-emerald-700">{{ __('Open') }}</p><p class="mt-2 text-3xl font-semibold text-emerald-950">{{ number_format($summary['open']) }}</p></div>
            <div class="rounded-lg border border-slate-200 bg-slate-50 p-5 shadow-sm"><p class="text-xs font-semibold uppercase text-slate-600">{{ __('Closed') }}</p><p class="mt-2 text-3xl font-semibold text-slate-950">{{ number_format($summary['closed']) }}</p></div>
        </div>

        <form method="GET" action="{{ route('branches.index') }}" class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
            <div class="grid grid-cols-1 gap-4 lg:grid-cols-12">
                <div class="lg:col-span-7"><x-input-label for="search" :value="__('Search')" /><x-text-input id="search" name="search" type="search" class="mt-1 block w-full" :value="$filterValue('search')" placeholder="{{ __('Code, name, address, phone or email') }}" /></div>
                <div class="lg:col-span-2"><x-input-label for="status" :value="__('Status')" /><select id="status" name="status" class="mt-1 block w-full rounded-lg border-slate-300 bg-white text-sm shadow-sm focus:border-emerald-500 focus:ring-emerald-500"><option value="">{{ __('Any') }}</option>@foreach ($statuses as $status)<option value="{{ $status->value }}" @selected($filterValue('status') === $status->value)>{{ $status->label() }}</option>@endforeach</select></div>
                <div class="flex items-end gap-2 lg:col-span-3"><button type="submit" class="inline-flex min-h-10 flex-1 items-center justify-center rounded-lg bg-slate-950 px-4 py-2 text-sm font-semibold text-white hover:bg-slate-800">{{ __('Filter') }}</button><a href="{{ route('branches.index') }}" class="inline-flex min-h-10 items-center justify-center rounded-lg border border-slate-200 px-3 py-2 text-sm font-semibold text-slate-700 hover:border-emerald-300">{{ __('Reset') }}</a></div>
            </div>
        </form>

        <div class="overflow-hidden rounded-lg border border-slate-200 bg-white shadow-sm">
            <div class="flex items-center justify-between border-b border-slate-200 px-5 py-4"><div><h3 class="font-semibold text-slate-950">{{ __('Branch List') }}</h3><p class="text-sm text-slate-500">{{ __('Branch information and operational state') }}</p></div><span class="text-sm text-slate-500">{{ $branches->firstItem() ?? 0 }}-{{ $branches->lastItem() ?? 0 }} / {{ $branches->total() }}</span></div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-200">
                    <thead class="bg-slate-50"><tr><th class="px-5 py-3 text-left text-xs font-semibold uppercase text-slate-500">{{ __('Branch') }}</th><th class="px-5 py-3 text-left text-xs font-semibold uppercase text-slate-500">{{ __('Contact') }}</th><th class="px-5 py-3 text-left text-xs font-semibold uppercase text-slate-500">{{ __('Manager') }}</th><th class="px-5 py-3 text-left text-xs font-semibold uppercase text-slate-500">{{ __('Status') }}</th><th class="px-5 py-3 text-right text-xs font-semibold uppercase text-slate-500">{{ __('Actions') }}</th></tr></thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($branches as $branch)
                            @php $statusClass = match ($branch->status->value) { 'open' => 'bg-emerald-50 text-emerald-700 ring-emerald-200', default => 'bg-slate-100 text-slate-700 ring-slate-200' }; @endphp
                            <tr class="hover:bg-slate-50">
                                <td class="px-5 py-4 text-sm"><a href="{{ route('branches.show', $branch) }}" class="font-bold text-slate-950 hover:text-emerald-700">{{ $branch->code }}</a><p class="mt-1 font-semibold text-slate-700">{{ $branch->name }}</p><p class="mt-1 text-xs text-slate-500">{{ $branch->address }}</p></td>
                                <td class="px-5 py-4 text-sm text-slate-700"><p class="text-slate-700">{{ $branch->phone }}</p><p class="text-xs text-slate-500">{{ $branch->email }}</p></td>
                                <td class="px-5 py-4 text-sm text-slate-700">{{ $branch->manager?->name ?? __('Not assigned') }}</td>
                                <td class="px-5 py-4 text-sm"><span class="inline-flex rounded-full px-2.5 py-1 text-xs font-semibold ring-1 ring-inset {{ $statusClass }}">{{ $branch->status->label() }}</span></td>
                                <td class="px-5 py-4 text-right text-sm"><a href="{{ route('branches.show', $branch) }}" class="font-semibold text-emerald-700 hover:text-emerald-900">{{ __('Manage') }}</a></td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="px-6 py-14 text-center text-sm text-slate-500">{{ __('No branches found.') }}</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        {{ $branches->links() }}
    </div>
</x-app-layout>
