@props(['disabled' => false])

<input @disabled($disabled) {{ $attributes->merge(['class' => 'rounded-lg border-[#cbd8e1] bg-white text-[#10243d] shadow-sm focus:border-[#0f9f8f] focus:ring-[#0f9f8f]']) }}>
