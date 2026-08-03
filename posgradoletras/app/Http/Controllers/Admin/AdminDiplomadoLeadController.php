<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DiplomadoLead;
use App\Models\Programa;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * Solicitudes de información de diplomados.
 *
 * El formulario público ya guardaba en `diplomado_leads`, pero no existía
 * ninguna pantalla para consultarlas: si fallaba el envío del correo, el
 * contacto quedaba inaccesible. Esta pantalla las expone y permite exportarlas.
 */
class AdminDiplomadoLeadController extends Controller
{
    public function index(Request $request)
    {
        $leads = DiplomadoLead::query()
            // Evita una consulta por fila al mostrar el programa de cada lead.
            ->with('programa:id,nombre,mencion,grado,slug')
            ->when($request->filled('q'), function ($query) use ($request) {
                $termino = '%' . $request->string('q')->trim() . '%';
                $query->where(function ($q) use ($termino) {
                    $q->where('nombres', 'like', $termino)
                        ->orWhere('apellidos', 'like', $termino)
                        ->orWhere('correo', 'like', $termino)
                        ->orWhere('telefono', 'like', $termino);
                });
            })
            ->when($request->filled('programa'), fn ($q) => $q->where('programa_id', $request->integer('programa')))
            ->latest()
            ->paginate(25)
            ->withQueryString();

        return view('admin.leads.index', [
            'leads' => $leads,
            'programas' => Programa::diplomados()->orderBy('nombre')->get(['id', 'nombre', 'mencion']),
            'total' => DiplomadoLead::count(),
            'ultimos7' => DiplomadoLead::where('created_at', '>=', now()->subDays(7))->count(),
        ]);
    }

    /**
     * Exporta las solicitudes a CSV respetando los filtros aplicados.
     *
     * Se transmite por streaming: aunque crezca a miles de filas, la memoria
     * usada no depende del número de registros.
     */
    public function export(Request $request): StreamedResponse
    {
        $consulta = DiplomadoLead::query()
            ->with('programa:id,nombre,mencion')
            ->when($request->filled('programa'), fn ($q) => $q->where('programa_id', $request->integer('programa')))
            ->latest();

        $nombre = 'solicitudes-diplomados-' . now()->format('Y-m-d') . '.csv';

        return response()->streamDownload(function () use ($consulta) {
            $salida = fopen('php://output', 'w');

            // BOM UTF-8: sin él, Excel en Windows rompe las tildes.
            fwrite($salida, "\xEF\xBB\xBF");

            fputcsv($salida, ['Fecha', 'Nombres', 'Apellidos', 'Correo', 'Teléfono', 'País', 'Región', 'Programa'], ';');

            $consulta->chunk(500, function ($filas) use ($salida) {
                foreach ($filas as $lead) {
                    fputcsv($salida, [
                        $lead->created_at?->format('d/m/Y H:i'),
                        $lead->nombres,
                        $lead->apellidos,
                        $lead->correo,
                        $lead->telefono,
                        $lead->pais,
                        $lead->region,
                        $lead->programa?->titulo_completo ?? '—',
                    ], ';');
                }
            });

            fclose($salida);
        }, $nombre, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }

    public function destroy(DiplomadoLead $lead)
    {
        $lead->delete();

        return redirect()->route('admin.leads.index')
            ->with('success', 'Solicitud eliminada.');
    }
}
