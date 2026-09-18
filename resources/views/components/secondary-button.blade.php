<button {{ $attributes->merge(['type' => 'button', 'class' => 'inline-flex items-center justify-center gap-2 rounded-lg border border-[#cbd8e1] bg-white px-4 py-2.5 text-xs font-semibold uppercase tracking-[0.08em] text-[#29415d] shadow-sm transition duration-150 ease-in-out hover:border-[#0f9f8f] hover:text-[#08776f] focus:outline-none focus:ring-2 focus:ring-[#0f9f8f] focus:ring-offset-2 disabled:opacity-25']) }}>
    {{ $slot }}
</button>
