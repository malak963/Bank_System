@props(['value'])

<label {{ $attributes->merge(['class' => 'block text-xs font-semibold uppercase tracking-[0.08em] text-[#52677d]']) }}>
    {{ $value ?? $slot }}
</label>
