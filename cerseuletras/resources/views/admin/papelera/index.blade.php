@extends('admin.layout.app')

@section('title', 'Papelera')

@section('content')
    <div class="max-w-5xl mx-auto">
        <div class="mb-6">
            <h1 class="text-2xl font-bold text-gray-800">Papelera</h1>
            <p class="text-sm text-gray-500 mt-1">
                Lo que se borra desde el panel ya no se pierde: sale del sitio pero
                queda aquí, y se puede devolver a su sitio con un clic.
            </p>
        </div>

        @if (session('success'))
            <div class="mb-5 rounded-lg bg-green-50 border border-green-200 px-4 py-3 text-sm text-green-800">
                <x-fas-circle-check class="mr-1" aria-hidden="true" /> {{ session('success') }}
            </div>
        @endif

        <div class="mb-5 flex flex-wrap gap-2">
            <a href="{{ route('admin.papelera.index') }}"
                class="rounded-full px-4 py-1.5 text-sm font-semibold {{ $tipoActivo ? 'bg-white text-gray-600 ring-1 ring-gray-300 hover:bg-gray-50' : 'bg-brand-azul text-white' }}">
                Todo
            </a>
            @foreach ($tipos as $clave => $etiqueta)
                <a href="{{ route('admin.papelera.index', ['tipo' => $clave]) }}"
                    class="rounded-full px-4 py-1.5 text-sm font-semibold {{ $tipoActivo === $clave ? 'bg-brand-azul text-white' : 'bg-white text-gray-600 ring-1 ring-gray-300 hover:bg-gray-50' }}">
                    {{ $etiqueta }}
                </a>
            @endforeach
        </div>

        @forelse ($elementos as $elemento)
            <div class="mb-2 flex flex-wrap items-center gap-4 rounded-xl border border-gray-200 bg-white px-4 py-3">
                <span class="flex h-9 w-9 flex-shrink-0 items-center justify-center rounded-lg bg-gray-100 text-gray-400">
                    <x-dynamic-component :component="$elemento['icono']" aria-hidden="true" />
                </span>

                <div class="min-w-0 flex-1">
                    <p class="font-semibold text-gray-700 truncate">{{ $elemento['titulo'] }}</p>
                    <p class="text-xs text-gray-400">
                        {{ $elemento['etiqueta'] }} · borrado el
                        {{ $elemento['borrado']?->format('d/m/Y \a \l\a\s H:i') }}
                    </p>
                </div>

                <form method="POST" action="{{ route('admin.papelera.restaurar', [$elemento['tipo'], $elemento['id']]) }}">
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
                <p class="mt-1 text-sm text-gray-400">
                    Aquí aparecerá lo que se borre, por si hace falta recuperarlo.
                </p>
            </div>
        @endforelse
    </div>
@endsection
