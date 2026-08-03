@extends('admin.layout.app')

@section('title', $anuncio->exists ? 'Editar anuncio' : 'Nuevo anuncio')

@section('content')
    <div class="max-w-2xl mx-auto">
        <h1 class="text-2xl font-bold text-gray-800 mb-1">
            {{ $anuncio->exists ? 'Editar anuncio' : 'Nuevo anuncio' }}
        </h1>
        <p class="text-sm text-gray-500 mb-6">
            Aparece en una ventana emergente al entrar a la portada. Solo se muestra
            una vez por sesión, para no molestar a quien ya lo vio.
        </p>

        @if ($errors->any())
            <div class="mb-5 rounded-lg bg-red-50 border border-red-200 px-4 py-3 text-sm text-red-800">
                <ul class="list-disc pl-5 space-y-0.5">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" data-avisar-sin-guardar
            action="{{ $anuncio->exists ? route('admin.anuncios.update', $anuncio) : route('admin.anuncios.store') }}"
            enctype="multipart/form-data" class="space-y-5 rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
            @csrf
            @if ($anuncio->exists) @method('PUT') @endif

            <div>
                <label for="titulo" class="block text-sm font-medium text-gray-700 mb-1">Nombre interno</label>
                <input id="titulo" type="text" name="titulo" required maxlength="120"
                    value="{{ old('titulo', $anuncio->titulo) }}"
                    placeholder="Convocatoria 2026-I"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-unmsm-guinda focus:border-unmsm-guinda">
                <p class="text-xs text-gray-500 mt-1">Solo para reconocerlo en la lista; no se muestra en el sitio.</p>
            </div>

            <div>
                <label for="imagen" class="block text-sm font-medium text-gray-700 mb-1">
                    Imagen {{ $anuncio->exists ? '(dejar vacío para conservar la actual)' : '' }}
                </label>
                @if ($anuncio->exists && $anuncio->imagen)
                    <img src="{{ $anuncio->imagen_url }}" alt="Imagen actual del anuncio"
                        class="mb-2 h-32 rounded-lg border border-gray-200 object-contain bg-gray-50">
                @endif
                <input id="imagen" type="file" name="imagen" accept="image/jpeg,image/png,image/webp"
                    @if (! $anuncio->exists) required @endif
                    class="w-full text-sm text-gray-600 file:mr-3 file:rounded-lg file:border-0 file:bg-gray-100 file:px-4 file:py-2 file:text-sm file:font-semibold hover:file:bg-gray-200">
                <div class="mt-2 rounded-lg bg-blue-50 border border-blue-200 px-3 py-2 text-xs text-blue-900">
                    <p class="font-bold">
                        <x-fas-circle-info class="mr-1" aria-hidden="true" />
                        Sube la imagen a {{ \App\Models\Anuncio::ANCHO_RECOMENDADO }} × {{ \App\Models\Anuncio::ALTO_RECOMENDADO }} px (proporción 4:5)
                    </p>
                    <p class="mt-1">
                        Es la forma del marco. Una imagen con otra proporción se
                        <strong>recorta</strong> para llenarlo, así que puede perderse
                        parte del cartel. JPG, PNG o WebP, máximo 4 MB.
                    </p>
                </div>

                @if ($anuncio->exists && $anuncio->recorte_notable)
                    <p class="mt-2 rounded-lg bg-amber-50 border border-amber-200 px-3 py-2 text-xs text-amber-900">
                        <x-fas-triangle-exclamation class="mr-1" aria-hidden="true" />
                        La imagen actual mide {{ $anuncio->imagen_ancho }} × {{ $anuncio->imagen_alto }} px:
                        se recorta un <strong>{{ $anuncio->recorte_porcentaje }}%</strong> {{ $anuncio->recorte_lado }}.
                        Súbela a {{ \App\Models\Anuncio::ANCHO_RECOMENDADO }} × {{ \App\Models\Anuncio::ALTO_RECOMENDADO }} px para que se vea completa.
                    </p>
                @endif
            </div>

            <div>
                <label for="alt" class="block text-sm font-medium text-gray-700 mb-1">Texto alternativo</label>
                <input id="alt" type="text" name="alt" maxlength="255" value="{{ old('alt', $anuncio->alt) }}"
                    placeholder="Convocatoria de admisión 2026-I abierta"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-unmsm-guinda focus:border-unmsm-guinda">
                <p class="text-xs text-gray-500 mt-1">
                    Describe la imagen para quien usa lector de pantalla o no puede verla.
                    Si lo dejas vacío se usa el nombre interno.
                </p>
            </div>

            <div class="grid gap-4 sm:grid-cols-2">
                <div>
                    <label for="link" class="block text-sm font-medium text-gray-700 mb-1">Enlace (opcional)</label>
                    <input id="link" type="url" name="link" maxlength="500" value="{{ old('link', $anuncio->link) }}"
                        placeholder="https://…"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-unmsm-guinda focus:border-unmsm-guinda">
                </div>
                <div>
                    <label for="link_texto" class="block text-sm font-medium text-gray-700 mb-1">Texto del botón</label>
                    <input id="link_texto" type="text" name="link_texto" maxlength="60"
                        value="{{ old('link_texto', $anuncio->link_texto) }}" placeholder="Ver convocatoria"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-unmsm-guinda focus:border-unmsm-guinda">
                </div>
            </div>

            <div class="grid gap-4 sm:grid-cols-2">
                <div>
                    <label for="visible_desde" class="block text-sm font-medium text-gray-700 mb-1">Mostrar desde</label>
                    <input id="visible_desde" type="date" name="visible_desde"
                        value="{{ old('visible_desde', $anuncio->visible_desde?->format('Y-m-d')) }}"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-unmsm-guinda focus:border-unmsm-guinda">
                </div>
                <div>
                    <label for="visible_hasta" class="block text-sm font-medium text-gray-700 mb-1">Retirar el</label>
                    <input id="visible_hasta" type="date" name="visible_hasta"
                        value="{{ old('visible_hasta', $anuncio->visible_hasta?->format('Y-m-d')) }}"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-unmsm-guinda focus:border-unmsm-guinda">
                </div>
            </div>
            <p class="-mt-2 text-xs text-gray-500">
                Ambas fechas son opcionales. Con una fecha de retirada el anuncio
                desaparece solo, sin que nadie tenga que acordarse.
            </p>

            <div class="grid gap-4 sm:grid-cols-2 sm:items-center">
                <div>
                    <label for="orden" class="block text-sm font-medium text-gray-700 mb-1">Orden</label>
                    <input id="orden" type="number" name="orden" min="0" max="999"
                        value="{{ old('orden', $anuncio->orden ?? 0) }}"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-unmsm-guinda focus:border-unmsm-guinda">
                    <p class="text-xs text-gray-500 mt-1">Con varios anuncios, el menor va primero.</p>
                </div>
                <label class="flex items-center gap-2 text-sm text-gray-700 sm:mt-5">
                    <input type="checkbox" name="is_visible" value="1"
                        @checked(old('is_visible', $anuncio->is_visible ?? true))
                        class="rounded border-gray-300 text-brand-red focus:ring-brand-red">
                    Visible
                </label>
            </div>

            <div class="flex items-center justify-end gap-3 border-t border-gray-100 pt-5">
                <a data-salir-sin-guardar href="{{ route('admin.anuncios.index') }}" class="px-4 py-2 text-sm text-gray-600 hover:text-red-700">Cancelar</a>
                <button type="submit" class="bg-brand-red text-white px-6 py-2.5 rounded-lg font-semibold hover:opacity-90 transition">
                    {{ $anuncio->exists ? 'Guardar cambios' : 'Crear anuncio' }}
                </button>
            </div>
        </form>
    </div>
@endsection
