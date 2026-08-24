// @vitest-environment jsdom
import { describe, it, expect, beforeEach, vi } from 'vitest';
import { llenarSelect, renombrarCampo, montarSelectorGeografico } from './selector-geografico.js';

const PAISES = [
    { nombre: 'Perú', codigo: 'PE' },
    { nombre: 'España', codigo: 'ES' },
    { nombre: 'Ciudad del Vaticano', codigo: 'VA' },
    { nombre: 'Japón', codigo: 'JP' },
];

/** `fetch` falso que responde según la ruta pedida. */
function fetchFalso({ regiones = {}, fallar = null } = {}) {
    return vi.fn(async (url) => {
        if (fallar && url.includes(fallar)) {
            return { ok: false, status: 500, json: async () => ({}) };
        }
        if (url.includes('/regiones')) {
            const codigo = url.match(/paises\/([^/]+)\/regiones/)[1];
            const etiquetas = { PE: 'Departamento', ES: 'Provincia', JP: 'Prefectura' };
            return {
                ok: true, status: 200,
                json: async () => ({ regiones: regiones[codigo] ?? [], etiqueta: etiquetas[codigo] ?? 'Región' }),
            };
        }
        return { ok: true, status: 200, json: async () => ({ paises: PAISES }) };
    });
}

describe('llenado de un select', () => {
    beforeEach(() => {
        document.body.innerHTML = '<select id="s"></select>';
    });

    const select = () => document.getElementById('s');

    it('pone el placeholder primero y luego las opciones', () => {
        llenarSelect(select(), PAISES, { placeholder: 'Elige…' });

        const opciones = [...select().options];
        expect(opciones[0].textContent).toBe('Elige…');
        expect(opciones[0].value).toBe('');
        expect(opciones).toHaveLength(PAISES.length + 1);   // + el placeholder
    });

    it('usa código como valor y nombre como texto', () => {
        llenarSelect(select(), PAISES);

        expect(select().options[1].value).toBe('PE');
        expect(select().options[1].textContent).toBe('Perú');
    });

    it('acepta una lista de textos simples', () => {
        llenarSelect(select(), ['Lima', 'Cusco']);

        expect(select().options[1].value).toBe('Lima');
        expect(select().options[1].textContent).toBe('Lima');
    });

    it('conserva el valor elegido si sigue en la lista', () => {
        // Al volver del servidor con un error de validación no debe perderse.
        llenarSelect(select(), PAISES, { valor: 'ES' });

        expect(select().value).toBe('ES');
    });

    it('descarta un valor que ya no existe', () => {
        llenarSelect(select(), PAISES, { valor: 'XXX' });

        expect(select().value).toBe('');
    });

    it('vaciar la lista no revienta', () => {
        expect(() => llenarSelect(select(), [])).not.toThrow();
        expect(llenarSelect(null, PAISES)).toBeNull();
    });
});

describe('selector de país y región', () => {
    let paisEl, regionEl, libreEl;

    beforeEach(() => {
        document.body.innerHTML = `
            <form>
                <select id="pais" name="pais"></select>
                <div data-campo>
                    <select id="region" name="region"></select>
                    <label for="region">Región&nbsp;<span>*</span></label>
                </div>
                <div data-campo class="hidden">
                    <input id="region-libre" name="region" disabled>
                    <label for="region-libre">Región&nbsp;<span>*</span></label>
                </div>
            </form>`;
        paisEl = document.getElementById('pais');
        regionEl = document.getElementById('region');
        libreEl = document.getElementById('region-libre');
    });

    const montar = (opciones = {}) =>
        montarSelectorGeografico({
            selectPais: paisEl,
            selectRegion: regionEl,
            inputRegionLibre: libreEl,
            ...opciones,
        });

    it('carga los países al arrancar', async () => {
        const fetchImpl = fetchFalso({ regiones: { PE: ['Lima'] } });
        await montar({ fetchImpl }).listo;

        expect([...paisEl.options].map((o) => o.textContent)).toContain('Perú');
    });

    it('carga las regiones del país preseleccionado', async () => {
        const fetchImpl = fetchFalso({ regiones: { PE: ['Áncash', 'Cusco', 'Lima'] } });
        await montar({ fetchImpl, paisInicial: 'PE' }).listo;

        expect([...regionEl.options].map((o) => o.value)).toEqual(['', 'Áncash', 'Cusco', 'Lima']);
    });

    it('cambiar de país recarga sus regiones', async () => {
        const fetchImpl = fetchFalso({ regiones: { PE: ['Lima'], ES: ['Madrid', 'Sevilla'] } });
        const selector = montar({ fetchImpl, paisInicial: 'PE' });
        await selector.listo;

        paisEl.value = 'ES';
        await selector.cargarRegiones('ES');

        expect([...regionEl.options].map((o) => o.value)).toEqual(['', 'Madrid', 'Sevilla']);
    });

    it('un país sin regiones deja escribirla a mano', async () => {
        // hay países que no tienen división administrativa: un
        // desplegable vacío sería un callejón sin salida.
        const fetchImpl = fetchFalso({ regiones: { VA: [] } });
        await montar({ fetchImpl, paisInicial: 'VA' }).listo;

        expect(libreEl.disabled).toBe(false);
        expect(regionEl.disabled).toBe(true);
        expect(libreEl.closest('[data-campo]').classList.contains('hidden')).toBe(false);
    });

    it('solo viaja el campo de región visible', async () => {
        // Con los dos activos el servidor recibe `region` dos veces.
        const fetchImpl = fetchFalso({ regiones: { PE: ['Lima'] } });
        await montar({ fetchImpl, paisInicial: 'PE' }).listo;

        expect(regionEl.disabled).toBe(false);
        expect(libreEl.disabled).toBe(true);
    });

    it('si el sitio no responde, el formulario sigue usable a mano', async () => {
        const fetchImpl = fetchFalso({ fallar: '/geografia/v2/paises' });
        await montar({ fetchImpl }).listo;

        expect(libreEl.disabled).toBe(false);
    });

    it('si fallan solo las regiones, se puede escribirla', async () => {
        const fetchImpl = fetchFalso({ fallar: '/regiones' });
        await montar({ fetchImpl, paisInicial: 'PE' }).listo;

        expect(libreEl.disabled).toBe(false);
    });

    it('sin país elegido la región queda a la espera', async () => {
        const fetchImpl = fetchFalso();
        const selector = montar({ fetchImpl });
        await selector.listo;
        await selector.cargarRegiones('');

        expect(regionEl.options[0].textContent).toBe('Elige primero un país');
    });

    it('sin los selects en la página no hace nada', () => {
        expect(montarSelectorGeografico({ selectPais: null, selectRegion: null })).toBeNull();
    });

    it('renombra el campo según el país', async () => {
        // «Región» en los 249 países es impreciso: en Perú son departamentos.
        const fetchImpl = fetchFalso({ regiones: { PE: ['Lima'] } });
        await montar({ fetchImpl, paisInicial: 'PE' }).listo;

        const label = document.querySelector('label[for="region"]');
        expect(label.textContent).toContain('Departamento');
        expect(regionEl.getAttribute('aria-label')).toBe('Departamento');
    });

    it('el placeholder acompaña a la etiqueta', async () => {
        const fetchImpl = fetchFalso({ regiones: { JP: ['Aichi'] } });
        await montar({ fetchImpl, paisInicial: 'JP' }).listo;

        expect(regionEl.options[0].textContent).toBe('Selecciona tu prefectura');
    });

    it('cambiar de país cambia también la etiqueta', async () => {
        const fetchImpl = fetchFalso({ regiones: { PE: ['Lima'], ES: ['Madrid'] } });
        const selector = montar({ fetchImpl, paisInicial: 'PE' });
        await selector.listo;

        await selector.cargarRegiones('ES');
        expect(document.querySelector('label[for="region"]').textContent).toContain('Provincia');
    });

    it('renombrar conserva el asterisco de obligatorio', () => {
        const campo = document.getElementById('region');
        renombrarCampo('Departamento', campo);

        const label = document.querySelector('label[for="region"]');
        expect(label.querySelector('span')).not.toBeNull();
        expect(label.querySelector('span').textContent).toBe('*');
    });

    it('renombrar sin campos no revienta', () => {
        expect(() => renombrarCampo('Región', null, undefined)).not.toThrow();
    });
});
