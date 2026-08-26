<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProgramaResource;
use App\Models\Programa;
use App\Models\SiteSetting;
use App\Models\TipoOferta;
use Illuminate\Http\JsonResponse;

/**
 * API de contenido de la oferta, de solo lectura.
 *
 * La consume el sitio público en Astro, que no toca la base de datos: si
 * mañana el esquema cambia, el contrato de aquí es lo único que hay que
 * mantener estable.
 *
 * Sin autenticación a propósito: publica lo que ya es público en el sitio, y
 * el filtro por `estado` es el mismo que aplica Blade. Si algún día expone
 * algo que no esté publicado, entonces sí hará falta Sanctum.
 */
class OfertaApiController extends Controller
{
    /**
     * Los tipos de oferta, con la unidad en que se mide cada uno.
     *
     * Sale del enum, no de una constante duplicada: es la misma fuente que usa
     * el sitio en Blade, el panel y las rutas.
     */
    public function tipos(): JsonResponse
    {
        $tipos = collect(TipoOferta::cases())->map(fn (TipoOferta $tipo) => [
            'slug' => $tipo->slug(),
            'singular' => $tipo->singular(),
            'plural' => $tipo->plural(),
            'medidas' => array_values($tipo->medidas()),
            'publicados' => Programa::deTipo($tipo)
                ->where('estado', Programa::ESTADO_PUBLICADO)
                ->count(),
            // Hero del listado, editable en Configuracion. Sin esto, el titulo
            // y la bajada de cada seccion tendrian que escribirse en la
            // plantilla del sitio.
            'hero' => $this->hero($tipo),
        ]);

        return response()->json(['data' => $tipos]);
    }

    /**
     * Textos e imagen del hero de un tipo, desde `site_settings`.
     */
    private function hero(TipoOferta $tipo): array
    {
        $ajustes = SiteSetting::get();
        $prefijo = $tipo->slug() . '_hero_';
        $imagen = $ajustes?->{$prefijo . 'imagen'};

        return [
            'titulo' => $ajustes?->{$prefijo . 'titulo'} ?: $tipo->plural(),
            'texto' => $ajustes?->{$prefijo . 'texto'} ?: null,
            'claim' => $ajustes?->{$prefijo . 'claim'} ?: null,
            // Sin imagen propia se cae a una del campus, la misma que usa el
            // hero de la portada: mejor una foto institucional que un bloque
            // de color plano.
            'imagen' => $imagen ? asset('storage/' . $imagen) : asset('images/campus-fachada.webp'),
        ];
    }

    /**
     * Listado de la oferta. `?tipo=cursos` la acota a un tipo.
     */
    public function index(): JsonResponse
    {
        $consulta = Programa::query()->visibles()->ordenPublicacion();

        $slug = request()->query('tipo');
        if ($slug) {
            $tipo = TipoOferta::desdeSlug($slug);
            if (! $tipo) {
                return response()->json([
                    'message' => "Tipo de oferta desconocido: {$slug}.",
                    'tipos_validos' => array_map(
                        fn (TipoOferta $t) => $t->slug(),
                        TipoOferta::cases()
                    ),
                ], 404);
            }
            $consulta->deTipo($tipo);
        }

        return response()->json([
            'data' => ProgramaResource::collection($consulta->get())->resolve(),
        ]);
    }

    /**
     * Ficha de un programa.
     */
    public function show(string $slug): JsonResponse
    {
        $programa = Programa::query()->with('docentes')->visibles()->where('slug', $slug)->first();

        if (! $programa) {
            return response()->json(['message' => 'Programa no encontrado.'], 404);
        }

        return response()->json([
            'data' => (new ProgramaResource($programa))->resolve(),
        ]);
    }
}
