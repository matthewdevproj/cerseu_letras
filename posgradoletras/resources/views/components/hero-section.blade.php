@props(['title', 'subtitle' => null, 'label' => null, 'image' => null])

@php
    $defaultImage = 'https://images.unsplash.com/photo-1497633762265-9d179a990aa6?ixlib=rb-4.0.3&auto=format&fit=crop&w=1920&q=80';
    $bgImage = $image ?? $defaultImage;
@endphp

<section class="relative w-full h-[50vh] min-h-[400px] flex items-center justify-center overflow-hidden">
    {{-- Imagen de Fondo --}}
    <div class="absolute inset-0">
        <img src="{{ $bgImage }}" alt="{{ $title }}" class="w-full h-full object-cover">
        {{-- Overlay guinda sólido --}}
        <div class="absolute inset-0 bg-[#6B1E20]/80"></div>
    </div>

    {{-- Texto Hero --}}
    <div class="container mx-auto px-6 relative z-10 text-white pt-20">
        <div class="max-w-3xl">
            @if($label)
                <p class="text-unmsm-dorado font-bold tracking-widest uppercase text-sm mb-3">{{ $label }}</p>
            @endif
            <h1 class="text-4xl md:text-5xl lg:text-6xl font-serif font-bold mb-4 drop-shadow-lg leading-tight">
                {{ $title }}
            </h1>
            @if($subtitle)
                <p class="text-gray-200 max-w-2xl font-light text-lg leading-relaxed">{{ $subtitle }}</p>
            @endif
        </div>
    </div>
</section>