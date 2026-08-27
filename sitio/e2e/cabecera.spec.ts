import { expect, test } from '@playwright/test';

/**
 * El desplegable de la barra de navegación.
 *
 * Antes era un `<details>`: solo abría al pulsar, y una vez abierto se quedaba
 * así aunque te fueras con el ratón a otra parte. Lo que se comprueba aquí es
 * justo eso —que abra al pasar, que cierre al salir y que pulsar no lo deje
 * colgado— y que nada de ello se consiga a costa del teclado.
 */
test.describe('Desplegables de la cabecera', () => {
    // El menú de escritorio no existe en el proyecto móvil, donde la
    // navegación va en un panel aparte que se abre con el botón.
    test.skip(({ isMobile }) => Boolean(isMobile), 'La barra de escritorio no está en móvil.');

    const primerDesplegable = (page: import('@playwright/test').Page) =>
        page.locator('nav[aria-label="Principal"] > div').first();

    test('abre al pasar el raton y cierra al salir', async ({ page }) => {
        await page.goto('/');

        const grupo = primerDesplegable(page);
        const boton = grupo.getByRole('button');
        const lista = grupo.getByRole('list');

        await expect(lista).toBeHidden();

        await boton.hover();
        await expect(lista).toBeVisible();
        await expect(boton).toHaveAttribute('aria-expanded', 'true');

        // Salir del desplegable lo cierra: el logotipo está lejos del menú.
        await page.locator('header a[href="/"]').first().hover();
        await expect(lista).toBeHidden();
        await expect(boton).toHaveAttribute('aria-expanded', 'false');
    });

    test('pulsar no lo deja colgado al retirar el raton', async ({ page }) => {
        await page.goto('/');

        const grupo = primerDesplegable(page);
        const boton = grupo.getByRole('button');
        const lista = grupo.getByRole('list');

        await boton.click();
        await expect(lista).toBeVisible();

        await page.locator('header a[href="/"]').first().hover();
        await expect(lista).toBeHidden();
    });

    test('funciona con teclado, sin depender del raton', async ({ page }) => {
        await page.goto('/');

        const grupo = primerDesplegable(page);
        const boton = grupo.getByRole('button');
        const lista = grupo.getByRole('list');

        await boton.focus();
        await page.keyboard.press('Enter');
        await expect(lista).toBeVisible();

        // Escape cierra y devuelve el foco al botón, para no perder el sitio.
        await page.keyboard.press('Escape');
        await expect(lista).toBeHidden();
        await expect(boton).toBeFocused();

        // La flecha abajo también abre, que es lo que espera quien navega así.
        await page.keyboard.press('ArrowDown');
        await expect(lista).toBeVisible();
    });

    test('al bajar, la barra se vuelve cristal y no tapa el contenido', async ({ page }) => {
        await page.goto('/');

        const barra = page.locator('#barra-navegacion');
        await page.mouse.wheel(0, 600);

        await expect(page.locator('header.cabecera')).toHaveClass(/bajada/);

        // El fondo llega por una transicion de 300 ms: leerlo de inmediato
        // devuelve el valor de partida, que es transparente.
        await expect
            .poll(async () => barra.evaluate((el) => getComputedStyle(el).backgroundColor))
            .toMatch(/rgba\(255, 255, 255, 0\.[0-8]/);

        const estilo = await barra.evaluate((el) => {
            const c = getComputedStyle(el);
            // Las dos, y sin `||`: `backdropFilter` devuelve la cadena 'none'
            // cuando no aplica, que es truthy, así que un `||` nunca llegaba a
            // mirar la prefijada y la prueba media la propiedad equivocada.
            const filtros = [c.backdropFilter, (c as any).webkitBackdropFilter]
                .filter((v) => v && v !== 'none')
                .join(' ');
            return { fondo: c.backgroundColor, filtro: filtros };
        });

        // Translúcido de verdad: con 0.95 el desenfoque estaba puesto y no se
        // veía, porque no quedaba fondo que desenfocar.
        expect(estilo.filtro).toContain('blur');
    });
});
