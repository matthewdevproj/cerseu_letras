# Pasos de despliegue

## 1. Copia de seguridad

```bash
php artisan db:respaldar
```

Guárdala **fuera del servidor** antes de seguir.

## 2. Migrar

```bash
php artisan migrate
```

Incluye dos migraciones que dan acceso al panel a los usuarios existentes y que
rescatan textos de programas si la base viniera de una versión antigua.

## 3. Cargar el contenido de las páginas nuevas

`menu_items`, `content_pages`, `content_sections` y el cronograma de admisión
son **tablas nuevas**: en la base de producción están vacías. Sin este paso, el
menú de navegación sale vacío y /tramites, /admision y /nosotros aparecen en
blanco.

```bash
php artisan db:seed --class=MenuItemSeeder
php artisan db:seed --class=ContenidoInicialSeeder
php artisan db:seed --class=NosotrosContentSeeder
```

**No ejecutes `php artisan db:seed` a secas**: la cadena completa intentaría
crear de nuevo programas, docentes y directorio, y varios de esos seeders no son
idempotentes.

Los tres de arriba sí lo son: si la tabla ya tiene contenido, no la tocan.

## 4. Correo

Pegar `MAIL_PASSWORD` en `.env` —una **contraseña de aplicación** de Google, no
la de la cuenta— y comprobar:

```bash
php artisan config:clear
php artisan correo:probar
```

Detalles en [correo.md](correo.md).

## 5. Cachés

```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

Hay que repetir `config:cache` **cada vez que cambie el `.env`**, o los valores
viejos siguen activos.

## 6. Dar acceso al panel

La migración promueve a los usuarios que ya existían. Para cualquier cuenta
creada después:

```bash
php artisan usuario:admin correo@unmsm.edu.pe
```

## Después de desplegar

- Revisar **las tarifas de 2-3 programas**: la migración las rellena con valores
  genéricos por grado que casi seguro no coinciden con los reales.
- Reasignar **docentes a programas**: los vínculos antiguos se eliminaron para
  que los IDs nuevos no los heredaran mal.
- Comprobar que el servidor tiene **`mod_deflate`** o **`mod_brotli`**; si no,
  el `.htaccess` se ignora en silencio y se pierde el 84 % de compresión del
  HTML.
