# PosgradoLetras8 - Laravel 12 con Docker

Proyecto Laravel 12 con PHP 8.2, Nginx y MySQL 8.0 utilizando Docker.

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
│       └── default.conf
└── posgradoletras/          ← Código Laravel 12
    ├── app/
    ├── routes/
    ├── resources/
    └── ...
```

## Instalación Rápida

### 1. Configurar el archivo .env

```bash
cd posgradoletras
copy .env.docker .env
```

### 2. Construir e iniciar los contenedores

```bash
docker compose build
docker compose up -d
```

### 3. Instalar dependencias y configurar Laravel

```bash
# Instalar dependencias de PHP
docker compose run --rm app composer install

# Generar key de la aplicación
docker compose run --rm app php artisan key:generate

# Ejecutar migraciones
docker compose run --rm app php artisan migrate

# (Opcional) Instalar dependencias de Node.js
docker compose run --rm app npm install
docker compose run --rm app npm run build
```

### 4. Acceder a la aplicación

Abre en tu navegador: [http://localhost:8080](http://localhost:8080)

## Comandos Útiles

### Artisan

```bash
docker compose run --rm app php artisan <comando>
```

### Composer

```bash
docker compose run --rm app composer <comando>
```

### NPM

```bash
docker compose run --rm app npm <comando>
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

| Servicio | Puerto | Descripción |
|----------|--------|-------------|
| web      | 8080   | Nginx       |
| app      | 9000   | PHP-FPM 8.2 |
| db       | 3306   | MySQL 8.0   |

## Base de Datos

- **Host:** `db` (desde contenedores) / `localhost` (desde host)
- **Puerto:** 3306
- **Base de datos:** laravel
- **Usuario:** laravel
- **Contraseña:** laravel
- **Root Password:** root

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
