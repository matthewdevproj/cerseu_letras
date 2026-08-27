@extends('admin.layout.app')

@section('title', 'Cronograma de Admisión')

@push('styles')
    <style>
        .form-label {
            font-weight: 600;
            color: #344767;
            margin-bottom: 0.35rem;
            font-size: 0.7rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .form-control {
            width: 100%;
            border: 1px solid #d1d5db;
            border-radius: 0.5rem;
            padding: 0.5rem 0.75rem;
            font-size: 0.875rem;
        }

        .form-control:focus {
            outline: 2px solid var(--brand);
            outline-offset: 1px;
            border-color: transparent;
        }
    </style>
@endpush

@section('content')
    @php
        // Estado inicial del repetidor: se serializa a JSON para Alpine.
        $pasosIniciales = $cronograma->pasos->map(fn ($p) => [
            'id' => $p->id,
            'titulo' => $p->titulo,
            'fecha_inicio' => $p->fecha_inicio ?? '',
            'fecha_fin' => $p->fecha_fin ?? '',
            'detalle' => $p->detalle ?? '',
            'publico' => $p->publico ?? '',
            'icono' => $p->icono,
            'destacado' => (bool) $p->destacado,
            'is_visible' => (bool) $p->is_visible,
        ])->values();
    @endphp

    <div class="max-w-6xl mx-auto" x-data="cronogramaAdmision(@js($pasosIniciales), @js($cronograma->is_visible), @js(collect($iconos)->map->path))">
        <div class="bg-white rounded-lg shadow-md p-6">
            <h1 class="text-xl font-bold text-gray-800 mb-1">Cronograma de Admisión</h1>
            <p class="text-sm text-gray-500 mb-6">
                Sección <code>Cronograma de Admisión</code> de la portada. Todo su contenido es editable:
                puedes adaptarla a la convocatoria vigente (cursos, talleres u otro periodo),
                agregar, ocultar, eliminar o reordenar etapas, y cambiar el botón principal.
            </p>

            <form action="{{ route('admin.cronograma-admision.update') }}" method="POST" data-avisar-sin-guardar
                x-data="{ submitting: false }" @submit="submitting = true">
                @csrf
                @method('PUT')
                <input type="hidden" name="pasos_payload" :value="JSON.stringify(pasos)">

                {{-- Visibilidad global de la sección --}}
                <div class="rounded-lg border p-4 mb-6 transition-colors"
                    :class="visible ? 'bg-green-50 border-green-200' : 'bg-amber-50 border-amber-200'">
                    <label class="flex items-start gap-3 cursor-pointer">
                        <input type="checkbox" name="is_visible" value="1" x-model="visible"
                            class="mt-1 h-4 w-4 rounded border-gray-300 text-unmsm-azul focus:ring-unmsm-azul">
                        <span>
                            <span class="block font-semibold text-gray-800 text-sm">Mostrar la sección en la portada</span>
                            <span class="block text-xs text-gray-600 mt-0.5"
                                x-text="visible
                                    ? 'La sección está visible para los visitantes.'
                                    : 'La sección está oculta: úsalo cuando no haya una convocatoria activa.'"></span>
                        </span>
                    </label>
                </div>

                {{-- Encabezado de la sección --}}
                <div class="grid md:grid-cols-2 gap-5 mb-8">
                    <div>
                        <label for="eyebrow" class="form-label block">Título superior del proceso</label>
                        <input id="eyebrow" type="text" name="eyebrow" class="form-control"
                            value="{{ old('eyebrow', $cronograma->eyebrow) }}"
                            placeholder="Proceso de Admisión 2026-I">
                        <p class="text-xs text-gray-400 mt-1">Texto pequeño en dorado, sobre el título.</p>
                    </div>
                    <div>
                        <label for="titulo" class="form-label block">Título principal de la sección</label>
                        <input id="titulo" type="text" name="titulo" class="form-control"
                            value="{{ old('titulo', $cronograma->titulo) }}"
                            placeholder="Cronograma de Admisión">
                    </div>
                    <div>
                        <label for="boton_texto" class="form-label block">Texto del botón principal</label>
                        <input id="boton_texto" type="text" name="boton_texto" class="form-control"
                            value="{{ old('boton_texto', $cronograma->boton_texto) }}"
                            placeholder="Iniciar Inscripción">
                        <p class="text-xs text-gray-400 mt-1">Déjalo vacío para no mostrar el botón.</p>
                    </div>
                    <div>
                        <label for="boton_url" class="form-label block">Enlace de redirección del botón</label>
                        <input id="boton_url" type="text" name="boton_url" class="form-control"
                            value="{{ old('boton_url', $cronograma->boton_url) }}"
                            placeholder="https://...  o  /diplomados/admision">
                        <p class="text-xs text-gray-400 mt-1">Formulario o plataforma del proceso vigente.</p>
                    </div>
                </div>

                {{-- Etapas --}}
                <div class="flex flex-wrap items-center justify-between gap-3 mb-3 pt-6 border-t border-gray-100">
                    <div>
                        <h2 class="font-bold text-gray-800">Etapas del cronograma</h2>
                        <p class="text-sm text-gray-500">
                            <span x-text="pasos.length"></span> etapa(s).
                            Usa las flechas para reordenarlas; el orden aquí es el que se ve en la portada.
                        </p>
                    </div>
                    <button type="button" @click="agregar()"
                        class="px-4 py-2 bg-unmsm-azul text-white rounded-lg hover:bg-unmsm-azul-dark text-sm font-medium">
                        <x-fas-plus class="mr-1" /> Agregar etapa
                    </button>
                </div>

                <template x-if="pasos.length === 0">
                    <div class="text-center py-10 border-2 border-dashed border-gray-200 rounded-lg text-gray-400 text-sm">
                        No hay etapas. Agrega la primera con el botón de arriba.
                    </div>
                </template>

                <div class="space-y-4">
                    <template x-for="(paso, index) in pasos" :key="paso.uid">
                        <div class="border rounded-lg p-4 transition-colors"
                            :class="paso.is_visible ? 'border-gray-200 bg-white' : 'border-gray-200 bg-gray-50 opacity-70'">

                            <div class="flex items-start gap-3 mb-4">
                                {{-- Reordenar --}}
                                <div class="flex flex-col items-center gap-1 pt-1">
                                    <button type="button" @click="mover(index, -1)" :disabled="index === 0"
                                        class="text-gray-400 hover:text-unmsm-azul disabled:opacity-30 disabled:hover:text-gray-400"
                                        aria-label="Subir etapa">
                                        <x-fas-chevron-up class="text-xs" />
                                    </button>
                                    <span class="text-xs font-bold text-gray-400" x-text="index + 1"></span>
                                    <button type="button" @click="mover(index, 1)" :disabled="index === pasos.length - 1"
                                        class="text-gray-400 hover:text-unmsm-azul disabled:opacity-30 disabled:hover:text-gray-400"
                                        aria-label="Bajar etapa">
                                        <x-fas-chevron-down class="text-xs" />
                                    </button>
                                </div>

                                {{-- Nombre de la etapa --}}
                                <div class="flex-1">
                                    <label class="form-label block">Nombre de la etapa *</label>
                                    <input type="text" x-model="paso.titulo" class="form-control"
                                        placeholder="Inscripción de postulantes">
                                </div>

                                <button type="button" @click="eliminar(index)"
                                    class="mt-6 text-red-500 hover:text-red-700 px-2" aria-label="Eliminar etapa">
                                    <x-fas-trash />
                                </button>
                            </div>

                            <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-4">
                                <div>
                                    <label class="form-label block">Fecha de inicio</label>
                                    <input type="text" x-model="paso.fecha_inicio" class="form-control"
                                        placeholder="5 ene">
                                </div>
                                <div>
                                    <label class="form-label block">Fecha de cierre</label>
                                    <input type="text" x-model="paso.fecha_fin" class="form-control"
                                        placeholder="02 abr">
                                </div>
                                <div>
                                    <label class="form-label block">Texto complementario</label>
                                    <input type="text" x-model="paso.detalle" class="form-control"
                                        placeholder="+ Envío de expediente">
                                </div>
                                <div>
                                    <label class="form-label block">Curso o público</label>
                                    <input type="text" x-model="paso.publico" class="form-control"
                                        placeholder="Cursos">
                                </div>
                            </div>

                            <div class="grid md:grid-cols-2 gap-4 mt-4 items-end">
                                <div>
                                    <label class="form-label block">Ícono representativo</label>
                                    <div class="flex items-center gap-3">
                                        <select x-model="paso.icono" class="form-control">
                                            @foreach ($iconos as $key => $icono)
                                                <option value="{{ $key }}">{{ $icono['label'] }}</option>
                                            @endforeach
                                        </select>
                                        {{-- Vista previa del ícono seleccionado --}}
                                        <span class="flex-shrink-0 w-10 h-10 rounded-full bg-unmsm-azul/5 text-unmsm-azul flex items-center justify-center">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                                stroke-width="1.5" stroke="currentColor" class="w-5 h-5" aria-hidden="true">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    :d="iconos[paso.icono] || ''" />
                                            </svg>
                                        </span>
                                    </div>
                                </div>

                                <div class="flex flex-wrap gap-5">
                                    <label class="flex items-center gap-2 text-sm text-gray-700 cursor-pointer">
                                        <input type="checkbox" x-model="paso.is_visible"
                                            class="h-4 w-4 rounded border-gray-300 text-unmsm-azul focus:ring-unmsm-azul">
                                        Mostrar esta etapa
                                    </label>
                                    <label class="flex items-center gap-2 text-sm text-gray-700 cursor-pointer">
                                        <input type="checkbox" @change="marcarDestacado(index, $event.target.checked)"
                                            :checked="paso.destacado"
                                            class="h-4 w-4 rounded border-gray-300 text-unmsm-azul focus:ring-unmsm-azul">
                                        Etapa en curso
                                        <span class="text-xs text-gray-400">(se resalta en blanco)</span>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </template>
                </div>

                <div class="flex items-center justify-end gap-3 mt-8 pt-5 border-t border-gray-100">
                    <a href="{{ url('/') }}#admision" target="_blank" rel="noopener noreferrer"
                        class="px-4 py-2 text-sm text-gray-600 hover:text-unmsm-azul">
                        <x-fas-eye class="mr-1" /> Ver en la portada
                    </a>
                    <button type="submit" :disabled="submitting"
                        class="px-6 py-2.5 bg-unmsm-azul text-white rounded-lg hover:bg-unmsm-azul-dark font-medium disabled:opacity-60">
                        <x-fas-save class="mr-1" />
                        <span x-text="submitting ? 'Guardando...' : 'Guardar cambios'"></span>
                    </button>
                </div>
            </form>
        </div>
    </div>

@endsection
