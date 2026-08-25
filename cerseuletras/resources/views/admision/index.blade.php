@extends('layouts.public')

@section('title', 'Admisión - CERSEU Letras UNMSM')

@php
    $emailAdmision = config('contacts.admision');
    $telefono = config('contacts.telefono');
    $whatsapp = config('contacts.whatsapp');
@endphp

@section('content')
    {{-- Sin año en el título: el proceso de cada tipo tiene su propio
         cronograma y sus propias convocatorias, que cambian por separado. --}}
    <x-hero-section title="Admisión" label="Cómo inscribirse"
        subtitle="Formación abierta a toda la comunidad, dentro y fuera de la Universidad"
        image="https://letras.unmsm.edu.pe/wp-content/uploads/2025/12/IMG_1565-scaled.jpg" />

    <section class="container mx-auto px-6 py-16 fade-in">
        <div class="grid lg:grid-cols-3 gap-8 items-start">

            <div class="lg:col-span-2 space-y-8">
                <div>
                    <h2 class="font-serif text-2xl font-bold text-unmsm-azul tracking-tight">
                        {{ $intro?->titulo ?? 'Elige el tipo de formación' }}
                    </h2>

                    @if ($intro?->cuerpo_renderizado)
                        <div class="mt-3 text-gray-600 leading-relaxed">
                            {!! $intro->cuerpo_renderizado !!}
                        </div>
                    @else
                        <p class="mt-3 text-gray-600 leading-relaxed">
                            Cada tipo de oferta tiene su propio proceso, con requisitos,
                            cronograma e inversión distintos. Entra en el que te interese
                            para ver las convocatorias abiertas y dejar tu solicitud.
                        </p>
                    @endif
                </div>

                {{-- Una tarjeta por caso de TipoOferta: al añadir un cuarto tipo
                     aparece aquí sin tocar esta plantilla. --}}
                <div class="grid sm:grid-cols-2 gap-5">
                    @foreach ($tipos as $tipo)
                        <a href="{{ route($tipo->slug() . '.admision') }}"
                            class="group block bg-white/70 backdrop-blur-sm rounded-xl p-6 shadow-sm ring-1 ring-gray-900/[0.06] border-l-[3px] border-unmsm-azul/60 transition-all duration-300 hover:bg-white/90 hover:shadow-lg hover:border-unmsm-azul">
                            <h3 class="font-serif text-lg font-bold text-unmsm-azul tracking-tight">
                                {{ $tipo->plural() }}
                            </h3>
                            <p class="mt-2 text-sm text-gray-600">
                                Se miden en {{ implode(' y ', $tipo->medidas()) }}.
                            </p>
                            <span
                                class="mt-4 inline-flex items-center gap-2 text-sm font-semibold text-unmsm-azul group-hover:gap-3 transition-all">
                                Ver el proceso
                                <x-fas-arrow-right class="text-xs" aria-hidden="true" />
                            </span>
                        </a>
                    @endforeach
                </div>

                <div class="rounded-xl bg-unmsm-azul/5 border border-unmsm-azul/20 p-5 text-sm text-gray-700">
                    <p class="mb-2">¿No sabes cuál te corresponde? Mira primero la oferta:</p>
                    <div class="flex flex-wrap gap-x-4 gap-y-1">
                        @foreach ($tipos as $tipo)
                            <a href="{{ route($tipo->slug() . '.index') }}"
                                class="font-semibold text-unmsm-azul hover:underline">{{ $tipo->plural() }}</a>
                        @endforeach
                    </div>
                </div>
            </div>

            <div class="lg:col-span-1">
                <div class="bg-unmsm-azul text-white rounded-2xl p-6 shadow-xl sticky top-24">
                    <h3 class="font-bold mb-4 font-serif text-xl">¿Necesitas ayuda?</h3>
                    <p class="text-sm mb-4 text-white/80">
                        Escríbenos y resolvemos tus dudas sobre cualquiera de los procesos.
                    </p>
                    <div class="space-y-3 text-sm">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-white/20 rounded-full flex items-center justify-center">
                                <x-fas-envelope class="text-base" aria-hidden="true" />
                            </div>
                            <div>
                                <span class="text-white/60 text-xs">Email</span>
                                <p class="font-medium break-all">{{ $emailAdmision }}</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-white/20 rounded-full flex items-center justify-center">
                                <x-fas-phone class="text-base" aria-hidden="true" />
                            </div>
                            <div>
                                <span class="text-white/60 text-xs">WhatsApp</span>
                                <a href="{{ $whatsapp }}" target="_blank" rel="noopener noreferrer"
                                    class="font-medium hover:text-white/80 transition-colors flex items-center gap-1">
                                    {{ $telefono }} <x-fas-external-link-alt class="text-xs" />
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </section>
@endsection
