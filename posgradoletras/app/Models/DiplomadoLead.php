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

    public function programa()
    {
        return $this->belongsTo(Programa::class);
    }
}
