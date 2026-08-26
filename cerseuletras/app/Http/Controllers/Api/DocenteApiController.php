<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Docente;
use Illuminate\Http\JsonResponse;

/**
 * Plana docente, de solo lectura.
 *
 * Se declara campo a campo, como el resto de la API: la tabla `docentes`
 * guarda correo y enlaces personales, y no todo lo que el panel administra
 * tiene por qué acabar publicado.
 */
class DocenteApiController extends Controller
{
    public function index(): JsonResponse
    {
        // `activos()` y `ordenados()` son los scopes del modelo, los mismos que
        // usa Blade. `estado` es un booleano, no la cadena «activo»: filtrar a
        // mano aquí habría devuelto una lista vacía sin dar ningún error.
        $docentes = Docente::query()->activos()->ordenados()->get();

        return response()->json([
            'data' => $docentes->map(fn (Docente $d) => $this->comoArray($d))->values(),
        ]);
    }

    public function show(string $slug): JsonResponse
    {
        $docente = Docente::query()->activos()->where('slug', $slug)->first();

        if (! $docente) {
            return response()->json(['message' => 'Docente no encontrado.'], 404);
        }

        return response()->json([
            'data' => $this->comoArray($docente, completo: true),
        ]);
    }

    private function comoArray(Docente $docente, bool $completo = false): array
    {
        $base = [
            'slug' => $docente->slug,
            'nombre' => trim($docente->nombres . ' ' . $docente->apellidos),
            'nombre_completo' => $docente->nombre_completo,
            'grado' => $docente->grado ?: null,
            'foto' => $docente->foto_url,
        ];

        if (! $completo) {
            return $base;
        }

        return $base + [
            'biografia' => $docente->biografia ?: null,
            'lineas_investigacion' => $docente->lineas_investigacion ?: null,
            // Perfiles academicos publicos. El correo no se expone: publicarlo
            // en HTML estatico es entregarselo a cualquier rastreador.
            'orcid' => $docente->orcid ?: null,
            'cti_vitae' => $docente->cti_vitae ?: null,
            'linkedin' => $docente->linkedin ?: null,
        ];
    }
}
