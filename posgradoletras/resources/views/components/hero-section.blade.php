@props(['title', 'subtitle' => null, 'label' => null, 'image' => null])

@php
    // JPEG progresivo para carga incremental (de borrosa a nítida)
    $defaultImage = 'https://images.unsplash.com/photo-1497633762265-9d179a990aa6?auto=format&fit=crop&w=1200&q=70&fm=pjpg';
    $bgImage = $image ?? $defaultImage;
    // Placeholder tiny blur (20px width) para instant load
    $tinyPlaceholder = 'data:image/svg+xml,%3Csvg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1200 600"%3E%3Cfilter id="b"%3E%3CfeGaussianBlur stdDeviation="12"/%3E%3C/filter%3E%3Crect width="100%25" height="100%25" fill="%236B1E20" filter="url(%23b)"/%3E%3C/svg%3E';
@endphp

<section
    class="relative w-full h-[40vh] md:h-[50vh] min-h-[300px] md:min-h-[400px] flex items-center justify-center overflow-hidden">
    {{-- Imagen de Fondo --}}
    <div class="absolute inset-0">
        {{-- Placeholder blur instant --}}
        <img src="{{ $tinyPlaceholder }}" alt="" class="absolute inset-0 w-full h-full object-cover" aria-hidden="true">
        {{-- Imagen real progresiva --}}
        <img src="{{ $bgImage }}" alt="{{ $title }}"
            class="absolute inset-0 w-full h-full object-cover transition-opacity duration-500" fetchpriority="high"
            decoding="async" width="1200" height="600" onload="this.style.opacity='1'" style="opacity:0">
        {{-- Overlay guinda sólido --}}
        <div class="absolute inset-0 bg-[#6B1E20]/80"></div>
    </div>

    {{-- Texto Hero --}}
    <div class="container mx-auto px-6 relative z-10 text-white pt-32 md:pt-20">
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