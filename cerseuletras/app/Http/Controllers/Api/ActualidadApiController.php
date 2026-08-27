<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Anuncio;
use App\Models\Cronograma;
use App\Models\SiteSetting;
use App\Models\Testimonio;
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

    /**
     * Anuncios de la portada y los ajustes con los que se muestran.
     *
     * Van juntos porque se usan juntos: sin el retardo y la frecuencia, el
     * sitio tendria que decidirlos por su cuenta y dejarian de ser
     * administrables. Si no hay ninguno vigente la lista viene vacia y el
     * sitio no pinta nada —ni marcado, ni CSS, ni JS—.
     */
    public function anuncios(): JsonResponse
    {
        return response()->json([
            'data' => [
                'items' => Anuncio::paraPopup(),
                'ajustes' => SiteSetting::ajustesPopup(),
            ],
        ]);
    }

    /**
     * Testimonios publicados.
     *
     * Hoy no hay ninguno cargado, y por eso mismo el endpoint existe: sin el,
     * el dia que la Unidad publique el primero no aparecerian en ninguna parte
     * y habria que volver a tocar codigo. La seccion se oculta sola cuando la
     * lista viene vacia.
     */
    public function testimonios(): JsonResponse
    {
        $testimonios = Testimonio::query()
            ->publicados()
            ->recientes()
            ->with('programa')
            ->get();

        return response()->json([
            'data' => $testimonios->map(fn (Testimonio $t) => [
                'nombre' => $t->nombre,
                'contenido' => $t->contenido,
                'foto' => $t->photo_url,
                'programa' => $t->programa?->nombre,
            ])->values(),
        ]);
    }
}
