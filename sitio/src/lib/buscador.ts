import type { EntradaBuscador } from './api';

/** Longitud mínima del término para lanzar una búsqueda. */
export const MINIMO = 2;

export type Resultado = EntradaBuscador & { score: number };

/** Minúsculas y sin tildes, igual que hace el servidor con el índice. */
export function normalizar(texto: string): string {
    return texto
        .toLowerCase()
        .normalize('NFD')
        .replace(/\p{Diacritic}/gu, '');
}

/**
 * Relevancia: un acierto en el título pesa mucho más que uno en el cuerpo, y
 * coincidir con el inicio del título más que hacerlo por el medio. Todos los
 * términos deben aparecer en algún campo (búsqueda tipo AND), para que añadir
 * una palabra siempre acote y nunca amplíe.
 *
 * Es la misma fórmula que aplica el buscador en PHP. Mientras convivan los dos
 * sitios hay dos copias; al retirar Blade queda solo esta.
 */
function puntuar(entrada: EntradaBuscador, terminos: string[]): number {
    let score = 0;

    for (const termino of terminos) {
        const enTitulo = entrada.t.includes(termino);
        const enCuerpo = entrada.c.includes(termino);

        if (!enTitulo && !enCuerpo) {
            return 0; // falta un término: no es un resultado válido
        }

        if (enTitulo) {
            score += entrada.t.startsWith(termino) ? 60 : 30;
            // Palabra completa dentro del título: «tesis» debe puntuar más en
            // «Redacción de Tesis» que en cualquier título que la contenga
            // como fragmento.
            if (new RegExp(`\\b${termino.replace(/[.*+?^${}()|[\]\\]/g, '\\$&')}\\b`).test(entrada.t)) {
                score += 15;
            }
        }

        if (enCuerpo) {
            score += 5;
        }
    }

    return score + entrada.peso;
}

/**
 * Busca en el índice y devuelve los resultados ordenados por relevancia.
 */
export function buscar(indice: EntradaBuscador[], consulta: string): Resultado[] {
    const limpia = consulta.trim();

    if (limpia.length < MINIMO) {
        return [];
    }

    const terminos = normalizar(limpia).split(/\s+/).filter(Boolean);

    return indice
        .map((entrada) => ({ ...entrada, score: puntuar(entrada, terminos) }))
        .filter((entrada) => entrada.score > 0)
        .sort((a, b) => b.score - a.score);
}

/**
 * Agrupa por categoría conservando el orden de relevancia: la categoría cuyo
 * mejor resultado puntúa más alto va primero.
 */
export function porCategoria(resultados: Resultado[]): [string, Resultado[]][] {
    const grupos = new Map<string, Resultado[]>();

    for (const resultado of resultados) {
        const grupo = grupos.get(resultado.categoria);
        if (grupo) {
            grupo.push(resultado);
        } else {
            grupos.set(resultado.categoria, [resultado]);
        }
    }

    return [...grupos.entries()];
}

/**
 * Descarga el índice una sola vez por pestaña.
 *
 * Se comparte entre el buscador de la cabecera y la página de resultados: si
 * alguien escribe en la cabecera y luego pulsa Enter, el índice ya está en
 * memoria y la página de resultados no vuelve a pedirlo. Las llamadas
 * simultáneas comparten la misma promesa en vez de disparar dos descargas.
 */
let pendiente: Promise<EntradaBuscador[]> | null = null;

export function cargarIndice(url = '/indice-busqueda.json'): Promise<EntradaBuscador[]> {
    if (!pendiente) {
        pendiente = fetch(url)
            .then((res) => {
                if (!res.ok) {
                    throw new Error(`El índice respondió ${res.status}`);
                }
                return res.json();
            })
            .catch((error) => {
                // Sin esto, un fallo de red dejaría la promesa fallida cacheada
                // para siempre y el buscador no se recuperaría al reintentar.
                pendiente = null;
                throw error;
            });
    }

    return pendiente;
}
