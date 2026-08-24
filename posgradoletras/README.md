# Documentación Técnica — CERSEU Letras UNMSM

Sitio del Centro de Responsabilidad Social y Extensión Universitaria de la
Facultad de Letras y Ciencias Humanas. Para instalación y Docker, ver el
[README de la raíz](../README.md).

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
- **programas**: la oferta del CERSEU. Pese al nombre heredado, una fila es un
  curso o un taller; los distingue la columna `grado` (`Curso` / `Taller`).
    - Campos: `id`, `nombre`, `slug`, `grado`, `mencion`, `modalidad`,
      `horas_academicas`, `duracion`, `sumilla`, `plan_estudios`,
      `inversion_economica`, `estado` (`publicado` / `proximamente` /
      `borrador`), `deleted_at`.
    - El enum `App\Models\TipoOferta` concentra lo único que cambia entre los
      dos tipos: rótulo, segmento de URL, valor de `grado` y unidad de duración.
- **docentes**: Información de los profesores.
    - Campos: `id`, `nombre`, `slug`, `grado_academico`, `categoria`, `dina_url`, `orcid`, `google_scholar`, `imagen_url`, `active`.
- **docente_programa**: Tabla pivote para la relación Muchos-a-Muchos entre Docentes y Programas.
- **testimonios**: testimonios de participantes. Se administran desde el
  panel; no vienen sembrados. La sección de la portada y `/testimonios` se
  ocultan solas mientras la tabla esté vacía.
    - Campos: `id`, `nombre`, `contenido`, `cargo`, `programa_id`, `imagen_path`, `published`, `orden`.
- **eventos**: Eventos y noticias académicas.
    - Campos: `id`, `titulo`, `slug`, `descripcion`, `fecha`, `hora`, `lugar`, `imagen_path`, `active`.
- **informativos**: Documentos informativos y recursos.
    - Campos: `id`, `titulo`, `descripcion`, `archivo_path`, `categoria`, `orden`, `active`.
- **cronogramas**: Fechas importantes y actividades del calendario académico.
    - Campos: `id`, `actividad`, `fecha_inicio`, `fecha_fin`, `active`.

#### Tablas de Admisión y Solicitudes
- **admision_settings**: una fila por tipo de oferta (`tipo` = `taller` |
  `curso`, con índice único). Guarda el contenido completo de
  `/{tipo}/admision`: hero, pasos, requisitos, pago, resultados y contacto.
- **admision_cronograma_items**: convocatorias de cada módulo
  (`admision_setting_id`, `programa`, `convocatoria`, fechas, `estado`). Aquí
  vive la programación anual: un mismo curso puede dictarse varias veces.
- **leads**: solicitudes de información del formulario público, con `tipo` para
  saber de qué módulo vienen y `aviso_enviado_en` / `aviso_error` para dejar
  constancia de si el correo de aviso salió.

#### Tablas de Configuración y Sistema
- **users**: Usuarios del sistema (Administradores).
    - Roles: definidos por columna `role` (ej. 'admin').
- **site_settings**: configuración global. Es una tabla de **una sola fila**
  —el modelo lo impide en `creating`— con una columna por ajuste: logo,
  favicon, correos por rol, redes, y los heros de portada, cursos y talleres
  (`{talleres|cursos}_hero_{titulo|texto|claim|imagen}`).
- **content_pages / content_sections**: contenido editable de `/tramites`,
  `/admision` y `/nosotros`. `ContentPage::GRUPOS` define si una página se
  divide en pestañas; `/tramites` ya no las usa.
- **documents**: gestión centralizada de documentos PDF.
- **directorio_posgrado**: directorio de contacto. Nombre heredado; a la espera
  del equipo del CERSEU está vacía y su enlace no aparece en el menú.

---

## 3. Módulos del Sistema

### Panel Administrativo (`/admin`)
El acceso está protegido por el middleware `auth` y `isAdmin`. Permite la gestión CRUD (Crear, Leer, Actualizar, Eliminar) de todos los contenidos.

#### Funcionalidades Clave:
1.  **Dashboard**: Vista general del sistema.
2.  **Gestión de Cursos y Talleres**: edición de la oferta. El tipo se elige
    en el desplegable «Grado».
3.  **Gestión de Docentes**: Catálogo de profesores, asignación a programas y enlaces a perfiles académicos (ORCID, DINA).
4.  **Configuración del Sitio**: Control de identidad visual (logos, favicon) y textos generales.
5.  **Calendario/Cronograma**: fechas de admisión y actividades.
6.  **Admisión por módulo** (`/admin/admision/{talleres|cursos}`): una misma
    pantalla sirve los dos, con un selector arriba. Cada tipo guarda sus
    propios ajustes y su propio cronograma.
7.  **Solicitudes** (`/admin/leads`): listado y exportación a CSV de las
    solicitudes de información, filtrables por tipo, con reenvío del aviso por
    correo cuando el envío falló.

### Frontend Público
El sitio público presenta la información de manera responsiva y optimizada para SEO.

#### Secciones Principales:
1.  **Inicio**: hero, indicadores, oferta destacada con filtro por tipo.
2.  **Cursos** (`/cursos`) y **Talleres** (`/talleres`): mismo controlador y
    mismas plantillas para los dos módulos. Cada uno trae listado, ficha
    (`/{tipo}/{slug}`), admisión (`/{tipo}/admision`) y formulario de solicitud.
3.  **Admisión**: guía general del proceso.
4.  **Trámites**: constancias y certificados. La página se arma con tantas
    secciones como se carguen desde el panel, sin número fijo.
5.  **Plana Docente**: buscador y perfiles.
6.  **Nosotros**: misión, visión, valores y autoridades.

Las rutas anteriores `/programas` y `/diplomados` responden con 301 hacia
`/cursos` y `/talleres`, para no romper enlaces ya publicados.

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
    Los seeders dejan el sitio utilizable: menú, contenido de `/tramites`,
    `/admision` y `/nosotros`, ajustes del sitio y la programación 2026 del
    CERSEU. Son idempotentes en lo que importa —no pisan contenido ya editado
    desde el panel— pero conviene no reejecutarlos sobre una instalación viva
    sin mirar antes qué hace cada uno.

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
