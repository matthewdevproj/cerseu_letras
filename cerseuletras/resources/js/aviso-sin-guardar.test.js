// @vitest-environment jsdom
import { describe, it, expect, beforeEach, vi } from 'vitest';
import { serializar, hayCambios, vigilarFormulario, montarAvisoSinGuardar } from './aviso-sin-guardar.js';

/** Ventana falsa: jsdom no deja observar el resultado de beforeunload. */
function ventanaFalsa() {
    const oyentes = {};
    return {
        addEventListener: (tipo, fn) => { (oyentes[tipo] ||= []).push(fn); },
        removeEventListener: (tipo, fn) => { oyentes[tipo] = (oyentes[tipo] || []).filter((f) => f !== fn); },
        confirm: vi.fn(() => true),
        /** Dispara beforeunload y dice si alguien lo bloqueó. */
        intentarSalir() {
            const evento = { preventDefault: vi.fn(), returnValue: null };
            (oyentes.beforeunload || []).forEach((fn) => fn(evento));
            return evento.preventDefault.mock.calls.length > 0;
        },
        oyentes,
    };
}

describe('instantánea del formulario', () => {
    beforeEach(() => {
        document.body.innerHTML = `
            <form id="f">
                <input type="hidden" name="_token" value="abc">
                <input name="titulo" value="Original">
                <textarea name="cuerpo">Texto</textarea>
                <input type="checkbox" name="visible" value="1" checked>
            </form>`;
    });

    it('recoge los campos del formulario', () => {
        const s = serializar(document.getElementById('f'));

        expect(s).toContain('titulo=Original');
        expect(s).toContain('cuerpo=Texto');
        expect(s).toContain('visible=1');
    });

    it('ignora el token CSRF, que se regenera solo', () => {
        expect(serializar(document.getElementById('f'))).not.toContain('_token');
    });

    it('sin formulario devuelve cadena vacía en vez de reventar', () => {
        expect(serializar(null)).toBe('');
    });

    it('detecta un cambio de valor', () => {
        const f = document.getElementById('f');
        const antes = serializar(f);

        expect(hayCambios(antes, f)).toBe(false);

        f.querySelector('[name=titulo]').value = 'Cambiado';
        expect(hayCambios(antes, f)).toBe(true);
    });

    it('detecta el desmarcado de una casilla', () => {
        const f = document.getElementById('f');
        const antes = serializar(f);

        f.querySelector('[name=visible]').checked = false;
        expect(hayCambios(antes, f)).toBe(true);
    });
});

describe('vigilancia del formulario', () => {
    let f, ventana;

    beforeEach(() => {
        document.body.innerHTML = `
            <form id="f">
                <input name="titulo" value="Original">
                <a href="#otra" data-salir-sin-guardar id="cancelar">Cancelar</a>
            </form>`;
        f = document.getElementById('f');
        ventana = ventanaFalsa();
        vigilarFormulario(f, { ventana });
    });

    it('sin cambios deja salir', () => {
        expect(ventana.intentarSalir()).toBe(false);
    });

    it('con cambios bloquea la salida', () => {
        f.querySelector('[name=titulo]').value = 'Cambiado';
        expect(ventana.intentarSalir()).toBe(true);
    });

    it('tras enviar el formulario deja salir aunque haya cambios', () => {
        // Guardar no es «salir con cambios pendientes».
        f.querySelector('[name=titulo]').value = 'Cambiado';
        f.dispatchEvent(new Event('submit'));

        expect(ventana.intentarSalir()).toBe(false);
    });

    it('volver a poner el valor original desactiva el aviso', () => {
        const campo = f.querySelector('[name=titulo]');
        campo.value = 'Cambiado';
        expect(ventana.intentarSalir()).toBe(true);

        campo.value = 'Original';
        expect(ventana.intentarSalir()).toBe(false);
    });

    it('«Cancelar» pregunta antes de irse si hay cambios', () => {
        f.querySelector('[name=titulo]').value = 'Cambiado';
        document.getElementById('cancelar').click();

        expect(ventana.confirm).toHaveBeenCalled();
    });

    it('«Cancelar» no pregunta si no se ha tocado nada', () => {
        document.getElementById('cancelar').click();
        expect(ventana.confirm).not.toHaveBeenCalled();
    });

    it('si se cancela la confirmación, no se navega', () => {
        ventana.confirm.mockReturnValueOnce(false);
        f.querySelector('[name=titulo]').value = 'Cambiado';

        const evento = new MouseEvent('click', { bubbles: true, cancelable: true });
        document.getElementById('cancelar').dispatchEvent(evento);

        expect(evento.defaultPrevented).toBe(true);
    });

    it('dejar de vigilar libera los oyentes', () => {
        const otro = ventanaFalsa();
        const baja = vigilarFormulario(f, { ventana: otro });

        f.querySelector('[name=titulo]').value = 'Cambiado';
        expect(otro.intentarSalir()).toBe(true);

        baja();
        expect(otro.intentarSalir()).toBe(false);
    });

    it('sin formulario devuelve una baja que no revienta', () => {
        expect(() => vigilarFormulario(null)()).not.toThrow();
    });
});

describe('montaje automático', () => {
    it('solo vigila los formularios marcados', () => {
        document.body.innerHTML = `
            <form id="vigilado" data-avisar-sin-guardar><input name="a" value="1"></form>
            <form id="libre"><input name="b" value="1"></form>`;

        const ventana = ventanaFalsa();
        montarAvisoSinGuardar({ ventana });

        document.querySelector('#libre [name=b]').value = '2';
        expect(ventana.intentarSalir()).toBe(false);

        document.querySelector('#vigilado [name=a]').value = '2';
        expect(ventana.intentarSalir()).toBe(true);
    });

    it('sin formularios marcados no hace nada', () => {
        document.body.innerHTML = '<form><input name="a"></form>';
        const ventana = ventanaFalsa();

        montarAvisoSinGuardar({ ventana });
        expect(ventana.intentarSalir()).toBe(false);
    });
});
