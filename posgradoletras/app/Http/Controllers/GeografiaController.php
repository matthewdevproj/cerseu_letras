<?php

namespace App\Http\Controllers;

use App\Services\GeografiaService;
use Illuminate\Http\JsonResponse;

/**
 * Países y regiones para el formulario de diplomados.
 *
 * El navegador habla con el sitio, no con el servicio externo: así el
 * formulario no añade peticiones a terceros y las respuestas se cachean.
 */
class GeografiaController extends Controller
{
    public function paises(): JsonResponse
    {
        return response()->json(['paises' => GeografiaService::paises()])
            // Una hora, no un día: el servidor ya las cachea un mes, así que
            // esto solo ahorra una petición pequeña. Con un plazo largo, un
            // cambio de formato —como el paso de códigos ISO3 a ISO2— deja
            // formularios degradados hasta que caduque.
            ->header('Cache-Control', 'public, max-age=3600');
    }

    public function regiones(string $codigo): JsonResponse
    {
        return response()->json([
            'regiones' => GeografiaService::regiones($codigo),
            // El formulario renombra el campo: «Departamento» en Perú,
            // «Prefectura» en Japón… Decir «Región» en los 249 es impreciso.
            'etiqueta' => GeografiaService::etiquetaSubdivision($codigo),
        ])->header('Cache-Control', 'public, max-age=3600');
    }
}
