@props(['title', 'subtitle' => null, 'label' => null, 'image' => null])

@php
    $defaultImage = 'https://images.unsplash.com/photo-1497633762265-9d179a990aa6?ixlib=rb-4.0.3&auto=format&fit=crop&w=1920&q=80';
    $bgImage = $image ?? $defaultImage;
@endphp

<section class="relative w-full h-[50vh] min-h-[400px] flex items-center justify-center bg-gray-900 overflow-hidden">
    {{-- Imagen de Fondo --}}
    <div class="absolute inset-0 opacity-50">
        <img src="{{ $bgImage }}" alt="{{ $title }}" class="w-full h-full object-cover">
        <div class="absolute inset-0 bg-gradient-to-b from-black/60 via-transparent to-gray-900/90"></div>
    </div>

    {{-- Texto Hero --}}
    <div class="relative z-10 text-center text-white px-4 mt-20">
        @if($label)
            <p class="text-unmsm-dorado font-bold tracking-widest uppercase text-sm mb-3">{{ $label }}</p>
        @endif
        <h1 class="text-4xl md:text-6xl font-serif font-bold mb-6 drop-shadow-lg">{{ $title }}</h1>
        @if($subtitle)
            <p class="text-gray-200 max-w-2xl mx-auto font-light text-lg leading-relaxed">{{ $subtitle }}</p>
        @endif
    </div>
</section>