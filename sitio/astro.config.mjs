// @ts-check
import { defineConfig } from 'astro/config';
import tailwindcss from '@tailwindcss/vite';
import sitemap from '@astrojs/sitemap';

export default defineConfig({
    // Estático por defecto: las páginas se generan contra la API de Laravel en
    // el build. La reconstrucción al publicar desde el panel está por definir
    // —es uno de los costos de separar el sitio— y hasta entonces cada cambio
    // de contenido exige un `npm run build`.
    // Dominio publico del sitio. Hace falta para el sitemap y para las URLs
    // canonicas: sin el, Astro no puede componerlas. Se toma del entorno para
    // que un despliegue de otra unidad no exija tocar este fichero.
    site: process.env.CERSEU_SITE ?? 'https://cerseuletras.unmsm.edu.pe',
    integrations: [sitemap()],

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
