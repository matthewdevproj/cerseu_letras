# Correo: qué falta para que funcione

Diagnóstico a 2 de agosto de 2026. **No se ha tocado ninguna credencial**: este
documento solo explica qué hay que rellenar y dónde.

## Estado actual

`MAIL_MAILER=log`, tanto en `.env` como en `.env.example`. Con ese valor Laravel
**no envía nada**: escribe el mensaje completo en `storage/logs/laravel.log` y
devuelve éxito. Por eso nada falla de forma visible — y por eso tampoco llega
nada.

Comprobado: `Mail::raw(...)` no lanza excepción y el remitente configurado es
`hello@example.com` con nombre `Laravel`, los valores que trae Laravel de
fábrica.

## Qué depende de esto

**1. Recuperar la contraseña.** Las rutas existen y funcionan
(`GET/POST forgot-password`, `GET/POST reset-password`), y `User` usa
`Notifiable`, así que el mecanismo está completo. Lo único que falta es el
transporte. Hoy, si alguien de Posgrado olvida su contraseña, el enlace de
recuperación acaba en un fichero de log del servidor: hace falta que alguien con
acceso por SSH lo saque de ahí, o restablecerla por consola.

**2. Avisos de solicitudes de diplomado.** `DiplomadoLeadController` envía a
`SiteSetting::contacto('admision')` cuando alguien rellena el formulario. El
envío va dentro de un `try/catch` que registra el fallo y **no interrumpe al
visitante** —correcto—, pero implica que un correo que no sale no se nota: el
usuario ve «tu solicitud fue registrada» igualmente.

Los leads **sí quedan guardados** en la tabla `diplomado_leads` y son visibles
en el panel (Solicitudes), así que no se pierde ninguno aunque el correo falle.
El aviso es una comodidad, no el registro.

## Qué hay que hacer

En el `.env` de **producción** (no en el repositorio):

```
MAIL_MAILER=smtp
MAIL_HOST=<servidor SMTP de la UNMSM>
MAIL_PORT=587
MAIL_USERNAME=<cuenta>
MAIL_PASSWORD=<contraseña>
MAIL_SCHEME=tls
```

**El remitente ya está puesto** en `.env` y `.env.example`:

```
MAIL_FROM_ADDRESS="admisionposgrado.letras@unmsm.edu.pe"
MAIL_FROM_NAME="Unidad de Posgrado - Letras UNMSM"
```

Solo faltan las cuatro líneas de conexión de arriba, que son credenciales.

El remitente es un buzón real del dominio `unmsm.edu.pe`, que es lo que exigen
SPF y DKIM: antes estaba en `hello@example.com` —el valor de fábrica de
Laravel— y con eso los correos habrían acabado rechazados o en spam.

**Las solicitudes de diplomado llegan a `admisionposgrado.letras@unmsm.edu.pe`**,
que sale de Configuración → Contacto en el panel, no de un valor fijo en el
código. Cambiarlo ahí cambia el destino.

Tras rellenarlo:

```bash
php artisan config:clear
php artisan correo:probar                    # al correo de admisión
php artisan correo:probar tu@correo.pe       # o a donde prefieras
```

El comando envía una solicitud de ejemplo igual que la del formulario —sin
guardarla en la base—, avisa si sigue en modo `log` y, si el envío falla, lista
las causas habituales.

## Qué está ya comprobado

`tests/Feature/CorreoSolicitudTest.php` verifica en cada ejecución de la suite
que el aviso sale al enviar el formulario, que va al correo de admisión
configurado en el panel, que responder escribe al solicitante, que el remitente
es del dominio `unmsm.edu.pe` y que **la solicitud se guarda aunque el envío
falle**. Lo único que no puede comprobar un test es el transporte real: para eso
está `correo:probar`.

## Aviso sobre la caché de configuración

Si en producción se ejecuta `php artisan config:cache`, **hay que volver a
ejecutarlo cada vez que cambie el `.env`** o los valores viejos seguirán
activos. Esta caché ya causó un problema en este proyecto: dejó `APP_ENV`
fijado y una tanda de tests corrió contra la base de datos de desarrollo.

## Pendiente de decidir

- **Notificar los fallos de envío.** Hoy el `catch` solo escribe en el log.
  Podría marcarse el lead como «aviso no enviado» y mostrarlo en el panel.
- **Verificación de correo al crear usuarios.** Las rutas de verificación no
  están publicadas (los tests de Breeze que fallan son justo por eso). No hace
  falta mientras las cuentas las cree un administrador a mano.
