import type { Page } from '@playwright/test';

/**
 * Deja el menú principal alcanzable, en la anchura que sea.
 *
 * En escritorio la navegación está siempre a la vista; por debajo de `lg` vive
 * en un panel que abre el botón de las tres rayas. Las pruebas tienen que
 * recorrer el camino real del visitante, no saltárselo, y este ayudante evita
 * repetir la bifurcación en cada una.
 */
export async function abrirMenu(page: Page) {
    const boton = page.locator('#abrir-menu');

    if (await boton.isVisible()) {
        if ((await boton.getAttribute('aria-expanded')) !== 'true') {
            await boton.click();
        }
        return page.locator('#menu-movil');
    }

    return page.locator('header nav[aria-label="Principal"]');
}

/**
 * Abre el buscador de la cabecera y devuelve su campo.
 *
 * La lupa despliega el campo: una caja de búsqueda permanente le robaba sitio
 * al menú y al logotipo.
 */
export async function abrirBuscador(page: Page) {
    const boton = page.locator('#abrir-buscador');

    if ((await boton.getAttribute('aria-expanded')) !== 'true') {
        await boton.click();
    }

    return page.locator('#buscador-q');
}
