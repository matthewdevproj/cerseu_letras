@extends('layouts.public')

@section('title', 'Nosotros - Posgrado Letras UNMSM')
@section('meta_description', 'Misión, visión, valores y autoridades de la Unidad de Posgrado de la Facultad de Letras y Ciencias Humanas de la UNMSM.')

@section('content')
    <!-- HERO DE SECCIÓN -->
    <x-hero-section title="Nosotros" label="Unidad de Posgrado"
        subtitle="La misión, la visión y los valores que guían a la Unidad de Posgrado de la Facultad de Letras y Ciencias Humanas."
        :image="asset('images/campus-fachada.jpg')" />

    <section class="container mx-auto px-6 py-16">

        {{-- Misión / Visión / Valores — layout editorial asimétrico (2:1) --}}
        <div class="grid lg:grid-cols-3 gap-8 lg:gap-12 mb-20">

            {{-- Misión + Visión --}}
            <div class="lg:col-span-2 space-y-12" data-reveal>
                <div class="relative pl-6">
                    <span class="absolute left-0 top-1.5 bottom-1.5 w-1.5 rounded-full bg-unmsm-guinda"></span>
                    <p class="text-unmsm-dorado font-bold tracking-widest uppercase text-xs mb-2">Nuestra razón de ser</p>
                    <h2 class="text-3xl font-serif font-bold text-gray-900 mb-4">Misión</h2>
                    <p class="text-gray-600 text-lg leading-relaxed">{{ $mision }}</p>
                </div>

                <div class="relative pl-6">
                    <span class="absolute left-0 top-1.5 bottom-1.5 w-1.5 rounded-full bg-unmsm-dorado"></span>
                    <p class="text-unmsm-dorado font-bold tracking-widest uppercase text-xs mb-2">Hacia dónde vamos</p>
                    <h2 class="text-3xl font-serif font-bold text-gray-900 mb-4">Visión</h2>
                    <p class="text-gray-600 text-lg leading-relaxed">{{ $vision }}</p>
                </div>
            </div>

            {{-- Valores — tarjeta de marca --}}
            <aside data-reveal>
                <div class="bg-unmsm-guinda text-white rounded-2xl p-8 shadow-lg h-full relative overflow-hidden">
                    <div class="absolute -top-10 -right-10 w-40 h-40 rounded-full bg-white/5"></div>
                    <h2 class="text-2xl font-serif font-bold mb-6 flex items-center gap-3 relative z-10">
                        <x-fas-star class="text-unmsm-dorado" aria-hidden="true" /> Valores
                    </h2>
                    <ul class="space-y-4 relative z-10">
                        @foreach($valores as $valor)
                            <li class="flex items-start gap-3">
                                <x-fas-circle-check class="text-unmsm-dorado mt-1 flex-shrink-0" aria-hidden="true" />
                                <span class="text-gray-100 leading-snug">{{ $valor }}</span>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </aside>
        </div>

        {{-- Autoridades --}}
        <div>
            <div class="flex items-center gap-4 mb-8">
                <div class="w-12 h-12 rounded-full bg-unmsm-guinda text-white flex items-center justify-center flex-shrink-0">
                    <x-fas-user-tie class="text-xl" aria-hidden="true" />
                </div>
                <div>
                    <h2 class="text-2xl md:text-3xl font-serif font-bold text-gray-900">Autoridades</h2>
                    <p class="text-gray-500 text-sm">Equipo directivo de la Unidad de Posgrado</p>
                </div>
            </div>

            <div class="grid md:grid-cols-3 gap-6" data-reveal-stagger>
                @foreach($autoridades as $autoridad)
                    <div class="bg-white p-6 rounded-xl border border-gray-100 border-l-4 border-l-unmsm-dorado shadow-sm hover:shadow-lg motion-safe:hover:-translate-y-1.5 transition-all duration-300">
                        <h3 class="font-bold text-unmsm-guinda text-lg mb-1 leading-tight">{{ $autoridad['nombre'] }}</h3>
                        <p class="text-xs font-bold text-gray-500 mb-4 uppercase tracking-wide">{{ $autoridad['cargo'] }}</p>
                        <a href="mailto:{{ $autoridad['email'] }}"
                            class="flex items-center gap-2 text-sm text-gray-600 hover:text-unmsm-guinda transition-colors min-w-0">
                            <x-fas-envelope class="text-unmsm-dorado flex-shrink-0" aria-hidden="true" />
                            <span class="truncate">{{ $autoridad['email'] }}</span>
                        </a>
                    </div>
                @endforeach
            </div>
        </div>

    </section>
@endsection
