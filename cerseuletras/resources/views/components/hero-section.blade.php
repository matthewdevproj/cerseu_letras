@props(['title', 'subtitle' => null, 'label' => null, 'image' => null])

@php
    // Foto real del campus UNMSM (auto-alojada) — evita el look genérico de stock.
    $defaultImage = asset('images/campus-aerea.jpg');
    $bgImage = $image ?? $defaultImage;
    // Si existe una versión .webp junto a la imagen (local), la servimos con
    // <picture>; para imágenes de BD sin webp, cae al JPEG/PNG original.
    $webpSrc = preg_replace('/\.(jpe?g|png)$/i', '.webp', $bgImage);
    $webpRel = ltrim(parse_url($webpSrc, PHP_URL_PATH) ?? '', '/');
    $hasWebp = $webpSrc !== $bgImage && $webpRel !== '' && is_file(public_path($webpRel));
    // Placeholder tiny blur (20px width) para instant load
    $tinyPlaceholder = 'data:image/svg+xml,%3Csvg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1200 600"%3E%3Cfilter id="b"%3E%3CfeGaussianBlur stdDeviation="12"/%3E%3C/filter%3E%3Crect width="100%25" height="100%25" fill="%23143B63" filter="url(%23b)"/%3E%3C/svg%3E';
@endphp

<section class="relative w-full min-h-[40vh] md:min-h-[50vh] flex items-center justify-center overflow-hidden h-auto">
    {{-- Imagen de Fondo --}}
    <div class="absolute inset-0">
        {{-- Placeholder blur instant --}}
        <img src="{{ $tinyPlaceholder }}" alt="" class="absolute inset-0 w-full h-full object-cover" aria-hidden="true">
        {{-- Imagen real progresiva (WebP con fallback si existe el sibling) --}}
        <picture>
            @if($hasWebp)<source srcset="{{ $webpSrc }}" type="image/webp">@endif
            <img src="{{ $bgImage }}" alt="{{ $title }}"
                class="absolute inset-0 w-full h-full object-cover transition-opacity duration-500" fetchpriority="high"
                decoding="async" width="1200" height="600" onload="this.style.opacity='1'" style="opacity:0">
        </picture>
        {{-- Overlay guinda en degradado (más oscuro abajo): da profundidad y
             mejora el contraste del texto sobre la imagen sin recursos extra. --}}
        <div class="absolute inset-0 bg-gradient-to-t from-[#143B63]/95 via-[#143B63]/80 to-[#143B63]/70"></div>
    </div>

    {{-- Texto Hero --}}
    <div class="container mx-auto px-6 relative z-10 text-white pt-32 pb-12 md:pt-40 md:pb-12">
        <div class="max-w-3xl">
            @if($label)
                <p class="text-unmsm-dorado font-bold tracking-widest uppercase text-sm mb-3">{{ $label }}</p>
            @endif
            @php
                $titleLength = strlen($title);
                $titleClass = $titleLength > 50
                    ? 'text-3xl md:text-4xl lg:text-5xl'
                    : 'text-4xl md:text-5xl lg:text-6xl';
            @endphp
            <h1 class="{{ $titleClass }} font-serif font-bold mb-4 drop-shadow-lg leading-tight">
                {{ $title }}
            </h1>
            @if($subtitle)
                <p class="text-gray-200 max-w-2xl font-normal text-lg leading-relaxed">{{ $subtitle }}</p>
            @endif
        </div>
    </div>
</section>