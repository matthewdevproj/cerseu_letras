<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Support\IndiceDeBusqueda;
use Illuminate\Http\JsonResponse;

/**
 * El índice de búsqueda, para que el sitio estático lo descargue una vez.
 *
 * Aquí no se busca: se entrega la lista entera y busca el navegador. Un sitio
 * estático no tiene servidor al que preguntar, y llamar a la API por cada
 * pulsación de tecla sería una petición por letra contra un contenido que solo
 * cambia cuando la Unidad publica algo. Con decenas de entradas el índice pesa
 * unas pocas decenas de kB: se descarga una vez, al primer uso del buscador, y
 * a partir de ahí los resultados salen sin red de por medio.
 *
 * Los campos normalizados van incluidos —no se recalculan en el navegador—
 * para que buscar «admision» siga encontrando «Admisión».
 */
class BuscadorApiController extends Controller
{
    public function index(): JsonResponse
    {
        $items = array_map(fn (array $item) => [
            'titulo' => $item['titulo'],
            'descripcion' => $item['descripcion'],
            'url' => $item['url'],
            'categoria' => $item['categoria'],
            'peso' => $item['peso'],
            // Nombres cortos: se repiten en cada entrada del índice y el
            // fichero entero viaja al navegador.
            't' => $item['_titulo_norm'],
            'c' => $item['_cuerpo_norm'],
        ], IndiceDeBusqueda::items());

        return response()->json(['data' => $items]);
    }
}
