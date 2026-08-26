<?php

namespace App\Jobs;

use App\Models\Lead;
use App\Services\AvisoDeSolicitud;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Str;
use Throwable;

/**
 * Envía el aviso de una solicitud fuera de la petición, con reintentos.
 *
 * Antes se enviaba en la misma petición del formulario y con un solo intento.
 * El servicio ya dejaba constancia del fallo en `leads.aviso_error`, pero un
 * SMTP caído medio minuto significaba perder el aviso hasta que alguien
 * revisara esa columna y reenviara a mano — y nadie revisa una columna.
 *
 * Los reintentos van espaciados y no seguidos: un servidor de correo que
 * rechaza por saturación necesita tiempo, no insistencia.
 */
class EnviarAvisoDeSolicitud implements ShouldQueue
{
    use Queueable;

    public int $tries = 5;

    /** 1 min, 5, 15 y 1 h: cubre desde un corte breve hasta uno de media mañana. */
    public array $backoff = [60, 300, 900, 3600];

    public function __construct(public Lead $lead)
    {
    }

    public function handle(): void
    {
        // `relanzar: true` hace que un fallo de entrega vuelva a subir como
        // excepción para que la cola reintente. Los fallos de configuración
        // —sin destinatario, correo en modo «log»— devuelven false sin lanzar:
        // reintentarlos cinco veces no los arregla y solo llena el log.
        AvisoDeSolicitud::enviar($this->lead, relanzar: true);
    }

    /**
     * Cuando se agotan los intentos, deja el motivo donde el panel lo muestra.
     */
    public function failed(Throwable $e): void
    {
        $this->lead->forceFill([
            'aviso_enviado_en' => null,
            'aviso_error' => Str::limit(
                'Tras ' . $this->tries . ' intentos: ' . $e->getMessage(),
                250
            ),
        ])->save();
    }
}
