<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AdmisionCronogramaItem extends Model
{
    protected $fillable = [
        'admision_setting_id',
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
        return $this->belongsTo(AdmisionSetting::class, 'admision_setting_id');
    }
}
