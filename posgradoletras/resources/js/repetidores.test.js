import { describe, it, expect } from 'vitest';
import {
    crearRepetidor,
    crearCronogramaAdmision,
    crearEditorContenido,
    crearInversionPeriodos,
    crearMenuNavegacion,
} from './repetidores.js';

describe('repetidor base', () => {
    it('conserva los elementos iniciales y les asigna una clave estable', () => {
        const r = crearRepetidor([{ titulo: 'A' }, { titulo: 'B' }]);

        expect(r.elementos).toHaveLength(2);
        expect(r.elementos[0].titulo).toBe('A');
        // `uid` es la clave de x-for; debe ser única
        expect(new Set(r.elementos.map((e) => e.uid)).size).toBe(2);
    });

    it('tolera una entrada vacía o inválida', () => {
        expect(crearRepetidor().elementos).toEqual([]);
        expect(crearRepetidor(null).elementos).toEqual([]);
        expect(crearRepetidor('no es lista').elementos).toEqual([]);
    });

    it('agrega al final con la plantilla indicada', () => {
        const r = crearRepetidor([], () => ({ titulo: 'nuevo' }));
        r.agregar();
        r.agregar();

        expect(r.elementos).toHaveLength(2);
        expect(r.elementos[1].titulo).toBe('nuevo');
        expect(r.elementos[0].uid).not.toBe(r.elementos[1].uid);
    });

    it('elimina por índice e ignora índices fuera de rango', () => {
        const r = crearRepetidor([{ t: 'A' }, { t: 'B' }, { t: 'C' }]);

        r.eliminar(1);
        expect(r.elementos.map((e) => e.t)).toEqual(['A', 'C']);

        r.eliminar(9);
        r.eliminar(-1);
        expect(r.elementos).toHaveLength(2);
    });

    it('reordena y respeta los extremos', () => {
        const r = crearRepetidor([{ t: 'A' }, { t: 'B' }, { t: 'C' }]);

        r.mover(0, 1);
        expect(r.elementos.map((e) => e.t)).toEqual(['B', 'A', 'C']);

        r.mover(2, -1);
        expect(r.elementos.map((e) => e.t)).toEqual(['B', 'C', 'A']);

        r.mover(0, -1);   // ya está arriba
        r.mover(2, 1);    // ya está abajo
        expect(r.elementos.map((e) => e.t)).toEqual(['B', 'C', 'A']);
    });

    it('la clave sobrevive al reordenado', () => {
        const r = crearRepetidor([{ t: 'A' }, { t: 'B' }]);
        const uidA = r.elementos[0].uid;

        r.mover(0, 1);
        expect(r.elementos[1].uid).toBe(uidA);
    });
});

describe('cronograma de admisión', () => {
    it('parte de las etapas dadas y de la visibilidad indicada', () => {
        const c = crearCronogramaAdmision([{ titulo: 'Inscripción' }], false);

        expect(c.pasos).toHaveLength(1);
        expect(c.visible).toBe(false);
    });

    it('la etapa nueva nace visible y sin destacar', () => {
        const c = crearCronogramaAdmision([]);
        c.agregar();

        expect(c.pasos[0]).toMatchObject({ is_visible: true, destacado: false, icono: 'documento' });
    });

    it('solo una etapa puede estar en curso a la vez', () => {
        const c = crearCronogramaAdmision([{ titulo: 'A' }, { titulo: 'B' }, { titulo: 'C' }]);

        c.marcarDestacado(1, true);
        expect(c.pasos.map((p) => p.destacado)).toEqual([false, true, false]);

        c.marcarDestacado(2, true);
        expect(c.pasos.map((p) => p.destacado)).toEqual([false, false, true]);
    });

    it('desmarcar deja todas sin destacar', () => {
        const c = crearCronogramaAdmision([{ titulo: 'A' }, { titulo: 'B' }]);

        c.marcarDestacado(0, true);
        c.marcarDestacado(0, false);
        expect(c.pasos.every((p) => !p.destacado)).toBe(true);
    });
});

describe('editor de contenido', () => {
    it('la sección nueva hereda el grupo por defecto', () => {
        const e = crearEditorContenido([], 'maestria');
        e.agregar();

        expect(e.secciones[0].grupo).toBe('maestria');
        expect(e.secciones[0].is_visible).toBe(true);
        expect(e.secciones[0].id).toBeNull();
    });

    it('mantiene el id de las secciones existentes', () => {
        const e = crearEditorContenido([{ id: 7, titulo: 'Paso I' }]);
        expect(e.secciones[0].id).toBe(7);
    });
});

describe('tarifas por periodo', () => {
    it('calcula el subtotal como matrícula + créditos × costo', () => {
        const i = crearInversionPeriodos(160, [{ matricula: 310, creditos: 14 }]);
        expect(i.subtotal(i.periodos[0])).toBe(310 + 14 * 160);
    });

    it('suma el total de todos los periodos', () => {
        const i = crearInversionPeriodos(160, [
            { matricula: 310, creditos: 14 },
            { matricula: 400, creditos: 16 },
        ]);
        // (310 + 2240) + (400 + 2560)
        expect(i.total).toBe(5510);
    });

    it('el total se recalcula al cambiar el costo por crédito', () => {
        const i = crearInversionPeriodos(100, [{ matricula: 0, creditos: 10 }]);
        expect(i.total).toBe(1000);

        i.costoCredito = 200;
        expect(i.total).toBe(2000);
    });

    it('sin periodos el total es cero, no NaN', () => {
        const i = crearInversionPeriodos(160, []);
        expect(i.total).toBe(0);
    });

    it('normaliza valores no numéricos en lugar de propagar NaN', () => {
        const i = crearInversionPeriodos('160', [{ matricula: '310', creditos: null }]);

        expect(i.costoCredito).toBe(160);
        expect(i.periodos[0].creditos).toBe(0);
        expect(i.total).toBe(310);
        expect(Number.isNaN(i.total)).toBe(false);
    });

    it('agregar y eliminar periodos ajusta el total', () => {
        const i = crearInversionPeriodos(100, [{ matricula: 200, creditos: 1 }]);
        expect(i.total).toBe(300);

        i.agregar();
        expect(i.periodos).toHaveLength(2);
        expect(i.total).toBe(300);   // el nuevo está a cero

        i.eliminar(0);
        expect(i.total).toBe(0);
    });
});

describe('menú de navegación', () => {
    const menuBase = () =>
        crearMenuNavegacion([
            { etiqueta: 'Nosotros', hijos: [{ etiqueta: 'Quiénes somos' }, { etiqueta: 'Directorio' }] },
            { etiqueta: 'Admisión', hijos: [] },
        ]);

    it('conserva las entradas y sus subentradas', () => {
        const m = menuBase();

        expect(m.items).toHaveLength(2);
        expect(m.items[0].hijos).toHaveLength(2);
        expect(m.items[1].hijos).toEqual([]);
    });

    it('una entrada sin hijos declarados no revienta al añadirle uno', () => {
        const m = crearMenuNavegacion([{ etiqueta: 'Suelta' }]);
        m.agregarHijo(0);

        expect(m.items[0].hijos).toHaveLength(1);
        expect(m.items[0].hijos[0].is_visible).toBe(true);
    });

    it('la entrada nueva nace visible y sin subentradas', () => {
        const m = menuBase();
        m.agregar();

        expect(m.items[2]).toMatchObject({ etiqueta: '', is_visible: true, nueva_pestana: false });
        expect(m.items[2].hijos).toEqual([]);
    });

    it('ordena y elimina subentradas de la entrada correcta', () => {
        const m = menuBase();

        m.moverHijo(0, 0, 1);
        expect(m.items[0].hijos.map((h) => h.etiqueta)).toEqual(['Directorio', 'Quiénes somos']);

        m.eliminarHijo(0, 0);
        expect(m.items[0].hijos.map((h) => h.etiqueta)).toEqual(['Quiénes somos']);
        expect(m.items[1].hijos).toEqual([]);   // la otra entrada no se toca
    });

    it('no mueve subentradas más allá de los extremos', () => {
        const m = menuBase();

        m.moverHijo(0, 0, -1);
        m.moverHijo(0, 1, 1);
        expect(m.items[0].hijos.map((h) => h.etiqueta)).toEqual(['Quiénes somos', 'Directorio']);
    });

    it('elegir ruta interna y dirección externa son excluyentes', () => {
        const m = menuBase();
        const entrada = m.items[0];

        entrada.url = 'https://ejemplo.pe';
        entrada.route_name = 'nosotros';
        m.usarRuta(entrada);
        expect(entrada.url).toBe('');

        entrada.url = 'https://ejemplo.pe';
        m.usarUrl(entrada);
        expect(entrada.route_name).toBe('');
    });

    it('vaciar el campo no borra el otro por sorpresa', () => {
        const m = menuBase();
        const entrada = m.items[0];
        entrada.route_name = 'nosotros';

        entrada.url = '';
        m.usarUrl(entrada);
        expect(entrada.route_name).toBe('nosotros');
    });

    it('editar una subentrada no altera las de otra entrada', () => {
        const m = menuBase();
        m.agregarHijo(1);
        m.items[1].hijos[0].etiqueta = 'Proceso';

        expect(m.items[0].hijos.map((h) => h.etiqueta)).toEqual(['Quiénes somos', 'Directorio']);
    });

    it('lista lo caducado, entradas y subentradas, para poder avisar', () => {
        const m = crearMenuNavegacion([
            { etiqueta: 'Admisión', hijos: [
                { etiqueta: 'Criterios 2025', caducado: true },
                { etiqueta: 'Vacantes', caducado: false },
            ] },
            { etiqueta: 'Convocatoria vieja', caducado: true, hijos: [] },
            { etiqueta: 'Nosotros', hijos: [] },
        ]);

        expect(m.caducados).toEqual(['Criterios 2025', 'Convocatoria vieja']);
    });

    it('sin nada caducado la lista queda vacía', () => {
        const m = crearMenuNavegacion([{ etiqueta: 'Nosotros', hijos: [{ etiqueta: 'Quiénes somos' }] }]);

        expect(m.caducados).toEqual([]);
    });

    it('la entrada nueva nace con la fecha de retirada en blanco', () => {
        const m = crearMenuNavegacion([]);
        m.agregar();

        expect(m.items[0].vigente_hasta).toBe('');
    });
});
