@extends('admin.layout.app')

@section('title', 'Anuncios de la portada')

@section('content')
    <div class="max-w-5xl mx-auto">
        <div class="flex flex-wrap items-start justify-between gap-4 mb-6">
            <div>
                <h1 class="text-2xl font-bold text-gray-800">Anuncios de la portada</h1>
                <p class="text-sm text-gray-500 mt-1">
                    Se muestran en una ventana emergente <strong>solo al entrar a la portada</strong>,
                    no en el resto de páginas. Si no hay ninguno vigente, no aparece nada.
                </p>
            </div>
            <div class="flex items-center gap-2">
                @if ($enPapelera)
                    <a href="{{ route('admin.anuncios.papelera') }}"
                        class="px-4 py-2.5 rounded-lg border border-gray-300 text-sm font-semibold text-gray-600 hover:bg-gray-50">
                        <x-fas-trash-arrow-up class="mr-1" aria-hidden="true" /> Papelera ({{ $enPapelera }})
                    </a>
                @endif
                <a href="{{ url('/') }}?previsualizar_anuncios=1" target="_blank" rel="noopener noreferrer"
                    class="px-4 py-2.5 rounded-lg border border-gray-300 text-sm font-semibold text-gray-600 hover:bg-gray-50">
                    <x-fas-eye class="mr-1" aria-hidden="true" /> Ver cómo queda
                </a>
                <a href="{{ route('admin.anuncios.create') }}"
                    class="bg-brand-azul text-white px-5 py-2.5 rounded-lg font-semibold hover:opacity-90 transition">
                    <x-fas-plus class="mr-1" aria-hidden="true" /> Nuevo anuncio
                </a>
            </div>
        </div>

        @if (session('success'))
            <div class="mb-5 rounded-lg bg-green-50 border border-green-200 px-4 py-3 text-sm text-green-800">
                <x-fas-circle-check class="mr-1" aria-hidden="true" /> {{ session('success') }}
            </div>
        @endif


        {{-- Ajustes de comportamiento: viven junto a los anuncios, que es donde
             se piensa en ellos, en vez de perdidos en Configuración. --}}
        <form method="POST" action="{{ route('admin.anuncios.ajustes') }}" data-avisar-sin-guardar
            class="mb-6 rounded-xl border border-gray-200 bg-white p-5 shadow-sm">
            @csrf

            <h2 class="mb-1 text-sm font-bold uppercase tracking-wider text-gray-500">Cómo se muestra</h2>
            <p class="mb-4 text-xs text-gray-500">Se aplica a todos los anuncios.</p>

            <div class="grid gap-4 sm:grid-cols-3">
                <label class="block">
                    <span class="mb-1 block text-xs font-semibold text-gray-600">Aparece a los…</span>
                    <div class="flex items-center gap-2">
                        <input type="number" name="popup_retardo_ms" min="0" max="20000" step="100"
                            value="{{ old('popup_retardo_ms', $ajustes->popup_retardo_ms ?? 1200) }}"
                            class="w-full rounded-lg border-gray-300 text-sm focus:border-brand-azul focus:ring-brand-azul">
                        <span class="text-xs text-gray-500">ms</span>
                    </div>
                    <span class="mt-1 block text-xs text-gray-500">1200 ms = 1,2 segundos</span>
                </label>

                <label class="block">
                    <span class="mb-1 block text-xs font-semibold text-gray-600">Se vuelve a ver</span>
                    @php $frec = old('popup_frecuencia', $ajustes->popup_frecuencia ?? 'sesion'); @endphp
                    <select name="popup_frecuencia"
                        class="w-full rounded-lg border-gray-300 text-sm focus:border-brand-azul focus:ring-brand-azul">
                        <option value="sesion" @selected($frec === 'sesion')>Una vez por visita</option>
                        <option value="dia" @selected($frec === 'dia')>Una vez al día</option>
                        <option value="siempre" @selected($frec === 'siempre')>En cada carga</option>
                    </select>
                    <span class="mt-1 block text-xs text-gray-500">«En cada carga» solo para algo urgente</span>
                </label>

                <label class="flex items-start gap-2 sm:mt-6">
                    <input type="checkbox" name="popup_auto_avance" value="1"
                        @checked(old('popup_auto_avance', $ajustes->popup_auto_avance ?? false))
                        class="mt-0.5 rounded border-gray-300 text-brand-azul focus:ring-brand-azul">
                    <span class="text-sm text-gray-700">
                        Pasar solo entre anuncios
                        <span class="block text-xs text-gray-500">Solo aplica con más de uno</span>
                    </span>
                </label>
            </div>

            <div class="mt-4 flex justify-end">
                <button type="submit" class="rounded-lg bg-gray-800 px-5 py-2 text-sm font-semibold text-white hover:bg-gray-700">
                    Guardar ajustes
                </button>
            </div>
        </form>

        @forelse ($anuncios as $anuncio)
            <div class="mb-3 flex flex-wrap items-center gap-4 rounded-xl border border-gray-200 bg-white p-4 shadow-sm">
                <img src="{{ $anuncio->imagen_url }}" alt=""
                    class="h-16 w-24 flex-shrink-0 rounded-lg object-cover bg-gray-100" loading="lazy" decoding="async">

                <div class="min-w-0 flex-1">
                    <p class="font-semibold text-gray-800 truncate">{{ $anuncio->titulo }}</p>
                    <p class="text-xs text-gray-500 mt-0.5">
                        @if ($anuncio->visible_desde || $anuncio->visible_hasta)
                            {{ $anuncio->visible_desde?->format('d/m/Y') ?? 'desde siempre' }}
                            →
                            {{ $anuncio->visible_hasta?->format('d/m/Y') ?? 'sin caducidad' }}
                        @else
                            Sin fechas: se muestra mientras esté visible
                        @endif
                    </p>
                </div>

                <div class="flex flex-wrap items-center gap-2">
                    @if ($anuncio->recorte_notable)
                        <span class="rounded-full bg-amber-50 px-2.5 py-1 text-xs font-semibold text-amber-800 ring-1 ring-amber-200"
                            title="Mide {{ $anuncio->imagen_ancho }} × {{ $anuncio->imagen_alto }} px. Recomendado: {{ \App\Models\Anuncio::ANCHO_RECOMENDADO }} × {{ \App\Models\Anuncio::ALTO_RECOMENDADO }} px">
                            <x-fas-crop class="text-[10px]" aria-hidden="true" /> Se recorta {{ $anuncio->recorte_porcentaje }}%
                        </span>
                    @endif
                </div>

                {{-- Estado real: de poco sirve «visible» si además está fuera de fecha. --}}
                <div class="flex flex-wrap items-center gap-2">
                    @if (! $anuncio->is_visible)
                        <span class="rounded-full bg-gray-100 px-2.5 py-1 text-xs font-semibold text-gray-500">Oculto</span>
                    @elseif ($anuncio->caducado)
                        <span class="rounded-full bg-amber-100 px-2.5 py-1 text-xs font-semibold text-amber-800">
                            <x-fas-triangle-exclamation class="text-[10px]" aria-hidden="true" /> Caducado
                        </span>
                    @elseif ($anuncio->programado)
                        <span class="rounded-full bg-blue-100 px-2.5 py-1 text-xs font-semibold text-blue-800">Programado</span>
                    @else
                        <span class="rounded-full bg-green-100 px-2.5 py-1 text-xs font-semibold text-green-800">En portada</span>
                    @endif
                </div>

                <div class="flex items-center gap-1">
                    <form method="POST" action="{{ route('admin.anuncios.toggle', $anuncio) }}">
                        @csrf
                        <button type="submit" class="rounded p-2 text-gray-400 hover:bg-gray-100 hover:text-gray-700"
                            title="{{ $anuncio->is_visible ? 'Ocultar' : 'Mostrar' }}">
                            @if ($anuncio->is_visible)
                                <x-fas-eye-slash aria-hidden="true" />
                            @else
                                <x-fas-eye aria-hidden="true" />
                            @endif
                            <span class="sr-only">{{ $anuncio->is_visible ? 'Ocultar' : 'Mostrar' }}</span>
                        </button>
                    </form>
                    <a href="{{ route('admin.anuncios.edit', $anuncio) }}"
                        class="rounded p-2 text-gray-400 hover:bg-gray-100 hover:text-gray-700" title="Editar">
                        <x-fas-pen aria-hidden="true" /><span class="sr-only">Editar</span>
                    </a>
                    <form method="POST" action="{{ route('admin.anuncios.destroy', $anuncio) }}">
                        @csrf @method('DELETE')
                        <button type="submit" class="rounded p-2 text-gray-400 hover:bg-red-50 hover:text-red-600"
                            title="Enviar a la papelera">
                            <x-fas-trash aria-hidden="true" /><span class="sr-only">Enviar a la papelera</span>
                        </button>
                    </form>
                </div>
            </div>
        @empty
            <div class="rounded-xl border-2 border-dashed border-gray-300 py-16 text-center">
                <p class="font-semibold text-gray-500">Todavía no hay anuncios</p>
                <p class="mt-1 text-sm text-gray-400">Sin anuncios vigentes, la portada no muestra ninguna ventana.</p>
            </div>
        @endforelse

        {{ $anuncios->links() }}
    </div>
@endsection
