@extends('layouts.public')

@section('title', 'Diplomados - Posgrado Letras UNMSM')

@section('content')

    {{-- HERO DE DIPLOMADOS (con formulario de solicitud de información integrado) --}}
    <section class="relative w-full overflow-hidden">
        <div class="absolute inset-0">
            {{-- WebP en vez del JPG: esta cabecera no pasa por
                 <x-hero-section>, así que se quedó cargando el original de
                 963 KB con prioridad alta. El WebP pesa 279 KB. --}}
            <img src="{{ $settings?->diplomados_hero_imagen ? asset('storage/' . $settings->diplomados_hero_imagen) : asset('images/campus-fachada.webp') }}"
                alt="Diplomados" class="absolute inset-0 w-full h-full object-cover"
                width="1600" height="900" fetchpriority="high" decoding="async">
            <div class="absolute inset-0 bg-unmsm-guinda/85"></div>
        </div>

        <div class="container mx-auto px-6 relative z-10 pt-32 pb-16 md:pt-40 md:pb-20">
            <div class="grid lg:grid-cols-2 gap-10 items-start">

                {{-- Columna izquierda: título, texto, claim, botones --}}
                <div class="text-white">
                    <p class="text-unmsm-dorado font-bold tracking-widest uppercase text-sm mb-3">Oferta Académica</p>
                    <h1 class="text-4xl md:text-5xl lg:text-6xl font-serif font-bold mb-4 drop-shadow-lg leading-tight">
                        {{ $settings?->diplomados_hero_titulo ?? 'Diplomados' }}
                    </h1>
                    <p class="text-gray-200 max-w-xl font-normal text-lg leading-relaxed mb-4">
                        {{ $settings?->diplomados_hero_texto ?? 'Especializa tus conocimientos con programas diseñados para responder a los desafíos contemporáneos desde las humanidades y las nuevas tecnologías.' }}
                    </p>
                    <p class="text-unmsm-dorado font-bold text-xl mb-8">
                        {{ $settings?->diplomados_hero_claim ?? 'El conocimiento evoluciona. Tu formación también.' }}
                    </p>

                    <div class="flex flex-wrap gap-4">
                        <x-button href="{{ route('diplomados.admision') }}" target="_blank" rel="noopener noreferrer" icon="fa-solid fa-user-plus"
                            class="border border-white/30 shadow-lg">Admisión</x-button>
                        <x-button href="#oferta-diplomados" icon="fa-solid fa-arrow-down"
                            class="!bg-white !text-unmsm-guinda hover:!bg-unmsm-dorado hover:!text-white shadow-lg">Conoce más</x-button>
                    </div>
                </div>

                {{-- Columna derecha: formulario de solicitud de información --}}
                <div class="bg-white rounded-2xl shadow-2xl p-6 md:p-8">
                    <h2 class="text-xl font-bold text-gray-900 mb-1">¡Solicitar Información!</h2>
                    <p class="text-sm text-gray-500 mb-5">Déjanos tus datos y nos pondremos en contacto contigo. </p>

                    <x-flash-message type="success" />

                    <form method="POST" action="{{ route('diplomados.solicitud') }}" class="space-y-4"
                        x-data="{ submitting: false }" @submit="submitting = true">
                        @csrf
                        <div class="grid grid-cols-2 gap-4">
                            <x-floating-input name="nombres" label="Nombres" :required="true" autocomplete="given-name" id="lead_nombres" />
                            <x-floating-input name="apellidos" label="Apellidos" :required="true" autocomplete="family-name" id="lead_apellidos" />
                        </div>

                        <x-floating-input name="correo" label="Correo electrónico" type="email" :required="true" autocomplete="email" id="lead_correo" />

                        {{-- País y región salen de listas reales en vez de texto
                             libre, que traía «peru», «Perú » y regiones mal
                             escritas, imposibles de agrupar después. Las sirve
                             el propio sitio (ver App\Services\GeografiaService),
                             no un tercero desde el navegador del visitante. --}}
                        <div class="grid grid-cols-2 gap-4">
                            <div data-campo>
                                <x-floating-input name="pais" label="País" type="select" :required="true"
                                    autocomplete="country" id="lead_pais">
                                    <option value=""></option>
                                </x-floating-input>
                            </div>

                            <div data-campo>
                                <x-floating-input name="region" label="Región" type="select" :required="true"
                                    id="lead_region">
                                    <option value=""></option>
                                </x-floating-input>
                            </div>

                            {{-- Respaldo para los países sin división administrativa
                                 y para cuando las listas no cargan. Lo activa el JS;
                                 deshabilitado no viaja en el envío. --}}
                            <div data-campo class="hidden">
                                <x-floating-input name="region" label="Región" :required="true"
                                    id="lead_region_libre" disabled="disabled" />
                            </div>
                        </div>

                        <x-floating-input name="telefono" label="Teléfono" type="tel" :required="true" autocomplete="tel" id="lead_telefono" />

                        <x-floating-input name="programa_id" label="Diplomado de interés" type="select" :required="true" id="lead_programa_id">
                            <option value=""></option>
                            @foreach($diplomados as $diplomado)
                                <option value="{{ $diplomado->id }}" {{ old('programa_id') == $diplomado->id ? 'selected' : '' }}>
                                    {{ $diplomado->titulo_completo }}
                                </option>
                            @endforeach
                        </x-floating-input>

                        <x-button type="submit" :block="true" size="lg" class="shadow-md disabled:opacity-60 disabled:cursor-not-allowed" x-bind:disabled="submitting">
                            <x-fas-spinner class="animate-spin" x-show="submitting" x-cloak aria-hidden="true" />
                            <span x-text="submitting ? 'Enviando...' : 'Enviar'"></span>
                        </x-button>
                    </form>
                </div>

            </div>
        </div>
    </section>

    {{-- GRID DE DIPLOMADOS (mismo esquema de diseño que /programas) --}}
    <section id="oferta-diplomados" class="container mx-auto px-6 py-16">
        <div class="mb-10 text-center">
            <h2 class="text-3xl font-serif font-bold text-gray-900 mb-2">Nuestros Diplomados</h2>
            <p class="text-gray-500">Conoce la oferta completa de diplomados de la Unidad de Posgrado.</p>
        </div>

        <div data-reveal class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            @forelse($diplomados as $programa)
                <x-program-card :programa="$programa" badge-label="Diplomado" badge-color="bg-amber-700"
                    duracion-unit="módulos" primary-cta-label="Más información" :show-brochure="true" />
            @empty
                <x-empty-state class="col-span-full" icon="fa-graduation-cap" title="Próximamente"
                    description="Muy pronto lanzaremos nuestra nueva oferta de diplomados." />
            @endforelse
        </div>
    </section>

    {{-- La sección «Contáctanos» que iba aquí se retiró (Obs. N.º 7): repetía
         ubicación, correo, WhatsApp, horario y redes, que ya están en el pie de
         página junto con los enlaces rápidos e institucionales. El footer queda
         como único cierre de contacto y navegación. --}}

@endsection
