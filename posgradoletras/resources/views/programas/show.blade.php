@extends('layouts.public')

@section('title', $programa->nombre . ' - Posgrado Letras UNMSM')

@section('content')
    <!-- HERO DE SECCIÓN -->
    <x-hero-section :title="$programa->nombre" :label="$programa->grado" :subtitle="'Grado que otorga: ' . ($programa->grado_otorga ?? $programa->grado)" :image="$programa->imagen_url" />

    <!-- CONTENIDO PRINCIPAL -->
    <div class="container mx-auto px-4 py-12">
        <div class="grid lg:grid-cols-3 gap-8">

            <!-- COLUMNA IZQUIERDA: Acordeones (2/3 ancho) -->
            <div class="lg:col-span-2">
                <div class="space-y-4">

                    {{-- Presentación --}}
                    @component('components.accordion', [
                        'id' => 'presentacion',
                        'title' => 'Presentación',
                        'active' => true
                    ])
                    <div class="text-gray-700 leading-relaxed space-y-4">
                        <p class="text-justify">
                            {{ $programa->sumilla ?? $programa->presentacion ?? 'Sin descripción disponible' }}
                        </p>

                        @if ($programa->plan_estudios && is_array($programa->plan_estudios) && isset($programa->plan_estudios['objetivos']))
                            <div class="mt-6 bg-blue-50 p-5 rounded-lg border-l-4 border-blue-600">
                                <h4 class="font-bold text-blue-800 mb-2">Objetivos del Programa</h4>
                                <ul class="list-disc list-inside space-y-2 text-sm text-blue-900">
                                    @foreach ($programa->plan_estudios['objetivos'] as $objetivo)
                                        <li>{{ $objetivo }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                    </div>
                    @endcomponent

                    {{-- Perfil de Ingreso y Egreso --}}
                    @component('components.accordion', [
                        'id' => 'perfil',
                        'title' => 'Perfil de Ingreso y Egreso'
                    ])
                    <div class="grid md:grid-cols-2 gap-8">
                        @if ($programa->plan_estudios && is_array($programa->plan_estudios) && isset($programa->plan_estudios['perfil_ingresante']))
                            <div>
                                <h4 class="font-bold text-gray-900 mb-3 border-b pb-2">Perfil de Ingreso</h4>
                                <ul class="list-disc list-inside space-y-2 text-sm text-gray-600">
                                    @foreach ($programa->plan_estudios['perfil_ingresante'] as $perfil)
                                        <li>{{ $perfil }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <div>
                            <h4 class="font-bold text-gray-900 mb-3 border-b pb-2">Perfil de Egreso</h4>
                            <p class="text-sm text-gray-600 text-justify">
                                {{ $programa->perfil_egresado ?? 'El egresado estará capacitado para diseñar y ejecutar proyectos de investigación originales, ejercer la docencia universitaria especializada y gestionar proyectos académicos con una visión crítica y ética.' }}
                            </p>
                        </div>
                    </div>
                    @endcomponent

                    {{-- Currícula / Plan de Estudios --}}
                    @component('components.accordion', [
                        'id' => 'curricula',
                        'title' => 'Plan de Estudios'
                    ])
                    <div class="text-gray-700">
                        <p class="mb-4">El plan de estudios consta de {{ $programa->duracion }} semestres académicos con un
                            total de {{ $programa->creditos }} créditos.</p>

                        <div class="space-y-3 mb-6">
                            <div class="flex items-center justify-between p-3 bg-gray-50 rounded border border-gray-100">
                                <span class="font-medium text-sm"><i
                                        class="fa-solid fa-circle-check text-unmsm-dorado mr-2"></i>Teoría y Métodos de
                                    Investigación</span>
                                <span
                                    class="text-xs bg-white px-2 py-1 rounded border shadow-sm">{{ $programa->creditos ? round($programa->creditos / 4) : 4 }}
                                    Créditos</span>
                            </div>
                            <div class="flex items-center justify-between p-3 bg-gray-50 rounded border border-gray-100">
                                <span class="font-medium text-sm"><i
                                        class="fa-solid fa-circle-check text-unmsm-dorado mr-2"></i>Seminario de
                                    Especialización</span>
                                <span
                                    class="text-xs bg-white px-2 py-1 rounded border shadow-sm">{{ $programa->creditos ? round($programa->creditos / 4) : 4 }}
                                    Créditos</span>
                            </div>
                            <div class="flex items-center justify-between p-3 bg-gray-50 rounded border border-gray-100">
                                <span class="font-medium text-sm"><i
                                        class="fa-solid fa-circle-check text-unmsm-dorado mr-2"></i>Tesis o Trabajo de
                                    Investigación</span>
                                <span
                                    class="text-xs bg-white px-2 py-1 rounded border shadow-sm">{{ $programa->creditos ? round($programa->creditos / 2) : 8 }}
                                    Créditos</span>
                            </div>
                        </div>

                        <div class="text-center">
                            @if($programa->plan_url)
                                <a href="{{ $programa->plan_url }}" target="_blank"
                                    class="inline-flex items-center gap-2 text-unmsm-guinda font-bold hover:underline">
                                    <i class="fa-solid fa-file-pdf"></i> Descargar Malla Curricular Completa (PDF)
                                </a>
                            @else
                                <p class="text-sm text-gray-600 italic">Contactar a la coordinación para información detallada
                                    del currículo</p>
                            @endif
                        </div>
                    </div>
                    @endcomponent

                    {{-- Plana Docente --}}
                    @component('components.accordion', [
                        'id' => 'plana-docente',
                        'title' => 'Plana Docente'
                    ])
                    @if ($programa->docentes && count($programa->docentes) > 0)
                        <div class="grid sm:grid-cols-2 gap-4">
                            @foreach ($programa->docentes as $profesor)
                                <div
                                    class="border border-gray-100 rounded-lg p-4 flex items-center gap-4 hover:shadow-md transition-shadow">
                                    <div
                                        class="w-12 h-12 rounded-full bg-unmsm-guinda/10 flex items-center justify-center text-unmsm-guinda font-bold text-lg">
                                        {{ substr($profesor->nombre, 0, 1) }}
                                    </div>
                                    <div>
                                        <h5 class="font-bold text-gray-800 text-sm">{{ $profesor->nombre }}</h5>
                                        @if (isset($profesor->email) && !empty($profesor->email))
                                            <a href="mailto:{{ $profesor->email }}" class="text-xs text-unmsm-dorado hover:underline">
                                                {{ $profesor->email }}
                                            </a>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-gray-600">Información de plana docente próximamente.</p>
                    @endif
                    @endcomponent

                    {{-- Horarios --}}
                    @component('components.accordion', [
                        'id' => 'horarios',
                        'title' => 'Horarios'
                    ])
                    <div class="text-gray-700">
                        @if ($programa->plan_url)
                            <div class="flex items-center gap-3 p-4 bg-gray-50 rounded-lg">
                                <svg class="w-8 h-8 text-red-600" fill="currentColor" viewBox="0 0 20 20">
                                    <path
                                        d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4z" />
                                </svg>
                                <div class="flex-1">
                                    <h5 class="font-semibold text-gray-900">Horarios del Programa</h5>
                                    <p class="text-sm text-gray-600">Descarga el documento con los horarios completos</p>
                                </div>
                                <a href="{{ $programa->plan_url }}" target="_blank"
                                    class="px-4 py-2 bg-unmsm-guinda text-white rounded hover:bg-unmsm-guinda/90 transition-colors">
                                    Descargar PDF
                                </a>
                            </div>
                        @else
                            <p class="text-gray-700">Consultar horarios con la coordinación del programa.</p>
                            <p class="text-sm text-gray-600 mt-2"><em>(Sujeto a cambios)</em></p>
                        @endif
                    </div>
                    @endcomponent

                    {{-- Inversión Económica --}}
                    @component('components.accordion', [
                        'id' => 'inversion',
                        'title' => 'Inversión Económica'
                    ])
                    <div class="space-y-4">
                        @if($programa->tipo === 'maestria')
                            @include('programas.inversion.maestria', ['programa' => $programa])
                        @elseif($programa->tipo === 'doctorado')
                            @include('programas.inversion.doctorado', ['programa' => $programa])
                        @else
                            <p class="text-gray-600">
                                Para información detallada sobre costos y créditos, revise la página oficial de la Facultad
                                o contacte a la coordinación del programa.
                            </p>
                        @endif
                    </div>
                    @endcomponent

                    {{-- Requisitos de Admisión --}}
                    @component('components.accordion', [
                        'id' => 'requisitos',
                        'title' => 'Requisitos de Admisión'
                    ])
                    <div class="text-gray-700">
                        <p class="mb-4">
                            Para conocer los requisitos completos del proceso de admisión, visita nuestra página de:
                        </p>
                        <a href="{{ route('admision') }}"
                            class="inline-flex items-center gap-2 px-6 py-3 bg-unmsm-guinda text-white rounded-lg hover:bg-red-900 transition font-semibold text-sm">
                            <i class="fa-solid fa-pen-to-square"></i> Ver Proceso de Admisión
                        </a>
                    </div>
                    @endcomponent

                </div>
            </div>

            <!-- COLUMNA DERECHA: Sidebar Sticky con Información Clave -->
            <div class="lg:col-span-1">
                <div class="sticky top-24 space-y-6">

                    <!-- Tarjeta Información Clave -->
                    <div class="bg-white rounded-xl shadow-lg border-t-4 border-unmsm-guinda p-6">
                        <h3 class="text-lg font-bold text-gray-800 mb-6 flex items-center gap-2">
                            <i class="fa-solid fa-circle-info text-unmsm-guinda"></i> Información Clave
                        </h3>

                        <!-- Ítem Créditos -->
                        <div class="flex items-center gap-4 mb-5">
                            <div
                                class="w-10 h-10 rounded-full bg-gray-100 flex items-center justify-center text-unmsm-guinda">
                                <i class="fa-solid fa-star"></i>
                            </div>
                            <div>
                                <p class="text-xs text-gray-500 uppercase font-bold">Créditos</p>
                                <p class="font-bold text-gray-900">{{ $programa->creditos }} Académicos</p>
                            </div>
                        </div>

                        <!-- Ítem Duración -->
                        <div class="flex items-center gap-4 mb-5">
                            <div
                                class="w-10 h-10 rounded-full bg-gray-100 flex items-center justify-center text-unmsm-guinda">
                                <i class="fa-regular fa-clock"></i>
                            </div>
                            <div>
                                <p class="text-xs text-gray-500 uppercase font-bold">Duración</p>
                                <p class="font-bold text-gray-900">{{ $programa->duracion_formateada }}
                                    ({{ $programa->duracion / 2 }} años)</p>
                            </div>
                        </div>

                        <!-- Ítem Modalidad -->
                        <div class="flex items-center gap-4 mb-6">
                            <div
                                class="w-10 h-10 rounded-full bg-gray-100 flex items-center justify-center text-unmsm-guinda">
                                <i class="fa-solid fa-laptop-file"></i>
                            </div>
                            <div>
                                <p class="text-xs text-gray-500 uppercase font-bold">Modalidad</p>
                                <p class="font-bold text-gray-900">{{ $programa->modalidad ?? 'Presencial' }}</p>
                            </div>
                        </div>

                        <!-- Botón Principal -->
                        <a href="{{ route('admision') }}"
                            class="block w-full py-3 bg-unmsm-guinda text-white text-center rounded-lg font-bold hover:bg-red-900 transition shadow-md mb-3 transform hover:scale-105 duration-200">
                            <i class="fa-solid fa-pen-to-square mr-2"></i> Postular Ahora
                        </a>

                        <a href="https://wa.me/message/ZF2GT3IJI5IJG1" target="_blank"
                            class="block w-full py-3 bg-white border border-gray-300 text-gray-700 text-center rounded-lg font-bold hover:bg-gray-50 transition text-sm">
                            <i class="fa-brands fa-whatsapp mr-2 text-green-600"></i> Consultar por WhatsApp
                        </a>
                    </div>

                    <!-- Tarjeta Contacto Rápido -->
                    <div class="bg-gray-900 rounded-xl p-6 text-white shadow-lg relative overflow-hidden">
                        <div
                            class="absolute top-0 right-0 w-32 h-32 bg-unmsm-guinda opacity-20 rounded-full blur-2xl -mr-10 -mt-10">
                        </div>
                        <h4 class="font-bold mb-4 relative z-10">¿Necesitas ayuda?</h4>
                        <p class="text-sm text-gray-400 mb-4 relative z-10">Contáctanos directamente:</p>

                        <a href="mailto:posgrado-letras@unmsm.site"
                            class="flex items-center gap-3 text-sm hover:text-unmsm-dorado transition mb-3 relative z-10">
                            <i class="fa-solid fa-envelope text-unmsm-dorado"></i>
                            posgrado-letras@unmsm.site
                        </a>
                        <a href="tel:+51982085037"
                            class="flex items-center gap-3 text-sm hover:text-unmsm-dorado transition relative z-10">
                            <i class="fa-solid fa-phone text-unmsm-dorado"></i>
                            982 085 037
                        </a>
                    </div>

                </div>
            </div>

        </div>
    </div>
@endsection