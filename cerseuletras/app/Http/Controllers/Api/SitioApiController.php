<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CronogramaAdmision;
use App\Models\MenuItem;
use App\Models\SiteSetting;
use Illuminate\Http\JsonResponse;

/**
 * Identidad y navegación del sitio.
 *
 * Es la pieza que hace practicable la regla de la propuesta —«lo que cambia
 * entre unidades sale de configuración, nunca del código»— también en el
 * frontend. Sin esto, el sitio en Astro tendría el nombre, el correo, el
 * teléfono y el menú escritos en una plantilla, que es el mismo error que se
 * corrigió en los seeders y en la migración, solo que en otro lenguaje.
 *
 * Cambiar el CERSEU por otra unidad debería ser cambiar filas de
 * `site_settings` y `menu_items`, no editar un `.astro`.
 */
class SitioApiController extends Controller
{
    /**
     * Identidad, contacto y redes.
     *
     * El contacto sale de `SiteSetting::contacto()`, el mismo accesor que usa
     * Blade, para que ambos sitios no puedan contradecirse: ahí está resuelta
     * la cascada campo del panel → valor de respaldo, y la derivación del
     * enlace de WhatsApp desde el teléfono.
     */
    public function configuracion(): JsonResponse
    {
        $ajustes = SiteSetting::get();

        return response()->json([
            'data' => [
                'nombre' => $ajustes?->site_name,
                'descripcion' => $ajustes?->site_description,
                'logo' => $ajustes?->logo_path,
                'favicon' => $ajustes?->favicon_path,
                'contacto' => [
                    'email' => SiteSetting::contacto('general'),
                    'email_admision' => SiteSetting::contacto('admision'),
                    'email_tramites' => SiteSetting::contacto('tramites'),
                    'telefono' => SiteSetting::contacto('telefono'),
                    'anexo' => $ajustes?->anexo,
                    'whatsapp' => SiteSetting::contacto('whatsapp'),
                    'direccion' => $ajustes?->direccion,
                    'horario' => $ajustes?->horario_atencion,
                ],
                // Hero de la portada. Los textos se editan en Configuracion;
                // tenerlos en la plantilla del sitio obligaria a desplegar
                // para cambiar un titular.
                'portada' => [
                    'kicker' => $ajustes?->home_hero_kicker,
                    'titulo' => $ajustes?->home_hero_titulo,
                    'texto' => $ajustes?->home_hero_texto,
                    'acciones' => array_values(array_filter([
                        $ajustes?->home_hero_cta1_texto ? [
                            'texto' => $ajustes->home_hero_cta1_texto,
                            'url' => $ajustes->home_hero_cta1_url ?: '/',
                        ] : null,
                        $ajustes?->home_hero_cta2_texto ? [
                            'texto' => $ajustes->home_hero_cta2_texto,
                            'url' => $ajustes->home_hero_cta2_url ?: '/',
                        ] : null,
                    ])),
                    // Las mismas fotos del campus que rota el carrusel en
                    // Blade. Van absolutas y en webp, resueltas contra la URL
                    // publica por el middleware ForzarUrlPublica.
                    'imagenes' => array_map(
                        fn (string $f) => asset("images/{$f}"),
                        ['campus-aerea.webp', 'campus-aerea-2.webp', 'campus-fachada.webp']
                    ),
                ],

                // Bloque «Como inscribirte» de la portada, con sus pasos.
                'inscripcion' => $this->inscripcion(),

                'redes' => array_filter([
                    'facebook' => $ajustes?->facebook,
                    'instagram' => $ajustes?->instagram,
                    'tiktok' => $ajustes?->tiktok,
                    'youtube' => $ajustes?->youtube,
                    'linkedin' => $ajustes?->linkedin,
                ]),
            ],
        ]);
    }

    /**
     * El menú, ya resuelto y anidado.
     *
     * Las URLs salen del accesor `enlace`, que prefiere `route_name` sobre una
     * URL escrita a mano —sobrevive a un cambio de ruta— y descarta las rutas
     * que ya no existen. Resolverlo aquí y no en Astro evita que el sitio
     * tenga que conocer el mapa de rutas de Laravel.
     */
    public function menu(): JsonResponse
    {
        $raiz = MenuItem::query()
            ->visibles()
            ->whereNull('parent_id')
            ->with(['hijos' => fn ($q) => $q->visibles()])
            ->orderBy('orden')
            ->get();

        return response()->json([
            'data' => $raiz->map(fn (MenuItem $item) => $this->comoArray($item))->values(),
        ]);
    }

    /**
     * Los pasos que la Unidad edita en /admin/cronograma-admision.
     *
     * Devuelve null si no hay ninguno visible, para que el sitio pueda omitir
     * la seccion entera en vez de pintar un titulo sobre un hueco.
     */
    private function inscripcion(): ?array
    {
        $bloque = CronogramaAdmision::get();

        if (! $bloque || ! $bloque->is_visible) {
            return null;
        }

        $pasos = $bloque->pasos->where('is_visible', true)->sortBy('orden')->values();

        if ($pasos->isEmpty()) {
            return null;
        }

        return [
            'eyebrow' => $bloque->eyebrow,
            'titulo' => $bloque->titulo,
            'boton' => $bloque->boton_texto ? [
                'texto' => $bloque->boton_texto,
                'url' => $bloque->boton_url ?: '/',
            ] : null,
            'pasos' => $pasos->map(fn ($paso) => [
                'titulo' => $paso->titulo,
                'detalle' => $paso->detalle ?: null,
                'fecha' => $paso->fecha_display ?: null,
                'publico' => $paso->publico ?: null,
                'destacado' => (bool) $paso->destacado,
                // El SVG ya resuelto: el catalogo de iconos vive en el modelo,
                // y duplicarlo en el frontend seria mantenerlo dos veces.
                'icono_path' => $paso->icono_path,
            ])->all(),
        ];
    }

    private function comoArray(MenuItem $item): array
    {
        $hijos = $item->hijos->map(fn (MenuItem $hijo) => $this->comoArray($hijo))->values();

        return [
            'etiqueta' => $item->etiqueta,
            'enlace' => $item->enlace,
            'nueva_pestana' => (bool) $item->nueva_pestana,
            'hijos' => $hijos->all(),
        ];
    }
}
