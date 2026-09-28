/**
 * Google Translate Field-Level Auto-Translator for Bank System
 * Enables bidirectional (Arabic <-> English) field translation across all form fields.
 */

(function () {
    'use strict';

    const TRANSLATE_ENDPOINT = '/translate/field';
    const CSRF_TOKEN = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

    // Translation function
    async function translateText(text, target = null, source = null, fieldName = '') {
        if (!text || !text.trim()) return null;

        const response = await fetch(TRANSLATE_ENDPOINT, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': CSRF_TOKEN,
            },
            body: JSON.stringify({
                text: text.trim(),
                target: target,
                source: source,
                field: fieldName,
            }),
        });

        if (!response.ok) {
            throw new Error(`Translation request failed (${response.status})`);
        }

        return await response.json();
    }

    // Attach translation button to a specific input or textarea element
    function attachTranslatorToField(field) {
        if (field.dataset.translatorAttached === 'true') return;
        if (field.type === 'hidden' || field.type === 'password' || field.type === 'file' || field.type === 'checkbox' || field.type === 'radio') return;
        if (field.classList.contains('no-auto-translate')) return;

        field.dataset.translatorAttached = 'true';

        // Find or create wrapper container
        let parent = field.parentElement;
        if (!parent) return;

        // Create the translate button
        const btn = document.createElement('button');
        btn.type = 'button';
        btn.tabIndex = -1;
        btn.className = 'field-translate-btn inline-flex items-center gap-1 px-1.5 py-0.5 text-[11px] font-medium text-emerald-700 bg-emerald-50 hover:bg-emerald-100 hover:text-emerald-800 rounded border border-emerald-200 transition-all shadow-2xs select-none';
        btn.title = 'ترجمة آلية عبر غوغل (عربي ⇄ إنكليزي) - Alt+T';
        btn.setAttribute('aria-label', 'Auto translate field with Google Translate');
        
        btn.innerHTML = `
            <svg class="w-3.5 h-3.5 translate-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5h12M9 3v2m1.048 9.5A18.022 18.022 0 016.412 9m6.088 9h7M11 21l5-10 5 10m-.188-5h-4.624M2.5 13.5A14.569 14.569 0 008 17.7M7 9a15.82 15.82 0 004.992 5.008"/>
            </svg>
            <span class="translate-label font-sans text-[10px]">ترجمة</span>
        `;

        // Position button near label if available, or floating at the end of the field
        const label = parent.querySelector('label') || parent.parentElement?.querySelector('label');
        if (label && !label.querySelector('.field-translate-btn')) {
            const container = document.createElement('span');
            container.className = 'inline-flex items-center ms-2';
            container.appendChild(btn);
            label.appendChild(container);
        } else {
            // If no label, position above or inline
            const wrapper = document.createElement('div');
            wrapper.className = 'flex justify-end mb-1';
            wrapper.appendChild(btn);
            field.insertAdjacentElement('beforebegin', wrapper);
        }

        // Click handler
        btn.addEventListener('click', async function (e) {
            e.preventDefault();
            e.stopPropagation();

            const currentValue = field.value || field.innerText;
            if (!currentValue || !currentValue.trim()) {
                field.focus();
                return;
            }

            // Check if already translated to toggle back (Undo)
            if (field.dataset.lastOriginal && field.value === field.dataset.lastTranslated) {
                field.value = field.dataset.lastOriginal;
                field.dispatchEvent(new Event('input', { bubbles: true }));
                field.dispatchEvent(new Event('change', { bubbles: true }));
                btn.querySelector('.translate-label').innerText = 'ترجمة';
                btn.classList.remove('bg-amber-50', 'text-amber-700', 'border-amber-200');
                btn.classList.add('bg-emerald-50', 'text-emerald-700', 'border-emerald-200');
                return;
            }

            // Show loading state
            const originalBtnHtml = btn.innerHTML;
            btn.disabled = true;
            btn.innerHTML = `
                <svg class="animate-spin w-3.5 h-3.5 text-emerald-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <span class="text-[10px]">جاري الترجمة...</span>
            `;

            try {
                const data = await translateText(currentValue, null, null, field.name || '');
                if (data && data.translated) {
                    field.dataset.lastOriginal = currentValue;
                    field.dataset.lastTranslated = data.translated;
                    field.value = data.translated;
                    field.dispatchEvent(new Event('input', { bubbles: true }));
                    field.dispatchEvent(new Event('change', { bubbles: true }));

                    // Update button to allow undo
                    btn.classList.remove('bg-emerald-50', 'text-emerald-700', 'border-emerald-200');
                    btn.classList.add('bg-amber-50', 'text-amber-700', 'border-amber-200');
                    btn.innerHTML = `
                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"/>
                        </svg>
                        <span class="translate-label text-[10px]">استرجاع (${data.source} ➔ ${data.target})</span>
                    `;
                }
            } catch (err) {
                console.error('Translation error:', err);
                btn.innerHTML = originalBtnHtml;
            } finally {
                btn.disabled = false;
            }
        });

        // Add keyboard shortcut Alt + T
        field.addEventListener('keydown', function (e) {
            if (e.altKey && (e.key === 't' || e.key === 'T' || e.key === 'ف')) {
                e.preventDefault();
                btn.click();
            }
        });
    }

    // Initialize across document
    function initAutoTranslators() {
        const fields = document.querySelectorAll('input[type="text"]:not([readonly]):not([disabled]), textarea:not([readonly]):not([disabled])');
        fields.forEach(attachTranslatorToField);
    }

    // Run on DOM load and when new content is dynamically inserted
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initAutoTranslators);
    } else {
        initAutoTranslators();
    }

    // Observe dynamic elements (e.g. modals, dynamic forms)
    const observer = new MutationObserver(function (mutations) {
        for (const mutation of mutations) {
            if (mutation.addedNodes.length) {
                initAutoTranslators();
                break;
            }
        }
    });

    observer.observe(document.body, { childList: true, subtree: true });

    // Expose helpers globally
    window.GoogleFieldTranslator = {
        translateText,
        attach: attachTranslatorToField,
        init: initAutoTranslators,
    };
})();
