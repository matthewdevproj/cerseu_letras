<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Lead extends Model
{
    protected $fillable = [
        'tipo',
        'nombres',
        'apellidos',
        'correo',
        'pais',
        'region',
        'telefono',
        'programa_id',
    ];

    // `aviso_enviado_en` y `aviso_error` quedan fuera de `$fillable` a
    // propósito: los escribe AvisoDeSolicitud, nunca la petición del visitante.
    protected $casts = [
        'tipo' => TipoOferta::class,
        'aviso_enviado_en' => 'datetime',
    ];

    public function programa()
    {
        return $this->belongsTo(Programa::class);
    }

    public function scopeDeTipo($query, TipoOferta $tipo)
    {
        return $query->where('tipo', $tipo->value);
    }

    /**
     * Si el aviso al CERSEU quedó sin enviar.
     *
     * `null` en las dos columnas significa «solicitud anterior a que se llevara
     * este registro», y no se marca como pendiente para no llenar el panel de
     * avisos por reenviar que probablemente sí salieron.
     */
    public function avisoPendiente(): bool
    {
        return $this->aviso_enviado_en === null && filled($this->aviso_error);
    }
}
