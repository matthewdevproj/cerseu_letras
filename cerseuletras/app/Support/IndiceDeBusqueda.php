<?php

namespace App\Support;

use App\Models\Docente;
use App\Models\Evento;
use App\Models\Informativo;
use App\Models\Programa;
use App\Models\TipoOferta;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

/**
 * Qué es buscable en el portal.
 *
 * Antes vivía dentro de SearchController, que además puntuaba y pintaba. Al
 * pasar el buscador a Astro haría falta una segunda lista de contenidos, y dos
 * listas se separan: se añade un tipo de oferta y aparece en un buscador y no
 * en el otro. Aquí queda una sola, y cada frontend decide cómo la usa.
 *
 * Las URL son rutas relativas, no absolutas. `route()` devuelve la dirección de
 * ESTA aplicación, y en un frontend desacoplado eso saca al visitante del sitio
 * que está viendo. Una ruta interna es una ruta: cada frontend la resuelve
 * contra su propio origen. En Blade el resultado es idéntico al de antes.
 */
class IndiceDeBusqueda
{
    /** Media hora: el índice se rehace solo tras un cambio en el panel. */
    private const TTL = 1800;

    /**
     * Entradas buscables, con los campos normalizados ya calculados.
     *
     * @return array<int, array<string, mixed>>
     */
    public static function items(): array
    {
        return Cache::remember('search_index', self::TTL, function () {
            $items = [];

            // Oferta académica. El `peso` desempata entre resultados que
            // puntúan igual; los talleres van por delante por ser la oferta
            // que el CERSEU quiere destacar.
            foreach (Programa::visibles()->get() as $programa) {
                $items[] = self::item(
                    titulo: $programa->titulo_completo,
                    descripcion: Str::limit(strip_tags((string) $programa->sumilla), 160),
                    url: self::ruta($programa->url),
                    categoria: $programa->tipoOferta()?->plural() ?? 'Oferta académica',
                    peso: $programa->esTaller() ? 25 : 15,
                    // El grado va en el cuerpo para que «taller» o «curso»
                    // encuentren también las fichas, no solo su página índice.
                    extra: [$programa->grado, $programa->mencion, $programa->modalidad],
                );
            }

            foreach (Docente::activos()->with('programas')->get() as $docente) {
                $items[] = self::item(
                    titulo: $docente->nombre_completo,
                    // Ningún docente tiene biografía cargada todavía, así que
                    // lo que identifica a cada uno es lo que dicta. También
                    // hace que buscar un curso encuentre a quien lo enseña.
                    descripcion: Str::limit($docente->programas->pluck('nombre')->implode(' · '), 160),
                    url: '/profesores/' . $docente->slug,
                    categoria: 'Docentes',
                    peso: 5,
                    extra: [$docente->grado],
                );
            }

            foreach (Evento::activos()->get() as $evento) {
                $items[] = self::item(
                    titulo: $evento->titulo,
                    descripcion: Str::limit(strip_tags((string) $evento->descripcion), 160),
                    // Un evento con enlace propio apunta fuera del sitio; sin
                    // él, al listado, que es donde se puede leer.
                    url: $evento->url ?: '/eventos',
                    categoria: 'Noticias y actividades',
                    peso: 5,
                );
            }

            foreach (Informativo::all() as $informativo) {
                $items[] = self::item(
                    titulo: $informativo->titulo,
                    descripcion: $informativo->categoria,
                    url: $informativo->url ?: '/informativos',
                    categoria: 'Información institucional',
                    peso: 0,
                );
            }

            foreach (self::paginas() as $pagina) {
                $items[] = self::item(...$pagina);
            }

            return $items;
        });
    }

    /**
     * Páginas fijas: no están en la base de datos, pero son lo que más se
     * busca. «admisión» debe llevar a la página de admisión antes que a
     * cualquier ficha que mencione la palabra, y de eso se ocupa el peso.
     *
     * @return array<int, array<string, mixed>>
     */
    private static function paginas(): array
    {
        $paginas = [];

        // Una entrada por tipo de oferta, generadas desde el enum: añadir un
        // tipo no debe obligar a acordarse de este archivo.
        foreach (TipoOferta::cases() as $tipo) {
            $paginas[] = [
                'titulo' => $tipo->plural(),
                'descripcion' => 'Oferta vigente de ' . Str::lower($tipo->plural()) . ' del CERSEU.',
                'url' => '/' . $tipo->slug(),
                'categoria' => $tipo->plural(),
                'peso' => 70,
                'extra' => [$tipo->singular(), 'formación continua', 'oferta'],
            ];

            $paginas[] = [
                'titulo' => 'Admisión de ' . Str::lower($tipo->plural()),
                'descripcion' => 'Requisitos, convocatorias, pago e inscripción.',
                'url' => '/' . $tipo->slug() . '/admision',
                'categoria' => 'Admisión',
                'peso' => 60,
                'extra' => ['postular', 'inscripción', 'convocatoria', 'requisitos', 'matrícula'],
            ];
        }

        return array_merge($paginas, [
            [
                'titulo' => 'Proceso de Admisión',
                'descripcion' => 'Cómo inscribirse en la oferta del CERSEU: requisitos y etapas.',
                'url' => '/admision',
                'categoria' => 'Admisión',
                'peso' => 55,
                'extra' => ['postular', 'inscripción', 'convocatoria', 'vacantes'],
            ],
            [
                'titulo' => 'Trámites',
                'descripcion' => 'Constancias, certificados y demás trámites: requisitos y pasos.',
                'url' => '/tramites',
                'categoria' => 'Trámites',
                'peso' => 60,
                'extra' => ['constancia', 'certificado', 'requisitos', 'pago'],
            ],
            [
                'titulo' => 'Cronograma Académico',
                'descripcion' => 'Calendario de actividades académicas del semestre vigente.',
                'url' => '/cronograma',
                'categoria' => 'Información institucional',
                'peso' => 20,
                'extra' => ['calendario', 'fechas', 'matrícula'],
            ],
            [
                'titulo' => 'Quiénes somos',
                'descripcion' => 'Misión, visión y valores del CERSEU.',
                'url' => '/nosotros',
                'categoria' => 'Información institucional',
                'peso' => 15,
                'extra' => ['misión', 'visión', 'institucional', 'nosotros'],
            ],
            [
                'titulo' => 'Plana docente',
                'descripcion' => 'Quiénes dictan los talleres, cursos y especializaciones.',
                'url' => '/plana-docente',
                'categoria' => 'Docentes',
                'peso' => 30,
                'extra' => ['profesores', 'docentes', 'plana'],
            ],
            [
                'titulo' => 'Directorio',
                'descripcion' => 'Cómo contactar con el CERSEU.',
                'url' => '/directorio',
                'categoria' => 'Información institucional',
                'peso' => 15,
                'extra' => ['contacto', 'teléfono', 'correo', 'dirección'],
            ],
            [
                'titulo' => 'Documentos y Recursos',
                'descripcion' => 'Reglamentos, formatos y documentos informativos.',
                'url' => '/informativos',
                'categoria' => 'Información institucional',
                'peso' => 15,
                'extra' => ['reglamento', 'formato', 'documento', 'descargas'],
            ],
            [
                'titulo' => 'Eventos',
                'descripcion' => 'Conferencias, coloquios y actividades del CERSEU.',
                'url' => '/eventos',
                'categoria' => 'Noticias y actividades',
                'peso' => 15,
                'extra' => ['conferencia', 'coloquio', 'actividad', 'agenda'],
            ],
        ]);
    }

    /**
     * De la URL absoluta que devuelve `route()` a la ruta que la contiene.
     */
    private static function ruta(string $url): string
    {
        return parse_url($url, PHP_URL_PATH) ?: '/';
    }

    /**
     * Una entrada, con los campos normalizados precalculados para no repetir
     * el trabajo en cada búsqueda.
     *
     * @param  array<int, string|null>  $extra
     * @return array<string, mixed>
     */
    private static function item(
        string $titulo,
        ?string $descripcion,
        string $url,
        string $categoria,
        int $peso = 0,
        array $extra = [],
    ): array {
        $descripcion = trim((string) $descripcion);
        $cuerpo = trim($descripcion . ' ' . implode(' ', array_filter($extra)));

        return [
            'titulo' => $titulo,
            'descripcion' => $descripcion,
            'url' => $url,
            'categoria' => $categoria,
            'peso' => $peso,
            '_titulo_norm' => self::normalizar($titulo),
            '_cuerpo_norm' => self::normalizar($cuerpo),
        ];
    }

    /** Minúsculas y sin tildes, para que «admision» encuentre «Admisión». */
    public static function normalizar(string $texto): string
    {
        return Str::lower(Str::ascii($texto));
    }

    /** Invalida el índice cuando cambia el contenido del panel. */
    public static function olvidar(): void
    {
        Cache::forget('search_index');
    }
}
