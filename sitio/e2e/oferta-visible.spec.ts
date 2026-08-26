import { expect, test } from '@playwright/test';

/**
 * El fallo que motiva este fichero: los 39 cursos existían en el HTML, la
 * página respondía 200 y la suite de PHP pasaba entera — y no se veía ni uno,
 * porque el umbral del IntersectionObserver era inalcanzable para una grilla
 * tan alta. Solo se detecta comprobando que la tarjeta esté VISIBLE, no que
 * esté presente.
 */
test.describe('La oferta se ve, no solo existe', () => {
    test('las tarjetas de cursos son visibles tras entrar en pantalla', async ({ page }) => {
        await page.goto('/cursos');

        const tarjetas = page.getByRole('article');
        await expect(tarjetas.first()).toBeVisible();

        // La última también: el fallo dejaba visibles las primeras y ocultas
        // las demás según la altura de la grilla.
        const ultima = tarjetas.last();
        await ultima.scrollIntoViewIfNeeded();
        await expect(ultima).toBeVisible();
    });

    test('el listado muestra los cursos publicados', async ({ page }) => {
        await page.goto('/cursos');
        await expect(page.getByRole('article')).not.toHaveCount(0);
    });

    test('un tipo sin oferta explica que está vacío en vez de quedarse en blanco', async ({ page }) => {
        await page.goto('/talleres');
        await expect(page.getByText('Próximamente')).toBeVisible();
    });
});
