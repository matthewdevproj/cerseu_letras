import { expect, test } from '@playwright/test';
import { abrirMenu } from './utiles';

test.describe('Accesibilidad de la cabecera y el pie', () => {
    test('el desplegable abre con teclado, no solo al pasar el ratón', async ({ page }) => {
        await page.goto('/');

        // En pantalla estrecha la navegación vive en un panel que hay que
        // abrir. La prueba reproduce el camino real del visitante, no se lo
        // salta.
        const navegacion = await abrirMenu(page);

        const desplegable = navegacion.locator('details').first();
        const resumen = desplegable.locator('summary');

        await resumen.focus();
        await page.keyboard.press('Enter');

        await expect(desplegable).toHaveAttribute('open', '');
        await expect(desplegable.locator('ul a').first()).toBeVisible();
    });

    test('el primer tabulador ofrece saltar al contenido', async ({ page }) => {
        await page.goto('/');
        await page.keyboard.press('Tab');

        const enfocado = page.locator(':focus');
        await expect(enfocado).toHaveText(/Saltar al contenido/i);
    });

    test('hay un solo h1 por página', async ({ page }) => {
        for (const ruta of ['/', '/cursos', '/nosotros', '/admision']) {
            await page.goto(ruta);

            // Acotado a `main`: los selectores CSS de Playwright atraviesan los
            // shadow roots abiertos, y la barra de herramientas de Astro en
            // desarrollo aporta cuatro <h1> propios («Audit», «Settings»...).
            // Contar los del documento entero medía la herramienta, no la página.
            await expect(page.locator('main h1'), `en ${ruta}`).toHaveCount(1);
        }
    });
});
