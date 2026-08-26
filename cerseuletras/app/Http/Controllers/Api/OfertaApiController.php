<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProgramaResource;
use App\Models\Programa;
use App\Models\AdmisionSetting;
use App\Models\SiteSetting;
use App\Models\TipoOferta;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

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
     * Contenido de la pagina de admision de un tipo.
     *
     * Sale entero de `admision_settings`, que edita la Unidad. Se entrega tal
     * cual: si hoy dice «grado de bachiller» es porque eso es lo que hay
     * guardado —un pendiente conocido, heredado de Posgrado— y corregirlo
     * aqui seria maquillar el sintoma en vez del dato.
     */
    public function admision(string $slug): JsonResponse
    {
        $tipo = TipoOferta::desdeSlug($slug);

        if (! $tipo) {
            return response()->json(['message' => "Tipo de oferta desconocido: {$slug}."], 404);
        }

        $ajustes = AdmisionSetting::query()->where('tipo', $tipo->value)->first();

        // El modelo ya castea estos campos a array; desde una consulta cruda
        // llegarian como JSON. Se admiten los dos para no depender de por
        // donde vino el dato.
        $comoLista = function (mixed $valor): array {
            if (is_array($valor)) {
                return $valor;
            }

            $decodificado = json_decode((string) $valor, true);

            return is_array($decodificado) ? $decodificado : [];
        };

        return response()->json([
            'data' => [
                'tipo' => $tipo->slug(),
                'titulo' => $ajustes?->hero_titulo ?: ('Admisión · ' . $tipo->plural()),
                'subtitulo' => $ajustes?->hero_subtitulo ?: null,
                'pasos' => $comoLista($ajustes?->pasos),
                'requisitos' => [
                    'lista' => $comoLista($ajustes?->requisitos_lista),
                    'observaciones' => $ajustes?->requisitos_observaciones ?: null,
                    'notas' => $ajustes?->requisitos_notas ?: null,
                    'correo' => $ajustes?->requisitos_email ?: null,
                ],
                'pago' => [
                    'costo' => $ajustes?->pago_costo ?: null,
                    'descripcion' => $ajustes?->pago_descripcion ?: null,
                    'instrucciones' => $comoLista($ajustes?->pago_instrucciones),
                    'observaciones' => $ajustes?->pago_observaciones ?: null,
                    'enlace_sanmarket' => $ajustes?->pago_link_sanmarket ?: null,
                ],
                'resultados' => [
                    'texto' => $ajustes?->resultados_texto ?: null,
                    'enlace' => $ajustes?->resultados_enlace ?: null,
                ],
                // Convocatorias con sus fechas, que es lo que trae a alguien
                // a esta pagina.
                'convocatorias' => $ajustes
                    ? DB::table('admision_cronograma_items')
                        ->where('admision_setting_id', $ajustes->id)
                        ->orderBy('id')
                        ->get()
                        ->map(fn ($c) => [
                            'programa' => $c->programa,
                            'convocatoria' => $c->convocatoria,
                            'inscripcion' => $c->fecha_inscripcion ?? null,
                            'limite' => $c->fecha_limite ?? null,
                            'estado' => $c->estado ?? null,
                        ])->all()
                    : [],
            ],
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
