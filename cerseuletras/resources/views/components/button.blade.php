@props([
    'href' => null,
    'type' => 'button',
    'variant' => 'solid',
    'color' => 'guinda',
    'size' => 'md',
    'icon' => null,
    'block' => false,
])

@php
    $palettes = [
        'guinda' => [
            'solid' => 'bg-unmsm-azul text-white hover:bg-unmsm-azul-dark focus-visible:bg-unmsm-azul-dark',
            'outline' => 'border border-unmsm-azul text-unmsm-azul hover:bg-unmsm-azul hover:text-white focus-visible:bg-unmsm-azul focus-visible:text-white',
            'ghost' => 'text-unmsm-azul hover:underline',
        ],
        'brand' => [
            'solid' => 'text-white hover:opacity-90 focus-visible:opacity-90 [background:var(--brand)] hover:[background:var(--brand-dark)]',
            'outline' => 'border [border-color:var(--brand)] [color:var(--brand)] hover:text-white hover:[background:var(--brand)] focus-visible:text-white focus-visible:[background:var(--brand)]',
            'ghost' => '[color:var(--brand)] hover:underline',
        ],
        'gold' => [
            'solid' => 'bg-unmsm-dorado/10 text-unmsm-azul hover:bg-unmsm-dorado hover:text-white focus-visible:bg-unmsm-dorado focus-visible:text-white',
            'outline' => 'border border-unmsm-dorado text-unmsm-azul hover:bg-unmsm-dorado hover:text-white focus-visible:bg-unmsm-dorado focus-visible:text-white',
            'ghost' => 'text-unmsm-dorado hover:underline',
        ],
    ];

    $sizes = [
        'sm' => 'px-4 py-2 text-xs',
        'md' => 'px-6 py-3 text-sm',
        'lg' => 'px-8 py-3.5 text-base',
    ];

    // Los variantes sólidos ganan profundidad (sombra que se realza en hover);
    // outline/ghost quedan planos para no competir visualmente.
    $depth = $variant === 'solid' ? ' shadow-sm hover:shadow-md motion-safe:hover:-translate-y-0.5' : '';

    $classes = 'inline-flex items-center justify-center gap-2 rounded-lg font-bold transition-all duration-200 '
        . 'motion-safe:active:translate-y-px '
        . 'focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-current '
        . ($palettes[$color][$variant] ?? $palettes['guinda']['solid']) . $depth . ' '
        . ($sizes[$size] ?? $sizes['md'])
        . ($block ? ' w-full' : '');

    $tag = $href ? 'a' : 'button';
@endphp

@if($tag === 'a')
    <a href="{{ $href }}" {{ $attributes->merge(['class' => $classes]) }}>
        @if($icon)<i class="{{ $icon }}" aria-hidden="true"></i>@endif
        {{ $slot }}
    </a>
@else
    <button type="{{ $type }}" {{ $attributes->merge(['class' => $classes]) }}>
        @if($icon)<i class="{{ $icon }}" aria-hidden="true"></i>@endif
        {{ $slot }}
    </button>
@endif
