<?php

namespace App\Http\Controllers;

use App\Support\IndiceDeBusqueda;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

/**
 * Buscador global del portal (Obs. N.º 5).
 *
 * Qué es buscable ya no se decide aquí: eso vive en IndiceDeBusqueda, que
 * comparte con el sitio en Astro. Este controlador se queda con lo que sí es
 * suyo —puntuar y pintar la página de resultados—.
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

        $terminos = collect(preg_split(
            '/\s+/',
            IndiceDeBusqueda::normalizar($q),
            -1,
            PREG_SPLIT_NO_EMPTY
        ));

        return collect(IndiceDeBusqueda::items())
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
}
