<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AdmisionDiplomadoCronogramaItem extends Model
{
    protected $fillable = [
        'admision_diplomado_setting_id',
        'programa',
        'convocatoria',
        'fecha_inscripcion',
        'fecha_limite',
        'estado',
        'orden',
    ];

    protected $casts = [
        'orden' => 'integer',
    ];

    public function setting()
    {
        return $this->belongsTo(AdmisionDiplomadoSetting::class, 'admision_diplomado_setting_id');
    }
}
