@props([
    'title',
    'value',
    'hint' => null,
])

<div class="rounded-xl bg-white p-5 border border-slate-200">
    <p class="text-xs font-medium text-slate-500">{{ $title }}</p>
    <p class="mt-2 text-2xl font-bold font-mono text-slate-900 tracking-tight">{{ $value }}</p>
    @if($hint)
        <p class="mt-1 text-xs text-slate-400">{{ $hint }}</p>
    @endif
</div>
