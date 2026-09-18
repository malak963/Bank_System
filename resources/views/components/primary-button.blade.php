<button {{ $attributes->merge(['type' => 'submit', 'class' => 'inline-flex items-center justify-center gap-2 rounded-lg border border-transparent bg-[#10243d] px-4 py-2.5 text-xs font-semibold uppercase tracking-[0.08em] text-white shadow-sm shadow-slate-900/15 transition duration-150 ease-in-out hover:bg-[#183653] focus:outline-none focus:ring-2 focus:ring-[#0f9f8f] focus:ring-offset-2']) }}>
    {{ $slot }}
</button>
