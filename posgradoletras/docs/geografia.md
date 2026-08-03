# País y región del formulario de diplomados

Los dos campos eran texto libre y llegaban valores como «peru», «Perú » o «PE»,
con regiones mal escritas: imposible agrupar las solicitudes después. Ahora son
desplegables con datos reales.

## Cómo está montado

**Países**: de `symfony/intl`, que empaqueta la norma **ISO 3166-1** con los
nombres de **CLDR** (los datos de idioma de Unicode). Son 249 y se actualizan
con el paquete, como hacen Symfony, Django o Rails con sus equivalentes.

**Regiones**: 4716 en 229 países, en `resources/data/paises-regiones.json`
(57 KB, indexado por código ISO alpha-2). Vienen del dataset abierto
[`dr5hn/countries-states-cities-database`](https://github.com/dr5hn/countries-states-cities-database),
leído **directamente de su repositorio**. Antes se pasaba por
`countriesnow.space`, que no es una fuente propia sino un envoltorio de ese
mismo dataset — y lo entregaba estropeado: a Perú le faltaba Loreto y las
tildes, y añadía sufijos en inglés («A Coruña Province»).

Ninguna de las dos cosas necesita salida a internet.

El navegador **no habla con ningún servicio externo**. Pide las listas al propio
sitio:

- `GET /geografia/paises`
- `GET /geografia/paises/{codigo}/regiones`

Detrás, `App\Services\GeografiaService` lee el archivo y lo cachea un mes. El
servicio externo (`countriesnow.space`, sin credenciales) solo interviene al
ejecutar `php artisan geografia:refrescar`, que reescribe el archivo.

## Detalles que importan

- **Perú va primero** en la lista; el resto, alfabético. Casi todo el alumnado
  es peruano y no tiene sentido hacerle buscar entre 246 opciones.
- **Los 249 nombres van en español**, de CLDR vía `symfony/intl`.
- **El campo cambia de nombre según el país**: «Departamento» en Perú,
  «Provincia» en España, «Prefectura» en Japón, «Estado federado» en Alemania.
  Llamarlo «Región» en los 249 es impreciso. Ver `ETIQUETA_SUBDIVISION`.
- **Un solo nivel administrativo por país** (`unSoloNivel`). El dataset los
  mezcla: España traía 50 provincias **y** 17 comunidades autónomas juntas, así
  que el desplegable ofrecía «Andalucía» al lado de «Almería». Se conserva el
  tipo mayoritario más los tipos con dos entradas o menos — así sobreviven el
  Distrito Federal de Brasil y Ceuta y Melilla. Es una heurística: para el
  Reino Unido (nueve tipos) el resultado sigue siendo imperfecto.
- **Los nombres van sin tocar.** Se usa `name`, no `translations.es`: esas
  traducciones son automáticas y meten errores («Berlina» por Berlin, «Bath y
  el noreste de Somerset»). Y no se recortan sufijos: romperían nombres
  oficiales como «Free State» (Sudáfrica), «Eastern Province» (Arabia Saudí) o
  «Mountain Province» (Filipinas).
- **Las regiones de Perú se fijan a mano** (`REGIONES_OFICIALES`): el servicio
  externo traía 24, le faltaba Loreto y «Huánuco» venía sin tilde. Son 24
  departamentos más la Provincia Constitucional del Callao.
- **Orden del alfabeto español.** `sort()` de PHP compara bytes y dejaba
  «Áncash» detrás de «Lima». Se usa `Collator('es_ES')`, con una comparación
  sin acentos como reserva si falta la extensión `intl`.
- **50 de los 246 países no tienen división administrativa.** En esos casos el
  desplegable de región se sustituye por un campo de texto: un desplegable
  vacío sería un callejón sin salida. Solo uno de los dos campos viaja en el
  envío (el otro va `disabled`), o el servidor recibiría `region` dos veces.

## Refrescar la lista

```bash
php artisan geografia:refrescar
```

Avisa si está usando el respaldo local en lugar de los datos descargados.

## Aviso para el entorno local (Windows)

Esto **solo afecta a `geografia:refrescar`**: el formulario funciona igual,
porque los datos están en el repositorio. En este equipo la descarga falla con:

```
cURL error 60: SSL certificate ... unable to get local issuer certificate
```

No es un fallo del código: cURL en Windows no trae configurado el paquete de
certificados raíz. El archivo del repositorio cubre el hueco y el formulario funciona con los 246
países igualmente.

Para verlo con los 246 en local, descarga <https://curl.se/ca/cacert.pem> y
apunta `php.ini` a él:

```ini
curl.cainfo = "C:\ruta\a\cacert.pem"
openssl.cafile = "C:\ruta\a\cacert.pem"
```

**No se ha desactivado la verificación SSL** para sortearlo: eso haría que la
aplicación aceptara cualquier certificado, en local y en producción.

En un servidor Linux con los certificados del sistema al día esto funciona sin
tocar nada.

## Calidad de los datos de regiones

Es el eslabón débil y conviene saberlo. El origen es un dataset comunitario, no
ISO 3166-2, y tiene fallos: para Perú faltaba **Loreto** entero y «Huánuco»
llegaba sin tilde. Por eso Perú lleva su lista fijada a mano en
`REGIONES_OFICIALES`.

**Es razonable suponer que otros países tengan fallos parecidos y no se han
revisado.** Auditar 4617 entradas a mano no sale a cuenta; lo sensato es
corregir por país conforme aparezcan, añadiéndolo a `REGIONES_OFICIALES`.

Si en algún momento el formulario necesitara precisión de nivel comercial
—validación por país, nombres locales, formato de dirección— la referencia es
`libaddressinput` de Google, que es lo que usan Stripe o Shopify. Para captar
solicitudes de diplomados es desproporcionado.
