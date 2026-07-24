@extends('layouts.public')

@section('title', 'Diplomados - Posgrado Letras UNMSM')

@section('content')

    {{-- HERO DE DIPLOMADOS (con formulario de solicitud de información integrado) --}}
    <section class="relative w-full overflow-hidden">
        <div class="absolute inset-0">
            <img src="{{ $settings?->diplomados_hero_imagen ? asset('storage/' . $settings->diplomados_hero_imagen) : asset('images/campus-fachada.jpg') }}"
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
                    <p class="text-gray-200 max-w-xl font-light text-lg leading-relaxed mb-4">
                        {{ $settings?->diplomados_hero_texto ?? 'Especializa tus conocimientos con programas diseñados para responder a los desafíos contemporáneos desde las humanidades, las ciencias sociales y las nuevas tecnologías.' }}
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
                    <p class="text-sm text-gray-500 mb-5">Cuéntanos tus datos y te contactaremos.</p>

                    <x-flash-message type="success" />

                    <form method="POST" action="{{ route('diplomados.solicitud') }}" class="space-y-4"
                        x-data="{ submitting: false }" @submit="submitting = true">
                        @csrf
                        <div class="grid grid-cols-2 gap-4">
                            <x-floating-input name="nombres" label="Nombres" :required="true" autocomplete="given-name" id="lead_nombres" />
                            <x-floating-input name="apellidos" label="Apellidos" :required="true" autocomplete="family-name" id="lead_apellidos" />
                        </div>

                        <x-floating-input name="correo" label="Correo electrónico" type="email" :required="true" autocomplete="email" id="lead_correo" />

                        <div class="grid grid-cols-2 gap-4">
                            <x-floating-input name="pais" label="País" value="Perú" :required="true" autocomplete="country-name" id="lead_pais" />
                            <x-floating-input name="region" label="Región" :required="true" id="lead_region" />
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

    {{-- CONTÁCTANOS --}}
    @php
        $whatsappLink = config('contacts.whatsapp', 'https://wa.me/51982085037');
    @endphp
    <section class="bg-gray-900 border-t-4 border-unmsm-dorado">
        <div class="container mx-auto px-6 py-12">
            <h2 class="text-2xl font-bold text-white mb-2">| Contáctanos</h2>
            <p class="text-gray-400 mb-8">Estamos a tu disposición para resolver cualquier duda sobre nuestros programas y procesos.</p>

            <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-6 text-gray-200">
                <div class="flex items-start gap-3">
                    <x-fas-envelope class="text-unmsm-dorado mt-1" />
                    <div>
                        <p class="text-xs uppercase text-gray-500 font-bold">Email</p>
                        <a href="mailto:{{ $settings?->email_admision ?? config('contacts.admision') }}" class="hover:text-unmsm-dorado transition-colors">
                            {{ $settings?->email_admision ?? config('contacts.admision') }}
                        </a>
                    </div>
                </div>
                <div class="flex items-start gap-3">
                    <x-fab-whatsapp class="text-unmsm-dorado mt-1" />
                    <div>
                        <p class="text-xs uppercase text-gray-500 font-bold">WhatsApp</p>
                        <a href="{{ $whatsappLink }}" target="_blank" rel="noopener noreferrer" class="hover:text-unmsm-dorado transition-colors">
                            {{ $settings?->telefono ?? config('contacts.telefono') }}
                        </a>
                    </div>
                </div>
                <div class="flex items-start gap-3">
                    <x-fas-location-dot class="text-unmsm-dorado mt-1" />
                    <div>
                        <p class="text-xs uppercase text-gray-500 font-bold">Ubicación</p>
                        <p>{{ $settings?->direccion ?? 'Ciudad Universitaria, Av. Venezuela s/n, Lima' }}</p>
                    </div>
                </div>
                <div class="flex items-start gap-3">
                    <x-far-clock class="text-unmsm-dorado mt-1" />
                    <div>
                        <p class="text-xs uppercase text-gray-500 font-bold">Horario de atención</p>
                        <p>{{ $settings?->horario_atencion ?? 'Lunes a Viernes de 8:00 am a 16:00 pm' }}</p>
                    </div>
                </div>
            </div>

            <div class="flex gap-4 mt-8">
                @if($settings?->facebook)
                    <a href="{{ $settings->facebook }}" target="_blank" rel="noopener noreferrer" class="text-gray-400 hover:text-white"><x-fab-facebook-f /></a>
                @endif
                @if($settings?->instagram)
                    <a href="{{ $settings->instagram }}" target="_blank" rel="noopener noreferrer" class="text-gray-400 hover:text-white"><x-fab-instagram /></a>
                @endif
                @if($settings?->twitter)
                    <a href="{{ $settings->twitter }}" target="_blank" rel="noopener noreferrer" class="text-gray-400 hover:text-white"><x-fab-twitter /></a>
                @endif
                @if($settings?->linkedin)
                    <a href="{{ $settings->linkedin }}" target="_blank" rel="noopener noreferrer" class="text-gray-400 hover:text-white"><x-fab-linkedin-in /></a>
                @endif
                @if($settings?->tiktok)
                    <a href="{{ $settings->tiktok }}" target="_blank" rel="noopener noreferrer" class="text-gray-400 hover:text-white"><x-fab-tiktok /></a>
                @endif
            </div>
        </div>
    </section>

@endsection
