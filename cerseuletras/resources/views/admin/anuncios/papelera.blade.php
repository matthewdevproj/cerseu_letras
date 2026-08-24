@extends('admin.layout.app')

@section('title', 'Papelera de anuncios')

@section('content')
    <div class="max-w-4xl mx-auto">
        <div class="mb-6 flex items-start justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-800">Papelera de anuncios</h1>
                <p class="text-sm text-gray-500 mt-1">
                    Los anuncios borrados se guardan aquí. No se ven en el sitio, pero
                    se pueden recuperar.
                </p>
            </div>
            <a href="{{ route('admin.anuncios.index') }}"
                class="px-4 py-2.5 rounded-lg border border-gray-300 text-sm font-semibold text-gray-600 hover:bg-gray-50">
                Volver a anuncios
            </a>
        </div>

        @forelse ($anuncios as $anuncio)
            <div class="mb-3 flex flex-wrap items-center gap-4 rounded-xl border border-gray-200 bg-white p-4">
                <img src="{{ $anuncio->imagen_url }}" alt=""
                    class="h-14 w-20 flex-shrink-0 rounded-lg object-cover opacity-60 grayscale bg-gray-100"
                    loading="lazy" decoding="async">
                <div class="min-w-0 flex-1">
                    <p class="font-semibold text-gray-700 truncate">{{ $anuncio->titulo }}</p>
                    <p class="text-xs text-gray-400">Borrado el {{ $anuncio->deleted_at->format('d/m/Y \a \l\a\s H:i') }}</p>
                </div>
                <form method="POST" action="{{ route('admin.anuncios.restaurar', $anuncio->id) }}">
                    @csrf
                    <button type="submit"
                        class="rounded-lg bg-green-50 px-4 py-2 text-sm font-semibold text-green-700 hover:bg-green-100">
                        <x-fas-arrow-rotate-left class="mr-1" aria-hidden="true" /> Restaurar
                    </button>
                </form>
            </div>
        @empty
            <div class="rounded-xl border-2 border-dashed border-gray-300 py-16 text-center">
                <p class="font-semibold text-gray-500">La papelera está vacía</p>
            </div>
        @endforelse

        {{ $anuncios->links() }}
    </div>
@endsection
