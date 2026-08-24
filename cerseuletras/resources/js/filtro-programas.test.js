// @vitest-environment jsdom
import { describe, it, expect, beforeEach } from 'vitest';
import { esVisible, coincideBusqueda, contarVisibles, montarFiltroProgramas } from './filtro-programas.js';

describe('reglas de visibilidad', () => {
    it('«todos» muestra cualquier tipo', () => {
        expect(esVisible('curso', 'todos')).toBe(true);
        expect(esVisible('taller', 'todos')).toBe(true);
    });

    it('un filtro concreto solo muestra su tipo', () => {
        expect(esVisible('taller', 'taller')).toBe(true);
        expect(esVisible('curso', 'taller')).toBe(false);
    });

    it('cuenta las tarjetas visibles', () => {
        const tipos = ['taller', 'taller', 'taller', 'curso'];

        expect(contarVisibles(tipos, 'taller')).toBe(3);
        expect(contarVisibles(tipos, 'curso')).toBe(1);
        expect(contarVisibles(tipos, 'todos')).toBe(4);
        expect(contarVisibles(tipos, 'inexistente')).toBe(0);
    });
});

describe('filtro montado sobre el DOM', () => {
    let grid, botones, vacio, api;

    beforeEach(() => {
        document.body.innerHTML = `
            <div id="filtros">
                <button data-filter="taller"></button>
                <button data-filter="curso"></button>
                <button data-filter="todos"></button>
            </div>
            <div id="grid">
                <div class="program-card" data-type="taller"></div>
                <div class="program-card" data-type="taller"></div>
                <div class="program-card" data-type="curso"></div>
            </div>
            <p id="vacio" class="hidden"></p>`;

        grid = document.getElementById('grid');
        botones = Array.from(document.querySelectorAll('#filtros button'));
        vacio = document.getElementById('vacio');
        api = montarFiltroProgramas({ grid, botones, mensajeVacio: vacio });
    });

    const visibles = () =>
        Array.from(grid.querySelectorAll('.program-card')).filter((c) => !c.classList.contains('hidden'));

    it('arranca en «taller», que es la oferta que el CERSEU destaca', () => {
        expect(visibles()).toHaveLength(2);
        expect(botones[0].getAttribute('aria-pressed')).toBe('true');
        expect(botones[1].getAttribute('aria-pressed')).toBe('false');
    });

    it('al pulsar otro filtro cambia lo visible y el botón activo', () => {
        botones[1].click();

        expect(visibles()).toHaveLength(1);
        expect(visibles()[0].dataset.type).toBe('curso');
        expect(botones[1].getAttribute('aria-pressed')).toBe('true');
        expect(botones[0].getAttribute('aria-pressed')).toBe('false');
    });

    it('«todos» vuelve a mostrarlas todas', () => {
        botones[2].click();
        expect(visibles()).toHaveLength(3);
    });

    it('el botón activo lleva las clases de resaltado y el resto no', () => {
        expect(botones[0].classList.contains('bg-unmsm-azul')).toBe(true);
        expect(botones[0].classList.contains('bg-white')).toBe(false);
        expect(botones[1].classList.contains('bg-white')).toBe(true);
    });

    it('muestra el mensaje cuando el filtro no deja nada', () => {
        expect(vacio.classList.contains('hidden')).toBe(true);

        // La grilla se queda sin cursos: el módulo existe pero no tiene oferta.
        grid.querySelector('[data-type="curso"]').dataset.type = 'taller';
        api.aplicar('curso');
        expect(visibles()).toHaveLength(0);
        expect(vacio.classList.contains('hidden')).toBe(false);
    });

    it('el mensaje se oculta al volver a un filtro con resultados', () => {
        grid.querySelector('[data-type="curso"]').dataset.type = 'taller';
        api.aplicar('curso');
        api.aplicar('todos');
        expect(vacio.classList.contains('hidden')).toBe(true);
    });

    it('sin grid no revienta', () => {
        expect(montarFiltroProgramas({ grid: null, botones: [] })).toBeNull();
    });
});

describe('búsqueda por texto', () => {
    const carta = { title: 'Maestría en Lingüística', desc: 'Estudio del lenguaje' };

    it('un término vacío no descarta nada', () => {
        expect(coincideBusqueda(carta, '')).toBe(true);
        expect(coincideBusqueda(carta, '   ')).toBe(true);
    });

    it('busca en el título y en la descripción, sin distinguir mayúsculas', () => {
        expect(coincideBusqueda(carta, 'LINGÜÍSTICA')).toBe(true);
        expect(coincideBusqueda(carta, 'lenguaje')).toBe(true);
        expect(coincideBusqueda(carta, 'química')).toBe(false);
    });

    it('tolera tarjetas sin datos de búsqueda', () => {
        expect(coincideBusqueda(undefined, 'algo')).toBe(false);
        expect(coincideBusqueda({}, '')).toBe(true);
    });
});

describe('variante de listado: filtro + buscador', () => {
    let grid, botones, noResults, input;

    beforeEach(() => {
        document.body.innerHTML = `
            <button data-filter="todos"></button>
            <button data-filter="taller"></button>
            <button data-filter="curso"></button>
            <input id="q">
            <div id="grid">
                <div class="program-card" data-type="taller" data-title="Lingüística" data-desc="lenguaje"></div>
                <div class="program-card" data-type="curso" data-title="Literatura" data-desc="letras"></div>
            </div>
            <div id="noResults" class="hidden"></div>`;

        grid = document.getElementById('grid');
        botones = Array.from(document.querySelectorAll('button'));
        noResults = document.getElementById('noResults');
        input = document.getElementById('q');

        montarFiltroProgramas({
            grid,
            botones,
            mensajeVacio: noResults,
            campoBusqueda: input,
            filtroInicial: 'todos',
            claseOculta: 'hidden-filter',
            clasesActivo: ['bg-white', 'text-unmsm-guinda', 'shadow-sm'],
            clasesInactivo: ['text-gray-500', 'hover:bg-gray-200'],
            claseInactivoExtra: null,
            ocultarGridVacio: true,
        });
    });

    const visibles = () =>
        Array.from(grid.querySelectorAll('.program-card'))
            .filter((c) => !c.classList.contains('hidden-filter'));

    it('usa la clase de ocultado propia de esta vista', () => {
        botones[1].click();
        expect(visibles()).toHaveLength(1);
        expect(grid.querySelector('[data-type="curso"]').classList.contains('hidden-filter')).toBe(true);
        expect(grid.querySelector('[data-type="curso"]').classList.contains('hidden')).toBe(false);
    });

    it('el buscador acota sobre el filtro activo', () => {
        input.value = 'letras';
        input.dispatchEvent(new Event('input'));

        expect(visibles()).toHaveLength(1);
        expect(visibles()[0].dataset.type).toBe('curso');
    });

    it('filtro y búsqueda se combinan, no se pisan', () => {
        botones[1].click();          // maestrías
        input.value = 'letras';      // texto que solo casa con el curso
        input.dispatchEvent(new Event('input'));

        expect(visibles()).toHaveLength(0);
        expect(noResults.classList.contains('hidden')).toBe(false);
        expect(grid.classList.contains('hidden')).toBe(true);
    });

    it('la grilla reaparece al limpiar la búsqueda', () => {
        input.value = 'nada';
        input.dispatchEvent(new Event('input'));
        expect(grid.classList.contains('hidden')).toBe(true);

        input.value = '';
        input.dispatchEvent(new Event('input'));
        expect(grid.classList.contains('hidden')).toBe(false);
        expect(visibles()).toHaveLength(2);
    });

    it('aplica el juego de clases de esta vista al botón activo', () => {
        expect(botones[0].classList.contains('bg-white')).toBe(true);
        expect(botones[1].classList.contains('text-gray-500')).toBe(true);
    });
});
