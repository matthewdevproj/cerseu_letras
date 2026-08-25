# CERSEU Letras — Laravel 12 con Docker

Sitio del **Centro de Responsabilidad Social y Extensión Universitaria** de la
Facultad de Letras y Ciencias Humanas de la UNMSM.

El CERSEU ofrece dos tipos de formación abierta a toda la comunidad —**cursos**
(se miden en meses) y **talleres** (en semanas)—, cada uno con su listado, su
página de admisión y su formulario de solicitud de información. El contenido se
administra desde el panel en `/admin`.

> El sitio nació como portal de la Unidad de Posgrado y conserva de aquel origen
> el nombre de la tabla `programas`, donde ahora viven cursos y talleres: los
> distingue la columna `grado`. Las URLs anteriores `/programas` y `/diplomados`
> redirigen con 301 a `/cursos` y `/talleres`, para no romper enlaces ya
> publicados.

## Requisitos

- Docker Desktop instalado
- Docker Compose v2+

## Estructura del Proyecto

```
├── docker-compose.yml
├── docker/
│   ├── php/
│   │   ├── Dockerfile
│   │   └── php.ini
│   ├── mysql/
│   │   ├── Dockerfile
│   │   ├── my.cnf
│   │   └── docker-entrypoint-initdb.d/
│   └── nginx/
│       ├── default.conf          ← sin TLS (desarrollo)
│       ├── default-ssl.conf      ← con TLS (producción)
│       └── compression.conf
├── docker-compose.dev.yml   ← capa de desarrollo (sin TLS, debug on)
├── .env.example             ← variables de Compose (BD y su usuario)
└── cerseuletras/            ← Código Laravel 12 (ver su propio README)
    ├── app/
    ├── routes/
    ├── resources/
    └── ...
```

## Instalación Rápida

### 1. Configurar los dos archivos .env

Hay **dos**, y es fácil confundirlos porque los lee gente distinta:

| Fichero | Lo lee | Para qué |
|---|---|---|
| `.env` (raíz) | Docker Compose | Crear la base de datos y su usuario |
| `cerseuletras/.env` | Laravel | Conectarse a esa base, correo, etc. |

Compose solo mira el `.env` que está junto al `docker-compose.yml`. Si pones la
contraseña únicamente en el de Laravel, MySQL se creará con la de plantilla y
la aplicación no podrá entrar.

```bash
cp .env.example .env
cp cerseuletras/.env.docker cerseuletras/.env
```

Ahora edita los dos y **haz que coincidan** en `DB_DATABASE`, `DB_USERNAME` y
`DB_PASSWORD`. En el de la raíz va además `DB_ROOT_PASSWORD`; en el de Laravel,
las credenciales de `MAIL_*` si vas a enviar correo. Ninguno de los dos `.env`
se versiona.

### 2. Construir e iniciar los contenedores

**Producción** (nginx con TLS en los puertos 80 y 443):

```bash
docker compose build
docker compose up -d
```

Necesita los certificados en `docker/nginx/ssl_sectigo/` (`fullchain.pem` y
`privkey.pem`). No están en el repositorio —son secretos— y sin ellos el
contenedor `web` no arranca.

**Desarrollo** (sin TLS, con `APP_DEBUG` activo):

```bash
docker compose -f docker-compose.yml -f docker-compose.dev.yml up -d --build
```

La capa de desarrollo sirve por el puerto 80 sin certificados y devuelve
`APP_ENV=local` y `APP_DEBUG=true`, que el compose base fija en `production`
como variables del contenedor —y esas ganan sobre el `.env`, de modo que sin
esta capa los errores salen como un 500 en blanco—. También publica el 5173
para `npm run dev`.

El fichero se llama `docker-compose.dev.yml` y no `docker-compose.override.yml`
a propósito: con ese nombre Compose lo aplicaría solo, y el servidor acabaría
con el modo depuración encendido tras un `git pull`.

La capa saca además `vendor/` y `node_modules/` del bind mount a volúmenes
nombrados. **En Windows esto no es opcional**: sobre el bind mount de Docker
Desktop, `readdir()` trunca los directorios grandes —en la carpeta de iconos de
FontAwesome, `scandir()` ve los 1758 SVG pero `FilesystemIterator` devuelve
926— y la mitad de los iconos deja de existir para la aplicación, con lo que
cualquier página que use uno revienta con «Unable to locate a class or view for
component». Como contrapartida, `composer install` y `npm install` hay que
ejecutarlos dentro del contenedor —que es lo que indican los pasos de abajo— y
el editor del host no ve esas dos carpetas.

### 3. Instalar dependencias y configurar Laravel

En los comandos de abajo, si levantaste con la capa de desarrollo, añade los
mismos `-f` a cada `docker compose run`.

```bash
# Dependencias de PHP
docker compose run --rm app composer install

# Key de la aplicación
docker compose run --rm app php artisan key:generate

# Migraciones y contenido inicial.
# En el primer arranque MySQL tarda unos segundos en aceptar conexiones y
# `depends_on` no espera a que esté listo: si sale «Connection refused»,
# repite el comando.
docker compose run --rm app php artisan migrate --seed

# Enlace de storage (imágenes subidas desde el panel)
docker compose run --rm app php artisan storage:link

# Frontend
docker compose run --rm app npm install
docker compose run --rm app npm run build
```

`--seed` deja el sitio utilizable desde el primer arranque: menú, textos de
`/nosotros`, `/tramites` y `/admision`, ajustes del sitio y la programación
2026 del CERSEU (39 cursos con sus docentes y 47 convocatorias). Sin él, esas
secciones salen en blanco porque su contenido es administrable y no vive en las
vistas.

### 4. Permisos de escritura

Laravel escribe en `storage/` y `bootstrap/cache`. El contenedor corre como un
usuario no-root, y **en Linux el bind mount conserva el propietario real de los
ficheros**: si el usuario del host no coincide con el del contenedor, la
aplicación no puede escribir y revienta al primer log.

Antes de construir, pon tus identificadores en el `.env` de la raíz:

```bash
id -u    # -> UID
id -g    # -> GID
```

Con `UID=1000` y `GID=1000` —lo habitual en el primer usuario de una VM— no
hay nada que cambiar. En Windows y macOS da igual: Docker Desktop no traslada
los propietarios.

Si aún así aparece un error de permisos:

```bash
docker compose run --rm app chmod -R 775 storage bootstrap/cache
```

### 5. Acceder a la aplicación

Abre en tu navegador: [http://localhost](http://localhost)

El panel está en `/admin`. Los seeders crean un administrador con contraseña de
desarrollo —la verás impresa al sembrar—: **cámbiala antes de exponer el
sitio**, porque está escrita en `database/seeders/UserSeeder.php`.

## Desplegar en una VM Linux

Lo anterior está verificado clonando el repositorio en limpio y siguiendo estos
pasos uno a uno. Al llevarlo a una VM hay tres diferencias:

1. **`UID`/`GID`** (paso 4). Es el fallo más común y no se manifiesta en
   Windows, porque allí Docker Desktop presenta todo el bind mount como `root`
   con permisos 777.

2. **Los volúmenes de `vendor/` y `node_modules/`** de la capa de desarrollo
   existen para esquivar un fallo de Docker Desktop en Windows, donde
   `readdir()` trunca los directorios grandes. En Linux no hacen falta, pero
   tampoco estorban: dejarlos evita tener dos configuraciones distintas.

3. **TLS**. La configuración de producción espera `fullchain.pem` y
   `privkey.pem` en `docker/nginx/ssl_sectigo/`, emitidos para el dominio de
   `NGINX_HOST`, y el DNS apuntando a la máquina. Sin eso, el contenedor `web`
   no arranca; usa la capa de desarrollo mientras tanto.

## Desarrollo sin Docker

El servidor de desarrollo del host no alcanza al contenedor de MySQL —el host
`db` solo resuelve dentro de Docker—, así que en local se usa SQLite:

```bash
cd cerseuletras
composer install && npm install && npm run build
php artisan migrate:fresh --seed
php artisan serve
```

Con `DB_CONNECTION=sqlite` y `DB_DATABASE` apuntando a un fichero `.sqlite`
en el `.env`.

## Comandos Útiles

### Artisan / Composer / NPM

```bash
docker compose run --rm app php artisan <comando>
docker compose run --rm app composer <comando>
docker compose run --rm app npm <comando>
```

### Pruebas

```bash
docker compose run --rm app php artisan test
docker compose run --rm app npm test          # Vitest
```

### Ver logs

```bash
docker compose logs -f app
docker compose logs -f web
docker compose logs -f db
```

### Entrar al contenedor

```bash
docker compose exec app bash
```

### Detener contenedores

```bash
docker compose down
```

### Detener y eliminar volúmenes (borra datos de MySQL)

```bash
docker compose down -v
```

## Servicios

| Servicio | Puerto            | Descripción |
|----------|-------------------|-------------|
| web      | 80 y 443          | Nginx (con TLS) |
| app      | —                 | PHP-FPM 8.2 (interno, sin puerto publicado) |
| db       | 3307 → 3306       | MySQL 8.0 (`DB_PORT` cambia el puerto del host) |

## Base de Datos

Los valores salen de `.env`; los de abajo son los que trae `.env.docker` como
plantilla.

- **Host:** `db` (desde contenedores) / `localhost` (desde el host)
- **Puerto:** 3306 dentro de la red de Docker, 3307 desde el host
- **Base de datos:** `cerseuletras`
- **Usuario:** `cerseu_user`
- **Contraseña:** la que pongas en `DB_PASSWORD`

## Identidad visual

- **Azul institucional:** `#143B63`. La escala completa (`unmsm-azul`,
  `-light`, `-dark`, `-soft`) está en `cerseuletras/tailwind.config.js`.
- **Dorado UNMSM:** `#B6A350` y `#C9AA36`.
- El rojo se reserva para lo semántico: errores de validación, botones de
  eliminar, iconos de PDF y la marca de YouTube.
- El logo se sirve desde `public/images/logo-cerseu.webp`. Va **sin fondo y con
  trazo oscuro**: el navbar y el pie le aplican `brightness-0 invert` para
  pintarlo de blanco sobre fondo oscuro. Un logo con fondo sólido se vería como
  un rectángulo blanco macizo bajo ese filtro.

## Solución de Problemas

### Permisos de storage/logs

```bash
docker compose run --rm app chmod -R 775 storage bootstrap/cache
docker compose run --rm app chown -R laravel:www-data storage bootstrap/cache
```

### Limpiar caché

```bash
docker compose run --rm app php artisan cache:clear
docker compose run --rm app php artisan config:clear
docker compose run --rm app php artisan view:clear
```

### Recrear contenedores

```bash
docker compose down
docker compose build --no-cache
docker compose up -d
```
