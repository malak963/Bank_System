@props(['active'])

@php
$classes = ($active ?? false)
            ? 'inline-flex items-center rounded-md border-b-2 border-emerald-300 bg-white/5 px-3 pt-1 text-sm font-semibold leading-5 text-white focus:outline-none focus:border-emerald-200 transition duration-150 ease-in-out'
            : 'inline-flex items-center rounded-md border-b-2 border-transparent px-3 pt-1 text-sm font-medium leading-5 text-slate-300 hover:bg-white/5 hover:text-white hover:border-slate-500 focus:outline-none focus:text-white focus:border-slate-500 transition duration-150 ease-in-out';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>
