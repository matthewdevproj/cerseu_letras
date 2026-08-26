import type { APIRoute } from 'astro';

/**
 * robots.txt generado, no escrito a mano.
 *
 * El dominio sale de `Astro.site`, que a su vez sale del entorno: un despliegue
 * para otra unidad no tiene que acordarse de editar este fichero para que el
 * sitemap deje de apuntar al dominio del CERSEU.
 */
export const GET: APIRoute = ({ site }) => {
    const sitemap = new URL('sitemap-index.xml', site).href;

    const cuerpo = `# El sitio público lo sirve Astro; el panel y la API siguen en Laravel y no
# tienen nada que ofrecer a un buscador.
User-agent: *
Allow: /
Disallow: /admin
Disallow: /api/
Disallow: /buscar

Sitemap: ${sitemap}
`;

    return new Response(cuerpo, {
        headers: { 'Content-Type': 'text/plain; charset=utf-8' },
    });
};
