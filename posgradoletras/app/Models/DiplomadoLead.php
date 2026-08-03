<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DiplomadoLead extends Model
{
    protected $fillable = [
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
        'aviso_enviado_en' => 'datetime',
    ];

    public function programa()
    {
        return $this->belongsTo(Programa::class);
    }

    /**
     * Si el aviso a la Unidad quedó sin enviar.
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
