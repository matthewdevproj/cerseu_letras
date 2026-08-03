@extends('admin.layout.app')

@section('title', 'Contenido de páginas')

@section('content')
    <div class="max-w-4xl mx-auto">
        <h1 class="text-xl font-bold text-gray-800 mb-1">Contenido de páginas</h1>
        <p class="text-sm text-gray-500 mb-6">
            Texto de las páginas largas del sitio. El diseño no cambia: aquí se edita lo que dicen.
        </p>

        <div class="grid sm:grid-cols-2 gap-4">
            @foreach (\App\Models\ContentPage::PAGINAS as $slug => $nombre)
                @php $p = $paginas[$slug] ?? null; @endphp
                <a href="{{ route('admin.contenido.edit', $slug) }}"
                    class="group block bg-white rounded-lg shadow-sm border border-gray-100 p-6 hover:border-unmsm-guinda/40 hover:shadow-md transition-all">
                    <div class="flex items-start justify-between gap-3">
                        <div>
                            <h2 class="font-bold text-gray-900 group-hover:text-unmsm-guinda transition-colors">{{ $nombre }}</h2>
                            <p class="text-sm text-gray-500 mt-0.5">/{{ $slug }}</p>
                        </div>
                        <x-fas-arrow-right class="mt-1 text-gray-300 group-hover:text-unmsm-guinda motion-safe:group-hover:translate-x-1 transition-all" aria-hidden="true" />
                    </div>
                    <p class="text-xs text-gray-400 mt-4">
                        {{ $p?->secciones_count ?? 0 }} sección(es)
                    </p>
                </a>
            @endforeach
        </div>
    </div>
@endsection
