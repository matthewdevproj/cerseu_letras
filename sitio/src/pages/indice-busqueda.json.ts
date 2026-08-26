import type { APIRoute } from 'astro';
import { obtenerIndiceBuscador } from '../lib/api';

/**
 * El índice del buscador, congelado en el build como un fichero más del sitio.
 *
 * Se pide a la API una vez, al construir, y se sirve como estático: el
 * navegador lo descarga desde el mismo origen que el resto del sitio —sin
 * CORS, sin depender de que Laravel esté en pie— y busca en local. El contenido
 * solo cambia cuando la Unidad publica algo, y eso ya dispara una
 * reconstrucción, así que el índice se regenera exactamente cuando debe.
 */
export const GET: APIRoute = async () => {
    const indice = await obtenerIndiceBuscador();

    return new Response(JSON.stringify(indice), {
        headers: { 'Content-Type': 'application/json; charset=utf-8' },
    });
};
