<button {{ $attributes->merge(['type' => 'submit', 'class' => 'inline-flex items-center justify-center gap-2 rounded-lg border border-transparent bg-[#b7434b] px-4 py-2.5 text-xs font-semibold uppercase tracking-[0.08em] text-white shadow-sm transition duration-150 ease-in-out hover:bg-[#96343c] focus:outline-none focus:ring-2 focus:ring-red-400 focus:ring-offset-2']) }}>
    {{ $slot }}
</button>
