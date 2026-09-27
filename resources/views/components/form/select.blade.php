@props([
    'id' => null,
    'label' => '',
    'name',
    'placeholder' => '',
    'options' => [],
    'selected' => '',
    'required' => false,
    'hint' => null,
])

@php
    $selectId = $id ?? $name;
    $currentVal = old($name, $selected);
@endphp

<div {{ $attributes->merge(['class' => 'space-y-1.5']) }}>
    @if($label)
        <label for="{{ $selectId }}" class="block text-xs font-semibold text-slate-700">
            {{ $label }}
            @if($required)
                <span class="text-rose-500 font-bold">*</span>
            @endif
        </label>
    @endif

    <div class="relative">
        <select
            name="{{ $name }}"
            id="{{ $selectId }}"
            @if($required) required @endif
            class="w-full rounded-lg border text-sm transition-all duration-150 px-3.5 py-2.5 bg-white
                   {{ $errors->has($name) 
                      ? 'border-rose-400 bg-rose-50/30 text-rose-900 focus:border-rose-500 focus:ring-2 focus:ring-rose-200' 
                      : 'border-slate-300 text-slate-800 hover:border-slate-400 focus:border-emerald-600 focus:ring-2 focus:ring-emerald-100' }} shadow-sm"
        >
            @if($placeholder)
                <option value="">{{ $placeholder }}</option>
            @endif

            @if(is_array($options) || is_iterable($options))
                @foreach($options as $key => $option)
                    <option value="{{ $key }}" {{ (string)$key === (string)$currentVal ? 'selected' : '' }}>
                        {{ is_array($option) ? ($option['label'] ?? $key) : $option }}
                    </option>
                @endforeach
            @endif

            {{ $slot }}
        </select>
    </div>

    @if($hint && !$errors->has($name))
        <p class="text-[11px] text-slate-500">{{ $hint }}</p>
    @endif

    @error($name)
        <p class="text-xs text-rose-600 flex items-center gap-1 font-medium mt-1">
            <svg class="h-3.5 w-3.5 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
            </svg>
            <span>{{ $message }}</span>
        </p>
    @enderror
</div>
