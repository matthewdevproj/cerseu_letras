/**
 * Servicio de reconstrucción del sitio.
 *
 * Escucha una petición de Laravel y ejecuta `astro build`. Existe porque al
 * separar el frontend, publicar dejó de ser instantáneo: el panel guarda en la
 * base y el sitio estático sigue mostrando lo anterior hasta que alguien
 * reconstruye. Esto cierra ese ciclo.
 *
 * Tres decisiones que no son obvias:
 *
 * 1. Va por HTTP y no vigilando un fichero compartido. Cuando el build pase a
 *    CI —que es donde debe acabar—, Laravel seguirá haciendo la misma llamada
 *    y solo cambia a quién. Con un fichero habría que reescribir las dos
 *    puntas.
 *
 * 2. Exige un token. Un endpoint que lanza un proceso pesado sin autenticar es
 *    una forma cómoda de tumbar el servidor desde fuera.
 *
 * 3. Las peticiones que llegan mientras hay un build en marcha NO encolan otro
 *    build cada una: marcan que hace falta repetirlo al terminar. Un editor
 *    que guarda diez cosas seguidas provoca dos builds, no diez.
 */
import { spawn } from 'node:child_process';
import { createServer } from 'node:http';

const PUERTO = Number(process.env.PUERTO_BUILD ?? 4322);
const TOKEN = process.env.CERSEU_BUILD_TOKEN ?? '';

let construyendo = false;
let repetirAlTerminar = false;
let ultimo = { estado: 'sin ejecutar', terminado: null, duracionMs: null, salida: null };

function log(...args) {
    console.log(new Date().toISOString(), ...args);
}

function construir() {
    if (construyendo) {
        repetirAlTerminar = true;
        log('build en curso; se repetirá al terminar');
        return;
    }

    construyendo = true;
    const inicio = Date.now();
    log('build: empieza');

    const proceso = spawn('npm', ['run', 'build'], {
        cwd: '/app',
        env: process.env,
        stdio: ['ignore', 'pipe', 'pipe'],
    });

    let cola = '';
    const recoger = (trozo) => {
        cola += trozo.toString();
        // Solo se guarda el final: un build entero son miles de líneas y lo
        // único que hace falta al fallar son las últimas.
        if (cola.length > 4000) cola = cola.slice(-4000);
    };
    proceso.stdout.on('data', recoger);
    proceso.stderr.on('data', recoger);

    proceso.on('close', (codigo) => {
        const duracionMs = Date.now() - inicio;
        construyendo = false;
        ultimo = {
            estado: codigo === 0 ? 'correcto' : 'fallido',
            terminado: new Date().toISOString(),
            duracionMs,
            salida: codigo === 0 ? null : cola.trim(),
        };
        log(`build: ${ultimo.estado} en ${duracionMs} ms`);

        if (repetirAlTerminar) {
            repetirAlTerminar = false;
            log('había peticiones durante el build: se reconstruye una vez más');
            construir();
        }
    });

    proceso.on('error', (e) => {
        construyendo = false;
        ultimo = {
            estado: 'fallido',
            terminado: new Date().toISOString(),
            duracionMs: Date.now() - inicio,
            salida: e.message,
        };
        log('build: no se pudo lanzar —', e.message);
    });
}

const servidor = createServer((peticion, respuesta) => {
    const responder = (codigo, cuerpo) => {
        respuesta.writeHead(codigo, { 'Content-Type': 'application/json; charset=utf-8' });
        respuesta.end(JSON.stringify(cuerpo));
    };

    if (peticion.url === '/estado' && peticion.method === 'GET') {
        return responder(200, { construyendo, ultimo });
    }

    if (peticion.url !== '/reconstruir' || peticion.method !== 'POST') {
        return responder(404, { mensaje: 'No existe.' });
    }

    if (!TOKEN) {
        return responder(500, {
            mensaje: 'CERSEU_BUILD_TOKEN no está configurado en el servicio de build.',
        });
    }

    if (peticion.headers.authorization !== `Bearer ${TOKEN}`) {
        return responder(401, { mensaje: 'Token inválido.' });
    }

    construir();

    // 202 y no 200: se acepta el encargo, no se ha terminado. Laravel no debe
    // esperar a que acabe un build de diez segundos dentro de un trabajo.
    return responder(202, { mensaje: 'Reconstrucción encargada.', construyendo: true });
});

servidor.listen(PUERTO, '0.0.0.0', () => {
    log(`servicio de build escuchando en el ${PUERTO}`);
    if (!TOKEN) log('AVISO: sin CERSEU_BUILD_TOKEN, las peticiones se rechazarán');
});
