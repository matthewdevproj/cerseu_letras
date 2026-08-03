@extends('admin.layout.app')

@section('title', 'Solicitudes de Diplomados')

@section('content')
    <div class="max-w-7xl mx-auto">

        {{-- Esta pantalla no mostraba ningún aviso: el mensaje de «Solicitud
             eliminada» se perdía, y el motivo de un reenvío fallido también. --}}
        @if (session('success'))
            <div class="mb-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded">
                {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div class="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded">
                {{ session('error') }}
            </div>
        @endif

        <div class="flex flex-wrap items-start justify-between gap-4 mb-6">
            <div>
                <h1 class="text-xl font-bold text-gray-800 mb-1">Solicitudes de información</h1>
                <p class="text-sm text-gray-500">
                    Contactos recibidos desde el formulario de <code>/diplomados</code>.
                </p>
            </div>
            <a href="{{ route('admin.leads.export', request()->only('programa')) }}"
                class="inline-flex items-center gap-2 px-4 py-2 bg-red-700 text-white rounded-lg hover:bg-red-800 text-sm font-medium">
                <x-fas-file-csv aria-hidden="true" /> Exportar CSV
            </a>
        </div>

        {{-- Resumen --}}
        <div class="grid sm:grid-cols-2 gap-4 mb-6">
            <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-5">
                <p class="text-xs uppercase tracking-wider text-gray-500 font-bold mb-1">Total de solicitudes</p>
                <p class="text-3xl font-bold text-gray-900">{{ $total }}</p>
            </div>
            <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-5">
                <p class="text-xs uppercase tracking-wider text-gray-500 font-bold mb-1">Últimos 7 días</p>
                <p class="text-3xl font-bold text-unmsm-guinda">{{ $ultimos7 }}</p>
            </div>
        </div>

        {{-- Filtros --}}
        <form method="GET" class="bg-white rounded-lg shadow-sm border border-gray-100 p-4 mb-6 flex flex-wrap gap-3 items-end">
            <div class="flex-1 min-w-[220px]">
                <label for="q" class="block text-xs font-bold uppercase tracking-wider text-gray-500 mb-1">Buscar</label>
                <input id="q" type="search" name="q" value="{{ request('q') }}"
                    placeholder="Nombre, correo o teléfono"
                    class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
            </div>
            <div class="min-w-[220px]">
                <label for="programa" class="block text-xs font-bold uppercase tracking-wider text-gray-500 mb-1">Programa</label>
                <select id="programa" name="programa" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
                    <option value="">Todos</option>
                    @foreach ($programas as $p)
                        <option value="{{ $p->id }}" @selected(request('programa') == $p->id)>{{ $p->titulo_completo }}</option>
                    @endforeach
                </select>
            </div>
            <button type="submit" class="px-4 py-2 bg-gray-800 text-white rounded-lg hover:bg-gray-900 text-sm font-medium">
                Filtrar
            </button>
            @if (request()->hasAny(['q', 'programa']))
                <a href="{{ route('admin.leads.index') }}" class="px-4 py-2 text-sm text-gray-500 hover:text-red-700">Limpiar</a>
            @endif
        </form>

        {{-- Listado --}}
        @if ($leads->isEmpty())
            <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-12 text-center">
                <x-fas-inbox class="text-4xl text-gray-200 mb-3" aria-hidden="true" />
                <p class="font-semibold text-gray-700">
                    {{ request()->hasAny(['q', 'programa']) ? 'Ninguna solicitud coincide con el filtro' : 'Aún no hay solicitudes' }}
                </p>
                <p class="text-sm text-gray-500 mt-1">
                    Las solicitudes del formulario de diplomados aparecerán aquí.
                </p>
            </div>
        @else
            <div class="bg-white rounded-lg shadow-sm border border-gray-100 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="bg-gray-50 text-xs text-gray-600 uppercase tracking-wider">
                            <tr>
                                <th class="px-4 py-3 text-left font-bold">Fecha</th>
                                <th class="px-4 py-3 text-left font-bold">Contacto</th>
                                <th class="px-4 py-3 text-left font-bold">Teléfono</th>
                                <th class="px-4 py-3 text-left font-bold">Ubicación</th>
                                <th class="px-4 py-3 text-left font-bold">Programa</th>
                                <th class="px-4 py-3 text-right font-bold w-16">Acción</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach ($leads as $lead)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-4 py-3 whitespace-nowrap text-gray-500">
                                        {{ $lead->created_at?->format('d/m/Y') }}
                                        <span class="block text-xs text-gray-400">{{ $lead->created_at?->format('H:i') }}</span>
                                    </td>
                                    <td class="px-4 py-3">
                                        <span class="block font-semibold text-gray-900">{{ $lead->nombres }} {{ $lead->apellidos }}</span>
                                        @if ($lead->avisoPendiente())
                                            {{-- La solicitud está guardada; lo que no salió es el aviso por
                                                 correo. Sin este distintivo, una y otra son indistinguibles. --}}
                                            <span class="mt-1 inline-flex items-center gap-1 rounded bg-amber-100 px-1.5 py-0.5 text-[11px] font-semibold text-amber-800"
                                                title="{{ $lead->aviso_error }}">
                                                <x-fas-triangle-exclamation aria-hidden="true" />
                                                Aviso no enviado
                                            </span>
                                        @endif
                                        @if ($lead->correo)
                                            <a href="mailto:{{ $lead->correo }}" class="text-xs text-unmsm-guinda hover:underline break-all">
                                                {{ $lead->correo }}
                                            </a>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap">
                                        @if ($lead->telefono)
                                            <a href="https://wa.me/{{ preg_replace('/\D/', '', $lead->telefono) }}"
                                                target="_blank" rel="noopener noreferrer"
                                                class="inline-flex items-center gap-1.5 text-gray-700 hover:text-green-600"
                                                title="Escribir por WhatsApp">
                                                <x-fab-whatsapp class="text-green-600" aria-hidden="true" />
                                                {{ $lead->telefono }}
                                            </a>
                                        @else
                                            <span class="text-gray-300">—</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 text-gray-600">
                                        {{ collect([$lead->region, $lead->pais])->filter()->implode(', ') ?: '—' }}
                                    </td>
                                    <td class="px-4 py-3">
                                        @if ($lead->programa)
                                            <a href="{{ route('programas.show', $lead->programa->slug) }}" target="_blank"
                                                rel="noopener noreferrer" class="text-gray-700 hover:text-unmsm-guinda">
                                                {{ $lead->programa->titulo_completo }}
                                            </a>
                                        @else
                                            <span class="text-gray-300">—</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 text-right whitespace-nowrap">
                                        @if ($lead->avisoPendiente())
                                            <form method="POST" action="{{ route('admin.leads.reenviar', $lead) }}" class="inline">
                                                @csrf
                                                <button type="submit"
                                                    class="mr-2 text-amber-600 hover:text-amber-800 transition-colors"
                                                    aria-label="Reenviar el aviso de la solicitud de {{ trim($lead->nombres . ' ' . $lead->apellidos) }}"
                                                    title="Reintentar el aviso por correo">
                                                    <x-fas-paper-plane aria-hidden="true" />
                                                </button>
                                            </form>
                                        @endif
                                        {{-- El modal de confirmación vive una sola vez en el layout admin
                                             y se abre despachando este evento. --}}
                                        <button type="button"
                                            onclick="window.dispatchEvent(new CustomEvent('confirm-delete', {
                                                detail: {
                                                    action: '{{ route('admin.leads.destroy', $lead) }}',
                                                    name: '{{ addslashes(trim($lead->nombres . ' ' . $lead->apellidos)) }}',
                                                    title: '¿Eliminar solicitud?'
                                                }
                                            }))"
                                            class="text-red-500 hover:text-red-700 transition-colors"
                                            aria-label="Eliminar solicitud de {{ trim($lead->nombres . ' ' . $lead->apellidos) }}">
                                            <x-fas-trash aria-hidden="true" />
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="mt-4">
                {{ $leads->links() }}
            </div>
        @endif
    </div>
@endsection
