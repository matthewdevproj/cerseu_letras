@extends('layouts.public')

@section('title', $programa->titulo_completo . ' - Posgrado Letras UNMSM')

@section('content')
    <!-- HERO DE SECCIÓN -->
    <x-hero-section :title="$programa->titulo_completo" :label="$programa->grado" :subtitle="'Grado que otorga: ' . ($programa->grado_otorga ?? $programa->grado)" :image="$programa->imagen_url" />

    <!-- CONTENIDO PRINCIPAL -->
    <div class="container mx-auto px-4 py-12">
        <div class="grid lg:grid-cols-3 gap-8">

            <!-- COLUMNA IZQUIERDA: Acordeones (2/3 ancho) -->
            <div class="lg:col-span-2">
                <div class="space-y-4">

                    {{-- Coordinador del Programa --}}
                    @php
                        $coordinador = $programa->docentes->firstWhere('pivot.es_coordinador', 1);
                    @endphp
                    @if($coordinador)
                        <div class="bg-gradient-to-r from-unmsm-guinda/5 to-unmsm-dorado/5 border border-unmsm-guinda/20 rounded-xl p-4 md:p-5">
                            <div class="flex items-start gap-3">
                                <div class="w-10 h-10 rounded-full bg-unmsm-guinda flex items-center justify-center text-white flex-shrink-0">
                                    <i class="fas fa-user-tie"></i>
                                </div>
                                <div class="flex-1">
                                    <h4 class="text-sm font-bold text-unmsm-guinda uppercase tracking-wide mb-1">Coordinador del Programa</h4>
                                    <a href="{{ route('profesores.show', $coordinador->slug) }}" 
                                       class="text-base font-semibold text-gray-900 hover:text-unmsm-guinda hover:underline transition-colors inline-flex items-center gap-2">
                                        {{ $coordinador->nombre }}
                                        <i class="fas fa-arrow-right text-xs"></i>
                                    </a>
                                    @if($coordinador->email)
                                        <p class="inline-flex items-center gap-1 text-sm text-unmsm-dorado mt-1">
                                            <i class="fas fa-envelope text-xs"></i>
                                            <span class="select-text">{{ $coordinador->email }}</span>
                                        </p>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endif

                    {{-- Presentación --}}
                    @component('components.accordion', [
                        'id' => 'presentacion',
                        'title' => 'Presentación',
                        'active' => true
                    ])
                    <div class="text-gray-700 leading-relaxed">
                        <p class="text-justify">
                            {{ $programa->sumilla ?? 'Sin descripción disponible' }}
                        </p>
                    </div>
                    @endcomponent

                    {{-- Objetivos Académicos --}}
                    @component('components.accordion', [
                        'id' => 'objetivos',
                        'title' => 'Objetivos Académicos'
                    ])
                    @if ($programa->objetivos_academicos && is_array($programa->objetivos_academicos) && count($programa->objetivos_academicos) > 0)
                        <div class="space-y-3">
                            <ul class="list-disc list-inside space-y-3 text-sm text-gray-700">
                                @foreach ($programa->objetivos_academicos as $objetivo)
                                    <li class="text-justify leading-relaxed">{{ $objetivo }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @else
                        <p class="text-sm text-gray-600 text-justify">
                            Los objetivos académicos del programa se encuentran en proceso de actualización.
                        </p>
                    @endif
                    @endcomponent

                    {{-- Perfil de Ingreso y Egreso --}}
                    @component('components.accordion', [
                        'id' => 'perfil',
                        'title' => 'Perfil de Ingreso y Graduado'
                    ])
                    <div class="space-y-8">
                        @if ($programa->perfil_ingresante && is_array($programa->perfil_ingresante) && count($programa->perfil_ingresante) > 0)
                            <div>
                                <h4 class="font-bold text-gray-900 mb-3 border-b pb-2">Perfil de Ingreso</h4>
                                <ul class="list-disc list-inside space-y-2 text-sm text-gray-600">
                                    @foreach ($programa->perfil_ingresante as $perfil)
                                        <li class="text-justify">{{ $perfil }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @else
                            <div>
                                <h4 class="font-bold text-gray-900 mb-3 border-b pb-2">Perfil de Ingreso</h4>
                                <p class="text-sm text-gray-600 text-justify">
                                    Profesionales con grado de bachiller interesados en profundizar conocimientos en el área de especialización del programa.
                                </p>
                            </div>
                        @endif

                        <div>
                            <h4 class="font-bold text-gray-900 mb-3 border-b pb-2">Perfil de Graduado</h4>
                            @php
                                $tienePerfilGraduado = false;
                                if ($programa->perfil_graduado) {
                                    if (is_array($programa->perfil_graduado) && count($programa->perfil_graduado) > 0) {
                                        $tienePerfilGraduado = true;
                                    } elseif (is_string($programa->perfil_graduado) && trim($programa->perfil_graduado) !== '') {
                                        $tienePerfilGraduado = true;
                                    }
                                }
                            @endphp

                            @if($tienePerfilGraduado)
                                @if(is_array($programa->perfil_graduado))
                                    <ul class="list-disc list-inside space-y-2 text-sm text-gray-600">
                                        @foreach ($programa->perfil_graduado as $perfil)
                                            <li class="text-justify">{{ $perfil }}</li>
                                        @endforeach
                                    </ul>
                                @else
                                    <p class="text-sm text-gray-600 text-justify">
                                        {{ $programa->perfil_graduado }}
                                    </p>
                                @endif
                            @else
                                <p class="text-sm text-gray-600 text-justify">
                                    El egresado estará capacitado para diseñar y ejecutar proyectos de investigación originales, ejercer la docencia universitaria especializada y gestionar proyectos académicos con una visión crítica y ética.
                                </p>
                            @endif
                        </div>
                    </div>
                    @endcomponent

                    {{-- Currícula / Plan de Estudios --}}
                    @component('components.accordion', [
                        'id' => 'curricula',
                        'title' => 'Plan de Estudios'
                    ])
                    <div class="text-gray-700">
                        <p class="mb-6">El plan de estudios consta de {{ $programa->duracion }} semestres académicos con un
                            total de {{ $programa->creditos }} créditos.</p>

                        @if ($programa->plan_estudios && is_array($programa->plan_estudios) && count($programa->plan_estudios) > 0)
                            @php
                                // Agrupar cursos por ciclo
                                $cursosPorCiclo = [];
                                foreach ($programa->plan_estudios as $curso) {
                                    $ciclo = $curso['ciclo'] ?? 'Sin ciclo';
                                    if (!isset($cursosPorCiclo[$ciclo])) {
                                        $cursosPorCiclo[$ciclo] = [];
                                    }
                                    $cursosPorCiclo[$ciclo][] = $curso;
                                }
                                ksort($cursosPorCiclo);
                            @endphp

                            {{-- Tabla de Plan de Estudios --}}
                            <div class="overflow-x-auto mb-8">
                                <table class="w-full border-collapse border border-gray-300">
                                    <thead>
                                        <tr class="bg-unmsm-guinda text-white">
                                            <th class="border border-gray-300 px-4 py-3 text-center w-24">
                                                <span class="font-bold text-sm">Semestre</span>
                                            </th>
                                            <th class="border border-gray-300 px-4 py-3 text-center">
                                                <span class="font-bold text-sm">Asignaturas</span>
                                            </th>
                                            <th class="border border-gray-300 px-4 py-3 text-center w-28">
                                                <span class="font-bold text-sm">Créditos</span>
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($cursosPorCiclo as $ciclo => $cursos)
                                            @foreach ($cursos as $index => $curso)
                                                <tr class="hover:bg-gray-50">
                                                    @if ($index === 0)
                                                        <td class="border border-gray-300 px-4 py-3 text-center bg-gray-50" rowspan="{{ count($cursos) }}">
                                                            <span class="text-unmsm-guinda font-bold" style="font-size: 2rem;">{{ str_pad($ciclo, 2, '0', STR_PAD_LEFT) }}</span>
                                                        </td>
                                                    @endif
                                                    <td class="border border-gray-300 px-4 py-3">
                                                        <span class="text-sm">{{ $curso['nombre'] ?? 'Sin nombre' }}</span>
                                                    </td>
                                                    <td class="border border-gray-300 px-4 py-3 text-center">
                                                        <span class="text-sm font-semibold">{{ $curso['creditos'] ?? 0 }}</span>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>

                            {{-- Sumillas por Semestre --}}
                            <div class="mt-10">
                                <h4 class="text-xl font-bold text-unmsm-guinda mb-6 border-b-2 border-unmsm-guinda pb-2">SUMILLAS</h4>
                                
                                @foreach ($cursosPorCiclo as $ciclo => $cursos)
                                    <div class="mb-8">
                                        <h5 class="text-lg font-bold text-gray-900 mb-4 uppercase">
                                            @php
                                                $numeroSemestre = [
                                                    '1' => 'PRIMER',
                                                    '2' => 'SEGUNDO',
                                                    '3' => 'TERCER',
                                                    '4' => 'CUARTO',
                                                    '5' => 'QUINTO',
                                                    '6' => 'SEXTO'
                                                ];
                                            @endphp
                                            {{ $numeroSemestre[$ciclo] ?? $ciclo }} SEMESTRE
                                        </h5>
                                        
                                        <div class="space-y-5">
                                            @foreach ($cursos as $curso)
                                                <div>
                                                    <h6 class="font-bold text-gray-900 mb-2">{{ $curso['nombre'] ?? 'Sin nombre' }}</h6>
                                                    @if (isset($curso['sumilla']) && !empty($curso['sumilla']))
                                                        <p class="text-sm text-gray-700 text-justify leading-relaxed">{{ $curso['sumilla'] }}</p>
                                                    @else
                                                        <p class="text-sm text-gray-500 italic">Sumilla no disponible</p>
                                                    @endif
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif

                        <div class="text-center mt-6">
                            @if($programa->plan_url)
                                <a href="{{ $programa->plan_url }}" target="_blank"
                                    class="inline-flex items-center gap-2 text-unmsm-guinda font-bold hover:underline">
                                    <i class="fa-solid fa-file-pdf"></i> Descargar Malla Curricular Completa (PDF)
                                </a>
                            @else

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
                        <div class="grid gap-4">
                            @foreach ($programa->docentes as $profesor)
                                <div class="group border border-gray-200 rounded-lg p-4 hover:shadow-lg hover:border-unmsm-guinda/30 transition-all duration-300 cursor-pointer relative">
                                    <a href="{{ route('profesores.show', $profesor->slug) }}" class="absolute inset-0 z-0" aria-label="Ver perfil de {{ $profesor->nombre }}"></a>
                                    
                                    <div class="flex items-start gap-4">
                                        <div class="w-12 h-12 rounded-full bg-unmsm-guinda/10 group-hover:bg-unmsm-guinda group-hover:text-white flex items-center justify-center text-unmsm-guinda font-bold text-lg flex-shrink-0 transition-colors duration-300">
                                            {{ substr($profesor->nombre, 0, 1) }}
                                        </div>
                                        <div class="flex-1">
                                            <div class="flex items-center justify-between gap-3 mb-2">
                                                <div class="flex items-center gap-3 flex-1">
                                                    <h5 class="font-bold text-gray-800 group-hover:text-unmsm-guinda text-base transition-colors duration-300">
                                                        {{ $profesor->nombre }}
                                                    </h5>
                                                    @if($profesor->pivot->es_coordinador)
                                                        <span class="inline-flex items-center gap-1 px-2 py-1 bg-unmsm-guinda text-white text-xs font-semibold rounded flex-shrink-0">
                                                            <i class="fas fa-star"></i>
                                                            Coordinador
                                                        </span>
                                                    @endif
                                                </div>
                                                <a href="{{ route('profesores.show', $profesor->slug) }}" 
                                                   class="inline-flex items-center gap-2 px-3 py-1.5 bg-unmsm-guinda/5 text-unmsm-guinda hover:bg-unmsm-guinda hover:text-white text-xs font-semibold rounded-lg transition-all duration-300 relative z-10 group/btn flex-shrink-0">
                                                    <span class="whitespace-nowrap">Ver más información</span>
                                                    <i class="fas fa-arrow-right text-xs group-hover/btn:translate-x-1 transition-transform"></i>
                                                </a>
                                            </div>
                                            
                                            @if (isset($profesor->email) && !empty($profesor->email))
                                                <a href="mailto:{{ $profesor->email }}" 
                                                   class="inline-flex items-center gap-1 text-sm text-unmsm-dorado hover:underline mb-2 relative z-10"
                                                   onclick="event.stopPropagation();">
                                                    <i class="fas fa-envelope text-xs"></i>
                                                    {{ $profesor->email }}
                                                </a>
                                            @endif

                                            <div class="flex flex-wrap gap-3 mt-3">
                                                @if (isset($profesor->orcid) && !empty($profesor->orcid))
                                                    <a href="https://orcid.org/{{ $profesor->orcid }}" target="_blank" rel="noopener noreferrer"
                                                       class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-green-50 text-green-700 text-xs font-medium rounded-lg hover:bg-green-100 transition-colors relative z-10"
                                                       onclick="event.stopPropagation();">
                                                        <i class="fab fa-orcid"></i>
                                                        <span>ORCID</span>
                                                    </a>
                                                @endif
                                                
                                                @if (isset($profesor->cti_vitae) && !empty($profesor->cti_vitae))
                                                    <a href="{{ $profesor->cti_vitae }}" target="_blank" rel="noopener noreferrer"
                                                       class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-blue-50 text-blue-700 text-xs font-medium rounded-lg hover:bg-blue-100 transition-colors relative z-10"
                                                       onclick="event.stopPropagation();">
                                                        <i class="fas fa-user-graduate"></i>
                                                        <span>CTI Vitae</span>
                                                    </a>
                                                @endif
                                                
                                                @if (isset($profesor->linkedin) && !empty($profesor->linkedin))
                                                    <a href="{{ $profesor->linkedin }}" target="_blank" rel="noopener noreferrer"
                                                       class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-indigo-50 text-indigo-700 text-xs font-medium rounded-lg hover:bg-indigo-100 transition-colors relative z-10"
                                                       onclick="event.stopPropagation();">
                                                        <i class="fab fa-linkedin"></i>
                                                        <span>LinkedIn</span>
                                                    </a>
                                                @endif
                                            </div>
                                        </div>
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
                        @if ($programa->horario_url)
                            <div class="mb-6">
                                <iframe src="{{ $programa->horario_url }}" 
                                        class="w-full border border-gray-300 rounded-lg" 
                                        style="height: 600px;">
                                </iframe>
                            </div>
                            <div class="text-center">
                                <a href="{{ $programa->horario_url }}" target="_blank"
                                    class="inline-flex items-center gap-2 px-6 py-3 bg-unmsm-guinda text-white rounded-lg hover:bg-red-900 transition font-semibold text-sm">
                                    <i class="fa-solid fa-file-pdf"></i> Descargar Horarios (PDF)
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
                        @if(strtolower($programa->grado) === 'maestría' || strtolower($programa->grado) === 'maestria')
                            @include('programas.inversion.maestria', ['programa' => $programa])
                        @elseif(strtolower($programa->grado) === 'doctorado')
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
                        <div class="flex items-center gap-4 mb-5">
                            <div
                                class="w-10 h-10 rounded-full bg-gray-100 flex items-center justify-center text-unmsm-guinda">
                                <i class="fa-solid fa-laptop-file"></i>
                            </div>
                            <div>
                                <p class="text-xs text-gray-500 uppercase font-bold">Modalidad</p>
                                <p class="font-bold text-gray-900">{{ $programa->modalidad }}</p>
                            </div>
                        </div>

                        <!-- Ítem Inversión Total -->
                        <div class="flex items-center gap-4 mb-6">
                            <div
                                class="w-10 h-10 rounded-full bg-gray-100 flex items-center justify-center text-unmsm-guinda">
                                <i class="fa-solid fa-money-bill-wave"></i>
                            </div>
                            <div>
                                <p class="text-xs text-gray-500 uppercase font-bold">Inversión Total</p>
                                <p class="font-bold text-gray-900">
                                    @php
                                        $grado = strtolower($programa->grado);
                                        
                                        // Usar estructura idéntica a los templates de inversión
                                        if ($grado === 'doctorado') {
                                            $costoPorCredito = 210;
                                            $semestresData = [
                                                ['matricula' => 310, 'creditos' => 14],
                                                ['matricula' => 500, 'creditos' => 14],
                                                ['matricula' => 500, 'creditos' => 14],
                                                ['matricula' => 500, 'creditos' => 10],
                                                ['matricula' => 500, 'creditos' => 10],
                                                ['matricula' => 500, 'creditos' => 10],
                                            ];
                                        } else {
                                            $costoPorCredito = 160;
                                            $semestresData = [
                                                ['matricula' => 310, 'creditos' => 14],
                                                ['matricula' => 400, 'creditos' => 16],
                                                ['matricula' => 400, 'creditos' => 20],
                                                ['matricula' => 400, 'creditos' => 22],
                                            ];
                                        }
                                        
                                        // Calcular costo total
                                        $costoTotal = 0;
                                        foreach ($semestresData as $sem) {
                                            $costoTotal += $sem['matricula'] + ($sem['creditos'] * $costoPorCredito);
                                        }
                                    @endphp
                                    S/&nbsp;{{ number_format($costoTotal, 0) }}
                                </p>
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