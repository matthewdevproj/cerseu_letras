#!/bin/bash
#
# Vuelca la base `posgradoletras` a una nueva `cerseuletras` y crea su usuario.
#
# Se ejecuta EN EL SERVIDOR, con los contenedores levantados. No modifica ni
# borra la base de origen: al terminar conviven las dos, de modo que si algo
# sale mal basta con devolver DB_DATABASE a su valor anterior en el .env.
#
#   Uso:  ./scripts/migrar_bd_a_cerseu.sh
#
#   Variables (todas opcionales salvo la contraseña de root):
#     DB_ROOT_PASSWORD   contraseña de root de MySQL   (obligatoria)
#     ORIGEN             base a copiar                 (por defecto posgradoletras)
#     DESTINO            base a crear                  (por defecto cerseuletras)
#     USUARIO            usuario a crear               (por defecto cerseu_user)
#     USUARIO_PASSWORD   su contraseña                 (obligatoria)
#     CONTENEDOR         nombre del contenedor MySQL   (por defecto cerseuletras_db)
#
set -euo pipefail

ORIGEN="${ORIGEN:-posgradoletras}"
DESTINO="${DESTINO:-cerseuletras}"
USUARIO="${USUARIO:-cerseu_user}"
CONTENEDOR="${CONTENEDOR:-cerseuletras_db}"

if [ -z "${DB_ROOT_PASSWORD:-}" ]; then
    echo "❌ Falta DB_ROOT_PASSWORD."
    echo "   Ej: DB_ROOT_PASSWORD=xxx USUARIO_PASSWORD=yyy $0"
    exit 1
fi

if [ -z "${USUARIO_PASSWORD:-}" ]; then
    echo "❌ Falta USUARIO_PASSWORD (la contraseña del usuario $USUARIO)."
    exit 1
fi

if ! docker ps --format '{{.Names}}' | grep -qx "$CONTENEDOR"; then
    echo "❌ El contenedor «$CONTENEDOR» no está corriendo."
    echo "   Contenedores activos:"
    docker ps --format '   - {{.Names}}'
    echo "   Indica otro con: CONTENEDOR=<nombre> $0"
    exit 1
fi

mysql_root() { docker exec -i "$CONTENEDOR" mysql -uroot -p"$DB_ROOT_PASSWORD" "$@"; }

echo "▶ Comprobando que «$ORIGEN» existe…"
if ! mysql_root -N -e "SHOW DATABASES LIKE '$ORIGEN';" | grep -qx "$ORIGEN"; then
    echo "❌ No existe la base «$ORIGEN»."
    exit 1
fi

TABLAS=$(mysql_root -N -e "SELECT COUNT(*) FROM information_schema.tables WHERE table_schema='$ORIGEN';")
echo "  «$ORIGEN» tiene $TABLAS tablas."

# Un destino con datos no se pisa: obliga a decidir a mano qué hacer con él.
if mysql_root -N -e "SHOW DATABASES LIKE '$DESTINO';" | grep -qx "$DESTINO"; then
    EXISTENTES=$(mysql_root -N -e "SELECT COUNT(*) FROM information_schema.tables WHERE table_schema='$DESTINO';")
    if [ "$EXISTENTES" -gt 0 ]; then
        echo "❌ «$DESTINO» ya existe y tiene $EXISTENTES tablas. No se toca."
        echo "   Bórrala a mano si de verdad quieres rehacerla."
        exit 1
    fi
fi

RESPALDO="/tmp/${ORIGEN}-$(date +%Y%m%d-%H%M%S).sql"
echo "▶ Volcando «$ORIGEN» a $RESPALDO (dentro del contenedor)…"
docker exec "$CONTENEDOR" sh -c \
    "mysqldump -uroot -p'$DB_ROOT_PASSWORD' --single-transaction --routines --triggers '$ORIGEN' > '$RESPALDO'"

echo "▶ Creando «$DESTINO» y el usuario «$USUARIO»…"
mysql_root <<SQL
CREATE DATABASE IF NOT EXISTS \`$DESTINO\` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER IF NOT EXISTS '$USUARIO'@'%' IDENTIFIED BY '$USUARIO_PASSWORD';
ALTER USER '$USUARIO'@'%' IDENTIFIED BY '$USUARIO_PASSWORD';
GRANT ALL PRIVILEGES ON \`$DESTINO\`.* TO '$USUARIO'@'%';
FLUSH PRIVILEGES;
SQL

echo "▶ Restaurando el volcado en «$DESTINO»…"
docker exec "$CONTENEDOR" sh -c \
    "mysql -uroot -p'$DB_ROOT_PASSWORD' '$DESTINO' < '$RESPALDO'"

NUEVAS=$(mysql_root -N -e "SELECT COUNT(*) FROM information_schema.tables WHERE table_schema='$DESTINO';")
echo
if [ "$NUEVAS" -eq "$TABLAS" ]; then
    echo "✅ Listo: «$DESTINO» tiene las mismas $NUEVAS tablas que «$ORIGEN»."
else
    echo "⚠️  «$DESTINO» quedó con $NUEVAS tablas y «$ORIGEN» tiene $TABLAS. Revisa el volcado."
    exit 1
fi

cat <<'FIN'

Siguientes pasos, a mano:

  1. En el .env del servidor:
         DB_DATABASE=cerseuletras
         DB_USERNAME=cerseu_user
         DB_PASSWORD=<la que acabas de usar>

  2. Aplicar las migraciones nuevas sobre la base ya copiada:
         docker compose run --rm app php artisan migrate --force

     Son dos: la que generaliza los diplomados a tipos de oferta y la que
     renombra directorio_posgrado a directorio_cerseu. Convierten los datos
     existentes; no hay que sembrar nada.

  3. Limpiar cachés:
         docker compose run --rm app php artisan config:clear
         docker compose run --rm app php artisan view:clear

  4. Comprobado que todo va, la base «posgradoletras» se puede archivar. El
     volcado queda dentro del contenedor por si hiciera falta volver atrás.
FIN
