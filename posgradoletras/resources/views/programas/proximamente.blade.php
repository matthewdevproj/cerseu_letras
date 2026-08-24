@extends('layouts.public')

@section('title', $programa->titulo_completo . ' (Próximamente) - CERSEU Letras UNMSM')
@section('meta_description', 'El curso ' . $programa->titulo_completo . ' se anunciará próximamente. Déjanos tus datos o consulta con el CERSEU para recibir información.')

@push('meta')
    {{-- Aún no hay contenido real: se pide a los buscadores que no lo indexen
         para no posicionar una página vacía. --}}
    <meta name="robots" content="noindex, follow">
@endpush

@section('content')
    <x-hero-section
        :title="$programa->titulo_completo"
        :subtitle="$programa->grado . ' · Próximamente'" />

    <section class="py-16 md:py-20 bg-gray-50">
        <div class="container mx-auto px-6 max-w-3xl">

            <div class="bg-white rounded-2xl shadow-lg border-t-4 border-unmsm-dorado p-8 md:p-12 text-center">

                <span class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-unmsm-dorado/15 text-unmsm-azul text-xs font-bold uppercase tracking-wider mb-6">
                    <x-fas-hourglass-half aria-hidden="true" />
                    Próximamente
                </span>

                <h1 class="font-serif text-2xl md:text-3xl font-bold text-gray-900 mb-4 leading-snug">
                    Pronto publicaremos la información de este curso
                </h1>

                <p class="text-gray-600 leading-relaxed mb-8 max-w-xl mx-auto">
                    El <strong class="text-gray-900">{{ $programa->titulo_completo }}</strong> forma parte de
                    la próxima oferta académica del CERSEU. Estamos preparando el plan de
                    estudios, la plana docente, la inversión y las fechas de admisión.
                </p>

                {{-- Solo se muestran los datos que ya estén cargados: nada de
                     casillas vacías ni valores en cero. --}}
                @php
                    $datos = collect([
                        ['label' => 'Modalidad', 'valor' => $programa->modalidad, 'icono' => 'fas-laptop-file'],
                        ['label' => 'Duración', 'valor' => $programa->duracion ? $programa->duracion_formateada : null, 'icono' => 'far-clock'],
                        ['label' => 'Créditos', 'valor' => $programa->creditos ? $programa->creditos . ' académicos' : null, 'icono' => 'fas-star'],
                    ])->filter(fn ($d) => filled($d['valor']));
                @endphp

                @if ($datos->isNotEmpty())
                    {{-- Clases literales: Tailwind no genera las que se arman
                         concatenando en tiempo de ejecución. --}}
                    @php
                        $columnas = [1 => 'sm:grid-cols-1', 2 => 'sm:grid-cols-2', 3 => 'sm:grid-cols-3'][$datos->count()] ?? 'sm:grid-cols-3';
                    @endphp
                    <dl class="grid {{ $columnas }} gap-4 mb-8 text-left">
                        @foreach ($datos as $dato)
                            <div class="rounded-xl border border-gray-100 bg-gray-50 p-4">
                                <dt class="text-[11px] uppercase tracking-wider text-gray-500 font-bold mb-1">
                                    {{ $dato['label'] }}
                                </dt>
                                <dd class="font-bold text-gray-900">{{ $dato['valor'] }}</dd>
                            </div>
                        @endforeach
                    </dl>
                @endif

                <div class="flex flex-col sm:flex-row gap-3 justify-center">
                    <a href="{{ \App\Models\SiteSetting::contacto('whatsapp') }}"
                        target="_blank" rel="noopener noreferrer"
                        class="inline-flex items-center justify-center gap-2 px-6 py-3 rounded-lg bg-unmsm-azul text-white font-bold text-sm hover:bg-unmsm-azul-dark transition-colors focus-visible:outline focus-visible:outline-2 focus-visible:outline-unmsm-azul focus-visible:outline-offset-2">
                        <x-fab-whatsapp aria-hidden="true" />
                        Consultar por WhatsApp
                    </a>
                    <a href="{{ route(($programa->tipoOferta() ?? \App\Models\TipoOferta::Curso)->slug() . '.index') }}"
                        class="inline-flex items-center justify-center gap-2 px-6 py-3 rounded-lg border border-gray-300 text-gray-700 font-bold text-sm hover:bg-gray-50 transition-colors">
                        Ver la oferta vigente
                        <x-fas-arrow-right class="text-xs" aria-hidden="true" />
                    </a>
                </div>
            </div>

            <p class="text-center text-sm text-gray-500 mt-6">
                ¿Necesitas información antes?
                <a href="mailto:{{ \App\Models\SiteSetting::contacto('admision') }}"
                    class="text-unmsm-azul font-semibold underline underline-offset-2">
                    Escríbenos al correo de admisión
                </a>.
            </p>
        </div>
    </section>
@endsection
