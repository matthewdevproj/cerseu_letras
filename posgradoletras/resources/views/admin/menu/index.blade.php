@extends('admin.layout.app')

@section('title', 'Menú de navegación')

@section('content')
    <div class="max-w-6xl mx-auto" x-data="menuNavegacion(@js($inicial))">
        <div class="flex items-start justify-between gap-4 mb-6">
            <div>
                <h1 class="text-2xl font-bold text-gray-800">Menú de navegación</h1>
                <p class="text-sm text-gray-500 mt-1">
                    Lo que se edita aquí sale en la barra superior del sitio, tanto en
                    ordenador como en móvil. Los enlaces que cambian cada convocatoria
                    —cuadro de vacantes, criterios de evaluación— se actualizan desde
                    aquí, sin tocar el código.
                </p>
            </div>
            <button type="submit" form="form-menu"
                class="flex-shrink-0 bg-brand-red text-white px-5 py-2.5 rounded-lg font-semibold hover:opacity-90 transition">
                <x-fas-save class="mr-1" aria-hidden="true" /> Guardar menú
            </button>
        </div>

        @if (session('success'))
            <div class="mb-5 rounded-lg bg-green-50 border border-green-200 px-4 py-3 text-sm text-green-800">
                <x-fas-circle-check class="mr-1" aria-hidden="true" /> {{ session('success') }}
            </div>
        @endif

        {{-- Aviso de caducados: el motivo de que exista este campo es que
             «Criterios de Evaluación» estuvo un año apuntando al documento de
             2025 sin que nadie lo notara. --}}
        <div x-show="caducados.length" x-cloak
            class="mb-5 rounded-lg bg-amber-50 border border-amber-200 px-4 py-3 text-sm text-amber-900">
            <x-fas-triangle-exclamation class="mr-1" aria-hidden="true" />
            <strong>Hay enlaces que ya pasaron de fecha</strong> y se han retirado del sitio:
            <span x-text="caducados.join(', ')"></span>.
            Actualiza su dirección y su fecha, o bórralos.
        </div>

        @if ($errors->any())
            <div class="mb-5 rounded-lg bg-red-50 border border-red-200 px-4 py-3 text-sm text-red-800">
                <ul class="list-disc pl-5 space-y-0.5">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form id="form-menu" method="POST" data-avisar-sin-guardar action="{{ route('admin.menu.update') }}">
            @csrf
            @method('PUT')

            <template x-for="(item, i) in items" :key="item.uid">
                <div class="mb-4 rounded-xl border border-gray-200 bg-white shadow-sm overflow-hidden">
                    {{-- Entrada de primer nivel --}}
                    <div class="flex flex-wrap items-center gap-3 border-b border-gray-100 bg-gray-50 px-4 py-3">
                        <span class="text-xs font-bold uppercase tracking-wider text-gray-400" x-text="'#' + (i + 1)"></span>

                        <input type="text" :name="`items[${i}][etiqueta]`" x-model="item.etiqueta"
                            placeholder="Texto del menú" required maxlength="60"
                            class="flex-1 min-w-[10rem] rounded-lg border-gray-300 text-sm font-semibold focus:border-brand-red focus:ring-brand-red">
                        <input type="hidden" :name="`items[${i}][id]`" :value="item.id">

                        <label class="flex items-center gap-1.5 text-xs text-gray-600">
                            <input type="checkbox" :name="`items[${i}][is_visible]`" x-model="item.is_visible"
                                class="rounded border-gray-300 text-brand-red focus:ring-brand-red"> Visible
                        </label>

                        <div class="flex items-center gap-1">
                            <button type="button" @click="mover(i, -1)" :disabled="i === 0"
                                class="rounded p-2 text-gray-400 hover:bg-gray-200 hover:text-gray-700 disabled:opacity-30"
                                title="Subir"><x-fas-arrow-up aria-hidden="true" /><span class="sr-only">Subir</span></button>
                            <button type="button" @click="mover(i, 1)" :disabled="i === items.length - 1"
                                class="rounded p-2 text-gray-400 hover:bg-gray-200 hover:text-gray-700 disabled:opacity-30"
                                title="Bajar"><x-fas-arrow-down aria-hidden="true" /><span class="sr-only">Bajar</span></button>
                            <button type="button" @click="eliminar(i)"
                                class="rounded p-2 text-gray-400 hover:bg-red-50 hover:text-red-600"
                                title="Eliminar"><x-fas-trash aria-hidden="true" /><span class="sr-only">Eliminar</span></button>
                        </div>
                    </div>

                    <div class="grid gap-3 px-4 py-3 md:grid-cols-4">
                        <label class="block">
                            <span class="mb-1 block text-xs font-semibold text-gray-500">Página del sitio</span>
                            <select :name="`items[${i}][route_name]`" x-model="item.route_name" @change="usarRuta(item)"
                                class="w-full rounded-lg border-gray-300 text-sm focus:border-brand-red focus:ring-brand-red">
                                <option value="">— ninguna —</option>
                                @foreach ($rutas as $ruta)
                                    <option value="{{ $ruta }}">{{ $ruta }}</option>
                                @endforeach
                            </select>
                        </label>

                        <label class="block md:col-span-2">
                            <span class="mb-1 block text-xs font-semibold text-gray-500">…o dirección externa</span>
                            <input type="url" :name="`items[${i}][url]`" x-model="item.url" @input="usarUrl(item)"
                                placeholder="https://…"
                                class="w-full rounded-lg border-gray-300 text-sm focus:border-brand-red focus:ring-brand-red">
                        </label>

                        <label class="block">
                            <span class="mb-1 block text-xs font-semibold text-gray-500">Icono (móvil)</span>
                            <input type="text" :name="`items[${i}][icono]`" x-model="item.icono"
                                placeholder="fas-info-circle"
                                class="w-full rounded-lg border-gray-300 text-sm focus:border-brand-red focus:ring-brand-red">
                        </label>

                        <label class="block">
                            <span class="mb-1 block text-xs font-semibold text-gray-500">Retirar el (opcional)</span>
                            <input type="date" :name="`items[${i}][vigente_hasta]`" x-model="item.vigente_hasta"
                                class="w-full rounded-lg border-gray-300 text-sm focus:border-brand-red focus:ring-brand-red"
                                :class="item.caducado ? 'border-amber-400 bg-amber-50' : ''">
                        </label>

                        <label class="flex items-center gap-1.5 text-xs text-gray-600 md:col-span-3">
                            <input type="checkbox" :name="`items[${i}][nueva_pestana]`" x-model="item.nueva_pestana"
                                class="rounded border-gray-300 text-brand-red focus:ring-brand-red">
                            Abrir en una pestaña nueva
                        </label>
                    </div>

                    {{-- Subentradas --}}
                    <div class="border-t border-gray-100 bg-gray-50/60 px-4 py-3">
                        <p class="mb-2 text-xs font-bold uppercase tracking-wider text-gray-400">Desplegable</p>

                        <template x-for="(hijo, j) in item.hijos" :key="j">
                            <div class="mb-2 flex flex-wrap items-center gap-2 rounded-lg border border-gray-200 bg-white px-3 py-2">
                                <input type="hidden" :name="`items[${i}][hijos][${j}][id]`" :value="hijo.id">
                                <input type="hidden" :name="`items[${i}][hijos][${j}][route_params]`" :value="hijo.route_params">

                                <input type="text" :name="`items[${i}][hijos][${j}][etiqueta]`" x-model="hijo.etiqueta"
                                    placeholder="Texto" required maxlength="60"
                                    class="min-w-[8rem] flex-1 rounded border-gray-300 text-sm focus:border-brand-red focus:ring-brand-red">

                                <select :name="`items[${i}][hijos][${j}][route_name]`" x-model="hijo.route_name" @change="usarRuta(hijo)"
                                    class="rounded border-gray-300 text-xs focus:border-brand-red focus:ring-brand-red">
                                    <option value="">— página —</option>
                                    @foreach ($rutas as $ruta)
                                        <option value="{{ $ruta }}">{{ $ruta }}</option>
                                    @endforeach
                                </select>

                                <input type="url" :name="`items[${i}][hijos][${j}][url]`" x-model="hijo.url" @input="usarUrl(hijo)"
                                    placeholder="https://…"
                                    class="min-w-[10rem] flex-1 rounded border-gray-300 text-xs focus:border-brand-red focus:ring-brand-red">

                                <input type="text" :name="`items[${i}][hijos][${j}][icono]`" x-model="hijo.icono"
                                    placeholder="fas-link"
                                    class="w-32 rounded border-gray-300 text-xs focus:border-brand-red focus:ring-brand-red">

                                <input type="date" :name="`items[${i}][hijos][${j}][vigente_hasta]`" x-model="hijo.vigente_hasta"
                                    title="Retirar el (opcional)"
                                    class="w-36 rounded border-gray-300 text-xs focus:border-brand-red focus:ring-brand-red"
                                    :class="hijo.caducado ? 'border-amber-400 bg-amber-50' : ''">

                                <label class="flex items-center gap-1 text-xs text-gray-500">
                                    <input type="checkbox" :name="`items[${i}][hijos][${j}][nueva_pestana]`" x-model="hijo.nueva_pestana"
                                        class="rounded border-gray-300 text-brand-red focus:ring-brand-red">↗
                                </label>
                                <label class="flex items-center gap-1 text-xs text-gray-500">
                                    <input type="checkbox" :name="`items[${i}][hijos][${j}][is_visible]`" x-model="hijo.is_visible"
                                        class="rounded border-gray-300 text-brand-red focus:ring-brand-red">Visible
                                </label>

                                <button type="button" @click="moverHijo(i, j, -1)" :disabled="j === 0"
                                    class="rounded p-1.5 text-gray-400 hover:bg-gray-100 disabled:opacity-30" title="Subir">
                                    <x-fas-arrow-up class="text-xs" aria-hidden="true" /><span class="sr-only">Subir</span></button>
                                <button type="button" @click="moverHijo(i, j, 1)" :disabled="j === item.hijos.length - 1"
                                    class="rounded p-1.5 text-gray-400 hover:bg-gray-100 disabled:opacity-30" title="Bajar">
                                    <x-fas-arrow-down class="text-xs" aria-hidden="true" /><span class="sr-only">Bajar</span></button>
                                <button type="button" @click="eliminarHijo(i, j)"
                                    class="rounded p-1.5 text-gray-400 hover:bg-red-50 hover:text-red-600" title="Eliminar">
                                    <x-fas-trash class="text-xs" aria-hidden="true" /><span class="sr-only">Eliminar</span></button>
                            </div>
                        </template>

                        <button type="button" @click="agregarHijo(i)"
                            class="text-sm font-semibold text-brand-red hover:underline">
                            <x-fas-plus class="text-xs" aria-hidden="true" /> Añadir subentrada
                        </button>
                    </div>
                </div>
            </template>

            <button type="button" @click="agregar()"
                class="w-full rounded-xl border-2 border-dashed border-gray-300 py-4 font-semibold text-gray-500 transition hover:border-brand-red hover:text-brand-red">
                <x-fas-plus aria-hidden="true" /> Añadir entrada al menú
            </button>
        </form>
    </div>
@endsection
