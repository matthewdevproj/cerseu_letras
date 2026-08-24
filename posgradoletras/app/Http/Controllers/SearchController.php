<?php

namespace App\Http\Controllers;

use App\Models\Docente;
use App\Models\Evento;
use App\Models\Informativo;
use App\Models\Programa;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

/**
 * Buscador global del portal (Obs. N.º 5).
 *
 * Busca sobre los contenidos que ya viven en la base de datos —diplomados,
 * maestrías, doctorados, docentes, eventos y recursos informativos— más un
 * índice de páginas institucionales fijas (admisión, trámites, etc.).
 *
 * No usa ningún motor externo: el volumen de contenido del portal es pequeño
 * (decenas de registros), así que el índice se construye en memoria y se
 * cachea, evitando consultas por cada pulsación de tecla.
 */
class SearchController extends Controller
{
    /** Longitud mínima del término para lanzar una búsqueda. */
    private const MIN_LENGTH = 2;

    /** Cuántos resultados devuelve el desplegable de la cabecera. */
    private const SUGGEST_LIMIT = 8;

    /**
     * Página completa de resultados.
     */
    public function index(Request $request)
    {
        $q = trim((string) $request->query('q', ''));
        $resultados = $this->buscar($q);

        return view('search.index', [
            'q' => $q,
            'resultados' => $resultados,
            'porCategoria' => $resultados->groupBy('categoria'),
        ]);
    }

    /**
     * Resultados en JSON para el desplegable de la cabecera.
     */
    public function suggest(Request $request): JsonResponse
    {
        $q = trim((string) $request->query('q', ''));
        $resultados = $this->buscar($q)->take(self::SUGGEST_LIMIT)->values();

        return response()->json([
            'query' => $q,
            'total' => $resultados->count(),
            'resultados' => $resultados,
        ]);
    }

    /**
     * Busca el término en el índice y devuelve los resultados por relevancia.
     */
    private function buscar(string $q)
    {
        if (Str::length($q) < self::MIN_LENGTH) {
            return collect();
        }

        $terminos = collect(preg_split('/\s+/', $this->normalizar($q), -1, PREG_SPLIT_NO_EMPTY));

        return collect($this->indice())
            ->map(function (array $item) use ($terminos) {
                $item['score'] = $this->puntuar($item, $terminos);

                return $item;
            })
            ->filter(fn (array $item) => $item['score'] > 0)
            ->sortByDesc('score')
            // Se descartan los campos internos de puntuación: lo que sale de
            // aquí se serializa tal cual en la respuesta JSON.
            ->map(fn (array $item) => [
                'titulo' => $item['titulo'],
                'descripcion' => $item['descripcion'],
                'url' => $item['url'],
                'categoria' => $item['categoria'],
            ])
            ->values();
    }

    /**
     * Relevancia: un acierto en el título pesa mucho más que uno en el cuerpo,
     * y coincidir con el inicio del título más que hacerlo por el medio.
     * Todos los términos deben aparecer en algún campo (búsqueda tipo AND).
     */
    private function puntuar(array $item, $terminos): int
    {
        $titulo = $item['_titulo_norm'];
        $cuerpo = $item['_cuerpo_norm'];
        $score = 0;

        foreach ($terminos as $termino) {
            $enTitulo = str_contains($titulo, $termino);
            $enCuerpo = str_contains($cuerpo, $termino);

            if (!$enTitulo && !$enCuerpo) {
                return 0; // falta un término: no es un resultado válido
            }

            if ($enTitulo) {
                $score += str_starts_with($titulo, $termino) ? 60 : 30;
                // Coincidencia de palabra completa dentro del título.
                if (preg_match('/\b' . preg_quote($termino, '/') . '\b/', $titulo)) {
                    $score += 15;
                }
            }

            if ($enCuerpo) {
                $score += 5;
            }
        }

        return $score + ($item['peso'] ?? 0);
    }

    /**
     * Índice de contenidos buscables, cacheado 30 minutos.
     *
     * Los cachés de contenido del panel (programas, eventos…) no invalidan este
     * índice, por eso la ventana es corta: un cambio en el panel se refleja en
     * el buscador como máximo media hora después.
     */
    private function indice(): array
    {
        return Cache::remember('search_index', 1800, function () {
            $items = [];

            // Oferta: talleres primero (la prioritaria), luego cursos. El `peso`
            // desempata entre resultados igual de buenos.
            foreach (Programa::visibles()->get() as $programa) {
                $esTaller = $programa->esTaller();

                $items[] = $this->item(
                    titulo: $programa->titulo_completo,
                    descripcion: Str::limit(strip_tags((string) $programa->sumilla), 160),
                    url: $programa->url,
                    categoria: $programa->tipoOferta()?->plural() ?? 'Oferta académica',
                    peso: $esTaller ? 25 : 15,
                    // El grado va en el cuerpo para que "taller" o "curso"
                    // encuentren también las fichas, no solo su página índice.
                    extra: [$programa->grado, $programa->mencion, $programa->modalidad],
                );
            }

            // Docentes
            foreach (Docente::activos()->get() as $docente) {
                $items[] = $this->item(
                    titulo: $docente->nombre_completo,
                    descripcion: Str::limit(strip_tags((string) $docente->biografia), 160),
                    url: route('profesores.show', $docente->slug),
                    categoria: 'Docentes',
                    peso: 5,
                    extra: [$docente->grado],
                );
            }

            // Eventos y actividades
            foreach (Evento::activos()->get() as $evento) {
                $items[] = $this->item(
                    titulo: $evento->titulo,
                    descripcion: Str::limit(strip_tags((string) $evento->descripcion), 160),
                    url: $evento->url ?: route('eventos.index'),
                    categoria: 'Noticias y actividades',
                    peso: 5,
                );
            }

            // Recursos informativos
            foreach (Informativo::all() as $informativo) {
                $items[] = $this->item(
                    titulo: $informativo->titulo,
                    descripcion: $informativo->categoria,
                    url: $informativo->url ?: route('informativos.index'),
                    categoria: 'Información institucional',
                    peso: 0,
                );
            }

            // Páginas fijas del portal.
            foreach ($this->paginas() as $pagina) {
                $items[] = $this->item(...$pagina);
            }

            return $items;
        });
    }

    /**
     * Páginas institucionales que no viven en la base de datos pero deben poder
     * encontrarse (admisión, trámites, directorio…).
     */
    private function paginas(): array
    {
        return [
            [
                'titulo' => 'Talleres',
                'descripcion' => 'Oferta vigente de talleres del CERSEU.',
                'url' => route('talleres.index'),
                'categoria' => 'Talleres',
                'peso' => 70,
                'extra' => ['taller', 'formación corta', 'especialización'],
            ],
            [
                'titulo' => 'Admisión de talleres',
                'descripcion' => 'Requisitos, cronograma, pago e inscripción para los talleres.',
                'url' => route('talleres.admision'),
                'categoria' => 'Admisión',
                'peso' => 60,
                'extra' => ['postular', 'inscripción', 'convocatoria', 'requisitos'],
            ],
            [
                'titulo' => 'Cursos',
                'descripcion' => 'Oferta vigente de cursos del CERSEU.',
                'url' => route('cursos.index'),
                'categoria' => 'Cursos',
                'peso' => 70,
                'extra' => ['curso', 'formación continua', 'especialización'],
            ],
            [
                'titulo' => 'Admisión de cursos',
                'descripcion' => 'Requisitos, cronograma, pago e inscripción para los cursos.',
                'url' => route('cursos.admision'),
                'categoria' => 'Admisión',
                'peso' => 60,
                'extra' => ['postular', 'inscripción', 'convocatoria', 'requisitos'],
            ],
            [
                'titulo' => 'Proceso de Admisión',
                'descripcion' => 'Proceso de admisión del CERSEU: requisitos y etapas.',
                'url' => route('admision'),
                'categoria' => 'Admisión',
                'peso' => 55,
                'extra' => ['postular', 'inscripción', 'convocatoria', 'vacantes', 'examen'],
            ],
            [
                'titulo' => 'Trámites',
                'descripcion' => 'Obtención del grado de Magíster y de Doctor: requisitos y pasos.',
                'url' => route('tramites'),
                'categoria' => 'Trámites',
                'peso' => 60,
                'extra' => ['grado', 'magíster', 'doctor', 'titulación', 'sustentación', 'tesis'],
            ],
            [
                'titulo' => 'Cronograma Académico',
                'descripcion' => 'Calendario de actividades académicas del semestre vigente.',
                'url' => route('cronograma'),
                'categoria' => 'Información institucional',
                'peso' => 20,
                'extra' => ['calendario', 'fechas', 'matrícula'],
            ],
            [
                'titulo' => 'Quiénes somos',
                'descripcion' => 'Misión, visión e historia de la Unidad de Posgrado.',
                'url' => route('nosotros'),
                'categoria' => 'Información institucional',
                'peso' => 15,
                'extra' => ['misión', 'visión', 'institucional', 'nosotros'],
            ],
            [
                'titulo' => 'Directorio de Posgrado',
                'descripcion' => 'Autoridades y personal de la Unidad de Posgrado.',
                'url' => route('directorio'),
                'categoria' => 'Información institucional',
                'peso' => 15,
                'extra' => ['contacto', 'autoridades', 'teléfono', 'correo'],
            ],
            [
                'titulo' => 'Documentos y Recursos',
                'descripcion' => 'Reglamentos, formatos y documentos informativos.',
                'url' => route('informativos.index'),
                'categoria' => 'Información institucional',
                'peso' => 15,
                'extra' => ['reglamento', 'formato', 'documento', 'descargas'],
            ],
            [
                'titulo' => 'Eventos',
                'descripcion' => 'Conferencias, coloquios y actividades del Posgrado.',
                'url' => route('eventos.index'),
                'categoria' => 'Noticias y actividades',
                'peso' => 15,
                'extra' => ['conferencia', 'coloquio', 'actividad', 'agenda'],
            ],
            [
                'titulo' => 'Testimonios',
                'descripcion' => 'Experiencias de egresados y estudiantes del Posgrado.',
                'url' => route('testimonios.index'),
                'categoria' => 'Noticias y actividades',
                'peso' => 10,
            ],
        ];
    }

    /**
     * Construye una entrada del índice, precalculando los campos normalizados
     * para no repetir el trabajo en cada búsqueda.
     */
    private function item(
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
            '_titulo_norm' => $this->normalizar($titulo),
            '_cuerpo_norm' => $this->normalizar($cuerpo),
        ];
    }

    /**
     * Minúsculas y sin tildes, para que "admision" encuentre "Admisión".
     */
    private function normalizar(string $texto): string
    {
        return Str::lower(Str::ascii($texto));
    }

    /**
     * Invalida el índice (para llamar desde el panel cuando cambie contenido).
     */
    public static function clearCache(): void
    {
        Cache::forget('search_index');
    }
}
