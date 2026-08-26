<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ContentPage;
use App\Models\ContentSection;
use Illuminate\Http\JsonResponse;

/**
 * Contenido editable de las páginas largas: /nosotros, /tramites, /admision.
 *
 * Es lo que permite portar esas páginas sin copiar su texto a una plantilla.
 * Sin este endpoint, llevarlas a Astro significaría escribir la misión y la
 * visión del CERSEU dentro de un `.astro` — contenido de una unidad dentro del
 * código, el mismo fallo que se corrigió en los seeders, en la migración y en
 * el fallback de un controlador.
 */
class PaginaApiController extends Controller
{
    public function mostrar(string $slug): JsonResponse
    {
        $pagina = ContentPage::porSlug($slug);

        if (! $pagina) {
            return response()->json(['message' => "Página «{$slug}» no encontrada."], 404);
        }

        $secciones = $pagina->secciones
            ->where('is_visible', true)
            ->sortBy('orden')
            ->values();

        return response()->json([
            'data' => [
                'slug' => $pagina->slug,
                'titulo' => $pagina->titulo,
                'subtitulo' => $pagina->subtitulo,
                'secciones' => $secciones->map(fn (ContentSection $s) => [
                    'grupo' => $s->grupo,
                    'numeral' => $s->numeral,
                    'titulo' => $s->titulo,
                    // Cuerpo YA renderizado: el accesor resuelve los tokens de
                    // contacto ({{email_general}}, {{telefono}}…) contra
                    // Configuración y convierte las etiquetas de icono en SVG.
                    // Enviar `cuerpo` en crudo publicaría esos marcadores tal
                    // cual, y obligaría a reimplementar esa resolución en
                    // TypeScript.
                    'cuerpo' => $s->cuerpo_renderizado,
                ])->all(),
            ],
        ]);
    }
}
