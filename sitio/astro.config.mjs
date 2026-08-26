// @ts-check
import { defineConfig } from 'astro/config';
import tailwindcss from '@tailwindcss/vite';

export default defineConfig({
    // Estático por defecto: las páginas se generan contra la API de Laravel en
    // el build. La reconstrucción al publicar desde el panel está por definir
    // —es uno de los costos de separar el sitio— y hasta entonces cada cambio
    // de contenido exige un `npm run build`.
    output: 'static',
    server: { host: true, port: 4321 },
    vite: {
        plugins: [tailwindcss()],
    },
});
