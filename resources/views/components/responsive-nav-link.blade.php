@props(['active'])

@php
$classes = ($active ?? false)
            ? 'block w-full rounded-md border-l-4 border-emerald-300 bg-slate-900 py-2 ps-3 pe-4 text-start text-base font-medium text-white focus:outline-none focus:text-white focus:bg-slate-900 focus:border-emerald-200 transition duration-150 ease-in-out'
            : 'block w-full rounded-md border-l-4 border-transparent py-2 ps-3 pe-4 text-start text-base font-medium text-slate-300 hover:bg-slate-900 hover:text-white hover:border-slate-500 focus:outline-none focus:text-white focus:bg-slate-900 focus:border-slate-500 transition duration-150 ease-in-out';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>
