// @ts-check
import { defineConfig } from 'astro/config';
import tailwindcss from '@tailwindcss/vite';
import sitemap from '@astrojs/sitemap';

export default defineConfig({
    // Estatico por defecto: las paginas se generan contra la API de Laravel en
    // el build. Publicar desde el panel encola un trabajo que pide la
    // reconstruccion, asi que un cambio de contenido no exige tocar nada a
    // mano (ver App\Jobs\ReconstruirSitio y sitio/herramientas).
    // Dominio publico del sitio. Hace falta para el sitemap y para las URLs
    // canonicas: sin el, Astro no puede componerlas. Se toma del entorno para
    // que un despliegue de otra unidad no exija tocar este fichero.
    site: process.env.CERSEU_SITE ?? 'https://cerseuletras.unmsm.edu.pe',
    integrations: [
        sitemap({
            // /buscar es una herramienta, no contenido: va con `noindex`, y
            // anunciarla en el sitemap seria pedirle a Google que indexe justo
            // lo que la pagina le dice que no indexe.
            filter: (pagina) => !pagina.includes('/buscar'),
        }),
    ],

    // Optimizacion de imagenes sobre las que sirve Laravel. Astro las descarga
    // en el build, genera varios anchos y las convierte a AVIF/WebP. Hace
    // falta autorizar el origen: por seguridad no procesa cualquier URL remota.
    image: {
        domains: ['localhost', 'web', 'cerseuletras.unmsm.edu.pe'],
    },
    output: 'static',
    server: { host: true, port: 4321 },
    vite: {
        plugins: [tailwindcss()],
        server: {
            // Vite rechaza las peticiones cuyo Host no reconoce —proteccion
            // contra rebinding de DNS—. Dentro de Docker, el contenedor de
            // pruebas pide por el nombre del servicio, `astro`, y recibia
            // «Blocked request» en vez de la pagina: 16 pruebas rojas que
            // parecian 16 fallos y eran uno.
            allowedHosts: ['astro', 'localhost'],
            // Sondeo en vez de inotify. Docker Desktop en Windows no propaga
            // los eventos del sistema de ficheros a traves del bind mount, asi
            // que el watcher nunca se entera de un cambio hecho desde el host:
            // el fichero cambia dentro del contenedor y la pagina sigue
            // sirviendo la version anterior, sin ningun error que lo delate.
            // Es el mismo origen que el truncado de readdir() en vendor/.
            watch: { usePolling: true, interval: 300 },
        },
    },
});
