<?php

namespace App\Services;

use App\Mail\NuevaSolicitudDiplomado;
use App\Models\DiplomadoLead;
use App\Models\SiteSetting;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

/**
 * Envía a la Unidad el aviso de una solicitud de diplomado y anota el resultado.
 *
 * Está aparte del controlador porque hay dos sitios que lo necesitan: el
 * formulario público y el botón de reenviar del panel. Y porque lo importante
 * no es enviar —eso es una línea— sino **dejar constancia de si salió**: sin
 * eso, un correo que no llega es invisible para el personal.
 *
 * Nunca lanza excepción: el formulario público no debe romperse porque el
 * servidor de correo esté caído. La solicitud ya está guardada.
 */
class AvisoDeSolicitud
{
    public static function enviar(DiplomadoLead $lead): bool
    {
        // En modo `log` Laravel escribe el mensaje en el fichero de log y
        // devuelve éxito. Darlo por enviado en el panel sería mentir: nadie lo
        // ha recibido.
        if (config('mail.default') === 'log') {
            return self::anotarFallo($lead, 'El correo está en modo «log»: se escribe en el log del servidor y no se entrega a nadie.');
        }

        $destino = SiteSetting::contacto('admision');

        if (blank($destino)) {
            return self::anotarFallo($lead, 'No hay correo de admisión configurado en Configuración → Contacto.');
        }

        try {
            Mail::to($destino)->send(new NuevaSolicitudDiplomado($lead));
        } catch (\Throwable $e) {
            Log::error('No se pudo enviar el aviso de la solicitud de diplomado #' . $lead->id . ': ' . $e->getMessage());

            return self::anotarFallo($lead, $e->getMessage());
        }

        $lead->forceFill([
            'aviso_enviado_en' => now(),
            'aviso_error' => null,
        ])->save();

        return true;
    }

    /**
     * Guarda el motivo del fallo recortado a lo que cabe en la columna.
     */
    private static function anotarFallo(DiplomadoLead $lead, string $motivo): bool
    {
        $lead->forceFill([
            'aviso_enviado_en' => null,
            'aviso_error' => Str::limit($motivo, 250),
        ])->save();

        return false;
    }
}
