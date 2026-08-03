@extends('admin.layout.app')

@section('title', 'Contenido · ' . ($pagina->titulo ?? $pagina->slug))

@section('content')
    @php
        $inicial = $pagina->secciones->map(fn ($s) => [
            'id' => $s->id,
            'grupo' => $s->grupo,
            'numeral' => $s->numeral ?? '',
            'titulo' => $s->titulo,
            'cuerpo' => $s->cuerpo ?? '',
            'is_visible' => (bool) $s->is_visible,
        ])->values();
    @endphp

    <div class="max-w-5xl mx-auto" x-data="editorContenido(@js($inicial), @js(array_key_first($grupos) ?: null))">
        <div class="bg-white rounded-lg shadow-md p-6">

            <div class="flex flex-wrap items-start justify-between gap-3 mb-1">
                <h1 class="text-xl font-bold text-gray-800">Contenido · {{ \App\Models\ContentPage::PAGINAS[$pagina->slug] }}</h1>
                <a href="{{ url('/' . $pagina->slug) }}" target="_blank" rel="noopener noreferrer"
                    class="text-sm text-gray-600 hover:text-red-700">
                    <x-fas-eye class="mr-1" aria-hidden="true" /> Ver la página
                </a>
            </div>
            <p class="text-sm text-gray-500 mb-6">
                Se edita el texto; la maqueta de la página no cambia.
            </p>

            <form method="POST" data-avisar-sin-guardar action="{{ route('admin.contenido.update', $pagina->slug) }}"
                x-data="{ submitting: false }" @submit="submitting = true">
                @csrf
                @method('PUT')

                <div class="grid md:grid-cols-2 gap-5 mb-8">
                    <div>
                        <label for="titulo" class="block text-xs font-bold uppercase tracking-wider text-gray-500 mb-1">Título</label>
                        <input id="titulo" type="text" name="titulo" value="{{ old('titulo', $pagina->titulo) }}"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
                    </div>
                    <div>
                        <label for="subtitulo" class="block text-xs font-bold uppercase tracking-wider text-gray-500 mb-1">Subtítulo</label>
                        <input id="subtitulo" type="text" name="subtitulo" value="{{ old('subtitulo', $pagina->subtitulo) }}"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
                    </div>
                </div>

                {{-- Ayuda de tokens --}}
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6 text-sm text-blue-900">
                    <p class="font-semibold mb-2">
                        <x-fas-circle-info class="mr-1" aria-hidden="true" /> Datos de contacto
                    </p>
                    <p class="mb-2 text-blue-800">
                        No escribas correos ni teléfonos directamente: usa estas etiquetas y se rellenarán
                        con lo que haya en <strong>Configuración</strong>, para que no queden desactualizados.
                    </p>
                    <div class="flex flex-wrap gap-2">
                        @foreach ($tokens as $token => $desc)
                            <code class="px-2 py-1 bg-white border border-blue-200 rounded text-xs">{{ $token }}</code>
                        @endforeach
                    </div>
                </div>

                <div class="flex flex-wrap items-center justify-between gap-3 mb-3 pt-5 border-t border-gray-100">
                    <div>
                        <h2 class="font-bold text-gray-800">Secciones</h2>
                        <p class="text-sm text-gray-500"><span x-text="secciones.length"></span> en total · el orden es el de la página</p>
                    </div>
                    <button type="button" @click="agregar()"
                        class="px-4 py-2 bg-red-700 text-white rounded-lg hover:bg-red-800 text-sm font-medium">
                        <x-fas-plus class="mr-1" aria-hidden="true" /> Agregar sección
                    </button>
                </div>

                <div class="space-y-4">
                    <template x-for="(s, index) in secciones" :key="s.uid">
                        <div class="border rounded-lg transition-colors"
                            :class="s.is_visible ? 'border-gray-200 bg-white' : 'border-gray-200 bg-gray-50 opacity-70'">

                            <div class="flex items-start gap-3 p-4 border-b border-gray-100">
                                <div class="flex flex-col items-center gap-1 pt-1">
                                    <button type="button" @click="mover(index, -1)" :disabled="index === 0"
                                        class="text-gray-400 hover:text-red-700 disabled:opacity-30" aria-label="Subir">
                                        <x-fas-chevron-up class="text-xs" />
                                    </button>
                                    <span class="text-xs font-bold text-gray-400" x-text="index + 1"></span>
                                    <button type="button" @click="mover(index, 1)" :disabled="index === secciones.length - 1"
                                        class="text-gray-400 hover:text-red-700 disabled:opacity-30" aria-label="Bajar">
                                        <x-fas-chevron-down class="text-xs" />
                                    </button>
                                </div>

                                <div class="flex-1 grid sm:grid-cols-12 gap-3">
                                    <input type="hidden" :name="`secciones[${index}][id]`" :value="s.id ?? ''">

                                    <div class="sm:col-span-2">
                                        <label class="block text-[11px] font-bold uppercase text-gray-500 mb-1">N.º</label>
                                        <input type="text" :name="`secciones[${index}][numeral]`" x-model="s.numeral"
                                            placeholder="I" class="w-full border border-gray-300 rounded-lg px-2 py-2 text-sm">
                                    </div>

                                    <div class="{{ $grupos ? 'sm:col-span-6' : 'sm:col-span-10' }}">
                                        <label class="block text-[11px] font-bold uppercase text-gray-500 mb-1">Título *</label>
                                        <input type="text" :name="`secciones[${index}][titulo]`" x-model="s.titulo"
                                            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
                                    </div>


                                    @if ($grupos)
                                        <div class="sm:col-span-4">
                                            <label class="block text-[11px] font-bold uppercase text-gray-500 mb-1">Pestaña</label>
                                            <select :name="`secciones[${index}][grupo]`" x-model="s.grupo"
                                                class="w-full border border-gray-300 rounded-lg px-2 py-2 text-sm">
                                                @foreach ($grupos as $valor => $etiqueta)
                                                    <option value="{{ $valor }}">{{ $etiqueta }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    @endif
                                </div>

                                <div class="flex flex-col items-end gap-2 pt-5">
                                    <label class="flex items-center gap-1.5 text-xs text-gray-600 cursor-pointer whitespace-nowrap">
                                        <input type="checkbox" :name="`secciones[${index}][is_visible]`" value="1"
                                            x-model="s.is_visible"
                                            class="h-4 w-4 rounded border-gray-300 text-red-700 focus:ring-red-700">
                                        Visible
                                    </label>
                                    <button type="button" @click="eliminar(index)"
                                        class="text-red-500 hover:text-red-700" aria-label="Eliminar sección">
                                        <x-fas-trash />
                                    </button>
                                </div>
                            </div>

                            <div class="p-4">
                                <label class="block text-[11px] font-bold uppercase text-gray-500 mb-1">Contenido</label>
                                {{-- El editor con formato se monta encima de este campo
                                     (resources/js/editor-texto.js). Sin JavaScript queda
                                     el textarea de siempre y se puede seguir trabajando. --}}
                                <textarea data-editor-texto :name="`secciones[${index}][cuerpo]`" x-model="s.cuerpo" rows="10"
                                    class="w-full border border-gray-300 rounded-lg px-3 py-2 text-xs font-mono leading-relaxed"></textarea>
                            </div>
                        </div>
                    </template>
                </div>

                <div class="flex items-center justify-end gap-3 mt-8 pt-5 border-t border-gray-100">
                    <a data-salir-sin-guardar href="{{ route('admin.contenido.index') }}" class="px-4 py-2 text-sm text-gray-600 hover:text-red-700">Volver</a>
                    <button type="submit" :disabled="submitting"
                        class="px-6 py-2.5 bg-red-700 text-white rounded-lg hover:bg-red-800 font-medium disabled:opacity-60">
                        <x-fas-save class="mr-1" aria-hidden="true" />
                        <span x-text="submitting ? 'Guardando...' : 'Guardar cambios'"></span>
                    </button>
                </div>
            </form>
        </div>
    </div>

@endsection
