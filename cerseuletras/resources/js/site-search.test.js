import { describe, it, expect, vi } from 'vitest';
import { crearBuscador, MIN_LONGITUD } from './site-search.js';

const URL_SUG = '/buscar/sugerencias';

/** Devuelve un `fetch` simulado que responde con los resultados indicados. */
function fetchFalso(resultados = [], { demora = 0 } = {}) {
    return vi.fn((_url, opciones) =>
        new Promise((resolve, reject) => {
            const t = setTimeout(
                () => resolve({ json: async () => ({ resultados }) }),
                demora
            );
            opciones?.signal?.addEventListener('abort', () => {
                clearTimeout(t);
                const e = new Error('abortada');
                e.name = 'AbortError';
                reject(e);
            });
        })
    );
}

describe('buscador global', () => {
    it('no consulta al servidor por debajo del mínimo de caracteres', async () => {
        const fetchImpl = fetchFalso();
        const b = crearBuscador({ urlSugerencias: URL_SUG, fetchImpl });

        b.q = 'a';
        await b.buscar();

        expect(fetchImpl).not.toHaveBeenCalled();
        expect(b.resultados).toEqual([]);
        expect(b.cargando).toBe(false);
    });

    it('consulta y guarda los resultados a partir del mínimo', async () => {
        const fetchImpl = fetchFalso([{ titulo: 'Diplomados', url: '/diplomados' }]);
        const b = crearBuscador({ urlSugerencias: URL_SUG, fetchImpl });

        b.q = 'di';
        await b.buscar();

        expect(b.q.trim().length).toBe(MIN_LONGITUD);
        expect(fetchImpl).toHaveBeenCalledOnce();
        expect(fetchImpl.mock.calls[0][0]).toContain('q=di');
        expect(b.resultados).toHaveLength(1);
        expect(b.cargando).toBe(false);
    });

    it('codifica el término en la URL', async () => {
        const fetchImpl = fetchFalso();
        const b = crearBuscador({ urlSugerencias: URL_SUG, fetchImpl });

        b.q = 'lingüística forense';
        await b.buscar();

        const url = fetchImpl.mock.calls[0][0];
        expect(url).toContain(encodeURIComponent('lingüística forense'));
        expect(url).not.toContain(' ');
    });

    it('cancela la petición anterior al escribir de nuevo', async () => {
        const fetchImpl = fetchFalso([{ titulo: 'Final', url: '/x' }], { demora: 30 });
        const b = crearBuscador({ urlSugerencias: URL_SUG, fetchImpl });

        b.q = 'dip';
        const primera = b.buscar();
        const abortada = b._abort;

        b.q = 'diplo';
        const segunda = b.buscar();

        await Promise.all([primera, segunda]);

        expect(abortada.signal.aborted).toBe(true);
        expect(fetchImpl).toHaveBeenCalledTimes(2);
        // La cancelada no debe apagar el indicador ni pisar los resultados.
        expect(b.resultados).toEqual([{ titulo: 'Final', url: '/x' }]);
        expect(b.cargando).toBe(false);
    });

    it('deja los resultados vacíos si la petición falla', async () => {
        const fetchImpl = vi.fn(() => Promise.reject(new Error('red caída')));
        const b = crearBuscador({ urlSugerencias: URL_SUG, fetchImpl });

        b.q = 'algo';
        await b.buscar();

        expect(b.resultados).toEqual([]);
        expect(b.cargando).toBe(false);
    });

    it('borrar el término limpia los resultados y cancela lo pendiente', async () => {
        const fetchImpl = fetchFalso([{ titulo: 'X', url: '/x' }]);
        const b = crearBuscador({ urlSugerencias: URL_SUG, fetchImpl });

        b.q = 'dip';
        await b.buscar();
        expect(b.resultados).toHaveLength(1);

        b.q = '';
        await b.buscar();
        expect(b.resultados).toEqual([]);
    });

    describe('navegación con teclado', () => {
        const conResultados = () => {
            const b = crearBuscador({ urlSugerencias: URL_SUG, fetchImpl: fetchFalso() });
            b.resultados = [{ url: '/a' }, { url: '/b' }, { url: '/c' }];
            return b;
        };

        it('avanza y da la vuelta al llegar al final', () => {
            const b = conResultados();
            b.mover(1); expect(b.activo).toBe(0);
            b.mover(1); expect(b.activo).toBe(1);
            b.mover(1); expect(b.activo).toBe(2);
            b.mover(1); expect(b.activo).toBe(0);   // vuelve al principio
        });

        it('retrocede desde el inicio hasta el último', () => {
            const b = conResultados();
            b.mover(-1);
            expect(b.activo).toBe(2);
        });

        it('no se mueve si no hay resultados', () => {
            const b = crearBuscador({ urlSugerencias: URL_SUG, fetchImpl: fetchFalso() });
            b.mover(1);
            expect(b.activo).toBe(-1);
        });

        it('Enter sobre un resultado devuelve su URL y frena el envío', () => {
            const b = conResultados();
            b.activo = 1;
            const ev = { preventDefault: vi.fn() };

            expect(b.irAlActivo(ev)).toBe('/b');
            expect(ev.preventDefault).toHaveBeenCalledOnce();
        });

        it('Enter sin selección deja pasar el formulario', () => {
            const b = conResultados();
            const ev = { preventDefault: vi.fn() };

            expect(b.irAlActivo(ev)).toBeNull();
            expect(ev.preventDefault).not.toHaveBeenCalled();
        });
    });

    it('cerrar cancela lo pendiente y olvida la selección', async () => {
        const fetchImpl = fetchFalso([], { demora: 30 });
        const b = crearBuscador({ urlSugerencias: URL_SUG, fetchImpl });

        b.abrir();
        b.q = 'dip';
        const p = b.buscar();
        b.activo = 1;
        const abort = b._abort;

        b.cerrar();
        await p;

        expect(b.open).toBe(false);
        expect(b.activo).toBe(-1);
        expect(abort.signal.aborted).toBe(true);
    });
});
