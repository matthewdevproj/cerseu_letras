@extends('layouts.public')

@section('title', 'Cronograma Académico {{ $cronograma?->code ?? "2026-I" }} - Posgrado Letras UNMSM')

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

        .section-heading td {
            background: linear-gradient(135deg, #761e23 0%, #5a161a 100%);
            color: white;
            font-weight: 700;
            font-size: 0.95rem;
            padding: 0.75rem 1.25rem;
            letter-spacing: 0.025em;
        }

        .docs-card {
            background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
            border: 1px solid #e2e8f0;
            border-left: 4px solid #761e23;
        }

        .docs-card ul {
            list-style: none;
            padding-left: 0;
        }

        .docs-card li a {
            color: #761e23;
            text-decoration: none;
            transition: all 0.2s;
        }

        .docs-card li a:hover {
            color: #d4af37;
            text-decoration: underline;
        }

        .btn-unmsm {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            background: linear-gradient(135deg, #761e23 0%, #5a161a 100%);
            color: white;
            padding: 0.875rem 1.5rem;
            border-radius: 0.5rem;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.3s;
        }

        .btn-unmsm:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(118, 30, 35, 0.3);
        }

        .btn-unmsm-outline {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            background: white;
            color: #761e23;
            border: 2px solid #761e23;
            padding: 0.875rem 1.5rem;
            border-radius: 0.5rem;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.3s;
        }

        .btn-unmsm-outline:hover {
            background: #761e23;
            color: white;
        }

        .nota-fuente {
            background: #f9fafb;
            font-size: 0.875rem;
            color: #6b7280;
            padding: 1rem 1.25rem !important;
        }

        .nota-fuente a {
            color: #761e23;
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
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-unmsm-guinda" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                Documentos Oficiales {{ $cronograma->code }}:
            </p>
            <ul class="space-y-2">
                @foreach($cronograma->documents as $doc)
                <li class="flex items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-unmsm-guinda" fill="none"
                        viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                    </svg>
                    <a href="{{ $doc->url }}" target="_blank">
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
            <div class="bg-unmsm-guinda text-white p-5">
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
        <div class="bg-yellow-50 border border-yellow-200 rounded-xl p-8 text-center mb-8">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 text-yellow-500 mx-auto mb-4" fill="none"
                viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
            </svg>
            <p class="text-yellow-800 font-medium">No hay cronograma disponible actualmente.</p>
            <p class="text-yellow-700 text-sm mt-2">El cronograma será publicado próximamente.</p>
        </div>
        @endif

        <!-- Botones de acción -->
        <div class="grid md:grid-cols-3 gap-4">
            <a href="{{ route('admision') }}" class="btn-unmsm-outline text-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
                </svg>
                Proceso de Admisión
            </a>
            <a href="{{ route('programas.index') }}" class="btn-unmsm text-center">
                Ver Programas
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                </svg>
            </a>
            <a href="{{ route('tramites') }}" class="btn-unmsm-outline text-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                Trámites y Grados
            </a>
        </div>

    </section>
@endsection