<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Cronograma;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

/**
 * Secciones que hoy están vacías: eventos, documentos y cronograma.
 *
 * Se exponen igual, y no se dejan sin endpoint «porque no hay datos»: sin
 * ellos, las páginas del sitio tendrían el estado vacío escrito a mano y no
 * mostrarían nada el día que la Unidad cargue el primer evento. La página es
 * estructura; que esté vacía es un estado, no una decisión de diseño.
 */
class ActualidadApiController extends Controller
{
    public function eventos(): JsonResponse
    {
        $eventos = DB::table('eventos')
            ->whereNull('deleted_at')
            ->where('activo', 1)
            ->orderBy('orden')
            ->orderByDesc('fecha_inicio')
            ->get();

        return response()->json([
            'data' => $eventos->map(fn ($e) => [
                'titulo' => $e->titulo,
                'descripcion' => $e->descripcion ?: null,
                'fecha_inicio' => $e->fecha_inicio,
                'fecha_fin' => $e->fecha_fin,
                'url' => $e->url ?: null,
                'imagen' => $e->imagen ? asset('storage/' . $e->imagen) : null,
            ])->values(),
        ]);
    }

    public function informativos(): JsonResponse
    {
        $filas = DB::table('informativos')
            ->whereNull('deleted_at')
            ->orderBy('categoria')
            ->orderBy('orden')
            ->get();

        // Agrupados por categoría, que es como los pinta el sitio.
        $porCategoria = $filas->groupBy('categoria')->map(fn ($grupo, $categoria) => [
            'categoria' => $categoria ?: 'General',
            'recursos' => $grupo->map(fn ($r) => [
                'titulo' => $r->titulo,
                'tipo' => $r->tipo ?: null,
                'url' => $r->url,
            ])->values(),
        ])->values();

        return response()->json(['data' => $porCategoria]);
    }

    public function cronograma(): JsonResponse
    {
        $cronograma = Cronograma::query()->where('is_active', true)->first();

        if (! $cronograma) {
            return response()->json(['data' => null]);
        }

        $items = DB::table('cronograma_items')
            ->where('cronograma_id', $cronograma->id)
            ->orderBy('orden')
            ->get();

        return response()->json([
            'data' => [
                'titulo' => $cronograma->title,
                'descripcion' => $cronograma->description,
                'items' => $items->map(fn ($i) => [
                    'seccion' => $i->section,
                    'es_encabezado' => (bool) $i->is_section_heading,
                    'actividad' => $i->actividad,
                    'fecha' => $i->fecha_text,
                ])->values(),
            ],
        ]);
    }
}
