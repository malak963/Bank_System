@props([
    'for' => null,
    'target' => null,
    'label' => __('Translate'),
])

<button type="button"
    @if($for) data-target-input="{{ $for }}" @endif
    @if($target) data-target-lang="{{ $target }}" @endif
    onclick="(function(btn){
        const selector = btn.getAttribute('data-target-input');
        const field = selector ? document.querySelector('[name=\'' + selector + '\'], #' + selector) : btn.closest('div')?.querySelector('input, textarea');
        if(!field || !field.value.trim()) return;
        
        const origText = btn.innerHTML;
        btn.disabled = true;
        btn.innerText = '...';

        fetch('{{ route('web.translate.field') }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ text: field.value, target: btn.getAttribute('data-target-lang') || null })
        })
        .then(r => r.json())
        .then(data => {
            if(data.success && data.translated) {
                field.value = data.translated;
                field.dispatchEvent(new Event('input', { bubbles: true }));
                field.dispatchEvent(new Event('change', { bubbles: true }));
            }
        })
        .catch(console.error)
        .finally(() => {
            btn.disabled = false;
            btn.innerHTML = origText;
        });
    })(this)"
    {{ $attributes->merge(['class' => 'inline-flex items-center gap-1 px-2 py-0.5 text-xs font-medium text-emerald-700 bg-emerald-50 hover:bg-emerald-100 rounded border border-emerald-200 transition-colors shadow-2xs']) }}>
    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5h12M9 3v2m1.048 9.5A18.022 18.022 0 016.412 9m6.088 9h7M11 21l5-10 5 10m-.188-5h-4.624M2.5 13.5A14.569 14.569 0 008 17.7M7 9a15.82 15.82 0 004.992 5.008"/>
    </svg>
    <span>{{ $label }}</span>
</button>
