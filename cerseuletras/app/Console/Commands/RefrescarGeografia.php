<?php

namespace App\Console\Commands;

use App\Services\GeografiaService;
use Illuminate\Console\Command;

/**
 * Vuelve a descargar la lista de países y regiones.
 *
 * La caché dura un mes. Esta orden fuerza la actualización tras arreglar la
 * salida a internet del servidor, o sirve para comprobar que funciona.
 */
class RefrescarGeografia extends Command
{
    protected $signature = 'geografia:refrescar';

    protected $description = 'Vuelve a descargar países y regiones del servicio externo';

    public function handle(): int
    {
        $this->info('Descargando la lista de países…');

        $descargados = GeografiaService::actualizarArchivo();

        if ($descargados === null) {
            $this->warn('No se pudo descargar: el archivo del repositorio se deja como estaba.');
            $this->line('Causa habitual: el servidor no tiene salida a internet, o a cURL le falta');
            $this->line('el paquete de certificados raíz (ver docs/geografia.md).');
            $this->line('El detalle está en storage/logs/laravel.log.');
        } else {
            $this->info("Archivo actualizado con {$descargados} países.");
        }

        GeografiaService::limpiarCache();
        $total = count(GeografiaService::paises());

        $this->line('');
        $this->info("En uso: {$total} países. Regiones de Perú: " . count(GeografiaService::regiones('PE')) . '.');

        // Se informa del fallo de descarga, pero si el archivo sirve la lista
        // completa el sitio está bien y no hay nada que arreglar con urgencia.
        return $total > 200 ? self::SUCCESS : self::FAILURE;
    }
}
