@extends('layouts.public')

@php
    $cronogramaCode = $cronograma?->code ?? '2026-I';
@endphp

@section('title', "Cronograma Académico {$cronogramaCode} - CERSEU Letras UNMSM")

@push('styles')
    <style>
        .cronograma-table {
            width: 100%;
            border-collapse: collapse;
        }

        .cronograma-table th,
        .cronograma-table td {
            padding: 0.875rem 1.25rem;
            text-align: left;
            border-bottom: 1px solid #e5e7eb;
        }

        .cronograma-table th {
            background: #f3f4f6;
            font-weight: 600;
            color: #374151;
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        .cronograma-table tbody tr:hover {
            background: #fef3f2;
        }

        /* Mismo rojo institucional que el resto de la página (unmsm-azul,
           #143B63), para no mezclar dos tonos de rojo en un mismo archivo. */
        .section-heading td {
            background: #143B63;
            color: white;
            font-weight: 700;
            font-size: 0.95rem;
            padding: 0.75rem 1.25rem;
            letter-spacing: 0.025em;
        }

        .docs-card {
            background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
            border: 1px solid #e2e8f0;
            border-left: 4px solid #143B63;
        }

        .docs-card ul {
            list-style: none;
            padding-left: 0;
        }

        .docs-card li a {
            color: #143B63;
            text-decoration: none;
            transition: all 0.2s;
        }

        .docs-card li a:hover {
            color: #B6A350;
            text-decoration: underline;
        }

        .nota-fuente {
            background: #f9fafb;
            font-size: 0.875rem;
            color: #6b7280;
            padding: 1rem 1.25rem !important;
        }

        .nota-fuente a {
            color: #143B63;
            font-weight: 500;
        }
    </style>
@endpush

@section('content')
    <!-- HERO DE SECCIÓN -->
    <x-hero-section title="Cronograma Académico" label="Semestre {{ $cronograma?->code ?? '2026-I' }}"
        subtitle="{{ $cronograma?->description ?? 'Fechas importantes del proceso de admisión, matrícula y actividades académicas' }}"
        image="https://letras.unmsm.edu.pe/wp-content/uploads/2025/12/IMG_1565-scaled.jpg" />

    <section class="container mx-auto px-6 py-12">

        @if($cronograma && $cronograma->documents->count() > 0)
        <!-- Documentos -->
        <div class="docs-card rounded-xl p-6 mb-8 shadow-sm">
            <p class="font-bold text-gray-800 mb-3 flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-unmsm-azul" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                Documentos Oficiales {{ $cronograma->code }}:
            </p>
            <ul class="space-y-2">
                @foreach($cronograma->documents as $doc)
                <li class="flex items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-unmsm-azul" fill="none"
                        viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                    </svg>
                    <a href="{{ $doc->url }}" target="_blank" rel="noopener noreferrer">
                        {{ $doc->display_title }}
                    </a>
                </li>
                @endforeach
            </ul>
        </div>
        @endif

        @if($cronograma && $cronograma->items->count() > 0)
        <!-- Cronograma Principal -->
        <div class="bg-white border border-gray-200 rounded-xl overflow-hidden shadow-lg mb-8">
            <div class="bg-unmsm-azul text-white p-5">
                <h2 class="font-bold text-xl font-serif flex items-center gap-3">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                    {{ $cronograma->title }}
                </h2>
            </div>

            <div class="overflow-x-auto">
                <table class="cronograma-table">
                    <thead>
                        <tr>
                            <th style="width: 65%;">Actividad</th>
                            <th style="width: 35%;">Fecha</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($cronograma->items as $item)
                            @if($item->is_section_heading)
                                <tr class="section-heading">
                                    <td colspan="2">
                                        <span class="flex items-center gap-2">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none"
                                                viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M9 5l7 7-7 7" />
                                            </svg>
                                            {{ $item->actividad }}
                                        </span>
                                    </td>
                                </tr>
                            @else
                                <tr>
                                    <td class="font-medium text-gray-800">{{ $item->actividad }}</td>
                                    <td class="text-gray-600">{{ $item->fecha_text }}</td>
                                </tr>
                            @endif
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @else
        <div class="bg-white border border-gray-200 rounded-xl mb-8 shadow-sm">
            <x-empty-state icon="fa-calendar-days" title="No hay cronograma disponible actualmente"
                description="El cronograma será publicado próximamente." />
        </div>
        @endif

        <!-- Botones de acción -->
        <div class="grid md:grid-cols-3 gap-4">
            <x-button href="{{ route('admision') }}" variant="outline" size="lg" icon="fas fa-user-plus">
                Proceso de Admisión
            </x-button>
            <x-button href="{{ route('cursos.index') }}" size="lg">
                Ver Cursos <x-fas-arrow-right aria-hidden="true" />
            </x-button>
            <x-button href="{{ route('tramites') }}" variant="outline" size="lg" icon="fas fa-file-alt">
                Trámites
            </x-button>
        </div>

    </section>
@endsection