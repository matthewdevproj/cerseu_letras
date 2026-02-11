@extends('layouts.public')

@section('title', 'Admisión 2026-I - Posgrado Letras UNMSM')

{{-- ========================================================
CORREOS DE CONTACTO - MODIFICAR AQUÍ
======================================================== --}}
@php
    // Correo para Admisión (envío de expediente, consultas de inscripción)
    $emailAdmision = config('contacts.admision');

    // Correo general para otras consultas
    $emailGeneral = config('contacts.general');

    // Teléfono / WhatsApp
    $telefono = config('contacts.telefono');
    $whatsapp = config('contacts.whatsapp');
@endphp

@push('styles')
    <style>
        .fade-in {
            animation: fadeIn 0.5s ease-in-out;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .step-card {
            border-left: 4px solid #761e23;
            transition: all 0.3s ease;
        }

        .step-card:hover {
            border-left-color: #d4af37;
            transform: translateX(4px);
        }

        .step-number {
            width: 48px;
            height: 48px;
            background: linear-gradient(135deg, #761e23 0%, #5a161a 100%);
            color: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            font-size: 1.25rem;
            flex-shrink: 0;
        }

        .requirement-list li {
            position: relative;
            padding-left: 1.5rem;
        }

        .requirement-list li::before {
            content: "✓";
            position: absolute;
            left: 0;
            color: #761e23;
            font-weight: bold;
        }

        .tab-btn.active {
            background: #761e23;
            color: white;
        }

        .tab-content {
            display: none;
        }

        .tab-content.active {
            display: block;
            animation: fadeIn 0.3s ease-out;
        }

        .cronograma-table {
            width: 100%;
            border-collapse: collapse;
        }

        .cronograma-table th,
        .cronograma-table td {
            padding: 0.75rem 1rem;
            text-align: left;
            border-bottom: 1px solid #e5e7eb;
        }

        .cronograma-table th {
            background: #f9fafb;
            font-weight: 600;
            color: #374151;
            font-size: 0.75rem;
            text-transform: uppercase;
        }

        .cronograma-table tbody tr:hover {
            background: #fef3f2;
        }
    </style>
@endpush

@section('content')
    <!-- HERO DE SECCIÓN -->
    <x-hero-section title="Admisión 2026-I" label="Proceso de Inscripción"
        subtitle="Inicia tu camino hacia la excelencia académica con la Decana de América"
        image="https://letras.unmsm.edu.pe/wp-content/uploads/2025/12/IMG_1565-scaled.jpg" />

    <section class="container mx-auto px-6 py-16 fade-in">

        <!-- Fecha límite destacada -->
        <div class="bg-gradient-to-r from-unmsm-guinda to-red-900 text-white rounded-2xl p-6 mb-12 shadow-xl">
            <div class="flex flex-col md:flex-row items-center justify-between gap-4">
                <div class="flex items-center gap-4">
                    <div class="w-16 h-16 bg-white/20 rounded-full flex items-center justify-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                    </div>
                    <div>
                        <p class="text-white/80 text-sm uppercase tracking-wider">Inscripciones abiertas</p>
                        <p class="text-2xl font-bold">05 de enero al 02 de abril del 2026</p>
                    </div>
                </div>
                <div class="text-center md:text-right">
                    <p class="text-white/80 text-sm">Publicación de resultados</p>
                    <p class="text-xl font-bold text-unmsm-dorado">09 de abril del 2026</p>
                </div>
            </div>
        </div>

        <div class="grid lg:grid-cols-3 gap-8">
            <!-- Columna Principal -->
            <div class="lg:col-span-2 space-y-8">

                <!-- Cronograma -->
                <div class="bg-white border border-gray-200 rounded-xl overflow-hidden shadow-md">
                    <div class="bg-unmsm-guinda text-white p-4">
                        <h3 class="font-bold text-lg font-serif">Cronograma del Proceso de Admisión 2026-I</h3>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="cronograma-table">
                            <thead>
                                <tr>
                                    <th>Actividad</th>
                                    <th>Fecha</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td class="font-medium text-gray-800">Inscripción de postulantes y envío de expediente
                                    </td>
                                    <td class="text-unmsm-guinda font-semibold">05 de enero al 25 de marzo</td>
                                </tr>
                                <tr>
                                    <td class="font-medium text-gray-800">Examen de conocimientos (Maestrías y Doctorados)
                                    </td>
                                    <td class="text-gray-600">26 de marzo</td>
                                </tr>
                                <tr>
                                    <td class="font-medium text-gray-800">Entrevista personal para Doctorado</td>
                                    <td class="text-gray-600">27 de marzo</td>
                                </tr>
                                <tr>
                                    <td class="font-medium text-gray-800">Evaluación del expediente</td>
                                    <td class="text-gray-600">Hasta el 30 de marzo</td>
                                </tr>
                                <tr>
                                    <td class="font-medium text-gray-800">Entrevista personal para Maestría</td>
                                    <td class="text-gray-600">31 de marzo</td>
                                </tr>
                                <tr class="bg-green-50">
                                    <td class="font-bold text-green-800">Publicación de resultados</td>
                                    <td class="text-green-700 font-bold">09 de abril</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="bg-white border border-gray-200 rounded-xl p-6 shadow-md">
                    <h3 class="font-bold text-lg text-unmsm-guinda mb-4 font-serif">Paso 1: Realizar el pago por derecho a
                        Inscripción</h3>

                    <div class="bg-blue-50 border-l-4 border-blue-500 p-4 mb-6">
                        <p class="text-gray-700 text-sm">
                            <strong>Importante:</strong> Antes de realizar el pago por derecho de inscripción verifique que
                            el programa de su
                            interés participe en el proceso de admisión actual y que esté dentro del cronograma establecido
                            en el
                            presente proceso de admisión.
                        </p>
                    </div>

                    <h4 class="font-bold text-md text-gray-800 mb-3">Costos de Inscripción</h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 md:gap-6 mb-6">
                        {{-- Maestría --}}
                        <div class="border border-gray-200 rounded-xl overflow-hidden shadow-sm">
                            <div class="bg-unmsm-guinda text-white p-3 md:p-4 text-center">
                                <h4 class="font-bold text-base md:text-lg">Maestría</h4>
                            </div>
                            <div class="p-3 md:p-5 space-y-3 md:space-y-4">
                                {{-- S/ 350 --}}
                                <div class="text-center p-3 md:p-4 bg-gray-50 rounded-lg border border-gray-100">
                                    <p class="text-xs md:text-sm text-gray-600 mb-2 md:mb-3">
                                        Graduados y personal administrativo de la UNMSM, docentes de universidades
                                        nacionales y Magisterio Nacional
                                    </p>
                                    <a href="https://sanmarket.unmsm.edu.pe/#/catalogo/dedff708-2b46-4e8a-9c8d-b035913e3b2a"
                                        target="_blank"
                                        class="inline-flex items-center gap-2 px-4 md:px-6 py-2 md:py-3 bg-unmsm-guinda text-white font-bold text-base md:text-xl rounded-lg hover:bg-red-900 transition-colors shadow-md">
                                        S/ 350.00 <i class="fas fa-external-link-alt text-xs md:text-sm"></i>
                                    </a>
                                </div>
                                {{-- S/ 450 --}}
                                <div class="text-center p-3 md:p-4 bg-gray-50 rounded-lg border border-gray-100">
                                    <p class="text-xs md:text-sm text-gray-600 mb-2 md:mb-3">
                                        Otros postulantes
                                    </p>
                                    <a href="https://sanmarket.unmsm.edu.pe/#/catalogo/6cce262e-3c78-42d8-9b1c-2a0666273547"
                                        target="_blank"
                                        class="inline-flex items-center gap-2 px-4 md:px-6 py-2 md:py-3 bg-unmsm-guinda text-white font-bold text-base md:text-xl rounded-lg hover:bg-red-900 transition-colors shadow-md">
                                        S/ 450.00 <i class="fas fa-external-link-alt text-xs md:text-sm"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                        {{-- Doctorado --}}
                        <div class="border border-gray-200 rounded-xl overflow-hidden shadow-sm">
                            <div class="bg-gray-800 text-white p-3 md:p-4 text-center">
                                <h4 class="font-bold text-base md:text-lg">Doctorado</h4>
                            </div>
                            <div class="p-3 md:p-5 space-y-3 md:space-y-4">
                                {{-- S/ 400 --}}
                                <div class="text-center p-3 md:p-4 bg-gray-50 rounded-lg border border-gray-100">
                                    <p class="text-xs md:text-sm text-gray-600 mb-2 md:mb-3">
                                        Graduados y personal administrativo de la UNMSM, docentes de universidades
                                        nacionales y Magisterio Nacional
                                    </p>
                                    <a href="https://sanmarket.unmsm.edu.pe/#/catalogo/92e3ea2e-818f-4288-b980-0ec85a359749"
                                        target="_blank"
                                        class="inline-flex items-center gap-2 px-4 md:px-6 py-2 md:py-3 bg-gray-800 text-white font-bold text-base md:text-xl rounded-lg hover:bg-gray-900 transition-colors shadow-md">
                                        S/ 400.00 <i class="fas fa-external-link-alt text-xs md:text-sm"></i>
                                    </a>
                                </div>
                                {{-- S/ 500 --}}
                                <div class="text-center p-3 md:p-4 bg-gray-50 rounded-lg border border-gray-100">
                                    <p class="text-xs md:text-sm text-gray-600 mb-2 md:mb-3">
                                        Otros postulantes
                                    </p>
                                    <a href="https://sanmarket.unmsm.edu.pe/#/catalogo/5f1ffb8f-a462-4757-9443-a51407f51f2e"
                                        target="_blank"
                                        class="inline-flex items-center gap-2 px-4 md:px-6 py-2 md:py-3 bg-gray-800 text-white font-bold text-base md:text-xl rounded-lg hover:bg-gray-900 transition-colors shadow-md">
                                        S/ 500.00 <i class="fas fa-external-link-alt text-xs md:text-sm"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <h3 class="font-bold text-lg text-unmsm-guinda mb-4 font-serif mt-6">Procedimiento de pago de
                    inscripción</h3>

                <div class="space-y-6">
                    <!-- Paso 1: Generar ticket -->
                    <div class="border border-gray-200 rounded-lg p-4">
                        <div class="flex items-start gap-3 mb-3">
                            <span
                                class="flex-shrink-0 w-8 h-8 bg-unmsm-guinda text-white rounded-full flex items-center justify-center text-sm font-bold">1</span>
                            <div class="flex-1">
                                <h4 class="font-bold text-gray-800 mb-2">Generar ticket en SanMarket-UNMSM</h4>
                                <p class="text-sm text-gray-600 mb-3">Registrarse con correo de dominio Gmail.</p>
                            </div>
                        </div>
                        <div class="w-full aspect-video rounded-lg overflow-hidden shadow-md">
                            <iframe class="w-full h-full" src="https://www.youtube.com/embed/wDpbuHt1xg4"
                                title="Tutorial: Generar ticket en SanMarket-UNMSM" frameborder="0"
                                allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
                                allowfullscreen>
                            </iframe>
                        </div>
                    </div>

                    <!-- Paso 2: Realizar el pago -->
                    <div class="border border-gray-200 rounded-lg p-4">
                        <div class="flex items-start gap-3 mb-3">
                            <span
                                class="flex-shrink-0 w-8 h-8 bg-unmsm-guinda text-white rounded-full flex items-center justify-center text-sm font-bold">2</span>
                            <div class="flex-1">
                                <h4 class="font-bold text-gray-800 mb-2">Realizar el pago en BCP o Yape</h4>
                                <p class="text-sm text-gray-600 mb-3">Puedes pagar a través de la App BCP, en un agente
                                    BCP o mediante Yape.</p>
                            </div>
                        </div>
                        <div class="w-full aspect-video rounded-lg overflow-hidden shadow-md">
                            <iframe class="w-full h-full" src="https://www.youtube.com/embed/feg7DN0pSLM"
                                title="Tutorial: Realizar el pago en BCP o Yape" frameborder="0"
                                allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
                                allowfullscreen>
                            </iframe>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Paso 2: Registrar comprobante -->
            <div class="bg-white border border-gray-200 rounded-xl p-6 shadow-md">
                <h3 class="font-bold text-lg text-unmsm-guinda mb-4 font-serif">Paso 2: Generación del código de postulante -
                    Inscripción Admisión</h3>

                <div class="bg-amber-50 border-l-4 border-amber-500 p-4 mb-6">
                    <p class="text-gray-700 text-sm">
                        Con la finalidad de que usted pueda adjuntar el comprobante de pago y habilitar su inscripción,
                        ponemos a su disposición este módulo.
                    </p>
                </div>

                <h4 class="font-bold text-md text-gray-800 mb-3">Deberá tener en cuenta lo siguiente:</h4>

                <div class="space-y-4 mb-6">
                    <div class="flex items-start gap-3">
                        <div
                            class="flex-shrink-0 w-6 h-6 bg-unmsm-guinda rounded-full flex items-center justify-center mt-0.5">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 text-white" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3"
                                    d="M5 13l4 4L19 7" />
                            </svg>
                        </div>
                        <p class="text-gray-700 text-sm">
                            El <strong>comprobante de pago</strong> que adjunte deberá ser <strong>legible</strong> y
                            estar
                            <strong>a nombre de la Universidad Nacional Mayor de San Marcos</strong>, ya que estará
                            sujeto a una verificación.
                            De no cumplir con estas especificaciones, su inscripción será invalidada así haya obtenido
                            una vacante.
                        </p>
                    </div>

                    <div class="flex items-start gap-3">
                        <div
                            class="flex-shrink-0 w-6 h-6 bg-unmsm-guinda rounded-full flex items-center justify-center mt-0.5">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 text-white" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3"
                                    d="M5 13l4 4L19 7" />
                            </svg>
                        </div>
                        <p class="text-gray-700 text-sm">
                            El pago por el derecho de admisión necesariamente tiene que ser realizado a través de la
                            <strong> <a href="https://sanmarket.unmsm.edu.pe/#/"
                                    class="text-unmsm-guinda font-semibold hover:underline">
                                    plataforma de SanMarket
                                </a></strong>.
                        </p>
                    </div>

                    <div class="flex items-start gap-3">
                        <div
                            class="flex-shrink-0 w-6 h-6 bg-unmsm-guinda rounded-full flex items-center justify-center mt-0.5">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 text-white" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3"
                                    d="M5 13l4 4L19 7" />
                            </svg>
                        </div>
                        <p class="text-gray-700 text-sm">
                            El <strong>número de documento de identidad</strong> que especifique al momento de
                            registrarse,
                            podrá ser usado una <strong>sola vez</strong>.
                        </p>
                    </div>

                    <div class="flex items-start gap-3">
                        <div
                            class="flex-shrink-0 w-6 h-6 bg-unmsm-guinda rounded-full flex items-center justify-center mt-0.5">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 text-white" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3"
                                    d="M5 13l4 4L19 7" />
                            </svg>
                        </div>
                        <p class="text-gray-700 text-sm">
                            Deberá ingresar el <strong>Número de secuencia de pago</strong> que aparece en su
                            comprobante de pago
                            para poder registrarse en el sistema.
                        </p>
                    </div>

                    <div class="flex items-start gap-3">
                        <div
                            class="flex-shrink-0 w-6 h-6 bg-unmsm-guinda rounded-full flex items-center justify-center mt-0.5">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 text-white" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3"
                                    d="M5 13l4 4L19 7" />
                            </svg>
                        </div>
                        <p class="text-gray-700 text-sm">
                            Si tiene algún inconveniente en el registro, agradeceremos que nos pueda escribir
                            indicando sus nombres y apellidos y el número de documento de identidad al siguiente correo:
                            <a href="mailto:admision.dgep@unmsm.edu.pe"
                                class="text-unmsm-guinda font-semibold hover:underline">
                                admision.dgep@unmsm.edu.pe
                            </a>
                        </p>
                    </div>
                </div>

                <div class="bg-gray-50 border border-gray-300 rounded-lg p-4 mb-6">
                    <p class="text-gray-700 text-sm mb-2">
                        <strong>Ingresar sus datos al siguiente enlace (necesitará el número de secuencia de su
                            comprobante de pago):</strong>
                    </p>
                    <a href="https://posgrado.unmsm.edu.pe/admision/registro/index.php" target="_blank"
                        class="text-unmsm-guinda hover:underline break-all">
                        https://posgrado.unmsm.edu.pe/admision/registro/index.php
                    </a>
                </div>

                {{-- Video tutorial --}}
                <div class="border border-gray-200 rounded-lg p-4 bg-white">
                    <div class="flex items-start gap-3 mb-3">
                        <span
                            class="flex-shrink-0 w-8 h-8 bg-red-600 text-white rounded-full flex items-center justify-center text-sm">
                            <i class="fab fa-youtube"></i>
                        </span>
                        <div class="flex-1">
                            <h4 class="font-bold text-gray-800 mb-1">Video tutorial: Generación del código de postulante
                            </h4>
                        </div>
                    </div>
                    <div class="w-full aspect-video rounded-lg overflow-hidden shadow-md">
                        <iframe class="w-full h-full" src="https://www.youtube.com/embed/yLDo0Eezwbg?si=vyBC9GyRwbxoxwnz"
                            title="Tutorial: Generación del código de postulante" frameborder="0"
                            allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
                            referrerpolicy="strict-origin-when-cross-origin" allowfullscreen>
                        </iframe>
                    </div>
                </div>
            </div>

            <!-- Paso 3: Requisitos -->
            <div class="bg-white border border-gray-200 rounded-xl p-6 shadow-md">
                <h3 class="font-bold text-lg text-unmsm-guinda mb-6 font-serif">Conoce los requisitos para
                    postular a los programas de Maestría y Doctorado</h3>

                <!-- MAESTRÍA -->
                <div class="mb-8">
                    <div class="bg-unmsm-guinda text-white p-4 rounded-t-lg">
                        <h4 class="font-bold text-lg">MAESTRÍA</h4>
                    </div>
                    <div class="border border-gray-200 rounded-b-lg p-4">
                        <ol class="space-y-4 text-gray-700 text-sm">
                            <li class="flex gap-3">
                                <span
                                    class="flex-shrink-0 w-6 h-6 bg-unmsm-guinda text-white rounded-full flex items-center justify-center text-xs font-bold">1</span>
                                <div>
                                    <span class="font-medium">
                                        <a
                                            href="https://posgrado.unmsm.edu.pe/doc/resumen-hv-postulante"class="text-unmsm-guinda font-semibold hover:underline">
                                            Resumen de la hoja de vida del postulante.
                                        </a></span>
                                    <p class="text-gray-500 mt-1">Curriculum vitae, documentado, foliado y ordenado de
                                        acuerdo a los rubros del formato de hoja de vida del postulante <a
                                            href="https://posgrado.unmsm.edu.pe/doc/criterios-evaluacion-admision-2025"class="text-unmsm-guinda font-semibold hover:underline">
                                            (criterios de evaluación).
                                        </a></p>
                                </div>
                            </li>
                            <li class="flex gap-3">
                                <span
                                    class="flex-shrink-0 w-6 h-6 bg-unmsm-guinda text-white rounded-full flex items-center justify-center text-xs font-bold">2</span>
                                <span><a
                                        href="https://www.gob.pe/488-obtener-constancia-de-inscripcion-de-diplomas"class="text-unmsm-guinda font-semibold hover:underline">
                                        Constancia de inscripción en línea del grado de Bachiller, Maestro o Doctor
                                        emitida por SUNEDU (*).</a></span>
                            </li>
                            <li class="flex gap-3">
                                <span
                                    class="flex-shrink-0 w-6 h-6 bg-unmsm-guinda text-white rounded-full flex items-center justify-center text-xs font-bold">3</span>
                                <span>Anteproyecto de Investigación de acuerdo con la postulación (<a
                                        href="https://drive.google.com/file/d/1_wuk3rBMZq3KAnkD5QDdTQi0G_srzDMO/view"class="text-unmsm-guinda font-semibold hover:underline">
                                        Modelo para Maestrías</a>
                        
                                    ).</span>
                            </li>
                            <li class="flex gap-3">
                                <span
                                    class="flex-shrink-0 w-6 h-6 bg-unmsm-guinda text-white rounded-full flex items-center justify-center text-xs font-bold">4</span>
                                <span>Copia simple del documento de identidad (DNI, carné de extranjería o
                                    pasaporte).</span>
                            </li>
                            <li class="flex gap-3">
                                <span
                                    class="flex-shrink-0 w-6 h-6 bg-unmsm-guinda text-white rounded-full flex items-center justify-center text-xs font-bold">5</span>
                                <span>Partida de nacimiento.</span>
                            </li>
                            <li class="flex gap-3">
                                <span
                                    class="flex-shrink-0 w-6 h-6 bg-unmsm-guinda text-white rounded-full flex items-center justify-center text-xs font-bold">6</span>
                                <span>Recibo de pago por <a
                                        href="https://posgrado.unmsm.edu.pe/admision/guia-pago"class="text-unmsm-guinda font-semibold hover:underline">
                                        derecho de inscripción</a>, realizado a través de <a
                                        href="https://sanmarket.unmsm.edu.pe/#/"class="text-unmsm-guinda font-semibold hover:underline">
                                        SanMarket-UNMSM</a>,
                                    culminando en BCP (App o agente) o Yape.</span>
                            </li>
                            <li class="flex gap-3">
                                <span
                                    class="flex-shrink-0 w-6 h-6 bg-unmsm-guinda text-white rounded-full flex items-center justify-center text-xs font-bold">7</span>
                                <span>Una foto tamaño pasaporte con fondo blanco, sin gafas.</span>
                            </li>
                        </ol>
                        <div
                            class="mt-4 p-4 bg-amber-50 border border-amber-200 rounded-lg text-sm text-amber-800 space-y-2">
                            <p><strong>(*)</strong> Los postulantes que obtuvieron el grado de Bachiller en la
                                Universidad
                                Nacional Mayor de San Marcos solo presentarán copia simple.</p>
                            <p>En el caso de graduados en el extranjero, los grados y títulos deberán estar revalidados
                                o
                                reconocidos según las normas vigentes.</p>
                            <p>Solo las personas con discapacidad deberán presentar su carnet de CONADIS.</p>
                        </div>
                    </div>
                </div>

                <!-- DOCTORADO -->
                <div>
                    <div class="bg-gray-800 text-white p-4 rounded-t-lg">
                        <h4 class="font-bold text-lg">DOCTORADO</h4>
                    </div>
                    <div class="border border-gray-200 rounded-b-lg p-4">
                        <ol class="space-y-4 text-gray-700 text-sm">
                            <li class="flex gap-3">
                                <span
                                    class="flex-shrink-0 w-6 h-6 bg-gray-800 text-white rounded-full flex items-center justify-center text-xs font-bold">1</span>
                                <div>
                                    <span class="font-medium"><a
                                        href="https://posgrado.unmsm.edu.pe/doc/resumen-hv-postulante"class="text-unmsm-guinda font-semibold hover:underline">
                                        Resumen de la hoja de vida del postulante</a>.</span>
                                    <p class="text-gray-500 mt-1">Curriculum vitae, documentado, foliado y ordenado de
                                        acuerdo a los rubros del formato de hoja de vida del postulante (<a
                                        href="https://posgrado.unmsm.edu.pe/doc/criterios-evaluacion-admision-2025"class="text-unmsm-guinda font-semibold hover:underline">
                                        criterios de evaluación</a>).</p>
                                </div>
                            </li>
                            <li class="flex gap-3">
                                <span
                                    class="flex-shrink-0 w-6 h-6 bg-gray-800 text-white rounded-full flex items-center justify-center text-xs font-bold">2</span>
                                <span><a
                                        href="https://www.gob.pe/488-obtener-constancia-de-inscripcion-de-diplomas"class="text-unmsm-guinda font-semibold hover:underline">
                                        Constancia de inscripción en línea del grado de Bachiller, Maestro o Doctor emitida por SUNEDU (*).</a></span>
                            </li>
                            <li class="flex gap-3">
                                <span
                                    class="flex-shrink-0 w-6 h-6 bg-gray-800 text-white rounded-full flex items-center justify-center text-xs font-bold">3</span>
                                <span>Anteproyecto de Investigación de acuerdo con la postulación (<a
                                        href="https://drive.google.com/file/d/1zJZ8rEGNVNTbyy6r1gdV93hQOuTO8t-P/view?usp=sharing"class="text-unmsm-guinda font-semibold hover:underline">
                                        Modelo para Doctorados</a>).</span>
                            </li>
                            <li class="flex gap-3">
                                <span
                                    class="flex-shrink-0 w-6 h-6 bg-gray-800 text-white rounded-full flex items-center justify-center text-xs font-bold">4</span>
                                <span>Certificado de suficiencia de un idioma extranjero o lenguas originarias.</span>
                            </li>
                            <li class="flex gap-3">
                                <span
                                    class="flex-shrink-0 w-6 h-6 bg-gray-800 text-white rounded-full flex items-center justify-center text-xs font-bold">5</span>
                                <span>Copia simple del documento de identidad (DNI, carné de extranjería o
                                    pasaporte).</span>
                            </li>
                            <li class="flex gap-3">
                                <span
                                    class="flex-shrink-0 w-6 h-6 bg-gray-800 text-white rounded-full flex items-center justify-center text-xs font-bold">6</span>
                                <span>Partida de nacimiento.</span>
                            </li>
                            <li class="flex gap-3">
                                <span
                                    class="flex-shrink-0 w-6 h-6 bg-gray-800 text-white rounded-full flex items-center justify-center text-xs font-bold">7</span>
                                <span>Recibo de pago por <a
                                        href="https://posgrado.unmsm.edu.pe/admision/guia-pago"class="text-unmsm-guinda font-semibold hover:underline">
                                        derecho de inscripción</a>, realizado a través de <a
                                        href="https://sanmarket.unmsm.edu.pe/#/"class="text-unmsm-guinda font-semibold hover:underline">
                                        SanMarket-UNMSM</a>,
                                    culminando en BCP (App o agente) o Yape.</span>
                            </li>
                            <li class="flex gap-3">
                                <span
                                    class="flex-shrink-0 w-6 h-6 bg-gray-800 text-white rounded-full flex items-center justify-center text-xs font-bold">8</span>
                                <span>Una foto tamaño pasaporte con fondo blanco, sin gafas.</span>
                            </li>
                        </ol>
                        <div
                            class="mt-4 p-4 bg-amber-50 border border-amber-200 rounded-lg text-sm text-amber-800 space-y-2">
                            <p><strong>(*)</strong> Los postulantes que obtuvieron el grado de Maestro o Doctor en la
                                Universidad Nacional Mayor de San Marcos solo presentarán copia simple.</p>
                            <p>En el caso de graduados en el extranjero, los grados y títulos deberán estar revalidados
                                o
                                reconocidos según las normas vigentes.</p>
                            <p>Solo las personas con discapacidad deberán presentar su carnet de CONADIS.</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Envío de Expediente -->
            <div class="bg-white border border-gray-200 rounded-xl p-6 shadow-md">
                <h3 class="font-bold text-lg text-unmsm-guinda mb-4 font-serif">Paso 3: Envío de Expediente</h3>

                <p class="text-gray-700 mb-4">
                    Antes de enviar el expediente, deberá contar con su <strong>código de postulante</strong>.
                </p>

                <!-- Advertencia de Fecha Límite -->
                <div class="bg-red-50 border border-red-300 rounded-lg p-4 mb-6">
                    <p class="text-red-800 flex items-start gap-2 mb-2">
                        <i class="fas fa-clock mt-1"></i>
                        <span>
                            La recepción de documentos será hasta la <strong>1:00 pm del 25 de Marzo del 2026</strong>.
                        </span>
                    </p>
                    <p class="text-red-800 ml-6 mb-2">
                        La recepción de documentos será exclusivamente por el formulario: <br>
                        <a href="https://share.google/jHxcVcvkryeHseIsQ" target="_blank"
                            class="font-bold underline break-all hover:text-red-900">
                            https://share.google/jHxcVcvkryeHseIsQ
                        </a>
                    </p>
                    <p class="text-red-700 text-sm mt-2 font-medium ml-6">
                        No se recibirán documentos posteriores a la fecha y hora señalada.
                    </p>
                </div>

                <div class="bg-gray-50 rounded-lg p-4 mb-6">
                    <p class="font-bold text-gray-800 mb-3">Forma de envío para la evaluación de expediente:</p>
                    <div class="grid md:grid-cols-2 gap-6 text-sm">
                        <div>
                            <div class="flex items-center gap-2 mb-2">
                                <span class="bg-unmsm-guinda text-white px-2 py-1 rounded text-xs font-bold">PDF</span>
                                <span class="font-bold text-gray-800">En archivo PDF</span>
                            </div>
                            <ul class="text-gray-600 space-y-2">
                                <li class="flex items-start gap-2">
                                    <span class="text-unmsm-guinda">•</span>
                                    <span>Anteproyecto de investigación.</span>
                                </li>
                                <li class="flex items-start gap-2">
                                    <span class="text-unmsm-guinda">•</span>
                                    <span>Formato de hoja de vida con código de postulante, constancia SUNEDU,
                                        certificado de suficiencia.</span>
                                </li>
                                <li class="flex items-start gap-2">
                                    <span class="text-unmsm-guinda">•</span>
                                    <span>CV documentado conforme a la hoja de vida del postulante.</span>
                                </li>
                                <li class="flex items-start gap-2">
                                    <span class="text-unmsm-guinda">•</span>
                                    <span>Partida de nacimiento y DNI.</span>
                                </li>
                                <li class="flex items-start gap-2">
                                    <span class="text-unmsm-guinda">•</span>
                                    <span>Recibo de pago.</span>
                                </li>
                            </ul>
                        </div>
                        <div>
                            <div class="flex items-center gap-2 mb-2">
                                <span class="bg-blue-600 text-white px-2 py-1 rounded text-xs font-bold">JPG</span>
                                <span class="font-bold text-gray-800">En archivo JPG</span>
                            </div>
                            <ul class="text-gray-600 space-y-2">
                                <li class="flex items-start gap-2">
                                    <span class="text-blue-600">•</span>
                                    <span>Foto.</span>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Enlace al Formulario -->
                <div class="text-center">
                    <a href="https://share.google/jHxcVcvkryeHseIsQ" target="_blank"
                        class="inline-flex items-center gap-2 px-6 py-4 bg-unmsm-dorado text-white font-bold text-lg rounded-xl hover:bg-yellow-600 transition-all shadow-lg hover:shadow-xl transform hover:-translate-y-1 w-full md:w-auto justify-center">
                        <i class="fas fa-file-upload text-xl"></i>
                        <span>REMISIÓN DE DOCUMENTOS – ADMISIÓN POSGRADO LETRAS 2026-I</span>
                    </a>
                    <p class="text-sm text-gray-500 mt-3">Clic en el botón para acceder al formulario de envío</p>
                </div>
            </div>

            <!-- Resultados -->
            <div class="bg-white border border-gray-200 rounded-xl p-6 shadow-md space-y-6">

    <!-- PASO 4 -->
    <div>
        <h3 class="font-bold text-lg text-unmsm-guinda mb-4 font-serif">
            Paso 4: Evaluaciones del proceso de admisión
        </h3>

        <!-- Examen -->
        <div class="bg-blue-50 border border-blue-200 rounded-lg p-5 mb-4">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-blue-600" fill="none"
                        viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9.75 3v1.5m4.5-1.5v1.5M4.5 7.5h15M6.75 7.5v11.25A2.25 2.25 0 009 21h6a2.25 2.25 0 002.25-2.25V7.5" />
                    </svg>
                </div>
                <div>
                    <p class="text-blue-800 font-medium">Examen de conocimientos</p>
                    <p class="text-blue-700 text-sm">Maestrías y Doctorados</p>
                    <p class="text-blue-700 font-bold">26 de marzo de 2026</p>
                </div>
            </div>
        </div>

        <!-- Entrevista -->
        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-5">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 bg-yellow-100 rounded-full flex items-center justify-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-yellow-600" fill="none"
                        viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 14.25c3.313 0 6-2.686 6-6S15.313 2.25 12 2.25 6 4.936 6 8.25s2.687 6 6 6z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4.5 21a7.5 7.5 0 0115 0" />
                    </svg>
                </div>
                <div>
                    <p class="text-yellow-800 font-medium">Entrevista personal</p>
                    <p class="text-yellow-700 text-sm">
                        Doctorado: <strong>27 de marzo de 2026</strong><br>
                        Maestría: <strong>30 de marzo de 2026</strong>
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- DIVISOR SUAVE -->
    <hr class="border-gray-200">

    <!-- PASO 5 -->
    <div>
        <h3 class="font-bold text-lg text-unmsm-guinda mb-4 font-serif">
            Paso 5: Visualizar los Resultados
        </h3>

        <div class="bg-green-50 border border-green-200 rounded-lg p-5">
            <div class="flex items-center gap-4 mb-3">
                <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-green-600" fill="none"
                        viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <div>
                    <p class="text-green-800 font-medium">Publicación de resultados</p>
                    <p class="text-green-700 text-xl font-bold">09 de abril del 2026</p>
                </div>
            </div>
            <p class="text-green-700 text-sm">
                Los resultados serán publicados aquí y también serán enviados a sus correos electrónicos.
            </p>
        </div>

        <p class="text-gray-600 text-sm mt-4">
            Cualquier información adicional, pueden revisar la página de la Dirección General de Estudios de Posgrado
            <a href="https://posgrado.unmsm.edu.pe/" target="_blank"
                class="text-unmsm-guinda font-medium hover:underline">
                https://posgrado.unmsm.edu.pe/
            </a>,
            si hubiera otra consulta escribir al correo
            <a href="mailto:{{ $emailGeneral }}"
                class="text-unmsm-guinda font-medium hover:underline">
                {{ $emailGeneral }}
            </a>.
        </p>
    </div>

</div>


        </div>

        <!-- Sidebar -->
        <div class="lg:col-span-1 space-y-6">
            <!-- Contacto -->
            <div class="bg-unmsm-guinda text-white rounded-2xl p-6 shadow-xl sticky top-24">
                <h3 class="font-bold mb-4 font-serif text-xl">¿Necesitas ayuda?</h3>
                <p class="text-sm mb-4 text-white/80">Contáctanos para resolver cualquier duda sobre el proceso de
                    admisión.</p>
                <div class="space-y-3 text-sm">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-white/20 rounded-full flex items-center justify-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                            </svg>
                        </div>
                        <div>
                            <span class="text-white/60 text-xs">Email</span>
                            <p class="font-medium">{{ $emailAdmision }}</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-white/20 rounded-full flex items-center justify-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                            </svg>
                        </div>
                        <div>
                            <span class="text-white/60 text-xs">WhatsApp</span>
                            <a href="{{ $whatsapp }}" target="_blank"
                                class="font-medium hover:text-white/80 transition-colors flex items-center gap-1">
                                {{ $telefono }} <i class="fas fa-external-link-alt text-xs"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Enlaces útiles -->
            <div class="bg-white border border-gray-200 rounded-xl p-6 shadow-md">
                <h4 class="font-bold text-gray-800 mb-4">Enlaces útiles</h4>
                <div class="space-y-2">
                    <a href="https://posgrado.unmsm.edu.pe/admision/inscripcion/subir_Voucher/Subir/index.php"
                        target="_blank" class="flex items-center gap-2 text-sm text-unmsm-guinda hover:underline">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                        </svg>
                        Subir comprobante de pago
                    </a>
                    <a href="https://sanmarket.unmsm.edu.pe" target="_blank"
                        class="flex items-center gap-2 text-sm text-unmsm-guinda hover:underline">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                        SanMarket UNMSM
                    </a>
                    <a href="https://posgrado.unmsm.edu.pe/" target="_blank"
                        class="flex items-center gap-2 text-sm text-unmsm-guinda hover:underline">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9" />
                        </svg>
                        DGEP - Posgrado UNMSM
                    </a>
                </div>
            </div>
        </div>
        </div>

    </section>
@endsection

@push('scripts')
    <script>
        function showTab(tab) {
            document.querySelectorAll('.tab-content').forEach(el => el.classList.remove('active'));
            document.querySelectorAll('.tab-btn').forEach(el => {
                el.classList.remove('active');
                el.classList.add('bg-gray-200', 'text-gray-700');
                el.classList.remove('bg-unmsm-guinda', 'text-white');
            });

            document.getElementById('content-' + tab).classList.add('active');
            const btn = document.getElementById('tab-' + tab);
            btn.classList.add('active');
            btn.classList.remove('bg-gray-200', 'text-gray-700');
        }
    </script>
@endpush
