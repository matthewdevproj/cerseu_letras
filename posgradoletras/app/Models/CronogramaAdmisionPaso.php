<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Etapa del "Cronograma de Admisión" de la portada.
 */
class CronogramaAdmisionPaso extends Model
{
    protected $table = 'cronograma_admision_pasos';

    protected $fillable = [
        'cronograma_admision_id',
        'titulo',
        'fecha_inicio',
        'fecha_fin',
        'detalle',
        'publico',
        'icono',
        'orden',
        'destacado',
        'is_visible',
    ];

    protected $casts = [
        'orden' => 'integer',
        'destacado' => 'boolean',
        'is_visible' => 'boolean',
    ];

    public function cronograma()
    {
        return $this->belongsTo(CronogramaAdmision::class, 'cronograma_admision_id');
    }

    /**
     * Fecha lista para mostrar: "5 ene - 02 abr" cuando hay inicio y cierre, o
     * solo la que exista. Las fechas son texto libre a propósito, porque el
     * cronograma oficial se publica con formatos muy variados
     * ("06 de abril", "Hasta el 06 de abril").
     */
    public function getFechaDisplayAttribute(): ?string
    {
        $inicio = trim((string) $this->fecha_inicio);
        $fin = trim((string) $this->fecha_fin);

        if ($inicio !== '' && $fin !== '') {
            return "{$inicio} - {$fin}";
        }

        return $inicio !== '' ? $inicio : ($fin !== '' ? $fin : null);
    }

    /**
     * Trazado SVG del ícono elegido.
     */
    public function getIconoPathAttribute(): string
    {
        return CronogramaAdmision::iconoPath($this->icono);
    }
}
