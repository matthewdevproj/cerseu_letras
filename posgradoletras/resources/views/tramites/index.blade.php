@extends('layouts.public')

@section('title', 'Trámites - Obtención de Grado - Posgrado Letras UNMSM')

{{-- ========================================================
CORREOS DE CONTACTO - MODIFICAR AQUÍ O EN config/contacts.php
======================================================== --}}
@php
    // Correo para Trámites (Grados, Títulos, Certificados)
    $emailTramites = config('contacts.tramites', 'upg.letras@unmsm.edu.pe');

    // Teléfono / WhatsApp
    $telefono = config('contacts.telefono', '982 085 037');
    $whatsapp = config('contacts.whatsapp', 'https://wa.me/51982085037');
@endphp

@push('styles')
    <style>
        .prose ul {
            list-style-type: disc;
            padding-left: 1.5rem;
            margin-bottom: 1rem;
        }

        .prose ol {
            list-style-type: decimal;
            padding-left: 1.5rem;
            margin-bottom: 1rem;
        }

        .prose li {
            margin-bottom: 0.4rem;
            color: #374151;
        }
    </style>
@endpush

@section('content')

    <!-- HERO DE SECCIÓN -->
    <x-hero-section title="Obtención de Grado" label="Trámites Académicos"
        image="https://letras.unmsm.edu.pe/wp-content/uploads/2025/12/DJI_0007-Trim-frame-at-0m5s.jpg" />

    <!-- LAYOUT SIDEBAR + CONTENIDO -->
    <div class="container mx-auto px-4 py-12" x-data="{ currentTab: 'maestria' }">
        <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">

            <!-- SIDEBAR: NAVEGACIÓN -->
            <aside class="lg:col-span-1">
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-2 lg:sticky lg:top-28">
                    <nav class="grid grid-cols-2 lg:flex lg:flex-col gap-2 lg:gap-1">
                        <button @click="currentTab = 'maestria'"
                            :class="{ 'bg-unmsm-guinda text-white shadow-md': currentTab === 'maestria', 'text-gray-600 hover:bg-gray-50': currentTab !== 'maestria' }"
                            class="flex items-center justify-center lg:justify-start gap-2 lg:gap-3 px-3 lg:px-4 py-3 rounded-lg text-xs lg:text-sm font-bold transition-all w-full text-center lg:text-left">
                            <x-fas-graduation-cap class="text-base lg:text-lg" x-bind:class="{ 'text-unmsm-dorado': currentTab === 'maestria', 'text-gray-400': currentTab !== 'maestria' }" />
                            Grado de Magíster
                        </button>

                        <button @click="currentTab = 'doctorado'"
                            :class="{ 'bg-gray-900 text-white shadow-md': currentTab === 'doctorado', 'text-gray-600 hover:bg-gray-50': currentTab !== 'doctorado' }"
                            class="flex items-center justify-center lg:justify-start gap-2 lg:gap-3 px-3 lg:px-4 py-3 rounded-lg text-xs lg:text-sm font-bold transition-all w-full text-center lg:text-left">
                            <x-fas-user-graduate class="text-base lg:text-lg" x-bind:class="{ 'text-white': currentTab === 'doctorado', 'text-gray-400': currentTab !== 'doctorado' }" />
                            Grado de Doctor
                        </button>
                    </nav>

                    <div class="hidden lg:block mt-6 p-4 bg-gray-50 rounded-lg border border-gray-100">
                        <h4 class="text-xs font-bold text-gray-400 uppercase mb-2">Informes</h4>
                        <div class="space-y-3 text-sm">
                            <a href="mailto:{{ $emailTramites }}"
                                class="flex items-center gap-2 text-gray-700 hover:text-unmsm-guinda">
                                <x-far-envelope class="text-unmsm-guinda" />
                                <span class="truncate">{{ $emailTramites }}</span>
                            </a>
                            <a href="{{ $whatsapp }}" target="_blank" rel="noopener noreferrer" 
                                class="flex items-center gap-2 text-gray-700 hover:text-green-600">
                                <x-fab-whatsapp class="text-green-500 text-lg" />
                                <span>{{ $telefono }}</span>
                            </a>
                        </div>
                    </div>

                    <!-- Recursos Rápidos -->
                    <div class="hidden lg:block mt-4 p-4 bg-unmsm-guinda/5 rounded-lg border border-unmsm-guinda/10">
                        <h4 class="text-xs font-bold text-unmsm-guinda uppercase mb-3">Documentos</h4>
                        <div class="space-y-2 text-xs">
                            {{-- <a href="https://letras.unmsm.edu.pe/wp-content/uploads/2022/06/Plantilla-oficial-de-Proyecto-de-Tesis.pdf"
                                target="_blank" rel="noopener noreferrer" 
                                class="flex items-center gap-2 text-gray-600 hover:text-unmsm-guinda transition">
                                <x-fas-file-pdf class="text-red-500" />
                                Plantilla Proyecto
                            </a> --}}
                            <a href="https://letras.unmsm.edu.pe/wp-content/uploads/2022/03/Directiva-de-Estrctura-de-tesis-Maestria-y-DoctoradoFFFFFFFFFF-1-1.pdf"
                                target="_blank" rel="noopener noreferrer" 
                                class="flex items-center gap-2 text-gray-600 hover:text-unmsm-guinda transition">
                                <x-fas-file-pdf class="text-red-500" />
                                Estructura de Tesis
                            </a>
                            <a href="https://sanmarket.unmsm.edu.pe/" target="_blank" rel="noopener noreferrer" 
                                class="flex items-center gap-2 text-gray-600 hover:text-unmsm-guinda transition">
                                <x-fas-credit-card class="text-green-600" />
                                SanMarket (Pagos)
                            </a>
                        </div>
                    </div>
                </div>
            </aside>

            <!-- CONTENIDO PRINCIPAL -->
            <section class="lg:col-span-3 min-h-[500px]">

                <!-- ======================= PESTAÑA MAESTRÍA ======================= -->
                <div x-show="currentTab === 'maestria'" x-transition:enter="transition ease-out duration-300"
                    x-transition:enter-start="opacity-0 translate-y-4" x-transition:enter-end="opacity-100 translate-y-0"
                    class="space-y-8 fade-in">

                    <div class="border-b pb-4 mb-6">
                        <h2 class="text-2xl font-serif font-bold text-unmsm-guinda">
                            Requisitos para la Obtención del Grado de Magíster
                        </h2>
                        <p class="text-gray-500 text-sm mt-1">
                            Proceso completo: inscripción de proyecto, declaración de expedito, sustentación y trámite de
                            diploma.
                        </p>
                    </div>

                    <!-- PASO I - MAGÍSTER -->
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                        <div class="bg-gray-50 px-6 py-4 border-b border-gray-200 flex items-center gap-3">
                            <div
                                class="w-8 h-8 rounded-full bg-unmsm-guinda text-white flex items-center justify-center font-bold text-sm">
                                I
                            </div>
                            <h3 class="font-bold text-gray-800">
                                Inscripción de proyecto de tesis y nombramiento de asesor
                            </h3>
                        </div>
                        <div class="p-6 prose prose-sm max-w-none text-gray-600">
                            <p>
                                El postulante debe presentar <strong>Solicitud en FUT (Formato Único de Trámite)</strong>
                                pidiendo la inscripción de proyecto de tesis y nombramiento de asesor, enviando en
                                <strong>formato PDF</strong> al correo
                                <strong>{{ $emailTramites }}</strong> lo siguiente:
                            </p>
                            <ul class="marker:text-unmsm-guinda">
                                <li>
                                    <strong>1.1. Proyecto de tesis:</strong>
                                    respetando la plantilla oficial de proyecto:
                                    <a href="https://letras.unmsm.edu.pe/wp-content/uploads/2026/05/GUIA-DE-PRESENTACION-DE-PROYECTO-DE-TESIS_2026FFF.pdf"
                                        target="_blank" rel="noopener noreferrer" class="text-unmsm-guinda underline hover:text-unmsm-dorado">
                                        Plantilla oficial de proyecto de tesis
                                    </a>.
                                </li>
                                <li>
                                    <strong>1.2. Carta simple del asesor</strong> aceptando la asesoría.
                                </li>
                            </ul>

                            <div class="bg-unmsm-guinda/5 border-l-4 border-unmsm-dorado p-4 mt-4 not-prose rounded">
                                <p class="text-xs text-unmsm-guinda font-medium mb-2">
                                    <x-fas-info-circle class="mr-1" />
                                    Indicaciones importantes:
                                </p>
                                <ul class="text-xs text-gray-700 list-disc pl-4 space-y-1">
                                    <li>El nombre del proyecto debe ser el mismo para la tesis, hasta su sustentación.</li>
                                    <li>
                                        Si hubiera algún cambio, se deberá comunicar a la UPG para realizar el
                                        trámite de cambio de nombre, antes de la fecha de revisión por el programa
                                        de antiplagio <strong>Turnitin</strong>.
                                    </li>
                                    <li>
                                        Adjuntar <strong>copia de DNI</strong> y <strong>partida de nacimiento</strong>.
                                        De acuerdo con los requisitos de la SUNEDU, ambos documentos deben tener
                                        correctamente colocadas las tildes en nombres y apellidos. Se devuelve el
                                        expediente cuya partida no coincida con el DNI.
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <!-- PASO II - MAGÍSTER -->
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                        <div class="bg-gray-50 px-6 py-4 border-b border-gray-200 flex items-center gap-3">
                            <div
                                class="w-8 h-8 rounded-full bg-unmsm-guinda text-white flex items-center justify-center font-bold text-sm">
                                II
                            </div>
                            <h3 class="font-bold text-gray-800">Declaración de expedito</h3>
                        </div>
                        <div class="p-6 text-gray-600 text-sm space-y-4">
                            <p class="font-semibold text-unmsm-guinda">
                                Requisitos académicos previos:
                            </p>
                            <ul class="list-disc list-inside space-y-1">
                                <li>Haber concluido su plan de estudios con una nota promedio de <strong>14
                                        (catorce)</strong> en escala vigesimal.</li>
                                <li>
                                    Tener concluida la tesis respetando la
                                    <strong><a
                                            href="https://letras.unmsm.edu.pe/wp-content/uploads/2026/05/Directiva-de-Estrctura-de-tesis-Maestria-y-DoctoradoFFFFFFFFFF-2.pdf"
                                            target="_blank" rel="noopener noreferrer" class="text-unmsm-guinda underline hover:text-unmsm-dorado">DIRECTIVA DE
                                            MODELO DE ESTRUCTURA DE TESIS</a></strong> – Programas de maestría y
                                    doctorado
                                    (DICTAMEN N° 000002-2022-UPG-VDIP-FLCH/UNMSM):
                                    <a href="https://letras.unmsm.edu.pe/wp-content/uploads/2026/05/Directiva-de-Estrctura-de-tesis-Maestria-y-DoctoradoFFFFFFFFFF-2.pdf"
                                        target="_blank" rel="noopener noreferrer" class="text-unmsm-guinda hover:underline">
                                        Ver directiva
                                    </a>
                                    |
                                    <a href="https://letras.unmsm.edu.pe/wp-content/uploads/2026/05/Modelo-de-caratula_biblioteca-1.pdf"
                                        target="_blank" rel="noopener noreferrer" class="text-unmsm-guinda hover:underline">
                                        Modelo de Carátula de Tesis
                                    </a>
                                </li>
                                <li>La tesis debe respetar el <strong>protocolo de presentación de tesis (R.D. N.º
                                        283-D-FLCH-19)</strong>.</li>
                                <li>Contar con <strong>informe final del asesor con firma</strong>.</li>
                            </ul>

                            <div class="bg-unmsm-guinda/5 border-l-4 border-unmsm-dorado p-4 rounded">
                                <p class="text-xs text-unmsm-guinda">
                                    El nombre de la tesis debe mantenerse igual hasta la sustentación. En caso de cambio,
                                    se debe comunicar a la UPG antes de la revisión por Turnitin.
                                </p>
                            </div>

                            <p class="mt-4">
                                El alumno debe presentar <strong>Solicitud en FUT</strong> pidiendo se le declare expedito,
                                enviando el expediente completo en formato PDF al correo
                                <strong>{{ $emailTramites }}</strong>, con los siguientes documentos:
                            </p>

                            <ul class="list-decimal pl-5 space-y-1">
                                <li>Copia del grado de <strong>bachiller</strong>.</li>
                                <li>Certificado de estudios (promedio ponderado 14).</li>
                                <li>
                                    Constancia original que acredite el dominio de <strong>un idioma</strong>, expedida
                                    por la FLCH – Oficina de suficiencia de idiomas (vigencia: 3 años).
                                </li>
                                <li>
                                    <strong>Pago de tasas a la Facultad para obtención del grado de Magíster</strong>
                                    (TUPA 2008, R.R. N.º 01545-R-08); los pagos se realizan a través de
                                    <a href="https://sanmarket.unmsm.edu.pe/#/" target="_blank" rel="noopener noreferrer" 
                                        class="text-unmsm-guinda underline hover:text-unmsm-dorado">
                                        SanMarket-UNMSM
                                    </a>.
                                    <span class="font-semibold block mt-1">
                                        Cada pago debe realizarse por separado y generar su respectiva boleta de venta.
                                    </span>
                                </li>
                            </ul>

                            <!-- TABLAS DE PAGOS MAGÍSTER -->
                            <div class="space-y-5 mt-4">
                                <!-- 5.4.1 Trámite de Grado -->
                                <div>
                                    <p class="font-semibold mb-2">5.4.1. Trámite de Grado de Magíster</p>
                                    <div class="overflow-x-auto border border-gray-200 rounded-lg">
                                        <table class="min-w-full divide-y divide-gray-200">
                                            <thead class="bg-unmsm-guinda">
                                                <tr>
                                                    <th
                                                        class="px-6 py-3 text-center text-xs font-bold text-white uppercase tracking-wider">
                                                        Concepto
                                                    </th>
                                                    <th
                                                        class="px-6 py-3 text-center text-xs font-bold text-white uppercase tracking-wider">
                                                        Monto
                                                    </th>
                                                    <th
                                                        class="px-6 py-3 text-center text-xs font-bold text-white uppercase tracking-wider">
                                                        Unidad
                                                    </th>
                                                </tr>
                                            </thead>
                                            <tbody class="bg-white divide-y divide-gray-200">
                                                <tr>
                                                    <td class="px-6 py-4 text-center text-sm">
                                                        Grado de Magíster
                                                    </td>
                                                    <td class="px-6 py-4 text-center font-bold text-unmsm-guinda">
                                                        S/ 1,555.00
                                                    </td>
                                                    <td class="px-6 py-4 text-center text-sm">
                                                        <p>Facultad de Letras y Ciencias Humanas</p>
                                                        <p>Unidad de Posgrado</p>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>

                                <!-- 5.4.2 Monitoreo -->
                                <div>
                                    <p class="font-semibold mb-2">
                                        5.4.2. Pago por monitoreo de investigación, avances y ejecución de tesis – Tarifario
                                        Descentralizado (R.D. N.º 1204-D-FLCH-17)
                                    </p>
                                    <div class="overflow-x-auto border border-gray-200 rounded-lg">
                                        <table class="min-w-full divide-y divide-gray-200">
                                            <thead class="bg-unmsm-guinda">
                                                <tr>
                                                    <th
                                                        class="px-6 py-3 text-center text-xs font-bold text-white uppercase tracking-wider">
                                                        Concepto
                                                    </th>
                                                    <th
                                                        class="px-6 py-3 text-center text-xs font-bold text-white uppercase tracking-wider">
                                                        Monto
                                                    </th>
                                                    <th
                                                        class="px-6 py-3 text-center text-xs font-bold text-white uppercase tracking-wider">
                                                        Unidad
                                                    </th>
                                                </tr>
                                            </thead>
                                            <tbody class="bg-white divide-y divide-gray-200">
                                                <tr>
                                                    <td class="px-6 py-4 text-center text-sm">
                                                        Pago por monitoreo de investigación, avances y ejecución de tesis
                                                    </td>
                                                    <td class="px-6 py-4 text-center font-bold text-unmsm-guinda">
                                                        S/ 1,500.00
                                                    </td>
                                                    <td class="px-6 py-4 text-center text-sm">
                                                        Tarifario Descentralizado
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>

                                <!-- 5.4.3 Expedición Diploma -->
                                <div>
                                    <p class="font-semibold mb-2">
                                        5.4.3. Expedición de Diplomas de Grado de Magíster – Sede Central (R.R. N.º
                                        01545-R-08)
                                    </p>
                                    <div class="overflow-x-auto border border-gray-200 rounded-lg">
                                        <table class="min-w-full divide-y divide-gray-200">
                                            <thead class="bg-unmsm-guinda">
                                                <tr>
                                                    <th
                                                        class="px-6 py-3 text-center text-xs font-bold text-white uppercase tracking-wider">
                                                        Concepto
                                                    </th>
                                                    <th
                                                        class="px-6 py-3 text-center text-xs font-bold text-white uppercase tracking-wider">
                                                        Monto
                                                    </th>
                                                    <th
                                                        class="px-6 py-3 text-center text-xs font-bold text-white uppercase tracking-wider">
                                                        Unidad
                                                    </th>
                                                </tr>
                                            </thead>
                                            <tbody class="bg-white divide-y divide-gray-200">
                                                <tr>
                                                    <td class="px-6 py-4 text-center text-sm">
                                                        Expedición de Diploma de Grado Académico de Magíster
                                                    </td>
                                                    <td class="px-6 py-4 text-center font-bold text-unmsm-guinda">
                                                        S/ 700.00
                                                    </td>
                                                    <td class="px-6 py-4 text-center text-sm">
                                                        Oficina de Secretaría General
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>

                                <!-- TOTAL (opcional visual) -->
                                <div class="bg-unmsm-guinda/5 border border-unmsm-guinda/20 rounded-lg px-6 py-3">
                                    <p class="text-sm font-semibold text-gray-800">
                                        <span class="mr-2">Total referencial de tasas:</span>
                                        <span class="text-unmsm-guinda text-base font-bold">S/ 3,755.00</span>
                                    </p>
                                    <p class="text-[11px] text-gray-500 mt-1 italic">
                                        Los pagos y montos se rigen por las resoluciones y tarifarios vigentes de la UNMSM.
                                    </p>
                                </div>
                            </div>

                            <!-- Artículo científico y documentos -->
                            <div class="mt-6 space-y-2">
                                <p>
                                    <strong>5.</strong> Acreditar un <strong>artículo científico</strong> que deberá ser
                                    parte de la
                                    tesis (ingresantes desde 2009), publicado o aceptado para su publicación en revista
                                    indexada
                                    en las bases: <strong>Latindex 2.0, Scopus, SciELO, Web of Science</strong> (adjuntar el
                                    artículo).
                                </p>
                                <p><strong>6.</strong> Partida de nacimiento: copia simple.</p>
                                <p>
                                    <strong>7.</strong> Copia del DNI.
                                    <span class="block text-xs text-gray-700 mt-1 font-semibold">
                                        Nota: En estos dos documentos deben figurar las tildes en nombres y apellidos por
                                        igual,
                                        la SUNEDU no expenderá el diploma si no están correctamente puestas las tildes.
                                    </span>
                                </p>
                            </div>

                            <!-- Turnitin -->
                            <div class="bg-amber-50 border-l-4 border-amber-400 p-4 mt-4 rounded">
                                <h4 class="text-sm font-bold text-amber-800 mb-2">
                                    <x-fas-shield-alt class="mr-1" />
                                    Revisión antiplagio (Turnitin)
                                </h4>
                                <p class="text-xs text-amber-800 mb-2">
                                    El graduando debe enviar la versión digital de la tesis al correo
                                    <strong>{{ $emailTramites }}</strong> para revisión en Turnitin, considerando:
                                </p>
                                <ul class="text-xs text-amber-900 list-disc pl-4 space-y-1">
                                    <li>El nombre de la tesis debe ser igual al inscrito en el proyecto de tesis.</li>
                                    <li>Indicar nombres y apellidos del asesor y su correo institucional.</li>
                                    <li>A partir de los ingresantes 2016 debe hacerse desde su correo institucional y
                                        personal.</li>
                                </ul>
                                <p class="text-xs text-amber-900 mt-2">
                                    La aceptación de similitud para la UNMSM es de <strong>-20 o 20 %</strong>. De no ser
                                    esa
                                    la calificación, deberá revisarse la tesis. No se aceptan tesis con observaciones.
                                </p>
                            </div>

                            <!-- Foto y Declaración -->
                            <div class="bg-gray-50 rounded-lg p-4 mt-4 border border-gray-100">
                                <h4 class="text-sm font-semibold text-gray-800 mb-2">
                                    <x-fas-camera class="mr-1 text-unmsm-dorado" />
                                    Documentos adicionales:
                                </h4>
                                <ul class="text-xs text-gray-700 space-y-1">
                                    <li>Adjuntar nuevamente copia del DNI.</li>
                                    <li>
                                        <strong>Foto tamaño pasaporte:</strong> foto digital de buena resolución, fondo
                                        blanco,
                                        no retocada, no borrosa ni anaranjada, de hombros hacia arriba (no medio cuerpo).
                                        Caballeros: saco negro y corbata. Damas: blusa y saco negro.
                                    </li>
                                    <li>
                                        <strong>Declaración Jurada</strong> de veracidad documentaria y de no adeudar dinero
                                        ni libros
                                        (solicitar los formatos a la UPG).
                                    </li>
                                </ul>
                            </div>

                            <p class="text-xs text-gray-600 mt-4">
                                Toda la documentación debe enviarse en formato PDF al correo
                                <strong>{{ $emailTramites }}</strong>. Con la confirmación de similitud y la
                                documentación
                                completa se procede a <strong>declarar expedito</strong> al graduando.
                            </p>
                        </div>
                    </div>

                    <!-- PASO III - MAGÍSTER -->
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                        <div class="bg-gray-50 px-6 py-4 border-b border-gray-200 flex items-center gap-3">
                            <div
                                class="w-8 h-8 rounded-full bg-unmsm-guinda text-white flex items-center justify-center font-bold text-sm">
                                III
                            </div>
                            <h3 class="font-bold text-gray-800">Sustentación pública de la tesis</h3>
                        </div>
                        <div class="p-6 text-gray-600 text-sm">
                            <ul class="space-y-3">
                                <li class="flex gap-3">
                                    <x-fas-check-circle class="text-green-500 mt-1" />
                                    <span>
                                        Una vez declarado expedito, el tesista solicita en FUT se le nombre
                                        <strong>Jurado Informante de Tesis</strong>, adjuntando la tesis en formato PDF
                                        al correo <strong>{{ $emailTramites }}</strong>.
                                    </span>
                                </li>
                                <li class="flex gap-3">
                                    <x-fas-check-circle class="text-green-500 mt-1" />
                                    <span>
                                        Si en los informes hay observaciones a la tesis, el graduando deberá levantarlas.
                                    </span>
                                </li>
                                <li class="flex gap-3">
                                    <x-fas-check-circle class="text-green-500 mt-1" />
                                    <span>
                                        Con el informe y el levantamiento de observaciones, de ser el caso, se procede a
                                        solicitar <strong>Jurado Examinador</strong>, así como fecha y hora para la
                                        sustentación.
                                    </span>
                                </li>
                                <li class="flex gap-3">
                                    <x-fas-check-circle class="text-green-500 mt-1" />
                                    <span>
                                        La determinación de la fecha de sustentación se comunica por correo electrónico
                                        desde <strong>{{ $emailTramites }}</strong> con una anticipación de
                                        <strong>48 horas</strong>.
                                    </span>
                                </li>
                                <li class="flex gap-3">
                                    <x-fas-check-circle class="text-green-500 mt-1" />
                                    <span>
                                        La sustentación pública de la tesis será <strong>televisada</strong>.
                                    </span>
                                </li>
                            </ul>
                        </div>
                    </div>

                    <!-- PASO IV - MAGÍSTER -->
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                        <div class="bg-gray-50 px-6 py-4 border-b border-gray-200 flex items-center gap-3">
                            <div
                                class="w-8 h-8 rounded-full bg-unmsm-guinda text-white flex items-center justify-center font-bold text-sm">
                                IV
                            </div>
                            <h3 class="font-bold text-gray-800">Trámite del Diploma de Grado</h3>
                        </div>
                        <div class="p-6 text-gray-600 text-sm space-y-4">
                            <p>
                                El expediente de Grado Académico es aprobado por el <strong>Consejo de Facultad</strong>.
                                Previamente, se deben adjuntar en PDF los siguientes archivos:
                            </p>
                            <ul class="list-disc pl-5 space-y-1">
                                <li>Autorización para publicación en <strong>Cybertesis</strong> (llenar formato).</li>
                                <li>Hoja de metadatos (llenar formato).</li>
                                <li>Tesis final.</li>
                                <li>Informe de originalidad (<strong>Turnitin</strong>).</li>
                                <li>Acta de sustentación.</li>
                            </ul>

                            <p class="text-xs text-gray-600">
                                Este trámite lo realiza la Unidad de Posgrado ante la Biblioteca Central.
                                Los formatos de Autorización y Hoja de metadatos deben solicitarse al correo
                                <strong>{{ $emailTramites }}</strong>, y luego remitirse debidamente llenos al mismo
                                correo.
                            </p>

                            <ul class="space-y-2 text-sm mt-2">
                                <li>
                                    <span class="font-bold text-unmsm-guinda">2.</span>
                                    Con la aprobación del Consejo de Facultad se emite la
                                    <strong>Resolución de Decanato</strong> que otorga el Grado Académico de Magíster. A
                                    partir
                                    de la fecha de expedición, todo trámite corresponde a la Sede Central.
                                </li>
                                <li>
                                    <span class="font-bold text-unmsm-guinda">3.</span>
                                    Aprobado con <strong>Resolución Rectoral</strong>, el expediente regresa a la Unidad de
                                    Posgrado
                                    para iniciar el trámite de <strong>Expedición de Diploma de Grado Académico de
                                        Magíster</strong>.
                                </li>
                            </ul>

                            <div class="bg-gray-50 rounded-lg p-4 border border-gray-100">
                                <p class="text-sm font-semibold text-gray-800 mb-2">
                                    Revisión de expedientes para la expedición del diploma:
                                </p>
                                <ul class="list-disc pl-5 text-xs space-y-1">
                                    <li>
                                        Verificar que el recibo de pago por
                                        <strong>Expedición de Diplomas de Grado de Magíster</strong> se encuentre
                                        incluido en el expediente.
                                    </li>
                                    <li>
                                        Foto tamaño pasaporte, fondo blanco, en papel fotográfico
                                        (mujeres: saco color negro y blusa blanca; varones: terno color negro).
                                    </li>
                                    <li>Copia simple del DNI.</li>
                                </ul>
                            </div>

                            <ul class="space-y-1 text-sm mt-2">
                                <li>
                                    <span class="font-bold text-unmsm-guinda">4.</span>
                                    La entrega del diploma es <strong>virtual</strong>.
                                </li>
                                <li>
                                    <span class="font-bold text-unmsm-guinda">5.</span>
                                    La inscripción en la <strong>SUNEDU</strong> la realiza la
                                    <strong>Secretaría General</strong> de la universidad.
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- ======================= PESTAÑA DOCTORADO ======================= -->
                <div x-show="currentTab === 'doctorado'" x-transition:enter="transition ease-out duration-300"
                    x-transition:enter-start="opacity-0 translate-y-4" x-transition:enter-end="opacity-100 translate-y-0"
                    class="space-y-8 fade-in" style="display: none;">

                    <div class="border-b pb-4 mb-6">
                        <h2 class="text-2xl font-serif font-bold text-gray-900">
                            Requisitos para la Obtención del Grado de Doctor
                        </h2>
                        <p class="text-gray-500 text-sm mt-1">
                            Proceso académico para la obtención del máximo grado: Doctor.
                        </p>
                    </div>

                    <!-- PASO I DOCTORADO -->
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                        <div class="bg-gray-50 px-6 py-4 border-b border-gray-200 flex items-center gap-3">
                            <h3 class="font-bold text-gray-800">
                                Examen de Suficiencia Doctoral
                            </h3>
                        </div>
                        <div class="p-6 prose prose-sm max-w-none text-gray-600">
                            <p>
                                El Examen de Suficiencia Doctoral es una evaluación académica mediante la cual el estudiante
                                demuestra los conocimientos y competencias necesarios para obtener la condición de
                                Candidato(a) a Doctor(a).
                                <br><br>
                                La evaluación comprende un ensayo académico y la defensa del proyecto de tesis doctoral ante
                                un jurado especializad
                                <br>

                            <ul>
                                <li>
                                    <a href="https://letras.unmsm.edu.pe/wp-content/uploads/2026/05/Examen-de-doctorado_Fn-1FFF-1-1.pdf"
                                        target="_blank" rel="noopener noreferrer" class="text-unmsm-guinda underline">
                                        Ver documento del Examen de Suficiencia Doctoral
                                    </a>
                                </li>
                            </ul>
                            </p>
                        </div>
                    </div>

                    <!-- PASO I DOCTORADO -->
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                        <div class="bg-gray-50 px-6 py-4 border-b border-gray-200 flex items-center gap-3">
                            <div
                                class="w-8 h-8 rounded-full bg-gray-900 text-white flex items-center justify-center font-bold text-sm">
                                I
                            </div>
                            <h3 class="font-bold text-gray-800">
                                Inscripción de proyecto de tesis y nombramiento de asesor
                            </h3>
                        </div>
                        <div class="p-6 prose prose-sm max-w-none text-gray-600">
                            <p>
                                El recurrente debe presentar <strong>Solicitud en FUT</strong> pidiendo inscripción de
                                proyecto
                                de tesis y nombramiento de asesor, enviando en formato PDF al correo
                                <strong>{{ $emailTramites }}</strong> lo siguiente:
                            </p>
                            <ul>
                                <li>
                                    <strong>1.1. Proyecto:</strong> respetando la plantilla oficial:
                                    <a href="https://letras.unmsm.edu.pe/wp-content/uploads/2026/05/GUIA-DE-PRESENTACION-DE-PROYECTO-DE-TESIS_2026FFF.pdf"
                                        target="_blank" rel="noopener noreferrer" class="text-unmsm-guinda underline">
                                        Plantilla oficial de proyecto de tesis
                                    </a>.
                                </li>
                                <li>
                                    <strong>1.2. Carta simple del asesor</strong> aceptando la asesoría.
                                </li>
                            </ul>
                            <div class="bg-unmsm-guinda/5 border-l-4 border-unmsm-dorado p-4 mt-4 not-prose rounded">
                                <p class="text-xs text-unmsm-guinda font-medium mb-2">
                                    <x-fas-info-circle class="mr-1" />
                                    Consideraciones:
                                </p>
                                <ul class="list-disc text-xs text-gray-700 pl-4 space-y-1">
                                    <li>El nombre del proyecto debe ser el mismo para la tesis hasta su sustentación.</li>
                                    <li>Si hubiera algún cambio, debe comunicarse a la UPG para el trámite de cambio de
                                        nombre antes de la revisión por Turnitin.</li>
                                    <li>
                                        Adjuntar copia de DNI y partida de nacimiento. De acuerdo con los requisitos de la
                                        SUNEDU,
                                        ambos documentos deben tener correctamente colocadas las tildes en nombres y
                                        apellidos.
                                        Se devuelve el expediente cuya partida no coincida con el DNI.
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <!-- PASO II DOCTORADO -->
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                        <div class="bg-gray-50 px-6 py-4 border-b border-gray-200 flex items-center gap-3">
                            <div
                                class="w-8 h-8 rounded-full bg-gray-900 text-white flex items-center justify-center font-bold text-sm">
                                II
                            </div>
                            <h3 class="font-bold text-gray-800">Declaración de expedito</h3>
                        </div>
                        <div class="p-6 text-gray-600 text-sm space-y-4">
                            <p class="font-semibold text-gray-800">
                                Requisitos académicos previos:
                            </p>
                            <ul class="list-disc list-inside space-y-1">
                                <li>Haber concluido su plan de estudios con una nota promedio de <strong>14
                                        (catorce)</strong> en escala vigesimal.</li>
                                <li>
                                    Tener concluida la tesis, respetando la
                                    <strong><a
                                            href="https://letras.unmsm.edu.pe/wp-content/uploads/2026/05/Directiva-de-Estrctura-de-tesis-Maestria-y-DoctoradoFFFFFFFFFF-2.pdf"
                                            target="_blank" rel="noopener noreferrer" class="text-unmsm-guinda underline hover:text-unmsm-dorado">DIRECTIVA
                                            DE MODELO DE ESTRUCTURA DE TESIS</a> </strong> – Programas de maestría y
                                    doctorado
                                    (DICTAMEN N° 000002-2022-UPG-VDIP-FLCH/UNMSM):
                                    <a href="https://letras.unmsm.edu.pe/wp-content/uploads/2026/05/Directiva-de-Estrctura-de-tesis-Maestria-y-DoctoradoFFFFFFFFFF-2.pdf"
                                        target="_blank" rel="noopener noreferrer" class="text-unmsm-guinda hover:underline">
                                        Ver directiva
                                    </a>
                                    |
                                    <a href="https://letras.unmsm.edu.pe/wp-content/uploads/2026/05/Modelo-de-caratula_biblioteca-1.pdf"
                                        target="_blank" rel="noopener noreferrer" class="text-unmsm-guinda hover:underline">
                                        Modelo de Carátula de Tesis
                                    </a>
                                </li>
                                <li>
                                    La tesis debe respetar el protocolo de presentación de tesis
                                    <strong>(R.D. N.º 283-D-FLCH-19)</strong>.
                                </li>
                                <li>Contar con el <strong>informe final del asesor</strong> con firma.</li>
                            </ul>

                            <p>
                                El alumno presenta <strong>Solicitud en FUT</strong> pidiendo se le declare expedito y envía
                                el expediente completo en PDF al correo electrónico
                                <strong>{{ $emailTramites }}</strong>, incluyendo:
                            </p>

                            <ul class="list-decimal pl-5 space-y-1">
                                <li>Copia del grado de <strong>Magíster</strong>.</li>
                                <li>Certificado de estudios (promedio ponderado 14).</li>
                                <li>
                                    Constancia original que acredite el dominio de <strong>dos idiomas</strong>, expedida
                                    por la FLCH – Oficina de suficiencia de idiomas (vigencia: 3 años).
                                </li>
                                <li>
                                    <strong>Pago de tasas a la Facultad para obtención del grado de Doctor</strong>
                                    (vía <a href="https://sanmarket.unmsm.edu.pe/#/" target="_blank" rel="noopener noreferrer" 
                                        class="text-unmsm-guinda underline hover:text-unmsm-dorado">SanMarket-UNMSM</a>),
                                    cada pago por separado con su respectiva boleta:
                                </li>
                            </ul>

                            <!-- TABLAS DOCTORADO -->
                            <div class="space-y-5 mt-4">
                                <!-- 5.4.1 Trámite de Grado de Doctor -->
                                <div>
                                    <p class="font-semibold mb-2">5.4.1. Trámite de Grado de Doctor</p>
                                    <div class="overflow-x-auto border border-gray-200 rounded-lg">
                                        <table class="min-w-full divide-y divide-gray-200">
                                            <thead class="bg-gray-900">
                                                <tr>
                                                    <th
                                                        class="px-6 py-3 text-center text-xs font-bold text-white uppercase tracking-wider">
                                                        Concepto
                                                    </th>
                                                    <th
                                                        class="px-6 py-3 text-center text-xs font-bold text-white uppercase tracking-wider">
                                                        Monto
                                                    </th>
                                                    <th
                                                        class="px-6 py-3 text-center text-xs font-bold text-white uppercase tracking-wider">
                                                        Unidad
                                                    </th>
                                                </tr>
                                            </thead>
                                            <tbody class="bg-white divide-y divide-gray-200">
                                                <tr>
                                                    <td class="px-6 py-4 text-center text-sm">
                                                        Grado de Doctor
                                                    </td>
                                                    <td class="px-6 py-4 text-center font-bold text-gray-900">
                                                        S/ 2,995.00
                                                    </td>
                                                    <td class="px-6 py-4 text-center text-sm">
                                                        <p>Facultad de Letras y Ciencias Humanas</p>
                                                        <p>Unidad de Posgrado</p>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>

                                <!-- 5.4.2 Monitoreo Doctorado -->
                                <div>
                                    <p class="font-semibold mb-2">
                                        5.4.2. Pago por monitoreo de investigación, avances y ejecución de tesis – Tarifario
                                        Descentralizado (R.D. N.º 1204-D-FLCH-17)
                                    </p>
                                    <div class="overflow-x-auto border border-gray-200 rounded-lg">
                                        <table class="min-w-full divide-y divide-gray-200">
                                            <thead class="bg-gray-900">
                                                <tr>
                                                    <th
                                                        class="px-6 py-3 text-center text-xs font-bold text-white uppercase tracking-wider">
                                                        Concepto
                                                    </th>
                                                    <th
                                                        class="px-6 py-3 text-center text-xs font-bold text-white uppercase tracking-wider">
                                                        Monto
                                                    </th>
                                                    <th
                                                        class="px-6 py-3 text-center text-xs font-bold text-white uppercase tracking-wider">
                                                        Unidad
                                                    </th>
                                                </tr>
                                            </thead>
                                            <tbody class="bg-white divide-y divide-gray-200">
                                                <tr>
                                                    <td class="px-6 py-4 text-center text-sm">
                                                        Pago por monitoreo de investigación, avances y ejecución de tesis
                                                    </td>
                                                    <td class="px-6 py-4 text-center font-bold text-gray-900">
                                                        S/ 2,000.00
                                                    </td>
                                                    <td class="px-6 py-4 text-center text-sm">
                                                        Tarifario Descentralizado
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>

                                <!-- 5.4.3 Expedición Diploma Doctor -->
                                <div>
                                    <p class="font-semibold mb-2">
                                        5.4.3. Expedición de Diplomas de Grado de Doctor – Sede Central (R.R. N.º
                                        01545-R-08)
                                    </p>
                                    <div class="overflow-x-auto border border-gray-200 rounded-lg">
                                        <table class="min-w-full divide-y divide-gray-200">
                                            <thead class="bg-gray-900">
                                                <tr>
                                                    <th
                                                        class="px-6 py-3 text-center text-xs font-bold text-white uppercase tracking-wider">
                                                        Concepto
                                                    </th>
                                                    <th
                                                        class="px-6 py-3 text-center text-xs font-bold text-white uppercase tracking-wider">
                                                        Monto
                                                    </th>
                                                    <th
                                                        class="px-6 py-3 text-center text-xs font-bold text-white uppercase tracking-wider">
                                                        Unidad
                                                    </th>
                                                </tr>
                                            </thead>
                                            <tbody class="bg-white divide-y divide-gray-200">
                                                <tr>
                                                    <td class="px-6 py-4 text-center text-sm">
                                                        Expedición de Diploma de Grado Académico de Doctor
                                                    </td>
                                                    <td class="px-6 py-4 text-center font-bold text-gray-900">
                                                        S/ 1,200.00
                                                    </td>
                                                    <td class="px-6 py-4 text-center text-sm">
                                                        Oficina de Secretaría General
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>

                            <div class="bg-gray-50 rounded-lg p-4 border border-gray-100 mt-4">
                                <p class="text-sm">
                                    <strong>6.</strong> Acreditar un artículo científico que deberá ser parte de la tesis
                                    (ingresantes desde 2009), publicado o aceptado para su publicación en revista indexada
                                    en:
                                    <strong>Latindex 2.0, Scopus, SciELO, Web of Science</strong> (adjuntar el artículo).
                                </p>
                                <p class="text-sm mt-2"><strong>7.</strong> Partida de nacimiento: copia simple.</p>
                                <p class="text-sm mt-1">
                                    <strong>8.</strong> Copia del DNI.
                                    <span class="block text-xs font-semibold mt-1">
                                        Nota: En ambos documentos deben figurar las tildes en nombres y apellidos por igual;
                                        la SUNEDU no expenderá el diploma si no están correctamente puestas.
                                    </span>
                                </p>
                            </div>

                            <!-- Turnitin Doctorado -->
                            <div class="bg-amber-50 border-l-4 border-amber-400 p-4 mt-4 rounded">
                                <h4 class="text-sm font-bold text-amber-800 mb-2">
                                    <x-fas-shield-alt class="mr-1" />
                                    Revisión Turnitin
                                </h4>
                                <p class="text-xs text-amber-800 mb-2">
                                    El graduando debe enviar la versión digital de la tesis al correo
                                    <strong>{{ $emailTramites }}</strong> para revisión por el programa de antiplagio
                                    Turnitin,
                                    considerando:
                                </p>
                                <ul class="text-xs text-amber-900 list-disc pl-4 space-y-1">
                                    <li>El nombre de la tesis debe ser igual al inscrito en el proyecto de tesis.</li>
                                    <li>Indicar los nombres y apellidos completos del asesor y su correo institucional.</li>
                                    <li>
                                        A partir de los ingresantes 2016 el envío debe realizarse desde el correo
                                        institucional
                                        y personal del graduando.
                                    </li>
                                </ul>
                                <p class="text-xs text-amber-900 mt-2">
                                    La aceptación de similitud para la UNMSM es de <strong>-20 o 20 %</strong>. De no ser
                                    esa la
                                    calificación, deberá revisarse la tesis. No se aceptan tesis con observaciones.
                                </p>
                            </div>

                            <!-- Documentos adicionales doctorado -->
                            <div class="bg-gray-50 rounded-lg p-4 mt-4 border border-gray-100">
                                <h4 class="text-sm font-semibold text-gray-800 mb-2">
                                    Documentos adicionales:
                                </h4>
                                <ul class="text-xs text-gray-700 space-y-1">
                                    <li>Adjuntar nuevamente copia de DNI.</li>
                                    <li>
                                        Foto tamaño pasaporte: foto digital de buena resolución, fondo blanco, no retocada,
                                        no
                                        borrosa ni anaranjada, de hombros hacia arriba. Caballeros: saco negro y corbata.
                                        Damas: blusa y saco negro.
                                    </li>
                                    <li>
                                        Declaración Jurada de veracidad documentaria y de no adeudar dinero ni libros
                                        (solicitar los formatos a la UPG).
                                    </li>
                                </ul>
                                <p class="text-xs text-gray-600 mt-3">
                                    Enviar toda la documentación en formato PDF al correo
                                    <strong>{{ $emailTramites }}</strong>. Con la confirmación de similitud y la
                                    documentación completa se procede a <strong>declarar expedito</strong>.
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- PASO III DOCTORADO -->
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                        <div class="bg-gray-50 px-6 py-4 border-b border-gray-200 flex items-center gap-3">
                            <div
                                class="w-8 h-8 rounded-full bg-gray-900 text-white flex items-center justify-center font-bold text-sm">
                                III
                            </div>
                            <h3 class="font-bold text-gray-800">Sustentación pública de la tesis</h3>
                        </div>
                        <div class="p-6 text-gray-600 text-sm">
                            <ul class="space-y-3">
                                <li class="flex gap-3">
                                    <x-fas-check-circle class="text-green-500 mt-1" />
                                    <span>
                                        Una vez declarado expedito, el tesista solicita en FUT se le nombre
                                        <strong>Jurado Informante de Tesis</strong>, adjuntando la tesis en formato PDF
                                        al correo <strong>{{ $emailTramites }}</strong>.
                                    </span>
                                </li>
                                <li class="flex gap-3">
                                    <x-fas-check-circle class="text-green-500 mt-1" />
                                    <span>
                                        Si en los informes hay observaciones a la tesis, el graduando deberá levantarlas.
                                    </span>
                                </li>
                                <li class="flex gap-3">
                                    <x-fas-check-circle class="text-green-500 mt-1" />
                                    <span>
                                        Con el informe y el levantamiento de observaciones, de ser el caso, se procede a
                                        solicitar <strong>Jurado Examinador</strong>, fecha y hora para la sustentación de
                                        la tesis.
                                    </span>
                                </li>
                                <li class="flex gap-3">
                                    <x-fas-check-circle class="text-green-500 mt-1" />
                                    <span>
                                        La fecha de sustentación se comunica por correo electrónico desde
                                        <strong>{{ $emailTramites }}</strong> de la Unidad de Posgrado de la Facultad,
                                        con <strong>48 horas</strong> de anticipación.
                                    </span>
                                </li>
                                <li class="flex gap-3">
                                    <x-fas-check-circle class="text-green-500 mt-1" />
                                    <span>
                                        La sustentación pública de la tesis será <strong>televisada</strong>.
                                    </span>
                                </li>
                            </ul>
                        </div>
                    </div>

                    <!-- PASO IV DOCTORADO -->
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                        <div class="bg-gray-50 px-6 py-4 border-b border-gray-200 flex items-center gap-3">
                            <div
                                class="w-8 h-8 rounded-full bg-gray-900 text-white flex items-center justify-center font-bold text-sm">
                                IV
                            </div>
                            <h3 class="font-bold text-gray-800">Trámite del Diploma de Grado</h3>
                        </div>
                        <div class="p-6 text-gray-600 text-sm space-y-4">
                            <p>
                                El expediente de Grado Académico de Doctor es aprobado por el <strong>Consejo de
                                    Facultad</strong>.
                                Previamente, se deben adjuntar los siguientes archivos en formato PDF:
                            </p>
                            <ul class="list-disc pl-5 space-y-1">
                                <li>Autorización para publicación en <strong>Cybertesis</strong> (llenar formato).</li>
                                <li>Hoja de metadatos (llenar formato).</li>
                                <li>Tesis final.</li>
                                <li>Informe de originalidad (Turnitin).</li>
                                <li>Acta de sustentación.</li>
                            </ul>

                            <p class="text-xs text-gray-600">
                                Este trámite lo realiza la Unidad de Posgrado ante la Biblioteca Central.
                                Los formatos de Autorización y Hoja de metadatos deben solicitarse al correo
                                <strong>{{ $emailTramites }}</strong> y retornarse debidamente llenos al mismo correo.
                            </p>

                            <ul class="space-y-2 text-sm mt-2">
                                <li>
                                    <span class="font-bold text-gray-900">2.</span>
                                    Con la aprobación del Consejo de Facultad se emite la
                                    <strong>Resolución de Decanato</strong> que otorga el Grado Académico de Doctor; a
                                    partir
                                    de la fecha de expedición, todo trámite corresponde a la Sede Central.
                                </li>
                                <li>
                                    <span class="font-bold text-gray-900">3.</span>
                                    Aprobado con <strong>Resolución Rectoral</strong>, el expediente regresa a la Unidad de
                                    Posgrado
                                    para iniciar el trámite de <strong>Expedición de Diploma de Grado Académico de
                                        Doctor</strong>.
                                </li>
                            </ul>

                            <div class="bg-gray-50 rounded-lg p-4 border border-gray-100">
                                <p class="text-sm font-semibold text-gray-800 mb-2">
                                    Para la expedición del diploma de Doctor se debe:
                                </p>
                                <ul class="list-disc pl-5 text-xs space-y-1">
                                    <li>
                                        Verificar que el recibo de pago por
                                        <strong>Expedición de Diplomas de Grado de Doctor</strong> esté incluido en el
                                        expediente.
                                    </li>
                                    <li>
                                        Foto tamaño pasaporte, fondo blanco, en papel fotográfico
                                        (mujeres: saco negro y blusa blanca; varones: terno color negro).
                                    </li>
                                    <li>Copia simple del DNI.</li>
                                </ul>
                            </div>

                            <ul class="space-y-1 text-sm mt-2">
                                <li>
                                    <span class="font-bold text-gray-900">4.</span>
                                    El expediente es enviado a la <strong>Oficina de Matrícula</strong> de la Facultad para
                                    el trámite del diploma.
                                </li>
                                <li>
                                    <span class="font-bold text-gray-900">5.</span>
                                    La entrega del diploma es <strong>virtual</strong>.
                                </li>
                                <li>
                                    <span class="font-bold text-gray-900">6.</span>
                                    La inscripción en la <strong>SUNEDU</strong> la realiza la <strong>Secretaría
                                        General</strong>
                                    de la universidad.
                                </li>
                            </ul>
                        </div>
                    </div>

                </div>

            </section>

            <!-- INFORMACIÓN Y RECURSOS (SOLO MÓVIL) -->
            <div class="lg:hidden space-y-4 mt-8">
                <div class="p-4 bg-gray-50 rounded-lg border border-gray-100">
                    <h4 class="text-xs font-bold text-gray-400 uppercase mb-2">Informes</h4>
                    <div class="space-y-3 text-sm">
                        <a href="mailto:{{ $emailTramites }}"
                            class="flex items-center gap-2 text-gray-700 hover:text-unmsm-guinda">
                            <x-far-envelope class="text-unmsm-guinda" />
                            <span class="truncate">{{ $emailTramites }}</span>
                        </a>
                        <a href="{{ $whatsapp }}" target="_blank" rel="noopener noreferrer" 
                            class="flex items-center gap-2 text-gray-700 hover:text-green-600">
                            <x-fab-whatsapp class="text-green-500 text-lg" />
                            <span>{{ $telefono }}</span>
                        </a>
                    </div>
                </div>

                <div class="p-4 bg-unmsm-guinda/5 rounded-lg border border-unmsm-guinda/10">
                    <h4 class="text-xs font-bold text-unmsm-guinda uppercase mb-3">Documentos Rápidos</h4>
                    <div class="space-y-2 text-xs">
                        {{-- <a href="https://letras.unmsm.edu.pe/wp-content/uploads/2022/06/Plantilla-oficial-de-Proyecto-de-Tesis.pdf"
                            target="_blank" rel="noopener noreferrer" 
                            class="flex items-center gap-2 text-gray-600 hover:text-unmsm-guinda transition">
                            <x-fas-file-pdf class="text-red-500" />
                            Plantilla Proyecto
                        </a> --}}
                        <a href="https://letras.unmsm.edu.pe/wp-content/uploads/2022/03/Directiva-de-Estrctura-de-tesis-Maestria-y-DoctoradoFFFFFFFFFF-1-1.pdf"
                            target="_blank" rel="noopener noreferrer" 
                            class="flex items-center gap-2 text-gray-600 hover:text-unmsm-guinda transition">
                            <x-fas-file-pdf class="text-red-500" />
                            Estructura de Tesis
                        </a>
                        <a href="https://sanmarket.unmsm.edu.pe/" target="_blank" rel="noopener noreferrer" 
                            class="flex items-center gap-2 text-gray-600 hover:text-unmsm-guinda transition">
                            <x-fas-credit-card class="text-green-600" />
                            SanMarket (Pagos)
                        </a>
                    </div>
                </div>
            </div>

        </div>
    </div>

@endsection
