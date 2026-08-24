@extends('layouts.public')

@section('title', 'Página no encontrada (404)')
@section('meta_description', 'La página que buscas no existe o fue movida.')

@section('content')
    <section class="relative flex items-center justify-center overflow-hidden bg-unmsm-azul text-white min-h-[80vh]">
        {{-- textura de puntos + resplandor dorado --}}
        <div class="absolute inset-0 opacity-[0.06]"
            style="background-image: radial-gradient(circle at 1px 1px, #fff 1.5px, transparent 0); background-size: 34px 34px;">
        </div>
        <div class="absolute -top-24 -right-24 w-96 h-96 rounded-full bg-unmsm-dorado/20 blur-3xl"></div>
        <div class="absolute -bottom-24 -left-24 w-96 h-96 rounded-full bg-black/20 blur-3xl"></div>

        <div class="container mx-auto px-6 relative z-10 text-center py-24">
            <p class="font-serif font-bold text-unmsm-dorado leading-none text-7xl md:text-9xl mb-2 drop-shadow-lg">404</p>
            <p class="text-unmsm-dorado font-bold tracking-widest uppercase text-xs md:text-sm mb-3">
                Universidad Nacional Mayor de San Marcos
            </p>
            <h1 class="text-3xl md:text-4xl font-serif font-bold mb-4">Página no encontrada</h1>
            <p class="text-white/85 max-w-xl mx-auto leading-relaxed mb-8">
                La página que buscas no existe, cambió de dirección o ya no está disponible.
                Te invitamos a volver al inicio o explorar nuestros cursos.
            </p>
            <div class="flex flex-wrap gap-4 justify-center">
                <a href="{{ route('home') }}"
                    class="inline-flex items-center gap-2 px-7 py-3 rounded-lg bg-unmsm-dorado text-unmsm-azul font-bold hover:bg-white transition-colors shadow-lg motion-safe:hover:-translate-y-0.5 duration-200">
                    <x-fas-house aria-hidden="true" /> Volver al inicio
                </a>
                <a href="{{ route('cursos.index') }}"
                    class="inline-flex items-center gap-2 px-7 py-3 rounded-lg border border-white/70 text-white font-bold hover:bg-white/10 transition-colors">
                    Ver cursos <x-fas-arrow-right aria-hidden="true" />
                </a>
            </div>
        </div>
    </section>
@endsection
