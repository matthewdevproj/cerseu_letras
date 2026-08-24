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
│   │   └── Dockerfile
│   └── nginx/
│       └── default-ssl.conf
└── cerseuletras/          ← Código Laravel 12 (ver su propio README)
    ├── app/
    ├── routes/
    ├── resources/
    └── ...
```

## Instalación Rápida

### 1. Configurar el archivo .env

```bash
cd cerseuletras
copy .env.docker .env
```

`.env.docker` trae credenciales de plantilla. Antes de levantar nada, pon las
reales en `.env`: `DB_PASSWORD`, `DB_ROOT_PASSWORD` y, si vas a enviar correo,
las de `MAIL_*`. `.env` no se versiona.

### 2. Construir e iniciar los contenedores

```bash
docker compose build
docker compose up -d
```

### 3. Instalar dependencias y configurar Laravel

```bash
# Dependencias de PHP
docker compose run --rm app composer install

# Key de la aplicación
docker compose run --rm app php artisan key:generate

# Migraciones y contenido inicial
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

### 4. Acceder a la aplicación

Abre en tu navegador: [http://localhost](http://localhost)

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
