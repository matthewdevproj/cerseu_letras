<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Lead;
use App\Models\Programa;
use App\Models\TipoOferta;
use App\Services\AvisoDeSolicitud;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * Solicitudes de información de talleres y cursos.
 *
 * El formulario público ya guardaba las solicitudes, pero no existía ninguna
 * pantalla para consultarlas: si fallaba el envío del correo, el contacto
 * quedaba inaccesible. Esta pantalla las expone y permite exportarlas.
 *
 * Las de los dos módulos se listan juntas —quien atiende las revisa igual— con
 * un filtro por tipo para separarlas cuando hace falta.
 */
class AdminLeadController extends Controller
{
    public function index(Request $request)
    {
        $tipo = TipoOferta::desdeSlug($request->string('tipo')->toString());

        $leads = Lead::query()
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
            ->when($tipo, fn ($q) => $q->deTipo($tipo))
            ->latest()
            ->paginate(25)
            ->withQueryString();

        return view('admin.leads.index', [
            'leads' => $leads,
            'tipo' => $tipo,
            'programas' => Programa::whereIn('grado', TipoOferta::grados())
                ->orderBy('nombre')->get(['id', 'nombre', 'mencion']),
            'total' => Lead::count(),
            'ultimos7' => Lead::where('created_at', '>=', now()->subDays(7))->count(),
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
        $tipo = TipoOferta::desdeSlug($request->string('tipo')->toString());

        $consulta = Lead::query()
            ->with('programa:id,nombre,mencion')
            ->when($request->filled('programa'), fn ($q) => $q->where('programa_id', $request->integer('programa')))
            ->when($tipo, fn ($q) => $q->deTipo($tipo))
            ->latest();

        $nombre = 'solicitudes-' . ($tipo?->slug() ?? 'todas') . '-' . now()->format('Y-m-d') . '.csv';

        return response()->streamDownload(function () use ($consulta) {
            $salida = fopen('php://output', 'w');

            // BOM UTF-8: sin él, Excel en Windows rompe las tildes.
            fwrite($salida, "\xEF\xBB\xBF");

            fputcsv($salida, ['Fecha', 'Tipo', 'Nombres', 'Apellidos', 'Correo', 'Teléfono', 'País', 'Región', 'Oferta'], ';');

            $consulta->chunk(500, function ($filas) use ($salida) {
                foreach ($filas as $lead) {
                    fputcsv($salida, [
                        $lead->created_at?->format('d/m/Y H:i'),
                        $lead->tipo?->singular() ?? '—',
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

    /**
     * Reintenta el aviso por correo de una solicitud.
     *
     * Sirve para las que llegaron mientras el correo no estaba configurado: una
     * vez puestas las credenciales, se reenvían desde aquí sin tener que
     * copiar los datos a mano.
     */
    public function reenviarAviso(Lead $lead)
    {
        if (AvisoDeSolicitud::enviar($lead)) {
            return back()->with('success', 'Aviso reenviado.');
        }

        // Se muestra el motivo tal cual: quien administra necesita saber si es
        // que falta la contraseña, si el servidor rechaza o si no hay
        // destinatario configurado.
        return back()->with('error', 'No se pudo enviar: ' . $lead->aviso_error);
    }

    public function destroy(Lead $lead)
    {
        $lead->delete();

        return redirect()->route('admin.leads.index')
            ->with('success', 'Solicitud eliminada.');
    }
}
