@props([
    'id',                 {{-- ID del vídeo de YouTube --}}
    'title' => 'Vídeo',
])

@php
    // Miniatura auto-alojada si existe; si no (vídeos añadidos después desde el
    // panel), se cae a la de YouTube, que sigue siendo una sola imagen frente
    // al reproductor completo.
    $local = 'images/video/' . $id . '.webp';
    $poster = is_file(public_path($local))
        ? asset($local)
        : 'https://img.youtube.com/vi/' . $id . '/hqdefault.jpg';
@endphp

{{--
    Vídeo con carga bajo demanda.

    Un iframe de YouTube arrastra cientos de KB de reproductor y cookies de
    terceros en cuanto se pinta la página, aunque nadie le dé al play. Aquí solo
    se muestra la miniatura y el reproductor se inserta al pulsar, usando el
    dominio sin cookies.

    Mismo criterio que la fachada del mapa en la portada.
--}}
<div x-data="{ activo: false }" class="w-full aspect-video rounded-lg overflow-hidden shadow-md relative bg-gray-900 not-prose my-4">

    <template x-if="activo">
        <iframe class="w-full h-full"
            src="https://www.youtube-nocookie.com/embed/{{ $id }}?autoplay=1&rel=0"
            title="{{ $title }}" frameborder="0"
            allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
            referrerpolicy="strict-origin-when-cross-origin" allowfullscreen></iframe>
    </template>

    <button type="button" x-show="activo === false" @click="activo = true"
        aria-label="Reproducir: {{ $title }}"
        class="group absolute inset-0 w-full h-full focus-visible:outline focus-visible:outline-2 focus-visible:outline-unmsm-dorado focus-visible:outline-offset-[-4px]">
        <img src="{{ $poster }}" alt="" aria-hidden="true" width="640" height="360"
            class="absolute inset-0 w-full h-full object-cover" loading="lazy" decoding="async">
        {{-- Velo para que el botón y el título se lean sobre cualquier miniatura --}}
        <span class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/30 to-black/20 group-hover:from-black/70 transition-colors"></span>
        <span class="relative flex h-full w-full flex-col items-center justify-center gap-3 text-white">
            <span class="w-16 h-16 rounded-full bg-red-600 flex items-center justify-center shadow-lg motion-safe:group-hover:scale-110 transition-transform">
                <x-fas-play class="text-2xl ml-1" aria-hidden="true" />
            </span>
            <span class="px-6 text-center text-sm font-semibold max-w-md drop-shadow">{{ $title }}</span>
            <span class="text-[11px] text-white/70">Se cargará desde YouTube al pulsar</span>
        </span>
    </button>

    {{-- Sin JavaScript, enlace directo al vídeo --}}
    <noscript>
        <a href="https://www.youtube.com/watch?v={{ $id }}" target="_blank" rel="noopener noreferrer"
            class="absolute inset-0 flex items-center justify-center text-white font-bold underline">
            Ver «{{ $title }}» en YouTube
        </a>
    </noscript>
</div>
