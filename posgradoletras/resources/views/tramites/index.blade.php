@extends('layouts.public')

@section('title', 'Trámites - Posgrado Letras UNMSM')

@section('content')
    <div class="container mx-auto px-4 py-8">

        <h2 class="text-2xl md:text-3xl font-bold text-unmsm-guinda mb-6 border-b-2 border-unmsm-dorado/30 pb-2 font-serif">
            Trámites y Requisitos
        </h2>

        <div class="space-y-8">

            <!-- Requisitos para Magíster -->
            <div class="bg-white border border-gray-200 rounded-lg p-6">
                <h3 class="text-xl font-bold text-unmsm-guinda mb-4 flex items-center gap-2 font-serif">
                    <svg xmlns="http://www.w3.org/2000/svg" class="text-unmsm-dorado h-6 w-6" fill="none"
                        viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    Requisitos para la Obtención del Grado de Magíster
                </h3>
                <ul class="space-y-3 text-gray-600">
                    <li class="flex gap-3">
                        <span class="w-1.5 h-1.5 bg-unmsm-guinda rounded-full mt-2 shrink-0"></span>
                        Haber aprobado los estudios de una duración mínima de dos (2) semestres académicos, con un contenido
                        mínimo de cuarenta y ocho (48) créditos.
                    </li>
                    <li class="flex gap-3">
                        <span class="w-1.5 h-1.5 bg-unmsm-guinda rounded-full mt-2 shrink-0"></span>
                        Dominio de un idioma extranjero o lengua nativa.
                    </li>
                    <li class="flex gap-3">
                        <span class="w-1.5 h-1.5 bg-unmsm-guinda rounded-full mt-2 shrink-0"></span>
                        Elaboración de una tesis o trabajo de investigación en la especialidad respectiva.
                    </li>
                    <li class="flex gap-3">
                        <span class="w-1.5 h-1.5 bg-unmsm-guinda rounded-full mt-2 shrink-0"></span>
                        Aprobación de la sustentación pública de la tesis.
                    </li>
                </ul>
            </div>

            <!-- Requisitos para Doctor -->
            <div class="bg-white border border-gray-200 rounded-lg p-6">
                <h3 class="text-xl font-bold text-unmsm-guinda mb-4 flex items-center gap-2 font-serif">
                    <svg xmlns="http://www.w3.org/2000/svg" class="text-unmsm-dorado h-6 w-6" fill="none"
                        viewBox="0 0 24 24" stroke="currentColor">
                        <path d="M12 14l9-5-9-5-9 5 9 5z" />
                        <path
                            d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 14l9-5-9-5-9 5 9 5zm0 0l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14zm-4 6v-7.5l4-2.222" />
                    </svg>
                    Requisitos para la Obtención del Grado de Doctor
                </h3>
                <ul class="space-y-3 text-gray-600">
                    <li class="flex gap-3">
                        <span class="w-1.5 h-1.5 bg-unmsm-guinda rounded-full mt-2 shrink-0"></span>
                        Haber obtenido el grado de Magíster.
                    </li>
                    <li class="flex gap-3">
                        <span class="w-1.5 h-1.5 bg-unmsm-guinda rounded-full mt-2 shrink-0"></span>
                        Haber aprobado los estudios de una duración mínima de seis (6) semestres académicos, con un
                        contenido mínimo de sesenta y cuatro (64) créditos.
                    </li>
                    <li class="flex gap-3">
                        <span class="w-1.5 h-1.5 bg-unmsm-guinda rounded-full mt-2 shrink-0"></span>
                        Dominio de dos idiomas extranjeros, uno de los cuales puede ser sustituido por una lengua nativa.
                    </li>
                    <li class="flex gap-3">
                        <span class="w-1.5 h-1.5 bg-unmsm-guinda rounded-full mt-2 shrink-0"></span>
                        Elaboración de una tesis de máxima rigurosidad académica y de carácter original.
                    </li>
                    <li class="flex gap-3">
                        <span class="w-1.5 h-1.5 bg-unmsm-guinda rounded-full mt-2 shrink-0"></span>
                        Aprobación de la sustentación pública de la tesis.
                    </li>
                </ul>
            </div>

            <!-- Reglamentos y Directivas -->
            <div class="bg-unmsm-guinda/5 border border-unmsm-guinda/20 rounded-lg p-6">
                <h3 class="text-lg font-bold text-unmsm-guinda mb-4 font-serif">Reglamentos y Directivas</h3>
                <p class="text-gray-600 mb-4">Consulta los documentos oficiales para más información sobre el proceso de
                    obtención de grados.</p>
                <div class="flex gap-3">
                    <a href="#"
                        class="inline-flex items-center gap-2 text-sm bg-unmsm-guinda text-white px-4 py-2 rounded hover:bg-unmsm-guinda/90 transition-colors">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        Reglamento de Posgrado
                    </a>
                    <a href="#"
                        class="inline-flex items-center gap-2 text-sm bg-white text-unmsm-guinda border border-unmsm-guinda px-4 py-2 rounded hover:bg-unmsm-guinda/5 transition-colors">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        Directivas Académicas
                    </a>
                </div>
            </div>

        </div>
    </div>
@endsection
