@props(['items' => []])

<nav aria-label="{{ __('Breadcrumb') }}" {{ $attributes->merge(['class' => 'bank-breadcrumb-nav']) }}>
    <ol class="breadcrumb">
        <li class="breadcrumb-item">
            <a href="{{ route('dashboard') }}" class="inline-flex items-center gap-1.5 hover:text-emerald-700">
                <svg class="h-4 w-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="m2.25 12 8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25" />
                </svg>
                <span>{{ __('Dashboard') }}</span>
            </a>
        </li>

        @if (!empty($items))
            @foreach ($items as $item)
                @if ($loop->last || empty($item['url']))
                    <li class="breadcrumb-item active" aria-current="page">
                        <span>{{ $item['label'] ?? $item }}</span>
                    </li>
                @else
                    <li class="breadcrumb-item">
                        <a href="{{ $item['url'] }}" class="hover:text-emerald-700">{{ $item['label'] }}</a>
                    </li>
                @endif
            @endforeach
        @endif

        {{ $slot }}
    </ol>
</nav>
