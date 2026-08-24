// @vitest-environment jsdom
import { describe, it, expect, beforeEach, vi } from 'vitest';
import { limpiarHtml, normalizar, estaVacio, montarEditor, montarEditores, ACCIONES } from './editor-texto.js';

describe('limpieza del HTML pegado', () => {
    it('conserva el formato que el diseño entiende', () => {
        const html = '<p>Un <strong>texto</strong> con <em>énfasis</em> y <ul><li>lista</li></ul></p>';

        const limpio = limpiarHtml(html);

        expect(limpio).toContain('<strong>');
        expect(limpio).toContain('<em>');
        expect(limpio).toContain('<li>');
    });

    it('desmonta las etiquetas ajenas pero no pierde el texto', () => {
        // Pegar desde Word trae <font>, <o:p> y demás.
        const limpio = limpiarHtml('<font size="4">Texto <b>importante</b></font>');

        expect(limpio).not.toContain('<font');
        expect(limpio).toContain('Texto');
        expect(limpio).toContain('<b>importante</b>');
    });

    it('quita los estilos en línea, que rompen la maqueta', () => {
        const limpio = limpiarHtml('<p style="color:#f0f;font-size:48px">Texto</p>');

        expect(limpio).not.toContain('style');
        expect(limpio).toContain('Texto');
    });

    it('elimina scripts en lugar de dejarlos pasar', () => {
        const limpio = limpiarHtml('<p>Hola</p><script>alert(1)</script>');

        expect(limpio).not.toContain('<script');
        expect(limpio).toContain('Hola');
    });

    it('conserva el href de los enlaces', () => {
        const limpio = limpiarHtml('<a href="https://unmsm.edu.pe" onclick="robar()">UNMSM</a>');

        expect(limpio).toContain('href="https://unmsm.edu.pe"');
        expect(limpio).not.toContain('onclick');
    });

    it('añade rel seguro a los enlaces que abren en otra pestaña', () => {
        const limpio = limpiarHtml('<a href="https://ejemplo.pe" target="_blank">Ir</a>');

        expect(limpio).toContain('rel="noopener noreferrer"');
    });

    it('tolera entradas vacías o nulas', () => {
        expect(limpiarHtml('')).toBe('');
        expect(limpiarHtml(null)).toBe('');
        expect(limpiarHtml(undefined)).toBe('');
    });
});

describe('normalización', () => {
    it('quita los párrafos vacíos que deja el editor', () => {
        expect(normalizar('<p>Texto</p><p><br></p>')).toBe('<p>Texto</p>');
    });

    it('convierte los espacios duros en espacios normales', () => {
        expect(normalizar('<p>Uno&nbsp;dos</p>')).toContain('Uno dos');
    });

    it('reconoce el contenido vacío', () => {
        expect(estaVacio('')).toBe(true);
        expect(estaVacio('<p><br></p>')).toBe(true);
        expect(estaVacio('<p>   </p>')).toBe(true);
        expect(estaVacio('<p>Algo</p>')).toBe(false);
    });
});

describe('editor montado sobre un textarea', () => {
    let textarea, ventana, desmontar;

    beforeEach(() => {
        document.body.innerHTML = `
            <form>
                <textarea id="cuerpo" name="cuerpo" data-editor-texto><p>Contenido inicial</p></textarea>
            </form>`;
        textarea = document.getElementById('cuerpo');
        ventana = { prompt: vi.fn(() => 'https://ejemplo.pe') };
        document.execCommand = vi.fn(() => true);
        desmontar = montarEditor(textarea, { ventana });
    });

    const area = () => document.querySelector('.editor-texto__area');

    it('carga el contenido existente en el área editable', () => {
        expect(area().innerHTML).toContain('Contenido inicial');
    });

    it('oculta el textarea pero lo deja en el formulario', () => {
        // Sigue siendo el campo que se envía: el servidor no cambia.
        expect(textarea.hidden).toBe(true);
        expect(textarea.closest('form')).not.toBeNull();
        expect(textarea.name).toBe('cuerpo');
    });

    it('pinta un botón por cada acción', () => {
        const botones = document.querySelectorAll('.editor-texto__boton');
        expect(botones).toHaveLength(Object.keys(ACCIONES).length);
    });

    it('los botones son type=button para no enviar el formulario', () => {
        const tipos = [...document.querySelectorAll('.editor-texto__boton')].map((b) => b.type);
        expect(new Set(tipos)).toEqual(new Set(['button']));
    });

    it('escribir en el área actualiza el textarea', () => {
        area().innerHTML = '<p>Texto nuevo</p>';
        area().dispatchEvent(new Event('input'));

        expect(textarea.value).toBe('<p>Texto nuevo</p>');
    });

    it('el textarea emite «input» para que el aviso de cambios se entere', () => {
        const espia = vi.fn();
        textarea.addEventListener('input', espia);

        area().innerHTML = '<p>Otro</p>';
        area().dispatchEvent(new Event('input'));

        expect(espia).toHaveBeenCalled();
    });

    it('pulsar negrita ejecuta el comando correspondiente', () => {
        document.querySelector('[data-accion="negrita"]')
            .dispatchEvent(new MouseEvent('mousedown', { bubbles: true }));

        expect(document.execCommand).toHaveBeenCalledWith('bold', false, null);
    });

    it('«Título» aplica el bloque h3, no un tamaño de letra', () => {
        document.querySelector('[data-accion="titulo"]')
            .dispatchEvent(new MouseEvent('mousedown', { bubbles: true }));

        expect(document.execCommand).toHaveBeenCalledWith('formatBlock', false, 'h3');
    });

    it('el enlace pide la dirección antes de aplicarlo', () => {
        document.querySelector('[data-accion="enlace"]')
            .dispatchEvent(new MouseEvent('mousedown', { bubbles: true }));

        expect(ventana.prompt).toHaveBeenCalled();
        expect(document.execCommand).toHaveBeenCalledWith('createLink', false, 'https://ejemplo.pe');
    });

    it('cancelar el diálogo del enlace no aplica nada', () => {
        ventana.prompt.mockReturnValueOnce('');

        document.querySelector('[data-accion="enlace"]')
            .dispatchEvent(new MouseEvent('mousedown', { bubbles: true }));

        expect(document.execCommand).not.toHaveBeenCalled();
    });

    it('la barra no roba el foco al área de texto', () => {
        const evento = new MouseEvent('mousedown', { bubbles: true, cancelable: true });
        document.querySelector('[data-accion="negrita"]').dispatchEvent(evento);

        expect(evento.defaultPrevented).toBe(true);
    });

    it('no se monta dos veces sobre el mismo campo', () => {
        montarEditor(textarea, { ventana });

        expect(document.querySelectorAll('.editor-texto__area')).toHaveLength(1);
    });

    it('desmontar devuelve el textarea a su sitio', () => {
        desmontar();

        expect(textarea.hidden).toBe(false);
        expect(document.querySelector('.editor-texto')).toBeNull();
        expect(document.getElementById('cuerpo')).not.toBeNull();
    });

    it('sin textarea no revienta', () => {
        expect(() => montarEditor(null)()).not.toThrow();
    });
});

describe('montaje automático', () => {
    it('solo toma los textarea marcados', () => {
        document.body.innerHTML = `
            <textarea data-editor-texto>uno</textarea>
            <textarea>dos</textarea>`;

        montarEditores({ ventana: { prompt: () => null } });

        expect(document.querySelectorAll('.editor-texto__area')).toHaveLength(1);
    });
});
