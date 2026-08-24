<?php

namespace App\Console\Commands;

use App\Mail\NuevaSolicitudInformacion;
use App\Models\Lead;
use App\Models\SiteSetting;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

/**
 * Comprueba que el correo sale de verdad.
 *
 * Envía una solicitud de ejemplo igual que la del formulario de talleres y cursos,
 * para verificar el transporte SMTP sin esperar a que escriba un visitante.
 * El registro de prueba no se guarda en la base.
 */
class ProbarCorreo extends Command
{
    protected $signature = 'correo:probar {destino? : A dónde enviar (por defecto, el correo de admisión)}';

    protected $description = 'Envía un correo de prueba para comprobar la configuración SMTP';

    public function handle(): int
    {
        $destino = $this->argument('destino') ?: SiteSetting::contacto('admision');
        $driver = config('mail.default');

        $this->line('Remitente: ' . config('mail.from.name') . ' <' . config('mail.from.address') . '>');
        $this->line('Destino:   ' . $destino);
        $this->line('Transporte: ' . $driver);
        $this->newLine();

        if ($driver === 'log') {
            $this->warn('MAIL_MAILER=log: el correo se escribirá en storage/logs/laravel.log y NO saldrá.');
            $this->line('Pon MAIL_MAILER=smtp con los datos del servidor y ejecuta `php artisan config:clear`.');
            $this->newLine();
        }

        // Un lead sin guardar: sirve para componer el mensaje igual que en
        // producción, pero no ensucia la tabla de solicitudes.
        $lead = new Lead([
            'nombres' => 'Prueba',
            'apellidos' => 'de Envío',
            'correo' => 'no-responder@ejemplo.pe',
            'pais' => 'PE',
            'region' => 'Lima',
            'telefono' => '000 000 000',
        ]);

        try {
            Mail::to($destino)->send(new NuevaSolicitudInformacion($lead));
        } catch (\Throwable $e) {
            $this->error('El envío falló: ' . $e->getMessage());
            $this->newLine();
            $this->line('Causas habituales:');
            $this->line('  · Credenciales incorrectas o cuenta sin permiso de envío SMTP.');
            $this->line('  · Puerto bloqueado por el servidor (probar 587 con TLS o 465 con SSL).');
            $this->line('  · Certificados de cURL/OpenSSL sin configurar (ver docs/correo.md).');

            return self::FAILURE;
        }

        if ($driver === 'log') {
            $this->info('Mensaje compuesto y escrito en el log. Revisa storage/logs/laravel.log.');

            return self::SUCCESS;
        }

        $this->info("Correo enviado a {$destino}.");
        $this->line('Si no llega en unos minutos, revisa la carpeta de spam y los registros del servidor.');

        return self::SUCCESS;
    }
}
