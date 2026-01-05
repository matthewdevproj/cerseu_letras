<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Docente extends Model
{
    protected $fillable = [
        'nombres',
        'apellidos',
        'grado',
        'especialidad',
        'email',
        'orcid',
        'cti_vitae',
        'linkedin',
        'biografia',
        'estado',
        'foto',
        'lineas_investigacion',
        'grupo_investigacion'
    ];

    protected $casts = [
        'estado' => 'integer',
        'lineas_investigacion' => 'array',
    ];

    // Relaciones
    public function programas()
    {
        return $this->belongsToMany(Programa::class, 'docente_programa')
            ->withPivot('es_coordinador', 'rol', 'orden')
            ->withTimestamps()
            ->orderBy('docente_programa.orden');
    }

    // Accessors
    public function getNombreCompletoAttribute()
    {
        $grado = $this->grado ? "{$this->grado} " : '';
        return "{$grado}{$this->nombres} {$this->apellidos}";
    }

    public function getNombreAttribute()
    {
        return $this->nombre_completo;
    }

    public function getFotoUrlAttribute()
    {
        if ($this->foto) {
            return asset('storage/' . $this->foto);
        }
        return asset('images/profesor-default.jpg');
    }

    // Scopes
    public function scopeActivos($query)
    {
        return $query->where('estado', 1);
    }

    public function scopeOrdenados($query)
    {
        return $query->orderBy('apellidos')->orderBy('nombres');
    }
}
