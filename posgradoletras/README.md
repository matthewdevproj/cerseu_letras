# Documentación Técnica - Posgrado Letras UNMSM

## 1. Arquitectura del Sistema

### Visión General
El sistema está construido sobre el framework **Laravel** (v12.x), utilizando una arquitectura MVC (Modelo-Vista-Controlador) tradicional. El frontend utiliza **Blade** como motor de plantillas, potenciado con **Tailwind CSS** para los estilos y **Alpine.js** para la interactividad ligera en el cliente.

### Stack Tecnológico
- **Backend:**
  - Laravel Framework: `^12.0`
  - PHP: `^8.2`
- **Frontend:**
  - Tailwind CSS: `v3.x` (Estándar)
  - Alpine.js: `^3.4.2`
  - Vite: `^7.0.7`
- **Base de Datos:**
  - Motor: MySQL `8.0` (Recomendado/Estándar)
  - Driver: PDO MySQL (PHP `^8.2`)

### Entorno Docker (Oficial)
Basado en `laravel/sail/runtimes/8.2/Dockerfile`:

- **Sistema Base:** Ubuntu 24.04 (Noble Numbat)
- **Runtime:** PHP 8.2 (CLI & Dev)
- **Extensiones PHP Instaladas:**
  - `php8.2-mysql`, `php8.2-pgsql`, `php8.2-sqlite3`, `php8.2-mongodb`, `php8.2-redis`
  - `php8.2-gd`, `php8.2-imagick`
  - `php8.2-curl`, `php8.2-mbstring`, `php8.2-xml`, `php8.2-zip`
  - `php8.2-bcmath`, `php8.2-soap`, `php8.2-intl`, `php8.2-readline`, `php8.2-ldap`
  - `php8.2-msgpack`, `php8.2-igbinary`, `php8.2-swoole`, `php8.2-memcached`
  - `php8.2-pcov`, `php8.2-xdebug`
- **Node.js:** Versión 22.x
- **Herramientas Adicionales:**
  - Composer, NPM, RN, Bun, Yarn
  - MySQL Client, PostgreSQL Client 18
  - Supervisor, FFmpeg, Git, Nano, Unzip

#### Servicios y Puertos Expuestos
| Servicio | Puerto Local | Descripción |
|----------|--------------|-------------|
| **web**  | `8080`       | Nginx / Servidor Web |
| **app**  | `9000`       | PHP-FPM 8.2 (Interno) |
| **db**   | `3306`       | MySQL 8.0 |

### Estructura de Directorios Clave
- `app/Http/Controllers`: Lógica de negocio y manejo de peticiones.
    - `Admin/`: Controladores protegidos para el panel administrativo.
- `app/Models`: Modelos Eloquent que representan las tablas de la base de datos.
- `resources/views`: Plantillas Blade.
    - `layouts/`: Plantillas base (public.blade.php, app.blade.php).
    - `admin/`: Vistas del panel de administración.
- `routes/web.php`: Definición de rutas web y grupos de middleware.

---

## 2. Base de Datos

### Esquema Relacional
El sistema utiliza las siguientes tablas principales:

#### Tablas de Contenido
- **programas**: Almacena información de Maestrías y Doctorados.
    - Campos: `id`, `nombre`, `slug`, `tipo` (maestria/doctorado), `descripcion`, `active`.
- **docentes**: Información de los profesores.
    - Campos: `id`, `nombre`, `slug`, `grado_academico`, `categoria`, `dina_url`, `orcid`, `google_scholar`, `imagen_url`, `active`.
- **docente_programa**: Tabla pivote para la relación Muchos-a-Muchos entre Docentes y Programas.
- **testimonios**: Testimonios de alumnos/egresados.
    - Campos: `id`, `nombre`, `contenido`, `cargo`, `programa_id`, `imagen_path`, `published`, `orden`.
- **eventos**: Eventos y noticias académicas.
    - Campos: `id`, `titulo`, `slug`, `descripcion`, `fecha`, `hora`, `lugar`, `imagen_path`, `active`.
- **informativos**: Documentos informativos y recursos.
    - Campos: `id`, `titulo`, `descripcion`, `archivo_path`, `categoria`, `orden`, `active`.
- **cronogramas**: Fechas importantes y actividades del calendario académico.
    - Campos: `id`, `actividad`, `fecha_inicio`, `fecha_fin`, `active`.

#### Tablas de Configuración y Sistema
- **users**: Usuarios del sistema (Administradores).
    - Roles: definidos por columna `role` (ej. 'admin').
- **site_settings**: Configuración global del sitio.
    - Campos: `key`, `value` (Permite configuración dinámica de logos, textos, etc.).
- **documents**: Gestión centralizada de documentos PDF.
- **directorio_posgrado**: Información de contacto y personal administrativo.

---

## 3. Módulos del Sistema

### Panel Administrativo (`/admin`)
El acceso está protegido por el middleware `auth` y `isAdmin`. Permite la gestión CRUD (Crear, Leer, Actualizar, Eliminar) de todos los contenidos.

#### Funcionalidades Clave:
1.  **Dashboard**: Vista general del sistema.
2.  **Gestión de Programas**: Edición de información de maestrías y doctorados.
3.  **Gestión de Docentes**: Catálogo de profesores, asignación a programas y enlaces a perfiles académicos (ORCID, DINA).
4.  **Configuración del Sitio**: Control de identidad visual (logos, favicon) y textos generales.
5.  **Calendario/Cronograma**: Actualización de fechas de admisión y actividades.

### Frontend Público
El sitio público presenta la información de manera responsiva y optimizada para SEO.

#### Secciones Principales:
1.  **Inicio**: Hero section, eventos destacados, accesos rápidos.
2.  **Programas**: Listado y detalle de Maestrías y Doctorados.
3.  **Admisión**: Guía paso a paso para postulantes (cronograma, requisitos y pagos).
4.  **Trámites**: Información sobre obtención de grados (Magíster/Doctor).
5.  **Plana Docente**: Buscador y perfiles de investigadores.
6.  **Nosotros**: Autoridades y directorio de contacto.

---

## 4. Requisitos de Instalación (Hardware/Software)

### Software Requerido
- **Servidor Web:** Apache o Nginx
- **Lenguaje:** PHP >= 8.2
    - Extensiones: BCMath, Ctype, Fileinfo, JSON, Mbstring, OpenSSL, PDO, Tokenizer, XML.
- **Base de Datos:** MySQL 5.7+ o MariaDB 10.3+
- **Composer:** v2.x
- **Node.js:** v18+ y NPM (para compilación de assets)
- **Docker (Entorno de Desarrollo):**
    - Docker Engine: `^24.0` (o superior)
    - Docker Compose: `v2.x`

### Hardware Recomendado (Servidor)
- **CPU:** 2 vCores mínimo.
- **RAM:** 4GB mínimo (recomendado 8GB para procesos de compilación y caché).
- **Almacenamiento:** 40GB SSD (considerando almacenamiento de documentos e imágenes).

---

## 5. Guía de Despliegue (Deployment)

1.  **Clonar Repositorio:**
    ```bash
    git clone [url-repo]
    cd posgradoletras
    ```

2.  **Instalar Dependencias PHP:**
    ```bash
    composer install --optimize-autoloader --no-dev
    ```

3.  **Configurar Entorno:**
    - Copiar `.env.example` a `.env`
    - Configurar credenciales de BD (`DB_DATABASE`, `DB_USERNAME`, etc.)
    - Generar key: `php artisan key:generate`

4.  **Base de Datos:**
    ```bash
    php artisan migrate --force
    php artisan db:seed --class=DatabaseSeeder # (Solo si es primera instalación)
    ```

5.  **Instalar Dependencias Frontend y Compilar:**
    ```bash
    npm install
    npm run build
    ```

6.  **Optimización (Producción):**
    ```bash
    php artisan config:cache
    php artisan route:cache
    php artisan view:cache
    php artisan storage:link
    ```
