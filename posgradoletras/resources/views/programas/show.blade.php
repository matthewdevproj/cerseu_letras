@extends('layouts.public')

@section('title', $programa->titulo_completo . ' - Posgrado Letras UNMSM')

@section('content')
    <!-- HERO DE SECCIÓN -->
    {{-- El subtítulo es la denominación del título que otorga el programa, con
         su propio rótulo editable. Sin denominación cargada no se pasa nada y
         el hero no dibuja el párrafo: antes se rellenaba solo con «Grado que
         otorga: …», que en un diplomado no corresponde (Obs. N.º 4). --}}
    <x-hero-section :title="$programa->titulo_completo" :label="$programa->grado"
        :subtitle="$programa->denominacion_otorga_texto" :image="$programa->imagen_url" />

    <!-- CONTENIDO PRINCIPAL -->
    <div class="container mx-auto px-4 py-12">
        @if($programa->grado === 'Diplomado')
            <x-breadcrumbs :items="[
                ['label' => 'Diplomados', 'url' => route('diplomados.index')],
                ['label' => $programa->titulo_completo],
            ]" />
        @else
            <x-breadcrumbs :items="[
                ['label' => 'Programas', 'url' => route('programas.index')],
                ['label' => $programa->grado . 's', 'url' => route('programas.index') . '?tipo=' . strtolower($programa->grado)],
                ['label' => $programa->titulo_completo],
            ]" />
        @endif
        <div class="grid lg:grid-cols-3 gap-8">

            <!-- COLUMNA IZQUIERDA: Acordeones (2/3 ancho) -->
            <div class="lg:col-span-2">
                <div class="space-y-4">

                    {{-- Responsable académico: la denominación («Coordinador» o
                         «Coordinadora») se configura por programa. --}}
                    @php
                        $coordinador = $programa->coordinador;
                    @endphp
                    @if($coordinador)
                        <x-docente-mini-card :profesor="$coordinador" variant="coordinador"
                            :denominacion="\App\Models\Programa::denominacionCoordinador($coordinador->pivot->coordinador_denominacion)" />
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
                                <h3 class="font-bold text-gray-900 mb-3 border-b pb-2">Perfil de Ingreso</h3>
                                <ul class="list-disc list-inside space-y-2 text-sm text-gray-600">
                                    @foreach ($programa->perfil_ingresante as $perfil)
                                        <li class="text-justify">{{ $perfil }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @else
                            <div>
                                <h3 class="font-bold text-gray-900 mb-3 border-b pb-2">Perfil de Ingreso</h3>
                                <p class="text-sm text-gray-600 text-justify">
                                    Profesionales con grado de bachiller interesados en profundizar conocimientos en el área de especialización del programa.
                                </p>
                            </div>
                        @endif

                        <div>
                            <h3 class="font-bold text-gray-900 mb-3 border-b pb-2">Perfil de Graduado</h3>
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
                        @php
                            $unidadPlan = $programa->grado === 'Diplomado' ? 'módulos' : 'semestres';
                        @endphp
                        <p class="mb-6">El plan de estudios consta de {{ $programa->duracion }} {{ $unidadPlan }} académicos con un
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
                                                <span class="font-bold text-sm">{{ $programa->grado === 'Diplomado' ? 'Módulo' : 'Semestre' }}</span>
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

                            {{-- Sumillas por Semestre (oculta para Diplomados; la información permanece en plan_estudios) --}}
                            @if($programa->grado !== 'Diplomado')
                            <div class="mt-10">
                                <h3 class="text-xl font-bold text-unmsm-guinda mb-6 border-b-2 border-unmsm-guinda pb-2">SUMILLAS</h3>

                                @foreach ($cursosPorCiclo as $ciclo => $cursos)
                                    <div class="mb-8">
                                        <h4 class="text-lg font-bold text-gray-900 mb-4 uppercase">
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
                                        </h4>

                                        <div class="space-y-5">
                                            @foreach ($cursos as $curso)
                                                <div>
                                                    <h5 class="font-bold text-gray-900 mb-2">{{ $curso['nombre'] ?? 'Sin nombre' }}</h5>
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
                        @endif

                        <div class="text-center mt-6">
                            @if($programa->plan_url)
                                <x-button href="{{ $programa->plan_url }}" target="_blank" rel="noopener noreferrer" variant="ghost" icon="fa-solid fa-file-pdf">
                                    Descargar Malla Curricular Completa (PDF)
                                </x-button>
                            @else

                            @endif
                        </div>
                    </div>
                    @endcomponent

                    {{-- Plana Docente (no aplica a Diplomados: solo se muestra el Coordinador, arriba) --}}
                    @if ($programa->grado !== 'Diplomado' && $programa->docentes->count() > 0)
                    @component('components.accordion', [
                        'id' => 'plana-docente',
                        'title' => 'Plana Docente'
                    ])
                        <div class="grid gap-4">
                            @foreach ($programa->docentes as $profesor)
                                <x-docente-mini-card :profesor="$profesor" variant="list"
                                    :es-coordinador="(bool) $profesor->pivot->es_coordinador"
                                    :denominacion="\App\Models\Programa::denominacionCoordinador($profesor->pivot->coordinador_denominacion)" />
                            @endforeach
                        </div>
                    @endcomponent
                    @endif

                    {{-- Horarios --}}
                    @if ($programa->horario_url)
                    @component('components.accordion', [
                        'id' => 'horarios',
                        'title' => 'Horarios'
                    ])
                    <div class="text-gray-700">
                            <div class="mb-6">
                                <iframe src="{{ $programa->horario_url }}" 
                                        class="w-full border border-gray-300 rounded-lg" 
                                        style="height: 600px;">
                                </iframe>
                            </div>
                            <div class="text-center">
                                <x-button href="{{ $programa->horario_url }}" target="_blank" rel="noopener noreferrer" icon="fa-solid fa-file-pdf">
                                    Descargar Horarios (PDF)
                                </x-button>
                            </div>
                    </div>
                    @endcomponent
                    @endif

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
                        @elseif(strtolower($programa->grado) === 'diplomado')
                            @include('programas.inversion.diplomado', ['programa' => $programa])
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
                        <x-button href="{{ $programa->admision_pdf_url ?: route('admision') }}"
                            target="{{ $programa->admision_pdf_url ? '_blank' : '_self' }}"
                            icon="fa-solid fa-pen-to-square">
                            Ver Proceso de Admisión
                        </x-button>
                    </div>
                    @endcomponent

                </div>
            </div>

            <!-- COLUMNA DERECHA: Sidebar Sticky con Información Clave -->
            <div class="lg:col-span-1">
                <div class="sticky top-24 space-y-6">

                    <!-- Tarjeta Información Clave -->
                    <div class="bg-white rounded-xl shadow-lg border-t-4 border-unmsm-guinda p-6">
                        <h2 class="text-lg font-bold text-gray-800 mb-6 flex items-center gap-2">
                            <x-fas-circle-info class="text-unmsm-guinda" /> Información Clave
                        </h2>

                        <!-- Ítem Créditos -->
                        <div class="flex items-center gap-4 mb-5">
                            <div
                                class="w-10 h-10 rounded-full bg-gray-100 flex items-center justify-center text-unmsm-guinda">
                                <x-fas-star />
                            </div>
                            <div>
                                <p class="text-xs text-gray-500 uppercase font-bold">Créditos</p>
                                <p class="font-bold text-gray-900">{{ $programa->creditos }} Académicos</p>
                            </div>
                        </div>

                        {{-- Horas académicas (Obs. N.º 6): va justo debajo de
                             Créditos y solo aparece si el programa las tiene
                             cargadas, para no anunciar un dato incompleto. --}}
                        @if($programa->horas_academicas)
                            <div class="flex items-center gap-4 mb-5">
                                <div
                                    class="w-10 h-10 rounded-full bg-gray-100 flex items-center justify-center text-unmsm-guinda">
                                    <x-fas-hourglass-half />
                                </div>
                                <div>
                                    <p class="text-xs text-gray-500 uppercase font-bold">Horas académicas</p>
                                    <p class="font-bold text-gray-900">{{ number_format($programa->horas_academicas, 0) }} horas</p>
                                </div>
                            </div>
                        @endif

                        <!-- Ítem Duración -->
                        <div class="flex items-center gap-4 mb-5">
                            <div
                                class="w-10 h-10 rounded-full bg-gray-100 flex items-center justify-center text-unmsm-guinda">
                                <x-far-clock />
                            </div>
                            <div>
                                <p class="text-xs text-gray-500 uppercase font-bold">Duración</p>
                                <p class="font-bold text-gray-900">
                                    {{ $programa->duracion_formateada }}
                                    @if($programa->grado !== 'Diplomado')
                                        ({{ $programa->duracion / 2 }} años)
                                    @endif
                                </p>
                            </div>
                        </div>

                        <!-- Ítem Modalidad -->
                        <div class="flex items-center gap-4 mb-5">
                            <div
                                class="w-10 h-10 rounded-full bg-gray-100 flex items-center justify-center text-unmsm-guinda">
                                <x-fas-laptop-file />
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
                                <x-fas-money-bill-wave />
                            </div>
                            <div>
                                <p class="text-xs text-gray-500 uppercase font-bold">Inversión Total</p>
                                <p class="font-bold text-gray-900">
                                    {{-- El importe lo calcula el modelo a partir de los datos del panel.
                                         Antes se recalculaba aquí con las tarifas escritas a mano, que
                                         podían quedar desalineadas con la pestaña de Inversión. --}}
                                    @if ($programa->costo_total !== null)
                                        S/&nbsp;{{ number_format($programa->costo_total, 0) }}
                                    @else
                                        <span class="text-gray-400 font-medium">Por definir</span>
                                    @endif
                                </p>
                            </div>
                        </div>

                        {{-- Orden de acciones solicitado por Posgrado (Obs. N.º 4):
                             1) Brochure  2) Postular Ahora  3) Consultar por WhatsApp.
                             El brochure se oculta por completo cuando el programa
                             todavía no tiene uno cargado, sin dejar hueco. --}}
                        @if ($programa->brochure_link)
                            <a href="{{ $programa->brochure_link }}" target="_blank" rel="noopener noreferrer"
                                class="group/brochure flex w-full items-center justify-center gap-2 py-3 mb-3 rounded-lg bg-unmsm-dorado/15 border border-unmsm-dorado/50 text-unmsm-guinda text-center font-bold text-sm hover:bg-unmsm-dorado hover:text-white hover:border-unmsm-dorado focus-visible:bg-unmsm-dorado focus-visible:text-white transition-all duration-300 focus-visible:outline focus-visible:outline-2 focus-visible:outline-unmsm-guinda focus-visible:outline-offset-2">
                                <x-fas-file-pdf aria-hidden="true" />
                                Brochure
                                <x-fas-arrow-up-right-from-square class="text-[0.7em] opacity-70 motion-safe:group-hover/brochure:translate-x-0.5 transition-transform" aria-hidden="true" />
                                <span class="sr-only">(se abre en una pestaña nueva)</span>
                            </a>
                        @endif

                        <!-- Botón Principal -->
                        <x-button href="{{ $programa->grado === 'Diplomado' ? route('diplomados.admision') : route('admision') }}"
                            :block="true" icon="fa-solid fa-pen-to-square" class="shadow-md mb-3 transform hover:scale-105 duration-200">
                            Postular Ahora
                        </x-button>

                        <a href="{{ \App\Models\SiteSetting::contacto('whatsapp') }}" target="_blank" rel="noopener noreferrer"
                            class="block w-full py-3 bg-white border border-gray-300 text-gray-700 text-center rounded-lg font-bold hover:bg-gray-50 transition text-sm">
                            <x-fab-whatsapp class="mr-2 text-green-600" /> Consultar por WhatsApp
                        </a>
                    </div>

                    <!-- Tarjeta Contacto Rápido -->
                    <div class="bg-gray-900 rounded-xl p-6 text-white shadow-lg relative overflow-hidden">
                        <div
                            class="absolute top-0 right-0 w-32 h-32 bg-unmsm-guinda opacity-20 rounded-full blur-2xl -mr-10 -mt-10">
                        </div>
                        <h2 class="font-bold mb-4 relative z-10">¿Necesitas ayuda?</h2>
                        <p class="text-sm text-gray-400 mb-4 relative z-10">Contáctanos directamente:</p>

                        @php
                            $contactoEmail = \App\Models\SiteSetting::contacto('admision');
                            $contactoTelefono = \App\Models\SiteSetting::contacto('telefono');
                        @endphp
                        @if ($contactoEmail)
                            <a href="mailto:{{ $contactoEmail }}"
                                class="flex items-center gap-3 text-sm hover:text-unmsm-dorado transition mb-3 relative z-10 break-all">
                                <x-fas-envelope class="text-unmsm-dorado flex-shrink-0" aria-hidden="true" />
                                {{ $contactoEmail }}
                            </a>
                        @endif
                        @if ($contactoTelefono)
                            <a href="tel:+51{{ preg_replace('/\D/', '', $contactoTelefono) }}"
                                class="flex items-center gap-3 text-sm hover:text-unmsm-dorado transition relative z-10">
                                <x-fas-phone class="text-unmsm-dorado flex-shrink-0" aria-hidden="true" />
                                {{ $contactoTelefono }}
                            </a>
                        @endif
                    </div>

                </div>
            </div>

        </div>
    </div>
@endsection