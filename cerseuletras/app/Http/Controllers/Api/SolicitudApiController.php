<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Jobs\EnviarAvisoDeSolicitud;
use App\Models\Lead;
use App\Models\Programa;
use App\Models\TipoOferta;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;

/**
 * Recepción de solicitudes de información desde el sitio público.
 *
 * Es el único endpoint de la API que escribe, y el que hace posible que el
 * sitio en Astro sustituya al de Blade: un sitio estático no puede procesar un
 * formulario, así que el POST tiene que llegar aquí.
 *
 * No lleva Sanctum a propósito. La propuesta reserva Sanctum para «exponer la
 * API a un cliente móvil o a terceros», y esto no es eso: es un formulario
 * público que cualquiera puede rellenar, igual que el de Blade. Autenticarlo
 * significaría repartir un token en el HTML de un sitio estático, que no
 * protege de nada. Lo que sí hace falta es contener el abuso, y de eso se
 * ocupan el límite por IP —declarado en la ruta— y el señuelo de abajo.
 */
class SolicitudApiController extends Controller
{
    public function store(Request $request, string $tipo): JsonResponse
    {
        $tipoOferta = TipoOferta::desdeSlug($tipo);

        if (! $tipoOferta) {
            return response()->json(['message' => "Tipo de oferta desconocido: {$tipo}."], 404);
        }

        // Señuelo: un campo que el navegador oculta y ninguna persona rellena.
        // Los robots que envían todos los campos del formulario sí lo hacen.
        // Se responde 201 igual que un envío correcto: decirle a un robot que
        // ha sido detectado es enseñarle a evitarlo la próxima vez.
        if (filled($request->input('sitio_web'))) {
            Log::info('Solicitud descartada por el señuelo anti-robots.');

            return response()->json(['message' => 'Solicitud registrada.'], 201);
        }

        $datos = $request->validate([
            'nombres' => 'required|string|max:255',
            'apellidos' => 'required|string|max:255',
            'correo' => 'required|email|max:255',
            'telefono' => 'required|string|max:50',
            'pais' => 'required|string|max:255',
            'region' => 'required|string|max:255',
            // Por slug y no por id: es la identidad que usa el resto de la
            // API, y el sitio no conoce los ids —ni tiene por que—.
            'programa' => ['required', 'string', Rule::exists('programas', 'slug')->whereNull('deleted_at')],
        ]);

        $programa = Programa::query()->where('slug', $datos['programa'])->firstOrFail();
        unset($datos['programa']);

        $lead = Lead::create($datos + [
            'tipo' => $tipoOferta->value,
            'programa_id' => $programa->id,
        ]);

        // Guardar primero y avisar después: el registro de la solicitud no
        // puede depender de que el correo funcione. Y el aviso va a la cola,
        // así que quien envía el formulario no espera al servidor de correo.
        try {
            EnviarAvisoDeSolicitud::dispatch($lead);
        } catch (\Throwable $e) {
            Log::error('No se pudo encolar el aviso de la solicitud #' . $lead->id . ': ' . $e->getMessage());
        }

        return response()->json([
            'message' => '¡Gracias! Tu solicitud fue registrada. Nos pondremos en contacto contigo pronto.',
        ], 201);
    }
}
